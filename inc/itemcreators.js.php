<?php
session_start();
?>
// Lets check if our session is active everytime we do an action
$(document).click(function (event) {
	$.ajax({
		url: "inc/session_live.php",
		global: false,
		data: {
			token: '<?php echo $_SESSION['atoken']; ?>',
		},
		success: function (data) {
			if (data == "NO") {
				parent.window.location.href = "index.php?expired";
			}
		}
	})
})

// Set datepicker defaults
$.datepicker.setDefaults({
    dateFormat: 'dd/mm/yy',
});

// Check dates and remove highlighted class acordingly (all dates are warned 1 month ahead)
function check_expiry() {
	$(".dp").change(function () {
		if(moment($(this).val(), "DD-MM-YYYY").isBefore(moment().add(1, "M")) == true) {
			$(this).addClass("expirydate");
		} else {
			$(this).removeClass("expirydate");
		}
	})
}

// Phone number validation method
$.validator.addMethod('phoneTZ', function (value) {
    return /[+][0-9 ]+$/.test(value);
}, 'Phone number format: +XXX YYYYYYY...');

$.validator.addMethod('numberplateTZ', function (value) {
	var reg = value.replace(/\s+/g,"");
	reg = reg.replace(/-/g,"");
    return /[a-zA-Z]{1}[0-9]{3}[a-zA-Z]{3}$/.test(reg);
}, 'Numberplate format: T XXX ABC...');

// Auto add row numbers to table, add id=numbered to table def
function tablerows() {
	$('#numbered tr, .numbered tr').not(":first").each(function(idx){
		$(this).children().first().html((idx + 1) + '. ' );
	});
}

function excel() {
	$("#excel").click(function (e) {
		e.preventDefault();
		// Export table class excel
		$("#newitem").load("ajax/divs/export_excel.php" ,{ exceltable: $("#exceltable").val(), excelsearchid: $("#excelsearchid").val() }, function () {
			$(this).draggable().fadeIn();
		});
	})
}

// Check if session expired and kick out
/*function session() {
	$(":input button").focusin(function () {
		$.ajax({
			url: "inc/session_live.php",
			global: false,
			success: function (data) {
				console.log(data)
				if (data == "NO") {
					parent.window.location.href = "index.php?expired";
				}
			}
		})
	})
}*/

