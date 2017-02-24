<?php
include "session_test.php";
include "basemessages.php";
/*
name of element
associated id
value of textbox
table to update
column to update
id column name for use in where clause
id column value for use in where clause
is it a number or text?
force new value to upper case?
width of text box
class name for textbox
extra properties such as style=.. etc etc
add hidden div?? for use when using the textbox with a dropdown. It will store the ID of the selected item, if selected update will be performed only on hidden div
onchangevent wether or not to add the event to the element. If no then we can call function from jquery
*/
function uinput($name, $id, $value, $table, $columnname, $idcolumnname, $idcolumnvalue, $isanumber, $uppercase, $widthinpx, $class, $extraproperties,$hiddeninput, $isadate, $onchangevent, $textarea) {
	// this.value as we are always updating the value using the current value in the created textbox
	$width = "";
	$ret = "";
	$date = "";
	if($widthinpx != null) {
		$width = " style = 'width: " . $widthinpx . "px' ";
	} else {
		$width;
	}

	$classname = "";
	if($class != null) {
		$classname = " class='" . $class . "' ";
	} else {
		$classname;
	}
	if($isadate == true) {
		$date = "true";
	} else {
		$date = "false";
	}
	if($uppercase == true) {
		$ucase = "true";
	} else {
		$ucase = "false";
	}
	if($textarea == true) {
		$tarea = "true";
	} else {
		$tarea = "false";
	}
	if($isanumber == true) {
		$isnum = "true";
	} else {
		$isnum = "false";
	}
	$ret .= "
	<input type=text onfocus='this.oldvalue = this.value;' " . $width . $classname . $extraproperties . " id='" . $name . "_" . $id . "' name='" . $name . "_" . $id . "'  value='" . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . "' ";
	// WHen creating a textbox we always save oldvalue in this.oldvalue property of textbox itself so we can make user revert..
	if($hiddeninput == false) {
		$ret .= " onfocus='this.oldvalue = this.value;' ";
		if($onchangevent == true) {
			// Most of time we update only textboxes not textarea with this function this is why texrarea parame is set to false.
			$ret .= "onchange='updatevalue(\"" . $table . "\",\"" . $columnname . "\", this.value ,\"" . $idcolumnname . "\",\"" . $idcolumnvalue . "\",\"" . $isnum . "\",\"" . 	$ucase . "\",this, \"" . $date . "\",\"" . $tarea . "\")'";
		}
	}
	$ret .= ">";

	if($hiddeninput == true) {
		$ret .= "<input type='hidden' id='" . $name . "_" . $id . "_id' name='" . $name . "_" . $id . "'_id' onfocus='this.oldvalue = this.value;'";
		if($onchangevent == true) {
			$ret .= "onchange='updatevalue(\"" . $table . "\",\"" . $columnname . "\", this.value,\"" . $idcolumnname . "\",\"" . $idcolumnvalue . "\",true, false, this, false,false)'>";
		}
	}
	return $ret;
}

function delbtn($table, $idcolumnname, $id, $pagetoload, $class, $divtoload) {
	$classname = "";
	if($class != null) {
		$classname = " class='". $class . "'";
	} else {
		$classname;
	}
	return "
	<button type='button' id='" . $table . "_" . $id . "' " . $classname . " title='DELETE THIS ITEM (" . $id . ")?' onclick='event.preventDefault(); delitem(\"". $table . "\",\"" . $idcolumnname . "\",\"" . $id . "\",\"" . $pagetoload . "\",\"" . $divtoload . "\", false)'><img class='smallbutton' src='icons/delete.png' " . $classname . "></button>
	";
}

function quicksearch($exceltable, $excelsearchid,$excelexport = true) { // parameters are used when creating the excel file from php
?>
<div style="float: right">
<input type="hidden" id="exceltable" value="<?php echo $exceltable; ?>">
<input type="hidden" id="excelsearchid" value="<?php echo $excelsearchid; ?>">
<?php
if($excelexport == true) {
?>
<img  src="icons/excel.png" alt="" id="excel" title="Export the current table view to Excel format">
<?php
}
?>
<label><input type="text" id="search" placeholder="Quick search.."> <button id="delquicksearch" class="smallbtn">X</button></label>
</div>
<br>
<?php
}

