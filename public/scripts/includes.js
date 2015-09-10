function loadRecordIncludes()
{
	loadRecordIdentificationIncludes();
	loadRecordReportIncludes();
}

var itemPending = false;
function loadRecordIdentificationIncludes()
{
	var myAjax = new Ajax.Request(baseUrl + "record/findcurrentrecordstatus",
    		{method: 'get',
			onSuccess: function(transport)
			{
				if (transport.responseJSON != null)
	        	{
					var val = JSON.parse(transport.responseText);
					itemPending = val.Status == 'Pending';
				}
				loadCallNumbers("item");
				//set the message to blank
				document.getElementById('callnumbermessage').innerHTML = "";
				//clear the hidden call number
				document.getElementById('callnumberhidden').value = "";
				//hide the messsage and buttons.
				document.getElementById('idsavecallbuttondiv').style.display = "none";

				loadInitialCounts("item");
				loadWorkAssignedTo("item");
			}
    		}
			);

}

function loadProposalReportIncludes()
{
	loadProposedBy("item");
}

function loadRecordReportIncludes()
{
	loadWorkDoneBy("item");
	loadFinalCounts("item");
	loadImportances("item");
}

function loadOSWIncludes()
{
	loadCallNumbers("osw");
	//set the message to blank
	document.getElementById('callnumbermessage').innerHTML = "";
	//clear the hidden call number
	document.getElementById('callnumberhidden').value = "";
	//hide the messsage and buttons.
	document.getElementById('idsavecallbuttondiv').style.display = "none";
	loadWorkTypes();
	loadWorkDoneBy("osw");
	loadFinalCounts("osw");
}

function loadGroupIncludes()
{
	loadGroupIdentificationIncludes();
	loadGroupReportIncludes();
}

function loadGroupIdentificationIncludes()
{
	loadInitialCounts("group");
	loadWorkAssignedTo("group");
}

function loadGroupProposalIncludes()
{
	loadProposedBy("group");
}

function loadGroupReportIncludes()
{
	loadWorkDoneBy("group");
	loadFinalCounts("group");
	loadImportances("group");
}

function isEditableForRepositoryAdmin()
{
	if (!isBeingEdited)
	{
		return accesslevel == 'Admin' || accesslevel == 'Regular'
			|| (accesslevel == 'Repository Admin' && itemPending);
	}
	return !isBeingEdited;
}

function isEditable()
{
	if (!isBeingEdited)
	{
		return accesslevel == 'Admin' || accesslevel == 'Regular';
	}
	return !isBeingEdited;
}

function loadCurators(recordtype)
{
	function onCuratorListReady() {

		var myColumnDefs = [
                {key:"DisplayName",label:"Name",
                	editor:new personDropdownCellEditor({acornUrl: "list/populatecurators"})},
                	{key:"PersonID"}
          ];
    	var myDataSource = new YAHOO.util.DataSource(baseUrl + "recordincludes/findcurators/recordtype/" + recordtype);
        myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myDataSource.responseSchema = {
            resultsList: "Result",
            fields: ["PersonID", "DisplayName"]
         };

        var myDataTable = new YAHOO.widget.DataTable("curatorlist", myColumnDefs, myDataSource, {scrollable:true, height:"4em"});
        if (isEditable())
        {
	        myDataTable.subscribe("rowClickEvent",myDataTable.onEventSelectRow);
	        myDataTable.subscribe("cellDblclickEvent",myDataTable.onEventShowCellEditor);
	        myDataTable.subscribe("editorBlurEvent", myDataTable.onEventSaveCellEditor); 
	        
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
					
					//Get the id from the record, not the text value 
					var elRecord = myDataTable.getRecord(elRow);
					var oldID = elRecord.getData("PersonID");
					if (oldID != oNewData || oNewData == 0)
					{
						//Set the must confirm page unload variable
						//(see checkModifications.js)
						mustConfirmLeave = true;
						//If the blank column was filled, add a new row
						if (oldID == 0)
						{
							myDataTable.addRow({PersonID: 0, DisplayName: "(Double click to add)"}); 
						}
						//If it was changed remove the old one from the array
						else
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/removecurator",
						    		{method: 'get', 
									parameters: {curatorid: oldID, recordtype: recordtype}});
						}
						//If the new column is blank, delete it
						if (oNewData == 0)
						{
							myDataTable.deleteRow(elRow);
						}
						//Otherwise add it to the array
						else
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/addcurator",
						    		{method: 'get', 
									parameters: {curatorid: oNewData, recordtype: recordtype}});
						}
					}
	        };
	        myDataTable.subscribe("editorSaveEvent", onCellEdit);
        }
        //We don't actually want to see this column.
    	myDataTable.hideColumn("PersonID");
	}

   // YAHOO.util.Event.onContentReady("curatorlist", onCuratorListReady);
    YAHOO.util.Event.onDOMReady(onCuratorListReady);
   
}

