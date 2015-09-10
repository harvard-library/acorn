
var editListDataTable;
function loadLists(selectedvalue)
{
	if (selectedvalue != '')
	{
		function onEditListsReady() {
			var myColumnDefs = getColumnDefs(selectedvalue);
			var fieldList = getFieldList(selectedvalue);
			
			var myDataSource = new YAHOO.util.DataSource(baseUrl + "list/populatelist/listname/" + selectedvalue + "/includeinactive/1");
			myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
			myDataSource.responseSchema = {
	            resultsList: "Result",
	            fields: fieldList
	         };
	       
			// Define a custom row formatter function
			var myRowFormatter = function(elTr, oRecord) {
				//If it is a new row, highlight it until it is updated.
			    if (oRecord.getData(getKeyColumn(selectedvalue)) < 0) {
			    	YAHOO.util.Dom.addClass(elTr, 'newitem');
			    }
			    return true;
			};
			
	        editListDataTable = new YAHOO.widget.DataTable("valuelist", myColumnDefs, myDataSource, {scrollable:true, height:"30em", formatRow: myRowFormatter});
	        editListDataTable.subscribe("rowClickEvent",editListDataTable.onEventSelectRow);
	        editListDataTable.subscribe("cellDblclickEvent",editListDataTable.onEventShowCellEditor);
	        editListDataTable.subscribe("editorBlurEvent", editListDataTable.onEventSaveCellEditor);
	
	        editListDataTable.subscribe("checkboxClickEvent", function(oArgs){ 
	        	var elCheckbox = oArgs.target; 
	        	var oRecord = this.getRecord(elCheckbox); 
	        	var column = editListDataTable.getColumn(elCheckbox);
	        	var oldvalue = 1;
	        	var value = 0;
	        	if (elCheckbox.checked)
	        	{
	        		value = 1;
	        		oldvalue = 0;
	        	}
	        	
	        	oRecord.setData(column.getKey(),value); 
	        	
	        	var pk = oRecord.getData(getKeyColumn(selectedvalue));
	        	if (pk > 0)
	        	{
	        		//Now update the field that was changed.
					var myAjax = new Ajax.Request(baseUrl + "list/updateitem",
					    {method: 'get', 
							parameters: {column: column.getKey(), pk: pk, newdata: value, listname: selectedvalue},
							onComplete: function(transport)
							{
								var val = JSON.parse(transport.responseText);
								//If there is an error message, show it and
								//put the values back
								if (val.ErrorMessage != undefined)
								{
									editListDataTable.updateCell(oRecord, column, oldvalue);
									document.getElementById('editlisterrors').innerHTML = val.ErrorMessage;
								}
								else
								{
									document.getElementById('editlisterrors').innerHTML = "";
								}
							}
					    }
					);
	        	}
	        });
	        
	        // When cell is edited, pulse the color of the row yellow
	        var onCellEdit = function(oArgs) {
	            var elCell = oArgs.editor.getTdEl();
				var oOldData = oArgs.oldData;
				var oNewData = oArgs.newData;
				// Grab the row el and the 2 colors
				var elRow = this.getTrEl(elCell);
				var origColor = YAHOO.util.Dom.getStyle(elRow.cells[0], "backgroundColor");
				var pulseColor = "#ff0";
	
				// Create a temp anim instance that nulls out when anim is complete
				var rowColorAnim = new YAHOO.util.ColorAnim(elRow.cells, {
						backgroundColor:{to:origColor, from:pulseColor}, duration:2});
				var onComplete = function() {
					rowColorAnim = null;
					YAHOO.util.Dom.setStyle(elRow.cells, "backgroundColor", "");
				};
				rowColorAnim.onComplete.subscribe(onComplete);
				rowColorAnim.animate();
				
				var elCellColumn = oArgs.editor.getColumn();
				var elRecord = editListDataTable.getRecord(elRow);
				var pk = elRecord.getData(getKeyColumn(selectedvalue));
				//If the pk is < 0, it is new. We have to add the entire new item
				if (pk < 0)
				{
					var params = getParameters(selectedvalue, elRecord);
					var myAjax = new Ajax.Request(baseUrl + "list/saveitem",
				    	{method: 'get', 
							parameters: params,
							onComplete: function(transport)
							{
								//Get the returned primary key
								if (transport.responseJSON != null)
								{
									var val = JSON.parse(transport.responseText);
									//If there is an error message, show it and
									//put the values back
									if (val.ErrorMessage != undefined)
									{
										editListDataTable.updateCell(elRecord, elCellColumn, oOldData);
										document.getElementById('editlisterrors').innerHTML = val.ErrorMessage;
									}
									else
									{
										pk = val.PrimaryKey;
										editListDataTable.updateCell(elRecord, editListDataTable.getColumn(getKeyColumn(selectedvalue)), pk);
										document.getElementById('editlisterrors').innerHTML = "";
										editListDataTable.render();
									}
								}
							}
				    	}
					);
				}
				else
				{
					//Now update the field that was changed.
					var myAjax = new Ajax.Request(baseUrl + "list/updateitem",
					    {method: 'get', 
							parameters: {column: elCellColumn.getKey(), pk: pk, newdata: oNewData, listname: selectedvalue},
							onComplete: function(transport)
							{
								var val = JSON.parse(transport.responseText);
								//If there is an error message, show it and
								//put the values back
								if (val.ErrorMessage != undefined)
								{
									editListDataTable.updateCell(elRecord, elCellColumn, oOldData);
									document.getElementById('editlisterrors').innerHTML = val.ErrorMessage;
								}
								else
								{
									document.getElementById('editlisterrors').innerHTML = "";
									if (selectedvalue == "Departments" && elCellColumn.getKey() == "Location")
									{
										editListDataTable.updateCell(elRecord, editListDataTable.getColumn("LocationID"), oNewData);
									}
								}
							}
					    }
					);
				}
	        };
	        editListDataTable.subscribe("editorSaveEvent", onCellEdit);
	        
	        hideKeyColumns(selectedvalue);
		}
	
	    YAHOO.util.Event.onContentReady("valuelist", onEditListsReady); 
	    document.getElementById('valuelist').style.display = "block";
	    document.getElementById('editlistbuttons').style.display = "block";
	    if (selectedvalue == 'People')
		{
			document.getElementById('addnewbutton').style.display = "none";
		}
	    else
	    {
	    	document.getElementById('addnewbutton').style.display = "inline-block";
	    }
	}
	else
	{
		document.getElementById('valuelist').style.display = "none";
		document.getElementById('editlistbuttons').style.display = "none";
	}
}

