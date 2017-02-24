<?php
include "../../../inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	$(".dp").datepicker();
})
</script>
</head>
<body>

<?php
$res = pg_query($con, "select tlid, tlinvoiceno, to_char(tlinvoicedate, 'dd/mm/yyyy') as tlinvoicedate, tlinvoiceno_ret, to_char(tlinvoicedate_ret, 'dd/mm/yyyy') as tlinvoicedate_ret from trip_log where tlid=" . $_REQUEST["id"]);
$row = pg_fetch_assoc($res, 0);
echo
"
<table class='tbllistnoborder' align='center' style='width: 50%'>
<tr>
<td colspan='2' class='bold underline'>
Set Invoice number for this trip:
</td>
</tr>
<tr>
<td>Main Invoice Number</td><td>" .
uinput("tlinvoiceno", $row["tlid"], $row["tlinvoiceno"], "trip_log", "tlinvoiceno", "tlid", $row["tlid"], false,true,100,"centered",null,false,false,true,false) .
"</td>
</tr>
<tr>
<td>Main Invoice Date</td><td>" .
uinput("tlinvoicedate", $row["tlid"], $row["tlinvoicedate"], "trip_log", "tlinvoicedate", "tlid", $row["tlid"], false,false,100,"dp centered ",null,false,true,true,false) .
"</td>
</tr>
<tr>
<td colspan=\"2\">
&nbsp;
</td>
</tr>
<tr>
<td>Return Invoice Number</td><td>" .
uinput("tlinvoiceno_ret", $row["tlid"], $row["tlinvoiceno_ret"], "trip_log", "tlinvoiceno_ret", "tlid", $row["tlid"], false,true,100,"centered",null,false,false,true,false) .
"</td>
</tr>
<tr>
<td>Return Invoice Date</td><td>" .
uinput("tlinvoicedate_ret", $row["tlid"], $row["tlinvoicedate_ret"], "trip_log", "tlinvoicedate_ret", "tlid", $row["tlid"], false,false,100,"dp centered ",null,false,true,true,false) .
"</td>
</tr>
</table>
";
?>
</body>
</html>