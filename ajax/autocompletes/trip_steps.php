<?php
include "../../inc/session_test.php";
include "connection.inc";

$term = $_GET['term'];

$q = pg_query($con, "select locid as id, locdescription || ' (' || lttype || ')' as label, coalesce(locdistance,0) as value from locations left join location_types on loctype=ltid where locations.company_id=" . $_SESSION["company"] . " and upper(locdescription) like upper('%$term%') order by label");

while($row = pg_fetch_assoc($q)) {
	$arr[] = array("id" => $row["id"], "label" => $row["label"], "thevalue" => $row["value"]);
}

echo json_encode($arr);
?>