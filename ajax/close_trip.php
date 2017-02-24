<?php
include "../inc/session_test.php";
include "connection.inc";

$res = pg_query( $con, "update trip_log set tlclosed=true where tlid=" . $_REQUEST['tripid'] . " returning tlid");

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>