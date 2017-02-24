<?php
header("Content-type: text/css");
include "../inc/config.inc";
?>
/* Google Fonts  */
@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700,300,400italic,700italic,600,600italic);

/* override ngprogress color */

.bar {
	background: <?php echo PROGRESSBARCOLOR; ?> ! important;
	z-index: 10000 ! important;
}

body {
	font-family: 'Open Sans', sans-serif;
	margin: 0px;
	font-size: <?php echo FONTSIZE; ?>px;
	color: <?php echo TEXTCOLOR?>;
}

label {
	font-family: 'Open Sans', sans-serif;
}

.new, a.amenu {
	color: <?php echo TEXTCOLOR?>;
	text-decoration: none;
}

.new:hover, a.amenu:hover {
	color: tomato;
	text-decoration: underline;
	cursor: pointer;
	font-weight: bold;
}

.credits {
	position: absolute;
	bottom: 0;
	right: 0;
	padding: 10px;
}

.social {
	position: absolute;
	left: 0;
	bottom: 0;
	padding: 10px;
}

.credits >a > img, .social > a > img {
	height: 30px;
}

.dropdown {
	background-image: url(../icons/arrow-down.png);
	background-position: right center;
	background-repeat: no-repeat;
	background-color: white;
}


.detail td {
	vertical-align: top;
}

.highlighted {
	background-color: yellow;
}

ul {
	line-height: 200%;
}

.ui-tabs-nav, .ui-tabs-active, .ui-tabs-panel {
	font-size: <?php echo FONTSIZE; ?>px;
	font-family: 'Open Sans', sans-serif;
}
.ui-tabs {
	min-height: 200px;
	max-height: 400px;
	overflow-y: scroll;
}

.ui-tabs .ui-tabs-nav li a:hover {
	font-size: <?php echo FONTSIZE; ?>px;
	font-family: 'Open Sans', sans-serif;
}

.ui-autocomplete {
	position: absolute;
	z-index: 250;
	font-size: <?php echo FONTSIZE; ?>px;
	max-height: 200px;
	overflow-y: auto;
	/* prevent horizontal scrollbar */
	overflow-x: hidden;
	min-width: 100px;
}

.ui-tooltip {
	font-size: <?php echo FONTSIZE; ?>px;
}

.ui-datepicker {
	font-size: <?php echo FONTSIZE; ?>px;
}

.head {
	position: absolute;
	top: 0px;
	left: 0px;
	width: 100%;
	height: 24px;
	line-height: 24px;
	border-bottom: 2px solid <?php echo MAINCOLOR?>;
	z-index: 100;
	background-color: <?php echo HEADERCOLOR?>;
}

.title {
	position: absolute;
	left: 0px;
	width: 100%;
	vertical-align: middle;
	line-height: 24px;
	text-align: center;
	font-family: 'Open Sans Semi-Bold', sans-serif;
	font-size: 14px;
	z-index: 101;
	color: <?php echo HEADERFONTCOLOR; ?>;
}

.logout {
	position: absolute;
	right: 0px;
	width: 100px;
	vertical-align: middle;
	line-height: 24px;
	text-align: right;
	font-family: 'Open Sans Semi-Bold', sans-serif;
	color: #424b54;
	font-size: <?php echo FONTSIZE; ?>px;
	padding-right: 5px;
	z-index: 102;
}


.main {
	position: absolute;
	top: 26px;
	height: calc(100% - 32px);
	width: 100%;
	vertical-align: top;
	/*background-image: url("/img/truck.png");
	background-position:  bottom left;
	background-repeat: no-repeat;*/
}

.menu {
	float: left;
	position: relative;
	width: 180px;
	top: 0px;
	height: calc(100% - 15px);
	padding-left: 0px;
	border-right: 1px dashed <?php echo MAINCOLOR?>;
	text-align: left;
	vertical-align: top;
}

.menu  tr  td {
	border-bottom-style: solid;
	border-bottom-width: 1px;
	border-bottom-color: lightgray;
	cursor: pointer;
	padding: 4px;
	text-align: center;
}

.menu  tr:not(.none)  td:hover {
	background-color: tomato;
}

.maincp {
	position: absolute;
	height: calc(100% - 10px);
	width: calc(100% - 20px);
	min-width: 960px;
}

.leftpanel {
	position: relative;
	display: inline;
	height: 100%;
	width: calc(100% - 150px);
	float: left;
}

.rightpanel {
	position: relative;
	display: inline;
	top: -2px;
	width: 120px;
	height: 100%;
	padding-left: 10px;
	border-left: 1px dashed <?php echo MAINCOLOR?>;
	float: right;
}

.sort tbody tr:hover {
	cursor: pointer;
	background-color: tomato;
}

.tblcp {
	position: relative;
	font-size: <?php echo FONTSIZE; ?>px;
	width: 100%;
}

.tblcp td {
	vertical-align: top;
}

.tblcp tr td {
	border-bottom: 1px solid lightgray;
}

.tblcporange  {
	color: orange;
	cursor: pointer;
}

.tblcporange:hover {
	text-decoration: underline;
}

.tblcpred  {
	color: red;
	cursor: pointer;
}

.tblcpred:hover {
	text-decoration: underline;
}

.tblcpblue  {
	color: blue;
	cursor: pointer;
}

.tblcpblue:hover {
	text-decoration: underline;
}

.workspace {
	position: absolute;
	width: calc(100% - 235px);
	height: calc(100% - 15px);
	line-height: 100%;
	padding-top: 5px;
	padding-bottom: 5px;
	padding-left: 10px;
	padding-right: 10px;
	right: 0px;
	overflow-y: auto;
}

