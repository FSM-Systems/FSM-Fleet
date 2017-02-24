<?php
include "../inc/session_test.php";
include "connection.inc";

pg_query($con, "begin;");

$res = pg_query($con, "insert into trip_config (tcdescription,company_id) values (upper('" . $_REQUEST["tripname"] . "'), " . $_SESSION["company"] . ") returning tcid");
$tcid = pg_fetch_result($res, 0, 0);

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	$res = pg_query($con, "insert into trip_config_expenses (tcetripid,tceexpense,tcefixedvalue) select " . $tcid . ",tceexpense,tcefixedvalue from trip_config_expenses where tcetripid=" . $_REQUEST["id"]);
	if(pg_result_error($res) != "") {
		echo pg_result_error($res);
	} else {
		pg_query($con, "commit;");
		echo "OK";
	}
}
?>