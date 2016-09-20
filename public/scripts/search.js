/**
 * Copyright 2016 The President and Fellows of Harvard College
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */
function initGroupSearchButtons()
{
	function onSearchButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSearchRecord = new YAHOO.widget.Button("searchgroupbutton", {type: "submit"});
	}

    YAHOO.util.Event.onContentReady("searchgroupbutton", onSearchButtonReady);
 
}

function initSearchButtons()
{
	function onRecordSearchButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSearchRecord = new YAHOO.widget.Button("searchrecordnumberbutton");
		oSearchRecord.on('click', findRecord);
	}

    YAHOO.util.Event.onContentReady("searchrecordnumberbutton", onRecordSearchButtonReady);
   
    function onOSWSearchButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSearchOSW = new YAHOO.widget.Button("searchoswnumberbutton");
		oSearchOSW.on('click', findOSW);
	}

    YAHOO.util.Event.onContentReady("searchoswnumberbutton", onOSWSearchButtonReady);

    function onStatusButtonReady() {

    	//Makes the buttons YUI widgets for a nicer look.
		var oStatus = new YAHOO.widget.Button("recordstatusbutton");
		oStatus.on('click', gotoStatus);
	}

    YAHOO.util.Event.onContentReady("recordstatusbutton", onStatusButtonReady);

	function onAdvancedSearchButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oAdvancedSearchRecord = new YAHOO.widget.Button("searchadvancedbutton");
		oAdvancedSearchRecord.on('click', advancedSearch);
		
		//Makes the buttons YUI widgets for a nicer look.
		var oClearAdvancedSearch = new YAHOO.widget.Button("clearadvancedbutton");
		oClearAdvancedSearch.on('click', function()
		{
				var myAjax = new Ajax.Request(baseUrl + 'search/clearadvancedsearch',
			    		{method: 'get',
			    		onSuccess: function(){
							populateSearch('');
			    		}
			    		});
		});
	}

    YAHOO.util.Event.onContentReady("searchadvancedbutton", onAdvancedSearchButtonReady);
    
    function onSaveSearchButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSaveSearch = new YAHOO.widget.Button("savesearchbutton");
		oSaveSearch.on('click', saveSearch);
	}

    YAHOO.util.Event.onContentReady("savesearchbutton", onSaveSearchButtonReady);
    
    function onOverwriteSearchButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oOverwriteSearch = new YAHOO.widget.Button("overwritesearchbutton");
		var overwriteval = document.getElementById('hiddenoverridesearchnameinput').value;
		if (overwriteval.length > 0)
		{
			oOverwriteSearch.on('click', overwriteSearch);
		}
		else
		{
			oOverwriteSearch.setStyle('display', 'none');
		}
	}

    YAHOO.util.Event.onContentReady("overwritesearchbutton", onOverwriteSearchButtonReady);
   
}

function gotoStatus()
{
	var num = document.getElementById('recordnumberinput').value;
	window.location = baseUrl + "record/recordstatus/recordnumber/" + num;
}

function findRecord()
{
	document.recordsearchform.action = baseUrl + "search/findrecord";
	document.recordsearchform.submit();
}

function findRecordFromEnter(keyevent)
{
	if (keyevent.keyCode == 13)
	{
		findRecord();
	}
}

function findOSW()
{
	document.recordsearchform.action = baseUrl + "search/findosw";
	document.recordsearchform.submit();
}

