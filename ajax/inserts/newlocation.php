<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$res = pg_query($con, "insert into locations (locdescription, loctype,locwarningdays,loctriplegtime,company_id)
values (
trimwhite(upper('" . pg_escape_string($_REQUEST["locdescription"]) . "')),
" . $_REQUEST["loctype_id"] . ",
" . $_REQUEST["days"] . ",
" . ($_REQUEST["tripleg"] =="" ? 'null' : $_REQUEST["tripleg"])  . ",
" . $_SESSION["company"] . "
) returning locid
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>