function loadWorkAssignedTo(recordtype)
{
	function onWorkAssignedToListReady() {

		var myColumnDefs = [
                {key:"DisplayName",label:"Name",
                	editor:new personDropdownCellEditor({acornUrl: "list/populatestaff"})},
                	{key:"PersonID"}
          ];
    	var myDataSource = new YAHOO.util.DataSource(baseUrl + "recordincludes/findworkassignedto/recordtype/" + recordtype);
        myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myDataSource.responseSchema = {
            resultsList: "Result",
            fields: ["PersonID", "DisplayName"]
         };

        var myDataTable = new YAHOO.widget.DataTable("workassignedtolist", myColumnDefs, myDataSource, {scrollable:true, height:"4em"});
        if (isEditableForRepositoryAdmin())
        {
	        myDataTable.subscribe("rowClickEvent",myDataTable.onEventSelectRow);
	        myDataTable.subscribe("cellDblclickEvent",myDataTable.onEventShowCellEditor);
	        myDataTable.subscribe("editorBlurEvent", myDataTable.onEventSaveCellEditor); 
	        
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
					
					//Get the id from the record, not the text value 
					var elRecord = myDataTable.getRecord(elRow);
					var oldID = elRecord.getData("PersonID");
					if (oldID != oNewData || oNewData == 0)
					{
						//Set the must confirm page unload variable
						//(see checkModifications.js)
						mustConfirmLeave = true;
						//If the blank column was filled, add a new row
						if (oldID == 0)
						{
							myDataTable.addRow({PersonID: 0, DisplayName: "(Double click to add)"}); 
						}
						//If it was changed remove the old one from the array
						else
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/removeworkassignedto",
						    		{method: 'get', 
									parameters: {workassignedtoid: oldID, recordtype: recordtype}});
						}
						//If the new column is blank, delete it
						if (oNewData == 0)
						{
							myDataTable.deleteRow(elRow);
						}
						//Otherwise add it to the array
						else
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/addworkassignedto",
						    		{method: 'get', 
									parameters: {workassignedtoid: oNewData, recordtype: recordtype}});
						}
					}
	        };
	        myDataTable.subscribe("editorSaveEvent", onCellEdit);
        }
        //We don't actually want to see this column.
    	myDataTable.hideColumn("PersonID");
	}

    YAHOO.util.Event.onDOMReady(onWorkAssignedToListReady);
   
}

