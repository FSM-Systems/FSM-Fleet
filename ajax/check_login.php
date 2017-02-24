<?php
// Check login does not isclude testing as we are coming from external resource.
include "../inc/connection.inc";

$res = pg_query( $con, "select * from login inner join _companies on company_id=compid where lusername='" . $_REQUEST['u'] . "' and lpassword='" . $_REQUEST['p'] . "' and active=true");
if (pg_num_rows($res) == 1) {
	$login = pg_fetch_assoc($res);
	// Ok we have a match, set sessions and send back response
	session_start();
	$_SESSION['logged_in'] = true;
	$_SESSION['id'] = $login['lid'];
	$_SESSION['description'] = $login['ldescription'];
	$_SESSION['coypaste'] = $login['lcopypaste'];
	$_SESSION['readonly'] = $login['lreadonly'];
	$_SESSION['stataccess'] = $login['lstataccess'];
	$_SESSION['tripsteps'] = $login['ltripsteps'];
	$_SESSION['tripmod'] = $login['ltripmod'];
	$_SESSION['invoicing'] = $login['linvoicing'];
	$_SESSION['email'] = $login['lemail'];
	$_SESSION['company'] = $login['company_id'];
	$_SESSION['logo'] = $login['_clogo'];
	$_SESSION['companyname'] = $login['_ccompanyname'];
	$_SESSION['companyphone'] = $login['_ccompanyphone'];
	$_SESSION['companyemail'] = $login['_ccompanyemail'];
	$_SESSION["smtp"] = $login["_csmtp"];
	$_SESSION["smtpuser"] = $login["_csmtpuser"];
	$_SESSION["smtppassword"] = $login["_csmtppassword"];
	$_SESSION["maintitle"] = $login['_ccompanyname'] . " Truck Information System";
	// Set session here 1 time, then we check if its ok from all pages
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
	// Set user token
	$_SESSION["atoken"] = uniqid();
	echo "OK";
} else {
	// Nope, wrong uname and pw
	echo "NO";
}
?>