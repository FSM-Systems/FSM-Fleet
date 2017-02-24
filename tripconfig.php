<?php
include "inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	$("#new").click(function (e) {
		e.preventDefault();
		$("#newitem").load("ajax/divs/newtripconfig.php", {btntext: "TRIP CONFIGURATION"}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

	$(".steps").click(function (e) {
		e.preventDefault();
		$("#newitem").load("ajax/divs/tripconfig_steps.php", { tcid: $(this).attr("id").replace("steps_","") }, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		})
	})

	$(".exp").click(function (e) {
		e.preventDefault();
		$("#newitem").load("ajax/divs/tripconfig_expenses.php", { tcid: $(this).attr("id").replace("exp_","") }, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		})
	})

	$(".dup").click(function () {
		var tripid = $(this).attr("id").replace("dup_","");
		$.confirm('DUPLICATE THIS TRIP?', function (answer) {
			if (answer) {
				$.prompt('PLEASE INSERT TRIP NAME', function (string) {
					if (string != "") {
						$.ajax({
							url: "ajax/duplicate_trip.php",
							data: {
								id: tripid,
								tripname: string,
								//token: "<?php echo $_SESSION["atoken"]; ?>",
								token: '<?php echo $_SESSION['atoken']; ?>',
							},
							success: function (data) {
								if (data != "OK") {
									$.alert('<?php echo QUERYERROR; ?>' + data);
								} else {
									$("#workspace").load("tripconfig.php");
								}
							}
						})
					} else {
						$.alert('PLEASE INSERT A NAME FOR THE TRIP!');
					}
				})
			}
		})
	})

	// Validation for elements
	$("#tripconfig").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		}
	})

	$('[name*="tcdistance"]').each(function() {
	    $(this).rules('add', {
	        required: true,
	        number: true,
	    });
	});


	// update tyre fixed costs
	$("#tyreexp").change(function () {
		updatevalue("trip_settings", "ts_tyre_usd_perkm", $(this).val(), "company_id", "<?php echo $_SESSION["company"]; ?>", true, false, this, false, false);
	})

	// Fetch default tyre fixed cost
	$.ajax({
		url: "ajax/get_table_column_value.php",
		global: false,
		data: {
			table: "trip_settings",
			col: "ts_tyre_usd_perkm",
			rowid: "company_id",
			rowidvalue: "<?php echo $_SESSION["company"]; ?>",
			token: '<?php echo $_SESSION['atoken']; ?>',
		},
		success: function (data) {
			$("#tyreexp").val(data);
		}
	})

	searchbox();
	excel();
})
</script>
</head>
<body>
<div style="float: right"><button id="new"><img class="icon" src="icons/wallet.png" alt=""> Create a new Trip Configration</button></div>
<br><br>
Tyre fixed expense per KM <input type="text" name="tyreexp" id="tyreexp"class="centered" style="width: 40px;"> USD/km (Automatic calculation per trip based on KM)
<div class="topline">
Current trip configurations registered on the System:
<br>
<form id="tripconfig">
<table class="tbllist searchtbl" style='min-width: 960px; width: 80%' cellpadding=2 cellspacing=0>
<tr>
	<th class="hidden">ID</th>
	<th>Description</th>
	<th>Distance</th>
	<th colspan="2">Configurations</th>
	<th class="right">Fixed Expense Total</th>
	<th></th>
</tr>
<?php
$res = pg_query($con, "select trip_config.*, '$. ' || sum(tcefixedvalue) as val from trip_config left join trip_config_expenses on tcid=tcetripid where trip_config.company_id=" . $_SESSION["company"] . " group by tcid order by tcdescription");
while($row = pg_fetch_assoc($res)) {
	echo "
		<tr class='tbl'>
				<td class='hidden'>" . $row["tcid"] . "</td>
				<td>" . uinput("tcdescription", $row["tcid"], $row["tcdescription"], "trip_config", "tcdescription", "tcid", $row["tcid"], false,true,300,null,null,false,false,true,false) . "</td>
				<td>" . uinput("tcdistance", $row["tcid"], $row["tcdistance"], "trip_config", "tcdistance", "tcid", $row["tcid"], true,false,50,"centered",null,false,false,true,false) . " km</td>
				<td><button class='steps' id='steps_" . $row["tcid"] . "'><img class='smallbutton' src='icons/steps.png'>&nbsp;&nbsp;SET TRIP STEPS</button></td>
				<td><button class='exp' id='exp_" . $row["tcid"] . "'><img class='smallbutton' src='icons/dollar.png'>&nbsp;&nbsp;SET TRIP FIXED EXPENSES</button></td>
				<td class='right'>" . $row["val"] . "</td>
				<td><button type='button' class='dup' id='dup_" . $row["tcid"] . "'>DUPLICATE TRIP</button></td>
				<td class='delbtn'>" . delbtn("trip_config", "tcid", $row["tcid"], "tripconfig.php", null, "#workspace")  . "</td>
		</tr>

	";
}
?>
</table>
</form>
</div>
</body>
</html>