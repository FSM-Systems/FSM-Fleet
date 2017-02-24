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
	$(".dp").datepicker();

	$("#btnfilter").click(function () {
		var formgo = true;
		// If we select a from date then we must add a to date and viceversa
		if ($("#fromdate").val() || $("#todate").val()) {
			if ($("#fromdate").val() && $("#todate").val()) {
				formgo = true;
			} else {
				formgo = false
			}
		}

		if (formgo == true) {
			$("#newitem").fadeOut();
			$("#workspace").load("triphistory.php", {
				filter: true,
				tnumberplate: $("#tnumberplate_id").val(),
				trnumberplate: $("#trnumberplate_id").val(),
				fromdate: $("#fromdate").val(),
				todate: $("#todate").val(),
				customer: $("#customer_id").val(),
				driver: $("#driver_id").val(),
				container: $("#container").val().replace(" ","").replace("-","").toUpperCase(),
			});
		} else {
			alert('YOU HAVE SELECTED ONLY ONE DATE OPTION. PLEASE SELECT ALSO THE SECOND ONE.');
		}
	});

	$("#newfrm input[type=text]").each(function () {
		$(this).css({"width" : "200px"});
	})

	acomplete("#tnumberplate","ajax/autocompletes/trucks.php");
	acomplete("#trnumberplate","ajax/autocompletes/trailers.php");
	acomplete("#customer","ajax/autocompletes/customers.php");
	acomplete("#driver","ajax/autocompletes/drivers.php");
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<form id="newfrm" name="newfrm">
<div>
<center>Select items to filter:</center><br>
</div>
<div>
<label class="newitemlabel">Truck:</label>
<input type="text" name="tnumberplate" id="tnumberplate"><br><br>
<input type="hidden" name="tnumberplate_id" id="tnumberplate_id">
</div>
<div>
<label class="newitemlabel">Trailer:</label>
<input type="text" name="trnumberplate" id="trnumberplate"><br><br>
<input type="hidden" name="trnumberplate_id" id="trnumberplate_id">
</div>
<div>
<label class="newitemlabel">From Date:</label> <input type="text" class="dp" name="fromdate" id="fromdate"><br><br>
</div>
<div>
<label class="newitemlabel">To Date:</label> <input type="text" class="dp" name="todate" id="todate"><br><br>
</div>
<div>
<label class="newitemlabel">Customer:</label>
<input type="text" name="customer" id="customer"><br><br>
<input type="hidden" name="customer_id" id="customer_id">
</div>
<div>
<label class="newitemlabel">Container Number:</label> <input type="text" name="container" id="container"><br><br>
</div>
<div>
<label class="newitemlabel">Driver:</label>
<input type="text" name="driver" id="driver"><br><br>
<input type="hidden" name="driver_id" id="driver_id">
</div>
<div style="text-align: center; width: 100%">
<input type="button" name="btnfilter" id="btnfilter" value="APPLY SELECTED FILTERS">
</div>
</form>
</body>
</html>