function loadWorkTypes()
{
	function onWorkTypeListReady() {
		var myWorkTypeColumnDefs = [
                {key:"WorkType",label:"(Add one per row)",
                	editor:new workTypeDropdownCellEditor({acornUrl: "list/populateworktypes"})},
                {key:"WorkTypeID"}
          ];
    	var myWorkTypeDataSource = new YAHOO.util.DataSource(baseUrl + "recordincludes/findworktypes");
    	myWorkTypeDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myWorkTypeDataSource.responseSchema = {
				resultsList: "Result",
	            fields: ["WorkTypeID", "WorkType"]
		};

		var myWorkTypeDataTable = new YAHOO.widget.DataTable("worktypelist", myWorkTypeColumnDefs, myWorkTypeDataSource, {scrollable:true, height:"4em"});
		if (isEditable())
	    {
			myWorkTypeDataTable.subscribe("rowClickEvent",myWorkTypeDataTable.onEventSelectRow);
			myWorkTypeDataTable.subscribe("cellDblclickEvent",myWorkTypeDataTable.onEventShowCellEditor);
			myWorkTypeDataTable.subscribe("editorBlurEvent", myWorkTypeDataTable.onEventSaveCellEditor); 
			
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
					
					//Get the id from the record, not the text value 
					var elRecord = myWorkTypeDataTable.getRecord(elRow);
					var oldID = elRecord.getData("WorkTypeID");
					if (oldID != oNewData || oNewData == 0)
					{
						//Set the must confirm page unload variable
						//(see checkModifications.js)
						mustConfirmLeave = true;
						//If the blank column was filled, add a new row
						if (oldID == 0)
						{
							myWorkTypeDataTable.addRow({WorkTypeID: 0, WorkType: "(Double click to add)"}); 
						}
						//If it was changed remove the old one from the array
						else
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/removeworktype",
						    		{method: 'get', 
									parameters: {worktypeid: oldID}});
						}
						//If the new column is blank, delete it
						if (oNewData == 0)
						{
							myWorkTypeDataTable.deleteRow(elRow);	
						}
						//Otherwise add it to the array
						else
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/addworktype",
						    		{method: 'get', 
									parameters: {worktypeid: oNewData}});
						}
					}
	        };
	        myWorkTypeDataTable.subscribe("editorSaveEvent", onCellEdit);
	    }
        //We don't actually want to see this column.
        myWorkTypeDataTable.hideColumn("WorkTypeID");
	}

    //YAHOO.util.Event.onContentReady("worktypelist", onWorkTypeListReady);   
    YAHOO.util.Event.onDOMReady(onWorkTypeListReady);   
}

