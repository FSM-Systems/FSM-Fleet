<?php
include "../inc/session_test.php";
include "connection.inc";

$res = pg_query( $con, "select ceemail from customer_emails where cecid=" . $_REQUEST["cid"] . "
union
select lemail from login where lemail is not null
");

if(pg_num_rows($res) > 0 ) {
	$ret = "";
	while($row = pg_fetch_assoc($res)) {
		$ret .= $row["ceemail"] . "+++";
	}
	echo substr($ret, 0, strlen($ret)- 3);
} else {
	echo "0";
}
?>