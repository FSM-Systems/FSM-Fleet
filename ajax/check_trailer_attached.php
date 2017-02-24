<?php
include "../inc/session_test.php";
include "connection.inc";

// Check if this trailer is attached to a truck which is performing a trip

$res = pg_query( $con, "select tid, tnumberplate from trucks where ttrailer=" . $_REQUEST["ttrailer_id"]);

if(pg_num_rows($res) > 0) {
	echo "true" . "§§§" . pg_fetch_result($res, 0, 1);
}
?>