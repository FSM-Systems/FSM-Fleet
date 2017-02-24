<?php
include "../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$res = pg_query( $con, "select " . $_REQUEST["col"] . " from " . $_REQUEST["table"] . " where " . $_REQUEST["rowid"] . "=" . $_REQUEST["rowidvalue"]);

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>