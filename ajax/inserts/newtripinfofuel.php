<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

// Parse location
if($_REQUEST["tlflocation"] !== "" ) {
	$loc = $_REQUEST["tlflocation"] ;
} else {
	$loc = "null";
}

$res = pg_query($con, "insert into trip_log_fuel (tlfdate,tlftripid,tlflocation,tlfqty,tlfvalue)
values (
'" . $_REQUEST["tlfdate"] . "',
" . $_REQUEST["tlftripid"] . ",
" . $loc . ",
" . $_REQUEST["tlfqty"] . ",
" . $_REQUEST["tlfvalue"] . "
) returning tlfid
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>