<?php
include "session_test.php";
include "connection.inc";

if($_REQUEST["checked"] == "true") {
	$strsql = "insert into login_permissions (lpuser,lpperm) values (" . $_REQUEST["lpuser"] . "," . $_REQUEST["lpperm"] . ") returning lpid";
} else {
	$strsql = "delete from login_permissions where  lpuser=" . $_REQUEST["lpuser"] . "  and lpperm= " . $_REQUEST["lpperm"] . " returning lpid";
}

$res = pg_query($con, $strsql);

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>