function loadWorkDoneBy(recordtype)
{
	function onWorkDoneByListReady() {
		var stringDateFormatter = function(elCell, oRecord, oColumn, oData) 
	    {
			if (oData == null || oData == '')
			{
				return oData;
			}
	    	//mm/dd/yyyy
	        var pattern1 = new RegExp("^[0-9]{2}/[0-9]{2}/[0-9]{4}");
	        //mm-dd-yyyy
	        var pattern2 = new RegExp("^[0-9]{2}-[0-9]{2}-[0-9]{4}");
	        //yyyy-mm-dd
	        var pattern3 = new RegExp("^[0-9]{4}-[0-9]{2}-[0-9]{2}");
			if (pattern1.test(oData))
			{
				var datesplit = oData.split("/");
				var month = datesplit[0];
				var day = datesplit[1];
				var year = datesplit[2];
				if (month.length < 2)
				{
					month = '0' + month;
				}
				if (day.length < 2)
				{
					day = '0' + day;
				}
				elCell.innerHTML = month + "-" + day + "-" + year;
			}
			else if (pattern2.test(oData))
			{
				var datesplit = oData.split("-");
				var month = datesplit[0];
				var day = datesplit[1];
				var year = datesplit[2];
				if (month.length < 2)
				{
					month = '0' + month;
				}
				if (day.length < 2)
				{
					day = '0' + day;
				}
				elCell.innerHTML = month + "-" + day + "-" + year;
			}
			else if (pattern3.test(oData))
			{
				oData = oData.substr(0,10);
				var datesplit = oData.split("-");
				var year = datesplit[0];
				var month = datesplit[1];
				var day = datesplit[2];
				if (month.length < 2)
				{
					month = '0' + month;
				}
				if (day.length < 2)
				{
					day = '0' + day;
				}
				elCell.innerHTML = month + "-" + day + "-" + year;
			}
			else if (oData instanceof Date)
			{
				var year = oData.getFullYear();
				var month = oData.getMonth()+1;
				var day = oData.getDate();
				if (month < 10)
				{
					month = '0' + month;
				}
				if (day < 10)
				{
					day = '0' + day;
				}
				
				elCell.innerHTML = month + "-" + day + "-" + year;
			}
			else
			{
				elCell.innerHTML = oData;
			}
	    };
	    // Add the custom formatter to the shortcuts
	    YAHOO.widget.DataTable.Formatter.stringDate = stringDateFormatter;
	    
		var myWorkByColumnDefs = [
	      {key: "DateCompleted", label: "Date", formatter:"stringDate", editor: new YAHOO.widget.DateCellEditor({disableBtns: true}), width: 50},
          {key:"DisplayName",label:"Work By",editor:new personDropdownCellEditor({acornUrl: "list/populatestaff/includecontractors/1", disableBtns : true}), width: 110},
	      {key:"CompletedHours",label:"Hours",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 50},
	      {key:"PersonID", width: 0},
	      {key:"ConservatorID", width: 0, hidden: true}
		];

		var completedDateParser = function(oData) 
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
	    
		var myWorkByDataSource = new YAHOO.util.DataSource(baseUrl + "recordincludes/findworkdoneby/recordtype/" + recordtype);
		myWorkByDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myWorkByDataSource.responseSchema = {
            resultsList: "Result",
            fields: [
                     {key: "PersonID"}, 
                     {key: "DateCompleted"}, 
                     {key: "DisplayName"}, 
                     {key: "CompletedHours", parser: "number"},
                     {key: "ConservatorID"}
                     ]
         };


		var myWorkByTable = new YAHOO.widget.DataTable("workdonebylist", myWorkByColumnDefs, myWorkByDataSource, {scrollable:true, height:"4em"});
		if (isEditable())
	    {
			myWorkByTable.subscribe("cellDblclickEvent",function(ev) {
				var target = YAHOO.util.Event.getTarget(ev);
				var column = this.getColumn(target);
				var rec = this.getRecord(target);
				if (column.key  == 'DateCompleted') {
					rec.setData('DateCompleted', new Date());	
				}
				myWorkByTable.onEventShowCellEditor(ev);
			});
			myWorkByTable.subscribe("editorBlurEvent", myWorkByTable.onEventSaveCellEditor);
	
		
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
					if (elCellColumn.getKey() == "CompletedHours")
					{
						//Set the must confirm page unload variable
						//(see checkModifications.js)
						mustConfirmLeave = true;
						var elRecord = myWorkByTable.getRecord(elRow);
						if (oNewData == "" || oNewData < 0)
						{
							myWorkByTable.updateCell(elRecord, elCellColumn, 0);
							oNewData = 0;
						}
						var conservatorID = elRecord.getData("ConservatorID");
						var personID = elRecord.getData("PersonID");
						var completeddate = elRecord.getData("DateCompleted");
						if (completeddate != null && completeddate instanceof Date)
						{
							var year = completeddate.getFullYear();
							var month = completeddate.getMonth()+1;
							var day = completeddate.getDate();
							if (month < 10)
							{
								month = '0' + month;
							}
							if (day < 10)
							{
								day = '0' + day;
							}
							completeddate = month + "-" + day + "-" + year;
						}

						if (personID > 0)
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/addworkdoneby",
					    		{method: 'get', 
								parameters: {completeddate: completeddate, workdonebyid: personID, completedhours: oNewData, recordtype: recordtype, conservatorid: conservatorID},
								onComplete: function(transport)
								{
									if (transport.responseJSON != null)
									{
										var retval = JSON.parse(transport.responseText);
										var hours = retval.CompletedHours;
										document.getElementById("actualhours").value = hours;
										elRecord.setData("ConservatorID", retval.ConservatorID);
									}
								}
					    		});
						}
					}
					//The date is being completed
					else if (elCellColumn.getKey() == "DateCompleted")
					{
						//Set the must confirm page unload variable
						//(see checkModifications.js)
						mustConfirmLeave = true;
						var elRecord = myWorkByTable.getRecord(elRow);
						var conservatorID = elRecord.getData("ConservatorID");
						var personID = elRecord.getData("PersonID");
						var completedHours = elRecord.getData("CompletedHours");
						var completeddate = oNewData;
						if (completeddate != null && completeddate instanceof Date)
						{
							var year = completeddate.getFullYear();
							var month = completeddate.getMonth()+1;
							var day = completeddate.getDate();
							if (month < 10)
							{
								month = '0' + month;
							}
							if (day < 10)
							{
								day = '0' + day;
							}
							completeddate = month + "-" + day + "-" + year;
						}

						if (completedHours == null || completedHours < 0 || completedHours == "")
						{
							completedHours = 0;
						}
						if (personID != 0)
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/addworkdoneby",
					    		{method: 'get', 
								parameters: {completeddate: completeddate, workdonebyid: personID, completedhours: completedHours, recordtype: recordtype, conservatorid: conservatorID},
								onComplete: function(transport)
								{
									if (transport.responseJSON != null)
									{
										var retval = JSON.parse(transport.responseText);
										var hours = retval.CompletedHours;
										document.getElementById("actualhours").value = hours;
										elRecord.setData("ConservatorID", retval.ConservatorID);
									}
								}
					    		});
						}
					}
					//The person is being updated.
					else
					{
						//Get the id from the record, not the text value 
						var elRecord = myWorkByTable.getRecord(elRow);
						var conservatorID = elRecord.getData("ConservatorID");
						var completedHours = elRecord.getData("CompletedHours");

						if (completedHours == null || completedHours < 0 || completedHours == "")
						{
							completedHours = 0;
						}
						var oldID = elRecord.getData("PersonID");
						var completeddate = elRecord.getData("DateCompleted");
						var completeddate = oNewData;
						if (completeddate != null && completeddate instanceof Date)
						{
							var year = completeddate.getFullYear();
							var month = completeddate.getMonth()+1;
							var day = completeddate.getDate();
							if (month < 10)
							{
								month = '0' + month;
							}
							if (day < 10)
							{
								day = '0' + day;
							}
							completeddate = month + "-" + day + "-" + year;
						}

						if (oldID != oNewData || oNewData == 0)
						{
							//Set the must confirm page unload variable
							//(see checkModifications.js)
							mustConfirmLeave = true;
							//If the blank column was filled, add a new row
							if (oldID == 0)
							{
								myWorkByTable.addRow({PersonID: 0, DisplayName: "(Double click to add)", ConservatorID: -1, DateCompleted: null}); 
							}
							//If it was changed remove the old one from the array
							else
							{
								var myAjax = new Ajax.Request(baseUrl + "recordincludes/removeworkdoneby",
							    		{method: 'get', 
										parameters: {recordtype: recordtype, conservatorid: conservatorID},
										onComplete: function(transport)
										{
											if (transport.responseJSON != null)
											{
												var retval = JSON.parse(transport.responseText);
												var hours = retval.CompletedHours;
												document.getElementById("actualhours").value = hours;
											}
										}});
							}
							//If the new column is blank, delete it
							if (oNewData == 0)
							{
								myWorkByTable.deleteRow(elRow);
							}
							//Otherwise add it to the array
							else
							{
								var myAjax = new Ajax.Request(baseUrl + "recordincludes/addworkdoneby",
							    		{method: 'get', 
										parameters: {completeddate: completeddate, workdonebyid: oNewData, completedhours: completedHours, recordtype: recordtype, conservatorid: conservatorID},
										onComplete: function(transport){
											elRecord.setData("PersonID", oNewData);
											if (transport.responseJSON != null)
											{
												var retval = JSON.parse(transport.responseText);
												var hours = retval.CompletedHours;
												document.getElementById("actualhours").value = hours;
												elRecord.setData("ConservatorID", retval.ConservatorID);
											}
										}
								});
							}
						}
					}
		    };
		    myWorkByTable.subscribe("editorSaveEvent", onCellEdit);
	    }
	    //We don't actually want to see this column.
	    myWorkByTable.hideColumn("PersonID");
	}
	
    //YAHOO.util.Event.onContentReady("workdonebylist", onWorkDoneByListReady); 
    YAHOO.util.Event.onDOMReady(onWorkDoneByListReady); 
}


