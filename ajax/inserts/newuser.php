<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$res = pg_query($con, "insert into login (lusername, lpassword, ldescription, lemail, company_id)
values (
'" . pg_escape_string($_REQUEST["lusername"]) . "',
'" . pg_escape_string($_REQUEST["lpassword"]) . "',
upper('" . pg_escape_string($_REQUEST["ldescription"]) . "'),
'" . $_REQUEST["lemail"] . "',
" . $_SESSION["company"] . "
) returning lid
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>