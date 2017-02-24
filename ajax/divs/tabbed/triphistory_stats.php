<?php
include "../../../inc/session_test.php";
include "itemcreators.php";
include "connection.inc";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	$("#genstats").click(function (e) {
		e.preventDefault();
		if ($("#frmtripstat").valid() == true) {
			updatevalue("trip_log", "tlvalue", $("#tripval").val(), "tlid", <?php echo $_REQUEST["id"]; ?>, true, false, this,false,false);
			updatevalue("trip_log", "tlvalueret", $("#tripvalret").val(), "tlid", <?php echo $_REQUEST["id"]; ?>, true, false, this,false,false);
			// Reload tab and wait 1 sec before doing as data has to be udated in db
			setTimeout(function () {
				$("#tabs").tabs("load", $("#tabs").tabs("option", "active"));
			}, 1000)
		}
	})

	$("#print").click(function () {
		// Change doc title temporary
		document.title= $("#truckprintname").text() + " " + $("#driverprintname").text();
		$("#printitem").attr("src", "prints/tripsummary.php?id=<?php echo $_REQUEST["id"]; ?>");
	});

		// Validation for elements
	$("#frmtripstat").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		},
		rules: {
			tripval: {
				required: true,
				number: true,
			},
			tripvalret: {
				required: true,
				number: true,
			},
		}
	})

	// On value changes refresh tab
	$('[name*="tlvalue"]').change(function () {
		// For some reaon we have to delay the reload to waiut for the ajax call to complete
		setTimeout(function () {
			$('#tabs').tabs('load', $("#tabs").tabs('option', 'active'));
		}, 1500)
	})

	$("#toroutekm").click(function (event) {
		event.preventDefault();
		$("#newitem").load("ajax/divs/trip_info_tab.php", {id: <?php echo $_REQUEST["id"]; ?>, mod: true, tabopen: 5}, function () {
			$(this).fadeIn().draggable();
			setTimeout(function () {
				$("#tldistance_<?php echo $_REQUEST["id"]; ?>").focus();
		}, 500)
		});
	})
})
</script>
</head>
<body>
<?php
// If no km the stop immediately
if(pg_fetch_result(pg_query($con, "select tldistance from trip_log where tlid=" . $_REQUEST["id"]), 0, 0) == "") {
	echo "This trip has no distance in km set. Statistics cannot be calculated.<br>Please update the value with the <a href='#' id='toroutekm'>Route Configuration --> Total Trip Kilomteres input box</a>";
	exit(0);
}
$tripval = pg_fetch_result(pg_query($con, "select tlvalue from trip_log where tlid=" . $_REQUEST["id"]), 0, 0);
// If no value initialize
if($tripval == "") {
	?>
	<form id="frmtripstat" name="frmtripstat">
	<table class="tbllistnoborder" style="width: 80%">
	<tr><td colspan="2" class="centered">This trip has no income value set. <br>Please add the data and statistics will be generated.</td></tr>
	<tr><td>Total main trip income:</td><td class="right">$<input type="text" class="centered" name="tripval" id="tripval" style="width: 50px;"></td></tr>
	<tr><td>Total return trip income:</td><td class="right">$<input type="text" class="centered" name="tripvalret" id="tripvalret" style="width: 50px;"></td></tr>
	<tr><td colspan="2" class="centered"><button id="genstats">GENERATE TRIP INCOME STATISTICS</button></td></tr>
	</table>
	</form>
	<?php
} else {
	// Display all stats
	$res = pg_query($con, "
	select 'total' as d, tlvalue as v, 1 as o from trip_log where tlid=" . $_REQUEST["id"] . " union
	select 'totalret' as d, tlvalueret as v, 1.5  as o from trip_log where tlid=" . $_REQUEST["id"] . " union
	select 'fuel' as d, sum(tlfvalue) as v, 2 as o from trip_log_fuel where tlftripid=" . $_REQUEST["id"] . " union
	select 'fuelconsumption' as d, coalesce(sum(tlfqty),0) as v, 5 as o from trip_log_fuel where tlftripid=" . $_REQUEST["id"] . " union
	select 'expense' as d, sum(tlevalue) as v, 3 as o from trip_log_expenses where tletripid=" . $_REQUEST["id"] . " union
	select 'km' as d, tldistance as v, 4 as o from trip_log where tlid=" . $_REQUEST["id"] . " union
	select 'tyres' as d, tltyrecost_per_km * tldistance as v, 5 as o from trip_log where tlid=" . $_REQUEST["id"] . "
	order by o"
	);

	$km = 0;
	$income = 0;
	$incomeret = 0;
	$fuel = 0;
	$fuelqty = 0;
	$exp = 0;
	$tyres = 0;
	echo "
	<table class='tbllistnoborder' style='width: 70%'>";
	while($row = pg_fetch_assoc($res)) {
		switch($row["d"]) {
			case "total":
					//echo "<tr><td>Main trip income:</td><td  class='right'>$. " . number_format($row["v"],2) . "</td></tr>";
					echo "<tr><td>Main trip income:</td><td  class='right'>$. " .
						uinput("tlvalue", $_REQUEST["id"], $row["v"], "trip_log", "tlvalue", "tlid", $_REQUEST["id"], true,false,80,"right",null,false,false,true,false)
						. "</td></tr>";
					$income = $row["v"];
				break;
				case "totalret":
					//echo "<tr><td>Return trip income:</td><td  class='right'>$. " . number_format($row["v"],2) . "</td></tr>";
					echo "<tr><td>Return trip income:</td><td  class='right'>$. " .
						uinput("tlvalueret", $_REQUEST["id"], $row["v"], "trip_log", "tlvalueret", "tlid", $_REQUEST["id"], true,false,80,"right",null,false,false,true,false)
						//$name, $id, $value, $table, $columnname, $idcolumnname, $idcolumnvalue, $isanumber, $uppercase, $widthinpx, $class, $extraproperties,$hiddeninput, $isadate, $onchangevent, $textarea)
						. "</td></tr>";
					$incomeret = $row["v"];
				break;
			case "fuel":
				echo "<tr><td>Total fuel expenses:</td><td class='right'>$. " . number_format($row["v"],2) . "</td></tr>";
				$fuel = $row["v"];
				break;
			case "expense":
				echo "<tr><td>Total trip expenses:</td><td class='right'>$. " . number_format($row["v"],2) . "</td></tr>";
				$exp = $row["v"];
				break;
			case "km":
				echo "<tr><td>Trip distance:</td><td class='right'>Km. " . $row["v"] . "</td></tr>";
				$km = $row["v"];
				break;
			case "fuelconsumption":
				echo "<tr><td>Fuel consumption:</td><td class='right'>LT. " . $row["v"] . "</td></tr>";
				$fuelqty = $row["v"];
				break;
			case "tyres":
				echo "<tr><td>Fixed tyre consumption expenses:</td><td class='right'>$. " . number_format($row["v"],2) . "</td></tr>";
				$tyres = $row["v"];
				break;
		}
	}
	if($fuelqty > 0 ) {
		echo "<tr><td>Average fuel consumption:</td><td class='right'>" . number_format($km/$fuelqty,2) . " Km/L</td></tr>";
	} else {
		echo "<tr><td>Average fuel consumption:</td><td class='right'> --- </td></tr>";
	}
	if($km > 0) {
		echo "<tr><td>Income per KM:</td><td class='right'>" . number_format(($income + $incomeret - $exp - $fuel - $tyres)/$km,2) . " $/KM</td></tr>";
	} else {
		echo "<tr><td>Income per KM:</td><td class='right'> --- </td></tr>";
	}
	echo "
	</table><br>";
	?>
	<button id="print"><img src="icons/print.png" alt=""> PRINT TRIP SUMMARY </button>
	<?php
}
?>
</body>
</html>