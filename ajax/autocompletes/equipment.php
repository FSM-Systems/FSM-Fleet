<?php
include "../../inc/session_test.php";
include "connection.inc";

$term = $_GET['term'];

$q = pg_query($con, "select eqid as id, eqdescription as label from equipment where upper(eqdescription) like upper('%$term%') and company_id=" . $_SESSION["company"] . " order by label");

while($row = pg_fetch_assoc($q)) {
	$arr[] = array("id" => $row["id"], "label" => $row["label"]); 	
}

echo json_encode($arr);
?>