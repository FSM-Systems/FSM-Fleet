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
	$("#number").keyup(function () {
		if ($(this).val() != "") {
			// active add
			$("#add").prop("disabled", false).addClass("disabled");
		} else {
			$("#add").prop("disabled", true).removeClass("disabled");
		}
	})

	// Avoid firing form
	$(".tel").click(function (e) {
		e.preventDefault();
	})

	$("#newfrm").validate({
		// No error message
		errorPlacement: function (error, element) {
			$(element).prop("title", $(error).text())
		},
		rules: {
			number: {
				required: {
					depends: function(){
						$(this).val($.trim($(this).val()));
						return true;
        			}
        		},
				phoneTZ: true,
				minlength: 13,
			}
		},
	});

	$('.rowcustphone').each(function() {
	    $(this).rules('add', {
	        required: true,
	        phoneTZ: true,
	    });
	});

	$("#add").click(function (e) {
		e.preventDefault();
		if ($("#newfrm").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newcustomerphone.php",
				type: "POST",
				data: {
					cpphoneno: $("#number").val(),
					cid: <?php echo $_REQUEST['cid']; ?>,
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						//$.alert('NEW CUSTOMER CREATED: ID ' + data);
						$("#newitem").load("ajax/divs/newcustomerphone.php", {cid: <?php echo $_REQUEST['cid']; ?>});
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
$res2 = pg_query($con, "select cpid,cpphoneno from customer_phones where cpcid=" . $_REQUEST["cid"] . " order by cpid");
?>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
<form id="newfrm">
	<table class="tbllistnoborder" border="0" id="numbered">
		<tr>
			<td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline">
				Add phone numbers for <?php echo pg_fetch_result($res, 0, 0); ?>:
			</td>
		</tr>
		<?php
		if(pg_num_rows($res2) > 0) {
			while($row = pg_fetch_assoc($res2)) {
				echo "<tr><td style='text-align: right'></td>
				<td>" . uinput("cpphoneno", $row["cpid"], $row["cpphoneno"], "customer_phones", "cpphoneno", "cpid", $row["cpid"], false,true,300,"rowcustphone"," style='width: 100%'",null,false,true,false) . "</td>
				<td class='smallbtn'>" . delbtn("customer_phones", "cpid", $row["cpid"], "ajax/divs/newcustomerphone.php?cid=" . $_REQUEST["cid"], "tel", "#newitem")  . "</td>";
			}
		}
		?>
		<tr>
			<td style="text-align: right">Number:</td>
			<td><input type="text" name="number" id="number" style="width: 300px" autocomplete=""></td>
			<td style="width: 10px;"><button id="add" disabled="true">ADD</button></td>
		</tr>
	</table>
</form>
<div style="text-align: center; width: 100%">
<input type="hidden" name="cid" id="cid" value="<?php echo $_REQUEST['cid']; ?>">
</div>
</body>
</html>