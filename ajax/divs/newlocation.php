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
			locdescription: "required",
			loctype: "required",
			loctype_id: "required",
			days: {
				required: true,
				number: true,
			},
		},
	});

	$("#btnnew").click(function () {
		if ($("#newfrm").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newlocation.php",
				type: "POST",
				data: {
					locdescription: $("#locdescription").val(),
					loctype_id: $("#loctype_id").val(),
					days: $("#days").val(),
					tripleg: $("#tripleg").val(),
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						$.alert('NEW LOCATION CREATED: ID ' + data);
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
	})

	acomplete("#loctype","ajax/autocompletes/location_types.php");

	$("#newfrm input").each(function () {
		$(this).css({"width": "200px"});
	});
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<form name="newfrm" id="newfrm">
<div>
<label class="newitemlabel">Description:</label> <input type="text" name="locdescription" id="locdescription" autocomplete=""><br><br>
</div>
<div>
<label class="newitemlabel">Location Type:</label> <input type="text" name="loctype" id="loctype"><input type="hidden" name="loctype_id" id="loctype_id"><br><br>
</div>
<div>
<label class="newitemlabel">Warn After:</label> <input type="text" name="days" id="days"><br><br>
</div>
<div>
<label class="newitemlabel">Trip Leg:</label> <input type="text" name="tripleg" id="tripleg"><br><br>
</div>
<div style="text-align: center; width: 100%">
<input type="button" name="btnnew" id="btnnew" value="CREATE NEW <?php echo $_REQUEST['btntext']; ?>">
</div>
</form>
<br>
<!--<div id="map" class="map" style="width: 600px; height: 400px">

</div>-->
<script type="text/javascript">
/*
var locmap = L.map('map', {zoom: 7});
// create the tile layer with correct attribution
	var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
var osmAttrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';
var osm = new L.TileLayer(osmUrl, {minZoom: 2, maxZoom: 24, attribution: osmAttrib});
locmap.addLayer(osm);
//locmap.setView(new L.LatLng(51.3, 0.7),9);
// To user location
locmap.locate({setView : true});
*/
</script>
</body>
</html>