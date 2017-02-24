<?php
include "../../inc/session_test.php";
include "connection.inc";

$term = $_GET['term'];

$q = pg_query($con, "select did as id, dname as label from drivers where company_id=" . $_SESSION["company"] . " and upper(dname) like upper('%$term%') order by label");

while($row = pg_fetch_assoc($q)) {
	$arr[] = array("id" => $row["id"], "label" => $row["label"]);
}

echo json_encode($arr);
?>