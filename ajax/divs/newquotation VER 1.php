<?php
include "../../inc/session_test.php";
include "basemessages.php";
include "ajax_security.php";
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
			qclient: "required",
			qdestination: "required",
			qcountry: "required",
			qcargo: "required",
			//qlength: "required",
			//qwidth: "required",
			//qheight: "required",
			//qweight: "required",
			qvalue: {
				required: true,
				number: true,
			},
		},
	});

	// When change customer add div woith customer emails.
	$("#qclient_id").change(function () {
		$.ajax({
			url: "ajax/get_customer_emails.php",
			type: "POST",
			data: {
				cid: $(this).val(),
				token: '<?php echo $_SESSION['atoken']; ?>',
			},
			success: function (data) {
				// Remove old emails
				$(".emails, .br").remove();
				if (data == "0") {
					$("#divqclient").after("<div id='emails' style='color: red' class='emails'>You have not set any emails for this customer.</div><br class='br'>");
					$("#emails").fadeIn().slideDown();
					$("#save").val("true");
					$("#btnnew").val("SAVE QUOTATION");
				} else {
					var count = 1; // Dynamic br adding
					var ret = data.split("+++");
					var emaillist = "<div id='emails' style='display: none; text-align: left' class='emails'><label class=\"newitemlabel\">Emails:</label><table align='right'> ";
					for (var x = 0; x < ret.length; x++) {
						count++;
						emaillist += "<tr><td><input type='checkbox' class='email_list' name='email_list[]' id='" + ret[x] + "' value='" + ret[x] + "'> <label for='" + ret[x] + "'>" + ret[x] + "</label></td><tr>";
					}
					emaillist += "</table></div>";
					for (x= 0; x <= count; x++) {
						emaillist += "<br class='br'>";
					}
					emaillist += "<br>";
					$("#divqclient").after(emaillist);
					$("#emails").fadeIn().slideDown();
					$("#btnnew").val("CREATE NEW <?php echo $_REQUEST["btntext"]; ?>");
					$("#save").val("false");
				}
			}
		})
	});

	// Enable send button once emails have been checked
	$(document).on("change", ".email_list", function () {
		if ($(".email_list:checked").length != 0) {
			$("#btnnew").prop("disabled", false);
		} else {
			$("#btnnew").prop("disabled", true);
		}
	})

	$("#btnnew").click(function () {
		$("#btnnew").prop("disabled", true);
		if ($(".email_list:checked").length==0) {
			$.alert('PLEASE SELECT AT  LEAST ONE EMAIL ADDRESS!');
		} else {
			if ($("#newfrm").valid() == true) {
				var form_data = $(this).closest('form').serialize();
	    		form_data['ajax'] = 1;
				$.ajax({
					url: "ajax/inserts/newquotation.php",
					type: "POST",
					data: form_data,
					success: function (data) {
						if (data.indexOf("MAILOK") > -1) {
							$.alert('NEW QUOTATION CREATED AND SENT BY EMAIL: ID ' + data);
							$("#newitem").fadeOut();
							$("#workspace").load("quotations.php");
						} else {
							switch(data) {
								case "NOEMAIL":
									$.alert("YOUR EMAIL ADDRESS HAS NOT BEEN SET SO THE QUOTATION HAS NOT BEEN DELIVERED TO THE CUSTOMER. PLEASE SET IT UP IN USER CONFIGURATION AND RESEND THIS QUOTATION.")
									$("#btnnew").prop("disabled", false);
									break;
								default:
									$.alert(data);
									$("#btnnew").prop("disabled", false);
							}
						}
					},
					error: function (data) {
						$.alert('<?php echo AJAXERROR; ?>');
						$("#btnnew").prop("disabled", false);
					},
				})
			} else {
				$.alert('<?php echo CHECKFORM; ?>');
				$("#btnnew").prop("disabled", false);
			}
		}
	});

	acomplete("#qclient","ajax/autocompletes/customers.php");
	acomplete("#qcountry","ajax/autocompletes/countries.php",true, false, true, "countries", "cncountry", "cnid");
	acomplete("#qpermits","ajax/autocompletes/quotation_permits.php",true, false, true, "quotation_permits", "qpdescription", "qpid");

	$("#newfrm input:text:not(#qlength, #qwidth, #qheight, #qweight), textarea").each(function () {
		$(this).css("width", "250px")
	});
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<label style="text-decoration: underline; font-weight: bold">Create a new quotation:</label><br>
<form name="newfrm" id="newfrm">
<div style="margin-top: 10px;" id="divqclient">
<label class="newitemlabel">Customer:</label> <input type="text" name="qclient" id="qclient" autocomplete="">
<input type="hidden" name="qclient_id" id="qclient_id" autocomplete=""><br><br>
</div>
<div>
<label class="newitemlabel">Destination:</label> <input type="text" name="qdestination" id="qdestination">
</div>
<div style="margin-top: 10px;">
<label class="newitemlabel">Country:</label> <input type="text" name="qcountry" id="qcountry" autocomplete="">
<input type="hidden" name="qcountry_id" id="qcountry_id" autocomplete=""><br><br>
</div>
<div>
<label class="newitemlabel">Cargo:</label> <input type="text" name="qcargo" id="qcargo"><br><br>
</div>
<div>
<label class="newitemlabel">Dimensions/Weight:</label>
	<div style="width: 250px; float: right; display: inline-block; vertical-align: middle">
		L: <input type="text" name="qlength" id="qlength" style="width: 30px;">
		W: <input type="text" name="qwidth" id="qwidth" style="width: 30px;">
		H: <input type="text" name="qheight" id="qheight" style="width: 30px;">
		Weight: <input type="text" name="qweight" id="qweight" style="width: 45px;">
	</div><br><br>
</div>
<div>
<label class="newitemlabel">Freight Quote: $</label> <input type="text" name="qvalue" id="qvalue">
</div>
<div style="margin-top: 10px;">
<label class="newitemlabel">Permits/Escort:</label> <input type="text" name="qpermits" id="qpermits" autocomplete="">
<input type="hidden" name="qpermits_id" id="qpermits_id" autocomplete=""><br>
</div>
<div style="margin-top: 10px;">
<label class="newitemlabel">Notes for Customer:</label>
<textarea name="qnotes" id="qnotes" style="height: 60px;"></textarea><br><br>
</div>
<div style="margin-top: 10px;">
<label class="newitemlabel">Notes (Internal):</label>
<textarea name="qnotesinternal" id="qnotesinternal" style="height: 60px;"></textarea><br><br>
</div>
<div style="text-align: center; width: 100%">
<input type="button" name="btnnew" id="btnnew" value="CREATE NEW <?php echo $_REQUEST["btntext"]; ?>">
</div>
<!-- Hidden input for saying if we save or send email -->
<input type="hidden" id="save" name="save" value="false">
<input type="hidden" id="token" name="token" value="<?php echo $_SESSION["atoken"]; ?>">
</form>
</body>
</html>