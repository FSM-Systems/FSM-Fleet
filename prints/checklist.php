<?php
include "../inc/session_test.php";
include "connection.inc";

$res = pg_query($con, "select tnumberplate, trnumberplate, dname, tcdescription from
trip_log
left join trucks on tltruck=tid
left join trailers on tltrailer=trid
left join drivers on tldriver=did
left join trip_config on tltripconfig=tcid
where tlid=" . $_REQUEST["id"]);

$info = pg_fetch_assoc($res, 0);
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="CSS_print.php">
</head>
<body onload="window.print()">
<img class="logo" src="../<?php echo $_SESSION['logo']; ?>" alt="" style="position: absolute">
<div class="header">
<?php echo $_SESSION['companyname']; ?> INSPECTION CHECK-LIST
</div>

<div class="headerinfo">
	<table border="0">
		<tr>
			<td style="width: 20mm;" class="bold">Truck:</td>
			<td style="width: 43mm"><?php echo $info["tnumberplate"] ?></td>
			<td style="width: 20mm" class="bold">Trailer:</td>
			<td style="width: 43mm"><?php echo $info["trnumberplate"] ?></td>
			<td style="width: 20mm" class="bold">Date:</td>
			<td><?php echo date("d/m/Y")?></td>
		</tr>
		<tr>
			<td class="bold">Kms:</td>
			<td></td>
			<td class="bold">Kms:</td>
			<td></td>
			<td class="bold">Driver:</td>
			<td><?php echo $info["dname"] ?></td>
		</tr>
		<tr>
			<td class="bold">Assigned Trip</td><td colspan="5"><?php echo $info["tcdescription"]; ?></td>
		</tr>
	</table>
</div>
<br>
<div class="truckinspection">
	<table cellpadding="2" cellspacing="0">
		<tr><td colspan="10" class="labelheader bold underline">TRUCK AND TRAILER INSPECTION</td></tr>
		<tr class="borderbottomblack">
			<td style="width: 35mm;" class="bold lalign">Truck Items</td>
			<td style="width: 30mm;" class="bold centered lalign" colspan="2">Satisfaction<br>Departure Check</td>
			<td style="width: 30mm;" class="bold centered lalign" colspan="2">Satisfaction<br>Return Check</td>
			<td style="width: 35mm;" class="bold lalign">Trailer Items</td>
			<td style="width: 30mm;" class="bold centered lalign" colspan="2">Satisfaction<br>Departure Check</td>
			<td style="width: 30mm;" class="bold centered lalign" colspan="2">Satisfaction<br>Return Check</td>
		</tr>
		<tr class="borderbottomgray">
			<td style="width: 30mm">Head Lights</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 30mm">Parking Lights</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
		</tr>
		<tr class="borderbottomgray">
			<td style="width: 30mm">Brake Lights</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 30mm">Brake Lights</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
		</tr>
		<tr class="borderbottomgray">
			<td style="width: 30mm">Indicators</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 30mm">Indicators</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
		</tr>
		<tr class="borderbottomgray">
			<td style="width: 30mm">Windscreen</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 30mm">Container Locks</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
		</tr>
		<tr class="borderbottomgray">
			<td style="width: 30mm">Side Mirrors</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 30mm">Brake Pipes</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
		</tr>
		<tr class="borderbottomgray">
			<td style="width: 30mm">Body Damage</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 30mm">Body Damage</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
		</tr>
		<tr class="borderbottomgray">
			<td style="width: 30mm">Conditions</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 30mm">Conditions</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
		</tr>
		<tr class="borderbottomgray">
			<td style="width: 30mm">Punctures</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 30mm">Punctures</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> YES
			</td>
			<td style="width: 10mm" class="leftborder">
				<input type="checkbox"> NO
			</td>
		</tr>
	</table>
</div>
<br>
<?php
// Fetch equipment list for trip
$res = pg_query($con, "

select eqdescription, esdqty
from trip_log left join equipment_sets on tlequipment=eqsid
	left join equipment_sets_det on eqsid=esdsetid
	left join equipment on esditem=eqid
	where tlid=" . $_REQUEST["id"]);
?>
<div class="equipmentlist">
	<table cellpadding="2" cellspacing="0">
		<tr><td colspan="4" class="labelheader bold underline">DRIVER'S EQUIPMENT LIST</td></tr>
		<?php
		$count = 1;
		if(pg_num_rows($res) > 0 && pg_fetch_result($res, 0, 0) != "") {
			while($row = pg_fetch_assoc($res)) {
				echo "
				<tr class='borderbottomgray'>
					<td>" . $count . ". " . $row["eqdescription"] . "</td>
					<td>" . $row["esdqty"] . " piece(s)</td>
					<td>Returned from trip: __________  pieces(s)</td>
					<td>Difference: __________ </td>
				</tr>";
				$count++;
			}
		} else {
			echo "<tr><td colspan='4'>No equipment set for this trip</td></tr>";
		}
		?>
	</table>
</div>
<br>
<div class="equipmentlist">
	<table cellpadding="2" cellspacing="0">
		<tr><td colspan="3" class="labelheader bold underline">WORKSHOP INSPECTION CHECK-LIST</td></tr>
		<tr class="borderbottomgray">
			<td style="width: 30mm">Grease</td>
			<td style="width: 40mm">
			<input type="checkbox">OK&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="checkbox">NOT OK
			</td>
			<td>
				<textarea>Notes: </textarea>
			</td>
		</tr>
		<tr class="borderbottomgray">
			<td style="width: 30mm">Oils</td>
			<td style="width: 40mm">
			<input type="checkbox">OK&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="checkbox">NOT OK
			</td>
			<td>
				<textarea>Notes: </textarea>
			</td>
		</tr>
		<tr class="borderbottomgray">
			<td style="width: 30mm">Filters</td>
			<td style="width: 40mm">
			<input type="checkbox">OK&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="checkbox">NOT OK
			</td>
			<td>
				<textarea>Notes: </textarea>
			</td>
		</tr>
		<tr class="borderbottomgray">
			<td style="width: 30mm">Batteries</td>
			<td style="width: 40mm">
			<input type="checkbox">OK&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="checkbox">NOT OK
			</td>
			<td>
				<textarea>Notes: </textarea>
			</td>
		</tr>
	</table>
</div>
<br>
<label class="labelheader bold underline">EXTRA NOTES AND INFORMATION:</label>

<div class="signatures">
	<table>
	<tr>
		<td style="width: 33%">Driver: ___________________________</td>
		<td style="width: 33%">Office: ___________________________</td>
		<td>Workshop: ___________________________</td>
	</tr>
</div>
</body>
</html>