function loadProposedBy(recordtype)
{
	function onProposedByListReady() {
		var myProposedByColumnDefs = [
	      {key:"DisplayName",label:"Proposed By",editor:new personDropdownCellEditor({acornUrl: "list/populatestaff/includecontractors/1", disableBtns : true}), width: 110},
	      {key:"PersonID", width: 0}
	    ];

		var myProposedByDataSource = new YAHOO.util.DataSource(baseUrl + "recordincludes/findproposedby/recordtype/" + recordtype);
		myProposedByDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myProposedByDataSource.responseSchema = {
            resultsList: "Result",
            fields: [
                     {key: "PersonID"}, 
                     {key: "DisplayName"}
                     ]
         };


		var myProposedByTable = new YAHOO.widget.DataTable("proposedbylist", myProposedByColumnDefs, myProposedByDataSource, {scrollable:true, height:"4em"});
		if (isEditable())
	    {
			myProposedByTable.subscribe("rowClickEvent",myProposedByTable.onEventSelectRow);
			myProposedByTable.subscribe("cellDblclickEvent",myProposedByTable.onEventShowCellEditor);
			myProposedByTable.subscribe("editorBlurEvent", myProposedByTable.onEventSaveCellEditor);
	
		
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
					
					//The person is being updated.
					var elRecord = myProposedByTable.getRecord(elRow);
					var oldID = elRecord.getData("PersonID");
					if (oldID != oNewData || oNewData == 0)
					{
						//Set the must confirm page unload variable
						//(see checkModifications.js)
						mustConfirmLeave = true;
						//If the blank column was filled, add a new row
						if (oldID == 0)
						{
							myProposedByTable.addRow({PersonID: 0, DisplayName: "(Double click to add)"}); 
						}
						//If it was changed remove the old one from the array
						else
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/removeproposedby",
						    		{method: 'get', 
									parameters: {proposedbyid: oldID, recordtype: recordtype}
									});
						}
						//If the new column is blank, delete it
						if (oNewData == 0)
						{
							myProposedByTable.deleteRow(elRow);
						}
						//Otherwise add it to the array
						else
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/addproposedby",
						    		{method: 'get', 
									parameters: {proposedbyid: oNewData, recordtype: recordtype}
							});
						}
					}
		    };
		    myProposedByTable.subscribe("editorSaveEvent", onCellEdit);
	    }
	    //We don't actually want to see this column.
	    myProposedByTable.hideColumn("PersonID");
	}
	
    YAHOO.util.Event.onDOMReady(onProposedByListReady); 
}

