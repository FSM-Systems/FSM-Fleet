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
			tcdescription: "required",
		},
	});

	$("#btnnew").click(function () {
		// Check form valid and that we have at least 2 dest. Counting tr so 1 is hidden as header.
		if ($("#newfrm").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newtripconfig.php",
				type: "POST",
				data: {
					tcdescription: $("#tcdescription").val(),
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						$.alert('NEW TRIP CONFIGURATION	 CREATED: ID ' + data);
						$("#newitem").fadeOut();
						$("#workspace").load("tripconfig.php");
					} else {
						$.alert('<?php echo QUERYERROR; ?>' + data);
					}
				},
				error: function (data) {
					$.alert('<?php echo AJAXERROR; ?>');
				},
			})
		}
	});
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<form name="newfrm" id="newfrm">
<div>
<label class="newitemlabel">Trip Name:</label> <input type="text" name="tcdescription" id="tcdescription" autocomplete="" style="width: 300px"><br><br>
</div>

<!--<div>
<label class="newitemlabel">Please add all trip steps:</label><br>
<input type="text" name="tripstep" id="tripstep" style="width: 300px" autocomplete="">
<input type="hidden" name="tripstep_id" id="tripstep_id" autocomplete="">
<input type="button" id="addstep" name="addstep" value="ADD"><br><br>
<table id="tbltripstep" class="tbltripstep">
	<tr>
		<td>Trip Step Location</td>
		<td></td>
	</tr>
</table>
</div>-->

<div style="text-align: center; width: 100%">
<input type="button" name="btnnew" id="btnnew" value="CREATE NEW <?php echo $_REQUEST['btntext']; ?>">
</div>
</form>
</body>
</html>