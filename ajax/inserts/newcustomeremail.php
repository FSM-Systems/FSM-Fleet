<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$res = pg_query($con, "insert into customer_emails (cecid,ceemail)
values (
" . $_REQUEST["cid"] . ",
trimwhitenoup('" . $_REQUEST["ceemail"] . "')
) returning ceid
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>