<?php
include "../../inc/session_test.php";
include "basemessages.php";
?>
<!DOCTYPE html>
<html>
<head>
<script src="inc/container_check.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	$("#newfrm").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		},
		rules: {
			tltruck: "required",
			tltrailer: "required",
			tldriver: "required",
			tltripconfig: "required",
			tlcustomer1: "required",
			tlcustomer2: "required",
			tlcontainer: "required",
			tlequipment: "required",
		},
	});

	$("#btnnew").click(function () {
		if ($("#newfrm").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newtriplog.php",
				type: "POST",
				data: {
					tltruck_id: $("#tltruck_id").val(),
					tltrailer_id: $("#tltrailer_id").val(),
					tldriver_id: $("#tldriver_id").val(),
					tltripconfig_id: $("#tltripconfig_id").val(),
					tlcustomer1_id: $("#tlcustomer1_id").val(),
					tlcustomer2_id: $("#tlcustomer2_id").val(),
					tlcontainer: $("#tlcontainer").val(),
					tlcontainer_ret: $("#tlcontainer_ret").val(),
					tlequipment_id: $("#tlequipment_id").val(),
					tlcargo: $("#tlcargo").val(),
					tlcargo_ret: $("#tlcargo_ret").val(),
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						$.alert('NEW TRIP CREATED: ID ' + data);
						$("#newitem").fadeOut();
						$("#workspace").load("tripinfo.php");
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

	$("#tltruck_id").change(function () {
		// Check default driver
		$.ajax({
			url: "ajax/check_default_trailer.php",
			type: "POST",
			data: {
				tltruck_id: $(this).val(),
				token: '<?php echo $_SESSION['atoken']; ?>',
			},
			success: function (data) {
				if (data != "") {
					var response = data.split("§§§");
					$("#tltrailer_id").val(response[0]);
					$("#tltrailer").val(response[1]);
				}
			}
		});
		// Check if we have already booked a trip for this truck. If so deny!!!
		$.ajax({
			url: "ajax/check_truck_booked.php",
			data: {
				tltruck_id: $("#tltruck_id").val(),
			},
			success: function (data) {
				if (data == "OK") {
					$("#btnnew").prop("disabled", false);
				} else {
					$.alert('THIS TRUCK IS ALREADY PERFORMING A TRIP! YOU CANNOT USE IT!', function () {
						$("#btnnew").prop("disabled", true);
						$("#tltruck").val(null).focus();
						$("#tltrailer").val(null);
					})
				}
			}
		})
	});

	// Check if driver license and passport is expiring and warn
	$("#tldriver_id").change(function () {
		$.ajax({
			url: "ajax/check_driver_expiries.php",
			data: {
				did: $(this).val(),
			},
			success: function (data) {
				if (data.length > 6) {
					var exp = data.split("§§§");
					if (parseInt(exp[2]) > 0) {
						$.alert('THIS DRIVER IS ALREADY PERFORMING A TRIP! YOU CANNOT SELECT HIM!', function () {
							$("#btnnew").prop("disabled", true);
							$("#tldriver").val(null).focus();
						});
					} else {
						if (exp[0] != "" || exp[1] != "") {
							var msg = "PLEASE CHECK THIS DRIVER'S DOCUMENTS! \n";
							if (exp[0] != "") {
								msg += "HIS DRIVING LICENSE IS EXPIRING ON: " + exp[0] +"\n";
							}
							if (exp[1] != "") {
								msg += "HIS PASSPORT IS EXPIRING ON: " + exp[1];
							}
							$.alert(msg);
						} else {
							$("#btnnew").prop("disabled", false);
						}
					}
				}
			}
		})
	})

	//function acomplete(element, source, deleteoption, withvalue, createnew, createtable, createcolumn, retid, refreshvalue, refreshvalueelement, refreshvaluepage, refreshvalueid)
	acomplete("#tltruck","ajax/autocompletes/trucks.php",true,false,true,"trucks","tnumberplate","tid");
	acomplete("#tltrailer","ajax/autocompletes/trailers.php?available=false",true,false,true,"trailers","trnumberplate","trid");
	acomplete("#tldriver","ajax/autocompletes/drivers.php",true,false,false);
	acomplete("#tltripconfig","ajax/autocompletes/tripconfig.php",true,false,false);
	acomplete("#tlcustomer1","ajax/autocompletes/customers.php",false,false,false);
	acomplete("#tlcustomer2","ajax/autocompletes/customers.php",true,false,false);
	acomplete("#tlequipment","ajax/autocompletes/equipment_sets.php",true,false,false);

	$("#tlcontainer, #tlcontainer_ret").change(function (e) {
		// Allow loose and owner containers
		if ($(this).val().toUpperCase().indexOf("LOOSE") == -1 && $(this).val().toUpperCase().indexOf("CARGO") == -1 && $(this).val().toUpperCase().indexOf("XXXX") == -1) {
			if (containercheckdigit($(this).val()) == false) {
				e.preventDefault();
				$.alert("<?php echo WRONGCONTAINER; ?>");
				$(this).addClass("error");
				$(this).val("");
				$(this).focus()
			}
		} else {
			if($(this).val().toUpperCase().indexOf("LOOSE") != -1 && $(this).val().toUpperCase().indexOf("CARGO") != -1) {
				// Spell correct
				$(this).val("LOOSE CARGO");
			}
		}
	})

	<?php
	if (isset($_REQUEST["tid"]) && isset($_REQUEST["truck"])) {
		?>
		$("#tltruck").val("<?php echo $_REQUEST["truck"]; ?>");
		$("#tltruck_id").val("<?php echo $_REQUEST["tid"]; ?>");
		$("#tltruck_id").trigger("change");
		<?php
	}
	?>

	// CLean trailer numberplate before submitting
	$("#tltrailer").change(function () {
		$(this).val($(this).val().replace(/\s/g,"").replace(/-/g,"-"))
	})

		// Check that trailer is not in a trip when assigning it:
	$("#tltrailer_id").change(function () {
		$.ajax({
			url: "ajax/check_trailer_in_use.php",
			type: "POST",
			data: {
				tltrailer_id: $(this).val(),
			},
			success: function (data) {
				var trip = data.split("§§§");
				if (trip[0] == "true") {
					$.alert("THIS TRAILER IS ALREADY BEING USED IN A TRIP\n(TRIP ID: " + trip[1] + "). YOU CANNOT ATTACH IT TO THIS TRUCK!")
					$(this).val("");
					$("#tltrailer").addClass("error");
					$("#tltrailer").val("");
				}
			}
		})
	})

	$("input:text:not(#tlcontainer, #tlcontainer_ret)").each(function () {
		$(this).css("width", "250px")
	});
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<label style="text-decoration: underline; font-weight: bold">Create a new trip log:</label><br>
<form name="newfrm" id="newfrm">
<div style="margin-top: 10px;">
<label class="newitemlabel">Truck Number Plate:</label> <input type="text" name="tltruck" id="tltruck" autocomplete="">
<input type="hidden" name="tltruck_id" id="tltruck_id" autocomplete=""><br><br>
</div>
<div>
<label class="newitemlabel">Trailer Number Plate:</label> <input type="text" name="tltrailer" id="tltrailer" autocomplete="">
<input type="hidden" name="tltrailer_id" id="tltrailer_id" autocomplete=""><br><br>
</div>
<div>
<label class="newitemlabel">Driver Name:</label> <input type="text" name="tldriver" id="tldriver">
<input type="hidden" name="tldriver_id" id="tldriver_id">
<br><br>
</div>
<div>
<label class="newitemlabel">Container(s):</label> <input type="text" name="tlcontainer" id="tlcontainer" style="width: 110px;"> Ret: <input type="text" name="tlcontainer_ret" id="tlcontainer_ret"  style="width: 105px;"><br><br>
</div>
<div>
<label class="newitemlabel">Trip Type:</label> <input type="text" name="tltripconfig" id="tltripconfig">
<input type="hidden" name="tltripconfig_id" id="tltripconfig_id"><br><br>
</div>
<div>
<label class="newitemlabel">Trip Equipment:</label> <input type="text" name="tlequipment" id="tlequipment">
<input type="hidden" name="tlequipment_id" id="tlequipment_id"><br><br>
</div>
<div>
<label class="newitemlabel">Primary Customer:</label> <input type="text" name="tlcustomer1" id="tlcustomer1">
<input type="hidden" name="tlcustomer1_id" id="tlcustomer1_id"><br><br>
</div>
<div>
<label class="newitemlabel">Return Customer:</label> <input type="text" name="tlcustomer2" id="tlcustomer2">
<input type="hidden" name="tlcustomer2_id" id="tlcustomer2_id"><br><br>
</div>
<div style="text-align: left">
<table align=center>
	<tr>
		<td>
			<b>Main Cargo:</b><br>
			<textarea id="tlcargo" name="tlcargo" style="height: 50px;"></textarea>
		</td>
		<td>
			<b>Return Cargo:</b><br>
			<textarea id="tlcargo_ret" name="tlcargo_ret" style="height: 50px;"></textarea>
		</td>
	</tr>
</table>
</div>
<div style="text-align: center; width: 100%">
<input type="button" name="btnnew" id="btnnew" value="CREATE NEW <?php echo $_REQUEST["btntext"]; ?>">
</div>
</form>
</body>
</html>