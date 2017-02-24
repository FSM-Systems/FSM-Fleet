<?php
include "../inc/session_test.php";
include "connection.inc";
include "ajax_security.php";

switch($_REQUEST["itemtype"]) {
	case "driver":
		$res = pg_query( $con, "select dname,dpphoneno from drivers left join driver_phones on did=dpdid where did=" . $_REQUEST['itemid']);
		if(pg_result_error($res) != "") {
			echo pg_result_error($res);
		} else {
			$ret = pg_fetch_result($res, 0, 0) . "<br><br>";
			/// Check if we have only 1 row and no phone. This means no data..
			if(pg_fetch_result($res, 0, 1) == "") {
				$ret .= "No phone numbers added for this driver yet.";
				echo $ret;
			} else {
				while($row = pg_fetch_assoc($res)) {
					$ret .= $row["dpphoneno"] . "<br><br>";
				}
				echo substr($ret, 0, strlen($ret) - 8);
			}
		}
		break;
	case "driverstatus":
		$res = pg_query( $con, "select dname,dpphoneno from drivers left join driver_phones on did=dpdid where did=" . $_REQUEST['itemid']);
		if(pg_result_error($res) != "") {
			echo pg_result_error($res);
		} else {
			$ret = pg_fetch_result($res, 0, 0) . "<br><br>";
			/// Check if we have only 1 row and no phone. This means no data..
			if(pg_fetch_result($res, 0, 1) == "") {
				$ret .= "No phone numbers added for this driver yet.";
				echo $ret;
			} else {
				while($row = pg_fetch_assoc($res)) {
					$ret .= $row["dpphoneno"] . "<br><br>";
				}
				// Get trip of driver
				$restrip = pg_query($con, "select tcdescription from trip_log left join trip_config on tltripconfig=tcid where tlclosed=false and tldriver=" . $_REQUEST['itemid']);
				if(pg_num_rows($restrip) > 0) {
					$ret .= "Currently on: " . pg_fetch_result($restrip, 0, 0) . " Route<br><br>";
				}
				echo substr($ret, 0, strlen($ret) - 8);
			}
		}
		break;
	case "customer":
		$res = pg_query( $con, "select cname,cpphoneno from customers left join customer_phones on cid=cpcid where cid=" . $_REQUEST['itemid']);
		if(pg_result_error($res) != "") {
			echo pg_result_error($res);
		} else {
			$ret = pg_fetch_result($res, 0, 0) . "<br><br>";
			if(pg_fetch_result($res, 0, 1) == "") {
				$ret .= "No phone numbers added for this customer yet.<br><br>";
			} else {
				while($row = pg_fetch_assoc($res)) {
					$ret .= $row["cpphoneno"] . "<br><br>";
				}
			}
			// For customers add also email with link
			$reseml = pg_query($con, "select ceemail from customer_emails where  cecid=" . $_REQUEST['itemid']);
			$eml ="";
			if(pg_num_rows($reseml) > 0) {
				while($rowe = pg_fetch_assoc($reseml)) {
					$ret .= "<a href='mailto:" . $rowe["ceemail"] . "'>" . $rowe["ceemail"] . "</a><br><br>";
					$eml .= $rowe["ceemail"] . ",";
				}
				// Add all emails as EMAIL ALL link
				$ret .= "<a href='mailto:" . substr($eml, 0, strlen($eml) - 1) . "'>EMAIL TO ALL</a><br><br>";
			} else {
				$ret .= "No email addresses added for this customer yet.<br><br>";
			}
			echo substr($ret, 0, strlen($ret) - 8);
		}
		break;
	case "cargo":
		$res = pg_query( $con, "select tlcargo from trip_log where tlid=" . $_REQUEST['itemid']);
		if(pg_result_error($res) != "") {
			echo pg_result_error($res);
		} else {
			if(pg_fetch_result($res, 0, 0) != "") {
				echo nl2br(pg_fetch_result($res, 0, 0));
			} else {
				echo "No cargo description set.";
			}
		}
		break;
	case "cargoret":
		$res = pg_query( $con, "select tlcargo_ret from trip_log where tlid=" . $_REQUEST['itemid']);
		if(pg_result_error($res) != "") {
			echo pg_result_error($res);
		} else {
			if(pg_fetch_result($res, 0, 0) != "") {
				echo nl2br(pg_fetch_result($res, 0, 0));
			} else {
				echo "No return cargo description set.";
			}
		}
		break;
}
?>