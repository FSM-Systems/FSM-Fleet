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

	$("#newfrmexp").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		},
		rules: {
			newdate: {
				required: true,
				dateITA: true,
			},
			newexp: "required",
			//newloc: "required",
			newval: "required",
		},
	});

	//element, source, deleteoption, withvalue, createnew, createtable, createcolumn, retid
	acomplete(".exp","ajax/autocompletes/expenses.php", true, false, true, "expense_types", "etdescription", "etid", true, $("#tdavg"), "ajax/get_trip_allowance_avg.php", <?php echo $_REQUEST["id"]; ?>);
	//acomplete_change(".exp", "expense_types", "etdescription");
	acomplete(".loc","ajax/autocompletes/locations.php", true);

	// Once deleted reload the expenses tab (index 1)
	$(".del").click(function (e) {
		e.preventDefault();
		$("#newfrmexp").validate({
		   onsubmit: false,
		})
		delitem("trip_log_expenses", "tleid", $(this).attr("id").replace("delexp_",""), null, null,true, "tabs", 1, false, true, $("#tdavg"), "ajax/get_trip_allowance_avg.php", <?php echo $_REQUEST["id"]; ?>);
	});

	$("#add").click(function (e) {
		//$.alert($("#newfrmexp").valid());
		e.preventDefault();
		if ($("#newfrmexp").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newtripinfoexpense.php",
				type: "POST",
				data: {
					tledate: $("#newdate").val(),
					tleetid: $("#newexp_id").val(),
					tlelocation: $("#newloc_id").val(),
					tlevalue: $("#newval").val(),
					tletripid: $("#tripid").val(),
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						// Reload this tab
						//$("#tabs").tabs("load", 1);
						$("#tabs").tabs("load", $("#tabs").tabs("option", "active"));
						$.ajax({
							url: "ajax/get_trip_allowance_avg.php",
							global: false,
							data: {
								id: <?php echo $_REQUEST["id"]; ?>,
								token: '<?php echo $_SESSION['atoken']; ?>',
							},
							success: function (data) {
								if ($.isNumeric(data)) {
									$("#tdavg").text("$. " + data + "/day");
								}
							}
						})
					} else {
						$.alert('<?php echo QUERYERROR; ?>' + data);
					}
				},
				error: function (data) {
					$.alert('<?php echo AJAXERROR; ?>');
				},
			})
		}
	})

	// On edit change status of table for tab change checks
	$("#expenses tr:last").find("input").change(function () {
		$("#expenses").data('changed', true);
	})

	$("#addstdexpense").click(function (e) {
		e.preventDefault()
		$.ajax({
			url: "ajax/add_trip_fixed_expenses.php",
			method: "POST",
			data: {
				tripid: $("#tripid").val(),
				tripconfigid: $("#tltripconfig").val(),
				token: '<?php echo $_SESSION['atoken']; ?>',
			},
			success: function (data) {
				switch(data) {
					case "NOEXPENSE":
						$.alert('THERE ARE NO FIXED EXPENSES CREATED FOR THIS TRIP. PLEASE CREATE THEM IN TRIP ROUTES/EXPENSES AND RETRY.')
						break;
					case "OK":
						$.ajax({
							url: "ajax/get_trip_allowance_avg.php",
							global: false,
							data: {
								id: <?php echo $_REQUEST["id"]; ?>,
								token: '<?php echo $_SESSION['atoken']; ?>',
							},
							success: function (data) {
								if ($.isNumeric(data)) {
									$("#tdavg").text("$. " + data + "/day");
								}
								$('#tabs').tabs('load', $("#tabs").tabs('option', 'active'));
							}
						});
						break;
					default:
						$.alert('<?php echo QUERYERROR; ?>' + data);
				}
			}
		})
	});
});
</script>
</head>
<body>
<?php
$res = pg_query($con, "select
etdescription, tleid, tlevalue, to_char(tledate, 'dd/mm/yyyy') as tledate, locdescription || ' (' || lttype || ')' as locdescription
from trip_log_expenses
left join expense_types on tleetid=etid
left join locations on tlelocation=locid
left join location_types on loctype=ltid
where tletripid=" . $_REQUEST["id"] . " order by tledate desc, tleid");

if(pg_num_rows($res) == 0) {
// Add button to apply all fixed expenses for trip
?>
<button id="addstdexpense">ADD STANDARD FIXED EXPENSES TO THIS TRIP</button>
<?php
}
?>
<form id="newfrmexp" name="newfrmexp">
<table class="tbllistnoborder" id="expenses" border="0" cellspacing=0 cellpadding=2 style="width: 650px">
<tr>
	<th>Date</th>
	<th>Expense Description</th>
	<th>Expense Location</th>
	<th class="right">Value</th>
</tr>
<?php
if(pg_num_rows($res) > 0) {
	while($row = pg_fetch_assoc($res)) {
		echo 	"
		<tr>
			<td>" . uinput("tledate", $row["tleid"], $row["tledate"], "trip_log_expenses", "tledate", "tleid", $row["tleid"], false,false,70,"dp",null,false,true,true,false) . "</td>
			<td>" . uinput("etdescription", $row["tleid"], $row["etdescription"], "trip_log_expenses", "tleetid", "tleid", $row["tleid"], false,true,180,"exp",null,true,false,true,false) . "</td>
			<td>" . uinput("tlelocation", $row["tleid"], $row["locdescription"], "trip_log_expenses", "tlelocation", "tleid", $row["tleid"], false,true,250,"loc",null,true,false,true,false) . "</td>
			<td>$ " . uinput("tlevalue", $row["tleid"], $row["tlevalue"], "trip_log_expenses", "tlevalue", "tleid", $row["tleid"], true,false,50,"right",null,false,false,true,false) . "</td>
			<td class='smallbtn'><button type='button' class='del' id='delexp_" . $row["tleid"] . "'><img src='icons/delete.png' class='smallbutton'></td>
		</tr>";
	}
} else {
	echo "<tr><td colspan='5'>No expenses currently added for this trip. You can start adding them.</td></tr>";
}
?>
<tr>
	<td><input type="text" style="width: 70px" id="newdate" name="newdate" class="dp"></td>
	<td><input type="text" id="newexp" name="newexp" class="exp" style="width: 180px"><input type="hidden" id="newexp_id"></td>
	<td><input type="text" id="newloc" name="newloc" style="width: 250px" class="loc"><input type="hidden" id="newloc_id"></td>
	<td>$ <input type="text" class="right" style="width: 50px" id="newval" name="newval"></td>
	<td><button type="button" id="add">ADD</button></td>
</tr>
</table>
</form>
<br>
<input type="hidden" name="tripid" id="tripid" value="<?php echo $_REQUEST['id']; ?>">
</body>
</html>