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

function initButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oDeleteButton = new YAHOO.widget.Button("deleteselectedbutton");
		oDeleteButton.on('click', deleteSelected);
	}

    YAHOO.util.Event.onContentReady("savedsearchbutton", onButtonsReady);
}

var searchDataTable;
function loadSavedSearches()
{
	function onSavedSearchesReady() {
		var myColumnDefs = getColumnDefs();
		
		var searchurl = baseUrl + "search/populatesavedsearches/includeglobal/0";
		//Show all if it is an administrator
		if (accesslevel == 'Admin')
		{
			searchurl = searchurl + "/includeall/1";
		}
		var myDataSource = new YAHOO.util.DataSource(searchurl);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myDataSource.responseSchema = {
            resultsList: "Result",
            fields: ["SearchName", "DisplayName", "IsGlobal", "SearchID"]
         };
       
        searchDataTable = new YAHOO.widget.DataTable("savedsearchlist", myColumnDefs, myDataSource, {scrollable:true, height:"30em"});
        searchDataTable.subscribe("rowClickEvent",searchDataTable.onEventSelectRow);
        searchDataTable.subscribe("cellDblclickEvent",searchDataTable.onEventShowCellEditor);
        searchDataTable.subscribe("editorBlurEvent", searchDataTable.onEventSaveCellEditor);

        searchDataTable.subscribe("checkboxClickEvent", function(oArgs){ 
        	var elCheckbox = oArgs.target; 
        	var oRecord = this.getRecord(elCheckbox); 
        	var column = searchDataTable.getColumn(elCheckbox);
        	var oldvalue = 1;
        	var value = 0;
        	if (elCheckbox.checked)
        	{
        		value = 1;
        		oldvalue = 0;
        	}
        	
        	oRecord.setData(column.getKey(),value); 
        	var pk = oRecord.getData("SearchID");
        	//Now update the field that was changed.
			var myAjax = new Ajax.Request(baseUrl + "search/updatesavedsearch",
			    {method: 'get', 
					parameters: {column: column.getKey(), pk: pk, newdata: value},
					onComplete: function(transport)
					{
						var val = JSON.parse(transport.responseText);
						//If there is an error message, show it and
						//put the values back
						if (val.ErrorMessage != undefined)
						{
							searchDataTable.updateCell(oRecord, column, oldvalue);
							document.getElementById('searcherrors').innerHTML = val.ErrorMessage;
						}
						else
						{
							document.getElementById('searcherrors').innerHTML = "";
						}
					}
			    }
			);
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
			var elRecord = searchDataTable.getRecord(elRow);
			var pk = elRecord.getData("SearchID");
			//Now update the field that was changed.
			var myAjax = new Ajax.Request(baseUrl + "search/updatesavedsearch",
			    {method: 'get', 
					parameters: {column: elCellColumn.getKey(), pk: pk, newdata: oNewData},
					onComplete: function(transport)
					{
						var val = JSON.parse(transport.responseText);
						//If there is an error message, show it and
						//put the values back
						if (val.ErrorMessage != undefined)
						{
							searchDataTable.updateCell(elRecord, elCellColumn, oOldData);
							document.getElementById('searcherrors').innerHTML = val.ErrorMessage;
						}
						else
						{
							document.getElementById('searcherrors').innerHTML = "";
						}
					}
			    }
			);

        };
        searchDataTable.subscribe("editorSaveEvent", onCellEdit);
        
        searchDataTable.hideColumn("SearchID");
        //IsGlobal settings can only be changed by the administrator.
        if (accesslevel != 'Admin')
        {
        	searchDataTable.hideColumn("DisplayName");
        	searchDataTable.hideColumn("IsGlobal");
	    }
	}

    YAHOO.util.Event.onContentReady("savedsearchlist", onSavedSearchesReady); 
}

function getColumnDefs()
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

	var defs = [
			{key:"SearchName",label:"Caption",editor:"textbox", maxAutoWidth: 400, width: 400},
			{key:"DisplayName", label: "Owner", maxAutoWidth: 100, width: 100},
			{key:"IsGlobal", formatter: "myCustomCheckbox", maxAutoWidth: 50, width: 50},
			{key:"SearchID", width: 0}
			];
	
	return defs;
}

function deleteSelected()
{
	var rows = searchDataTable.getSelectedRows();
	var recordset = searchDataTable.getRecordSet();
	for(var i = 0; i < rows.length; i++)
	{
			var oRecord = recordset.getRecord(rows[i]);
			var searchID = oRecord.getData("SearchID");
			var myAjax = new Ajax.Request(baseUrl + "search/removesearch",
		    		{method: 'get', 
					parameters: {searchID: searchID},
		    		onSuccess: function(transport)
		    		{
						var val = JSON.parse(transport.responseText);
						//If there is an error message, show it and
						//put the values back
						if (val.ErrorMessage != undefined)
						{
							document.getElementById('searcherrors').innerHTML = val.ErrorMessage;
							return;
						}
						else
						{
							searchDataTable.deleteRow(oRecord);
							document.getElementById('searcherrors').innerHTML = "";
						}
		    		}
		    	});
	}
}

