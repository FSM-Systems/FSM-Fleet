<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

// Fetch current trip config ip after creating it
$res = pg_query($con, "insert into trip_config (tcdescription,company_id) values (trimwhite(upper('" . pg_escape_string($_REQUEST["tcdescription"]) ."')), " . $_SESSION["company"] . ") returning tcid");
if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	$tripid = pg_fetch_result($res ,0 , 0);
	echo $tripid; // echo numeric so we know query has succeeded
}
?>