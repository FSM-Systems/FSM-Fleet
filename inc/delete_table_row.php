<?php
include "session_test.php";
include "connection.inc";
include "ajax_security.php";

$res = pg_query($con, "delete from " . $_REQUEST['table'] . " where " . $_REQUEST['colnameid'] . "=" . $_REQUEST['colvalid'] . " returning " . $_REQUEST['colnameid'] );


if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>