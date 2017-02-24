<?php
include "../../inc/connection.inc";
include "../../inc/session_test.php";
include "basemessages.php";
?>
<!DOCTYPE html>
<html>
<head>

</head>
<body>
<?php
$res = pg_query($con, "select * from drivers left join driver_phones on did=dpid where did=" . $_REQUEST["did"]);
$d = pg_fetch_assoc($res, 0);
?>
</body>
</html>