var advancedsearchDropdownOptions = [{Column: "", ColumnID: ""},
                                     {Column: 'Activity Type', ColumnID: 'Activity'},
                                     {Column: 'Approving Curator', ColumnID: 'ItemIdentification.ApprovingCuratorID'},
                                     {Column: 'Author/Artist Display Name', ColumnID: 'Items.AuthorArtist'},
                                     {Column: 'Call Number', ColumnID: 'CallNumbers.CallNumber'},
                                     {Column: 'Charge To', ColumnID: 'ItemIdentification.ChargeToID'},
                                     {Column: 'Collection Name/Other ID', ColumnID: 'Items.CollectionName'},
                                     {Column: 'Comments', ColumnID: 'ItemIdentification.Comments'},
                                     {Column: 'Coordinator', ColumnID: 'Items.CoordinatorID'},
                                     {Column: 'Curator', ColumnID: 'ItemIdentification.CuratorID'},
                                     {Column: 'Date of Object', ColumnID: 'Items.DateOfObject'},
                                     {Column: 'Department', ColumnID: 'ItemIdentification.DepartmentID'},
                                     {Column: 'FileName', ColumnID: 'Files.FileName'},
                                     {Column: 'Format', ColumnID: 'Items.FormatID'},
                                     {Column: 'Group', ColumnID: 'ItemIdentification.GroupID'},
                                     {Column: 'Importance', ColumnID: 'ItemImportances.ImportanceID'},
                                     {Column: 'Login By', ColumnID: 'ItemLogin.LoginByID'},
                                     {Column: 'Login Date', ColumnID: 'ItemLogin.LoginDate'},
                                     {Column: 'Login To Location', ColumnID: 'ItemLogin.ToLocationID'},
                                     {Column: 'Logout By', ColumnID: 'ItemLogout.LogoutByID'},
                                     {Column: 'Logout Date', ColumnID: 'ItemLogout.LogoutDate'},
                                     {Column: 'OSW Status', ColumnID: 'OSWStatus'},
                                     {Column: 'OSW Work End Date', ColumnID: 'OSW.WorkEndDate'},
                                     {Column: 'OSW Work Start Date', ColumnID: 'OSW.WorkStartDate'},
                                     {Column: 'Project', ColumnID: 'ItemIdentification.ProjectID'},
                                     {Column: 'Proposal By', ColumnID: 'ProposedBy.PersonID'},
                                     {Column: 'Proposal Dimension-Height', ColumnID: 'ItemProposal.Height'},
                                     {Column: 'Proposal Dimension-Width', ColumnID: 'ItemProposal.Width'},
                                     {Column: 'Proposal Dimension-Thickness', ColumnID: 'ItemProposal.Thickness'},
                                     {Column: 'Proposal Treatment', ColumnID: 'ItemProposal.Treatment'},
                                     {Column: 'Purpose', ColumnID: 'ItemIdentification.PurposeID'},
                                     {Column: 'Record Number', ColumnID: 'RecordID'},
                                     {Column: 'Report By', ColumnID: 'ItemReport.ReportByID'},
                                     {Column: 'Report Dimension-Height', ColumnID: 'ItemReport.Height'},
                                     {Column: 'Report Dimension-Width', ColumnID: 'ItemReport.Width'},
                                     {Column: 'Report Dimension-Thickness', ColumnID: 'ItemReport.Thickness'},
                                     {Column: 'Report Treatment', ColumnID: 'ItemReport.Treatment'},
                                     {Column: 'Repository', ColumnID: 'ItemIdentification.HomeLocationID'},
                                     {Column: 'Status', ColumnID: 'ItemStatus'},
                                     {Column: 'Storage', ColumnID: 'Items.Storage'},
                                     {Column: 'Title', ColumnID: 'ItemIdentification.Title'},
                                     {Column: 'TUB', ColumnID: 'Locations.TUB'},
                                     {Column: 'Work Assigned To', ColumnID: 'WorkAssignedTo.PersonID'},
                                     {Column: 'Work Done By', ColumnID: 'ItemConservators.PersonID'},
                                     {Column: 'Work Done By Date', ColumnID: 'ItemConservators.DateCompleted'},
                                     {Column: 'Work Type', ColumnID: 'OSWWorkTypes.WorkTypeID'}
                                     ];

advancedColumnDropdownCellEditor = function(oConfigs) {
	   this._sId = "advancedsearch-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   advancedColumnDropdownCellEditor.superclass.constructor.call(this, "advancedsearchdropdown", oConfigs); 
};

// advancedColumnDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(advancedColumnDropdownCellEditor, acornDropdownCellEditor, {

	/**
	 * Render a form with select element.
	 *
	 * @method renderForm
	 */
	renderForm : function() {
		var elDropdown = this.getContainerEl().appendChild(document.createElement("select"));
	    elDropdown.style.zoom = 1;
	    this.acornDropdown = elDropdown;

    	for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
			dropdownOption = this.acornDropdownOptions[i];
            elOption = document.createElement("option");
            elOption.value = dropdownOption.ColumnID;
            elOption.innerHTML = dropdownOption.Column;
            elOption = elDropdown.appendChild(elOption);       
        }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
	});

//Copy static members to advancedColumnDropdownCellEditor class
YAHOO.lang.augmentObject(advancedColumnDropdownCellEditor, acornDropdownCellEditor);


