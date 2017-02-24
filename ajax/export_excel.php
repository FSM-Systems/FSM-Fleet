<?php
include "../inc/session_test.php";
include "connection.inc";

// Build query string and order in SQL by first column
$join = ''; // USed only when we are requesting to exprt trailer number plate
$qry = "select ";

foreach($_POST["excelfields"] as $field) {
	// If we are requesting a trailer then modify query to join with trailers table to get trailer number plate
	if($field == "ttrailer") {
		$qry .= "trnumberplate , ";
		$join = ' left join trailers on ttrailer=trid ';
	} else {
		$qry .= $field . ", ";
	}
}

$qry = substr($qry, 0, strlen($qry) - 2);

$qry .= " from " . $_POST["table"] . $join . " where " . $_POST["searchid"] . " in (" . substr($_POST["ids"], 0, strlen($_POST["ids"]) - 1 ) . ")  order by " . $_POST["excelfields"][0] . ";";

$res = pg_query($con, $qry);

if(pg_result_error($res) != "") {
	echo pg_result_error($res);
} else {
	$rowcount = 2;
	// get array woth data
	//$arr = pg_fetch_assoc($res);

	// Export to excel
	require_once '../inc/phpexcel/PHPExcel.php';
	$objPHPExcel = new PHPExcel();

	// Headers
	$objPHPExcel->setActiveSheetIndex(0);
	for($col = 0; $col < pg_num_fields($res); $col++) {
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, pg_field_name($res, $col));
	}

	while($row = pg_fetch_row($res)) {
		for($col = 0; $col < count($row); $col++) {
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $rowcount, $row[$col]);
		}
		$rowcount++;
	}

	// Autosize all columns
	foreach(range('A','Z') as $columnID) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
	}

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save("/tmp/" . $_POST["table"] . "_" . date("d-m-Y") . ".xlsx");
	echo "OK§§§". $_POST["table"] . "_" . date("d-m-Y") . ".xlsx"; // output confirmation and filename
}
?>