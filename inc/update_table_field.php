<?php
include "session_test.php";
include "connection.inc";
include "ajax_security.php";

$colval = "'" . $_REQUEST["colval"] . "'";

if($_REQUEST['isanumber']== "true") {
	if(strlen($_REQUEST['colval']) >0) {
		$colval = $_REQUEST['colval'];
	} else {
		$colval = "null";
	}
}

if($_REQUEST['uppercase']== "true") {
	if(strlen($_REQUEST['colval']) >0) {
		$colval = "regexp_replace(upper('" . pg_escape_string($_REQUEST['colval']) . "'), '\s+', ' ', 'g')";
	} else {
		$colval = "null";
	}
}

if($_REQUEST["isadate"]== "true") {
	if(strlen($_REQUEST['colval']) >0) {
		$colval = "'" . $_REQUEST['colval'] . "'";
	} else {
		$colval = "null";
	}
}

if($_REQUEST["textarea"]== "true") {
	if(strlen($_REQUEST['colval']) >0) {
		$colval = "'" . pg_escape_string($_REQUEST['colval']) . "'";
	} else {
		$colval = "null";
	}
}

$res = pg_query($con, "update " .  $_REQUEST['table'] . " set " . $_REQUEST['colname'] . "=" . $colval . " where " . $_REQUEST['colnameid'] . "=" . $_REQUEST['colvalid'] . " returning " . $_REQUEST['colnameid'] );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>