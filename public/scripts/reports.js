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

var calendarChooser = null;
var beginDateChooser = null;
var endDateChooser = null;

function initCompletedByRepositoryContent()
{
	function onButtonReady() {
		//Makes the buttons YUI widgets for a nicer look.
		var oNewReportButton = new YAHOO.widget.Button("createreportbutton");
		oNewReportButton.on('click', createCompletedByRepositoryReport);
	}

	YAHOO.util.Event.onContentReady("createreportbutton", onButtonReady);	
}

function initCombinedCompletedByRepositoryContent()
{
	function onButtonReady() {
		//Makes the buttons YUI widgets for a nicer look.
		var oNewReportButton = new YAHOO.widget.Button("createreportbutton");
		oNewReportButton.on('click', createCombinedCompletedByRepositoryReport);
	}

	YAHOO.util.Event.onContentReady("createreportbutton", onButtonReady);	
}

function initCompletedByPersonContent()
{
	function onButtonReady() {
		//Makes the buttons YUI widgets for a nicer look.
		var oNewReportButton = new YAHOO.widget.Button("createreportbutton");
		oNewReportButton.on('click', createCompletedByPersonReport);
	}

	YAHOO.util.Event.onContentReady("createreportbutton", onButtonReady);	
}

function initCompletedByPersonWithHoursContent()
{
	function onButtonReady() {
		//Makes the buttons YUI widgets for a nicer look.
		var oNewReportButton = new YAHOO.widget.Button("createreportbutton");
		oNewReportButton.on('click', createCompletedByPersonWithHoursReport);
		
		loadWorkDoneBy();
	}

	YAHOO.util.Event.onContentReady("createreportbutton", onButtonReady);	
}

var workDoneByDatatable;
function loadWorkDoneBy()
{
	function onWorkDoneByListReady() {
		var myWorkByColumnDefs = [
	      {key:"DisplayName",label:"",editor:new personDropdownCellEditor({acornUrl: "list/populatestaff/includecontractors/1", disableBtns : true}), width: 110},
	      {key:"PersonID", width: 0}
	    ];

		//No initial data
		var data = [{DisplayName: "(Double click to add)", PersonID: 0}];
		var myWorkByDataSource = new YAHOO.util.DataSource(data);
		myWorkByDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
		myWorkByDataSource.responseSchema = {
            fields: [
                     {key: "PersonID"}, 
                     {key: "DisplayName"}
                     ]
         };


		workDoneByDatatable = new YAHOO.widget.DataTable("workdonebylist", myWorkByColumnDefs, myWorkByDataSource, {scrollable:true, height:"4em"});

		workDoneByDatatable.subscribe("rowClickEvent",workDoneByDatatable.onEventSelectRow);
		workDoneByDatatable.subscribe("cellDblclickEvent",workDoneByDatatable.onEventShowCellEditor);
		workDoneByDatatable.subscribe("editorBlurEvent", workDoneByDatatable.onEventSaveCellEditor);
	
		
			// When cell is edited, pulse the color of the row yellow
		    var onCellEdit = function(oArgs) {          
					var elCell = oArgs.editor.getTdEl();
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
					
					//If it is the completed hours, then update only the hours
					var elCellColumn = oArgs.editor.getColumn();
					//Get the id from the record, not the text value 
					var elRecord = workDoneByDatatable.getRecord(elRow);
					var oldID = elRecord.getData("PersonID");
					if (oldID != oNewData || oNewData == 0)
					{
						elRecord.setData("PersonID", oNewData);
						//If the blank column was filled, add a new row
						if (oldID == 0)
						{
							workDoneByDatatable.addRow({PersonID: 0, DisplayName: "(Double click to add)"}); 
						}
						//If the new column is blank, delete it
						if (oNewData == 0)
						{
							workDoneByDatatable.deleteRow(elRow);
						}
					}
		    };
		    workDoneByDatatable.subscribe("editorSaveEvent", onCellEdit);

	    //We don't actually want to see this column.
	    workDoneByDatatable.hideColumn("PersonID");
	}
	
    //YAHOO.util.Event.onContentReady("workdonebylist", onWorkDoneByListReady); 
    YAHOO.util.Event.onDOMReady(onWorkDoneByListReady); 
}

function initCompletedByProjectContent()
{
	function onButtonReady() {
		//Makes the buttons YUI widgets for a nicer look.
		var oNewReportButton = new YAHOO.widget.Button("createreportbutton");
		oNewReportButton.on('click', createCompletedByProjectReport);
	}

	YAHOO.util.Event.onContentReady("createreportbutton", onButtonReady);	
}

