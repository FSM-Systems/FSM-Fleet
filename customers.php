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
		//$("#newitem").fadeIn().load("ajax/divs/newcustomer.php").draggable();
		$("#newitem").load("ajax/divs/newcustomer.php", {btntext: "CUSTOMER"}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	})

	$(".tel").click(function () {
		$("#newitem").load("ajax/divs/newcustomerphone.php", { cid: $(this).attr("id").replace("tel_","") }, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		})
	})

	$(".eml").click(function () {
		$("#newitem").load("ajax/divs/newcustomeremail.php", { cid: $(this).attr("id").replace("eml_","") }, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		})
	})

	searchbox();
	excel();
})
</script>
<?php
videohelp("k6_5WQJLX90");
?>
</head>
<body>
<div style="float: right" id="topbuttons">
	<button id="new"><img class="icon" src="icons/customer.png" alt=""> Create a new Customer</button>
</div>
<br><br>
<div class="topline">
Current customers registered on the System:
<br>
<?php quicksearch("customers", "cid"); ?>
<table class="tbllist searchtbl" id="tbllist" cellpadding=2 cellspacing=0 style="width: 60%">
<tr class="tbl">
	<th class="hidden" db="cid">ID</th>
	<th db="cname">Name</th>
	<th db="caddress">Address </th>
	<th></th>
</tr>
<?php
$res = pg_query($con, "select * from customers where company_id=" . $_SESSION["company"] . " order by cname");
if(pg_num_rows($res) > 0) {
	while($row = pg_fetch_assoc($res)) {
		echo "
			<tr class='tbl'>
					<td class='hidden excelid'>" . $row["cid"] . "</td>
					<td>" . uinput("cname", $row["cid"], $row["cname"], "customers", "cname", "cid", $row["cid"], false,true,220,null,null,null,false,true,false) . "</td>
					<td>" . uinput("caddress", $row["cid"], $row["caddress"], "customers", "caddress", "cid", $row["cid"], false,false,350,null,null,null,false,true,false) . "</td>
					<td class='delbtn'><button title='ADD EMAIL' class='eml' id='eml_" . $row["cid"] . "'><img class='smallbutton' src='icons/email.png'></button></td>
					<td class='delbtn'><button title='ADD PHONE' class='tel' id='tel_" . $row["cid"] . "'><img class='smallbutton' src='icons/telephone.png'></button></td>
					<td class='delbtn'>" . delbtn("customers", "cid", $row["cid"], "customers.php", null, "#workspace")  . "</td>
			</tr>
		";
	}
} else {
	echo "<tr><td colspan='100'>No customers registered yet.</td></tr>";
}
?>
</table>
</div>
</body>
</html>