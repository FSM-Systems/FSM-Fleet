<?php
include "inc/session_test.php";
include "connection.inc";
include "itemcreators.php";
?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript">
$(document).ready(function () {
	// Call backup page
	$("#backup").click(function () {
		$.confirm('CREATE A COMPLETE BACKUP OF YOU DMS SYSTEM?', function (answer) {
			if (answer) {
				$.ajax({
					url: "inc/create_backup.php",
					data: {
						token: '<?php echo $_SESSION['atoken']; ?>',
					},
					success: function (data) {
						var astatus = data.split("§§§");
						if (astatus[0] == "SUCCESS") {
							// Serve file for download then refresh page
							$("#download").attr("src", "inc/serve_file_for_download.php?filedir=<?php echo BACKUPDIR; ?>&filename=" + astatus[1]);
							$("#workspace").load("backups.php");
						} else {
							$.alert('THERE HAS BEEN AN ERROR CREATING THE BACKUP! PLEASE TRY AGAIN! \n\n' +data);
						}
					}
				})
			}
		})
	});

	$(".deletebak").click(function () {
		$.confirm('DELETE THIS BACKUP?' , function (answer) {
			if (answer) {
				$.ajax({
					url: "inc/delete_backup.php",
					type: "POST",
					data: {
						fname: $("#fname_" + $(this).attr("id")).text(),
						id: $(this).attr("id"),
						token: "<?php echo $_SESSION["atoken"]; ?>",
					},
					success: function (data) {
						if (!$.isNumeric(data)) {
							$.alert('THERE HAS BEEN AN ERROR DELETING THE BACKUP! PLEASE TRY AGAIN.' + data)
						} else {
							$("#workspace").load("backups.php");
						}
					}
				})
			}
		})
	})
})
</script>
</head>
<body>
<div style="float: right"><button id="backup"><img class="icon" src="icons/backup.png" alt=""> Create DMS Backup</button></div>
<br><br>
<div class="topline">
Create and download a backup of your whole system and data:<br>
<br>
<?php
$res = pg_query($con, "select *, to_char(bdate, 'dd/mm/yyyy hh24:mm:ss') as bdate from backups where company_id=" . $_SESSION["company"] . " order by bid desc");
?>
<table class="tbllist searchtbl" style="text-align: left; width: 600px;" align="left" cellpadding=2 cellspacing=0>
	<thead>
		<tr>
			<th style="width: 150px;">Backup Date</th>
			<th>File Name</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			if(pg_num_rows($res) == 0) {
				echo "<tr><td colspan='3'>No backups available yet.</td></tr>";
			} else {
				while($row = pg_fetch_assoc($res)) {
					$arrfile= explode("/", $row["bfilename"]);
					$name = end($arrfile);
					echo "
					<tr class='tbl'>
						<td>" . $row["bdate"] . "</td>
						<td><a href='backups/" . $name. "' id='fname_" . $row["bid"] . "'>" . $name . "</a></td>
						<td><button id='" . $row["bid"] . "' class='smallbutton deletebak' ><img src='icons/delete.png' class='smallbutton'></button></td>
					</tr>";
				}
			}
		?>
	</tbody>
</table>
</div>
<iframe id="download" style="display: none"></iframe>
</body>
</html>