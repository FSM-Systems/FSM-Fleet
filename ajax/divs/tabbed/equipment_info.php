<?php
include "../../../inc/session_test.php";
include "connection.inc";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$("#print").click(function () {
	$("#printitem").attr("src", "prints/checklist.php?id=<?php echo $_REQUEST["id"]; ?>")
})
</script>
</head>
<body>
<?php
$setid = pg_fetch_result(pg_query($con, "select tlequipment from trip_log where tlid=" . $_REQUEST["id"]), 0, 0);
if($setid != "") {
	$res = pg_query($con, "select eqdescription, esdqty
	from equipment_sets 
	left join equipment_sets_det on eqsid=esdsetid
	left join equipment on esditem=eqid
	where eqsid=" . $setid . " order by eqsid");
	
	if(pg_num_rows($res) > 0) {
		echo "<div style='text-align: left'><ul>";
		while($row = pg_fetch_assoc($res)) {
			echo "<li>" . $row["esdqty"] . " pieces of " . $row["eqdescription"] . "</li>";
		}
		echo "</ul></div>";
	} else {
		echo "This vehicle	has left without any equipment!";
	}
} else {
	echo "This trip has been created without a set of tools!<br><br>";	
}
?>
<button id="print"><img src="icons/print.png" alt=""> PRINT CHECK LIST</button>
</body>
</html>