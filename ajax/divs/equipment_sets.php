<?php
include "../../inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
function addequip(esdsetid,val) {
	insertvalue("equipment_sets_det", "esdsetid, esditem", esdsetid + "," + val, "esdid", $("#newitem"), "ajax/divs/equipment_sets.php?eqsid=<?php echo $_REQUEST["eqsid"]; ?>");
}

$(document).ready(function () {
	// Dropdiown
	acomplete(".dd", "ajax/autocompletes/equipment.php", true, false,false);
	// Table row numbers
	tablerows();
})
</script>
<?php
	$res  = pg_query($con, "select * from equipment_sets_det left join equipment on esditem=eqid where esdsetid=" . $_REQUEST["eqsid"] . " order by esdid");
	$res2 = pg_query($con, "select eqsdescription from equipment_sets where eqsid=" . $_REQUEST["eqsid"]);
?>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<table class="tbllistnoborder" id="numbered">
<tr>
	<td style="text-align: center" colspan="3">ADD EQUIPMENT FOR <b><?php echo pg_fetch_result($res2, 0, 0);?></b></td>
</tr>
<?php
	//echo "<td>";
		while($row = pg_fetch_assoc($res)) {
			echo "<tr>
			<td></td>
			<td>" .
			uinput("esditem", $row["esdid"], $row["eqdescription"], "equipment_sets_det", "esditem", "esdid", $row["esdid"], false,true,300,"dd"," style='width: 100%'",true,false,true,false)
				. "<td>
				<input type='text'  id='exp_" . $row["esdid"] . "_val' style='width: 50px' value='" . $row["esdqty"]  . "' class='centered'
				onchange='updatevalue(\"equipment_sets_det\",\"esdqty\", this.value,\"esdid\",\"" . $row['esdid'] . "\",true,false,this,false,false)'>
			</td>
			<td>" .
			delbtn("equipment_sets_det", "esdid", $row["esdid"], "ajax/divs/equipment_sets.php?eqsid=" . $_REQUEST["eqsid"], "", "#newitem") . "
			</td>
			</tr>";
		}

		// Extra line for adding items with dropdown
	echo "
		<tr>
			<td></td>
			<td>
				<input type='text' class='dd' id='addequip' style='width: 300px;'>
				<input type='hidden' id='addequip_id' onchange='addequip(" . $_REQUEST['eqsid'] . ", this.value)'>
			</td>
			<td></td>
		</tr>";
		//echo "</td><td><input type='hidden' id='addequip_val'></td><td class='delbtn'></td></tr>";
?>
</table>
</body>
</html>