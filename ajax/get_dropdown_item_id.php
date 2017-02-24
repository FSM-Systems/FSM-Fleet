<?php
include "../inc/session_test.php";
include "connection.inc";

// Check if this trailer is attached to a truck which is performing a trip

$res = pg_query( $con, "select " . $_REQUEST["retid"] . " from " . $_REQUEST["table"] . " where " . $_REQUEST["colnames"] . " like '%" . urldecode($_REQUEST["colvals"]) . "%'");

if(pg_num_rows($res) > 0) {
	echo pg_fetch_result($res, 0, 0);
} else {
	echo "FALSE";
}
?>