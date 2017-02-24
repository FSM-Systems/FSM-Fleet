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
	$("#new").click(function () {
		$("#newitem").load("ajax/divs/newdriver.php", {btntext: "DRIVER"},  function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

	$(function() {
		$( ".dp" ).datepicker();
	});

	$(".tel").click(function (e) {
		e.preventDefault();
		$("#newitem").load("ajax/divs/newdriverphone.php", {did: $(this).attr("id").replace("tel_","")}, function () {
			$(this).fadeIn().draggable();
		})
	})

	$(".email").click(function (e) {
		e.preventDefault();
		$("#newitem").load("ajax/divs/driver_sendinfo_email.php", {did: $(this).attr("id").replace("email_","")}, function () {
			$(this).fadeIn().draggable();
		})
	})

	// Validation for elements
	$("#drivers").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		}
	})

	$('[name*="dname"]').each(function() {
	    $(this).rules('add', {
	        required: true,
	    });
	});

	$('[name*="dlicenceno"]').each(function() {
		$(this).rules('add', {
			required: true,
			maxlength: 10,
			minlength: 10,
	    });
	});

	$('[name*="dlicenseexp"], [name*="dpassportexp"]').each(function() {
		$(this).rules('add', {
			required: true,
			dateITA: true,
	    });
	});

	// Load QTip for drivers
	$(".qtipdriver").each(function() {
		$(this).qtip({
			content: {
				text: 'Loading Driver Information...', // The text to use whilst the AJAX request is loading
				ajax: {
					global: false,
					url: 'ajax/qtip.php', // URL to the local file
					type: 'GET', // POST or GET
					data: {
						itemtype: "driverstatus",
						itemid: this.id,
						token: "<?php echo $_SESSION["atoken"]; ?>",
					}
				}
			},
			position: {
				target: "mouse",
				adjust: {
					mouse: false,
				}
			},
			show: {
             solo: true
         	},
		});
	})

	check_expiry();
	searchbox();
})
</script>
<?php
videohelp("S8pvfK2QfBw");
?>
</head>
<body>
<div style="float: right" id="topbuttons">
	<button id="new"><img class="icon" src="icons/drivers.png" alt=""> Create a new Driver</button>
</div>
<br><br>
<div class="topline">
Current drivers registered on the System:
<br>
<form name="drivers" id="drivers">
<?php quicksearch("drivers", "did"); ?>
<table class="tbllist searchtbl" cellpadding=2 cellspacing=0 style="min-width: 960px; width: 75%">
<tr>
	<th class="hidden" db="did">ID</th>
	<th db="dname">Name</th>
	<th db="dlicenceno">License Number </th>
	<th db="dlicenseexp">License Expiry</th>
	<th db="dpassportno">Passport Number</th>
	<th db="dpassportexp">Passport Expiry</th>
	<th>Status</th>
	<th></th>
</tr>
<?php
$res = pg_query($con, "select *, to_char(dlicenseexp, 'DD/MM/YYYY') as dlicenseexp, to_char(dpassportexp, 'DD/MM/YYYY') as dpassportexp,
case when dlicenseexp <= now() + interval '30 days' then true else false end as licenseexpiring,
case when dpassportexp <= now() + interval '30 days' then true else false end as passportexpiring
from drivers where company_id=" . $_SESSION["company"] . " order by dname");
while($row = pg_fetch_assoc($res)) {
	// Check if driver is on a trip.
	$res2 = pg_query($con, "select count(tldriver) from trip_log where tlclosed=false and tldriver=" . $row["did"]);
	if(pg_fetch_result($res2, 0, 0) == "0") {
		$status = "<label style='font-weight: bold; color: green'>IN OFFICE</label>";
	} else {
		$status = "<label style='font-weight: bold; color: red' class='qtipdriver' id='" . $row["did"] . "'>DRIVING</label>";
	}
	if($row["licenseexpiring"] == "t") {
		$drvclass = " expirydate";
	} else {
		$drvclass = "";
	}

	if($row["passportexpiring"] == "t") {
		$ppclass = " expirydate";
	} else {
		$ppclass = "";
	}
	echo "
		<tr class='tbl'>
				<td class='hidden excelid'>" . $row["did"] . "</td>
				<td>" . uinput("dname", $row["did"], $row["dname"], "drivers", "dname", "did", $row["did"], false,true,250,null,null,null,false,true,false) . "</td>
				<td>" . uinput("dlicenceno", $row["did"], $row["dlicenceno"], "drivers", "dlicenceno", "did", $row["did"], false,false,null,null,null,null,false,true,false) . "</td>
				<td>" . uinput("dlicenseexp", $row["did"], $row["dlicenseexp"], "drivers", "dlicenseexp", "did", $row["did"], false,false,80,"dp centered" . $drvclass,null,null,true,true,false) . "</td>
				<td>" . uinput("dpassportno", $row["did"], $row["dpassportno"], "drivers", "dpassportno", "did", $row["did"], false,true,null,null,null,null,false,true,false) . "</td>
				<td>" . uinput("dpassportexp", $row["did"], $row["dpassportexp"], "drivers", "dpassportexp", "did", $row["did"], false,false,80,"dp centered" . $ppclass,null,null,true,true,false) . "</td>
				<td>" . $status . "</td>
				<td class='delbtn'><button title='ADD PHONE' class='tel' id='tel_" . $row["did"] . "'><img class='smallbutton' src='icons/telephone.png'></button></td>
				<td class='delbtn'><button title='SEND DRIVER INFO BY MAIL' class='email' id='email_" . $row["did"] . "'><img class='smallbutton' src='icons/emailarrow.png'></button></td>
				<td class='delbtn'>" . delbtn("drivers", "did", $row["did"], "drivers.php", null, "#workspace")  . "</td>
		</tr>
	";
}
?>
</table>
<form>
</div>
</body>
</html>