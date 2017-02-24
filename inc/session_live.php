<?php
include "session_test.php";
require_once "config.inc";
include "ajax_security.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > GLOBALSESSIONTIMEOUT)) {
	// Say no and destroy all current sessions
	session_destroy();
	echo "NO"	;
}
?>