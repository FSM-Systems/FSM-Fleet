<?php
include "../inc/session_test.php";
require_once "connection.inc";
include "ajax_security.php";

$res = pg_query( $con, "
select sum(exp) from (
select sum(case when troadlicense <= now() + interval '30 days' then 1 else 0 end) as exp from trucks where company_id=" . $_SESSION['company'] . "
union
select sum(case when trroadlicense <= now() + interval '30 days' then 1 else 0 end) as exp from trailers where company_id=" . $_SESSION['company'] . "
union
select sum(case when dlicenseexp <= now() + interval '30 days' then 1 else 0 end) as exp from drivers where company_id=" . $_SESSION['company'] . "
union
select SUM(case when dpassportexp <= now() + interval '30 days' then 1 else 0 end) as exp from drivers where company_id=" . $_SESSION['company'] . "
) as foo
");

echo pg_fetch_result($res, 0, 0);
?>