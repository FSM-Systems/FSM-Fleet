<?php
include "../../../inc/session_test.php";
include "connection.inc";
?>
<!DOCTYPE html>
<html>
<body>
<?php
$res = pg_query($con, "select
tlfqty, tlfid, tlfvalue, to_char(tlfdate, 'dd/mm/yyyy') as tlfdate, locdescription || ' (' || lttype || ')' as locdescription, tcdistance
from trip_log_fuel
left join trip_log on tlftripid=tlid
left join trip_config on tltripconfig=tcid
left join locations on tlflocation=locid
left join location_types on loctype=ltid
where tlftripid=" . $_REQUEST["id"] . " order by tlfdate");
?>
<form id="newfrmfuel" name="newfrmfuel">
<table class="tbllistnoborder bottomborder" border="0" cellspacing="0" cellpadding="2" style="width: 650px">
<tr>
	<th>Date</th>
	<th>Location</th>
	<th class="right">Litres</th>
	<th class="right">Value</th>
</tr>
<?php
if(pg_num_rows($res) > 0) {
	$km = pg_fetch_result($res, 0, 5);
	$total = 0;
	$fuel = 0;
	while($row = pg_fetch_assoc($res)) {
		$total += $row["tlfvalue"];
		$fuel += $row["tlfqty"];
		echo 	"
		<tr>
			<td>" . $row["tlfdate"] . "</td>
			<td>" . $row["locdescription"] . "</td>
			<td class='right'>LT." . $row["tlfqty"]. "</td>
			<td class='right'>$ " . $row["tlfvalue"] . "</td>
		</tr>";
	}
	echo "
	<tr>
		<td colspan='2' class='bold right'>TOTAL FUEL FOR TRIP (CONSUMPTION: " . (($fuel > 0) ? number_format($km/$fuel,2) . " Km/L" : " ") . " )</td>
		<td class='right bold'>" . (($fuel > 0) ? "LT. " . $fuel : " ")  . "</td>
		<td class='right bold'>" . (($total > 0) ? "$" . number_format($total,2) : " ")  . "</td>
	</tr>";
} else {
	echo "<tr><td colspan='5' class='bold' style='color: tomato'>No fuel was registered for this trip.</td></tr>";
}
?>
</table>
</form>
<br>
<input type="hidden" name="tripidfuel" id="tripidfuel" value="<?php echo $_REQUEST['id']; ?>">
</body>
</html>