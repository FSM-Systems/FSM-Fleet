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
		$("#newitem").load("ajax/divs/newtrailer.php", {btntext: "TRAILER"}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

	$(function() {
		$( ".dp" ).datepicker();
	});

	// Validation for elements
	$("#trailers").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		}
	})

	$('[name*="trnumberplate"]').each(function() {
	    $(this).rules('add', {
	        required: true,
	        numberplateTZ: true,
	    });
	});

	$('[name*="tryear"]').each(function() {
		$(this).rules('add', {
			required: true,
			min: 1950,
			max: 2100,
	    });
	});

	$('[name*="traxles"]').each(function() {
		$(this).rules('add', {
			required: true,
			min: 1,
			max: 5,
	    });
	});

	$('[name*="trroadlicense"]').each(function() {
		$(this).rules('add', {
			required: true,
			dateITA: true,
	    });
	});

	check_expiry();
	searchbox();
	excel();
})
</script>
<?php
videohelp("7vljjnwG4ZM");
?>
</head>
<body>
<div style="float: right" id="topbuttons">
	<button id="new"><img class="icon" src="icons/new.png" alt=""> Create a new Trailer</button>
</div>
<br><br>
<div class="topline">
Current trailers registered on the System:
<br>
<form name="trailers" id="trailers">
<?php quicksearch("trailers", "trid"); ?>
<table class="tbllist searchtbl" cellpadding=2 cellspacing=0 style="width: 70%">
<tr>
	<th class="hidden" db="trid">ID</th>
	<th db="trnumberplate">Number Plate</th>
	<th db="trchassisnumber">Chassis Number</th>
	<th db="trmake">Trailer Make</th>
	<th db="tryear">Year</th>
	<th db="traxles">Axles</th>
	<th db="trroadlicense">Road License</th>
	<th></th>
</tr>
<?php
$res = pg_query($con, "select *, to_char(trroadlicense, 'dd/mm/yyyy') as trroadlicense,
case when trroadlicense <= now() + interval '30 days' then true else false end as expiring
from trailers where company_id=" . $_SESSION["company"] . " order by trnumberplate");
while($row = pg_fetch_assoc($res)) {
	if($row["expiring"] == "t") {
		$class = " expirydate";
	} else {
		$class = "";
	}
	echo "
		<tr class='tbl'>
				<td class='hidden excelid'>" . $row["trid"] . "</td>
				<td>" . uinput("trnumberplate", $row["trid"], $row["trnumberplate"], "trailers", "trnumberplate", "trid", $row["trid"], false,true,75,"centered",null,null,false,true,false) . "
				<td>" . uinput("trchassisnumber", $row["trid"], $row["trchassisnumber"], "trailers", "trchassisnumber", "trid", $row["trid"], false,true,200,null,null,null,false,true,false) . "</td>
				<td>" . uinput("trmake", $row["trid"], $row["trmake"], "trailers", "trmake", "trid", $row["trid"], false,true,200,null,null,null,false,true,false) . "</td>
				<td>" . uinput("tryear", $row["trid"], $row["tryear"], "trailers", "tryear", "trid", $row["trid"], true,false,50,"centered", null,null,false,true,false) . "</td>
				<td>" . uinput("traxles", $row["trid"], $row["traxles"], "trailers", "traxles", "trid", $row["trid"], true,false,25,"centered",null,null,false,true,false) . "</td>
				<td>" . uinput("trroadlicense", $row["trid"], $row["trroadlicense"], "trailers", "trroadlicense", "trid", $row["trid"], false,false,80,"dp centered" . $class,null,null,true,true,false) . "</td>
				<td class='delbtn'>" . delbtn("trailers", "trid", $row["trid"], "trailers.php", null, "#workspace")  . "</td>
		</tr>
	";
}
?>
</table>
</form>
</div>
</body>
</html>