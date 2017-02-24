<?php
//clear session from disk
session_start();
session_destroy();
header("Location: index.php");
?>