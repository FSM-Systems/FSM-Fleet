<?php
include "../../../inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	$(".dp").datepicker();

	$("#going").change(function () {
		updatevalue("trip_log", "tlcargo", $(this).val(), "tlid", <?php echo $_REQUEST["id"]?>, false, false, this, false,true);
	})

	$("#returning").change(function () {
		updatevalue("trip_log", "tlcargo_ret", $(this).val(), "tlid", <?php echo $_REQUEST["id"]?>, false, false, this, false,true);
	})
})
</script>
</head>
<body>

<?php
$res = pg_query($con, "select tlid, tlcargo, tlcargo_ret from trip_log where tlid=" . $_REQUEST["id"]);
$row = pg_fetch_assoc($res, 0);
echo
"
<table class='tbllistnoborder' align='center' style='width: 100%'>
<tr>
<td class='bold'>
Cargo Going:
</td>
<td class='bold'>
Cargo Returning:
</td>
</tr>
<tr>
<td>
<textarea id='going' style='height: 70px; width: 95%'>" . $row["tlcargo"] . "</textarea>
</td>
<td>
<textarea id='returning' style='height: 70px; width: 95%'>" . $row["tlcargo_ret"] . "</textarea>
</td>
</tr>
</table>
";
?>
</body>
</html>