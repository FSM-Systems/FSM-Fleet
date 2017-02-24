<?php
include "../inc/session_test.php";
include "connection.inc";

// Queries
// Header
$res = pg_query($con, "
select case when trnumberplate is not null then tnumberplate || '/' || trnumberplate else tnumberplate end as tnumberplate,
dname, tcdescription, tlcontainer, tlcontainer_ret, c1.cname as c1, c2.cname as c2, tldistance from
trip_log
left join trucks on tltruck=tid
left join trailers on tltrailer=trid
left join drivers on tldriver=did
left join trip_config on tltripconfig=tcid
left join customers as c1 on tlcustomer1=c1.cid
left join customers as c2 on tlcustomer2=c2.cid
where tlid=" . $_REQUEST["id"]);

$info = pg_fetch_assoc($res, 0);

// Route info
$res = pg_query($con, "
select
tldid, tldaction, to_char(tldactiondate, 'dd/mm/YYYY') as tldactiondate,
tldactiondate - lag(tldactiondate, 1) over (order by tldactiondate) daycount,
locdescription,lttype,loctriplegtime,locwarningdays
 from

trip_log left join trip_log_det on tlid=tldtripid
left join locations on tldlocation=locid
left join location_types on loctype=ltid

where tlid=" . $_REQUEST["id"] . " order by tldid, tldorder ;
");
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="CSS_print.php">
</head>
<body onload="window.print()">
<img class="logo" src="../<?php echo $_SESSION['logo']; ?>" alt="" style="position: absolute">
<div class="header">
<?php echo $_SESSION["companyname"]; ?> - <?php echo $info["tnumberplate"] ?> ROUTE REPORT
</div>

<div class="headerinfo">
	<table border="0">
		<tr>
			<td style="width: 20mm;" class="bold">Truck:</td>
			<td style="width: 43mm"><?php echo $info["tnumberplate"]; ?></td>
			<td style="width: 20mm" class="bold">Trip Distance:</td>
			<td style="width: 40mm"><?php echo $info["tldistance"]; ?> km</td>
			<td style="width: 23mm" class="bold">Date:</td>
			<td><?php echo date("d/m/Y")?></td>
		</tr>
		<tr>
			<td class="bold">Container(s):</td>
			<td><?php echo $info["tlcontainer"]; ?><br><?php echo $info["tlcontainer_ret"]; ?></td>
			<td class="bold">Main Customer:</td>
			<td><?php echo $info["c1"]; ?></td>
			<td class="bold">Return Customer:</td>
			<td><?php echo $info["c1"]; ?></td>
		</tr>
		<tr>
			<td class="bold">Driver:</td>
			<td><?php echo $info["dname"]; ?></td>
			<td class="bold">Assigned Trip</td>
			<td><?php echo $info["tcdescription"]; ?></td>
			<td class="bold"></td>
			<td></td>
		</tr>
	</table>
</div>
<table class="tbltripstep">
<?php
$prev = "";
$counter = 1; // Do not disbale first textbox if values are empty.
$daycount = 0;
$warningtimestyle = "";
$triplegtimestyle = "";
// If values there then do not disbale!
while($row = pg_fetch_assoc($res)) {
	$daycount += $row["daycount"];
	if($row["locdescription"] != $prev) {
		echo '<tr><td colspan=3>
		<br><label class="headlabel">' . $row["locdescription"] . ' (' . $row['lttype'] . ')' . '</label></td></tr>';
		// Check against trip leg time as we have done a leg and style if greater
		if($row["loctriplegtime"] != "" && $row["daycount"] > $row["loctriplegtime"]) {
			$triplegtimestyle = " style='color: red; font-weight: bold' ";
		} else {
			$triplegtimestyle = "";
		}
	} else {
		// Check against time in location as we are in the location and style if greater
		if($row["locwarningdays"] != "" && $row["daycount"] > $row["locwarningdays"]) {
			$warningtimestyle = " style='color: red; font-weight: bold' ";
		} else {
			$warningtimestyle = "";
		}
	}
	echo '<tr ' . $warningtimestyle . $triplegtimestyle . '><td><label class="newitemlabel">' . $row["tldaction"] . ': </label></td>';
	echo '<td>' . $row["tldactiondate"] . '</td>';
	if($counter == 1) {
		echo '<td></td>';
	} else {
		if($row["daycount"] != "") {
			if($row["daycount"] == 1) {
				echo '<td>Time: ' . $row["daycount"] . ' day</td>';
			} else {
				echo '<td>Time: ' . $row["daycount"] . ' days</td>';
			}
		} else {
			echo '<td></td>';
		}
	}
	echo '</tr>';
	$prev = $row["locdescription"];
	$counter++;
}
?>
</table>
<?php
// Display total day count
echo 'Total time required for trip:<b> ' . $daycount . ' days</b>';
?>
</body>
</html>