// Delete option allows a user to delete the value directly from the textbox associated with the dropdown.
// Otherwise he will be warned and always forced to make a choice.
// With value will add an extra value to a textbox that has _val apended to the current id
// Create new, if set will open a new confirmation to add the item to the dropdoen list
function acomplete(element, source, deleteoption, withvalue, createnew, createtable, createcolumn, retid, refreshvalue, refreshvalueelement, refreshvaluepage, refreshvalueid) {
	// Add arrow as this is a dropdown
	$(element).addClass("dropdown");

	$(element).keydown(function (event) {
		if (event.keyCode == 13) {
			// trigger change of autocomplete
			$(element).trigger("select");
			$(element).next("input").focus();
		}
	})

	$(element).autocomplete({
		source: source,
		minLength: 0,
		global: false,
		select: function (event, ui) {
			if (withvalue == true) {
				$("#" + $(this).attr("id") + "_val").val(ui.item.thevalue);
				//$("#" + $(this).attr("id") + "_val").trigger("change");
			}
			// Update hidden on select option
			$("#" + $(this).attr("id") + "_id").val(ui.item.id);
			// For items that have change event bound trigger ot so we are updating data in table.
			$("#" + $(this).attr("id") + "_id").trigger("change");
			// When changing also check if we have to update the avg value
			if (refreshvalue == true) {
				// Call page to get new value
				//refreshvalue, refreshvalueelement, refreshvaluepage
				$.ajax({
					url: refreshvaluepage,
					global: false,
					data: {
						id: refreshvalueid,
						token: '<?php echo $_SESSION['atoken']; ?>',
					},
					success: function (data) {
						refreshvalueelement.text("$. " + data + "/day");
					}
				})
			}
		},
		change: function (event, ui) {
			//alert($(this).val());
			var newlabel = $(this).val();
			var workingelement = $(this);
			//////alert(workingelement.attr("id"))
			if (!ui.item && $(this).val().length > 0) { // Item not selected in the dropdown list
				$.ajax({
					url: "ajax/check_dropdown_item_exists.php",
					global: false,
					method: "POST",
					data: {
						table: createtable,
						colnames: createcolumn,
						colvals: encodeURI(String($(this).val().toUpperCase())),
						token: '<?php echo $_SESSION['atoken']; ?>',
					},
					success: function (data) {
						if (data != "TRUE" && createnew == true) {
							// Ask confirm to add new item to table
							$.confirm('ITEM DOES NOT EXIST! ADD TO LIST?', function (answer) {
								if (answer) {
									$.ajax({
										url: "inc/insert_table_field.php",
										global: false,
										method:"POST",
										data: {
											table: createtable,
											colnames: createcolumn,
											colvals: String(newlabel.toUpperCase()),
											retid: retid,
											token: '<?php echo $_SESSION['atoken']; ?>',
										},
										success: function (data) {
											if ($.isNumeric(data)) {
												$("#" + workingelement.attr("id") + "_id").val(data);
												// And update DB
												$("#" + workingelement.attr("id") + "_id").trigger("change");
												if (refreshvalue == true) {
													// Call page to get new value
													//refreshvalue, refreshvalueelement, refreshvaluepage
													$.ajax({
														url: refreshvaluepage,
														global: false,
														data: {
															id: refreshvalueid,
															token: '<?php echo $_SESSION['atoken']; ?>',
														},
														success: function (data) {
															refreshvalueelement.text("$. " + data + "/day");
														}
													})
												}
											} else {
												$.alert(data);
											}
										},
										error: function () {
											$.alert('ERROR CREATING THE NEW ITEM!');
										}
									})
								} else {
									// Restore previous value
									workingelement.val(workingelement.prop("oldvalue")).focus()
								}
							})
						} else if (createnew == false) {
							$.alert('THIS ITEM DOES NOT EXIST  IN THE LIST.')
							// Blank element as we are not doing anything
							workingelement.val(workingelement.prop("oldvalue")).focus()
						} else {
							// Commit change with value that already exists
							// fecth item id and trigger select event
							$.ajax({
								url: "ajax/get_dropdown_item_id.php",
								global: false,
								method: "POST",
								data: {
									table: createtable,
									colnames: createcolumn,
									colvals: String($(element).val().toUpperCase()),
									retid: retid,
									token: '<?php echo $_SESSION['atoken']; ?>',
								},
								success: function (data) {
									if ($.isNumeric(data)) {
										$("#" + workingelement.attr("id") + "_id").val(data);
										$("#" + workingelement.attr("id") + "_id").trigger("change");
										if (refreshvalue == true) {
											// Call page to get new value
											//refreshvalue, refreshvalueelement, refreshvaluepage
											$.ajax({
												url: refreshvaluepage,
												global: false,
												data: {
													id: refreshvalueid,
													token: '<?php echo $_SESSION['atoken']; ?>',
												},
												success: function (data) {
													refreshvalueelement.text("$. " + data + "/day");
												}
											})
										}
									}
								}
							})
						}
					}
				})
			} else {
				$(this).val((ui.item ? ui.item.label : "")); // If empty put back the last one
				if (!ui.item) {
					if (deleteoption !== true) {
						this.value = "";
						$.alert('YOU CAN SELECT FROM DROPDOWN ONLY!');
						$(element).val(element.oldvalue).focus();
					} else {
						$("#" + $(this).attr("id") + "_id").val("");
						$("#" + $(this).attr("id") + "_id").trigger("change");
					}
				} else {
					// If we want update the averages
					if (refreshvalue == true) {
						// Call page to get new value
						//refreshvalue, refreshvalueelement, refreshvaluepage
						$.ajax({
							url: refreshvaluepage,
							global: false,
							data: {
								id: refreshvalueid,
								token: '<?php echo $_SESSION['atoken']; ?>',
							},
							success: function (data) {
								refreshvalueelement.text("$. " + data + "/day");
							}
						})
					}
				}
			}
		}
	}).dblclick(function () {
		$(this).autocomplete("search", "");
	}).click(function () {
		$(this).autocomplete("search", "");
	})
}

function updatecheckbox(table, columnname, columnvalue, idcolumnname, idcolumnvalue) {
	$.ajax({
		url: "inc/update_checkbox_field.php",
		method: "POST",
		data: {
			table: table,
			colname: columnname,
			colval: columnvalue,
			colnameid: idcolumnname,
			colvalid: idcolumnvalue,
			token: '<?php echo $_SESSION['atoken']; ?>',
		},
		success: function (data) {
			// No info on update
			if (!$.isNumeric(data)) {
				$.alert('DATA UPDATE ERROR! EITHER THE DATA IS WRONG OR YOU CANNOT DELETE IT!\n\nERROR MESSAGE:\n' + data);
			}
		},
		error: function (data) {
			$.alert('ERROR');
		}
	});
}

function updatevalue(table, columnname, columnvalue, idcolumnname, idcolumnvalue, isanumber, uppercase, element, isadate, textarea) {
	$.ajax({
		url: "inc/update_table_field.php",
		method: "POST",
		data: {
			table: table,
			colname: columnname,
			colval: columnvalue,
			colnameid: idcolumnname,
			colvalid: idcolumnvalue,
			isanumber: isanumber,
			uppercase: uppercase,
			isadate: isadate,
			textarea: textarea,
			token: '<?php echo $_SESSION['atoken']; ?>',
		},
		success: function (data) {
			// No info on update
			if (!$.isNumeric(data)) {
				// If we have jquery validation rules then alert with those otherwise show database error (based on field)
				if ( Object.keys( $( element ).rules() ).length == 0) {
					$.alert('DATA UPDATE ERROR! EITHER THE DATA IS WRONG OR YOU CANNOT DELETE IT!\n\nERROR MESSAGE:\n' + data, function () {
						$(element).focus();
						$(element).closest("td").addClass("highlighted")
					});
				} else {
					$.alert('THE DATA YOU INSERTED IS WRONG! PLEASE CHECK AND CORRECT!' + data, function () {
						$(element).focus();
						$(element).addClass("error");
					});
				}
			} else {
				$(element).closest("td").removeClass("highlighted")
			}
		},
		error: function () {
			$.alert('ERROR!', function () {
				$(element).addClass("highlighted");
			});
		}
	});
}

