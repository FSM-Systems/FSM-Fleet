<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$res = pg_query($con, "insert into customers (cname, caddress, company_id)
values (
trimwhite(upper('" . pg_escape_string($_REQUEST["cname"]) . "')),
'" . $_REQUEST["caddress"] . "',
" . $_SESSION["company"] . "
) returning cid
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>