function initCompletedWorkByTubContent()
{
	function onButtonReady() {
		//Makes the buttons YUI widgets for a nicer look.
		var oNewReportButton = new YAHOO.widget.Button("createreportbutton");
		oNewReportButton.on('click', createCompletedWorkByTubReport);
	}

	YAHOO.util.Event.onContentReady("createreportbutton", onButtonReady);	
}

function initCompletedHoursByTubContent()
{
	function onButtonReady() {
		//Makes the buttons YUI widgets for a nicer look.
		var oNewReportButton = new YAHOO.widget.Button("createreportbutton");
		oNewReportButton.on('click', createCompletedHoursByTubReport);
	}

	YAHOO.util.Event.onContentReady("createreportbutton", onButtonReady);	
}

function initARLForWPCContent()
{
	function onButtonReady() {
		//Makes the buttons YUI widgets for a nicer look.
		var oNewReportButton = new YAHOO.widget.Button("createreportbutton");
		oNewReportButton.on('click', createARLForWPCReport);
	}

	YAHOO.util.Event.onContentReady("createreportbutton", onButtonReady);	
}

function initARLForRepositoryContent()
{
	function onButtonReady() {
		//Makes the buttons YUI widgets for a nicer look.
		var oNewReportButton = new YAHOO.widget.Button("createreportbutton");
		oNewReportButton.on('click', createARLForRepositoryReport);
	}

	YAHOO.util.Event.onContentReady("createreportbutton", onButtonReady);	
}

function initWorkDoneByCSVContent()
{
	function onButtonReady() {
		//Makes the buttons YUI widgets for a nicer look.
		var oNewReportButton = new YAHOO.widget.Button("createreportbutton");
		oNewReportButton.on('click', createWorkDoneByCSVReport);
	}

	YAHOO.util.Event.onContentReady("createreportbutton", onButtonReady);	
}

