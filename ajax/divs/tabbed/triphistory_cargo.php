<?php
include "../../../inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
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
<textarea id='going' style='height: 70px; width: 95%' disabled='true'>" . $row["tlcargo"] . "</textarea>
</td>
<td>
<textarea id='returning' style='height: 70px; width: 95%' disabled='true'>" . $row["tlcargo_ret"] . "</textarea>
</td>
</tr>
</table>
";
?>
</body>
</html>