/*	Written by Jonas Jensen 2010-12-XX */

var EmployeeDataTable, SiteDataTable, PromoCodeDataTable, PromoCodeSiteDataTable, ComputerDataTable;
var aPos;               // referencevariable for datatable rows
var editedCell;         // referencevariable for clicked cells
var sitelist;
var Customer, Employee;
				
function AJAX_UpdateComputer(id,enteredText,originalHTML,settingsParams,savingAnimationCallbacks) { 
	var data = { "NewValue" : enteredText }
	//var regex = /(\w+)_(\d+)?.*/;
	$(this.parentNode).children().each(function(index) {
		data[$(this).attr("id")] = this.innerHTML;
	});
	data["ID"] = this.id;
	var datastring = JSON.stringify(data);
	Customer = $(this).parent().children('td[id^=CustomerID_]')[0];
	Employee = $(this).parent().children('td[id^=EmployeeID_]')[0];
	$.ajax({
		url: "ajax_functions/WebService1.php/UpdateComputer",
		type: "POST",
		data: { data: datastring },
		dataType: "json",
		async: false,
		success: function(data, status) {
			var regex = /(.*):(.*)/;
			$(Customer).html(data.d.toString().match(regex)[1]);
			$(Employee).html(data.d.toString().match(regex)[2]);
		},
		error: function(request, status, error) {
			alert(error);
		}
	});
	//return enteredText; //passing callback_skip_dom_reset to editInPlace so it won't give any warnings if nothing is returned
}

function AJAX_Login() {
	$.ajax({
		type: "POST",
		url: "ajax_functions/loginserver.php",
		data: "password=" + $("#login").val(),
		dataType: "html",
		success: function(data,status) {
			window.location.href=".";
		}
	});
}

function UpdateSites() {
	$.ajax({
		type: "GET",
		url: "ajax_functions/make_option_list.php",
		data: { "TableSource": "Site" } ,
		success: function(data, status) {
			var New_Employee_Site = $("#New_Employee_Site");
		    var New_PromoCodeSite_Site = $("#New_PromoCodeSite_Site");
			var site_array = $.trim(data).split(",");
			sitelist = ":,"; //dropdown inside the table can reset fields to null
			
			New_Employee_Site.empty();
			New_PromoCodeSite_Site.empty();
			
			for(i=0; i!=site_array.length; i++) { 
				var name = site_array[i].split(':')[0];
				var id = site_array[i].split(':')[1];
				var selected = (i == 0) ? 'selected="selected"' : '';
				var option = $('<option ' + selected + '></option>').val(id).text(id + ": " + name);
				sitelist = sitelist + name + ":" + id;
				if (i != site_array.length-1) sitelist = sitelist + ",";
				New_Employee_Site.append(option.clone());
				New_PromoCodeSite_Site.append(option);
			}

			//need to figure out a way to update editInPlace. preferably without unbind and bind.
			bind_jQuery_editInPlace(".editInPlace_Employee_Site_dropdown", EmployeeDataTable, "ajax_functions/WebService1.php/UpdateEmployee", "select", sitelist);
			$("#New_Employee_Site_Input").val($("#New_Employee_Site option:selected").text());
			$("#New_PromoCodeSite_Site_Input").val($("#New_PromoCodeSite_Site option:selected").text());	
		}
	});
}

function UpdateEmployees() {
	$.ajax({
		type: "GET",
		url: "ajax_functions/make_option_list.php",
		data: { "TableSource": "Employee" } ,
		success: function(data, status) {
			var array = $.trim(data).split(",");
			//var list = ":null,"; //dropdown inside the table can reset fields to null
			var list = "";
			for(i=0; i!=array.length; i++) {
				var name = array[i].split(':')[0];
				var id = array[i].split(':')[1];
				list = list + name + ":" + id;
				if (i != array.length-1) list = list + ",";
			}
			bind_jQuery_editInPlace(".editInPlace_Computer_Employee_dropdown", ComputerDataTable, "ajax_functions/WebService1.php/UpdateComputer", "select", list, AJAX_UpdateComputer);
		}
	});
}