function getParameters(selectedvalue, elRecord)
{
	var params = {};
	switch (selectedvalue)
	{
	case "Departments":
		params = {
			listname: selectedvalue,
			DepartmentID: elRecord.getData("DepartmentID"),
			DepartmentName: elRecord.getData("DepartmentName"),
			LocationID: elRecord.getData("LocationID"),
			ShortName: elRecord.getData("ShortName"),
			Acronym: elRecord.getData("Acronym"),
			Inactive: elRecord.getData("Inactive")	
			};
		break;
	case "Formats":
		params = {
			listname: selectedvalue,
			FormatID: elRecord.getData("FormatID"),
			Format: elRecord.getData("Format"),
			Inactive: elRecord.getData("Inactive")	
			};
		break;
	case "Importances":
		params = {
			listname: selectedvalue,
			ImportanceID: elRecord.getData("ImportanceID"),
			Importance: elRecord.getData("Importance"),
			Inactive: elRecord.getData("Inactive")	
			};
		break;
	case "Locations":
		params = {
			listname: selectedvalue,
			LocationID: elRecord.getData("LocationID"),
			Location: elRecord.getData("Location"),
			TUB: elRecord.getData("TUB"),
			ShortName: elRecord.getData("ShortName"),
			Acronym: elRecord.getData("Acronym"),
			Inactive: elRecord.getData("Inactive"),	
			IsRepository: elRecord.getData("IsRepository"),	
			IsWorkLocation: elRecord.getData("IsWorkLocation")	
			};
		break;
	case "People":
		params = {
			listname: selectedvalue,
			ProjectID: elRecord.getData("PersonID"),
			Project: elRecord.getData("DisplayName"),
			Inactive: elRecord.getData("Inactive")	
			};
		break;
	case "Projects":
		params = {
			listname: selectedvalue,
			ProjectID: elRecord.getData("ProjectID"),
			ProjectName: elRecord.getData("ProjectName"),
			ProjectDescription: elRecord.getData("Description"),
			StartDate: elRecord.getData("StartDate"),
			EndDate: elRecord.getData("EndDate"),
			Inactive: elRecord.getData("Inactive")	
			};
		break;
	case "Purposes":
		params = {
			listname: selectedvalue,
			PurposeID: elRecord.getData("PurposeID"),
			Purpose: elRecord.getData("Purpose"),
			Inactive: elRecord.getData("Inactive")	
			};
		break;
	case "WorkTypes":
		params = {
			listname: selectedvalue,
			WorkTypeID: elRecord.getData("WorkTypeID"),
			WorkType: elRecord.getData("WorkType"),
			Inactive: elRecord.getData("Inactive")	
			};
		break;
	}
	return params;
}

