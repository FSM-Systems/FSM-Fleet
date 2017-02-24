<?php
include "../inc/session_test.php";
include "connection.inc";

// Check if this trailer is attached to a truck which is performing a trip

$resavg = pg_query($con, "select (sum(tlevalue)/(case when now()::date - tlbooked::date = 0 then 1 else now()::date - tlbooked::date end)::integer)::numeric(12,2) from trip_log left join trip_log_expenses on tlid=tletripid left join expense_types on tleetid=etid where (etaverageperday=true or position('ALLOWANCE' in upper(etdescription)) > 0) and tletripid=" . $_REQUEST["id"] . " group by tlbooked");
if(pg_num_rows($resavg) > 0) {
	$avg = pg_fetch_result($resavg,0 ,0);
} else {
	$avg = 0;
};

if(pg_result_error($resavg) != "") {
	echo pg_result_error($resavg);
} else {
	echo $avg;
}
?>