<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$trailer = "null";
if($_REQUEST["ttrailer_id"] != "") {
	$trailer = $_REQUEST["ttrailer_id"];
}

$res = pg_query($con, "insert into trucks (tnumberplate, tenginenumber, tchassisnumber, ttrailer, tmake, tyear, troadlicense, company_id)
values (
rtrim(replace(upper('" . $_REQUEST["tnumberplate"] . "'), ' ', '')),
trimwhite(upper('" . $_REQUEST["tenginenumber"] . "')),
trimwhite(upper('" . $_REQUEST["tchassisnumber"] . "')),
" . $trailer . ",
rtrim(upper('" . $_REQUEST["tmake"] . "')),
" . $_REQUEST["tyear"] . ",
'" . $_REQUEST["troadlicense"] . "',
" . $_SESSION["company"] . "
) returning tid
" );

if($_REQUEST["ttrailer_id"] != "") {
	// Remove the current trailer from any truck
	pg_query($con, "update trucks set ttrailer=null where ttrailer=" . $_REQUEST["ttrailer_id"] . " and tid <> " . pg_fetch_result($res, 0, 0));
}

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>