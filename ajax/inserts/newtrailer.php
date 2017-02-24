<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

$res = pg_query($con, "insert into trailers (trnumberplate, trchassisnumber, trmake, tryear, traxles, trroadlicense, company_id)
values (
trimwhite(upper('" . $_REQUEST["trnumberplate"] . "')),
trimwhite(upper('" . $_REQUEST["trchassisnumber"] . "')),
trimwhite(upper('" . $_REQUEST["trmake"] . "')),
" . $_REQUEST["tryear"] . ",
" . $_REQUEST["traxles"] . ",
'" . $_REQUEST["trroadlicense"] . "',
" . $_SESSION["company"] . "
) returning trid
" );

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	echo pg_fetch_result($res, 0, 0);
}
?>