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
		$("#newitem").load("ajax/divs/newtriplog.php", {btntext: "TRIP LOG"}, function () {
			$(this).fadeIn().draggable();
			$("#newfrm input:text").first().focus();
		});
	});

	$(".info").click(function () {
		//$("#newitem").fadeIn().load("ajax/divs/tripinfo.php?id=" + $(this).attr("id")).draggable();
		$("#newitem").load("ajax/divs/trip_info_tab.php", {id: $(this).attr("id")}, function () {
			$(this).fadeIn().draggable();
		});
	})

	// Do not open info window when clicking delete button
	$(".noevent").click(function (event) {
		event.preventDefault();
		event.stopImmediatePropagation()
	})

	searchbox();
	excel();
})
</script>
</head>
<?php
$res = pg_query($con, "
select tlid,to_char(tlbooked, 'dd/mm/YYYY') as tlbooked,tnumberplate,dname,tcdescription,c1.cname as c1,c2.cname as c2,ldescription,tlcontainer,tlcontainer_ret,trnumberplate from

trip_log left join trucks on tltruck=tid
left join trailers on tltrailer=trid
left join drivers on tldriver=did
left join trip_config on tltripconfig=tcid
left join customers as c1 on tlcustomer1=c1.cid
left join customers as c2 on tlcustomer2=c2.cid
left join login on tloperator=lid

where tlclosed = false and trip_log.company_id=" . $_SESSION["company"] . " order by tlid desc
");
?>
<body>
<div style="float: right"><button id="new"><img class="icon" src="icons/starttrip.png" alt=""> Start a new trip</button></div>
<br><br>
<div class="topline">
Current trips being performed (Total <?php echo pg_num_rows($res);?> trips active):
<br>
<?php quicksearch("trip_log", "tlid", false); ?>
<table class="tbllist searchtbl" cellpadding=2 cellspacing=0 style="min-width: 1152px;">
<tr>
	<th class="hidden">ID</th>
	<th>Booked</th>
	<th>Truck</th>
	<th>Container</th>
	<!-- <th>Container Ret</th> -->
	<th>Driver</th>
	<th>Trip Route</th>
	<th>Primary Customer</th>
	<th>Return Customer</th>
	<th>Created By</th>
	<th></th>
	<th></th>
</tr>
<?php
while($row = pg_fetch_assoc($res)) {
	echo "
	<tr class='tbl info' id='" . $row["tlid"] . "'>
	<td class='hidden'>" . $row["tlid"] . "</td>
	<td>" . $row["tlbooked"] . "</td>
	<td>" . $row["tnumberplate"] . " - " . $row["trnumberplate"] . "</td>
	<td>" . $row["tlcontainer"] . "&nbsp;</td>";
	//<td>" . $row["tlcontainer_ret"] . "&nbsp;</td>
	echo "<td>" . $row["dname"] . "</td>
	<td>" . $row["tcdescription"] . "</td>
	<td>" . $row["c1"] . "</td>
	<td>" . $row["c2"] . "</td>
	<td>" . $row["ldescription"] . "</td>
	<td class='delbtn'><button title='CLICK TO VIEW OR MODIFY' class='info' id='" . $row["tlid"] . "'><img class='smallbutton' src='icons/gear.png'></button></td>
	<td class='delbtn'>" . delbtn("trip_log", "tlid", $row["tlid"], "tripinfo.php", "noevent", "#workspace")  . "</td>
	</tr>";
}
?>
</table>
</div>
</body>
</html>