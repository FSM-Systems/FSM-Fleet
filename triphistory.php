<?php
include "inc/session_test.php";
include "inc/connection.inc";
include "inc/itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	$("#new").click(function () {
		$("#newitem").load("ajax/divs/applyfilters.php", function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

	$(".info").click(function () {
		$("#newitem").load("ajax/divs/trip_history_tab.php", { id: $(this).attr("id").replace("inf_","") }, function () {
			$("#newitem").fadeIn().draggable();
		} )
	})

	searchbox();
	excel();
})
</script>
</head>
<body>
<div style="float: right">
<button id="new"><img class="icon" src="icons/filter.png" alt=""> Apply filters</button>
<button id="reset" onclick="$('#newitem').fadeOut();$('#workspace').load('triphistory.php');"><img class="icon" src="icons/reset.png" alt=""> Reset filters</button>
</div>
<br><br>
<div class="topline">
Trips that have been completed:
<br>
<table class="tbllist searchtbl" cellpadding=2 cellspacing=0 style="min-width: 1152px;">
<tr>
	<th class="hidden">ID</th>
	<th style="width: 120px;">Truck</th>
	<th>Driver</th>
	<th style="width: 90px;">Container</th>
	<!-- <th style="width: 90px;">Container Ret</th> -->
	<th style="width: 150px;">Trip Route</th>
	<th>Main Customer</th>
	<th>Return Customer</th>
	<th>Start</th>
	<th>End</th>
	<th class="right">Days</th>
	<th>Created By</th>
</tr>
<?php
// Build dynamic where clause
$qstr = '';
$whereclause = "";
if(isset($_REQUEST["filter"]) && $_REQUEST["filter"] == "true") {
	$whereclause = " where tlclosed=true ";
	if($_REQUEST["tnumberplate"] != "") {
		$whereclause .= " and trucks.tid=" . $_REQUEST["tnumberplate"];
		$qstr .= 'tnumberplate=' . $_REQUEST["tnumberplate"] . '&';
	}

	if($_REQUEST["trnumberplate"] != "") {
		$whereclause .= " and trid=" . $_REQUEST["trnumberplate"];
		$qstr .= 'trnumberplate=' . $_REQUEST["trnumberplate"] . '&';
	}

	if($_REQUEST["driver"] != "") {
		$whereclause .= " and did=" . $_REQUEST["driver"];
		$qstr .= 'did=' . $_REQUEST["did"] . '&';
	}

	if($_REQUEST["customer"] != "") {
		$whereclause .= " and (tlcustomer1 =" . $_REQUEST["customer"] . " or tlcustomer2 =" . $_REQUEST["customer"] . ")";
		$qstr .= 'customer=' . $_REQUEST["customer"] . '&';
	}

	if($_REQUEST["fromdate"] != "") {
		$whereclause .= " and tlid in (select tlid from trip_log_det where tldactiondate between '" . $_REQUEST["fromdate"] . "' and '" . $_REQUEST["todate"] . "')";
		$qstr .= 'fromdate=' . $_REQUEST["fromdate"] . '&' . $_REQUEST['todate'] . '&';
	}

	if($_REQUEST["container"] != "") {
		$whereclause .= " and upper(tlcontainer) like '%" . strtoupper(str_replace("-", "", str_replace(" ", "", $_REQUEST["container"]))) . "%'";
		$qstr .= 'container=' . $_REQUEST["container"] . '&';
	}

	// Clean new query string
	$qstr = '?' . substr_replace($qstr, '', strlen($qstr) - 1);

	// Select closed after filtering
	$whereclause .= " and tlclosed=true ";
} else {
	$whereclause = " where tlclosed=true ";
}

$whereclause .= " and trip_log.company_id=" . $_SESSION["company"];

// Set an offset for the pager
$numresults = 20;
if(isset($_REQUEST['pager'])) {
	$offset = ' offset ' . $_REQUEST['pager'] * $numresults;
} else {
	$offset = ' offset 0 ';
}

// Count total rows for pager
$respager = pg_query($con, "select count(*) from trip_log left join trucks on tltruck=tid left join trailers on tltrailer=trid left join drivers on tldriver=did left join customers as c1 on tlcustomer1=c1.cid left join customers as c2 on tlcustomer2=c2.cid left join trip_config on tltripconfig=tcid left join login on tloperator=lid " . $whereclause);
$resultcount = ceil(pg_fetch_result($respager, 0, 0) / $numresults);

$strsql = "select *, case when trid is not null then tnumberplate || ' - ' || trnumberplate else tnumberplate end as tnumberplate,
c1.cname as c1, c2.cname as c2
from trip_log
left join trucks on tltruck=tid
left join trailers on tltrailer=trid
left join drivers on tldriver=did
left join customers as c1 on tlcustomer1=c1.cid
left join customers as c2 on tlcustomer2=c2.cid
left join trip_config on tltripconfig=tcid
left join login on tloperator=lid
" . $whereclause . "
order by tlid desc limit " . $numresults . $offset;

$res = pg_query($con, $strsql);
while($row = pg_fetch_assoc($res)) {
	// Find min and max dates of trip
	$res2 = pg_query($con, "select to_char(min(tldactiondate), 'dd/mm/yyyy') as min, to_char(max(tldactiondate) , 'dd/mm/yyyy') as max, max(tldactiondate) - min(tldactiondate) as days from trip_log_det where tldtripid=" . $row["tlid"]);
	echo "
		<tr class='tbl info' id='inf_" . $row["tlid"] . "'>
				<td class='hidden'>" . $row["tlid"] . " </td>
				<td>" . $row["tnumberplate"] . " </td>
				<td>" . $row["dname"] . " </td>
				<td>" . $row["tlcontainer"] . "&nbsp;</td>";
				//<td>" . $row["tlcontainer_ret"] . "&nbsp;</td>
				echo "<td>" . $row["tcdescription"] . " </td>
				<td>" . $row["c1"] . " </td>
				<td>" . $row["c2"] . " </td>
				<td>" . pg_fetch_result($res2, 0 ,0 ) . " </td>
				<td>" . pg_fetch_result($res2, 0, 1) . " </td>
				<td class='right' style='padding-right: 4px;'>" . pg_fetch_result($res2, 0, 2 ) . " </td>
				<td>" . $row["ldescription"] . " </td>
				<td class='delbtn'><button class='info' id='inf_" . $row["tlid"] . "'><img class='smallbutton' src='icons/information-icon.png'></button></td>
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
	<?php
	if (strlen($qstr) > 0 ) {
	?>
		$("#workspace").load("triphistory.php<?php echo $qstr; ?>&pager=" + $(this).attr("id"));
	<?php
	} else {
	?>
		$("#workspace").load("triphistory.php?pager=" + $(this).attr("id"));
	<?php
	}
	?>
})
</script>
</div>
</body>
</html>