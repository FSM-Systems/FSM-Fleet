<?php
include "../../inc/session_test.php";
include "basemessages.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	$("#newfrm").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		},
		rules: {
			lusername: "required",
			lpassword: "required",
			ldescription: "required",
			lemail: {
				required: true,
				email: true,
			},
		},
	});

	// Check also that at least one checkbox is selected
	$("#btnnew").click(function () {
		if ($("#newfrm").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newuser.php",
				type: "POST",
				data: {
					lusername: $("#lusername").val(),
					lpassword: $("#lpassword").val(),
					ldescription: $("#ldescription").val(),
					lemail: $("#lemail").val(),
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						$.alert('NEW USER CREATED: ID ' + data);
						$("#newitem").fadeOut();
						$("#workspace").load("userconfig.php");
					} else {
						$.alert('<p style="color: red">YOU CANNOT USE THIS USERNAME/PASSWORD COMBINATION!</p>');
					}
				},
				error: function (data) {
					$.alert('<?php echo AJAXERROR; ?>');
				},
			})
		} else {
			$.alert('<?php echo CHECKFORM; ?>');
		}
	});
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<form name="newfrm" id="newfrm">
<div>
<label class="newitemlabel">Username:</label> <input type="text" name="lusername" id="lusername" autocomplete=""><br><br>
</div>
<div>
<label class="newitemlabel">Password:</label> <input type="password" name="lpassword" id="lpassword" autocomplete=""><br><br>
</div>
<div>
<label class="newitemlabel">Description:</label> <input type="text" name="ldescription" id="ldescription" autocomplete=""><br><br>
</div>
<div>
<label class="newitemlabel">Email:</label> <input type="text" name="lemail" id="lemail" autocomplete=""><br><br>
</div>
<div style="text-align: center; width: 100%">
<input type="button" name="btnnew" id="btnnew" value="CREATE NEW  <?php echo $_REQUEST["btntext"]; ?>">
</div>
</form>
</body>
</html>