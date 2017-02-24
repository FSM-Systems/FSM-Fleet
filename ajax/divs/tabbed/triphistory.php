<?php
include "../../../inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<style type="text/css">
label.headlabel {
  display: inline-block;
  vertical-align: top;
  font-weight: bold;
  text-decoration: underline;
}​
label.newitemlabel {
  display: inline-block;
  width: 140px;
  text-align: right;
  vertical-align: top;
}​
</style>
<script type="text/javascript">
$("#printroute").click(function () {
	$("#printitem").attr("src", "prints/routereport.php?id=<?php echo $_REQUEST["id"]; ?>")
})
</script>
</head>
<body>
<table class="tbltripstep">
<button id="printroute"><img src="icons/print.png" alt=""> PRINT ROUTE REPORT</button>
<?php
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
<br>
<?php
// Display total day count
echo 'Total time required for trip:<b> ' . $daycount . ' days</b>';
?>
</body>
</html>