
preservationDropdownCellEditor = function(oConfigs) {
	   this._sId = "preservation-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   preservationDropdownCellEditor.superclass.constructor.call(this, "preservationdropdown", oConfigs); 
};

// preservationDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(preservationDropdownCellEditor, acornDropdownCellEditor, {
	/**
	 * Render a form with select element.
	 *
	 * @method renderForm
	 */
	renderForm : function() {
		var elDropdown = this.getContainerEl().appendChild(document.createElement("select"));
	    elDropdown.style.zoom = 1;
	    this.acornDropdown = elDropdown;
	    if (this.acornDropdownOptions == null)
	    {
	    	var myAjax = new Ajax.Request(baseUrl + this.acornUrl,
	    		{method: 'get', 
				parameters: {includeinactive: 0, includeblank: 0},
				onSuccess: function(transport) {
					if (transport.responseJSON != null)
					{
						var value = YAHOO.lang.JSON.parse(transport.responseText);
			    		this.acornDropdownOptions = value.Result;
			    		for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
			    			dropdownOption = this.acornDropdownOptions[i];
				            elOption = document.createElement("option");
				            elOption.value = dropdownOption.AutotextID;
				            elOption.innerHTML = dropdownOption.Caption;
				            elOption = elDropdown.appendChild(elOption);
				        }
					}
					else
					{
						this.acornDropdownOptions = null;
					}
				  }
	    		});
	    }
	    else
	    {
	    	for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
				dropdownOption = this.acornDropdownOptions[i];
	            elOption = document.createElement("option");
	            elOption.value = dropdownOption.AutotextID;
	            elOption.innerHTML = dropdownOption.Caption;
	            elOption = elDropdown.appendChild(elOption);       
	        }
	    }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
	});

//Copy static members to preservationDropdownCellEditor class
YAHOO.lang.augmentObject(preservationDropdownCellEditor, acornDropdownCellEditor);