function UpdatePromoCodes() {
	$.ajax({
		type: "GET",
		url: "ajax_functions/make_option_list.php",
		data: { "TableSource": "PromoCode" } ,
		success: function(data, status) {
			var New_PromoCodeSite_Code = $("#New_PromoCodeSite_Code");
			var promocode_array = $.trim(data).split(",");
			New_PromoCodeSite_Code.empty();
			for(i=0; i!=promocode_array.length; i++) {
				var promocode = promocode_array[i].split(':')[0];
				var id = promocode_array[i].split(':')[1];
				var selected = (i == 0) ? 'selected="selected"' : '';
				var option = $('<option ' + selected + '></option>').val(id).text(promocode);
				New_PromoCodeSite_Code.append(option);
			}
			$("#New_PromoCodeSite_Code_Input").val($("#New_PromoCodeSite_Code option:selected").text());
		}
	});
}

function bind_jQuery_editInPlace(element, datatable, target, field_type, select_options, callbackfunction) {
	$(element, datatable.fnGetNodes()).bind("click", function(event) {
		aPos = datatable.fnGetPosition(this);		        //get a reference to the row clicked
		editedCell = $(this);                               //get a reference to the cell which was clicked
	}).editInPlace({                                        //bind editInPlace jQuery plugin to element
		url: target,                                        //target is usually a WebService update method
		params: "ajax=yes",
		field_type: field_type,                             //usually text or selector. determines the inputtype
		textarea_cols: 80,
		default_text: "",                                   //empty/null cells are displayed with this text
		select_options: select_options,                     //if selector attach the options as commadelimited list
		show_buttons: true,                                 //save/cancel buttons will be shown
		update_value: 'NewValue',
		element_id: 'ID',
		bg_over: '#f99',
		callback: callbackfunction,
		callback_skip_dom_reset: (callbackfunction),
		//delegate: { didCloseEditInPlace: function(aDOMNode, aSettingsDict) { alert(aDOMNode); } },
		success: function() {                               //editInPlace post was a success, reassign the correct data so sorting will continue to work            
			var data = datatable.fnGetData(aPos[0]);	    //use reference of row clicked
			data[aPos[1]] = editedCell.html();              //put back the new data 
		},
		error: function(request,data) {
			alert(request+":"+data);
		}
	});
}

function bind_jQuery_delete(element,id,target,datatable) {
	$(element).live("click", function(event) {								//bind with jQuery live. all future elements will be bound.
		aPos = datatable.fnGetPosition(this);								//get a reference to the row clicked
		editedCell = $(this);												//get a reference to the cell which was clicked
		var data = { "ID": editedCell.attr('id').replace(id,'') };
		var datastring = JSON.stringify(data);
		$('#Dialog_ReallyDelete').data('data', datastring).data('datatable', datatable).data('aPos', aPos).data('target', target).data('id', id).dialog('open');	
	});
}