var myAdvancedSearchDataTable;
function populateSearch(selectedvalue)
{
	var onCellEdit = function(oArgs) {
            var elCell = oArgs.editor.getTdEl();
            var oOldData = oArgs.oldData;
            var oNewData = oArgs.newData;
            var elRow = myAdvancedSearchDataTable.getTrEl(elCell);
    		if (oOldData != oNewData)
    		{
    			var elRecord = oArgs.editor.getRecord();
    			elRecord.setData("ColumnID", oNewData);
    			var colname = elRecord.getData('ColumnID');
    			
    			var defaultvalue = "";
    			//If this is a select field, it should be Equals
    			if (!isTextFieldSearch(colname))
    			{
    				myAdvancedSearchDataTable.updateCell(elRecord, myAdvancedSearchDataTable.getColumn("searchtype"), 'Equals');
    				//Set to blank item
    				myAdvancedSearchDataTable.updateCell(elRecord, myAdvancedSearchDataTable.getColumn("val"), '');
    				myAdvancedSearchDataTable.updateCell(elRecord, myAdvancedSearchDataTable.getColumn("SelectedValue"), '0');
    				defaultvalue = "0";
    			}
    			
    			//If the blank column was filled, add a new row
    			if (oOldData === "")
    			{
    				myAdvancedSearchDataTable.addRow({openparen: "", colname:"", booltype: 'AND', searchtype: 'Equals', val: "", closeparen: "", ColumnID: "", SelectedValue: defaultvalue}); 
    			}
    			//If the new column is blank, delete it
    			else if (oNewData === "")
    			{
    				myAdvancedSearchDataTable.deleteRow(elRow);
    			}
    			
    			//Make sure the first record has a blank booltype;
    			myAdvancedSearchDataTable.updateCell(myAdvancedSearchDataTable.getRecordSet().getRecord(0), myAdvancedSearchDataTable.getColumn("booltype"), '');
    		}
        };
        
        var formatterDispatcher = function (elCell, oRecord, oColumn, oData) {
			var columnname = oRecord.getData('ColumnID');
			//alert(columnname);
			if (isDateFieldSearch(columnname))
			{
				YAHOO.widget.DataTable.formatDate.call(this, elCell, oRecord, oColumn, oData);
			}
			else if (isTextFieldSearch(columnname))
			{
				YAHOO.widget.DataTable.formatText.call(this, elCell, oRecord, oColumn, oData);
			}
			else
			{
				elCell.innerHTML = oData;
			}
		};

    	var columneditor = new advancedColumnDropdownCellEditor({acornDropdownOptions:advancedsearchDropdownOptions, disableBtns:true});
    	columneditor.subscribe("saveEvent", onCellEdit);
    	var myAdvancedSearchDefs = [
                {key:"booltype",label:"",editor:new YAHOO.widget.DropdownCellEditor({dropdownOptions:['AND', 'OR'],defaultValue: "AND",disableBtns:true}), width: 15, maxAutoWidth: 15},
                {key:"openparen",label:"(",editor:new YAHOO.widget.DropdownCellEditor({dropdownOptions:['', '('],disableBtns:true}), width: 5, maxAutoWidth: 5},
                {key:"colname",label:"Column Name",editor:columneditor, width: 200, maxAutoWidth: 200},
                {key:"searchtype",label:"",editor:new YAHOO.widget.DropdownCellEditor({dropdownOptions:['Equals', 'Begins With', 'Contains', '>', '<'],defaultValue: "Equals",disableBtns:true}), maxAutoWidth: 60},
                {key:"val",label:"Value",formatter:formatterDispatcher,editor:"text", width: 200, maxAutoWidth: 200},
                {key:"closeparen",label:")",editor:new YAHOO.widget.DropdownCellEditor({dropdownOptions:['', ')'],disableBtns:true}), width: 5, maxAutoWidth: 5},
                {key:"ColumnID",maxAutoWidth: 0},
                {key:"SelectedValue",maxAutoWidth: 0, defaultValue: ""}
                ];

		var advparams = document.getElementById('advancedsearchparameters').value;
		var searchurl = baseUrl + 'search/loadcurrentsearch/advancedsearchparameters/' + advparams;
		if (selectedvalue.length > 0 && selectedvalue != 0)
		{
			searchurl = baseUrl + 'search/loadsavedsearch/loadsavedsearchselect/' + selectedvalue;
		}
		
		var searchDateParser = function(oData) 
	    {
			if (oData instanceof Date)
			{
				return oData;
			}
	    	var date = null;

	        //mm/dd/yyyy
	        var pattern1 = new RegExp("^[0-9]{1,2}/[0-9]{1,2}/[0-9]{2,4}$");
	        //mm-dd-yyyy
	        var pattern2 = new RegExp("^[0-9]{1,2}-[0-9]{1,2}-[0-9]{2,4}$");
	        //yyyy-mm-dd
	        var pattern3 = new RegExp("^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$");
			if (pattern1.test(oData))
			{
				var datesplit = oData.split("/");
				var month = parseInt(datesplit[0]);
				var day = datesplit[1];
				var year = datesplit[2];
				date = new Date(year, month-1, day);
			}
			else if (pattern2.test(oData))
			{
				var datesplit = oData.split("-");
				var month = parseInt(datesplit[0]);
				var day = datesplit[1];
				var year = datesplit[2];
				date = new Date(year, month-1, day);
			}
			else if (pattern3.test(oData))
			{
				var datesplit = oData.split("-");
				var year = datesplit[0];
				var month = parseInt(datesplit[1]);
				var day = datesplit[2];
				date = new Date(year, month-1, day);
			}

	        // Validate
	        if(date != null && date instanceof Date) 
	        {
	            return date;
	        }
	        else 
	        {
	            return oData;
	        }
	    };
		var myAdvancedSearchDataSource = new YAHOO.util.DataSource(searchurl); 
		myAdvancedSearchDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON; 
		myAdvancedSearchDataSource.responseSchema = { 
				resultsList: "Result", 
				fields: ["openparen", "colname", "booltype", "searchtype", {key:"val", parser: searchDateParser}, "closeparen", "ColumnID", "SelectedValue"] 
		}; 

        myAdvancedSearchDataTable = new YAHOO.widget.DataTable("advancedsearchlist", myAdvancedSearchDefs, myAdvancedSearchDataSource, {scrollable:true, height:"20em", width: "100%"});
		
		myAdvancedSearchDataTable.subscribe("rowClickEvent",myAdvancedSearchDataTable.onEventSelectRow);
        myAdvancedSearchDataTable.subscribe("editorBlurEvent", myAdvancedSearchDataTable.onEventSaveCellEditor);
		
        myAdvancedSearchDataTable.subscribe('cellDblclickEvent',function(ev) {
			var target = YAHOO.util.Event.getTarget(ev);
			var column = this.getColumn(target);
			var rec = this.getRecord(target);
			var colname = rec.getData('ColumnID');
			if (column.key  == 'val') {
				var editor = getColumnEditor(colname);
				column.editor = editor;
				
				if (isDateFieldSearch(colname))
				{
					rec.setData('val', new Date());
				}
				
				// When cell is edited, pulse the color of the row yellow
		        var onDataCellEdit = function(oArgs) {
		            var elCell = oArgs.editor.getTdEl();
		            var oOldData = oArgs.oldData;
		            var oNewData = oArgs.newData;
		            var columnname = oArgs.editor.getColumn().key;
		            if (columnname == 'val')
					{
		            	// Grab the row el and the 2 colors
						if (oOldData != oNewData)
						{
							var elRecord = oArgs.editor.getRecord();
							if (isDateFieldSearch(colname) && oNewData instanceof Date)
							{
								var year = oNewData.getFullYear();
								var month = oNewData.getMonth()+1;
								if (month < 10)
								{
									month = '0' + month;
								}
								var day = oNewData.getDate();
								if (day < 10)
								{
									day = '0' + day;
								}
								oNewData = year + "-" + month + "-" + day;
							}
							elRecord.setData("SelectedValue", oNewData);
						}
					}
		        };
		        column.editor.subscribe("saveEvent", onDataCellEdit);
				
		    }
			else if (column.key == 'searchtype')
			{
				column.editor = getSearchTypeEditor(colname);
			}
			else if (column.key == 'booltype')
			{
				if(myAdvancedSearchDataTable.getRecordSet().getRecordIndex(rec) > 0)
				{
					column.editor = new YAHOO.widget.DropdownCellEditor({dropdownOptions:['AND', 'OR'],defaultValue: "AND",disableBtns:true});
				}
				else
				{
					column.editor = null;
				}
			}
			this.onEventShowCellEditor(ev);
		});
		
        myAdvancedSearchDataTable.hideColumn("ColumnID");
        myAdvancedSearchDataTable.hideColumn("SelectedValue");
}

