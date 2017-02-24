<?php
include "../../inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
function addloc(tripid,val) {
	var locationval;
	if ($("#addloc_val").val() != "") {
		locationval = $("#addloc_val").val();
	} else {
		locationval = "null";
	}
	insertvalue("trip_config_expenses", "tcetripid,tceexpense,tcefixedvalue", tripid + "," + val + "," + locationval, "tceid", $("#newitem"), "ajax/divs/tripconfig_expenses.php?tcid=<?php echo $_REQUEST["tcid"]; ?>");
}

$(document).ready(function () {
	// Autocomplete
	acomplete(".dd", "ajax/autocompletes/expenses.php", false, true, false);
	// Table rows
	tablerows();
})
</script>
<?php
	$res  = pg_query($con, "select * from trip_config left join trip_config_expenses on tcid=tcetripid left join expense_types on tceexpense = etid where tcetripid=" . $_REQUEST["tcid"] . " order by tceid");
	$res2 = pg_query($con, "select tcdescription from trip_config where tcid=" . $_REQUEST["tcid"]);
?>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<table class="tbllistnoborder" id="numbered">
<tr>
	<td style="text-align: center" colspan="3">ADD FIXED TRIP EXPENSES FOR <b><?php echo pg_fetch_result($res2, 0, 0);?></b></td>
</tr>
<?php
	//echo "<td>";
	while($step = pg_fetch_assoc($res)) {
		echo "<tr>
		<td></td><td>" .
		uinput("exp", $step["tceid"], $step["etdescription"], "trip_config_expenses", "tceexpense", "tceid", $step["tceid"], false,true,null,"dd","style='width: 300px'",true, false,true, false) .
		"</td>
		<td>
		<input type='text'  id='exp_" . $step["tceid"] . "_val' style='width: 50px' value='" . $step["tcefixedvalue"]  . "' class='centered'
		onchange='updatevalue(\"trip_config_expenses\",\"tcefixedvalue\", this.value,\"tceid\",\"" . $step['tceid'] . "\",true,false,this,false,false)'>
		</td>
		<td>" .
		delbtn("trip_config_expenses", "tceid", $step["tceid"], "ajax/divs/tripconfig_expenses.php?tcid=" . $_REQUEST["tcid"], "smallbutton", "#newitem") . "
		</td>
		</tr>";
	}
	// Extra line for adding items with dropdown
	echo "<tr><td></td><td>
	<input type='text' class='dd' id='addloc' style='width: 300px;'>
	<input type='hidden' class='dd' id='addloc_id' onchange='addloc(" . $_REQUEST['tcid'] . ",this.value)'>";
	//echo "</td>";
	echo "</td><td><input type='hidden' id='addloc_val'></td><td class='delbtn'></td></tr>";
?>
</table>
</body>
</html>