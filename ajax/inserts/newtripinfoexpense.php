<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

// Parse location
if($_REQUEST["tlelocation"] !== "" ) {
	$loc = $_REQUEST["tlelocation"] ;
} else {
	$loc = "null";
}

$res = pg_query($con, "insert into trip_log_expenses (tledate,tletripid,tlelocation,tleetid,tlevalue)
values (
'" . $_REQUEST["tledate"] . "',
" . $_REQUEST["tletripid"] . ",
" . $loc . ",
" . $_REQUEST["tleetid"] . ",
" . $_REQUEST["tlevalue"] . "
) returning tleid
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>