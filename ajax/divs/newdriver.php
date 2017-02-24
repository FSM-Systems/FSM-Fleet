<?php
include "../../inc/session_test.php";
include "basemessages.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	$(function() {
		$( "#dlicenseexp, #dpassportexp" ).datepicker();
	});

	$("#newfrm").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		},
		rules: {
			dname: "required",
			dlicenceno: {
				required: true,
				maxlength: 10,
				minlength: 10,
			},
			dlicenseexp: {
				required: true,
				dateITA: true,
			},
			dpassportno: "required",
			dpassportexp: {
				required: true,
				dateITA: true,
			},
		},
	});

	$("#btnnew").click(function () {
		if ($("#newfrm").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newdriver.php",
				type: "POST",
				data: {
					dname: $("#dname").val(),
					dlicenceno: $("#dlicenceno").val(),
					dlicenseexp: $("#dlicenseexp").val(),
					dpassportno: $("#dpassportno").val(),
					dpassportexp: $("#dpassportexp").val(),
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						$.alert('NEW DRIVER CREATED: ID ' + data);
						$("#newitem").fadeOut();
						$("#workspace").load("drivers.php");
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

	$("#newfrm input:text").each(function () {
		$(this).css("width", "200px");
	});
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<form name="newfrm" id="newfrm">
<div>
<label class="newitemlabel">Driver Name:</label> <input type="text" name="dname" id="dname" autocomplete=""><br><br>
</div>
<div>
<label class="newitemlabel">License Number:</label> <input type="text" name="dlicenceno" id="dlicenceno"><br><br>
</div>
<div>
<label class="newitemlabel">License Expiry:</label> <input type="text" name="dlicenseexp" id="dlicenseexp"><br><br>
</div>
<div>
<label class="newitemlabel">Passport Number:</label> <input type="text" name="dpassportno" id="dpassportno"><br><br>
</div>
<div>
<label class="newitemlabel">Passport Expiry:</label> <input type="text" name="dpassportexp" id="dpassportexp"><br><br>
</div>
<div style="text-align: center; width: 100%">
<input type="button" name="btnnew" id="btnnew" value="CREATE NEW <?php echo $_REQUEST['btntext']; ?>">
</div>
</form>
</body>
</html>