<?php
include "../../../inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {

	$(".dp").datepicker({maxDate: '0'});

	$("#newfrmfuel").validate({
		debug: true,
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		},
		rules: {
			newdatefuel: {
				required: true,
				dateITA: true,
			},
			newexpfuel: "required",
			//newlocfuel: "required",
			newqtyfuel: {
				required: true,
				number: true,
			},
			newvalfuel: {
				required: true,
				number: true,
			},
		},
	});

	acomplete(".exp","ajax/autocompletes/expenses.php", true);
	acomplete(".loc","ajax/autocompletes/locations.php", true);

	// Once deleted reload the expenses tab (index 1)
	$(".del").click(function (e) {
		e.preventDefault();
		$("#newfrmfuel").validate({
		   onsubmit: false,
		})
		delitem("trip_log_fuel", "tlfid", $(this).attr("id").replace("del_",""), null, null,true, "tabs", 2, false);
	});

	$("#addfuel").click(function (e) {
		e.preventDefault();
		//$.alert($("#newfrmfuel").valid());
		if ($("#newfrmfuel").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newtripinfofuel.php",
				type: "POST",
				data: {
					tlfdate: $("#newdatefuel").val(),
					tlflocation: $("#newlocfuel_id").val(),
					tlfvalue: $("#newvalfuel").val(),
					tlfqty: $("#newqtyfuel").val(),
					tlftripid: $("#tripidfuel").val(),
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						// Reload this tab
						$("#tabs").tabs("load", $("#tabs").tabs("option", "active"));
					} else {
						$.alert('<?php echo QUERYERROR; ?>' + data);
					}
				},
				error: function (data) {
					$.alert('<?php echo AJAXERROR; ?>');
				},
			})
		}
	});

	// On edit change status of table for tab change checks
	$("#fuel tr:last").find("input").change(function () {
		$("#fuel").data('changed', true);
	});
});
</script>
</head>
<body>
<?php
$res = pg_query($con, "select
tlfqty, tlfid, tlfvalue, to_char(tlfdate, 'dd/mm/yyyy') as tlfdate, locdescription || ' (' || lttype || ')' as locdescription
from trip_log_fuel
left join locations on tlflocation=locid
left join location_types on loctype=ltid
where tlftripid=" . $_REQUEST["id"] . " order by tlfdate");
?>
<form id="newfrmfuel" name="newfrmfuel">
<table class="tbllistnoborder" id="fuel" border="0" cellspacing="0" cellpadding="2" style="width: 650px">
<tr>
	<th>Date</th>
	<th>Location</th>
	<th class="right">Litres</th>
	<th class="right">Value</th>
</tr>
<?php
if(pg_num_rows($res) > 0) {
	while($row = pg_fetch_assoc($res)) {
		echo 	"
		<tr>
			<td>" . uinput("tlfdate", $row["tlfid"], $row["tlfdate"], "trip_log_fuel", "tlfdate", "tlfid", $row["tlfid"], false,false,70,"dp",null,false,true,true,false,false) . "</td>
			<td>" . uinput("tlflocation", $row["tlfid"], $row["locdescription"], "trip_log_fuel", "tlflocation", "tlfid", $row["tlfid"], false,true,250,"loc",null,true,false,true,false,false) . "</td>
			<td class='right'>LT." . uinput("tlfqty", $row["tlfid"], $row["tlfqty"], "trip_log_fuel", "tlfqty", "tlfid", $row["tlfid"], false,false,50,"centered",null,false,false,true,false,false) . "</td>
			<td class='right'>$ " . uinput("tlfvalue", $row["tlfid"], $row["tlfvalue"], "trip_log_fuel", "tlfvalue", "tlfid", $row["tlfid"], false,false,50,"centered",null,false,false,true,false,false) . "</td>
			<td class='smallbtn'><button type='button' class='del' id='del_" . $row["tlfid"] . "'><img src='icons/delete.png' class='smallbutton'></td>
		</tr>";
	}
} else {
	echo "<tr><td colspan='5'>No fuel information currently added for this trip. You can start adding them.</td></tr>";
}
?>
<tr>
	<td><input type="text" style="width: 70px" id="newdatefuel" name="newdatefuel" class="dp"></td>
	<td><input type="text" id="newlocfuel" name="newlocfuel" style="width: 250px" class="loc"><input type="hidden" id="newlocfuel_id" name="newlocfuel_id"></td>
	<td class="right">LT. <input type="text" class="centered" style="width: 50px; text-align: center" id="newqtyfuel" name="newqtyfuel"></td>
	<td class="right">$ <input type="text" class="centered" style="width: 50px; text-align: center" id="newvalfuel" name="newvalfuel"></td>
	<td><button type="button" id="addfuel">ADD</button></td>
</tr>
</table>
</form>
<br>
<input type="hidden" name="tripidfuel" id="tripidfuel" value="<?php echo $_REQUEST['id']; ?>">
</body>
</html>