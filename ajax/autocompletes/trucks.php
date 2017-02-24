<?php
include "../../inc/session_test.php";
include "connection.inc";
// search by numberplate or make
$term = $_GET['term'];

$q = pg_query($con, "select tid as id, tnumberplate as label from trucks where company_id=" . $_SESSION["company"] . " and (upper(tnumberplate) like upper('%$term%') or upper(tmake) like upper('%$term%')) order by label");

while($row = pg_fetch_assoc($q)) {
	$arr[] = array("id" => $row["id"], "label" => $row["label"]);
}

echo json_encode($arr);
?>