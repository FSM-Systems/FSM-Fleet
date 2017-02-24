<?php
include "inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	$("#new").click(function () {
		$("#newitem").load("ajax/divs/newlocation.php", {btntext: "LOCATION"}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

	$("#newloctype").click(function () {
		$("#newitem").load("ajax/divs/newlocationtype.php", {btntext: "LOCATION TYPE"}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

		// Validation for elements
	$("#frmlocation").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		}
	})

	$('[name*="locwarningdays"], [name*="loctriplegtime"]').each(function() {
	    $(this).rules('add', {
	        required: true,
	        number: true,
	    });
	});

	// Autocomplete location types
	acomplete(".loctype",  "ajax/autocompletes/location_types.php",true,false,false);

})
</script>
</head>
<body>
<div style="float: right"><button id="new"><img class="icon" src="icons/location.png" alt=""> Create a new Location</button> <button id="newloctype"><img class="icon" src="icons/tripconfig.png" alt=""> Create a new Location Type</button></div>
<br><br>
<div class="topline">
Current locations and location types registered on the System:
<br>
<table style="min-width: 1152px;">
	<tr>
		<td style="width: 60%; vertical-align: top;">
			<div>
				<form name="frmlocation" id="frmlocation">
					<table class="tbllist" style="width: 100%" cellpadding=2 cellspacing=0>
					<tr>
						<th class="hidden">ID</th>
						<th>Location</th>
						<th>Location Type</th>
						<th>Warn After</th>
						<th>Trip Leg Days</th>
						<th>Distance</th>
						<th></th>
					</tr>
					<?php
					// Set an offset for the pager
					$numresults = 20;
					if(isset($_REQUEST['pager'])) {
						$offset = ' offset ' . $_REQUEST['pager'] * $numresults;
					} else {
						$offset = ' offset 0 ';
					}

					// Count total rows for pager
					$respager = pg_query($con, "select count(*) from locations left join location_types on loctype=ltid where locations.company_id=" . $_SESSION['company']);
					$resultcount = ceil(pg_fetch_result($respager, 0, 0) / $numresults);

					$res = pg_prepare($con, "locations", "select * from locations left join location_types on loctype=ltid where locations.company_id=$1 order by locdescription limit " . $numresults . $offset);
					$res = pg_execute($con, "locations", array($_SESSION["company"]));
					if(pg_num_rows($res) > 0) {
						while($row = pg_fetch_assoc($res)) {
							echo "
								<tr class='tbl'>
										<td class='hidden'>" . $row["locid"] . "</td>
										<td>" . uinput("locdescription", $row["locid"], $row["locdescription"], "locations", "locdescription", "locid", $row["locid"], false,true,180,null,false,false,false,true,false) . "</td>
										<td>

											" . uinput("lttype", $row["locid"], $row["lttype"], "locations", "loctype", "locid", $row["locid"], false,true,180,"loctype",false,true,false,true,false) . "
										</td>
										<td>" . uinput("locwarningdays", $row["locid"], $row["locwarningdays"], "locations", "locwarningdays", "locid", $row["locid"], true,true,40,"centered",false,false,false,true,false) . " days</td>
										<td>" . uinput("loctriplegtime", $row["locid"], $row["loctriplegtime"], "locations", "loctriplegtime", "locid", $row["locid"], true,true,40,"centered",false,false,false,true,false) . " days</td>
										<td>" . uinput("locdistance", $row["locid"], $row["locdistance"], "locations", "locdistance", "locid", $row["locid"], true,true,40,"centered",false,false,false,true,false) . " km</td>
										<td class='delbtn'>" . delbtn("locations", "locid", $row["locid"], "locations.php", null, "#workspace")  . "</td>
								</tr>
							";
						}
					} else {
						echo "<tr><td colspan='100'>No locations defined yet.</td></tr>";
					}
					?>
					</table>
				<form>
				<br>
				<?php
				if(pg_fetch_result($respager, 0, 0) > $numresults) {
					for($p = 1; $p <= $resultcount; $p++) {
						if(isset($_REQUEST['pager']) && $_REQUEST['pager'] == $p - 1) {
							$pgstyle = 'style="background-color: black; color: white; border: 1px solid black;" disabled="true"';
						} else {
							if(!isset($_REQUEST['pager']) && $p == 1) {
								$pgstyle = 'style="background-color: black; color: white; border: 1px solid black;" disabled="true"';
							} else {
								$pgstyle = '';
							}
						}
						echo '<button ' . $pgstyle . ' class="nextpage" id="' . ($p - 1) . '" type="button">' . $p . '</button>&nbsp;';
					}
				}
				?>
				<script type="text/javascript">
				$(".nextpage").click(function () {
					$("#workspace").load("locations.php?pager=" + $(this).attr("id"));
				})
				</script>
			</div>
		</td>
		<td style="width: 40%; vertical-align: top;">
		<br>
			<div>
				<table class="tbllistnoborder" style="width: 100%" cellpadding=2 cellspacing=0>
				<tr>
					<th class="hidden">ID</th>
					<th>Location Type Description</th>
					<th>Steps</th>
				</tr>
				<?php
				$res = pg_prepare($con, "loctype", "select * from location_types where company_id=$1 order by lttype");
				$res = pg_execute($con, "loctype", array($_SESSION["company"]));
				if(pg_num_rows($res) > 0 ) {
					while($row = pg_fetch_assoc($res)) {
						echo "
							<tr class='tbl'>
									<td class='hidden'>" . $row["ltid"] . "</td>
									<td>" . uinput("lttype", $row["ltid"], $row["lttype"], "location_types", "lttype", "ltid", $row["ltid"], false,true,null,null,false,false,false,true,false) . "</td>";

									echo "<td>";?>
									<form id="frmchk_<?php echo $row['ltid']; ?>" class="frmchk">
									<table class="tbllistnoborder" cellpadding=0 cellspacong=0>
									<tr>
										<td><input type='checkbox' class="chk" onchange="updatecheckbox('location_types', 'ltarrivaldate', this.checked, 'ltid', <?php echo $row['ltid']; ?>)" id="ltarrivaldate_<?php echo $row['ltid']; ?>" <?php echo $row["ltarrivaldate"] == "t" ? " checked" : "" ; ?>></td><td> Arrival Date</td>
										<td><input type='checkbox' class="chk" onchange="updatecheckbox('location_types', 'ltloadingdate', this.checked, 'ltid', <?php echo $row['ltid']; ?>)" id="ltloadingdate_<?php echo $row['ltid']; ?>" <?php echo $row["ltloadingdate"] == "t" ? " checked" : "" ; ?>></td><td> Loading Date</td>
									</tr>
									<tr>
										<td><input type='checkbox' class="chk" onchange="updatecheckbox('location_types', 'ltoffloadingdate', this.checked, 'ltid', <?php echo $row['ltid']; ?>)" id="ltoffloadingdate_<?php echo $row['ltid']; ?>" <?php echo $row["ltoffloadingdate"] == "t" ? " checked" : "" ; ?>></td><td>Offloading Date</td>
										<td><input type='checkbox' class="chk" onchange="updatecheckbox('location_types', 'ltdeparturedate', this.checked, 'ltid', <?php echo $row['ltid']; ?>)" id="ltdeparturedate_<?php echo $row['ltid']; ?>" <?php echo $row["ltdeparturedate"] == "t" ? " checked" : "" ; ?>></td><td> Departure Date</td>
									</tr>
									</table>
									</form>
									<?php
									echo "</td>
									<td class='delbtn'>" . delbtn("location_types", "ltid", $row["ltid"], "locations.php", null, "#workspace")  . "</td>
							</tr>
						";
					}
				} else {
					echo "<tr><td colspan='100'>No locations defined yet.</td></tr>";
				}
				?>
				</table>
			</div>
		</td>
	</tr>
<table>
</div>

</body>
</html>