function loadImportances(recordtype)
{
	function onImportanceListReady() {
		var myImportanceColumnDefs = [
              {key:"Importance",label:"Importance",editor: new importanceDropdownCellEditor({acornUrl: "list/populateimportances"})},
              {key: "ImportanceID"}
        ];
		
		var myImportanceDataSource = new YAHOO.util.DataSource(baseUrl + "recordincludes/findimportances/recordtype/" + recordtype);
		myImportanceDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myImportanceDataSource.responseSchema = {
            resultsList: "Result",
    		fields: ["Importance", "ImportanceID"]
    	};

		var myImportanceDataTable = new YAHOO.widget.DataTable("importancelist", myImportanceColumnDefs, myImportanceDataSource, {scrollable:true, height:"4em"});
		if (isEditable())
        {
			myImportanceDataTable.subscribe("rowClickEvent",myImportanceDataTable.onEventSelectRow);
			myImportanceDataTable.subscribe("cellDblclickEvent",myImportanceDataTable.onEventShowCellEditor);
			myImportanceDataTable.subscribe("editorBlurEvent", myImportanceDataTable.onEventSaveCellEditor);
			
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
					
					//Get the id from the record, not the text value 
					var elRecord = myImportanceDataTable.getRecord(elRow);
					var oldID = elRecord.getData("ImportanceID");
					if (oldID != oNewData || oNewData == 0)
					{
						//Set the must confirm page unload variable
						//(see checkModifications.js)
						mustConfirmLeave = true;
						//If the blank column was filled, add a new row
						if (oldID == 0)
						{
							myImportanceDataTable.addRow({ImportanceID: 0, Importance: "(Double click to add)"}); 
						}
						//If it was changed remove the old one from the array
						else
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/removeimportance",
						    		{method: 'get', 
									parameters: {importanceid: oldID, recordtype: recordtype}});
						}
						//If the new column is blank, delete it
						if (oNewData == 0)
						{
							myImportanceDataTable.deleteRow(elRow);
						}
						//Otherwise add it to the array
						else
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/addimportance",
						    		{method: 'get', 
									parameters: {importanceid: oNewData, recordtype: recordtype}});
						}
					}
		    };
		    myImportanceDataTable.subscribe("editorSaveEvent", onCellEdit);
        }
	    //We don't actually want to see this column.
	    myImportanceDataTable.hideColumn("ImportanceID");

	}

    //YAHOO.util.Event.onContentReady("importancelist", onImportanceListReady); 
    YAHOO.util.Event.onDOMReady(onImportanceListReady); 
}

