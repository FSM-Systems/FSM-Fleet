<?php
include "../inc/session_test.php";
include "connection.inc";

// Fetch current trips trip_log
$tripid = $_REQUEST["tripid"];

$res = pg_query($con, "select tldid,tldlocation,tldaction,tldactiondate,tldorder from trip_log_det where tldtripid=" . $tripid . " order by tldid, tldorder");
$arr_current_trip = pg_fetch_all($res); // old trip config array

$res2 = pg_query($con, "select locid, ltdeparturedate, ltarrivaldate, ltloadingdate, ltoffloadingdate from locations left join location_types on loctype=ltid");
$arr_locations = pg_fetch_all($res2);

$currlocation = "";
$strinsert = "begin; "; // insert for query, start with a transaction for data consistency

$triplogdetid = "";

// Delete current trip data
$strinsert .= "delete from trip_log_det where tldtripid=" . $tripid . ";";

foreach($_REQUEST as $key => $x) {
	// Print all route steps for debugging purposes
	//echo $key . ": " . $x . "<br>";
	// If we have location store it
	if(strpos($key,"tldid_loc_") !== false) {
		$currlocation = $x;	
	}
	
	// If we have an action then start to build query string with new inserts
	// Check if this location already has a value from previous trip and add it
	// Check what actions we have and add them in the new trip if it is a new location (loading offloading etc)
	if(strpos($key,"tldid_action_") !== false) {
		// Lets see if we have a trip log det id. (IN trip routes page when a location is added, we add also a bogus trip_log_det id so that we know it is a new location)
		// If we have it, copy data from current trip array
		// If not, take from locations abd build new row with corresponding actions
		$triplogdetid = str_replace("tldid_action_", "", $key);
		if(strpos($triplogdetid, "xxx") === false) { // we are sending xxx from tripinfo_routes to identify its a newly added location
			foreach($arr_current_trip as $curr) {
				if($triplogdetid === $curr["tldid"]) {		
					// Foud line in table check if action date is null or not
					if($curr["tldactiondate"] != "") {
						$strinsert .= "insert into trip_log_det (tldtripid,tldlocation,tldaction,tldactiondate,tldorder) values (" . $tripid . "," . $curr["tldlocation"] . ",'" . $curr["tldaction"] ."','" . $curr["tldactiondate"] ."'," . $curr["tldorder"] . ");";
						// Action inserted exit and restart loop
						break;
					} else {
						// Action inserted exit and restart loop
						$strinsert .= "insert into trip_log_det (tldtripid,tldlocation,tldaction,tldactiondate,tldorder) values (" . $tripid . "," . $curr["tldlocation"] . ",'" . $curr["tldaction"] ."',null, " . $curr["tldorder"] . ");";
						break;		
					}
				}
			}
		} else {
			// Create a new row with new inserted location
			// Fetch data from location array
			foreach($arr_locations as $loc)	{
				if($currlocation === $loc["locid"]) {
					// Check what action dates we have to insert (bool true) and add them (arrival, loading, offloading, departure) with null value
					if($loc["ltarrivaldate"] == "t") {
						$strinsert .= "insert into trip_log_det (tldtripid,tldlocation,tldaction,tldactiondate,tldorder) values (" . $tripid . "," . $currlocation . ",'ARRIVAL DATE',null,1);";				
					}
					if($loc["ltloadingdate"] == "t") {
						$strinsert .= "insert into trip_log_det (tldtripid,tldlocation,tldaction,tldactiondate,tldorder) values (" . $tripid . "," . $currlocation . ",'LOADING DATE',null,2);";				
					}
					if($loc["ltoffloadingdate"] == "t") {
						$strinsert .= "insert into trip_log_det (tldtripid,tldlocation,tldaction,tldactiondate,tldorder) values (" . $tripid . "," . $currlocation . ",'OFFLOADING DATE',null,3); ";				
					}
					if($loc["ltdeparturedate"] == "t") {
						$strinsert .= "insert into trip_log_det (tldtripid,tldlocation,tldaction,tldactiondate,tldorder) values (" . $tripid . "," . $currlocation . ",'DEPARTURE DATE',null,4);";				
					}
					// Action inserted exit and restart loop
					break;
				}			
			}	
		}
	}
}

$strinsert .= "commit;";


// TO UPDATE DATABASE...
$res = pg_query($con, $strinsert);

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
}
?>