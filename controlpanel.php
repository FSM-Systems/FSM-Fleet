<?php
include "inc/session_test.php";
require_once "itemcreators.php";
require_once "connection.inc";
?>
<div class="maincp">
	<div class="leftpanel">
	<?php
	echo cpitem($con, "tripstatus");
	?>
	<br>
	<br>
	<br>
	<?php
	echo cpitem($con, "ontheroad");
	?>
	</div>
	<div class="rightpanel">
	<?php
	echo cpitem($con, "notbooked");
	?>
	</div>
</div>