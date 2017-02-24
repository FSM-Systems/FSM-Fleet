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
			lttype: "required",
			'chkloc[]': {
				required: true,
			}
		},
	});

	// Check also that at least one checkbox is selected
	$("#btnnew").click(function () {
		//if ($("#newfrm").valid() == true && $('#newfrm input:checked').length > 0) {
		if ($("#newfrm").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newlocationtype.php",
				type: "POST",
				data: {
					lttype: $("#lttype").val(),
					depdate: $("#depdate").is(":checked"),
					arrdate: $("#arrdate").is(":checked"),
					loaddate: $("#loaddate").is(":checked"),
					offloaddate: $("#offloaddate").is(":checked"),
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						$.alert('NEW LOCATION  TYPE CREATED: ID ' + data);
						$("#newitem").fadeOut();
						$("#workspace").load("locations.php");
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
	});
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<form name="newfrm" id="newfrm">
<div>
<label class="newitemlabel">Location Type Description:</label> <input type="text" name="lttype" id="lttype" autocomplete=""><br><br>
</div>
<div style="text-align: center; width: 100%;">
	<div style="text-align: left; width: 150px; margin: 0 auto;">
		Steps Associated:<br>
		<input type='checkbox' class="chk" name="chkloc[]" id="arrdate"> <label for="arrdate">Arrival Date</label><br>
		<input type='checkbox' class="chk" name="chkloc[]" id="loaddate"> <label for="loaddate">Loading Date</label><br>
		<input type='checkbox' class="chk" name="chkloc[]" id="offloaddate"> <label for="offloaddate">Offloading Date</label><br>
		<input type='checkbox' class="chk" name="chkloc[]" id="depdate"> <label for="depdate">Departure Date<label><br>
	</div>
</div>
<div style="text-align: center; width: 100%"><br>
<input type="button" name="btnnew" id="btnnew" value="CREATE NEW <?php echo $_REQUEST['btntext']; ?>">
</div>
</form>
</body>
</html>