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
	$(".dp").datepicker({
		maxDate: '0',
		onSelect: function (date, obj) {
			// First check that the current date is equal or greater that previous in line! Find inputs with class dp as they are the ones we are interested in!
			var prevdate = $(this).closest('tr').prevAll(":has(input):first").find('input.dp').val();
			if (typeof prevdate !== "undefined") {
				// We after 1st date insertion so lets check.
				arrprevdate = prevdate.split("/");
				var objprevdate = new Date(arrprevdate[2], arrprevdate[1] - 1, arrprevdate[0]);
				arrcurrdate = date.split("/");
				var objcurrdate = new Date(arrcurrdate[2], arrcurrdate[1] - 1, arrcurrdate[0]);
				if (objcurrdate.getTime() >= objprevdate.getTime()) {
					var godate = true;
				} else {
					$.alert('THE DATE YOU HAVE INSERTED CANNOT BE BEFORE THE PREVIOUS STEP DATE!');
					var godate = false;
				}
			} else {
				var godate = true;
			}

			if (godate == true) {
				//$.alert(prevdate)
	 			// Activate next  textbox and focus on it for further editing
	 			var el = $(this).closest('tr').nextAll(":has(input):first").find('input').prop("disabled", false).removeClass("disabled");
	 			// Trigger change as when using this event it overrides element onchange and we have to force the event to fire
				$(this).trigger("change");
				if ($(this).closest("tr").is(":last-child")) {
					// Activate close button on last insert of date
					$("#btnclose").prop("disabled", false)
				}
			} else {
				$(this).val("");
				$(this).focus();
			}
		}
	});

	$(".dp").change(function () {
		if ($(this).val() == "") {
			// Remove focus from previously focused element
			var el = $(this).closest('tr').nextAll(":has(input):first").find('input').prop("disabled", true).addClass("disabled");
		}
	});

	// Close trip button (check all dates are present)
	$("#btnclose").click(function () {
		var closeok = true;
		$("#routedates").find("input").each(function () {
			if ($(this).val() == "") {
				closeok = false;
				return;
			}
		})

		if (closeok == false) {
			$.alert('YOU CANNOT CLOSE THIS TRIP AS THE DATES ARE NOT SET!');
		} else {
			$.confirm("ARE YOU SURE YOU WANT TO CLOSE THIS TRIP?", function (answer) {
				if (answer) {
					$.ajax({
						url: "ajax/close_trip.php",
						data: {
							tripid: $("#tripid").val(),
							token: '<?php echo $_SESSION['atoken']; ?>',
						},
						success: function (data) {
							if ($.isNumeric(data)) {
								$.alert('TRIP CLOSED: ID ' + data);
								$("#newitem").fadeOut();
								$("#workspace").load("tripinfo.php");
							} else {
								$.alert('<?php echo QUERYERROR; ?>' + data);
							}
						},
						error: function (data) {
							$.alert('<?php echo AJAXERROR; ?>');
						}
					})
				}
			})
		}
	});
});
</script>
<script src="inc/container_check.js"></script>
</head>
<body>
<table class="tbltripstep" id="routedates" style="width: 400px">
<?php
$res = pg_query($con, "
select
tldid, tldaction, to_char(tldactiondate, 'dd/mm/YYYY') as tldactiondate, tldactiondate - lag(tldactiondate, 1) over (order by tldactiondate) daycount, locdescription,lttype
 from
trip_log left join trip_log_det on tlid=tldtripid
left join locations on tldlocation=locid
left join location_types on loctype=ltid
where tlid=" . $_REQUEST["id"] . " order by tldid, tldorder ;
");
?>

<?php
$prev = "";
$prevdate = "";
$disabled= "";
$disablebtn = "";
$class = "";
$counter = 1; // Do not disbale first textbox if values are empty.
// If values there then do not disbale!
while($row = pg_fetch_assoc($res)) {
	// Check if we have an empty date, if so disabe button as trip has not been completed
	if($row["tldactiondate"] == "") {
		$disablebtn = " disabled=\"true\"";
	} else {
		$disablebtn = "";
	}

	if($row["locdescription"] . $row["lttype"] != $prev ) {
		echo '<tr><td colspan=3 style="border-bottom: 1px solid black">
		<label class="headlabel bold">' . $row["locdescription"] . ' (' . $row['lttype'] . ')' . '</label></td></tr>';
	}

	if($counter == 1) {
		$disabled = "";
	} else {
		// If we are on second or more row check if previous empty or not. If empty disable else enable as we always want 1 input box active at a time.
		if($row["tldactiondate"] == "") {
			if($prevdate != "") {
				$disabled = "";
				$class = "";
			} else {
				$disabled = " disabled='true'";
				$class = " disabled";
			}
		}	else {
			$disabled = "";
		}
	}

	if($_SESSION["tripsteps"] == "f") { // Check if for user we have to disable or not (based on settings)
		$disabled = "";
		$disablebtn = "";
		$class = "";
	}

	echo '<tr><td><label class="newitemlabel">' . $row["tldaction"] . ': </label></td><td>'
	. uinput("tldactiondate", $row["tldid"], $row["tldactiondate"], "trip_log_det", "tldactiondate", "tldid", $row["tldid"], false,false,80,"dp centered " . $class,$disabled,false,true,true,false) .
	'</td>';
	if($row["daycount"] != "") {
		if($row["daycount"] == 1) {
			echo '<td class="bold">Time: ' . $row["daycount"] . ' day</td>';
		} else {
			echo '<td class="bold">Time: ' . $row["daycount"] . ' days</td>';
		}
	} else {
		echo '<td></td>';
	}
	echo '</tr>';
	$prev = $row["locdescription"] . $row["lttype"];
	// Save date for comparison
	$prevdate = $row["tldactiondate"];
	$counter++;
}
?></table><br>
<div style="text-align: center; width: 100%">
<input type="hidden" name="tripid" id="tripid" value="<?php echo $_REQUEST['id']; ?>">
<?php
// Close button only if we are NOT editing
if(!isset($_REQUEST["mod"])) {
?>
<input type="button" <?php echo $disablebtn; ?> name="btnclose" id="btnclose" value="CLOSE THIS TRIP">
<?php
}
?>
</div>
</body>
</html>