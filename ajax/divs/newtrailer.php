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
			trnumberplate: {
				required: true,
				numberplateTZ: true,
			},
			trchassisnumber: "required",
			trmake: "required",
			tryear: {
				required: true,
				min: 1950,
				max: 2100,
			},
			traxles: {
				required: true,
				min: 2,
				max: 5,
			},
			trroadlicense: {
				required: true,
				dateITA: true
			},
		},
	});

	$("#trnumberplate").change(function () {
		$(this).val($(this).val().replace(/ /g ,"").replace(/-/g,""));
	})

	$("#btnnew").click(function () {
		if ($("#newfrm").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newtrailer.php",
				type: "POST",
				data: {
					trnumberplate: $("#trnumberplate").val(),
					trchassisnumber: $("#trchassisnumber").val(),
					trmake: $("#trmake").val(),
					tryear: $("#tryear").val(),
					traxles: $("#traxles").val(),
					trroadlicense: $("#trroadlicense").val(),
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						$.alert('NEW TRAILER CREATED: ID ' + data);
						$("#newitem").fadeOut();
						$("#workspace").load("trailers.php");
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
	})

	$(function() {
		$( ".dp" ).datepicker();
	});
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<form name="newfrm" id="newfrm">
<div>
<label class="newitemlabel">Number Plate:</label> <input type="text" name="trnumberplate" id="trnumberplate" autocomplete=""><br><br>
</div>
<div>
<label class="newitemlabel">Chassis Number:</label> <input type="text" name="trchassisnumber" id="trchassisnumber"><br><br>
</div>
<div>
<label class="newitemlabel">Trailer Make:</label> <input type="text" name="trmake" id="trmake"><br><br>
</div>
<div>
<label class="newitemlabel">Year Bought:</label> <input type="text" name="tryear" id="tryear"><br><br>
</div>
<div>
<label class="newitemlabel">Number of Axles:</label> <input type="text" name="traxles" id="traxles"><br><br>
</div>
<div>
<label class="newitemlabel">Road License Expiry:</label> <input type="text" class="dp" name="trroadlicense" id="trroadlicense"><br><br>
</div>
<div style="text-align: center; width: 100%">
<input type="button" name="btnnew" id="btnnew" value="CREATE NEW <?php echo $_REQUEST['btntext']; ?>">
</div>
</form>
</body>
</html>