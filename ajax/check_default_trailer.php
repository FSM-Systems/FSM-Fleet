<?php
include "../inc/session_test.php";
include "connection.inc";

$res = pg_query( $con, "select trid, trnumberplate || ' (' || trmake || ' - ' || traxles || ' axles)'  from trucks left join trailers on ttrailer=trid where tid=" . $_REQUEST["tltruck_id"]);
$trailer = pg_fetch_result($res, 0, 0) . "§§§" . pg_fetch_result($res, 0, 1);

echo $trailer;
?>