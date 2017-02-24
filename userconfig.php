<?php
include "inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	$("#new").click(function () {
		$("#newitem").load("ajax/divs/newuser.php", {btntext: "SYSTEM USER"}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

	$(".perms").click(function () {
		// Pass also user description to make things easier when editing perms
		$("#newitem").load("ajax/divs/permissions.php", {id: $(this).attr("id").replace("lperm_",""), user: $(this).closest("td").prevAll(":has(input):last").find("input").val()}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

		// Validation for elements
	$("#users").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		}
	});

	$('[name*="lemail"]').each(function() {
		$(this).rules('add', {
			required: true,
			email: true,
	    });
	});

	// Convert all passwords to password field
	$('[name*="lpassword"]').each(function() {
		$(this).attr("type", "password");
	});

	searchbox();
	excel();
})
</script>
</head>
<body>
<div style="float: right"><button id="new"><img class="icon" src="icons/login.png" alt=""> Create a new System User</button></div>
<br><br>
<div class="topline">
Current users registered on the System:
<br>
<form id="users" name="users">
	<table class="tbllist searchtbl" cellpadding=2 cellspacing=0 style="width: 65%">
	<tr>
		<th>Username</th>
		<th>Password</th>
		<th>Description</th>
		<th>Email Address</th>
		<th></th>
		<th></th>
	</tr>
	<?php
	$res = pg_query($con, "select * from login where company_id=" . $_SESSION["company"] . " order by lid");
	while($row = pg_fetch_assoc($res)) {
		echo "
			<tr class='tbl'>
					<td>" . uinput("lusername", $row["lid"], $row["lusername"], "login", "lusername", "lid", $row["lid"], false,false,null,null,null,false,false,true,false) . "</td>
					<td>" . uinput("lpassword", $row["lid"], $row["lpassword"], "login", "lpassword", "lid", $row["lid"], false,false,null,null,null,false,false,true,false) . "</td>
					<td>" . uinput("ldescription", $row["lid"], $row["ldescription"], "login", "ldescription", "lid", $row["lid"], false,true,300,null,null,false,false,true,false) . "</td>
					<td>" . uinput("lemail", $row["lid"], $row["lemail"], "login", "lemail", "lid", $row["lid"], false,false,300,null,null,false,false,true,false) . "</td>";
					?>
					<td class="delbtn"><button type="button" class="perms" id="lperm_<?php echo $row['lid']; ?>"><img class="smallbutton" src="icons/lock.png" alt=""></button></td>
					<?php
					echo "
					<td class='delbtn'>" . delbtn("login", "lid", $row["lid"], "userconfig.php", null, "#workspace")  . "</td>
			</tr>
		";
	}
	?>
	</table>
</form>
</div>
</body>
</html>