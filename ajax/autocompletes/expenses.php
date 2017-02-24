<?php
include "../../inc/session_test.php";
include "connection.inc";

$term = $_GET['term'];

$q = pg_query($con, "select etid as id, etdescription as label, etfixedvalue as value from expense_types where upper(etdescription) like upper('%$term%') order by label");

while($row = pg_fetch_assoc($q)) {
	$arr[] = array("id" => $row["id"], "label" => $row["label"], "thevalue" =>$row["value"]); 	
}

echo json_encode($arr);
?>