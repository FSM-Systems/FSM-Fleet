<?php
include "../../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

pg_query($con, "begin");

$resdist = pg_query($con, "select coalesce(tcdistance,0) from trip_config where tcid=" . $_REQUEST['tltripconfig_id']);
$distance = pg_fetch_result($resdist, 0, 0);

$restyre = pg_query($con, "select coalesce(ts_tyre_usd_perkm,0) as tyreexp from trip_settings where tsid=1");
if(pg_num_rows($restyre) > 0 ) {
	$tyreexp = pg_fetch_result($restyre, 0, 0);
} else {
	$tyreexp = 0;
}

// Create new trip header and save id
$res = pg_query($con, "insert into trip_log (tltruck,tldriver,tlequipment,tltripconfig,tlcustomer1,tlcustomer2,tlcontainer,tlcontainer_ret,tlcargo,tlcargo_ret,tloperator,tldistance,tltyrecost_per_km,company_id) values (

" . $_REQUEST['tltruck_id'] . ",
" . $_REQUEST['tldriver_id'] . ",
" . $_REQUEST['tlequipment_id'] . ",
" . $_REQUEST['tltripconfig_id'] . ",
" . $_REQUEST['tlcustomer1_id'] . ",
" . $_REQUEST['tlcustomer2_id'] . ",
replace(upper('" . $_REQUEST['tlcontainer'] . "'), ' ', ''),
replace(upper('" . $_REQUEST['tlcontainer_ret'] . "'), ' ', ''),
'" . $_REQUEST['tlcargo'] . "',
'" . $_REQUEST['tlcargo_ret'] . "',
" . $_SESSION["id"] . ",
" . $distance . ",
" . $tyreexp . ",
" . $_SESSION["company"] . ")
 returning tlid");

if(pg_result_error($res) != "") {
	// Delete trip header
	pg_query($con, "delete from trip_logs where tlid=" . $logid);
	// Display error
	echo pg_result_error($res);
	pg_query($con, "rollback;");
} else {
	// Get trip id
	$logid = pg_fetch_result($res, 0, 0);
	// Get default trailer
	$deftrailer = pg_fetch_result(pg_query($con, "select ttrailer from trucks where tid=" . $_REQUEST["tltruck_id"]), 0, 0);
	// Update the trailer used for this trip. If we are using the same one as in trucks then add default one.
	// If we change the value then update also trucks table so that the new one becomes default
	// Check only if values are not null
		if($deftrailer != $_REQUEST["tltrailer_id"]) {
			// Update defaults and trip_log
			$res = pg_query($con, "update trip_log set tltrailer=" . $_REQUEST["tltrailer_id"] . " where tlid=" .$logid);
			$res = pg_query($con, "update trucks set ttrailer=" . $_REQUEST["tltrailer_id"] . " where tid=" . $_REQUEST["tltruck_id"]);
			// Remove selected trailer from any previous truck
			$res = pg_query($con, "update trucks set ttrailer=null where ttrailer=" . $_REQUEST["tltrailer_id"] . " and tid <> " . $_REQUEST["tltruck_id"]);
			// Delete any
		} else {
			// Use default trailer
			$res = pg_query($con, "update trip_log set tltrailer=" . $deftrailer . " where tlid=" .$logid);
		}

	if(pg_result_error($res) != "") {
		// Delete trip header
		pg_query($con, "delete from trip_logs where tlid=" . $logid);
		// Display error
		echo pg_result_error($res);
		pg_query($con, "rollback;");
	} else {
		// update also trip_log_det
		// select all location that are specific to this trip
		//$res = pg_query($con, "select * from trip_config left join trip_config_det on tcid=tcdtripid left join locations on tcdlocation=locid left join location_types on loctype=ltid where tcid=" . $_REQUEST["tltripconfig_id"] . " order by tcdid");
		$res = pg_query($con, "select * from trip_config_det left join locations on tcdlocation=locid left join location_types on loctype=ltid where tcdtripid=" . $_REQUEST["tltripconfig_id"] . " order by tcdid");

		if(pg_num_rows($res) == 0) {
			echo "<br><br><b style='color: red'>THIS TRIP CONFIGURATION DOES NOT HAVE ANY STEPS! PLEASE ADD THEM AND TRY AGAIN!</b>";
			pg_query($con, "rollback");
		}

		$strsql = "";
		while($row = pg_fetch_assoc($res) ) {
			// Loop through actions and create 1 line per action
			// Add ordering for action dates
			// Order is always ARRIVAL, LOADING, OFFLOADING, DEPARTURE
			if($row["ltarrivaldate"] == "t") {
				$strsql .=	"(" . $logid . "," . $row["locid"] . ",'ARRIVAL DATE',1),";
			}
			if($row["ltloadingdate"] == "t") {
				$strsql .=	"(" . $logid . "," . $row["locid"] . ",'LOADING DATE',2),";
			}
			if($row["ltoffloadingdate"] == "t") {
				$strsql .=	"(" . $logid . "," . $row["locid"] . ",'OFFLOADING DATE',3),";
			}
			if($row["ltdeparturedate"] == "t") {
				$strsql .=	"(" . $logid . "," . $row["locid"] . ",'DEPARTURE DATE',4),";
			}
		}
		$strsql = substr($strsql, 0, strlen($strsql) - 1);

		$strsql = "insert into trip_log_det (tldtripid,tldlocation,tldaction,tldorder) values " . $strsql;

		// Insert planned trip into trip_log_det
		$res = pg_query($con, $strsql);

		if(pg_result_error($res) != "") {
			pg_query($con, "delete from trip_logs where tlid=" . $logid);
			// Display error
			echo pg_result_error($res);
			pg_query($con, "rollback;");
		} else {
			// Insert the fixed expenses template into the trip_log_expenses table
			$strsql = "insert into trip_log_expenses (tletripid,tleetid,tlevalue) select " . $logid. ", tceexpense,tcefixedvalue from trip_config_expenses where tcetripid=" . $_REQUEST["tltripconfig_id"];
			pg_query($con, $strsql);

			if(pg_result_error($res) != "") {
				pg_query($con, "delete from trip_logs where tlid=" . $logid);
				// Display error
				echo pg_result_error($res);
				pg_query($con, "rollback;");
			} else {
				pg_query($con, "commit");
				echo $logid;
			}
		}
	}
}
?>