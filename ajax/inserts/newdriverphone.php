<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$res = pg_query($con, "insert into driver_phones (dpdid,dpphoneno)
values (
" . $_REQUEST["did"] . ",
trimwhite('" . $_REQUEST["dpphoneno"] . "')
) returning dpid
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>