function insertvalue(table, columnnames, columnvalues, retid, loaddiv, loadpage) {
	$.ajax({
		url: "inc/insert_table_field.php",
		method: "POST",
		data: {
			table: table,
			colnames: columnnames,
			colvals: columnvalues,
			retid: retid,
			token: '<?php echo $_SESSION['atoken']; ?>',
		},
		success: function (data) {
			// No info on update
			if (!$.isNumeric(data)) {
				$.alert('DATA UPDATE ERROR! EITHER THE DATA IS WRONG OR YOU CANNOT DELETE IT!\n\nERROR MESSAGE:\n' + data);
			} else {
				loaddiv.load(loadpage);
			}
		},
		error: function (data) {
			$.alert('ERROR!');
		}
	});
}

// If we supply  refreshtab = true then it will refresh in a jquery ui tab after success ajax
function delitem(table, idcolumnname, idcolumnvalue, pagetoload, divtoload, refreshtab, tab, index,closewin, refreshvalue, refreshvalueelement, refreshvaluepage, refreshvalueid) {
	$.confirm('DELETE THIS ITEM?', function (answer) {
		if (answer) {
			// Close new item only when we delete from main working panel
			//if ($("#" + table + "_" + idcolumnvalue).closest("div").attr("id") != "newitem") {
			if (closewin == true) {
				$("#newitem").fadeOut();
			}
			$.ajax({
				url: "inc/delete_table_row.php",
				method: "POST",
				data: {
					table: table,
					colnameid: idcolumnname,
					colvalid: idcolumnvalue,
					token: '<?php echo $_SESSION['atoken']; ?>',
				},
				success: function (data) {
					// No info on update
					if (!$.isNumeric(data)) {
						$.alert('DATA UPDATE ERROR! EITHER THE DATA IS WRONG OR YOU CANNOT DELETE IT!\n\nERROR MESSAGE:\n' + data);
					} else {
						if (refreshtab == true) {
							//$("#tabs").tabs("load", 2);
							$("#" + tab).tabs("load", index);
						} else {
							// Refresh data
							$(divtoload).load(pagetoload);
						}
						// update averages if required
						if (refreshvalue == true) {
							// Call page to get new value
							//refreshvalue, refreshvalueelement, refreshvaluepage
							$.ajax({
								url: refreshvaluepage,
								global: false,
								data: {
									id: refreshvalueid,
									token: '<?php echo $_SESSION['atoken']; ?>',
								},
								success: function (data) {
									refreshvalueelement.text("$. " + data + "/day");
								}
							})
						}
					}
				},
				error: function (data) {
					$.alert('ERROR!');
				}
			});
		}
	})
}

function searchbox() {
	// text input search for tables (such as trip history etc)
	$("#search").keyup(function () {
		// remove any open window when searching
		$("#newitem").fadeOut();
		//split the current value of searchInput
		var data = this.value.toUpperCase().split(" ");
		//create a jquery object of the rows
		var jo = $(".searchtbl").find("tr").not("tr:first"); // exclude headers
		if (this.value == "") {
			jo.show();
			// Remove highlighting
			$(".searchtbl td").removeClass('highlighted');
			return;
		}
		//hide all the rows
		jo.hide();

		//Recusively filter the jquery object to get results.
		jo.filter(function (i, v) {
		var $t = $(this);
		if ($t.find("input").length > 0) {
  			var txt = '';

		    $(v).find("input").each(function(n,e){
		        txt += e.value;
		    });

		    for(var d=0; d<data.length; d++){
		       if (txt.search(data[d])>=0) {
		            return true;
		       }
		    }
		} else {
			for (var d = 0; d < data.length; ++d) {
				if ($t.is(":contains('" + data[d] + "')")) {
					return true;
				}
			}
		}
		return false;
	})
	//show the rows that match.
	.show();

	// Highligh table cells
	// Loop through all td's
	$(".searchtbl").find("tr").not("tr:first").find("td").each(function (index, elem) {
		var $elem = $(elem);
		if ($elem.find("input").length > 0) {
			for (var d = 0; d < data.length; ++d) {
				// Highlight
				if ($elem.find("input").val().toUpperCase().indexOf(data[d]) != -1) {
					$elem.addClass('highlighted');
				} else {
					$elem.removeClass('highlighted');
				}
			}
		} else {
			for (var d = 0; d < data.length; ++d) {
				// Highlight
				if ($elem.text().toUpperCase().indexOf(data[d]) != -1) {
					$elem.addClass('highlighted');
				} else {
					$elem.removeClass('highlighted');
				}
			}
		}
	})


	})

	$("#delquicksearch").click(function (e) {
		// remove excel export window
		$("#newitem").fadeOut();
		e.preventDefault();
		$("#search").val("").trigger("keyup");
	});
}