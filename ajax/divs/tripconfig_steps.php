<?php
include "../../inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
function addloc(tripid,val,dist) {
	// Increase distance based on location added
	//$("#distance").val(parseInt($("#distance").val()) + parseInt(dist));
	updatevalue("trip_config", "tcdistance", parseInt($("#distance").val()) + parseInt(dist), "tcid", <?php echo $_REQUEST["tcid"]; ?>, true, false, this, false, false);
	// Update trip config_det
	insertvalue("trip_config_det", "tcdtripid,tcdlocation", tripid + "," + val, "tcdid", $("#newitem"), "ajax/divs/tripconfig_steps.php?tcid=<?php echo $_REQUEST["tcid"]; ?>");
}

$(document).ready(function () {
	$(".smallbutton").click(function () {
		// Remove value from distances
		updatevalue("trip_config", "tcdistance", parseInt($("#distance").val()) - parseInt($("#step_" + $(this).attr("id").replace("trip_config_det_","") + "_val").val()), "tcid", <?php echo $_REQUEST["tcid"]; ?>, true, false, this, false, false);
	})

	// Update trip leg distance if changed here
	$(".locdistance").change(function () {
		// Update location distance in DB
		updatevalue("locations", "locdistance", $(this).val() , "locid", $(this).attr("id").replace("step_","").replace("_val",""), true, false, this, false, false);
		// Update trip distance in DB
		updatevalue("trip_config", "tcdistance", parseInt($("#distance").val()) + parseInt($(this).val()), "tcid", <?php echo $_REQUEST["tcid"]; ?>, true, false, this, false, false);
	})
	// Dropdown
	acomplete(".dd", "ajax/autocompletes/trip_steps.php", false, true, false);
	// Table rows
	tablerows();
})
</script>
<?php
	$distance = pg_fetch_result(pg_query($con, "select coalesce(tcdistance,0) as dist from trip_config where tcid=" . $_REQUEST["tcid"]), 0, 0);
	$res  = pg_query($con, "select *, locdescription || ' (' || lttype || ')' as locdescription from trip_config left join trip_config_det on tcid=tcdtripid left join locations on tcdlocation = locid left join location_types on loctype=ltid where tcdtripid=" . $_REQUEST["tcid"] . " order by tcdid");
	$res2 = pg_query($con, "select tcdescription from trip_config where tcid=" . $_REQUEST["tcid"]);
?>
</head>
<body>
<button onclick="$('#newitem').fadeOut();$('#workspace').load('tripconfig.php')" class="closebutton">X</button>
<table class="tbllistnoborder" id="numbered">
<tr>
	<td style="text-align: center" colspan="3">ADD TRIP STEPS FOR <b><?php echo pg_fetch_result($res2, 0, 0);?></b></td>
</tr>
<?php
	// Fetch steps as summary
	//$txtsteps = "";
	//$txtsteps = substr($txtsteps, 0, strlen($txtsteps) - 5);
	while($step = pg_fetch_assoc($res)) {
		echo "
		<td></td>
		<td>" .
		uinput("step", $step["tcdid"], $step["locdescription"], "trip_config_det", "tcdlocation", "tcdid", $step["tcdid"], false,true,300,"dd","style='width: 300px'",true, false, true, false)
		. "</td>
		<td>
		<input type='text'  id='step_" . $step["locid"] . "_val' style='width: 50px' value='" . $step["locdistance"]  . "' class='centered locdistance'> km
		</td>
		<td>" .
		delbtn("trip_config_det", "tcdid", $step["tcdid"], "ajax/divs/tripconfig_steps.php?tcid=" . $_REQUEST["tcid"], "smallbutton", "#newitem") . "</td></tr>";
	}
	// Extra line for adding items with dropdown
	echo "<tr><td></td><td>
	<input type='text' class='dd' id='addloc' style='width: 300px;'>
	</td>
	<td>
	<input type='text' class='' id='addloc_val' style='width: 50px;'>
	<input type='hidden' class='dd' id='addloc_id' onchange='addloc(" . $_REQUEST['tcid'] . ",this.value, $(\"#addloc_val\").val())'>";
	echo "</td><td class='delbtn'></td>";
?>
<input type="hidden" name="distance" id="distance" value="<?php echo $distance?>">
</table>
</body>
</html>