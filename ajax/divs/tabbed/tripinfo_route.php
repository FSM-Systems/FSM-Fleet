<?php
include "../../../inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	// Draggable rows
	$(".sort tbody").sortable({
		items: "> tr",
		appendTo: "parent",
		helper: "clone",
		stop: function (event, ui) {
			tablerows();
		}
	}).disableSelection();

	$("#apply").click(function () {
		$.confirm('ARE YOU SURE YOU WANT TO APPLY THIS CONFIGURATION?', function (answer) {
			if (answer) {
				var str = '';
				var querystr = {};
				// Add trip id to query string
				querystr["tripid"] = <?php echo $_REQUEST["id"]; ?>;
				querystr["token"] = "<?php echo $_SESSION["atoken"]; ?>";
				$(".hiddeninputs").each(function () {
					querystr[$(this).attr("id")] = $(this).val();
				})
				// apply changes to DB
				$.ajax({
					url: "ajax/update_trip_log_det.php",
					type: "POST",
					data: querystr,
					success: function (data) {
						//$("#testbench").html(data)
						if (data !== "") {
							$.alert("ERROR UPDATING TRIP LOG! PLEASE CHECK AND TRY AGAIN!" + data);
						} else {
							done = true;
							$("#apply").prop("disabled", true)
							$.alert("TRIP LOG UPDATED SUCCESSFULLY!")
							// Set status to not changed
							$("#route").data('changed', false);
						}
					},
					error: function (xhr, status, err) {
						$.alert("ERROR UPDATING TRIP LOG! PLEASE CHECK AND TRY AGAIN!" + err);
					}
				})
			}
		});
	});

	// Custom autocomplete as we are not working with DB here
	$("#addloc").addClass("dropdown");
	$("#addloc").autocomplete({
		source: 	"ajax/autocompletes/trip_steps.php",
		minLength: 0,
		select: function (event, ui) {
			// Add new row to table and apply row numbering
			// For trip_log_det id add bogus xxx so we know it is a new one and force the creation of actions in sql
			// Append also a random number to make the xxx id unique otherwise it will appear only once in the query string
			var rand = Math.floor((Math.random() * 100) + 1);
			$("#route tr:last").before("<tr><td></td><td><div name='loc_" + ui.item.id + "' class='currentlocations'><label style='color: red; font-weight: bold'>" + ui.item.label + " (" + ui.item.id + ") </label><input type='hidden' class='hiddeninputs' id='tldid_loc_" + ui.item.id + "' value='" + ui.item.id + "'><input type='hidden' class='hiddeninputs' id='tldid_action_xxx_" + rand + "'></div></td><td class='smallbutton' style='vertical-align: middle'><button class='smallbutton' id='delete_" + rand +"' onclick='delrow(this)'><img src='icons/delete.png' class='smallbutton'></button></td></tr>");
			// Apply numbering
			tablerows();
			$(this).val("").focus();
			//alert($("#numbered tbody tr").not(":last").length)
			if ($("#route").data('changed') == true) {
				$("#apply").prop("disabled", false);
			}
			return false;
		}
	}).dblclick(function () {
		$(this).autocomplete("search");
	}).click(function () {
		$(this).autocomplete("search");
	});


	// Dropdown
	acomplete(".dd", "ajax/autocompletes/trip_steps.php", false, true);
	// Table rows
	tablerows();

	// Save form status for later check. Check if we are adding rows and alter state of #route
	$(document).bind('DOMNodeInserted', function(e) {
		var element = e.target;
		if($(element).is("tr")) {
			$("#route").data('changed', true);
		}
	});
})

// Funtion as elements added are not considered after page is loaded
function delrow(element) {
	$.confirm('DELETE THIS ROUTE ITEM?', function (answer) {
		if (answer) {
			$("#route").data('changed', true);
			// Delete row and apply numbers
			$(element).closest("tr").remove();
			tablerows();
			// Apply button..
			if ($("#route").data('changed') == true) {
				$("#apply").prop("disabled", false);
			} else {
				$("#apply").prop("disabled", true);
			}
		}
	})
}
</script>
<?php
$res = pg_query($con, "
select locid, tlid, locdescription, case when locdistance is not null then locdistance || ' km' else null end as locdistance, loctype, lttype, tldistance
 from
trip_log left join trip_log_det on tlid=tldtripid
left join locations on tldlocation=locid
left join location_types on loctype=ltid
where tlid=" . $_REQUEST["id"] . " group by locid, tlid, locdescription,lttype order by min(tldid)
");
?>
<table class="tbllistnoborder bottomborder sort numbered" id="route" cellpadding=2 cellspacing=0>
<thead>
	<tr>
		<th style="text-align: center" colspan="3">
			TRIP CONFIGURATION (Drag and Drop to sort)
		</th>
	</tr>
</thead>
	<tbody>
		<?php
			// Location
			// Action
			// Actiondate
			// Working only table. Commits are made at the end when user confirms
			while($loc = pg_fetch_assoc($res)) {
				// For every location fetch associated action in trip_log_det for this trip and location
				echo "
				<tr>
					<td></td>
					<td>";
					$res2 = pg_query($con, "select * from trip_log_det
					left join locations on tldlocation=locid
					left join location_types on loctype=ltid
					where tldtripid=" . $loc["tlid"] . " and locid=" . $loc["locid"]);
					$arrsteps = pg_fetch_all($res2);
					$currrow = 0;
					// Display location details
					echo "<div id='loc_" . $loc["locid"] . "' name='loc_" . $loc["locid"] . "' class='currentlocations'><label style='font-weight: bold'>" . $loc["locdescription"] . " (" . $loc["lttype"] . ")" . "</label> ";
					echo "<input type='hidden' class='hiddeninputs' id='tldid_loc_" . $loc["locid"] . "' value='" . $loc["locid"] . "'>";
					echo "<div  style='display: inline-block' >( ";
					$strsteps = "";
					foreach($arrsteps as $step) {
						$strsteps .= "<label name='step_" . $step["tldid"] . "' style='width: 300px'>" . $step["tldaction"] . "</label>,&nbsp;&nbsp;";
						echo "<input type='hidden' class='hiddeninputs' id='tldid_action_" . $step["tldid"] . "' value='" . $step["tldaction"] . "'>";
					}
					echo substr($strsteps,0,strlen($strsteps) - 13) ;
					echo " )</div>";
					echo " " . $loc["locdistance"] . "</div>";
					echo "
					</td>
					<td class='smallbutton' style='vertical-align: middle'>
						<button class='' id='delete_" . $step["tldid"] ."' onclick='delrow(this)'><img src='icons/delete.png' class='smallbutton'></button>
					</td>
				</tr>";
			}
		?>
		<tr>
			<td></td>
			<td>
				<input type='text' id='addloc' style='min-width: 300px; width: 100%'>
			</td>
			<td class='delbtn'></td>
		</tr>
	</tbody>
</table>

<table class="tbllistnoborder">
		<tr>
		<td>
			Total Trip Kilometers:
			<?php
			echo uinput("tldistance", pg_fetch_result($res, 0, 1), pg_fetch_result($res, 0, 6), "trip_log", "tldistance", "tlid", pg_fetch_result($res, 0, 1), true,false,80,"center",null,false,false,true,false);
			?>
		</td>
	</tr>
	<tr>
		<td colspan="4" class="centered"><button id="apply" disabled="true">APPLY NEW CONFIGURATION TO TRIP</button></td>
	</tr>
</table>
</body>
</html>