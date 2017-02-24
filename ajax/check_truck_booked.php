<?php
include "../inc/session_test.php";
include "connection.inc";

$res = pg_query( $con, "select count(tltruck) from trip_log where tltruck=" . $_REQUEST["tltruck_id"] . " and tlclosed=false ");
$count = pg_fetch_result($res, 0, 0);

if($count == 0) {
	echo "OK";
} else {
	echo "NO";
}
?>