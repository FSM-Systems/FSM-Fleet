<?php
include "../../inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	$("#tabs").tabs({
		//show: { effect: "slide", duration: 600 }
		<?php
		if (isset($_REQUEST["tabopen"])) {
			// Open selected tab
			echo "active: " . $_REQUEST["tabopen"] . ",";
		}
		?>
	});

	$("#adminedit").click(function () {
		$("#newitem").load("ajax/divs/trip_info_tab.php", {id: <?php echo $_REQUEST["id"]; ?>, mod: true, tabopen: $("#tabs").tabs("option", "active")}, function () {
			$(this).fadeIn().draggable();
		});
	})
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<div style="position: relative; width: 600px; text-align: left;">
<table class="tbllistnoborder" style="width: 600px; left: 0px;">
<?php
$res = pg_query($con, "select
dname,c1.cname as c1,c2.cname as  c2, to_char(tlbooked, 'dd/mm/yyyy') as tlbooked, tlcontainer, tlcontainer_ret,
case when trid is not null then tnumberplate || ' - ' || trnumberplate else tnumberplate end as tnumberplate
from trip_log
left join trucks on tltruck=tid
left join trailers on ttrailer=trid
left join drivers on tldriver=did
left join customers as c1 on tlcustomer1=c1.cid
left join customers as c2 on tlcustomer2=c2.cid

where tlid=" . $_REQUEST["id"] );
$row = pg_fetch_assoc($res, 0);

$resavg = pg_query($con, "select (sum(tlevalue)/trip_length_days(" . $_REQUEST["id"] . "))::numeric(12,2) from trip_log left join trip_log_expenses on tlid=tletripid left join expense_types on tleetid=etid where (etaverageperday=true or position('ALLOWANCE' in upper(etdescription)) > 0) and tletripid=" . $_REQUEST["id"] . " group by tlbooked");
if(pg_num_rows($resavg) > 0) {
	$avg = pg_fetch_result($resavg,0 ,0);
} else {
	$avg = 0;
}


echo "<tr class='headtr'><td colspan=2>
<table style='width: 100%' class='detail'>
<tr><td class='bold underline'>Truck:</td><td class='bold underline'  id='truckprintname'>" . $row["tnumberplate"] . "</td></tr>
<tr><td>Driver:</td><td id='driverprintname'>" . $row["dname"] . "</td></tr>
<tr><td>Booked:</td><td>" . $row["tlbooked"] . "</td></tr>
<tr><td>Allowance AVG:</td><td>$. " . $avg . "/day</td></tr>
</table>
</td><td>
<table style='width: 100%' class='detail'>
<tr><td>Main Customer:</td><td>" . $row["c1"] . "</td></tr>
<tr><td>Return Customer:</td><td>" . $row["c2"] . "</td></tr>
<tr><td>Container:</td><td>" . $row["tlcontainer"] . "</td></tr>
<tr><td>Container Ret:</td><td>" . $row["tlcontainer_ret"] . "</td></tr>
</table>
</tr>";
?>
</table>
</div>
<div id="tabs">
  <ul>
    <li><a href="ajax/divs/tabbed/triphistory.php?id=<?php echo $_REQUEST["id"]; ?>">Schedule</a></li>
    <li><a href="ajax/divs/tabbed/triphistory_expenses.php?id=<?php echo $_REQUEST["id"]; ?>">Expenses</a></li>
    <li><a href="ajax/divs/tabbed/triphistory_fuel.php?id=<?php echo $_REQUEST["id"]; ?>">Fuel Log</a></li>
    <li><a href="ajax/divs/tabbed/equipment_info.php?id=<?php echo $_REQUEST["id"]; ?>">Equipment</a></li>
    <?php
	if($_SESSION['stataccess'] == "t") {
    ?>
    <li><a href="ajax/divs/tabbed/triphistory_stats.php?id=<?php echo $_REQUEST["id"]; ?>">Statistics</a></li>
    <?php
    }
    ?>
    <?php
	if($_SESSION["invoicing"] == "t") {
    ?>
    <li><a href="ajax/divs/tabbed/invoicing_info.php?id=<?php echo $_REQUEST["id"]; ?>">Invoicing Information</a></li>
    <?php
    }
    ?>
    <li><a href="ajax/divs/tabbed/triphistory_cargo.php?id=<?php echo $_REQUEST["id"]; ?>">Cargo</a></li>
  </ul>
</div>
<br>
<?php
if($_SESSION["tripmod"] == "t") {
?>
<button id="adminedit">EDIT THIS TRIP (EVEN IF CLOSED)</button>
<?php
}
?>
</body>
</html>