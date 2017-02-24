<?php
include "session_test.php";
$dir = $_REQUEST["filedir"];
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $_REQUEST['filename'] . '"');
header('Cache-Control: max-age=0');
readfile($dir . $_REQUEST['filename']);
?>