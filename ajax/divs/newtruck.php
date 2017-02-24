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
		$( ".dp" ).datepicker();
	});

	$("#newfrm").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		},
		rules: {
			tnumberplate: {
				required: true,
				numberplateTZ: true,
			},
			tenginenumber: "required",
			tchassisnumber: "required",
			tmake: "required",
			tyear: {
				required: true,
				min: 1950,
				max: 2100,
			},
			troadlicense: {
				required: true,
				dateITA: true,
			},
		},
	});

	$("#tnumberplate").change(function () {
		$(this).val($(this).val().replace(/ /g ,"").replace(/-/g,""));
	})

	$("#btnnew").click(function () {
		if ($("#newfrm").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newtruck.php",
				type: "POST",
				data: {
					tnumberplate: $("#tnumberplate").val().replace(" ",""),
					tenginenumber: $("#tenginenumber").val(),
					tchassisnumber: $("#tchassisnumber").val(),
					ttrailer_id: $("#ttrailer_id").val(),
					tmake: $("#tmake").val(),
					tyear: $("#tyear").val(),
					troadlicense: $("#troadlicense").val(),
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						$.alert('NEW TRUCK CREATED: ID ' + data);
						$("#newitem").fadeOut();
						$("#workspace").load("trucks.php");
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

	acomplete("#ttrailer", "ajax/autocompletes/trailers.php",true,false,true,"trailers","trnumberplate","trid");

	$("#ttrailer_id").change(function (e) {
		//$.alert($(this).val())
		$.ajax({
			url: "ajax/check_trailer_attached.php",
			type: "POST",
			data: {
				ttrailer_id: $(this).val(),
				token: '<?php echo $_SESSION['atoken']; ?>',
			},
			success: function (data) {
				if (data != "") {
					var t = data.split("§§§");
					/*if (!confirm('THIS TRAILER IS ALREADY ATTACHED TO ' + t[1] + '.\n\nATTACH IT TO THIS TRUCK?')) {
						$(this).val("");
						$("#ttrailer").val("");
						e.preventDefault();
					}*/
					$.confirm('THIS TRAILER IS ALREADY ATTACHED TO ' + t[1] + '.\n\nATTACH IT TO THIS TRUCK?', function (answer) {
						if (!answer) {
							$(this).val("");
							$("#ttrailer").val("");
							$("#ttrailer").focus();
							e.preventDefault();
						} else {
							// Ok focus on next element
							$("#tenginenumber").focus();
						}
					})
				}
			}
		})
	});

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
<label class="newitemlabel">Number Plate:</label> <input type="text" name="tnumberplate" id="tnumberplate" autocomplete=""><br><br>
</div>
<div>
<label class="newitemlabel">Trailer:</label> <input type="text" name="ttrailer" id="ttrailer"><input type="hidden" name="ttrailer_id" id="ttrailer_id"><br><br>
</div>
<div>
<label class="newitemlabel">Engine Number:</label> <input type="text" name="tenginenumber" id="tenginenumber"><br><br>
</div>
<div>
<label class="newitemlabel">Chassis Number:</label> <input type="text" name="tchassisnumber" id="tchassisnumber"><br><br>
</div>
<div>
<label class="newitemlabel">Truck Make:</label> <input type="text" name="tmake" id="tmake"><br><br>
</div>
<div>
<label class="newitemlabel">Year Bought:</label> <input type="text" name="tyear" id="tyear"><br><br>
</div>
<div>
<label class="newitemlabel">Road License:</label> <input type="text" class="dp" name="troadlicense" id="troadlicense"><br><br>
</div>
<div style="text-align: center; width: 100%">
<input type="button" name="btnnew" id="btnnew" value="CREATE NEW <?php echo $_REQUEST['btntext']; ?>">
</div>
</form>
