<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$res = pg_query($con, "insert into customer_phones (cpcid,cpphoneno)
values (
" . $_REQUEST["cid"] . ",
trimwhite('" . $_REQUEST["cpphoneno"] . "')
) returning cpid
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>