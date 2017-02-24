<?php
include "../inc/session_test.php";
include "connection.inc";

// Swap trailers	
pg_query($con, "update trucks set ttrailer=null where ttrailer=" . $_REQUEST["ttrailer_id"] . " and tid <> " .$_REQUEST["truckid"]);

?>