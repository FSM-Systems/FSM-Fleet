<!DOCTYPE html>
<html>
<head>
<title>DMS Truck Management System</title>
<meta name="description" content="Keep records, log and track all important data of your trucks. Optimize your workflow and expenditures. Save money and precious resources for your fleet">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="keywords" content="truck,management,system,database,data,fleet-management,fleet,haulage,dms,reporting,reports,trucks,fleet,optimise,employee,automation,efficiency,cost-effective,vehicles,vehicle">
<meta name="robots" content="index, nofollow">
<link rel="shortcut icon" href="favicon.ico" />
<style type="text/css">
@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700,300,400italic,700italic,600,600italic);
body, div.title, input {
	font-family: 'Open Sans', sans-serif;
}
</style>
<?php
session_start();
// Go to main if logged in
if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]== true) {
	?>
	<script type="text/javascript">
		window.location.href = "main.php";
	</script>
	<?php
	$_SESSION['LAST_ACTIVITY'] = time();
}

include "inc/jquery.inc";
include "inc/basemessages.php";
include "inc/connection.inc";
include "inc/config.inc";

?>
<script type="text/javascript">
$(document).ready(function () {
	// Focus on username for ease of use
	$("#u").focus();

	// Use return to act as login click button
	$("#u, #p").keypress(function (e) {
		if (e.which == 13) {
			$("#btnlogin").trigger("click");
		}
	})

	// Disable autocomplete
	$("input").attr("autocomplete", "false");

	// Tooltips to make things easier
	$(function() {
		$( document ).tooltip();
	});

	// Login Validation
	$("#login").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		},
		rules: {
			u: "required",
			p: "required",
		},
		messages: {
			u: "PLEASE INSERT YOUR USERNAME!",
			p: "PLEASE SUPPLY A PASSWORD!",
		}
	});

	// test function for delaying scripts
	function sleep(miliseconds) {
		var currentTime = new Date().getTime();
		while (currentTime + miliseconds >= new Date().getTime()) {
		}
	}

	 // Submit login if everything is ok
	$("#btnlogin").click(function () {
		if ($("#login").valid() == true) {
			// Call ajax for login check
			$.ajax({
				url: "ajax/check_login.php",
				method: "POST",
				data: {
					u: $("#u").val(),
					p: $("#p").val(),
				},
				success: function (data) {
					if (data == "OK") {
						//window.location.href = "main.php";
						//$(body).load("main.php", {reminder: true});
						var $form=$(document.createElement('form')).css({display:'none'}).attr("method","POST").attr("action","main.php");
						var $input=$(document.createElement('input')).attr('name','reminder').val(true);
						$form.append($input);
						$("body").append($form);
						$form.submit();
					} else {
						$.alert('WRONG USERNAME/PASSWORD COMBINATION! PLEASE TRY AGAIN.', function () {
							$("#u").focus()
						});
						//$("#u").val("").focus();
						//$("#p").val("");
					}
				},
				error: function (data) {
					$.alert('<?php echo AJAXERROR; ?>' + data, function () {
						$("#u").focus()
					})
				}
			});
		} else {
			$.alert('PLEASE FILL IN USERNAME AND PASSWORD! ', function () {
				$("#u").focus()
			});
		}
	});
});
</script>
</head>
<body>
<div class="head">
<img src="img/fsm.png" class="logo">
</div>
<div class="title">
<?php echo MAINTITLE; ?>
</div>
<div class="main">
	<div class="login">
	<form name="login" id="login">
	Username: <input type="text" name="u" id="u" autocomplete="false" style="width: 100px;">
	Password: <input type="password" name="p" id="p" style="width: 100px;">
	<input type="button"  id="btnlogin" value="DMS LOGIN" style="width: 80px">
	</form>
	<?php
	if(isset($_REQUEST["expired"])) {
		echo	"<label style='color: red; font-weight: bold;'>Your session has expired. Please login again.</label>";
	}
	if(isset($_REQUEST["invalid"])) {
		echo	"<label style='color: green; font-weight: bold;'>You have requested an invalid page. Please login before using the system.</label>";
	}
	?>
	</div>
</div>
	<?php
	if(SHOWSOCIAL == true) {
	?>
	<div class="credits">
		<a href="http://www.postgresql.org" target="_blank"><img src="img/pg.gif" alt="PostgreSQL Database"></a>
		<a href="http://www.php.net" target="_blank"><img src="img/php-power-white.gif" alt="PHP"></a>
		<a href="http://www.jquery.com" target="_blank"><img src="img/jquery.png" alt="jQuery"></a>
	</div>

	<div class="social">
		<a href="https://www.facebook.com/fsmsystems" target="_blank"><img src="img/social/fb.png" alt="Facebook"></a>
		<a href="https://plus.google.com/107400453957224519668" target="_blank"><img src="img/social/googleplus.png" alt="Google+"></a>
		<a href="https://twitter.com/@mazzofab" target="_blank"><img src="img/social/twitter.png" alt="Twitter"></a>
		<a href="https://www.linkedin.com/company/fsm-systems" target="_blank"><img src="img/social/linkedin.png" alt="LinkedIn"></a>
		<a href="https://www.youtube.com/channel/UCdhYhLkQ-Q04F8ruOpnraIA" target="_blank"><img src="img/social/youtube.png" alt="YouTube"></a>
	</div>
	<?php
	}
	?>
</body>
</html>