function initDateChoosers()
{
	beginDateChooser = new YAHOO.widget.Calendar("beginDateChooser","beginDateChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
	beginDateChooser.render();
	beginDateChooser.selectEvent.subscribe(handleSelect, beginDateChooser, true);
	
	endDateChooser = new YAHOO.widget.Calendar("endDateChooser","endDateChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
	endDateChooser.render();
	endDateChooser.selectEvent.subscribe(handleSelect, endDateChooser, true);
}

var textfieldinputname = '';
function handleSelect(type, args, obj) {
	var dates = args[0];
	var date = dates[0];
	var year = date[0], month = date[1], day = date[2];
	if (month < 10)
	{
		month = '0' + month;
	}
	if (day < 10)
	{
		day = '0' + day;
	}
	
	document.getElementById(textfieldinputname).value = month + "-" + day + "-" + year;
	calendarChooser.hide();
}

function showCalendarChooser(textfieldinput)
{
	textfieldinputname = textfieldinput;
	if (textfieldinput == 'begindateinput')
	{
		calendarChooser = beginDateChooser;
	}
	else
	{
		calednarChooser = endDateChooser;
	}
	if (calendarChooser != null)
	{
		calendarChooser.show();
	}
}

function createCompletedByRepositoryReport()
{
	var locid = document.getElementById('locationselect').value;
	var startdt = document.getElementById('begindateinput').value;
	var enddt = document.getElementById('enddateinput').value;
	if (locid == "" || locid == undefined || startdt == "" || enddt == "")
	{
		window.location = baseUrl + 'reports/completedrepositoryreport/locationselect/' + locid + '/begindateinput/' + startdt + '/enddateinput/' + enddt + '/';
	}
	else
	{
		var myAjax = new Ajax.Request(baseUrl + "reports/completedrepositoryreport",
    		{method: 'get', 
			parameters: {locationselect: locid, begindateinput: startdt, enddateinput: enddt},
			onSuccess: function(transport)
			{
				if (transport.responseJSON != null)
				{
					var val = JSON.parse(transport.responseText);
					if (val.Filename != undefined)
					{
						//Open the report.
						var filename = val.Filename.trim();
						window.open(baseUrl + 'userreports/pdfreports/' + filename);
					}
				}
			}
    		});
	}
}

function createCombinedCompletedByRepositoryReport()
{
	var locid = document.getElementById('locationselect').value;
	var startdt = document.getElementById('begindateinput').value;
	var enddt = document.getElementById('enddateinput').value;
	if (locid == "" || locid == undefined || startdt == "" || enddt == "")
	{
		window.location = baseUrl + 'reports/combinedcompletedrepositoryreport/locationselect/' + locid + '/begindateinput/' + startdt + '/enddateinput/' + enddt + '/';
	}
	else
	{
		var myAjax = new Ajax.Request(baseUrl + "reports/combinedcompletedrepositoryreport",
    		{method: 'get', 
			parameters: {locationselect: locid, begindateinput: startdt, enddateinput: enddt},
			onSuccess: function(transport)
			{
				if (transport.responseJSON != null)
				{
					var val = JSON.parse(transport.responseText);
					if (val.Filename != undefined)
					{
						//Open the report.
						var filename = val.Filename.trim();
						window.open(baseUrl + 'userreports/pdfreports/' + filename);
					}
				}
			}
    		});
	}
}

function createCompletedByPersonReport()
{
	var persid = document.getElementById('personselect').value;
	var startdt = document.getElementById('begindateinput').value;
	var enddt = document.getElementById('enddateinput').value;
	if (persid == "" || persid == undefined || startdt == "" || enddt == "")
	{
		window.location = baseUrl + 'reports/completedpersonreport/personselect/' + persid + '/begindateinput/' + startdt + '/enddateinput/' + enddt + '/';
	}
	else
	{
		var myAjax = new Ajax.Request(baseUrl + "reports/completedpersonreport",
    		{method: 'get', 
			parameters: {personselect: persid, begindateinput: startdt, enddateinput: enddt},
			onSuccess: function(transport)
			{
				if (transport.responseJSON != null)
				{
					var val = JSON.parse(transport.responseText);
					if (val.Filename != undefined)
					{
						//Open the report.
						var filename = val.Filename.trim();
						window.open(baseUrl + 'userreports/pdfreports/' + filename);
					}
				}
			}
    		});
	}
}

function getWorkByJSONList()
{
	var records = workDoneByDatatable.getRecordSet().getRecords();
	var workby = new Array();
	for(var i = 0; i < records.length; i++)
	{
		var personID = records[i].getData("PersonID");
		if (personID > 0)
		{
			workby[personID] = personID;
		}
	}
	var retval = null;
	if (workby.length > 0)
	{
		retval = Object.toJSON(workby);
	}
	return retval;
}

function createCompletedByPersonWithHoursReport()
{
	//var persid = document.getElementById('personselect').value;
	var people = getWorkByJSONList();
	var startdt = document.getElementById('begindateinput').value;
	var enddt = document.getElementById('enddateinput').value;
	if (people == null || startdt == "" || enddt == "")
	{
		window.location = baseUrl + 'reports/completedpersonwithhoursreport/begindateinput/' + startdt + '/enddateinput/' + enddt + '/';
	}
	else
	{
		var myAjax = new Ajax.Request(baseUrl + "reports/completedpersonwithhoursreport",
    		{method: 'get', 
			parameters: {peoplelist: people, begindateinput: startdt, enddateinput: enddt},
			onSuccess: function(transport)
			{
				if (transport.responseJSON != null)
				{
					var val = JSON.parse(transport.responseText);
					if (val.Filename != undefined)
					{
						//Open the report.
						var filename = val.Filename.trim();
						window.open(baseUrl + 'userreports/pdfreports/' + filename);
					}
				}
			}
    		});
	}
}

function createCompletedByProjectReport()
{
	var projid = document.getElementById('projectselect').value;
	var startdt = document.getElementById('begindateinput').value;
	var enddt = document.getElementById('enddateinput').value;
	if (projid == "" || projid == undefined || startdt == "" || enddt == "")
	{
		window.location = baseUrl + 'reports/completedprojectreport/projectselect/' + projid + '/begindateinput/' + startdt + '/enddateinput/' + enddt + '/';
	}
	else
	{
		var myAjax = new Ajax.Request(baseUrl + "reports/completedprojectreport",
    		{method: 'get', 
			parameters: {projectselect: projid, begindateinput: startdt, enddateinput: enddt},
			onSuccess: function(transport)
			{
				if (transport.responseJSON != null)
				{
					var val = JSON.parse(transport.responseText);
					if (val.Filename != undefined)
					{
						//Open the report.
						var filename = val.Filename.trim();
						window.open(baseUrl + 'userreports/pdfreports/' + filename);
					}
				}
			}
    		});
	}
}

function createCompletedWorkByTubReport()
{
	var startdt = document.getElementById('begindateinput').value;
	var enddt = document.getElementById('enddateinput').value;
	if (startdt == "" || enddt == "")
	{
		window.location = baseUrl + 'reports/completedtubworkreport/begindateinput/' + startdt + '/enddateinput/' + enddt + '/';
	}
	else
	{
		var myAjax = new Ajax.Request(baseUrl + "reports/completedtubworkreport",
    		{method: 'get', 
			parameters: {begindateinput: startdt, enddateinput: enddt},
			onSuccess: function(transport)
			{
				if (transport.responseJSON != null)
				{
					var val = JSON.parse(transport.responseText);
					if (val.Filename != undefined)
					{
						//Open the report.
						var filename = val.Filename.trim();
						window.open(baseUrl + 'userreports/pdfreports/' + filename);
					}
				}
			}
    		});
	}
}

function createCompletedHoursByTubReport()
{
	var startdt = document.getElementById('begindateinput').value;
	var enddt = document.getElementById('enddateinput').value;
	if (startdt == "" || enddt == "")
	{
		window.location = baseUrl + 'reports/completedtubhoursreport/begindateinput/' + startdt + '/enddateinput/' + enddt + '/';
	}
	else
	{
		var myAjax = new Ajax.Request(baseUrl + "reports/completedtubhoursreport",
    		{method: 'get', 
			parameters: {begindateinput: startdt, enddateinput: enddt},
			onSuccess: function(transport)
			{
				if (transport.responseJSON != null)
				{
					var val = JSON.parse(transport.responseText);
					if (val.Filename != undefined)
					{
						//Open the report.
						var filename = val.Filename.trim();
						window.open(baseUrl + 'userreports/pdfreports/' + filename);
					}
				}
			}
    		});
	}
}

function createARLForWPCReport()
{
	var startdt = document.getElementById('begindateinput').value;
	var enddt = document.getElementById('enddateinput').value;
	if (startdt == "" || enddt == "")
	{
		window.location = baseUrl + 'reports/arlforwpcreport/begindateinput/' + startdt + '/enddateinput/' + enddt + '/';
	}
	else
	{
		var myAjax = new Ajax.Request(baseUrl + "reports/arlforwpcreport",
    		{method: 'get', 
			parameters: {begindateinput: startdt, enddateinput: enddt},
			onSuccess: function(transport)
			{
				if (transport.responseJSON != null)
				{
					var val = JSON.parse(transport.responseText);
					if (val.Filename != undefined)
					{
						//Open the report.
						var filename = val.Filename.trim();
						window.open(baseUrl + 'userreports/pdfreports/' + filename);
					}
				}
			}
    		});
	}
}

function createARLForRepositoryReport()
{
	var locid = document.getElementById('locationselect').value;
	var startdt = document.getElementById('begindateinput').value;
	var enddt = document.getElementById('enddateinput').value;
	if (locid == "" || locid == undefined || startdt == "" || enddt == "")
	{
		window.location = baseUrl + 'reports/arlforrepositoryreport/locationselect/' + locid + '/begindateinput/' + startdt + '/enddateinput/' + enddt + '/';
	}
	else
	{
		var myAjax = new Ajax.Request(baseUrl + "reports/arlforrepositoryreport",
    		{method: 'get', 
			parameters: {locationselect: locid, begindateinput: startdt, enddateinput: enddt},
			onSuccess: function(transport)
			{
				if (transport.responseJSON != null)
				{
					var val = JSON.parse(transport.responseText);
					if (val.Filename != undefined)
					{
						//Open the report.
						var filename = val.Filename.trim();
						window.open(baseUrl + 'userreports/pdfreports/' + filename);
					}
				}
			}
    		});
	}
}

function createWorkDoneByCSVReport()
{
	var startdt = document.getElementById('begindateinput').value;
	var enddt = document.getElementById('enddateinput').value;
	if (startdt == "" || enddt == "")
	{
		window.location = baseUrl + 'reports/workdonebyreport/begindateinput/' + startdt + '/enddateinput/' + enddt + '/';
	}
	else
	{
		var myAjax = new Ajax.Request(baseUrl + "reports/workdonebyreport",
    		{method: 'get', 
			parameters: {begindateinput: startdt, enddateinput: enddt},
			onSuccess: function(transport)
			{
				if (transport.responseJSON != null)
				{
					var val = JSON.parse(transport.responseText);
					if (val.Filename != undefined)
					{
						window.location = baseUrl + 'search/promptforsave/filename/' + val.Filename;
						document.body.style.cursor = 'default';
					}
				}
			}
    		});
	}
}
