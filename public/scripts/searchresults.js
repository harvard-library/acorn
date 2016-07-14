
function initButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oRefineSearchButton = new YAHOO.widget.Button("refinesearchbutton");
		oRefineSearchButton.on("click", refinesearch);
		var oExportButton = new YAHOO.widget.Button("exportbutton");
		oExportButton.on('click', exportToExcel);
		var oExportAllButton = new YAHOO.widget.Button("exportallbutton");
		oExportAllButton.on('click', exportAllToExcel);
		var oSaveColumnsButton = new YAHOO.widget.Button("savecolumnsbutton");
		oSaveColumnsButton.on('click', function()
				{
					window.location="/search/savecolumnorder";
				}
		);
	}

    YAHOO.util.Event.onContentReady("searchresultsbuttons", onButtonsReady);
}

function refinesearch() {
	window.location="/search/searchrecord";
}

var searchResultsDataTable;
function loadSearchResults(sortcol, dir)
{
	function onSearchResultsReady(hiddencols) {
		
		this.statusCustomFormatter = function(elCell, oRecord, oColumn, oData) {
			var recnum = oRecord.getData("RecordID");
			var type = oRecord.getData("Type");
			if (type == 'Item')
			{
				elCell.innerHTML = ' <a href="../record/recordstatus/recordnumber/' + recnum + '">' + oData + '</a>';
			}
	    };
	    
	    // Add the custom formatter to the shortcuts
	    YAHOO.widget.DataTable.Formatter.statusCustom = this.statusCustomFormatter;

		this.recordCustomFormatter = function(elCell, oRecord, oColumn, oData) {
			var recnum = oRecord.getData("RecordID");
			var type = oRecord.getData("Type");
			if (type == 'Item')
			{
				elCell.innerHTML = ' <a href="../record/index/recordnumber/' + recnum + '">' + oData + '</a>';
			}
			else if (type == 'OSW')
			{
				elCell.innerHTML = ' <a href="../record/osw/recordnumber/' + recnum + '">' + oData + '</a>';
			}
		};
	        
	    // Add the custom formatter to the shortcuts
	    YAHOO.widget.DataTable.Formatter.recordCustom = this.recordCustomFormatter;

	    this.stringDateFormatter = function(elCell, oRecord, oColumn, oData) 
	    {
	    	//mm/dd/yyyy
	        var pattern1 = new RegExp("^[0-9]{1,2}/[0-9]{1,2}/[0-9]{2,4}$");
	        //mm-dd-yyyy
	        var pattern2 = new RegExp("^[0-9]{1,2}-[0-9]{1,2}-[0-9]{2,4}$");
	        //yyyy-mm-dd
	        var pattern3 = new RegExp("^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$");
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
			else
			{
				elCell.innerHTML = oData;
			}
	    };
	    // Add the custom formatter to the shortcuts
	    YAHOO.widget.DataTable.Formatter.stringDate = this.stringDateFormatter;

         //Build the column definitions
         var myColumnDefs =[];
         
         var fields = ['Type', 'RecordID', 'Status', 'CallNumbers', 'Title', 'AuthorArtist',
                       'ProjectName', 'GroupName', 'LoginDate', 'Coordinator', 'Repository', 'WorkAssignedTo', 'WorkDoneBy', 'ReportHours', 
                       'Activity', 'CollectionName', 'DateOfObject', 'ChargeToID',
                       'DepartmentName', 'Format', 'ItemCount', 'VolumeCount', 'SheetCount', 'PhotoCount', 'BoxCount', 'HousingCount',
                       'OSWStatus', 'ProposedBy', 'ProposalDate', 'ProposedHours','Purpose', 
                       'ReportBy', 'ReportDate', 'LogoutDate', 'Importances', 'Storage', 'TemporaryToLocation', 'TemporaryDate', 'TUB', 'WorkType',
                       'WorkStartDate', 'WorkEndDate', 'WorkLocation', 'Curator', 'InsuranceValue', 'Summary'];
         
         for (var i = 0; i < fields.length; i++)
         {
        	 myColumnDefs[i] = getResultColumnDef(fields[i]);         	 
         }
         
         var searchDataSource = new YAHOO.util.DataSource(baseUrl + "search/findsearchresults?");
		 searchDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
         searchDataSource.responseSchema = {
              resultsList: "Result",
              fields: fields,
              metaFields: 
        	       {"totalRecords": "totalRecords"} // Access to value in the server response,

          };
	     
         // Create DataTable
         searchResultsDataTable = new YAHOO.widget.ScrollingDataTable("columnshowhide", 
        		 myColumnDefs, 
        		 searchDataSource, 
        		 {initialRequest: "sort=" + sortcol + "&dir=" + dir + "&startIndex=0&results=50", dynamicData: true, 
        	 		sortedBy:{key:sortcol,dir:dir},
        	 		scrollable:true, height:"30em", width:"100%", 
        	 		paginator: new YAHOO.widget.Paginator({ rowsPerPage:50 }), 
        	 		draggableColumns:true});
         
         searchResultsDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
             oPayload.totalRecords = oResponse.meta.totalRecords;
             var paginator = this.get('paginator');
             paginator.set('totalRecords', oResponse.meta.totalRecords);
 	    	
             document.getElementById('totalrecordcount').innerHTML = "Count: " + oResponse.meta.totalRecords;
             return oPayload;
         };

         for(var i = 0; i < hiddencols.length; i++)
         {
        	 searchResultsDataTable.hideColumn(hiddencols[i]);
         }

            // Shows dialog, creating one when necessary
            var newCols = true;
            var showDlg = function(e) {
                YAHOO.util.Event.stopEvent(e);

                if(newCols) {
                    // Populate Dialog
                    // Using a template to create elements for the SimpleDialog
                	//All possible columns
                    var allColumns = fields;
                    var elPicker = YAHOO.util.Dom.get("dt-dlg-picker");
                    var elTemplateCol = document.createElement("div");
                    YAHOO.util.Dom.addClass(elTemplateCol, "dt-dlg-pickercol");
                    var elTemplateKey = elTemplateCol.appendChild(document.createElement("span"));
                    YAHOO.util.Dom.addClass(elTemplateKey, "dt-dlg-pickerkey");
                    var elTemplateBtns = elTemplateCol.appendChild(document.createElement("span"));
                    YAHOO.util.Dom.addClass(elTemplateBtns, "dt-dlg-pickerbtns");
                    var onclickObj = {fn:handleButtonClick, obj:this, scope:false };
                    
                    // Create one section in the SimpleDialog for each Column
                    var elColumn, elKey, elButton, oButtonGrp;
                    for(var i=0,l=allColumns.length;i<l;i++) {
                        var oColumn = searchResultsDataTable.getColumn(allColumns[i]);
                        
                        // Use the template
                        elColumn = elTemplateCol.cloneNode(true);
                        
                        // Write the Column key
                        elKey = elColumn.firstChild;
                        //elKey.innerHTML = oColumn.getKey();
						elKey.innerHTML = oColumn.label;
                        
                        // Create a ButtonGroup
                        oButtonGrp = new YAHOO.widget.ButtonGroup({ 
                                        id: "buttongrp"+i, 
                                        name: oColumn.getKey(), 
                                        container: elKey.nextSibling
                        });
                        oButtonGrp.addButtons([
                            { label: "Show", value: "Show", checked: ((!oColumn.hidden)), onclick: onclickObj},
                            { label: "Hide", value: "Hide", checked: ((oColumn.hidden)), onclick: onclickObj}
                        ]);
                                        
                        elPicker.appendChild(elColumn);
                    }
                    newCols = false;
            	}
                myDlg.show();
            };
            var hideDlg = function(e) {
                this.hide();
            };
            var handleButtonClick = function(e, oSelf) {
                var sKey = this.get("name");
                if(this.get("value") === "Hide") {
                    // Hides a Column
                	var myAjax = new Ajax.Request(baseUrl + "search/hideresultcolumn",
                    		{method: 'get',
                			parameters: {column: sKey},
                			onComplete: function (){
                				searchResultsDataTable.hideColumn(sKey);
                			}
                    		});
                }
                else {
                    // Shows a Column
                	var myAjax = new Ajax.Request(baseUrl + "search/showresultcolumn",
                    		{method: 'get',
                			parameters: {column: sKey},
                			onComplete: function (){
                				searchResultsDataTable.showColumn(sKey);
                			}
                    		});
                }
            };
            
            // Create the SimpleDialog
            YAHOO.util.Dom.removeClass("dt-dlg", "inprogress");
            var myDlg = new YAHOO.widget.SimpleDialog("dt-dlg", {
                    width: "30em",
    			    visible: false,
    			    modal: true,
    			    buttons: [ 
    					{ text:"Close",  handler:hideDlg }
                    ],
                    fixedcenter: true,
                    constrainToViewport: true
    		});
    		myDlg.render();

            // Nulls out myDlg to force a new one to be created
            searchResultsDataTable.subscribe("columnReorderEvent", function(){
                newCols = true;
                YAHOO.util.Event.purgeElement("dt-dlg-picker", true);
                YAHOO.util.Dom.get("dt-dlg-picker").innerHTML = "";
                updateVisibleColumns();
            }, this);
    		
            
    		// Hook up the SimpleDialog to the link
    		YAHOO.util.Event.addListener("dt-options-link", "click", showDlg, this, true);
    
	}
	
	//Get the visible columns and then display the table
    var myAjax = new Ajax.Request(baseUrl + "search/gethiddencolumns",
    		{method: 'get',
			onComplete: function (transport){
    			var val = JSON.parse(transport.responseText);
				onSearchResultsReady(val.Result);
			}
    		});
    
}

