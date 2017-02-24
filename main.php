<?php
include "inc/session_test.php";
require_once "connection.inc";
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo APPTITLE; ?></title>
<link rel="shortcut icon" href="favicon.ico" />
<?php
include "jquery.inc";
include "basemessages.php";
?>
<script src="inc/itemcreators.js.php"></script>
<script type="text/javascript">
$(document).ready(function () {
	// Tooltips
	$( document ).tooltip({track: true,});

	// Close all tips on any click
	$(document).click(function () {
		$('.qtip:visible').qtip('hide');
	})
	// Copy paste
	<?php
	if ($_SESSION['coypaste'] == "f") {
	?>
	$('body').bind("cut copy paste", function(e) {
		$.alert('SORRY, YOU ARE NOT ALLOWED TO COPY AND PASTE!');
		e.preventDefault();
	});
	<?php
	}
	?>

	// Reminder when loggin in
	<?php
	if (isset($_REQUEST["reminder"]) && $_REQUEST["reminder"] == true) {
	?>
	$.ajax({
		url: "ajax/check_reminders.php",
		type: "POST",
		data: {
			token: '<?php echo $_SESSION['atoken']; ?>',
		},
		success: function (data) {
			if (parseInt(data) > 0) {
				// Display expiries
				$("#newitem").load("ajax/divs/expiries.php", function () {
					$(this).draggable().fadeIn();
				})
			}
		}
	})
	<?php
	}
	?>

	// Disable autocomplete
	$("input").attr("autocomplete", "false");

	// Handle menu clicks
	$(".amenu").click(function () {
		$("#newitem").fadeOut();
		<?php
		if ($_SESSION['readonly'] == "t") {
		?>
		$("#workspace").load($(this).attr("id"), function () {
			// Disable all inputs, buttons and the link on the control panel to create a new trip
			$('input, textarea, button, .new').prop('disabled', true)
		});
		<?php
		} else {
		?>
			$("#workspace").load($(this).attr("id"));
		<?php
		}
		?>
		$("#workingtitle").text($(this).attr("description"));
	});

	$("#workspace").load("controlpanel.php", function () {
		// Enbale/Disable view and new tip directly from control panel for users based on settings
		<?php
		// Disable clicking on control panel items for readonly users
		if ($_SESSION['readonly'] == "f") {
		?>
		$(".new").click(function () {
			var platenum = $(this).text().split(" / ");
			$("#newitem").load("ajax/divs/newtriplog.php", { btntext:"TRIP LOG", tid: $(this).attr("id").replace("new_",""), truck: platenum[0] }, function () {
				$(this).fadeIn().draggable();
			});
		});
		<?php
		}

		if ($_SESSION['readonly'] == "f") {
		?>
		$(".det").click(function () {
			$("#newitem").load("ajax/divs/trip_info_tab.php", { id: $(this).attr("id").replace("det_",""), truck: $(this).text() }, function () {
				$(this).fadeIn().draggable();
			});
		});
		<?php
		}
		?>

		// Load QTip for drivers
		$(".qtipdriver").each(function() {
			$(this).qtip({
				content: {
					text: 'Loading Driver Information...', // The text to use whilst the AJAX request is loading
					ajax: {
						global: false,
						url: 'ajax/qtip.php', // URL to the local file
						type: 'GET', // POST or GET
						data: {
							itemtype: "driver",
							itemid: this.id,
							token: '<?php echo $_SESSION['atoken']; ?>',
						}
					}
				},
				position: {
					target: "mouse",
					adjust: {
						mouse: false,
						x: 30,
					}
				},
				show: {
                solo: true
            	},
			});
		})

		// Load QTip for customers
		$(".qtipcust").each(function() {
			$(this).qtip({
				content: {
					text: 'Loading Customer Information...', // The text to use whilst the AJAX request is loading
					ajax: {
						global: false,
						url: 'ajax/qtip.php', // URL to the local file
						type: 'GET', // POST or GET
						data: {
							itemtype: "customer",
							itemid: this.id,
							token: '<?php echo $_SESSION['atoken']; ?>',
						}
					}
				},
				position: {
					target: "mouse",
					adjust: {
						mouse: false,
						x: 30,
					}
				},
				show: {
                solo: true
            	},
			});
		})

		// Load QTip for customers
		$(".qtipcargo").each(function() {
			$(this).qtip({
				content: {
					text: 'Loading Cargo Information...', // The text to use whilst the AJAX request is loading
					ajax: {
						global: false,
						url: 'ajax/qtip.php', // URL to the local file
						type: 'GET', // POST or GET
						data: {
							itemtype: "cargo",
							itemid: this.id,
							token: '<?php echo $_SESSION['atoken']; ?>',
						}
					}
				},
				position: {
					target: "mouse",
					adjust: {
						mouse: false,
						x: 30,
					}
				},
				show: {
                solo: true
            },
			});
		})

		// Load QTip for customers
		$(".qtipcargoret").each(function() {
			$(this).qtip({
				content: {
					text: 'Loading Return Cargo Information...', // The text to use whilst the AJAX request is loading
					ajax: {
						global: false,
						url: 'ajax/qtip.php', // URL to the local file
						type: 'GET', // POST or GET
						data: {
							itemtype: "cargoret",
							itemid: this.id,
							token: '<?php echo $_SESSION['atoken']; ?>',
						}
					}
				},
				position: {
					target: "mouse",
					adjust: {
						mouse: false,
						x: 30,
					}
				},
				show: {
                solo: true
            	},
			});
		})
	});
});
</script>
</head>
<body>

<img src="<?php echo $_SESSION['logo']; ?>" class="logo" onclick="window.location.href=''">
<div class="head">
<label class="username"><?php echo $_SESSION['description']?></label>
</div>

<div class="title">
<label id="workingtitle"><?php echo $_SESSION['maintitle']; ?></label>
</div>

<div class="logout">
<a href="logout.php" title="Logout from the system"><img src="icons/logout2.png" style="height: 18px;"></a>
</div>

<div class="main">
	<div class="menu">
		<table border="0" cellpadding=2 cellspacing=0 style="width: 100%">
			<?php
			$res = pg_query($con, "select * from menu where mid in (select lpperm from login_permissions where lpuser=" . $_SESSION["id"] . ") order by morder");
			while($row = pg_fetch_assoc($res)) {
				if($row["mspacebefore"] == "t") {
					echo "<tr class='none'><td>&nbsp;</td></tr>";
				}
				echo	'<tr class="amenu" id="' . $row['mpage'] . '" description="' . $row['mdescription'] . '"><td><img style="float: left" src="icons/menuicons/' . $row['micon'] . '"> ' . strtoupper($row['mtitle']) . '</td></tr>';
			}
			?>
		</table>
	</div>
	<div class="workspace" id="workspace">

	</div>
</div>
<!-- div for new windows for data insertion -->
<div id="newitem" class="newitem"></div>
<iframe id="printitem" class="printitem"></iframe>
<div id="testbench" style="width: 600px; height: 400px; background: white; position: absolute; bottom: 0px; right: 0px; display: none;">test</div>
</body>
</html>