function videohelp($youtubeid) {
	?>
	<script type="text/javascript">
	// Add video button after page button
	$("#new").after(' <button id="btnvideohelp"><img class="icon" src="icons/youtube-logotype.png" alt=""> Online Help</button>');
	$("#topbuttons").after('<div id="videohelp" class="videohelp"></div>');

	$("#btnvideohelp").click(function () {
		$("#videohelp").html('<iframe id="tube" width="420" height="315" src="https://www.youtube.com/embed/<?php echo $youtubeid; ?>" frameborder="0" allowfullscreen></iframe><br><br><button onclick="$(\'#videohelp\').slideUp().fadeOut(); $(\'#tube\').attr(\'src\', null);">Close Help Video</button>').draggable().slideDown().fadeIn();
	});
	</script>
	<?php
}

function cpitem($con, $type) {
	switch($type) {
		case "tripstatus":
			// All trucks that have loading date set at starting
			$res = pg_query($con, "
			select tltruck, case when tltrailer is not null then tnumberplate || '/' || trnumberplate else tnumberplate end as tnumberplate,array_to_string(trip_status(tlid), '§§§') as ts,
			c1.cname as c1, c1.cid as c1id, c2.cname as c2, c2.cid as c2id, dname, tlcontainer, tlcontainer_ret,tlid, tlinvoiceno, tlinvoiceno_ret, tlcargo, tlcargo_ret, did from trip_log
			left join trucks on tltruck=tid
			left join trailers on tltrailer=trid
			left join customers as c1 on tlcustomer1=c1.cid
			left join customers as c2 on tlcustomer2=c2.cid
			left join drivers on tldriver=did
			where tlclosed=false and ('AT CHECKPOINT' = any(trip_status(tlid)) or 'AT YARD' = any(trip_status(tlid))) and trucks.company_id=" . $_SESSION["company"] . " order by trip_status(tlid) desc");
			?>
			<b>TRUCKS AT CHECKPOINTS:</b><br>
			<div class="cpanelitem">
			<table class="tblcp" cellpadding=2 cellspacing=0>
				<tr>
					<th style="width: 12%;">Truck</th>
					<th style="width: 8%;">Driver</th>
					<th style="width: 10%;">Container</th>
					<th style="width: 15%;">Location</th>
					<th style="width: 20%;">Customers</th>
					<th style="width: 14%;">Status</th>
					<th style="width: 9%;" class="right">At checkpoint</th>
				</tr>
				<?php
				if(pg_num_rows($res) > 0 ) {
					while($row = pg_fetch_assoc($res)) {
						// Split ts data for use
						$ts = explode("§§§", $row["ts"]);
						if(($ts[3] > $ts[5]) && $ts[5] != 0 ) { // Only compare if days are set in location.php for comparison
							//$style = " style='color: red; font-weight: bold; cursor: pointer;' title='This truck has been at the checkpoint for more that " . $ts[5] . " days!' ";
							//$style = " class='tblcpred' title='This truck has been at the checkpoint for more than " . $ts[5] . " days!' ";
							$style = " class='tblcpred det'";
						} else {
							$style = " class='hover det'";
						}
						if($ts[1] == "AWAITING AT YARD") {
							$style = " class='tblcporange det'";
						}
						// Drivers first name only
						$drivername = explode(" ", $row["dname"]);
						echo "
						<tr" . $style . " id='det_" . $row["tlid"] . "' style='line-height: 16px;'>
							<td>" . $row["tnumberplate"] . "</td>
							<td><span  class='qtipdriver' id='" . $row["did"] . "'>" . $drivername[0] . "</span></td>
							<td><span class='qtipcargo' id='" . $row["tlid"] . "'>" . $row["tlcontainer"] . "</span><br><span class='qtipcargoret' id='" . $row["tlid"] . "'>" . $row["tlcontainer_ret"] . "</span></td>
							<td>" . str_replace(" (", "<br>(", $ts[0]) . "</td>
							<td><span class='qtipcust' id=" . $row["c1id"] . ">1. " . $row["c1"] . "</span><br><span class='qtipcust' id=" . $row["c2id"] . ">2. " . $row["c2"] . "</span></td>";
							echo "<td>" . $ts[1] . ": " . $ts[2] . "</td>
							<td class='right'>" . abs($ts[3]) . " day(s)</td>
						</tr>";
					}
				} else {
					echo "<tr><td colspan='100'><label style='color: green; font-weight: bold; text-decoration: underline'>NO TRUCKS CURRENTLY AT CHECKPOINTS.</label></td></tr>";
				}
				?>
			</table>
			</div>
			<?php
			break;
		case "ontheroad":
				// All trucks that have loading date set at starting
				$res = pg_query($con, "
				select tltruck, case when tltrailer is not null then tnumberplate || '/' || trnumberplate else tnumberplate end as tnumberplate,array_to_string(trip_status(tlid), '§§§') as ts,
				c1.cname as c1, c1.cid as c1id, c2.cname as c2, c2.cid as c2id, dname, tlcontainer, tlcontainer_ret, tlid, tlinvoiceno, tlinvoiceno_ret, tlcargo, tlcargo_ret,did from trip_log
				left join trucks on tltruck=tid
				left join trailers on tltrailer=trid
				left join customers as c1 on tlcustomer1=c1.cid
				left join customers as c2 on tlcustomer2=c2.cid
				left join drivers on tldriver=did
				where tlclosed=false and 'ON THE ROAD' = any(trip_status(tlid)) and trucks.company_id=" . $_SESSION["company"] . " order by trip_status(tlid)");
				?>
				<b>TRUCKS TRAVELLING:</b><br>
				<div class="cpanelitem">
				<table class="tblcp" cellpadding="2" cellspacing="0">
					<tr>
					<th style="width: 12%;">Truck</th>
					<th style="width: 8%;">Driver</th>
					<th style="width: 10%;">Container</th>
					<th style="width: 15%;">Last known location</th>
					<th style="width: 20%;">Customers</th>
					<th style="width: 14%;">Status</th>
					<th style="width: 9%;" class="right">On the road</th>
					</tr>
					<?php
					if(pg_num_rows($res) > 0 ) {
						while($row = pg_fetch_assoc($res)) {
							// Split ts data for use
							$ts = explode("§§§", $row["ts"]);
							if(count($ts) == 6)  {
								// Truck has left and is on the road
								//if($ts[3] > $ts[5]) {
								if(($ts[3] > $ts[5]) && $ts[5] != 0 ) {
									//$style = " style='color: green; font-weight: bold; cursor: pointer;' title='This truck has been on the road for more that " . $ts[5] . " days!' ";
									$style = " class='tblcpblue det'";
								} else {
									$style = " class='hover det'";
								}
								// Drivers first name only
								$drivername = explode(" ", $row["dname"]);
								echo "
								<tr" . $style . " id='det_" . $row["tlid"] . "' style='line-height: 16px;'>
									<td>" . $row["tnumberplate"] . "</td>
									<td><span  class='qtipdriver' id='" . $row["did"] . "'>" . $drivername[0] . "</span></td>
									<td><span class='qtipcargo' id='" . $row["tlid"] . "'>" . $row["tlcontainer"] . "</span><br><span class='qtipcargoret' id='" . $row["tlid"] . "'>" . $row["tlcontainer_ret"] . "</span></td>
									<td>" . str_replace(" (", "<br>(", $ts[0]) . "</td>
									<td><span class='qtipcust' id=" . $row["c1id"] . ">1. " . $row["c1"] . "</span><br><span class='qtipcust' id=" . $row["c2id"] . ">2. " . $row["c2"] . "</span></td>";
									echo "<td>" . $ts[1] . ": " . $ts[2] . "</td>
									<td class='right'>" . abs($ts[3]) . " day(s)</td>
								</tr>";
							} else {
								//echo "<tr><td>" . $row["tnumberplate"] . "</td><td colspan=4>CURRENTLY BOOKED BUT NOT DEPARTED OR LOADED</td></tr>";
							}
						}
					} else {
						echo "<tr><td colspan='100'><label style='color: green; font-weight: bold; text-decoration: underline'>NO TRUCKS CURRENTLY ON THE ROAD.</label></td></tr>";
					}
					?>
				</table>
				</div>
				<?php
			break;
		case "notbooked":
			// All trucks that have are not in tlclosed=false
			$res = pg_query($con, "
			select tid, tnumberplate, trnumberplate from trucks left join trailers on ttrailer=trid where tid not in (select tltruck from trip_log where tlclosed=false) and trucks.company_id=" . $_SESSION["company"] . " order by tnumberplate");
			if(pg_num_rows($res) > 0 ) {
			?>
			<b>TRUCKS IN YARD (<?php echo pg_num_rows($res);?>):</b><br>
				<div class="cpanelitem">
					<div id="atyard">
						<table class="tblcp" cellpadding=2 cellspacing=0 style="width: 90px;">
							<tr>
								<th></th>
							</tr>
							<?php
							while($row = pg_fetch_assoc($res)) {
								echo "
								<tr>
									<td class='new' id='new_" . $row["tid"] . "' style='white-space:nowrap; width: auto; padding: 5px;'>" . $row["tnumberplate"];
									if($row["trnumberplate"] != "") {
										echo " / " . $row["trnumberplate"];
									}
								echo "</td></tr>";
							}
						}
						?>
						</table>
					</div>
				</div>
			<?php
			break;
		}
}
?>