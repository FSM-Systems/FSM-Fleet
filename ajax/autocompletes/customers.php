<?php
include "../../inc/session_test.php";
include "connection.inc";

$term = $_GET['term'];

$q = pg_query($con, "select cid as id, cname as label from customers where company_id=" . $_SESSION["company"] . " and upper(cname) like upper('%$term%') order by label");

while($row = pg_fetch_assoc($q)) {
	$arr[] = array("id" => $row["id"], "label" => $row["label"]);
}

echo json_encode($arr);
?>