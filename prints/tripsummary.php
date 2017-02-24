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

/*  ----------------- schedule ----------------- */
$prev = "";
$counter = 1; // Do not disbale first textbox if values are empty.
$daycount = 0;
$warningtimestyle = "";
$triplegtimestyle = "";
$rowsched = array();
$resschedule = pg_query($con, "
select
tldid, tldaction, to_char(tldactiondate, 'dd/mm/YYYY') as tldactiondate,
tldactiondate - lag(tldactiondate, 1) over (order by tldactiondate) daycount,
locdescription,lttype,loctriplegtime,locwarningdays
 from

trip_log left join trip_log_det on tlid=tldtripid
left join locations on tldlocation=locid
left join location_types on loctype=ltid

where tlid=" . $_REQUEST["id"] . " order by tldid, tldorder ;");
// Get trip days
while($row = pg_fetch_assoc($resschedule)) {
	$rowsched[] = $row;
	$daycount += $row["daycount"];
}
/*  ----------------- schedule ----------------- */

/*  ----------------- expenses ----------------- */
$res = pg_query($con, "select
etdescription, tleid, coalesce(tlevalue, 0) as tlevalue, to_char(tledate, 'dd/mm/yyyy') as xtledate, locdescription
from trip_log_expenses
left join expense_types on tleetid=etid
left join locations on tlelocation=locid
where tletripid=" . $_REQUEST["id"] . " order by tledate");
/*  ----------------- expenses ----------------- */
/* ----------------- tyre expenses --------------*/
$restyre = pg_query($con, "select coalesce(tltyrecost_per_km,0) * coalesce(tldistance,0) from trip_log where tlid= " . $_REQUEST["id"]);
if(pg_num_rows($restyre) > 0 ) {
	$tyreexp = pg_fetch_result($restyre, 0, 0);
} else {
	$tyreexp = 0;
}
/* ----------------- tyre expenses --------------*/

/*  ----------------- fuel ----------------- */
$resfuel = pg_query($con, "select
tlfqty, tlfid, tlfvalue, to_char(tlfdate, 'dd/mm/yyyy') as xtlfdate, locdescription || ' (' || lttype || ')' as locdescription, tcdistance
from trip_log_fuel
left join trip_log on tlftripid=tlid
left join trip_config on tltripconfig=tcid
left join locations on tlflocation=locid
left join location_types on loctype=ltid
where tlftripid=" . $_REQUEST["id"] . " order by tlfdate");
/*  ----------------- fuel ----------------- */

/*  ----------------- statistics ----------------- */
$resstats = pg_query($con, "
select 'total' as d, tlvalue as v, 1 as o from trip_log where tlid=" . $_REQUEST["id"] . " union
select 'totalret' as d, tlvalueret as v, 1.5  as o from trip_log where tlid=" . $_REQUEST["id"] . " union
select 'fuel' as d, sum(tlfvalue) as v, 2 as o from trip_log_fuel where tlftripid=" . $_REQUEST["id"] . " union
select 'fuelconsumption' as d, coalesce(sum(tlfqty),0) as v, 5 as o from trip_log_fuel where tlftripid=" . $_REQUEST["id"] . " union
select 'expense' as d, sum(tlevalue) as v, 3 as o from trip_log_expenses where tletripid=" . $_REQUEST["id"] . " union
select 'km' as d, tcdistance as v, 4 as o from trip_log inner join trip_config on tltripconfig=tcid where tlid=" . $_REQUEST["id"] .
" order by o");
/*  ----------------- statistics ----------------- */

/*  ----------------- average driver allowance ----------------- */
$resavg = pg_query($con, "select  ((sum(tlevalue)/trip_length_days(" . $_REQUEST["id"] . "))::numeric(12,2)) from trip_log left join trip_log_expenses on tlid=tletripid left join expense_types on tleetid=etid where (etaverageperday=true or position('ALLOWANCE' in upper(etdescription)) > 0) and tletripid=" . $_REQUEST["id"] . " group by tlbooked");
if(pg_num_rows($resavg) > 0) {
	$avg = pg_fetch_result($resavg,0 ,0);
} else {
	$avg = 0;
};
/*  ----------------- average driver allowance ----------------- */
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="CSS_print.php">
</head>
<body onload="window.print()">
<img class="logo" src="../<?php echo $_SESSION['logo']; ?>" alt="" style="position: absolute">
<div class="header">
<?php echo $_SESSION["companyname"]; ?> - <?php echo $info["tnumberplate"] ?> TRIP SUMMARY
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
			<td class="bold">Allowance AVG:</td>
			<td><?php echo $avg; ?>/day</td>
		</tr>
	</table>
</div>
<br>
<div class="truckinspection">
	<table>
		<tr>
			<td style="width: 50%">
				<table class="tbltripstep" style="width: 100%">
					<tr><td colspan="10" class="labelheader bold underline">DRIVING SCHEDULE (TOTAL <?php echo $daycount; ?> DAYS)</td></tr>
					<?php
					// If values there then do not disbale!
					foreach($rowsched as $row) {
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
			</td>
			<td style="width: 50%">
				<table class="tbltripstep"  style="width: 100%">
					<tr><td colspan="10" class="labelheader bold underline">EXPENSES SUMMARY</td></tr>
					<?php
					$total = 0;
					while($row = pg_fetch_assoc($res)) {
						$total += $row["tlevalue"];
						echo 	"
						<tr class='border'>
							<td>" . $row["xtledate"] . "</td>
							<td>" . $row["etdescription"] . "</td>
							<td>" . $row["locdescription"] . "</td>
							<td class='right'>" . (($row["tlevalue"] > 0) ? "$" . number_format($row["tlevalue"],2) : " ")  . "</td>
						</tr>";
					}
					// Add tyre cost to total
					$total += $tyreexp;
					// Add line for fixed tyre expenses
					echo "<tr><td>&nbsp;</td><td colspan='2'>FIXED TYRE COSTS</td><td class='right'>" . (($tyreexp > 0) ? "$" . number_format($tyreexp,2) : " ") . "</td></tr>";
					echo "<tr><td colspan='3' class='bold right underline'>TOTAL TRIP EXPENSES</td><td class='right bold underline'>" . (($total > 0) ? "$" . number_format($total,2) : " ") . "</td></tr>";
					?>
				</table>
				<br>
				<table class="tbltripstep" style="width: 100%">
				<tr><td colspan="10" class="labelheader bold underline">FUEL SUMMARY</td></tr>
				<?php
				if(pg_num_rows($resfuel) > 0) {
					$totalfuelvalue = 0;
					$fuel = 0;
					$km = pg_fetch_result($resfuel, 0, 5);
					while($row = pg_fetch_assoc($resfuel)) {
						$totalfuelvalue += $row["tlfvalue"];
						$fuel += $row["tlfqty"];
						echo 	"
						<tr class='border'>
							<td>" . $row["xtlfdate"] . "</td>
							<td>" . $row["locdescription"] . "</td>
							<td class='right' style='width: 16mm;'>LT." . $row["tlfqty"]. "</td>
							<td class='right' style='width: 16mm;'>$ " . number_format($row["tlfvalue"],2) . "</td>
						</tr>";
					}
					echo "
					<tr>
						<td colspan='2' class='bold right underline'>TOTAL FUEL FOR TRIP</td>
						<td class='right bold underline'>" . (($fuel > 0) ? "LT. " . $fuel : " ")  . "</td>
						<td class='right bold underline'>" . (($totalfuelvalue > 0) ? "$" . number_format($totalfuelvalue, 2) : " ")  . "</td>
					</tr>";
				} else {
					$km = 0;
					$fuel = 0;
					echo "<tr><td colspan='10'>No fuel registered for this trip!</td></tr>";
				}
				?>
				</table>
			</td>
		</tr>
	</table>
</div>
<br>
<div class="equipmentlist">
	<table class="tbllist" cellpadding="2" cellspacing="0" style="width: 60%">
		<tr><td colspan="4" class="labelheader bold underline">TRIP ACCOUNTING STATISTICS</td></tr>
		<?php
		$income = 0;
		$incomeret = 0;
		$exp = 0;
		while($row = pg_fetch_assoc($resstats)) {
			switch($row["d"]) {
				case "total":
						echo "<tr class='border'><td>Main trip income:</td><td  class='right'>$. " . number_format($row["v"],2) . "</td></tr>";
						$income = $row["v"];
					break;
				case "totalret":
						echo "<tr class='border'><td>Return trip income:</td><td  class='right'>$. " . number_format($row["v"],2) . "</td></tr>";
						$incomeret = $row["v"];
					break;
				case "fuel":
					echo "<tr class='border'><td>Total fuel expenses:</td><td class='right'>$. " . number_format($row["v"],2) . "</td></tr>";
					$fuel = $row["v"];
					break;
				case "expense":
					echo "<tr class='border'><td>Total trip expenses:</td><td class='right'>$. " . number_format($row["v"] + $tyreexp,2) . "</td></tr>";
					$exp = $row["v"] + $tyreexp;
					break;
			}
		}
		// Net income
		echo "<tr class='border'><td>Net trip income:</td><td class='right bold underline'>$. " . number_format($income + $incomeret - $fuel - $exp,2) . "</td></tr>";
		if($fuel > 0 ) {
			echo "<tr class='border'><td>Average fuel consumption:</td><td class='right bold underline'>" . number_format($km/$fuel,2) . " Km/L</td></tr>";
		} else {
			echo "<tr class='border'><td>Average fuel consumption:</td><td class='right'> --- </td></tr>";
		}
		if($km > 0) {
			echo "<tr class='border'><td>Income per KM:</td><td class='right bold underline'>" . number_format(($income + $incomeret - $exp - $fuel)/$km,2) . " $/KM</td></tr>";
		} else {
			echo "<tr class='border'><td>Income per KM:</td><td class='right'> --- </td></tr>";
		}
		echo "</table>";
		?>
	</table>
</div>
<br>
<label class="labelheader bold underline">EXTRA NOTES AND INFORMATION:</label>

<div class="signatures">
	<table>
	<tr>
		<td style="width: 33%">Driver: ___________________________</td>
		<td style="width: 33%">Office: ___________________________</td>
		<td>Management: ___________________________</td>
	</tr>
</div>
</body>
</html>