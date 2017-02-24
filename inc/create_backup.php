<?php
include "session_test.php";
include "connection.inc";
include "ajax_security.php";

// Step 1, dump db in /tmp
$datenow = date("d-m-Y_H:i:s");
$dumpfile = BACKUPDIR . "databasedump_" . $datenow . ".sql";
$zipdumpfile = BACKUPDIR . "databasedump_" . $datenow . ".sql.zip";

if (ip_is_private($_SERVER['REMOTE_ADDR'])) {
	exec(DUMPCOMMAND . " -C -d mtl -h 127.0.0.1 -p 5433 -U mtl -f " . $dumpfile, $output);
} else {
	exec(DUMPCOMMAND . " -C -d mtl -h 127.0.0.1 -p 5432 -U mtl -f " . $dumpfile, $output);
}
if(empty($output)) {
	// Success now compress the file with zip
	exec(ZIPCOMMAND . " " . $zipdumpfile . " " . $dumpfile, $output);
	//echo $output[0];
	if(strpos($output[0], "deflated")) {
		// Remove original dump
		unlink($dumpfile);
		// Success update database
		$res = pg_query($con, "insert into backups (bfilename, company_id) values ('" . $zipdumpfile . "', " . $_SESSION["company"] . ")");
		if(pg_result_error($res) != "") {
			echo pg_result_error($res);
		} else {
			echo "SUCCESS§§§databasedump_" . $datenow . ".sql.zip";
		}
	} else {
		echo "ERROR";
	}
} else {
	echo "ERROR";
}
?>