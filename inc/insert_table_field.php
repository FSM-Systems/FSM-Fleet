<?php
include "session_test.php";
include "connection.inc";
include "ajax_security.php";

if(is_string($_REQUEST["colvals"]) && strpos($_REQUEST["colvals"], ",") == 0) {
	$colvals = "'" . $_REQUEST["colvals"] . "'";
} else {
	$colvals = $_REQUEST["colvals"];
}

$res = pg_query($con, "insert into " . $_REQUEST["table"] . "(" . $_REQUEST["colnames"] . ", company_id) values (" . $colvals . ", " . $_SESSION["company"] . ") returning " . $_REQUEST["retid"] . ";");


if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}

?>