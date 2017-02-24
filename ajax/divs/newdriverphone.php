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
	});

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

	$('.rowdrvphone').each(function() {
	    $(this).rules('add', {
	        required: true,
	        phoneTZ: true,
	    });
	});

	$(".rowdrvphone").change(function () {
		if ($(this).valid() == false) {
			$(this).focus().select();
		} else {
			$(this).removeClass("error");
			updatevalue("driver_phones", "dpphoneno","'" + $(this).val() + "'", "dpid", $(this).attr("id").replace("dpphoneno_","").replace("_id",""), false, true, this, false,false);
			$("#workspace").load("drivers.php");
		}
	})

	$(".tel").click(function (e) {
		e.preventDefault();
	})

	$("#add").click(function (e) {
		e.preventDefault();
		if ($("#newfrm").valid() == true) {
			$.ajax({
				url: "ajax/inserts/newdriverphone.php",
				type: "POST",
				data: {
					dpphoneno: $("#number").val(),
					did: <?php echo $_REQUEST['did']; ?>,
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					if ($.isNumeric(data)) {
						//$.alert('NEW CUSTOMER CREATED: ID ' + data);
						$("#newitem").load("ajax/divs/newdriverphone.php", {did: <?php echo $_REQUEST['did']; ?>});
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
$res = pg_query($con, "select dname from drivers where did=" . $_REQUEST["did"]);
$res2 = pg_query($con, "select dpid,dpphoneno from driver_phones where dpdid=" . $_REQUEST["did"] . " order by dpid");
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
				<td>" . uinput("dpphoneno", $row["dpid"], $row["dpphoneno"], "driver_phones", "dpphoneno", "dpid", $row["dpid"], false,false,300,"rowdrvphone"," style='width: 100%'",null,false,false,false) . "</td>
				<td class='smallbtn'>" . delbtn("driver_phones", "dpid", $row["dpid"], "ajax/divs/newdriverphone.php?did=" . $_REQUEST["did"], "tel", "#newitem")  . "</td>";
			}
		}
		?>
		</tr>
		<tr>
			<td style="text-align: right">Number:</td>
			<td><input type="text" name="number" id="number" style="width: 300px" autocomplete=""></td>
			<td style="width: 10px;"><button id="add" disabled="true">ADD</button></td>
		</tr>
	</table>
</form>
<div style="text-align: center; width: 100%">
<input type="hidden" name="did" id="did" value="<?php echo $_REQUEST['did']; ?>">
</div>
</body>
</html>