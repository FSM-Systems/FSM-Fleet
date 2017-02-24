<?php
include "../inc/session_test.php";
include "connection.inc";

$res = pg_query( $con, "select 
case when dlicenseexp <= now() + interval '30 days' then to_char(dlicenseexp, 'dd/mm/yyyy') else null end as l,
case when dpassportexp <= now() + interval '30 days' then to_char(dpassportexp, 'dd/mm/yyyy') else null end as p
from drivers
where did=" . $_REQUEST["did"]);

$res2 = pg_query($con, "select count(tldriver) from trip_log where tldriver=" . $_REQUEST["did"] . " and tlclosed=false");

$exp = pg_fetch_result($res, 0, 0) . "§§§" . pg_fetch_result($res, 0, 1) . "§§§" . pg_fetch_result($res2, 0, 0);

echo $exp;
?>