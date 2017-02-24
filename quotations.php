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
	$(".dp").datepicker();

	$("#filter").click(function () {
		$("#newitem").load("ajax/divs/applyfilters_quotations.php", function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	})

	$("#newquote").click(function () {
		$("#newitem").load("ajax/divs/newquotation.php", {btntext: "QUOTATION AND SEND BY EMAIL"}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

	$(".info").click(function () {
		$("#newitem").load("ajax/divs/quotation_notes.php", {qid: $(this).attr("id")}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	})

	acomplete(".client","ajax/autocompletes/customers.php");
	acomplete(".country","ajax/autocompletes/countries.php",true, false, true, "countries", "cncountry", "cnid");
	acomplete(".permits","ajax/autocompletes/quotation_permits.php",true, false, true, "quotation_permits", "qpdescription", "qpid");

	searchbox();
	excel();
})
</script>
</head>
<?php
// Build dynamic where clause
$qstr = "";
$whereclause = " where 1=1 ";
if(isset($_REQUEST["filter"]) && $_REQUEST["filter"] == "true") {
	if($_REQUEST["client"] != "") {
		$whereclause .= " and cid=" . $_REQUEST["client"];
		$qstr .= 'client=' . $_REQUEST["client"] . '&';
	}

	if($_REQUEST["destination"] != "") {
		$whereclause .= " and position(upper('" . $_REQUEST["destination"] . "') in upper(qdestination)) > 0";
		$qstr .= 'destination=' . $_REQUEST["destination"] . '&';
	}

	if($_REQUEST["country"] != "") {
		$whereclause .= " and qcountry=" . $_REQUEST["country"];
		$qstr .= 'country=' . $_REQUEST["country"] . '&';
	}

	if($_REQUEST["goods"] != "") {
		$whereclause .= " and position(upper('" . $_REQUEST["goods"] . "') in upper(qcargo)) > 0";
		$qstr .= 'goods=' . $_REQUEST["goods"] . '&';
	}

	if($_REQUEST["permits"] != "") {
		$whereclause .= " and qpermits =" . $_REQUEST["permits"];
		$qstr .= 'permits=' . $_REQUEST["permits"] . '&';
	}

	// Clean new query string
	$qstr = '?' . substr_replace($qstr, '', strlen($qstr) - 1);
}

$whereclause .= " and quotations.company_id=" . $_SESSION["company"];

// Set an offset for the pager
$numresults = 20;
if(isset($_REQUEST['pager'])) {
	$offset = ' offset ' . $_REQUEST['pager'] * $numresults;
} else {
	$offset = ' offset 0 ';
}

// Count total rows for pager
$respager = pg_query($con, "select count(*) from quotations left join quotation_permits on qpermits=qpid left join customers on qclient=cid left join countries on qcountry=cnid " . $whereclause);
$resultcount = ceil(pg_fetch_result($respager, 0, 0) / $numresults);


$res = pg_query($con, "
select *, to_char(qdate, 'dd/mm/yyyy') as qdate from quotations left join quotation_permits on qpermits=qpid left join customers on qclient=cid left join countries on qcountry=cnid " . $whereclause . " order by qid desc limit " . $numresults . $offset);
?>
<body>
<div style="float: right">
<button id="newquote"><img class="icon" src="icons/quotation.png" alt=""> New quote</button>
<button id="filter"><img class="icon" src="icons/filter.png" alt=""> Apply filters</button>
<button id="reset" onclick="$('#newitem').fadeOut();$('#workspace').load('quotations.php');"><img class="icon" src="icons/reset.png" alt=""> Reset filters</button>
</div>
<br><br>
<div class="topline">
Quotations created and registered in the system:
<br>
<table class="tbllist searchtbl" cellpadding=2 cellspacing=0>
<tr>
	<th>ID</th>
	<th>Date</th>
	<th>Client</th>
	<th>Destination</th>
	<th>Country</th>
	<th>Goods/Cargo</th>
	<th>Length</th>
	<th>Width</th>
	<th>Height</th>
	<th>Weight</th>
	<th>Freight</th>
	<th>Permits/Escort</th>
	<th></th>
	<th></th>
</tr>
<?php
while($row = pg_fetch_assoc($res)) {
	echo "
	<tr class='tbl'>
	<td>" . $row["qid"] . "</td>
	<td>" . uinput("qdate", $row["qid"], $row["qdate"], "quotations", "qdate", "tid", $row["qid"], false,false,70,"dp centered",null,null,true,true,false) . "</td>
	<td>" . uinput("qclient", $row["qid"], $row["cname"], "quotations", "qclient", "qid", $row["qid"], false,true,180,"client",null,true,false,true,false) . "</td>
	<td>" . uinput("qdestination", $row["qid"], $row["qdestination"], "quotations", "qdestination", "qid", $row["qid"], false,true,null,null,null,null,false,true,false) . "</td>
	<td>" . uinput("qcountry", $row["qid"], $row["cncountry"], "quotations", "qcountry", "qid", $row["qid"], false,true,100,"country",null,true,false,true,false) . "</td>
	<td>" . uinput("qcargo", $row["qid"], $row["qcargo"], "quotations", "qcargo", "qid", $row["qid"], false,true,200,null,null,null,false,true,false) . "</td>
	<td>" . uinput("qlength", $row["qid"], $row["qlength"], "quotations", "qlength", "qid", $row["qid"], true,false,35,"centered",null,null,false,true,false) . "</td>
	<td>" . uinput("qwidth", $row["qid"], $row["qwidth"], "quotations", "qwidth", "qid", $row["qid"], true,false,35,"centered",null,null,false,true,false) . "</td>
	<td>" . uinput("qheight", $row["qid"], $row["qheight"], "quotations", "qheight", "qid", $row["qid"], true,false,35,"centered",null,null,false,true,false) . "</td>
	<td>" . uinput("qweight", $row["qid"], $row["qweight"], "quotations", "qweight", "qid", $row["qid"], true,false,35,"centered",null,null,false,true,false) . "</td>
	<td>" . uinput("qvalue", $row["qid"], $row["qvalue"], "quotations", "qvalue", "qid", $row["qid"], true,false,40,"centered",null,null,false,true,false) . "</td>
	<td>" . uinput("qpermits", $row["qid"], $row["qpdescription"], "quotations", "qpermits", "qid", $row["qid"], false,true,null,"permits",null,true,false,true,false) . "</td>
	<td class='delbtn'><button title='CLICK TO VIEW AND EDIT NOTES' class='info' id='" . $row["qid"] . "'><img class='smallbutton' src='icons/notes.png'></button></td>
	<td class='delbtn'>" . delbtn("quotations", "qid", $row["qid"], "quotations.php", null, "#workspace")  . "</td>
	</tr>";
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
		$("#workspace").load("quotations.php<?php echo $qstr; ?>&pager=" + $(this).attr("id"));
	<?php
	} else {
	?>
		$("#workspace").load("quotations.php?pager=" + $(this).attr("id"));
	<?php
	}
	?>
})
</script>
</div>
</body>
</html>