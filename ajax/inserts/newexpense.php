<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$cols = "";
$vals = "";

if($_REQUEST["etfixedvalue"] != "") {
	$cols = "etdescription, etfixedvalue";
	$vals = "trimwhite(upper('" . pg_escape_string($_REQUEST["etdescription"]) . "'))," . $_REQUEST["etfixedvalue"];
} else {
	$cols = "etdescription";
	$vals = "trimwhite(upper('" . pg_escape_string($_REQUEST["etdescription"]) . "'))";
}

$cols .= ",company_id";
$vals .= "," . $_SESSION["company"];

$res = pg_query($con, "insert into expense_types (" . $cols . ")
values (" . $vals . ") returning etid
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>