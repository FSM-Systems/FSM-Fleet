<?php
include "../../inc/session_test.php";
include "connection.inc";

$term = $_GET['term'];

$q = pg_query($con, "select qpid as id, qpdescription as label from quotation_permits where upper(qpdescription) like upper('%$term%') order by label");

while($row = pg_fetch_assoc($q)) {
	$arr[] = array("id" => $row["id"], "label" => $row["label"]); 	
}

echo json_encode($arr);
?>