function getKeyColumn(selectedvalue)
{
	var key = '';
	switch (selectedvalue)
	{
	case "Departments":
		key = "DepartmentID";
		break;
	case "Formats":
		key = "FormatID";
		break;
	case "Importances":
		key = "ImportanceID";
		break;
	case "Locations":
		key = "LocationID";
		break;
	case "People":
		key = "PersonID";
		break;
	case "Projects":
		key = "ProjectID";
		break;
	case "Purposes":
		key = "PurposeID";
		break;
	case "WorkTypes":
		key = "WorkTypeID";
		break;
	}
	return key;
}

function hideKeyColumns(selectedvalue)
{
	switch (selectedvalue)
	{
	case "Departments":
		editListDataTable.hideColumn("LocationID");
		editListDataTable.hideColumn("DepartmentID");
		break;
	case "Formats":
		editListDataTable.hideColumn("FormatID");
		break;
	case "Importances":
		editListDataTable.hideColumn("ImportanceID");
		break;
	case "Locations":
		editListDataTable.hideColumn("LocationID");
		break;
	case "People":
		editListDataTable.hideColumn("PersonID");
		break;
	case "Projects":
		editListDataTable.hideColumn("ProjectID");
		break;
	case "Purposes":
		editListDataTable.hideColumn("PurposeID");
		break;
	case "WorkTypes":
		editListDataTable.hideColumn("WorkTypeID");
		break;
	}
}

