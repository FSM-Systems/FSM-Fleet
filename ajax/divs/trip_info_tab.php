<?php
include "../../inc/session_test.php";
require_once "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script src="inc/container_check.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	// Main container update
	$("#tlcontainer_<?php echo $_REQUEST["id"]; ?>").change(function (event) {
		if ($(this).val().toUpperCase().indexOf("LOOSE") == -1 && $(this).val().toUpperCase().indexOf("CARGO") == -1 && $(this).val().toUpperCase().indexOf("XXXX") == -1) {
			if (containercheckdigit($(this).val()) === false) {
				$(this).addClass("error").focus();
				$.alert("<?php echo WRONGCONTAINER; ?>");
			} else {
				$(this).removeClass("error");
				updatevalue("trip_log", "tlcontainer",$(this).val().toUpperCase().replace(" ","").replace("-",""), "tlid", <?php echo $_REQUEST["id"]; ?>, false, true, this, false, false);
				$("#workspace").load("tripinfo.php");
			}
		} else {
			// update in DB with LOOSE CARGO
			$(this).removeClass("error");
			if ($(this).val().toUpperCase().indexOf("XXXX") != -1) {
				updatevalue("trip_log", "tlcontainer",$(this).val().toUpperCase().replace(" ","").replace("-",""), "tlid", <?php echo $_REQUEST["id"]; ?>, false, true, this, false,false);
			} else {
				updatevalue("trip_log", "tlcontainer","LOOSE CARGO", "tlid", <?php echo $_REQUEST["id"]; ?>, false, true, this, false,false);
			}
			$("#workspace").load("tripinfo.php");
		}
	});
	// Return Container Update
		$("#tlcontainer_ret_<?php echo $_REQUEST["id"]; ?>").change(function (event) {
		if ($(this).val().toUpperCase().indexOf("LOOSE") == -1 && $(this).val().toUpperCase().indexOf("CARGO") == -1 && $(this).val().toUpperCase().indexOf("XXXX") == -1) {
			if (containercheckdigit($(this).val()) == false) {
				$(this).addClass("error").focus();
				$.alert("<?php echo WRONGCONTAINER; ?>");
			} else {
				$(this).removeClass("error");
				updatevalue("trip_log", "tlcontainer_ret",$(this).val().toUpperCase().replace(" ","").replace("-",""), "tlid", <?php echo $_REQUEST["id"]; ?>, false, true, this, false,false);
				$("#workspace").load("tripinfo.php");
			}
		} else {
			// update in DB with LOOSE CARGO
			$(this).removeClass("error");
			if ($(this).val().toUpperCase().indexOf("XXXX") != -1) {
				updatevalue("trip_log", "tlcontainer_ret",$(this).val().toUpperCase().replace(" ","").replace("-",""), "tlid", <?php echo $_REQUEST["id"]; ?>, false, true, this, false,false);
			} else {
				updatevalue("trip_log", "tlcontainer_ret","LOOSE CARGO", "tlid", <?php echo $_REQUEST["id"]; ?>, false, true, this, false,false);
			}
			$("#workspace").load("tripinfo.php");
		}
	});

	$("#tabs").tabs({
		<?php
		if (isset($_REQUEST["tabopen"])) {
			// Open selected tab
			echo "active: " . $_REQUEST["tabopen"] . ",";
		}
		?>
		beforeActivate: function (event, ui) {
			switch(ui.oldTab.index()) {
				case 1: // expenses tab
					if ($("#expenses").data('changed') == true) {
						event.preventDefault();
						$.confirm('YOU HAVE MADE CHANGES THE EXPENSES. ARE YOU SURE YOU WANT TO LEAVE THE PAGE? ALL CHANGES WILL BE LOST!' , function (answer) {
							if (answer === true) {
								$("#expenses").data('changed', false);
								$("#tabs").tabs("option", "active", ui.newTab.index());
							}
						});
					}
					break;
				case 2: // fuel tab
					if ($("#fuel").data('changed') == true) {
						event.preventDefault();
						$.confirm('YOU HAVE MADE CHANGES THE FUEL TABLE. ARE YOU SURE YOU WANT TO LEAVE THE PAGE? ALL CHANGES WILL BE LOST!' , function (answer) {
							if (answer === true) {
								$("#fuel").data('changed', false);
								$("#tabs").tabs("option", "active", ui.newTab.index());
							}
						});
					}
					break;
				case 4: // trip routes check
					if($("#route").data('changed') == true) {
	  					event.preventDefault();
						$.confirm('YOU HAVE MADE CHANGES TO THIS ROUTE. ARE YOU SURE YOU WANT TO LEAVE THE PAGE? ALL CHANGES WILL BE LOST!' , function (answer) {
							//alert(answer)
							if (answer === true) {
								$("#route").data('changed', false);
								$("#tabs").tabs("option", "active", ui.newTab.index());
							}
						});
	  				}
					break;
			}
		}
	});

	acomplete("#tldriver","ajax/autocompletes/drivers.php", true, false, false);
	acomplete("#tltripconfig","ajax/autocompletes/tripconfig.php", true, false, false);
	acomplete(".customer","ajax/autocompletes/customers.php", true, false, false);

	$(".closebutton").click(function () {
		// Just alert
		if ($("#expenses").data('changed') == true || $("#fuel").data('changed') == true || $("#route").data('changed') == true) {
			$.confirm('YOU HAVE MODIFIED DATA! IF YOU CONTINUE YOU WILL LOOSE YOUR CHANGES!', function (answer) {
				if (answer === true) {
					$("#newitem").fadeOut();
					return false;
				}
			})
		} else {
			$("#newitem").fadeOut();
		}
	});

	// Admin user has completed editing of information
	$("#editcomplete").click(function () {
		$("#newitem").load("ajax/divs/trip_history_tab.php", { id: <?php echo $_REQUEST["id"]; ?><?php echo isset($_REQUEST["tabopen"]) ? ", tabopen: $(\"#tabs\").tabs(\"option\", \"active\")" : ""  ;?> }, function () {
			$("#newitem").fadeIn().draggable();
			$("#workspace").load("triphistory.php");
		} )
	});
})
</script>
</head>
<body>
<?php
if(!isset($_REQUEST["mod"])) {
?>
<button class="closebutton">X</button>
<?php
}
?>
<div style="position: relative; width: 600px; text-align: left;">
<table class="tbllistnoborder" style="width: 600px; left: 0px; position: relative;">
<?php
$res = pg_query($con, "select
dname,c1.cname as c1,c2.cname as  c2, to_char(tlbooked, 'dd/mm/yyyy') as tlbooked, tlcontainer, tlcontainer_ret,
case when trid is not null then tnumberplate || ' - ' || trnumberplate else tnumberplate end as tnumberplate,tltripconfig
from trip_log
left join trucks on tltruck=tid
left join trailers on ttrailer=trid
left join drivers on tldriver=did
left join customers as c1 on tlcustomer1=c1.cid
left join customers as c2 on tlcustomer2=c2.cid

where tlid=" . $_REQUEST["id"] );
$row = pg_fetch_assoc($res, 0);

$resavg = pg_query($con, "select ((sum(tlevalue)/trip_length_days(" . $_REQUEST["id"] . "))::numeric(12,2)) from trip_log left join trip_log_expenses on tlid=tletripid left join expense_types on tleetid=etid where (etaverageperday=true or position('ALLOWANCE' in upper(etdescription)) > 0) and tletripid=" . $_REQUEST["id"] . " group by tlbooked");
if(pg_num_rows($resavg) > 0) {
	$avg = pg_fetch_result($resavg,0 ,0);
} else {
	$avg = 0;
}

echo "<tr class='headtr'><td colspan=2>
<table style='width: 100%' class='detail'>
<tr><td class='bold underline'>Truck:</td><td class='bold underline'>" . $row["tnumberplate"] . "</td></tr>
<tr><td>Driver:</td><td>" . $row["dname"] . "</td></tr>
<tr><td>Booked:</td><td>" . $row["tlbooked"] . "</td></tr>
<tr><td>Allowance AVG:</td><td id='tdavg'>$. " . $avg . "/day</td></tr>
</table>
</td><td>
<table style='width: 100%' class='detail'>
<tr><td>Main Customer:</td><td>" . uinput("tlcustomer1", $_REQUEST["id"], $row["c1"], "trip_log", "tlcustomer1", "tlid", $_REQUEST["id"], false,true,250,"customer",null,true,false,true,false) . "</td></tr>
<tr><td>Return Customer:</td><td>" . uinput("tlcustomer2", $_REQUEST["id"], $row["c2"], "trip_log", "tlcustomer2", "tlid", $_REQUEST["id"], false,true,250,"customer",null,true,false,true,false)   . "</td></tr>
<tr><td>Container:</td><td>" . uinput("tlcontainer", $_REQUEST["id"], $row["tlcontainer"], "trip_log", "tlcontainer", "tlid", $_REQUEST["id"], false,true,100,"centered",null,null,false,false,false) . "</td></tr>
<tr><td>Container Ret:</td><td>" . uinput("tlcontainer_ret", $_REQUEST["id"], $row["tlcontainer_ret"], "trip_log", "tlcontainer_ret", "tlid", $_REQUEST["id"], false,true,100,"centered",null,null,false,false,false) . "</td></tr>
</table>
</tr>";
?>
</table>
</div>
<div id="tabs">
	<ul>
	<li><a href="ajax/divs/tabbed/tripinfo.php?id=<?php echo $_REQUEST["id"]; ?><?php echo isset($_REQUEST["mod"]) ? "&mod=true": ""; ?>">Schedule</a></li>
	<li><a href="ajax/divs/tabbed/tripinfo_expenses.php?id=<?php echo $_REQUEST["id"]; ?>">Expenses</a></li>
	<li><a href="ajax/divs/tabbed/tripinfo_fuel.php?id=<?php echo $_REQUEST["id"]; ?>">Fuel Log</a></li>
	<li><a href="ajax/divs/tabbed/equipment_info.php?id=<?php echo $_REQUEST["id"]; ?>">Equipment</a></li>
	<?php
	if($_SESSION['stataccess'] == "t") {
	?>
	<li><a href="ajax/divs/tabbed/triphistory_stats.php?id=<?php echo $_REQUEST["id"]; ?>">Statistics</a></li>
	<?php
	}
	?>
	<li><a href="ajax/divs/tabbed/tripinfo_route.php?id=<?php echo $_REQUEST["id"]; ?>">Route Configuration</a></li>
	<?php
	if($_SESSION["invoicing"] == "t") {
	?>
	<li><a href="ajax/divs/tabbed/invoicing_info.php?id=<?php echo $_REQUEST["id"]; ?>">Invoicing Information</a></li>
	<?php
	}
	?>
	<li><a href="ajax/divs/tabbed/tripinfo_cargo.php?id=<?php echo $_REQUEST["id"]; ?>">Cargo</a></li>
	</ul>
</div>
<br>
<?php
if($_SESSION["tripmod"] == "t" && isset($_REQUEST["mod"])) {
?>
<button id="editcomplete">EDITING COMPLETED</button>
<?php
}
?>
<input type="hidden" name="tltripconfig" id="tltripconfig" value="<?php echo $row["tltripconfig"]; ?>">
</body>
</html>