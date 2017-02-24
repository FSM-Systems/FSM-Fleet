<?php
require_once "config.inc";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]== true) {
	if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > GLOBALSESSIONTIMEOUT)) {
	    // last request was more than 30 minutes ago
	    session_unset();     // unset $_SESSION variable for the run-time
	    session_destroy();   // destroy session data in storage
	    // Back to index
	    ?>
		<script type="text/javascript">
			parent.window.location.href = "<?php echo WWWADDRESS . PREFIX; ?>/index.php?expired";
		</script>
		<?php
		exit();
	}
	$_SESSION['LAST_ACTIVITY'] = time();
} else {
	?>
		<script type="text/javascript">
			parent.window.location.href = "<?php echo WWWADDRESS . PREFIX; ?>/index.php?invalid";
		</script>
		<?php
		exit();
}
// Includes on this app
set_include_path($_SERVER["DOCUMENT_ROOT"] . "/inc/");
?>