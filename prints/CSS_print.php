<?php
header("Content-type: text/css");
include "../inc/session_test.php";
include "connection.inc";
?>
/* Google Fonts  */
@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700,300,400italic,700italic,600,600italic);

@page {
  size: A4;
  margin: 0;
}

/*@media print { */
 body, html {
		width: 210mm;
		height: 295mm;
		margin: 10px;
		font-family: 'Open Sans', sans-serif;
		font-size: 11px;
	}

	textarea {
		width: 100%;
		height: 15mm;
		border-color: gray;
		border-style: solid;
		border-width: 1px;
	}

	.checkboxholder {
		border: 1px solid black;
		width: 70px;
		height: 18px;
		line-height: 18px;
		vertical-align: middle;
		padding: 1px;
	}

	.checkbox {
		border: 1px solid black;
		height: 14px;
		width: 14px;
		line-height: 14px;
		float: left;
	}

	.logo {
		float: left;
	}

	.leftborder {
		border-left-color: lightgray;
		border-left-style: dashed;
		border-left-width: 1px;
	}

	table {
		position: relative;
		font-family: 'Open Sans', sans-serif;
		font-size: 11px;
		width: calc(100% - 20px);
	}

	tr.border td {
		border-bottom: 1px dashed lightgray;
	}

	table td {
		vertical-align: top;
	}

	.bold {
		font-weight: bold;
	}

	.underline {
		text-decoration: underline;
	}

	.centered {
		text-align: center;
	}

	.right {
		text-align: right;
	}

	.labelheader {
		font-size: 12px;
	}

	.valign {
		vertical-align: top;
	}

	.lalign {
		vertical-align: bottom;
	}

	tr.borderbottomblack td {
		border-bottom-color: black;
		border-bottom-style: solid;
		border-bottom-width: 1px;
	}

	tr.borderbottomgray td {
		border-bottom-color: lightgray;
		border-bottom-style: dashed;
		border-bottom-width: 1px;
	}


	.header {
		border-bottom-color: <?php echo MAINCOLOR?>;
		border-bottom-style: solid;
		border-bottom-width: 2px;
		width:100%;
		text-align: center;
		line-height: 30px;
		font-size: 16px;
		font-weight: bold;
	}

	.headerinfo {
		width:100%;
		border-bottom-color: lightgray;
		border-bottom-style: solid;
		border-bottom-width: 1px;
	}

	.equipmentlist {
		width:100%;
	}

	.signatures {
		margin-bottom: 20px;
		position: absolute;
		bottom: 0px;
		width:100%;
	}
/*}*/