.topline {
	border-top: 1px solid lightgray;
	padding-top: 4px;
	margin-top: 10px;
}

.login {
	width: 500px;
	height: 50px;
	line-height: 50px;
	vertical-align: middle;
	text-align: center;
	top: 45%;
	position: relative;
	margin: 0 auto;
	border: 1px solid lightgrey;
	border-radius: 5px;
	/* overflow: auto; */
}

.printitem {
	display: none;
}

.newitem {
	display: none;
	border: 1px solid black;
	min-width: 100px;
	border-radius: 5px;
	padding: 10px;
	background-color: white;
	z-index: 150;
	position: absolute;
	top: 10%;
	left: 20%;
	vertical-align: top;
	margin: 0 auto;
	text-align: center;
	min-height: 10px;
}

.videohelp {
	width: 420;
	height: 345;
	display: none;
}

.tblhistory {
	width: 100%;
}

.tblhistory td {
	font-size: <?php echo FONTSIZE; ?>px;
}

.tblhistory th {
	font-size: <?php echo FONTSIZE; ?>px;
}

.tbltripstep {
	width: 100%;
	text-align: left;
}

.tbltripstep td {
	font-size: <?php echo FONTSIZE; ?>px;
}

th {
	text-align: left;
	text-decoration: underline;
}

/* tblcp tr:hover td {
	background-color: tomato;
	color: gray;
	cursor: pointer;
} */

td.delbtn {
	width: 10px;
	text-decoration: none;
}

tr.tbl td {
	border-bottom-color: gray;
	border-bottom-style: dashed;
	border-bottom-width: 1px;
}

tr.tbl:nth-child(even) {
	background-color: <?php echo ALTROWCOLOR?>;
}

.tbllist {
	padding-top: 10px;
	font-size: <?php echo FONTSIZE; ?>px;
	width: 95%;
}

.tbllistnoborder {
	font-size: <?php echo FONTSIZE; ?>px;
	width: 100%;
	margin: 0 auto;
	padding: 0px;
	text-align: left;
 }

 /* Border on every row except last. This is good for data display tables */
 .bottomborder tr:not(:last-child) td {
	border-bottom: 1px dashed lightgray;
 }

 .bottomborderstrong tr:not(:last-child) td {
	border-bottom: 1px solid black;
 }

.tbllist tr:last-child td {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: gray;
}

tr.tbl:not(:first-child):hover {
	background-color: <?php echo MOUSEOVERCOLOR?>;
	cursor: pointer;
}

tr.tbl:not(:first-child) {
	border-bottom: 1px dashed black;
}

.headtr td {
	font-size: <?php echo FONTSIZE; ?>px;
	vertical-align: top;
}

img {
	vertical-align: middle;
	cursor: pointer;
}

.icon {
	width: 20px;
}

.logo {
	//opacity: 0.4;
	position: absolute;
	vertical-align: bottom;
	height: 24px;
	z-index: 10000;
}

.logo:hover {
	//opacity: 1;
}

label.username {
	padding-left: 85px;
	font-size: <?php echo FONTSIZE; ?>px;
	color: <?php echo HEADERFONTCOLOR; ?>;
}

.closebutton {
	position: absolute;
	display:block;
	padding:2px 5px;
	background:#ccc;
	top: -10px;
	right: -10px;
	height: 20px;
	width: 20px;
	border-radius: 10px;
	background-color: <?php echo CLOSEBUTTONCOLOR?>;
	color: <?php echo CLOSEBUTTONTEXTCOLOR?>;
	border-color: black;
}

.smallbutton {
	font-size: <?php echo FONTSIZE; ?>px;
	width: 16px;
	height: 16px;
	line-height: 16px;
	vertical-align: middle;
	text-align: center;
	padding: 0px;
}

img.smallbutton {
	width: 12px;
	height: 12px;
}

.closebutton:hover {
	background-color: <?php echo CLOSEBUTTONHOVERCOLOR?>;
}

button {
	min-height: 20px;
	vertical-align: middle;
	min-width: 20px;
}

.spinnerbutton {
		background:
		url('/img/ajax-loader.gif')
		no-repeat
		left center;
	}

img {
	vertical-align: middle;
}

.hover:hover {
	cursor: pointer;
	text-decoration: underline;
}

.centered {
	text-align: center;
}

.right {
	text-align: right;
}

.bold {
	font-weight: bold;
}

.underline {
	text-decoration: underline;
}

.hidden {
	display: none;
}

input[type="button"]:hover, button:hover {
	cursor: pointer;
	background-color: <?php echo BUTTONHOVERCOLOR?>;
	border: 1px solid black;
}

input, button, textarea {
	border: 1px solid lightgray;
	background-color: white;
	border-radius: 5px;
	cursor: pointer;
	font-size: <?php echo FONTSIZE; ?>px;
}

input.txtordering {
	width: 20px;
	text-align: center;
}

#search {
	width: 200px;
	height: 15px;
}

.disabled {
	background-color: lightgray;
}

/* jQuery Validation error styling */
input.error, select.error, textarea.error {
	background: #FFC;
	border: 2px solid red;
	top: 0px;
}

.expirydate {
	border-color: orange;
	border-width: 2px;
	background: #fbebc5;
	color: black;
	font-weight: bold;
}

.newitemlabel {
  display: inline-block;
  width: 140px;
  text-align: right;
  vertical-align: top;
}​

.headlabel {
  display: inline-block;
  vertical-align: top;
  font-weight: bold;
  text-decoration: underline;
}​

.map {
	height: 180px;
}