function getColumnEditor(columnname)
{
	var editor = null;
	if (isDateFieldSearch(columnname))
	{
		var navConfig = {
		        strings : {
		            month: "Choose Month",
		            year: "Enter Year",
		            submit: "OK",
		            cancel: "Cancel",
		            invalidYear: "Please enter a valid year"
		        },
		        monthFormat: YAHOO.widget.Calendar.SHORT,
		        initialFocus: "year"
		  };
		editor = new YAHOO.widget.DateCellEditor({disableBtns: true, navigator:navConfig});
	}
	else if (isTextFieldSearch(columnname))
	{
		editor = new YAHOO.widget.TextboxCellEditor();
	}
	else if (columnname != undefined)
	{
		editor = getDropdownEditor(columnname);
	}
	return editor;
}

function getColumnFormatter(columnname)
{
	var formatter = null;
	if (isDateFieldSearch(columnname))
	{
		formatter = YAHOO.widget.DataTable.formatDate;
	}
	else if (isTextFieldSearch(columnname))
	{
		formatter = new YAHOO.widget.DataTable.formatText;
	}
	else if (columnname != undefined)
	{
		formatter = YAHOO.widget.DataTable.formatDropdown;
	}
	return formatter;
}

groupDropdownCellEditor = function(oConfigs) {
	   this._sId = "group-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   groupDropdownCellEditor.superclass.constructor.call(this, "groupdropdown", oConfigs); 
};

//groupDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(groupDropdownCellEditor, acornDropdownCellEditor, {

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
				onSuccess: function(transport) {
					if (transport.responseJSON != null)
					{
						var value = YAHOO.lang.JSON.parse(transport.responseText);
			    		this.acornDropdownOptions = value.Result;
			    		for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
			    			dropdownOption = this.acornDropdownOptions[i];
				            elOption = document.createElement("option");
				            elOption.value = dropdownOption.GroupID;
				            elOption.innerHTML = dropdownOption.GroupName;
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
	            elOption.value = dropdownOption.GroupID;
	            elOption.innerHTML = dropdownOption.GroupName;
	            elOption = elDropdown.appendChild(elOption);       
	        }
	    }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
	});

//Copy static members to groupDropdownCellEditor class
YAHOO.lang.augmentObject(groupDropdownCellEditor, acornDropdownCellEditor);

projectDropdownCellEditor = function(oConfigs) {
	   this._sId = "project-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   projectDropdownCellEditor.superclass.constructor.call(this, "projectdropdown", oConfigs); 
};

//projectDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(projectDropdownCellEditor, acornDropdownCellEditor, {

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
				onSuccess: function(transport) {
					if (transport.responseJSON != null)
					{
						var value = YAHOO.lang.JSON.parse(transport.responseText);
			    		this.acornDropdownOptions = value.Result;
			    		for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
			    			dropdownOption = this.acornDropdownOptions[i];
				            elOption = document.createElement("option");
				            elOption.value = dropdownOption.ProjectID;
				            elOption.innerHTML = dropdownOption.ProjectName;
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
	            elOption.value = dropdownOption.ProjectID;
	            elOption.innerHTML = dropdownOption.ProjectName;
	            elOption = elDropdown.appendChild(elOption);       
	        }
	    }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
	});

//Copy static members to projectDropdownCellEditor class
YAHOO.lang.augmentObject(projectDropdownCellEditor, acornDropdownCellEditor);

departmentDropdownCellEditor = function(oConfigs) {
	   this._sId = "department-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   departmentDropdownCellEditor.superclass.constructor.call(this, "departmentdropdown", oConfigs); 
};

//departmentDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(departmentDropdownCellEditor, acornDropdownCellEditor, {

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
				onSuccess: function(transport) {
					if (transport.responseJSON != null)
					{
						var value = YAHOO.lang.JSON.parse(transport.responseText);
			    		this.acornDropdownOptions = value.Result;
			    		for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
			    			dropdownOption = this.acornDropdownOptions[i];
				            elOption = document.createElement("option");
				            elOption.value = dropdownOption.DepartmentID;
				            elOption.innerHTML = dropdownOption.DepartmentName;
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
	            elOption.value = dropdownOption.DepartmentID;
	            elOption.innerHTML = dropdownOption.DepartmentName;
	            elOption = elDropdown.appendChild(elOption);       
	        }
	    }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
	});

//Copy static members to departmentDropdownCellEditor class
YAHOO.lang.augmentObject(departmentDropdownCellEditor, acornDropdownCellEditor);

