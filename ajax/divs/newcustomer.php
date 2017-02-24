<?php
include "../../inc/session_test.php";
include "basemessages.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	// Prevent all form submits as we use ajax
	$("#newfrm").submit(function (event) {
		event.preventDefault();
	});

	$("#newfrm").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		},
		rules: {
			cname: "required",
			caddress: "required",
		},
	});

	$("#btnnew").click(function () {
		if ($("#newfrm").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newcustomer.php",
				type: "POST",
				data: {
					cname: $("#cname").val(),
					caddress: $("#caddress").val(),
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						$.alert('NEW CUSTOMER CREATED: ID ' + data);
						$("#newitem").fadeOut();
						$("#workspace").load("customers.php");
					} else {
						$.alert('<?php echo QUERYERROR; ?>' + data);
					}
				},
				error: function (data) {
					$.alert('<?php echo AJAXERROR; ?>');
				},
			})
		} else {
			$.alert('<?php echo CHECKFORM; ?>');
		}
	})

	$("#newfrm input:text, textarea").each(function () {
		$(this).css("width", "200px");
	});
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<form name="newfrm" id="newfrm">
<div>
<label class="newitemlabel">Customer Name:</label> <input type="text" name="cname" id="cname" autocomplete=""><br><br>
</div>
<div>
<label class="newitemlabel">Address:</label> <textarea name="caddress" id="caddress" style="height: 50px;"></textarea><br><br>
</div>
<div style="text-align: center; width: 100%">
<input type="button" name="btnnew" id="btnnew" value="CREATE NEW <?php echo $_REQUEST['btntext']; ?>">
</div>
</form>
</body>
</html>