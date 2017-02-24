<?php
include "../../../inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<body>
<?php
$res = pg_query($con, "select 
etdescription, tleid, coalesce(tlevalue, 0) as tlevalue, to_char(tledate, 'dd/mm/yyyy') as tledate, locdescription
from trip_log_expenses
left join expense_types on tleetid=etid 
left join locations on tlelocation=locid
where tletripid=" . $_REQUEST["id"] . " order by tledate");
?>
<form id="newfrmexp" name="newfrmexp">
<table class="tbllistnoborder bottomborder" border="0" cellspacing=0 cellpadding=2 style="width: 650px">
<tr>
	<th>Date</th>
	<th>Expense Description</th>
	<th>Location</th>
	<th class="right">Value</th>
</tr>
<?php
$total = 0;
if(pg_num_rows($res) > 0) {
	while($row = pg_fetch_assoc($res)) {
		$total += $row["tlevalue"];
		echo 	"
		<tr>
			<td>" . $row["tledate"] . "</td>
			<td>" . $row["etdescription"] . "</td>
			<td>" . $row["locdescription"] . "</td>
			<td class='right'>" . (($row["tlevalue"] > 0) ? "$" . number_format($row["tlevalue"],2) : " ")  . "</td>
		</tr>";
	}
	echo "<tr><td colspan='3' class='bold right'>TOTAL TRIP EXPENSES</td><td class='right bold'>" . (($total > 0) ? "$" . number_format($total,2) : " ") . "</td></tr>";
} else {
	echo "<tr><td colspan='5' class='bold' style='color: tomato'>No expenses were registered for this trip.</td></tr>";
}	
?>
</table>
</form>
<br>
<input type="hidden" name="tripid" id="tripid" value="<?php echo $_REQUEST['id']; ?>">
</body>
</html>