function updateVisibleColumns()
{
	var colset = searchResultsDataTable.getColumnSet();
    var cols = colset.keys;
    var vals = new Array();
    for (var i=0;i < cols.length;i++)
    {
    	if (!cols[i].hidden)
    	{
    		vals[i] = cols[i].getKey();
    	}
    }
    var jsonstring = Object.toJSON(vals);
	
	//Get the visible columns and then display the table
    var myAjax = new Ajax.Request(baseUrl + "search/updatevisiblecolumnorder",
    		{method: 'post',
    		parameters: {columns: jsonstring}});
}

function getResultColumnDef(key)
{
	var def = {};
	switch (key)
	{
	case "Type":
		def = {key:"Type", label:"Type", width: 20, resizeable: true};
		break;
	case "RecordID":
		def = {key:"RecordID",label:"Rec#", formatter: "recordCustom", width: 30, resizeable: true, sortable: true};
		break;
	case "Status":
		def = {key:"Status", label:"Status", formatter: "statusCustom", width: 30, resizeable: true};
		break;
	case "Title":
		def = {key:"Title", label:"Title", width: 150, resizeable: true, sortable: true};
		break;
	case "ProjectName":
		def = {key:"ProjectName", label: "Project", width: 100, resizeable: true, sortable: true};
		break;
	case "GroupName":
		def = {key:"GroupName", label: "Group", width: 100, resizeable: true};
		break;
	case "AuthorArtist":
		def = {key:"AuthorArtist", label: "Author/Artist", width: 100, resizeable: true};
		break;
	case "CallNumbers":
		def = {key:"CallNumbers", label: "Call Numbers", width: 100, resizeable: true};
		break;
	case "Importances":
		def = {key:"Importances", label: "Importances", width: 75, resizeable: true};
		break;
	case "LoginDate":
		def = {key:"LoginDate", label: "Login Date", formatter:"stringDate",width: 50, resizeable: true};
		break;
	case "Coordinator":
		def = {key:"Coordinator", label: "Coordinator", width: 100, resizeable: true, sortable: true};
		break;
	case "Storage":
		def = {key:"Storage", label: "Storage", width: 100, resizeable: true};
		break;
	case "Activity":
		def = {key:"Activity", label: "Activity", width: 75, resizeable: true};
		break;
	case "Repository":
		def = {key:"Repository", label: "Repository", width: 100, resizeable: true, sortable: true};
		break;
	case "ChargeToID":
		def = {key:"ChargeToID", label: "Charge To", width: 100, resizeable: true, sortable: true};
		break;
	case "TUB":
		def = {key:"TUB", label: "TUB", width: 50, resizeable: true, sortable: true};
		break;
	case "DepartmentName":
		def = {key:"DepartmentName", label: "Department", width: 100, resizeable: true, sortable: true};
		break;
	case "ItemCount":
		def = {key:"ItemCount", label: "# Items", width: 40, resizeable: true};
		break;
	case "VolumeCount":
		def = {key:"VolumeCount", label: "# Vols", width: 40, resizeable: true};
		break;
	case "SheetCount":
		def = {key:"SheetCount", label: "# Sheets", width: 40, resizeable: true};
		break;
	case "PhotoCount":
		def = {key:"PhotoCount", label: "# Photos", width: 40, resizeable: true};
		break;
	case "BoxCount":
		def = {key:"BoxCount", label: "# Boxes", width: 40, resizeable: true};
		break;
	case "HousingCount":
		def = {key:"HousingCount", label: "# House", width: 40, resizeable: true};
		break;
	case "CollectionName":
		def = {key:"CollectionName", label: "Coll Name", width: 50, resizeable: true};
		break;
	case "DateOfObject":
		def = {key:"DateOfObject", label: "Date of Obj", width: 50, resizeable: true};
		break;
	case "TemporaryToLocation":
		def = {key:"TemporaryToLocation", label: "Temp To", width: 50, resizeable: true};
		break;
	case "TemporaryDate":
		def = {key:"TemporaryDate", label: "Temp Date", formatter:"stringDate", width: 50, resizeable: true};
		break;
	case "WorkAssignedTo":
		def = {key:"WorkAssignedTo", label: "Wk Assg To", width: 75, resizeable: true};
		break;
	case "ProposalDate":
		def = {key:"ProposalDate", label: "Prop Date", formatter:"stringDate", width: 50, resizeable: true};
		break;
	case "ProposedHours":
		def = {key:"ProposedHours", label: "Prop Hrs", width: 50, resizeable: true};
		break;
	case "ReportDate":
		def = {key:"ReportDate", label: "Rep Date", formatter:"stringDate", width: 50, resizeable: true};
		break;
	case "ReportHours":
		def = {key:"ReportHours", label: "Comp Hrs", width: 50, resizeable: true};
		break;
	case "LogoutDate":
		def = {key:"LogoutDate", label: "Logout Date", formatter:"stringDate", width: 50, resizeable: true};
		break;
	case "OSWStatus":
		def = {key:"OSWStatus", label: "OSW Status", width: 50, resizeable: true};
		break;
	case "Purpose":
		def = {key:"Purpose", label: "Purpose", width: 75, resizeable: true};
		break;
	case "Format":
		def = {key:"Format", label: "Format", width: 75, resizeable: true};
		break;
	case "ProposedBy":
		def = {key:"ProposedBy", label: "Proposed By", width: 100, resizeable: true};
		break;
	case "ReportBy":
		def = {key:"ReportBy", label: "Report By", width: 100, resizeable: true};
		break;
	case "WorkDoneBy":
		def = {key:"WorkDoneBy", label: "Work Done By", width: 100, resizeable: true};
		break;
	case "WorkType":
		def = {key:"WorkType", label: "Work Types", width: 100, resizeable: true};
		break;
	case "WorkStartDate":
		def = {key:"WorkStartDate", label: "OSW Work St", width: 50, resizeable: true};
		break;
	case "WorkEndDate":
		def = {key:"WorkEndDate", label: "OSW Work End", width: 50, resizeable: true};
		break;
	case "Curator":
		def = {key:"Curator", label: "Curator", width: 75, resizeable: true};
		break;
	case "InsuranceValue":
		def = {key:"InsuranceValue", label: "Ins. Val", width: 60, resizeable: true};
		break;
	case "Summary":
		def = {key:"Summary", label: "Trt. Summary", width: 75, resizeable: true};
		break;
	case "WorkLocation":
		def = {key:"WorkLocation", label: "Work Loc", width: 75, resizeable: true};
		break;
	}
	return def;
}

