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
		$("#newitem").load("ajax/divs/newequipment.php", {btntext: "EQUIPMENT"}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

	$("#newloctype").click(function () {
		$("#newitem").load("ajax/divs/newequipmentset.php", {btntext: "EQUIPMENT SET"}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

	// New window to configure sets
	$(".sets").click(function () {
		$("#newitem").load("ajax/divs/equipment_sets.php", { eqsid: $(this).attr("id").replace("sets_","") }, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		})
	});
})
</script>
</head>
<body>
<div style="float: right"><button id="new"><img class="icon" src="icons/equipment.png" alt=""> Create a new Equipment</button> <button id="newloctype"><img class="icon" src="icons/equipmentset.png" alt=""> Create a new Equipment Set</button></div>
<br><br>
<div class="topline">
Current equipment and configurations registered on the System:
<br>
<div style="width: 60%; min-width: 800px;">
<div style="width: 48%; float: left;">
<table class="tbllist" style="width: 100%" cellpadding=2 cellspacing=0>
<tr>
	<th class="hidden">ID</th>
	<th>Description</th>
	<th></th>
</tr>
<?php
$res = pg_query($con, "select * from equipment where company_id=" . $_SESSION["company"] . " order by eqdescription");
if(pg_num_rows($res) > 0) {
while($row = pg_fetch_assoc($res)) {
	echo "
		<tr class='tbl'>
				<td class='hidden'>" . $row["eqid"] . "</td>
				<td>" . uinput("eqdescription", $row["eqid"], $row["eqdescription"], "equipment", "eqdescription", "eqid", $row["eqid"], false,true,300,null,false,false,false,true,false) . "</td>
				<td class='delbtn'>" . delbtn("equipment", "eqid", $row["eqid"], "equipment.php", null, "#workspace")  . "</td>
		</tr>
	";
}
} else {
	echo "<tr><td colspan='2'>No equipment registered</td></tr>";
}
?>
</table>
</div>

<div style="width: 48%; float: right;">
<table class="tbllist" style="width: 100%" cellpadding=2 cellspacing=0>
<tr>
	<th class="hidden">ID</th>
	<th>Equipment Set Description</th>
	<th></th>
	<th></th>
</tr>
<?php
$res = pg_query($con, "select * from equipment_sets where company_id=" . $_SESSION["company"] . " order by eqsdescription");
if(pg_num_rows($res) > 0) {
while($row = pg_fetch_assoc($res)) {
	echo "
		<tr class='tbl'>
				<td class='hidden'>" . $row["eqsid"] . "</td>
				<td>" . uinput("eqsdescription", $row["eqsid"], $row["eqsdescription"], "equipment_sets", "eqsdescription", "eqsid", $row["eqsid"], false,true,300,null,false,false,false,true,false) . "</td>
				<td class='delbtn'><button class='sets' id='sets_" . $row["eqsid"] . "' title='ADD ITEMS TO THIS SET'><img class='smallbutton' src='icons/gear.png'></button></td>
				<td class='delbtn'>" . delbtn("equipment_sets", "eqsid", $row["eqsid"], "equipment.php", null, "#workspace")  . "</td>
		</tr>
	";
}
} else {
	echo "<tr><td colspan='4'>No equipment sets defined</td></tr>";
}
?>
</table>
</div>
</div>
</div>
</body>
</html>