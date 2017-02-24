<?php
include "../../inc/session_test.php";
include "connection.inc";
include "basemessages.php";
?>
<!DOCTYPE html>
<html>
<head>
<style type="text/css">
textarea {
	width: 250px;
	height: 100px;
}
</style>
<script src="inc/container_check.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	$(".notes").change(function () {
		updatevalue("quotations", $(this).attr("id"), $(this).val(), "qid",<?php echo $_REQUEST["qid"]; ?> , false, false, this, false,true);
	});

		// Enable send button once emails have been checked
	$(document).on("change", ".email_list", function () {
		if ($(".email_list:checked").length != 0) {
			$("#btnresend").prop("disabled", false);
		} else {
			$("#btnresend").prop("disabled", true);
		}
	})

	$("#btnresend").click(function (event) {
		$("#btnresend").prop("disabled", true);
		event.preventDefault();
		if ($(".email_list:checked").length==0) {
			$.alert('PLEASE SELECT AT LEAST ONE EMAIL ADDRESS!');
		} else {
			var form_data = $(this).closest('form').serialize();
    		form_data['ajax'] = 1;
			$.ajax({
				url: "ajax/inserts/newquotation.php",
				type: "POST",
				data: form_data,
				success: function (data) {
					switch(data) {
						case "NOEMAIL":
							$.alert('YOUR ACCOUNT DOES NOT HAVE AN EMAIL SET.<br>PLEASE SET IT FROM USER ADMINISTRATION->Email Address<br>RELOGIN INTO DMS AND TRY AGAIN.');
							break;
						case " MAILOK":
							$.alert(data);
							$("#newitem").fadeOut();
							break;
					}
				},
				error: function (data) {
					$.alert('<?php echo AJAXERROR; ?>');
					$("#btnresend").prop("disabled", false);
				},
			})
		}
	});
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<?php
$reseml = pg_query( $con, "select ceemail as eml from customer_emails where cecid in (select qclient from quotations where qid=" . $_REQUEST["qid"] . ") union select lemail as eml from login where lemail is not null");
$res = pg_query($con, "select qid, qnotes, qnotesinternal, cname, ceemail from quotations left join customers on qclient=cid left join customer_emails on cid=cecid where qid=" . $_REQUEST["qid"]);
$notes = pg_fetch_assoc($res, 0);
?>
<form name="frmresend" id="frmresend">
<label style="font-weight: bold; text-decoraton: underline">Quotation <?php echo $notes["qid"] . " - " . $notes["cname"]; ?></label><br>
Notes for Customer:<br>
<textarea id="qnotes" class="notes"><?php echo $notes["qnotes"]?></textarea><br><br>
Notes (internal):<br>
<textarea id="qnotesinternal" class="notes"><?php echo $notes["qnotesinternal"]?></textarea><br>
<table style="text-align: left">
<?php
while($row = pg_fetch_assoc($reseml)) {
	echo "<tr><td><input type='checkbox' class='email_list' name='email_list[]' id='" . $row["eml"] . "' value='" . $row["eml"] . "'><label for='" . $row["eml"] . "'>" . $row["eml"] . "</label></td></tr>";
}
?>
</table>
<input type="hidden" name="resend" value="1">
<input type="hidden" name="save" value="false">
<input type="hidden" name="qid" value="<?php echo $_REQUEST["qid"]; ?>">
<input type="hidden" name="token" id="token" value="<?php echo $_SESSION["atoken"]; ?>">
<button id="btnresend" disabled="true">RESEND THIS QUOTATION</button>
</form>
</body>
</html>