tubDropdownCellEditor = function(oConfigs) {
	   this._sId = "tub-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   tubDropdownCellEditor.superclass.constructor.call(this, "tubdropdown", oConfigs); 
};

//tubDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(tubDropdownCellEditor, acornDropdownCellEditor, {

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
				onSuccess: function(transport) {
					if (transport.responseJSON != null)
					{
						var value = YAHOO.lang.JSON.parse(transport.responseText);
			    		this.acornDropdownOptions = value.Result;
			    		for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
			    			dropdownOption = this.acornDropdownOptions[i];
				            elOption = document.createElement("option");
				            elOption.value = dropdownOption.TUB;
				            elOption.innerHTML = dropdownOption.TUB;
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
	            elOption.value = dropdownOption.TUB;
	            elOption.innerHTML = dropdownOption.TUB;
	            elOption = elDropdown.appendChild(elOption);       
	        }
	    }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
	});

//Copy static members to tubDropdownCellEditor class
YAHOO.lang.augmentObject(tubDropdownCellEditor, acornDropdownCellEditor);

purposeDropdownCellEditor = function(oConfigs) {
	   this._sId = "purpose-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   purposeDropdownCellEditor.superclass.constructor.call(this, "purposedropdown", oConfigs); 
};

//purposeDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(purposeDropdownCellEditor, acornDropdownCellEditor, {

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
				onSuccess: function(transport) {
					if (transport.responseJSON != null)
					{
						var value = YAHOO.lang.JSON.parse(transport.responseText);
			    		this.acornDropdownOptions = value.Result;
			    		for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
			    			dropdownOption = this.acornDropdownOptions[i];
				            elOption = document.createElement("option");
				            elOption.value = dropdownOption.PurposeID;
				            elOption.innerHTML = dropdownOption.Purpose;
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
	            elOption.value = dropdownOption.PurposeID;
	            elOption.innerHTML = dropdownOption.Purpose;
	            elOption = elDropdown.appendChild(elOption);       
	        }
	    }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
	});

//Copy static members to purposeDropdownCellEditor class
YAHOO.lang.augmentObject(purposeDropdownCellEditor, acornDropdownCellEditor);

storageDropdownCellEditor = function(oConfigs) {
	   this._sId = "storage-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   storageDropdownCellEditor.superclass.constructor.call(this, "storagedropdown", oConfigs); 
};

//storageDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(storageDropdownCellEditor, acornDropdownCellEditor, {

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
				onSuccess: function(transport) {
					if (transport.responseJSON != null)
					{
						var value = YAHOO.lang.JSON.parse(transport.responseText);
			    		this.acornDropdownOptions = value.Result;
			    		for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
			    			dropdownOption = this.acornDropdownOptions[i];
				            elOption = document.createElement("option");
				            elOption.value = dropdownOption.Storage;
				            elOption.innerHTML = dropdownOption.Storage;
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
	            elOption.value = dropdownOption.Storage;
	            elOption.innerHTML = dropdownOption.Storage;
	            elOption = elDropdown.appendChild(elOption);       
	        }
	    }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
	});

//Copy static members to storageDropdownCellEditor class
YAHOO.lang.augmentObject(storageDropdownCellEditor, acornDropdownCellEditor);