var customTextDataTable;
function loadList(selectedvalue)
{
	if (selectedvalue != '')
	{
		function onEditListsReady() {
			var myColumnDefs = getColumnDefs(selectedvalue);
			var fieldList = getFieldList(selectedvalue);
			
			var autotexturl = baseUrl + "list/populateautotext/autotexttype/" + selectedvalue + "/includecopytreatment/0/includeglobal/0";
			//Show all if it is an administrator
			if (accesslevel == 'Admin')
			{
				autotexturl = autotexturl + "/includeall/1";
			}
			var myDataSource = new YAHOO.util.DataSource(autotexturl);
			myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
			myDataSource.responseSchema = {
	            resultsList: "Result",
	            fields: fieldList
	         };
	       
			// Define a custom row formatter function
			var myRowFormatter = function(elTr, oRecord) {
				//If it is a new row, highlight it until it is updated.
			    if (oRecord.getData("AutotextID") < 0) {
			    	YAHOO.util.Dom.addClass(elTr, 'newitem');
			    }
			    return true;
			};
			
	        customTextDataTable = new YAHOO.widget.DataTable("customtextlist", myColumnDefs, myDataSource, {scrollable:true, height:"30em", formatRow: myRowFormatter});
	        customTextDataTable.subscribe("rowClickEvent",customTextDataTable.onEventSelectRow);
	        customTextDataTable.subscribe("cellDblclickEvent",customTextDataTable.onEventShowCellEditor);
	        customTextDataTable.subscribe("editorBlurEvent", customTextDataTable.onEventSaveCellEditor);
	
	        customTextDataTable.subscribe("checkboxClickEvent", function(oArgs){ 
	        	var elCheckbox = oArgs.target; 
	        	var oRecord = this.getRecord(elCheckbox); 
	        	var column = customTextDataTable.getColumn(elCheckbox);
	        	var oldvalue = 1;
	        	var value = 0;
	        	if (elCheckbox.checked)
	        	{
	        		value = 1;
	        		oldvalue = 0;
	        	}
	        	
	        	oRecord.setData(column.getKey(),value); 
	        	var pk = oRecord.getData("AutotextID");
	        	if (pk > 0)
	        	{
	        		//Now update the field that was changed.
					var myAjax = new Ajax.Request(baseUrl + "list/updateautotext",
					    {method: 'get', 
							parameters: {column: column.getKey(), pk: pk, newdata: value, autotexttype: selectedvalue},
							onComplete: function(transport)
							{
								var val = JSON.parse(transport.responseText);
								//If there is an error message, show it and
								//put the values back
								if (val.ErrorMessage != undefined)
								{
									customTextDataTable.updateCell(oRecord, column, oldvalue);
									document.getElementById('customtexterrors').innerHTML = val.ErrorMessage;
								}
								else
								{
									document.getElementById('customtexterrors').innerHTML = "";
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
				var elRecord = customTextDataTable.getRecord(elRow);
				var pk = elRecord.getData("AutotextID");
				//If the pk is < 0, it is new. We have to add the entire new item
				if (pk < 0)
				{
					var params = {
							AutotextType: selectedvalue,
							AutotextID: elRecord.getData("AutotextID"),
							Caption: elRecord.getData("Caption"),
							Autotext: elRecord.getData("Autotext"),
							IsGlobal: elRecord.getData("IsGlobal"),
							DependentAutotextID: elRecord.getData("DependentAutotextID")
							};
					var myAjax = new Ajax.Request(baseUrl + "list/saveautotext",
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
										customTextDataTable.updateCell(elRecord, elCellColumn, oOldData);
										document.getElementById('customtexterrors').innerHTML = val.ErrorMessage;
									}
									else
									{
										pk = val.PrimaryKey;
										customTextDataTable.updateCell(elRecord, customTextDataTable.getColumn("AutotextID"), pk);
										document.getElementById('customtexterrors').innerHTML = "";
										customTextDataTable.render();
									}
								}
							}
				    	}
					);
				}
				else
				{
					//Now update the field that was changed.
					var myAjax = new Ajax.Request(baseUrl + "list/updateautotext",
					    {method: 'get', 
							parameters: {column: elCellColumn.getKey(), pk: pk, newdata: oNewData, autotexttype: selectedvalue},
							onComplete: function(transport)
							{
								var val = JSON.parse(transport.responseText);
								//If there is an error message, show it and
								//put the values back
								if (val.ErrorMessage != undefined)
								{
									customTextDataTable.updateCell(elRecord, elCellColumn, oOldData);
									document.getElementById('customtexterrors').innerHTML = val.ErrorMessage;
								}
								else
								{
									document.getElementById('customtexterrors').innerHTML = "";
									if (selectedvalue == "Dependency" && elCellColumn.getKey() == "DependentAutotext")
									{
										customTextDataTable.updateCell(elRecord, customTextDataTable.getColumn("DependentAutotextID"), oNewData);
									}
								}
							}
					    }
					);
				}
	        };
	        customTextDataTable.subscribe("editorSaveEvent", onCellEdit);
	        
	        customTextDataTable.hideColumn("DependentAutotextID");
	        customTextDataTable.hideColumn("AutotextID");
	        //IsGlobal settings can only be changed by the administrator.
	        if (accesslevel != 'Admin')
	        {
	        	customTextDataTable.hideColumn("DisplayName");
	        	customTextDataTable.hideColumn("IsGlobal");
		        }
		}
	
	    YAHOO.util.Event.onContentReady("customtextlist", onEditListsReady); 
	    document.getElementById('customtextlist').style.display = "block";
	    document.getElementById('customtextbuttons').style.display = "block";
	}
	else
	{
		document.getElementById('customtextlist').style.display = "none";
		document.getElementById('customtextbuttons').style.display = "none";
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
	if (selectedvalue == "Dependency")
	{
		defs = [
			{key:"Caption",label:"Caption",editor:"textbox", maxAutoWidth: 100, width: 100},
			{key:"Autotext",label:"Autotext", editor:"textarea", maxAutoWidth: 400},
			{key:"DependentAutotext",label:"Pres Level 1",editor:new preservationDropdownCellEditor({acornUrl: "list/populateautotext/autotexttype/Preservation/includeall/1"}), maxAutoWidth: 75, width: 75},
			{key:"DisplayName", label: "Owner", maxAutoWidth: 100, width: 100},
			{key:"IsGlobal", formatter: "myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"DependentAutotextID", maxAutoWidth: 0, width: 0},
			{key:"AutotextID", width: 0}
			];
	}
	else
	{
		defs = [
			{key:"Caption",label:"Caption",editor:"textbox", maxAutoWidth: 100, width: 100},
			{key:"Autotext",label:"Autotext", editor: "textarea", maxAutoWidth: 500},
			{key:"DisplayName", label: "Owner", maxAutoWidth: 100, width: 100},
			{key:"IsGlobal", formatter: "myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"DependentAutotextID", maxAutoWidth: 0, width: 0},
			{key:"AutotextID", width: 0}
			];
	}
	return defs;
}

function getFieldList(selectedvalue)
{
	var fields = [];
	if (selectedvalue == "Dependency")
	{
		fields = ["Caption", "Autotext", "DependentAutotext", "DisplayName", "IsGlobal", "DependentAutotextID", "AutotextID"];
	}
	else
	{
		fields = ["Caption", "Autotext", "DisplayName", "IsGlobal", "DependentAutotextID", "AutotextID"];
	}
	return fields;
}

function initButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oAddNewButton = new YAHOO.widget.Button("addnewbutton");
		oAddNewButton.on('click', addNew);
		var oDeleteButton = new YAHOO.widget.Button("deleteselectedbutton");
		oDeleteButton.on('click', deleteSelected);
	}
	
	YAHOO.util.Event.onContentReady("customtextbuttons", onButtonsReady);
	document.getElementById('customtextbuttons').style.display = "none";
}

function addNew()
{
	var selectedtext = document.getElementById('listtypeselect').value;
	//Calculate the new temp id
	var newid = (customTextDataTable.getRecordSet().getLength()+1) * -1;
	//using an arbitrary number for PersonID to 'trick' the formatter.
	customTextDataTable.addRow(
		{AutotextID: newid, Caption: "New " + selectedtext, DisplayName: '', IsGlobal: 0, DependentAutotextID: null}
	);
}

function deleteSelected()
{
	var rows = customTextDataTable.getSelectedRows();
	var recordset = customTextDataTable.getRecordSet();
	var autotexttype = document.getElementById('listtypeselect').value;
	for(var i = 0; i < rows.length; i++)
	{
			var oRecord = recordset.getRecord(rows[i]);
			var autotextID = oRecord.getData("AutotextID");
			var myAjax = new Ajax.Request(baseUrl + "list/removeautotext",
		    		{method: 'get', 
					parameters: {autotextID: autotextID, autotexttype: autotexttype},
		    		onSuccess: function(transport)
		    		{
						var val = JSON.parse(transport.responseText);
						//If there is an error message, show it and
						//put the values back
						if (val.ErrorMessage != undefined)
						{
							document.getElementById('customtexterrors').innerHTML = val.ErrorMessage;
							return;
						}
						else
						{
							customTextDataTable.deleteRow(oRecord);
							document.getElementById('customtexterrors').innerHTML = "";
						}
		    		}
		    	});
	}
}