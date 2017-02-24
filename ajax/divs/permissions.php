<?php
include "../../inc/session_test.php";
include "connection.inc";
include "basemessages.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {

	$(".chkperm").click(function () {
		$.ajax({
			url: "inc/update_checkbox_field_userpermission.php",
			type: "POST",
			data: {
				lpuser: <?php echo $_REQUEST["id"]; ?>,
				lpperm: $(this).attr("id").replace("perm_",""),
				checked: $(this).is(":checked"),
				token: '<?php echo $_SESSION['atoken']; ?>',
			},
			success: function (data) {
				if (!$.isNumeric(data)) {
					$.alert('<?php echo QUERYERROR; ?>' + data);
				}
			},
			error: function (data) {
				$.alert('<?php echo AJAXERROR; ?>');
			},
		})
	});
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<?php
$login = pg_fetch_assoc(pg_query($con, "select * from login where lid=" . $_REQUEST["id"]), 0);
echo "Edit permissions for <label class='bold underline'>" . $_REQUEST["user"] . "</label><br>";
// Show all permissions and check the current ones
$res = pg_query($con, "select * from menu order by mtitle");
?>
<table class="tbllistnoborder" style="text-align: left">
<tr>
	<td colspan="3" class="bold">
		Menu Items:
	</td>
</tr>
<?php
while($row = pg_fetch_assoc($res)) {
	echo "<tr><td class='delbtn'><input type='checkbox' class='chkperm' id='perm_" . $row["mid"] . "' name='perm_" . $row["mid"] . "'";
	// Is user allowed?
	$res2 = pg_query($con, "select * from login_permissions where lpuser=" . $_REQUEST["id"]);
	while($perm = pg_fetch_assoc($res2)) {
		if($perm["lpperm"] == $row["mid"]) {
		 echo " checked";
		}
	}
	echo "></td><td><label for='perm_" . $row["mid"] . "'>" . $row["mtitle"] . "</td><td><label for='perm_" . $row["mid"] . "'>(" . $row["mdescription"] . ")" . "</label></td></tr>";
}
?>
<tr>
	<td colspan="3" class="bold">
		Actions:
	</td>
</tr>
<tr>
	<td class="delbtn"><input type="checkbox" id="copypaste" name="copypaste"
	<?php
	if($login["lcopypaste"] == "t") {
		echo " checked";
	}
	?>
	onclick="updatecheckbox('login', 'lcopypaste', this.checked, 'lid', <?php echo $_REQUEST["id"]; ?>)"
	></td>
	<td><label for="copypaste">Allow Copy/Cut/Paste</label></td>
	<td><label for="copypaste">(Allow the user to cut copy and paste data)</label></td>
</tr>
<tr>
	<td class="delbtn"><input type="checkbox" id="readonly" name="readonly"
	<?php
	if($login["lreadonly"] == "t") {
		echo " checked";
	}
	?>
	onclick="updatecheckbox('login', 'lreadonly', this.checked, 'lid', <?php echo $_REQUEST["id"]; ?>)"
	></td>
	<td><label for="readonly">Read Only User</label></td>
	<td><label for="readonly">(User can only read data)</label></td>
</tr>
<tr>
	<td class="delbtn"><input type="checkbox" id="stataccess" name="stataccess"
	<?php
	if($login["lstataccess"] == "t") {
		echo " checked";
	}
	?>
	onclick="updatecheckbox('login', 'lstataccess', this.checked, 'lid', <?php echo $_REQUEST["id"]; ?>)"
	></td>
	<td><label for="stataccess">Trip Statistics</label></td>
	<td><label for="stataccess">(User can view trip accouting)</label></td>
</tr>
<tr>
	<td class="delbtn"><input type="checkbox" id="stepbystep" name="stepbystep"
	<?php
	if($login["ltripsteps"] == "t") {
		echo " checked";
	}
	?>
	onclick="updatecheckbox('login', 'ltripsteps', this.checked, 'lid', <?php echo $_REQUEST["id"]; ?>)"
	></td>
	<td><label for="stepbystep">Step by step trips</label></td>
	<td><label for="stepbystep">(User edits trip step dates one at the time)</label></td>
</tr>
<tr>
	<td class="delbtn"><input type="checkbox" id="invoicing" name="invoicing"
	<?php
	if($login["linvoicing"] == "t") {
		echo " checked";
	}
	?>
	onclick="updatecheckbox('login', 'linvoicing', this.checked, 'lid', <?php echo $_REQUEST["id"]; ?>)"
	></td>
	<td><label for="invoicing">Invoice Information</label></td>
	<td><label for="invoicing">(User can view and edit invoice information)</label></td>
</tr>
<tr>
	<td class="delbtn"><input type="checkbox" id="tripmod" name="tripmod"
	<?php
	if($login["ltripmod"] == "t") {
		echo " checked";
	}
	?>
	onclick="updatecheckbox('login', 'ltripmod', this.checked, 'lid', <?php echo $_REQUEST["id"]; ?>)"
	></td>
	<td><label for="tripmod">Modify Trip</label></td>
	<td><label for="tripmod">(User can trip logs after they've been closed)</label></td>
</tr>
</table>
</body>
</html>