function getDropdownEditor(columnname)
{
	var editor = null;
	switch(columnname)
	{
	case "Items.CoordinatorID":
		editor = new personDropdownCellEditor({acornUrl: "list/populatestaff/includeinactive/1/includeblank/1/additionalwhere/People.PersonID IN (SELECT DISTINCT CoordinatorID FROM Items)"});
		break;
	case "ItemIdentification.CuratorID":
		editor = new personDropdownCellEditor({acornUrl: "list/populatestaff/includeinactive/1/includeblank/1/includecurators/1/additionalwhere/People.PersonID IN (SELECT DISTINCT CuratorID FROM ItemIdentification)"});
		break;
	case "ItemIdentification.ApprovingCuratorID":
		editor = new personDropdownCellEditor({acornUrl: "list/populatestaff/includeinactive/1/includeblank/1/includecurators/1/additionalwhere/People.PersonID IN (SELECT DISTINCT ApprovingCuratorID FROM ItemIdentification)"});
		break;
	case "ItemIdentification.DepartmentID":
		editor = new departmentDropdownCellEditor({acornUrl: "list/populaterepositorydepartments/includeinactive/1/includeblank/1"});
		break;
	case "Items.FormatID":
		editor = new formatDropdownCellEditor({acornUrl: "list/populateformats/includeinactive/1/includeblank/1"});
		break;
	case "ItemIdentification.GroupID":
		editor = new groupDropdownCellEditor({acornUrl: "list/populategroups/includeinactive/1/includeblank/1"});
		break;
	case "ItemImportances.ImportanceID":
		editor = new importanceDropdownCellEditor({acornUrl: "list/populateimportances/includeinactive/1/includeblank/1"});
		break;
	case "ItemLogin.LoginByID":
		editor = new personDropdownCellEditor({acornUrl: "list/populatestaff/includeinactive/1/includeblank/1/additionalwhere/People.PersonID IN (SELECT DISTINCT LoginByID FROM ItemLogin)"});
		break;
	case "ItemLogout.LogoutByID":
		editor = new personDropdownCellEditor({acornUrl: "list/populatestaff/includeinactive/1/includeblank/1/additionalwhere/People.PersonID IN (SELECT DISTINCT LogoutByID FROM ItemLogout)"});
		break;
	case "ItemLogin.ToLocationID":
		editor = new locationDropdownCellEditor({acornUrl: "list/populatelocations/includeinactive/1/includeblank/1/isrepositorysearch/0/limittorepository/0"});
		break;
	case "ItemIdentification.ProjectID":
		editor = new projectDropdownCellEditor({acornUrl: "list/populateprojects/includeinactive/1/includeblank/1"});
		break;
	case "ProposedBy.PersonID":
		editor = new personDropdownCellEditor({acornUrl: "list/populatestaff/includeinactive/1/includeblank/1/additionalwhere/People.PersonID IN (SELECT DISTINCT PersonID FROM ProposedBy)"});
		break;
	case "ItemReport.ReportByID":
		editor = new personDropdownCellEditor({acornUrl: "list/populatestaff/includeinactive/1/includeblank/1/additionalwhere/People.PersonID IN (SELECT DISTINCT ReportByID FROM ItemReport)"});
		break;
	case "ItemIdentification.HomeLocationID":
		editor = new locationDropdownCellEditor({acornUrl: "list/populatelocations/includeinactive/1/includeblank/1/isrepositorysearch/1"});
		break;
	case "ItemConservators.PersonID":
		editor = new personDropdownCellEditor({acornUrl: "list/populatestaff/includeinactive/1/includeblank/1/additionalwhere/People.PersonID IN (SELECT DISTINCT PersonID FROM ItemConservators)"});
		break;
	case "WorkAssignedTo.PersonID":
		editor = new personDropdownCellEditor({acornUrl: "list/populatestaff/includeinactive/1/includeblank/1/additionalwhere/People.PersonID IN (SELECT DISTINCT PersonID FROM WorkAssignedTo)"});
		break;
	case "OSWWorkTypes.WorkTypeID":
		editor = new workTypeDropdownCellEditor({acornUrl: "list/populateworktypes/includeinactive/1/includeblank/1"});
		break;
	case "Locations.TUB":
		editor = new tubDropdownCellEditor({acornUrl: "list/populatetubs/includeinactive/1/includeblank/0"});
		break;
	case "ItemStatus":
		editor = new YAHOO.widget.DropdownCellEditor({dropdownOptions: ["Pending", "Logged In", "Temp Out", "Done", "Manually Marked as Done"]});
		break;
	case "OSWStatus":
		editor = new YAHOO.widget.DropdownCellEditor({dropdownOptions: ["Open", "Closed"]});
		break;
	case "Activity":
		editor = new YAHOO.widget.DropdownCellEditor({dropdownOptions: ["Admin Only", "Custom Housing Only", "Exam Only", "On-site", "Treatment"]});
		break;
	case "ItemIdentification.PurposeID":
		editor = new purposeDropdownCellEditor({acornUrl: "list/populatepurposes/includeinactive/1/includeblank/1"});
		break;
	case "Items.Storage":
		editor = new storageDropdownCellEditor({acornUrl: "list/populatestorage/includeblank/1"});
		break;
	case "ItemIdentification.ChargeToID":
		editor = new locationDropdownCellEditor({acornUrl: "list/populatechargeto"});
		break;
	default:
		editor = new YAHOO.widget.DropdownCellEditor();
	}
	return editor;
}

