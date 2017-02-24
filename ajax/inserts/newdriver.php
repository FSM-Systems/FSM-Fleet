<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$res = pg_query($con, "insert into drivers (dname, dlicenceno, dlicenseexp, dpassportno, dpassportexp, company_id)
values (
trimwhite('" . pg_escape_string($_REQUEST["dname"]) . "'),
trimwhite('" . $_REQUEST["dlicenceno"] . "'),
'" . $_REQUEST["dlicenseexp"] . "',
trimwhite('" . $_REQUEST["dpassportno"] . "'),
'" . $_REQUEST["dpassportexp"] . "',
" . $_SESSION["company"] . "
) returning did
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>