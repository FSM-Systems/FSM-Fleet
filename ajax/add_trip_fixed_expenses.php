<?php
include "../inc/session_test.php";
include "connection.inc";

// See if we have fixed expenses otherwise alert user to create them
$res = pg_query($con, "select count(*) from trip_config_expenses where tcetripid=" . $_REQUEST["tripconfigid"] );
if(pg_fetch_result($res, 0, 0) == "0") {
	echo "NOEXPENSE";
} else {
	// update
	$res = pg_query($con, "
	insert into trip_log_expenses (tletripid,tleetid,tlevalue)
	select " . $_REQUEST["tripid"] . ",tceexpense,tcefixedvalue from trip_config_expenses where tcetripid=" . $_REQUEST["tripconfigid"] );

	if(pg_result_error($res) != "") {
		echo pg_result_error($res);
	} else {
		echo "OK";
	}
}
?>