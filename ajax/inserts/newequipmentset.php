<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$res = pg_query($con, "insert into equipment_sets (eqsdescription, company_id)
values (
trimwhite(upper('" . $_REQUEST["eqsdescription"] . "')), " . $_SESSION["company"] . "
) returning eqsid
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>