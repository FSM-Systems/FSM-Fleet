<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$res = pg_query($con, "insert into equipment (eqdescription, company_id)
values (
trimwhite(upper('" . pg_escape_string($_REQUEST["eqdescription"]) . "')), " . $_SESSION["company"] . "
) returning eqid
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>