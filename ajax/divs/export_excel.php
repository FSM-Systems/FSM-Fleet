<?php
include "../../inc/session_test.php";
include "basemessages.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	// List all table headers as choice
	$(".searchtbl").find("tr:first").find("th").each(function () {
		if ($(this).text() != "" && $(this).attr("db") ) {
			$('#excelfields > tbody:last-child').before('<tr><td><input name="excelfields[]" type="checkbox" id="chk_' + $(this).attr("db") + '" value="' + $(this).attr("db") + '"><label for="chk_' + $(this).attr("db") + '"> ' + $(this).text() + '</label></td></tr>');
		}
	});

	$(".searchtbl").find("td.excelid").each(function () {
		//console.log($(this).css('display') )
		if ($(this).closest("tr").css('display') !== "none") {
			//console.log($(this).text());
			var val = $('#ids').val() + $(this).text() + ",";
			$('#ids').val(val);
		}
	});

	$("#export").click(function (e) {
		e.preventDefault();
		if ($("input[name='excelfields[]']:checked").length > 0 ) {
			var form_data = $(this).closest('form').serialize();
			form_data['ajax'] = 1;
			$.ajax({
				url: "ajax/export_excel.php",
				method: "POST",
				data: form_data,
				success: function (data) {
					if (data.substring(0,5) != "OK§§§") {
						$.alert('THERE HAS BEEN AN ERROR EXPORTING TO EXCEL. PLEASE TRY AGAIN!' + data);
					} else {
						var files = data.split('§§§')
						var excfilename = files[1];
						$("#download").attr("src", "inc/serve_file_for_download.php?filedir=/tmp/&filename=" + excfilename);
						$("#newitem").fadeOut();
					}
				}
			})
		} else {
			$.alert('YOU HAVE TO SELECT AT LEAST ONE ITEM TO EXPORT.')
		}
	})

	$("#chkall").click(function () {
		if ($(this).prop("checked") == true) {
			$("input[name='excelfields[]']").prop("checked", true);
		} else {
			$("input[name='excelfields[]']").prop("checked", false);
		}
	});
})
</script>
</head>
<body>
<button onclick="$('#newitem').fadeOut();" class="closebutton">X</button>
Select fields you want to export:<br>
<form name="excelfields" id="excelfields">
	<input type="hidden" name="table" id="table" value="<?php echo $_REQUEST["exceltable"]; ?>">
	<input type="hidden" name="searchid" id="searchid" value="<?php echo $_REQUEST["excelsearchid"]; ?>">
	<input type="hidden" name="ids" id="ids" value="">
	<input type="hidden" name="token" value="<?php echo $_SESSION["atoken"]; ?>">
	<table id="excelfields" class="tbllistnoborder">
		<tr><td><input type="checkbox" name="chkall" id="chkall"><label for="chkall" style="font-weight: bold"> SELECT ALL FIELDS</label></tr>
		<tbody>
			<tr><td class="centered"><button id="export">EXPORT TO FILE</button></td></tr>
		</tbody>
	</table>
</form>
<iframe id="download" style="display: none"></iframe>
</body>
</html>