<?php
include "../../inc/session_test.php";
include "basemessages.php";
?>
<!DOCTYPE html>
<html>
<head>
<style type="text/css">
	.searchtxt {
		width: 200px;
	}
</style>
<script src="inc/container_check.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	$(".dp").datepicker();

	$("#btnfilter").click(function () {
		$("#newitem").fadeOut();
		$("#workspace").load("quotations.php", {
			filter: true,
			client: $("#client_id").val(),
			destination: $("#destination").val(),
			country: $("#country_id").val(),
			goods: $("#goods").val(),
			permits: $("#permits_id").val(),
		});
	});

	$("#newfrm input[type=text]").each(function () {
		$(this).addClass("searchtxt");
	})

	acomplete("#client","ajax/autocompletes/customers.php");
	acomplete("#country","ajax/autocompletes/countries.php");
	acomplete("#permits","ajax/autocompletes/quotation_permits.php");
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
<label class="newitemlabel">Client:</label>
<input type="text" name="client" id="client"><br><br>
<input type="hidden" name="client_id" id="client_id">
</div>
<div>
<label class="newitemlabel">Destination:</label>
<input type="text" name="destination" id="destination"><br><br>
</div>
<div>
<label class="newitemlabel">Country:</label>
<input type="text" name="country" id="country"><br><br>
<input type="hidden" name="country_id" id="country_id">
</div>
<div>
<label class="newitemlabel">Goods:</label>
<input type="text" name="goods" id="goods"><br><br>
</div>
<div>
<label class="newitemlabel">Permits:</label>
<input type="text" name="permits" id="permits"><br><br>
<input type="hidden" name="permits_id" id="permits_id">
</div>
<div style="text-align: center; width: 100%">
<input type="button" name="btnfilter" id="btnfilter" value="APPLY SELECTED FILTERS">
</div>
</form>
</body>
</html>