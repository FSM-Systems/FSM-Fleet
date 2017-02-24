<?php
include "../../inc/session_test.php";
include "connection.inc";

$term = $_GET['term'];
$q = pg_query($con, "select trid as id, trnumberplate || ' (' || trmake || ' - ' || traxles || ' axles)'  as label from trailers where upper(trnumberplate || ' (' || trmake || ' - ' || traxles || ' axles)') like upper('%$term%') and company_id=" . $_SESSION["company"] . " order by label");

while($row = pg_fetch_assoc($q)) {
	$arr[] = array("id" => $row["id"], "label" => $row["label"]);
}

echo json_encode($arr);
?>