<?php
include "../inc/session_test.php";
include "connection.inc";

// Check if this trailer is attached to a truck which is performing a trip

$res = pg_query( $con, "select " . $_REQUEST["colnames"] . " from " . $_REQUEST["table"] . " where company_id=" . $_SESSION["company"] . " and " . $_REQUEST["colnames"] . " like '%" . urldecode($_REQUEST["colvals"]) . "%'");

if(pg_num_rows($res) > 0) {
	echo "TRUE";
} else {
	echo "FALSE";
}
?>