function isTextFieldSearch(columnname)
{
	return columnname == 'RecordID' || columnname == 'Items.AuthorArtist' || columnname == 'CallNumbers.CallNumber'
		|| columnname == "Items.CollectionName" || columnname == "ItemIdentification.Comments" || columnname == "Items.DateOfObject"
			|| columnname == "ItemLogin.LoginDate" || columnname == "ItemLogout.LogoutDate"
				|| columnname == "OSW.WorkStartDate" || columnname == "OSW.WorkEndDate" || columnname == "ItemConservators.DateCompleted"
				|| columnname == "ItemProposal.Treatment" || columnname == "ItemReport.Treatment" || columnname == "ItemIdentification.Title"
				|| columnname == "ItemProposal.Height" || columnname == "ItemProposal.Width" || columnname == "ItemProposal.Thickness"
				|| columnname == "ItemReport.Height" || columnname == "ItemReport.Width" || columnname == "ItemReport.Thickness" || columnname == "Files.FileName";
}

function isDateFieldSearch(columnname)
{
	return columnname == "ItemLogin.LoginDate" || columnname == "ItemLogout.LogoutDate" 
		|| columnname == "OSW.WorkStartDate" || columnname == "OSW.WorkEndDate" || columnname == "ItemConservators.DateCompleted";
}

function getSearchTypeEditor(columnname)
{
	var editor = null;
	//If it is a text field, then make the search types available
	//Otherwise, dropdowns are strictly equals
	if (isTextFieldSearch(columnname))
	{
		editor = new YAHOO.widget.DropdownCellEditor({dropdownOptions:['Equals', 'Begins With', 'Contains', '>', '<'],defaultValue: "Equals",disableBtns:true});
	}
	return editor;
}

function getSearchParams()
{
	//Get the rows from the table
	var recordset = myAdvancedSearchDataTable.getRecordSet();
	var matching = new Array();
	//Build the search from each row
	for(var i = 0; i < recordset.getLength(); i++)
	{
		var oRecord = recordset.getRecord(i);
		var columnValue = oRecord.getData('ColumnID');
		var value = oRecord.getData('SelectedValue');
		if (columnValue != "")
		{
			var openparen = oRecord.getData('openparen');
			var booleanValue = null;
			if (i > 0)
			{
				booleanValue = oRecord.getData('booltype');
			}
			var colname = oRecord.getData('colname');
			var valuename = oRecord.getData('val');
			if (isDateFieldSearch(columnValue))
			{
				valuename = value;
			}
			
			var compareValue = oRecord.getData('searchtype');
			var closeparen = oRecord.getData('closeparen');
			matching[i] = {openparen: openparen,
				booltype: booleanValue,
				column: columnValue,
				searchtype: compareValue,
				value: value,
				closeparen: closeparen,
				colname: colname,
				valuename: valuename};
		}
	}
	return matching;
}

function advancedSearch()
{
	var matching = getSearchParams();
	if (matching.length > 0)
	{
		var jsonstring = Object.toJSON(matching);
		//Submit the search
		document.getElementById('advancedsearchparameters').value = jsonstring;
		document.advancedsearchform.submit();
	}
}

function saveSearch()
{
	var matching = getSearchParams();
	if (matching.length > 0)
	{
		var jsonstring = Object.toJSON(matching);
		//Submit the search
		document.getElementById('advancedsearchparameters').value = jsonstring;
		document.advancedsearchform.action = baseUrl + 'search/savesearch';
		document.advancedsearchform.submit();
	}
}

function overwriteSearch()
{
	var matching = getSearchParams();
	if (matching.length > 0)
	{
		var jsonstring = Object.toJSON(matching);
		//Submit the search
		document.getElementById('advancedsearchparameters').value = jsonstring;
		document.advancedsearchform.action = baseUrl + 'search/overwritesearch';
		document.advancedsearchform.submit();
	}
}

function loadSearches()
{
	var selectlist = document.getElementById('loadsavedsearchselect');
	if (selectlist.options.length < 2)
	{
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl + 'search/populatesavedsearches/includeblank/1',
	    		{method: 'get', 
				onComplete: setSearchesInList});
	}
}

function setSearchesInList(transport)
{
	if (transport.responseJSON != null)
	{
		var selectlist = document.getElementById('loadsavedsearchselect');
		var selectedvalue = selectlist.value;
		var returnvalue = JSON.parse(transport.responseText);
		var val = returnvalue.Result;
		for (var i = 0; i < val.length; i++)
		{
			var newoption = new Option(val[i].SearchName, val[i].SearchID);
			selectlist.options[i] = newoption;
		}
		document.body.style.cursor = 'default';
		selectlist.value = selectedvalue;
	}
}