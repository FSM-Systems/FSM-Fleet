<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

// Build query string dynamically
$strcolumns = "lttype,";
$strvalues = "upper('" . $_REQUEST['lttype'] . "'),";

// Arrival Date
if($_REQUEST['arrdate'] == "true") {
	$strcolumns .= "ltarrivaldate,";
	$strvalues .= "true,";
}

// Loading Date
if($_REQUEST['loaddate'] == "true") {
	$strcolumns .= "ltloadingdate,";
	$strvalues .= "true,";
}

// Offloading Date
if($_REQUEST['offloaddate'] == "true") {
	$strcolumns .= "ltoffloadingdate,";
	$strvalues .= "true,";
}

// Departure date
if($_REQUEST['depdate'] == "true") {
	$strcolumns .= "ltdeparturedate,";
	$strvalues .= "true,";
}


$strcolumns = substr($strcolumns, 0, strlen($strcolumns) - 1);
$strvalues = substr($strvalues, 0, strlen($strvalues) - 1);

// Add company ID
$strcolumns .= ",company_id";
$strvalues .= "," . $_SESSION["company"];


$res = pg_query($con, "insert into location_types (" . $strcolumns . ") values (" . $strvalues . ") returning ltid" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>