function loadInitialCounts(recordtype)
{
	function onInitialCountsListReady() {
		var myInitialCountColumnDefs = [
            {key:"CountType",label:"Count Type"},
  			{key:"TotalCount",label:"Count",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber})},
          	{key:"Description",label:"Description",editor:"textarea"}
         ];
  		var myInitialCountSource = new YAHOO.util.DataSource(baseUrl + "recordincludes/findinitialcounts/recordtype/" + recordtype);
  		myInitialCountSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
  		myInitialCountSource.responseSchema = {
              resultsList: "Result",
              fields: [
                   {key: "CountType"}, 
                   {key: "TotalCount", parser: "number"}, 
                   {key: "Description"}
               ]
      	};

        var myDataTable2 = new YAHOO.widget.DataTable("countlist", myInitialCountColumnDefs, myInitialCountSource, {scrollable:true, height:"8.5em"});
        if (isEditableForRepositoryAdmin())
        {
	        myDataTable2.subscribe("cellDblclickEvent",myDataTable2.onEventShowCellEditor);
	        myDataTable2.subscribe("editorBlurEvent", myDataTable2.onEventSaveCellEditor);
			
			// When cell is edited, pulse the color of the row yellow
	        var onCountCellEdit = function(oArgs) {
	        	//Set the must confirm page unload variable
				//(see checkModifications.js)
				mustConfirmLeave = true;
				
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
					var elRecord = myDataTable2.getRecord(elRow);
					if (elCellColumn.getKey() == "TotalCount")
					{
						if (oNewData == "" || oNewData < 0)
						{
							myDataTable2.updateCell(elRecord, elCellColumn, 0);
							oNewData = 0;
						}
					}
					var description = elRecord.getData("Description");
					var countType = elRecord.getData("CountType");
					var totalCount = elRecord.getData("TotalCount");
					var myAjax = new Ajax.Request(baseUrl + "recordincludes/updateinitialcount",
					    	{method: 'get', 
							parameters: {counttype: countType, totalcount: totalCount, description: description, recordtype: recordtype}});
	        };
	        myDataTable2.subscribe("editorSaveEvent", onCountCellEdit);
        }
	}
    YAHOO.util.Event.onContentReady("countlist", onInitialCountsListReady); 
}

function loadFinalCounts(recordtype)
{
	function onFinalCountsListReady() {
		var myFinalCountColumnDefs = [
            {key:"CountType",label:"Count Type"},
			{key:"TotalCount",label:"Count",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber})},
        	{key:"Description",label:"Description",editor:"textarea"}
            ];
		var myFinalCountDataSource = new YAHOO.util.DataSource(baseUrl + "recordincludes/findfinalcounts/recordtype/" + recordtype);
	    myFinalCountDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    myFinalCountDataSource.responseSchema = {
            resultsList: "Result",
            fields: [
                     {key: "CountType"}, 
                     {key: "TotalCount", parser: "number"}, 
                     {key: "Description"}
                     ]
    	};

        var myFinalCountDataTable = new YAHOO.widget.DataTable("finalcountlist", myFinalCountColumnDefs, myFinalCountDataSource, {scrollable:true, height:"10em"});
        if (isEditable())
        {
	        myFinalCountDataTable.subscribe("cellDblclickEvent",myFinalCountDataTable.onEventShowCellEditor);
	        myFinalCountDataTable.subscribe("editorBlurEvent", myFinalCountDataTable.onEventSaveCellEditor);
	        
	     // When cell is edited, pulse the color of the row yellow
	        var onCountCellEdit = function(oArgs) {
	            
		        	//Set the must confirm page unload variable
					//(see checkModifications.js)
					mustConfirmLeave = true;
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
					var elRecord = myFinalCountDataTable.getRecord(elRow);
					if (elCellColumn.getKey() == "TotalCount")
					{
						if (oNewData == "" || oNewData < 0)
						{
							var elRecord = myFinalCountDataTable.getRecord(elRow);
							myFinalCountDataTable.updateCell(elRecord, elCellColumn, 0);
							oNewData = 0;
						}
					}
					
					var description = elRecord.getData("Description");
					var countType = elRecord.getData("CountType");
					var totalCount = elRecord.getData("TotalCount");
					var myAjax = new Ajax.Request(baseUrl + "recordincludes/updatefinalcount",
					    	{method: 'get', 
							parameters: {counttype: countType, totalcount: totalCount, description: description, recordtype: recordtype}});
					
	        };
	        myFinalCountDataTable.subscribe("editorSaveEvent", onCountCellEdit);
        }
	}
    //YAHOO.util.Event.onContentReady("finalcountlist", onFinalCountsListReady); 
    YAHOO.util.Event.onDOMReady(onFinalCountsListReady); 
}