function exportToExcel()
{
	
	var paginator = searchResultsDataTable.get('paginator');
	var pagecount = paginator.getTotalPages();
	if (pagecount > 0)
	{
		var sort = searchResultsDataTable.getState().sortedBy.key;
		var dir = searchResultsDataTable.getState().sortedBy.dir;
		if (dir == 'yui-dt-desc')
		{
			dir = 'desc';
		}
		else
		{
			dir = 'asc';
		}
		//The pagination uses increments of 50.  
		//Current Page starts with 1 whereas the MySQL query
		//starts with 0.  
		var startIndex = (paginator.getCurrentPage()-1)*50;
		
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl+'search/exporttoexcel',
		    	{method: 'post', 
				parameters: {"sort": sort, "dir":dir, "startIndex": startIndex},
				onSuccess: openCSVReport});
	}
	else
	{
		alert('There are no records to export!');
	}
}

function exportAllToExcel()
{
	var paginator = searchResultsDataTable.get('paginator');
	var pagecount = paginator.getTotalPages();
	if (pagecount > 0)
	{
		var sort = searchResultsDataTable.getState().sortedBy.key;
		var dir = searchResultsDataTable.getState().sortedBy.dir;
		if (dir == 'yui-dt-desc')
		{
			dir = 'desc';
		}
		else
		{
			dir = 'asc';
		}
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl+'search/exportalltoexcel',
		    	{method: 'post', 
				parameters: {"sort": sort, "dir":dir},
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
	window.location = baseUrl + 'search/promptforsave/filename/' + filename;
	document.body.style.cursor = 'default';
}
