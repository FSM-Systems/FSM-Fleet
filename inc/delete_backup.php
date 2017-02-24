<?php
include "connection.inc";
include "session_test.php";

// Delete file
$dumpfile = BACKUPDIR . $_REQUEST["fname"];
$res = pg_query($con, "delete from backups where bid=" . $_REQUEST["id"] . " returning bid");
if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	unlink($dumpfile);
	echo pg_fetch_result($res, 0, 0);
}
?>