function loadCallNumbers(recordtype)
{
	function onCallNumberListReady(notLoggedInAndRepositoryAdmin) {
		var myColumnDefs = [
	        {key:"CallNumber",label:"(Use one per line)",editor:"textbox"}
	    ];
		
		var myDataSource = new YAHOO.util.DataSource(baseUrl + "recordincludes/findcallnumbers/recordtype/" + recordtype);
	    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    myDataSource.responseSchema = {
            resultsList: "Result",
    		fields: ["CallNumber"]
    	};
	
	    var myDataTable = new YAHOO.widget.DataTable("callnumbercells", myColumnDefs, myDataSource, {scrollable:true, height:"4em"});
	    if (isEditableForRepositoryAdmin())
        {
		    myDataTable.subscribe("rowClickEvent",myDataTable.onEventSelectRow);
		    myDataTable.subscribe("cellDblclickEvent",myDataTable.onEventShowCellEditor);
		    myDataTable.subscribe("editorBlurEvent", myDataTable.onEventSaveCellEditor);
		
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
					
					//Get the id from the record, not the text value 
					var elRecord = myDataTable.getRecord(elRow);
					if (oOldData != oNewData || oNewData == "")
					{
						//Set the must confirm page unload variable
						//(see checkModifications.js)
						mustConfirmLeave = true;
						//If the blank column was filled, add a new row
						if (oOldData == "(Double click to add)" || oOldData == "")
						{
							myDataTable.addRow({CallNumber: "(Double click to add)"}); 
						}
						//If the new column is blank, delete it
						if (oNewData == "")
						{
							myDataTable.deleteRow(elRow);
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/removecallnumber",
						    		{method: 'get', 
									parameters: {callnumber: oOldData, recordtype: recordtype}});
						}
						//Otherwise if it is a duplicate, give the user an option to override.
						else
						{
							var myAjax = new Ajax.Request(baseUrl + "recordincludes/addcallnumber",
						    		{method: 'get', 
									parameters: {callnumber: oNewData, recordtype: recordtype},
									onComplete: function(transport)
									{
										if (transport.responseJSON != null)
										{
											var retval = JSON.parse(transport.responseText);
											var dup = retval.Duplicate;
											//If the call number exists in another item,
											//warn the user.
											if (dup != null)
											{
												//set the message
												document.getElementById('callnumbermessage').innerHTML = "The call number, " + oNewData + " exists in " + dup + ".";
												//store the call number in a hidden value so that it can be overridden if the user clicks on override.
												document.getElementById('callnumberhidden').value = oNewData;
												//show the messsage and buttons.
												document.getElementById('idsavecallbuttondiv').style.display = "block";
				
											}
											else
											{
												
											}
										}
									}
									});
						}
					}
		    };
		    myDataTable.subscribe("editorSaveEvent", onCellEdit);
        }
	}
    YAHOO.util.Event.onContentReady("callnumbercells", onCallNumberListReady); 
}