$(function() {
	$.datepicker.setDefaults($.datepicker.regional['sv']); 
	$.datepicker.setDefaults({ dateFormat: 'yy-mm-dd' });
 
	$("#Button_OpenNewSite").click(function(event) { $('#Dialog_AddSite').dialog('open'); } );
	$('#Dialog_AddSite').dialog({
		autoOpen: false,
		width: 600,
		buttons: { 	
			"Ok": function() {
			    var vals = [                                                                //fetch values from textboxes 
					$("#New_Site_Name"),			$("#New_Site_Address"),			
					$("#New_Site_Zip"),				$("#New_Site_City"),
					$("#New_Site_StreetAddress"),	$("#New_Site_StreetZip"),
					$("#New_Site_StreetCity"),		$("#New_Site_PhoneNo"),
					$("#New_Site_FaxNo"),			$("#New_Site_OrgRegNo"),
					$("#New_Site_Country"),			$("#New_Site_MailAddress")
				];	
				var data = { "Name": vals[0].val() ,			"Address": vals[1].val() ,		"Zip": vals[2].val() ,			"City": vals[3].val() , 
							 "StreetAddress": vals[4].val() ,	"StreetZip": vals[5].val(),		"StreetCity": vals[6].val() ,	"PhoneNo": vals[7].val() , 
							 "FaxNo": vals[8].val() ,			"OrgRegNo": vals[9].val() ,		"Country": vals[10].val(),		"MailAddress": vals[11].val() }
		       	var datastring = JSON.stringify(data);
				$.ajax({ 
		            type: "POST",
		            url: "ajax_functions/WebService1.php/AddSite",
		            data: { data: datastring },
		            dataType: "json",
		            success: function(data, status) {   
		                var success = (data.d.toString().substr(0, 5) == "added");
				        if (success) {
		                    var sID = data.d.toString().substr(5);                      //extract the ID
		                    var ID = parseInt(sID);                                     //convert it to int type
		                    SiteDataTable.fnAddData([ID, vals[0].val(), vals[1].val(), vals[2].val(), vals[3].val(), vals[4].val(), vals[5].val(), vals[6].val(), vals[7].val(), vals[8].val(), vals[11].val(), 'No', "<div class='Delete_icon'></div>" ]);
				            var nRows = SiteDataTable.fnGetNodes();
		                    for (var i = 0; i < nRows.length; i++) {
		                        if (parseInt(nRows[i].cells[0].innerHTML) == ID) {
									//alert(nRows[i].cells[1].getAttribute("id"));
		                            nRows[i].cells[0].setAttribute("id", "ID_" + sID);
									nRows[i].cells[1].setAttribute("id", "Name_" + sID);
									nRows[i].cells[2].setAttribute("id", "Address_" + sID);
									nRows[i].cells[3].setAttribute("id", "Zip_" + sID);
									nRows[i].cells[4].setAttribute("id", "City_" + sID);
									nRows[i].cells[5].setAttribute("id", "StreetAddress_" + sID);
									nRows[i].cells[6].setAttribute("id", "StreetZip_" + sID);
									nRows[i].cells[7].setAttribute("id", "StreetCity_" + sID);
									nRows[i].cells[8].setAttribute("id", "PhoneNo_" + sID);
									nRows[i].cells[9].setAttribute("id", "FaxNo_" + sID);
									nRows[i].cells[10].setAttribute("id", "MailAddress_" + sID);
									nRows[i].cells[11].setAttribute("id", "Approved_" + sID);
		                            nRows[i].cells[12].setAttribute("id", "Delete_Site_" + sID);
		                            nRows[i].cells[12].setAttribute("style", "text-align:right;");
		                            for (var j = 0; j < nRows[i].cells.length; j++) { 
		                                if (j >= 1 && j <= 10) {
		                                    bind_jQuery_editInPlace(nRows[i].cells[j], SiteDataTable, "ajax_functions/WebService1.php/UpdateSite", "text");
		                                }
										else if (j == 11) {
											bind_jQuery_editInPlace(nRows[i].cells[j], SiteDataTable, "ajax_functions/WebService1.php/UpdateSite", "select", "No,Yes");
										}
		                            }
		                            break;
		                        }
		                    }
		                    $(vals).each(function() { $(this).val('') });                   //clear textboxes
							UpdateSites();
		                }
		            }
		        });
				$(this).dialog("close"); 
			}, 
			"Cancel": function() { 	$(this).dialog("close"); } 
		}
	});

	$("#Button_OpenNewEmployee").click(function(event) { $('#Dialog_AddEmployee').dialog('open'); } );
	$('#Dialog_AddEmployee').dialog({
		autoOpen: false,
		width: 600,
		open: function() {
			$("#New_Employee_Site_Input").select();
		},
		buttons: {
			"Ok": function() {  
				var vals = [
					$("#New_Employee_Site"),			$("#New_Employee_Name"),            
					$("#New_Employee_PhoneNo"),			$("#New_Employee_Comment"),
					$("#New_Employee_MailAddress"),		$("#New_Employee_MailAddress2"),
					$("#New_Employee_Optician"),		$("#New_Employee_Scientist"),
				];
				var data = {	"SiteID": vals[0].val(),			"Name": vals[1].val(),				"PhoneNo": vals[2].val(),		"Comment": vals[3].val(),
								"MailAddress": vals[4].val(),		"MailAddress2": vals[5].val(), 	 	"Optician": vals[6].val(),		"Scientist": vals[7].val() }
				var datastring = JSON.stringify(data);
				$.ajax({
					type: "POST",
					url: "ajax_functions/WebService1.php/AddEmployee",
					data: { data: datastring },
					dataType: "json",
					success: function(data, status) {
						var success = (data.d.toString().substr(0, 5) == "added");
						if (success) {  
							var regex = /added(\d+):(\d+)/
							var sEmployeeID = data.d.toString().match(regex)[1];
							var sEmployeeCustomerID = data.d.toString().match(regex)[2];
							var EmployeeID = parseInt(sEmployeeID);
							var EmployeeCustomerID = parseInt(sEmployeeCustomerID);
							EmployeeDataTable.fnAddData([EmployeeID, vals[0].find("option:selected").text().split(":")[1], EmployeeCustomerID, vals[1].val(), vals[2].val(), vals[3].val(), vals[4].val(), vals[5].val(), '', vals[6].find("option:selected").text(), vals[7].find("option:selected").text() , "<div class='Delete_icon'></div>"]);
							var nRows = EmployeeDataTable.fnGetNodes();
							for (var i = 0; i < nRows.length; i++) {
								if (parseInt(nRows[i].cells[0].innerHTML) == EmployeeID) {
									nRows[i].cells[0].setAttribute("id", "ID_" + sEmployeeID);
									nRows[i].cells[1].setAttribute("id", "SiteID_" + sEmployeeID);
									nRows[i].cells[2].setAttribute("id", "CustomerID_" + sEmployeeID);
									nRows[i].cells[3].setAttribute("id", "Name_" + sEmployeeID);
									nRows[i].cells[4].setAttribute("id", "PhoneNo_" + sEmployeeID);
									nRows[i].cells[5].setAttribute("id", "Comment_" + sEmployeeID);
									nRows[i].cells[6].setAttribute("id", "MailAddress_" + sEmployeeID);
									nRows[i].cells[7].setAttribute("id", "MailAddress2_" + sEmployeeID);
									nRows[i].cells[8].setAttribute("id", "Password_" + sEmployeeID);
									nRows[i].cells[9].setAttribute("id", "Optician_" + sEmployeeID);
									nRows[i].cells[10].setAttribute("id", "Scientist_" + sEmployeeID);
									nRows[i].cells[11].setAttribute("id", "Delete_Employee_" + sEmployeeID);
									nRows[i].cells[11].setAttribute("style", "text-align:right;");
									for (var j = 0; j < nRows[i].cells.length; j++) {
										if (j == 1) {
											bind_jQuery_editInPlace(nRows[i].cells[j], EmployeeDataTable, "ajax_functions/WebService1.php/UpdateEmployee", "select", sitelist);
										}
										else if (j >= 3 && j <= 4 || j >= 6 && j <= 8) {
											bind_jQuery_editInPlace(nRows[i].cells[j], EmployeeDataTable, "ajax_functions/WebService1.php/UpdateEmployee", "text");
										}
										else if (j == 5) {
											bind_jQuery_editInPlace(nRows[i].cells[j], EmployeeDataTable, "ajax_functions/WebService1.php/UpdateEmployee", "textarea");
										}
										else if (j >= 9 && j <= 10) {
											bind_jQuery_editInPlace(nRows[i].cells[j], EmployeeDataTable, "ajax_functions/WebService1.php/UpdateEmployee", "select", "No,Yes");
										}
									}
									break;
								}
							}
							$(vals).each(function() { $(this).val('') });
						}
					}
				});
				$(this).dialog("close"); 
			},
			"Cancel": function() {  $(this).dialog("close"); }
		}
	});

	$("#Button_OpenNewPromoCode").click(function(event) { $('#Dialog_AddPromoCode').dialog('open'); } );
	$("#New_PromoCode_Starts").datepicker();
	$("#New_PromoCode_Expires").datepicker();
	$('#Dialog_AddPromoCode').dialog({
		autoOpen: false,
		width: 600,
		buttons: {
			"Ok": function() {
				var vals = [
					$("#New_PromoCode_Code"),            $("#New_PromoCode_Starts"),
					$("#New_PromoCode_Expires"),         $("#New_PromoCode_Global")
				];
				var data = { "PromoCode": vals[0].val(), "StartDate": vals[1].val(), "ExpireDate": vals[2].val(), "Global": vals[3].val()  }
				var datastring = JSON.stringify(data);
				$.ajax({
					type: "POST",
					url: "ajax_functions/WebService1.php/AddPromoCode",
					data: { data: datastring },
					dataType: "json",
					success: function(data, status) {
						var success = (data.d.toString().substr(0, 5) == "added");
						if (success) {
							var regex = /added(\d+)/;
							var sPromoCodeID = data.d.toString().match(regex)[1];
							var PromoCodeID = parseInt(sPromoCodeID);
							PromoCodeDataTable.fnAddData([PromoCodeID, vals[0].val(), vals[1].val(), vals[2].val(), vals[3].find("option:selected").text(), "<div class='Delete_icon'></div>"]);
							var nRows = PromoCodeDataTable.fnGetNodes();
							for (var i = 0; i < nRows.length; i++) {
								if (parseInt(nRows[i].cells[0].innerHTML) == PromoCodeID) {
									nRows[i].cells[0].setAttribute("id", "ID_" + sPromoCodeID);
									nRows[i].cells[1].setAttribute("id", "PromoCode_" + sPromoCodeID);
									nRows[i].cells[2].setAttribute("id", "StartDate_" + sPromoCodeID);
									nRows[i].cells[3].setAttribute("id", "ExpireDate_" + sPromoCodeID);
									nRows[i].cells[4].setAttribute("id", "Global_" + sPromoCodeID);
									nRows[i].cells[5].setAttribute("id", "Delete_PromoCode_" + sPromoCodeID);
									nRows[i].cells[5].setAttribute("style", "text-align:right;");
									for (var j = 0; j < nRows[i].cells.length; j++) {
										if (j >= 1 && j <= 3) {
											bind_jQuery_editInPlace(nRows[i].cells[j], PromoCodeDataTable, "ajax_functions/WebService1.php/UpdatePromoCode", "text");
										}
										else if (j == 4) {
											bind_jQuery_editInPlace(nRows[i].cells[j], PromoCodeDataTable, "ajax_functions/WebService1.php/UpdatePromoCode", "select", "No,Yes");
										}
									}
									break;
								}
							}
							$(vals).each(function() { $(this).val('') });
							UpdatePromoCodes();
						}
					}
				});
				$(this).dialog("close");
			},
			"Cancel": function() {  $(this).dialog("close"); }
		}
	});

	$("#Button_OpenNewPromoCodeSite").click(function(event) { $('#Dialog_AddPromoCodeSite').dialog('open'); } );
	$('#Dialog_AddPromoCodeSite').dialog({
		autoOpen: false,
		width: 600,
		open: function() {
			$("#New_PromoCodeSite_Site_Input").select();
		},
		buttons: {
			"Ok": function() {
				var vals = [ $("#New_PromoCodeSite_Site"), $("#New_PromoCodeSite_Code") ];
				var data = { "SiteID": vals[0].val(), "PromoCodeID": vals[1].val()  }
				var datastring = JSON.stringify(data);
				$.ajax({
					type: "POST",
					url: "ajax_functions/WebService1.php/AddPromoCodeSite",
					data: { data: datastring },
					dataType: "json",
					success: function(data, status) {
						var success = (data.d.toString().substr(0, 5) == "added");
						if (success) {
							var SiteID = parseInt(vals[0].val());
							var Site = vals[0].find("option:selected").text().split(":")[1];
							var PromoCodeID = parseInt(vals[1].val());
							var PromoCode = vals[1].find("option:selected").text();
							PromoCodeSiteDataTable.fnAddData( [ Site, SiteID, PromoCode, "<div class='Delete_icon'></div>" ] );
							var nRows = PromoCodeSiteDataTable.fnGetNodes();
							for (var i = 0; i < nRows.length; i++) {
								if (parseInt(nRows[i].cells[1].innerHTML) == SiteID && nRows[i].cells[2].innerHTML.indexOf(PromoCode) != -1) {
									nRows[i].cells[0].setAttribute("id", "Site_" + SiteID);
									nRows[i].cells[1].setAttribute("id", "SiteID_" + SiteID);
									nRows[i].cells[2].setAttribute("id", "PromoCode_" + PromoCodeID);
									nRows[i].cells[3].setAttribute("id", "Delete_PromoCodeSite_" + PromoCodeID + ":" + SiteID);
									nRows[i].cells[3].setAttribute("style", "text-align:right;");
									break;
								}
							}

							$(vals).each(function() { $(this).val('') });
						}
					}
				});
				$(this).dialog("close");
			},
			"Cancel": function() {  $(this).dialog("close"); }
		}

	});

	$('#Dialog_ReallyDelete').dialog({
		autoOpen: false,
		width: 600,
		buttons: {
			"Ok": function() {  
				var datatable = $(this).data('datatable');
				var data = $(this).data('data');
				var target = $(this).data('target');
				var id = $(this).data('id');
				aPos = $(this).data('aPos');
				$.ajax({
						type: "POST",  
						url: target,
						data: { data: data },
						dataType: "json",
						success: function(data,status) { 
							datatable.fnDeleteRow(aPos[0]);
							if (id == "Delete_Site_") UpdateSites();
							else if (id == "Delete_PromoCode_") UpdatePromoCodes();
						}			
				});
				$(this).dialog("close"); 
			},
			"Cancel": function() {  $(this).dialog("close"); }
		}
	});

	$('#Dialog_Login').dialog({
        autoOpen: true,
        width: 600,
        buttons: {
            "Ok": function() {
				AJAX_Login();
                //$(this).dialog("close");
            }
        }
    });

	shortcut.add("Alt+E", function() {      //bind Alt+T so it opens #new_employee form
		//$('#Dialog_AddSite').dialog('close');
		$('#Button_OpenNewEmployee').click();     //open ticket form
	}, {
		'type': 'keydown',        
		'target': document                  //bind key to keypresses originating from document
	});
	
	shortcut.add("Alt+S", function() {      //bind Alt+U so it opens #new_user form
		//$('#Dialog_AddEmployee').dialog('close');
		$('#Button_OpenNewSite').click();       //open user form
	}, {
		'type': 'keydown',
		'target': document                  //bind key to keypresses originating from document
	});

	shortcut.add("Alt+C", function() {      //bind Alt+U so it opens #new_user form
		$('#Button_OpenNewPromoCode').click();       //open user form
	}, {
		'type': 'keydown',
		'target': document                  //bind key to keypresses originating from document
	});

	shortcut.add("Alt+I", function() {      //bind Alt+U so it opens #new_user form
		$('#Button_OpenNewPromoCodeSite').click();       //open user form
	}, {
		'type': 'keydown',
		'target': document                  //bind key to keypresses originating from document
	});

	$('#switcher').themeswitcher();

	$("#tabs").tabs(); 
	$( "#tabs" ).bind( "tabsshow", function(event, ui) {
		switch(ui.index) {
			case 0:
				EmployeeDataTable.fnAdjustColumnSizing();
				break;
			case 1:
				SiteDataTable.fnAdjustColumnSizing();
				break;
			case 2:
    			ComputerDataTable.fnAdjustColumnSizing();
				break;
			case 3:
				PromoCodeDataTable.fnAdjustColumnSizing();
				PromoCodeSiteDataTable.fnAdjustColumnSizing();	
				break;
			default:
		}
	});

	$("#New_PromoCodeSite_Site").combobox();
	$("#New_PromoCodeSite_Code").combobox();
	$("#New_Employee_Site").combobox();
	$( "#toggle" ).click(function() { $( "#New_PromoCodeSite_Site" ).toggle();	});

	EmployeeDataTable = $("#EmployeeTable").dataTable( { "sScrollX": "100%", "bJQueryUI": true, "sPaginationType": "full_numbers" /*, "bAutoWidth": true, "sDom": '<"top"fl>rt<"bottom"ip><"clear">'*/ } );
	SiteDataTable = $("#SiteTable").dataTable( { "sScrollX": "100%", "bJQueryUI": true, "sPaginationType": "full_numbers" /* , "aoColumnDefs": [  { "sClass": "editInPlace_Site", "aTargets": "_all" } ] */ } );
	ComputerDataTable = $("#ComputerTable").dataTable( { "sScrollX": "100%", "bJQueryUI": true, "sPaginationType": "full_numbers" } );
	PromoCodeDataTable = $("#PromoCodeTable").dataTable( { "sScrollX": "100%", "bJQueryUI": true, "sPaginationType": "full_numbers" } );
	PromoCodeSiteDataTable = $("#PromoCodeSiteTable").dataTable( { "sScrollX": "100%", "bJQueryUI": true, "sPaginationType": "full_numbers" } );
	
	//$(window).bind('resize', function () { DataTables_AdjustColumnSizing(); });

	$('.Button_OpenNew').button();

	$("#login").keypress(function(event) {
		if (event.keyCode == $.ui.keyCode.ENTER) { AJAX_Login(); }
	});

	bind_jQuery_delete("td[id^='Delete_Employee_']", "Delete_Employee_", "ajax_functions/WebService1.php/DeleteEmployee", EmployeeDataTable);
	bind_jQuery_delete("td[id^='Delete_Site_']", "Delete_Site_", "ajax_functions/WebService1.php/DeleteSite", SiteDataTable);
	bind_jQuery_delete("td[id^='Delete_PromoCode_']", "Delete_PromoCode_", "ajax_functions/WebService1.php/DeletePromoCode", PromoCodeDataTable);
	bind_jQuery_delete("td[id^='Delete_PromoCodeSite_']", "Delete_PromoCodeSite_", "ajax_functions/WebService1.php/DeletePromoCodeSite", PromoCodeSiteDataTable);

	bind_jQuery_editInPlace(".editInPlace_Employee", EmployeeDataTable, "ajax_functions/WebService1.php/UpdateEmployee", "text");
	bind_jQuery_editInPlace(".editInPlace_Employee_multirow", EmployeeDataTable, "ajax_functions/WebService1.php/UpdateEmployee", "textarea");
	bind_jQuery_editInPlace(".editInPlace_Employee_dropdown", EmployeeDataTable, "ajax_functions/WebService1.php/UpdateEmployee", "select", "No,Yes");
	bind_jQuery_editInPlace(".editInPlace_Site", SiteDataTable, "ajax_functions/WebService1.php/UpdateSite", "text");
	bind_jQuery_editInPlace(".editInPlace_Site_dropdown", SiteDataTable, "ajax_functions/WebService1.php/UpdateSite", "select", "No,Yes");
	bind_jQuery_editInPlace(".editInPlace_PromoCode", PromoCodeDataTable, "ajax_functions/WebService1.php/UpdatePromoCode", "text");
	bind_jQuery_editInPlace(".editInPlace_PromoCode_dropdown", PromoCodeDataTable, "ajax_functions/WebService1.php/UpdatePromoCode", "select", "No,Yes");
	bind_jQuery_editInPlace(".editInPlace_Computer_Customer", ComputerDataTable, "ajax_functions/WebService1.php/UpdateComputer", "text", "", AJAX_UpdateComputer);

	//$("#New_PromoCode_Code").autocomplete( { source: availableTags } );

	UpdateSites();
	UpdatePromoCodes();
	UpdateEmployees();

	EmployeeDataTable.fnAdjustColumnSizing();

});


