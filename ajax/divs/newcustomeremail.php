<?php
include "../../inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	$("#ceemail").keyup(function () {
		if ($(this).val() != "") {
			// active add
			$("#add").prop("disabled", false).addClass("disabled");
		} else {
			$("#add").prop("disabled", true).removeClass("disabled");
		}
	})

	$("#newfrm").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		},
		rules: {
			ceemail: {
				required: true,
				email: true,
			}
		}
	});

	$('.rowcustemail').each(function() {
	    $(this).rules('add', {
	        required: true,
	        email: true,
	    });
	});

	// Remove white space in email and activate button if ok
	$("#ceemail").change(function () {
		$("#ceemail").val($("#ceemail").val().replace(" " ,"g"));
		if ($(this).valid() == true) {
			$("#add").prop("disabled", false).removeClass("disabled");
		} else {
			$("#add").prop("disabled", true).addClass("disabled");
		}
	})

	$(".rowcustemail").change(function () {
		if ($(this).valid() == false) {
			$(this).focus();
		} else {
			$(this).removeClass("error");
			updatevalue("customer_emails", "ceemail","'" + $(this).val() + "'", "ceid", $(this).attr("id").replace("cephoneno_","").replace("_id",""), false, true, this, false,false);
			$("#workspace").load("customers.php");
		}
	})

	$(".eml").click(function (e) {
		e.preventDefault();
	})

	$("#add").click(function (e) {
		e.preventDefault();
		if ($("#newfrm").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newcustomeremail.php",
				type: "POST",
				data: {
					ceemail: $("#ceemail").val(),
					cid: <?php echo $_REQUEST['cid']; ?>,
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						//$.alert('NEW CUSTOMER CREATED: ID ' + data);
						$("#newitem").load("ajax/divs/newcustomeremail.php", {cid: <?php echo $_REQUEST['cid']; ?>});
					} else {
						$.alert('<?php echo QUERYERROR; ?>' + data);
					}
				},
				error: function (data) {
					$.alert('<?php echo AJAXERROR; ?>');
				},
			})
		}
	})

	tablerows();
})
</script>
<?php
$res = pg_query($con, "select cname from customers where cid=" . $_REQUEST["cid"]);
$res2 = pg_query($con, "select ceid,ceemail from customer_emails where cecid=" . $_REQUEST["cid"] . " order by ceid");
?>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<form id="newfrm" name="newfrm">
	<table class="tbllistnoborder" border="0" id="numbered">
		<tr>
			<td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline">
				Add email addresses for <?php echo pg_fetch_result($res, 0, 0); ?>:
			</td>
		</tr>
			<?php
			if(pg_num_rows($res2) > 0) {
				while($row = pg_fetch_assoc($res2)) {
					echo "<tr><td style='text-align: right'></td>
					<td>" . uinput("cephoneno", $row["ceid"], $row["ceemail"], "customer_emails", "ceemail", "ceid", $row["ceid"], false,false,300,"rowcustemail"," style='width: 100%'",null, false,false,false) . "</td>
					<td class='smallbtn'>" . delbtn("customer_emails", "ceid", $row["ceid"], "ajax/divs/newcustomeremail.php?cid=" . $_REQUEST["cid"], "eml", "#newitem")  . "</td>";
				}
			}
			?>
			<tr>
				<td style="text-align: right">Email:</td>
				<td><input type="text" name="ceemail" id="ceemail" style="width: 300px" autocomplete=""></td>
				<td style="width: 10px;"><button id="add" type="button">ADD</button></td>
			</tr>
	</table>
</form>
<div style="text-align: center; width: 300px">
<input type="hidden" name="cid" id="cid" value="<?php echo $_REQUEST['cid']; ?>">
</div>
</body>
</html>