function getColumnDefs(selectedvalue)
{
	this.myCustomCheckboxFormatter = function(elCell, oRecord, oColumn, oData) {
		if (oData == 1)
		{
			elCell.innerHTML = '<input type="checkbox" checked="checked">';
		}
		else
		{
			elCell.innerHTML = '<input type="checkbox">';
		}
    };
    
    // Add the custom formatter to the shortcuts
    YAHOO.widget.DataTable.Formatter.myCustomCheckbox = this.myCustomCheckboxFormatter;

	var defs = [];
	switch (selectedvalue)
	{
	case "Departments":
		defs = [
			{key:"DepartmentName",label:"Department",editor:"textbox", maxAutoWidth: 200},
			{key:"Location",label:"Location",editor:new locationDropdownCellEditor({acornUrl: "list/populatelocations"}), maxAutoWidth: 200},
			{key:"ShortName",label:"Short Name",editor:"textbox", maxAutoWidth: 100},
			{key:"Acronym",label:"Acronym",editor:"textbox", maxAutoWidth: 50},
			{key:"Inactive",label:"Inactive",formatter:"myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"DepartmentID", maxAutoWidth: 0, width: 0},
			{key:"LocationID", width: 0}
			];
		break;
	case "Formats":
		defs = [
			{key:"Format",label:"Format",editor:"textbox"},
			{key:"Inactive",label:"Inactive",formatter:"myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"FormatID", width: 0}
			];
		break;
	case "Importances":
		defs = [
			{key:"Importance",label:"Importance",editor:"textbox"},
			{key:"Inactive",label:"Inactive",formatter:"myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"ImportanceID", width: 0}
			];
		break;
	case "Locations":
		defs = [
			{key:"Location",label:"Location",editor:"textbox", maxAutoWidth: 200},
			{key:"TUB",label:"TUB",editor:"textbox", maxAutoWidth: 200},
			{key:"ShortName",label:"Short Name",editor:"textbox", maxAutoWidth: 100},
			{key:"Acronym",label:"Acronym",editor:"textbox", maxAutoWidth: 50},
			{key:"Inactive",label:"Inactive",formatter:"myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"IsRepository",label:"Repository",formatter:"myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"IsWorkLocation",label:"Work Loc",formatter:"myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"LocationID", width: 0}
			];
		break;
	case "People":
		this.personNameFormatter = function(elCell, oRecord, oColumn, oData) {
			var personid = oRecord.getData("PersonID");
			elCell.innerHTML = ' <a href="' + baseUrl + 'people/index/personid/' + personid + '">' + oData + '</a>';
	    };
	    
	    // Add the custom formatter to the shortcuts
	    YAHOO.widget.DataTable.Formatter.personName = this.personNameFormatter;

		defs = [
			{key:"DisplayName",label:"Full Name",formatter: "personName", maxAutoWidth: 200},
			{key:"Inactive",label:"Inactive",formatter:"myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"PersonID", width: 0}
			];
		break;
	case "Projects":
		defs = [
			{key:"ProjectName",label:"Project",editor:"textbox", maxAutoWidth: 200},
			{key:"StartDate",label:"Start Date",editor:"textbox", maxAutoWidth: 100},
			{key:"EndDate",label:"End Date",editor:"textbox", maxAutoWidth: 100},
			{key:"ProjectDescription",label:"Description",editor:"textarea"},
			{key:"Inactive",label:"Inactive",formatter:"myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"ProjectID", width: 0}
			];
		break;
	case "Purposes":
		defs = [
			{key:"Purpose",label:"Purpose",editor:"textbox"},
			{key:"Inactive",label:"Inactive",formatter:"myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"PurposeID", width: 0}
			];
		break;
	case "WorkTypes":
		defs = [
			{key:"WorkType",label:"Work Type",editor:"textbox"},
			{key:"Inactive",label:"Inactive",formatter:"myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"WorkTypeID", width: 0}
			];
		break;
	}
	return defs;
}

function getFieldList(selectedvalue)
{
	var fields = [];
	switch (selectedvalue)
	{
	case "Departments":
		fields = ["DepartmentID", "DepartmentName", "Location", "ShortName", "Acronym", "Inactive", "LocationID"];
		break;
	case "Formats":
		fields = ["Format", "Inactive", "FormatID"];
		break;
	case "Importances":
		fields = ["Importance", "Inactive", "ImportanceID"];
		break;
	case "Locations":
		fields = ["Location", "TUB", "ShortName", "Acronym", "Inactive", "IsRepository", "IsWorkLocation", "LocationID"];
		break;
	case "People":
		fields = ["DisplayName", "Inactive", "PersonID"];
		break;
	case "Projects":
		fields = ["ProjectName", "StartDate", "EndDate", "ProjectDescription", "Inactive", "ProjectID"];
		break;
	case "Purposes":
		fields = ["Purpose", "Inactive", "PurposeID"];
		break;
	case "WorkTypes":
		fields = ["WorkType", "Inactive", "WorkTypeID"];
		break;
	}
	return fields;
}

function initButtons(newaccesslevel)
{
	setAccessLevel(newaccesslevel);
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oAddNewButton = new YAHOO.widget.Button("addnewbutton");
		oAddNewButton.on('click', addNew);
		var oExportButton = new YAHOO.widget.Button("exportbutton");
		oExportButton.on('click', exportToExcel);
	}
	
	YAHOO.util.Event.onContentReady("editlistbuttons", onButtonsReady);
	
	//If the access level is not Admin, also load the projects list
	if (accesslevel != 'Admin')
	{
		loadLists('Projects');
	}
	else
	{
		document.getElementById('editlistbuttons').style.display = "none";
	}
}

function addNew()
{
	var selectedvalue = document.getElementById('listtypeselect').value;
	//Calculate the new temp id
	var newid = (editListDataTable.getRecordSet().getLength()+1) * -1;
	switch (selectedvalue)
	{
	case "Departments":
		editListDataTable.addRow(
			{DepartmentID: newid, DepartmentName: "New Department", LocationID: 5, Location: "Other Location (see Comments)", Inactive: 0}
		);
		break;
	case "Formats":
		editListDataTable.addRow(
			{FormatID: newid, Format: "New Format", Inactive: 0}
		);
		break;
	case "Importances":
		editListDataTable.addRow(
			{ImportanceID: newid, Importance: "New Importance", Inactive: 0}
		);
		break;
	case "Locations":
		editListDataTable.addRow(
			{LocationID: newid, Location: "New Location", Inactive: 0}
		);
		break;
	case "Projects":
		editListDataTable.addRow(
			{ProjectID: newid, ProjectName: "New Project", Inactive: 0}
		);
		break;
	case "Purposes":
		editListDataTable.addRow(
			{PurposeID: newid, Purpose: "New Purpose", Inactive: 0}
		);
		break;
	case "WorkTypes":
		editListDataTable.addRow(
			{WorkTypeID: newid, WorkType: "New Work Type", Inactive: 0}
		);
		break;
	}
}

function exportToExcel()
{
	var selectedvalue = document.getElementById('listtypeselect').value;
	if (selectedvalue != '')
	{
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl+'admin/exporttoexcel',
		    	{method: 'post', 
				parameters: {listname: selectedvalue},
				onSuccess: openCSVReport});
	}
	else
	{
		alert('There are no records to export!');
	}
}

function openCSVReport(transport)
{
	var filename = transport.responseText;
	if (filename.length > 0)
	{
		saveCSVReport(filename);
	}
	
}
function saveCSVReport(filename)
{
	window.location = baseUrl + 'admin/promptforsave/filename/' + filename;
	document.body.style.cursor = 'default';
}