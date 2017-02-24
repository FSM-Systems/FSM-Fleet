<?php
$user_error = 'Access denied - not an AJAX request...';
if(!isset($_REQUEST['token'])) {
	trigger_error($user_error ." 1", E_USER_ERROR);
}

if (isset($_REQUEST["token"]) && ($_SESSION["atoken"] != $_REQUEST["token"])) {
	trigger_error($user_error . " 2", E_USER_ERROR);
}
// prevent direct access -- security feature
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
	trigger_error($user_error . " 3", E_USER_ERROR);
}
?>