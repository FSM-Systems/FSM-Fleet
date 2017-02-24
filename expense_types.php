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
		$("#newitem").load("ajax/divs/newexpense.php", {btntext: "EXPENSE TYPE"}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

	searchbox();
	excel();
})
</script>
</head>
<body>
<div style="float: right"><button id="new"><img class="icon" src="icons/cash.png" alt=""> Create a new Expense Type</button></div>
<br><br>
<div class="topline">
Expenses Registered on the System:
<br>
<?php quicksearch("expense_types", "etid"); ?>
<table class="tbllist searchtbl" cellpadding=2 cellspacing=0 style="width: 50%; min-width: 600px;">
<tr>
	<th class="hidden" db="etid">ID</th>
	<th db="etdescription">Expense Description</th>
	<th db="etfixedvalue">Fixed Value</th>
	<th class='centered'>Calculate Averages</th>
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
$respager = pg_query($con, "select count(*) from expense_types where company_id=" . $_SESSION['company']);
$resultcount = ceil(pg_fetch_result($respager, 0, 0) / $numresults);

$res = pg_prepare($con, "expenses", "select * from expense_types where company_id=$1 order by etdescription limit " . $numresults . $offset);
$res = pg_execute($con, "expenses", array($_SESSION["company"]));
while($row = pg_fetch_assoc($res)) {
	echo "
		<tr class='tbl'>
				<td class='hidden excelid'>" . $row["etid"] . "</td>
				<td>" . uinput("etdescription", $row["etid"], $row["etdescription"], "expense_types", "etdescription", "etid", $row["etid"], false,true,350,null,null,null,false,true,false) . "</td>
				<td>" . uinput("etfixedvalue", $row["etid"], $row["etfixedvalue"], "expense_types", "etfixedvalue", "etid", $row["etid"], true,false,50,"centered",null,null,false,true,false) . "</td>
				<td class='centered'><input type=\"checkbox\" id=\"stataccess\" name=\"stataccess\"";
				if($row["etaverageperday"] == "t") {
					echo " checked";
				}
				echo " onclick=\"updatecheckbox('expense_types', 'etaverageperday', this.checked, 'etid'," .  $row["etid"] . ")\"	></td>
				<td class='delbtn'>" . delbtn("expense_types", "etid", $row["etid"], "expense_types.php", null, "#workspace")  . "</td>
		</tr>
	";
}
?>
</table>
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
	$("#workspace").load("expense_types.php?pager=" + $(this).attr("id"));
})
</script>
</div>
</body>
</html>