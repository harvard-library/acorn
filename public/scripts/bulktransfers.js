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
	
	
var matchingBulkLoginDataTable;
function loadMatchingBulkLoginRecords(selectedvalue, repository)
{
		var loginColumnDefs = [
              {key:"ItemID",label:"Rec #", width: 20},
              {key:"Department", label: "Rep/Dept", width: 100},
              {key:"Curator", label: "Curator", width: 100},
              {key:"CallNumbers", label: "Call Nums", width: 50},
              {key: "Title", label: "Title",width: 100},
              {key: "AuthorArtist", label: "Author/Artist", width: 75},
              {key: "DateOfObject", label: "Date of Obj", width: 50},
              {key: "Comments", label: "Comments", width: 100}
              ];
  		 
          var loginDataSource = new YAHOO.util.DataSource(baseUrl + "transfers/findmatchingloginrecords?selectedlocation=" + selectedvalue + "&repository=" + repository + "&");
          loginDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
          loginDataSource.responseSchema = {
              resultsList: "Result",
              fields: ["ItemID", "Department", "Curator", "CallNumbers", "Title", "AuthorArtist", "DateOfObject", 
                       "VolumeCount", "SheetCount", "PhotoCount", "BoxCount", "OtherCount", "Comments"],
              metaFields: 
        	       {"totalRecords": "totalRecords"} // Access to value in the server response,

           };

          matchingBulkLoginDataTable = new YAHOO.widget.DataTable("matchingbulktransferrecords", loginColumnDefs, loginDataSource, {initialRequest: "startIndex=0&results=25", dynamicData: true, scrollable:true, height:"12em", paginator: new YAHOO.widget.Paginator({ rowsPerPage:25 })});
          matchingBulkLoginDataTable.subscribe("rowClickEvent",matchingBulkLoginDataTable.onEventSelectRow);

}

var matchingBulkLogoutDataTable;
function loadMatchingBulkLogoutRecords(selectedvalue, repository)
{
	
		var logoutColumnDefs = [
              {key:"ItemID",label:"Rec #", width: 20},
              {key:"Department", label: "Rep/Dept", width: 100},
              {key:"Curator", label: "Curator", width: 100},
              {key:"CallNumbers", label: "Call Nums", width: 50},
              {key: "Title", label: "Title",width: 100},
              {key: "AuthorArtist", label: "Author/Artist", width: 75},
              {key: "DateOfObject", label: "Date of Obj", width: 50},
              {key: "Comments", label: "Comments", width: 100}
              ];
  		 
          var logoutDataSource = new YAHOO.util.DataSource(baseUrl + "transfers/findmatchinglogoutrecords?selectedlocation=" + selectedvalue + "&");
          logoutDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
          logoutDataSource.responseSchema = {
              resultsList: "Result",
              fields: ["ItemID", "Department", "Curator", "CallNumbers", "Title", "AuthorArtist", "DateOfObject", 
                       "VolumeCount", "SheetCount", "PhotoCount", "BoxCount", "OtherCount", "Comments"],
              metaFields: {
              	"totalRecords": "totalRecords" // Access to value in the server response
          	  }	
           };

          matchingBulkLogoutDataTable = new YAHOO.widget.DataTable("matchingbulktransferrecords", logoutColumnDefs, logoutDataSource, {initialRequest: "startIndex=0&results=25", dynamicData: true, scrollable:true, height:"12em", paginator: new YAHOO.widget.Paginator({ rowsPerPage:25})});
          matchingBulkLogoutDataTable.subscribe("rowClickEvent",matchingBulkLogoutDataTable.onEventSelectRow);
}

var taskListCallback = {
		 success: function(sRequest, oResponse, oPayload){
	      	var paginator = this.get('paginator');
	     	this.onDataReturnInitializeTable(sRequest, oResponse, oPayload);
	    	paginator.set('totalRecords', oResponse.meta.totalRecords);
	      },
	      failure: function(sRequest, oResponse, oPayload) {
	        this.onDataReturnInitializeTable(sRequest, oResponse, oPayload);
	      }
 }; 

function refreshLoginTable(){
	var selectedvalue = document.getElementById('loginfromselect').value;
	var repositoryvalue = document.getElementById('repositoryselect').value;
	matchingBulkLoginDataTable.getDataSource().sendRequest(
	         "selectedlocation=" + selectedvalue + "&repository=" + repositoryvalue, taskListCallback, matchingBulkLoginDataTable);
}

function refreshLogoutTable(){
	var selectedvalue = document.getElementById('logoutfromselect').value;
	matchingBulkLogoutDataTable.getDataSource().sendRequest(
	         "selectedlocation=" + selectedvalue, taskListCallback, matchingBulkLogoutDataTable);
}



function transferListEmpty()
{
	this.invoke = function()
	{
		document.getElementById("addtotransferlistbutton").style.display = 'block';
	};
}

function handleLoginSelect(type, args, obj) {
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
	
	document.getElementById('logindateinput').value = month + "-" + day + "-" + year;
	loginChooser.hide();
}

function getMatchedLoginTransfers()
{
	var rows = matchingBulkLoginDataTable.getSelectedRows();
	var recordset = matchingBulkLoginDataTable.getRecordSet();
	var matching = new Array();
	for(var i = 0; i < rows.length; i++)
	{
		var oRecord = recordset.getRecord(rows[i]);
		var itemID = oRecord.getData("ItemID");
		matching[itemID] = itemID;
	}
	return matching;
}

function saveLogin()
{
	var matching = getMatchedLoginTransfers();
	var jsonstring = Object.toJSON(matching);

	document.bulkloginform.action = baseUrl + "transfers/savebulklogin";
	document.getElementById('transferids').value = jsonstring;
	document.bulkloginform.submit();
}

function handleLogoutSelect(type, args, obj) {
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
	
	document.getElementById('logoutdateinput').value = month + "-" + day + "-" + year;
	logoutChooser.hide();
}

function getMatchedLogoutTransfers()
{
	  	var rows = matchingBulkLogoutDataTable.getSelectedRows();
		var recordset = matchingBulkLogoutDataTable.getRecordSet();
		var matching = new Array();
		for(var i = 0; i < rows.length; i++)
		{
			var oRecord = recordset.getRecord(rows[i]);
			var itemID = oRecord.getData("ItemID");
			matching[itemID] = itemID;
		}
		return matching;
}


function saveLogout()
{
	var matching = getMatchedLogoutTransfers();
	var jsonstring = Object.toJSON(matching);

	document.bulklogoutform.action = baseUrl + "transfers/savebulklogout";
	document.getElementById('transferids').value = jsonstring;
	document.bulklogoutform.submit();
}

function transferBulkLogin()
{
	var matching = getMatchedLoginTransfers();
	var jsonstring = Object.toJSON(matching);
	document.bulkloginform.action = baseUrl + "transfers/addtologintransferlist";
	document.getElementById('transferids').value = jsonstring;
	document.bulkloginform.submit();
}



function initLoginButtons()
{
	function onButtonsReady() {

	    var oSaveButton = new YAHOO.widget.Button("saveloginbutton");
	    oSaveButton.on("click", saveLogin);
        
        var oAddtoTransfer = new YAHOO.widget.Button("addtotransferlistbutton");
        oAddtoTransfer.on("click", transferBulkLogin);
	}
	
    YAHOO.util.Event.onContentReady("loginbuttons", onButtonsReady);
    document.getElementById('transferformmenuitem').style.display = 'block';
    initTransferDialog();
}

function transferBulkLogout()
{
	var matching = getMatchedLogoutTransfers();
	var jsonstring = Object.toJSON(matching);
	
	document.bulklogoutform.action = baseUrl + "transfers/addtologouttransferlist";
	document.getElementById('transferids').value = jsonstring;
	document.bulklogoutform.submit();
}

function initLogoutButtons()
{
	function onButtonsReady() {

	    var oSaveButton = new YAHOO.widget.Button("savelogoutbutton");
	    oSaveButton.on("click", saveLogout);
        
        var oAddtoTransfer = new YAHOO.widget.Button("addtotransferlistbutton");
        oAddtoTransfer.on("click", transferBulkLogout);
	}
	
    YAHOO.util.Event.onContentReady("logoutbuttons", onButtonsReady);
}


function updateMatchingButtons(listname)
{
	var myAjax = new Ajax.Request(baseUrl + "transfers/findcurrenttransferinfo",
    		{method: 'get',
			parameters: {listname: listname},
			onComplete: function (transport)
			{
				fromto = "";
				if (transport.responseJSON !== null)
	        	{
					var val = JSON.parse(transport.responseText);
	        		var f = val.FromLocationID;
	        		if (f.length > 0)
	        		{
		        		var ftext = val.FromLocation;
		        		var ttext = val.ToLocation;
		        		var dtext = val.Department;
		        		var curtext = val.Curator;
		        		fromto = "(" + ftext + " to " + ttext + ", " + dtext + ", " + curtext + ")";
	        		}
	        		
	        	}
	        	if (listname == "login")
				{
					initLoginButtons();
				}
				else
				{
					initLogoutButtons();
				}
	        	document.getElementById("transferlisttitle").innerHTML = "Transfer List " + fromto;
			}
    	});
}

var loginChooser;
function initLogin()
{ 
    loadLoginTransferList(transferListEmpty, document.bulkloginform);
	var selectedIndex = document.getElementById('loginfromselect').value;
	var repository = document.getElementById('repositoryselect').value;
	loadMatchingBulkLoginRecords(selectedIndex, repository);
	updateMatchingButtons("login");
	
	loginChooser = new YAHOO.widget.Calendar("loginChooser","loginCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
  	loginChooser.render();
  	loginChooser.selectEvent.subscribe(handleLoginSelect, loginChooser, true);
  	document.getElementById('transferformmenuitem').style.display = 'block';
  	initTransferDialog();
}


var logoutChooser;
function initLogout()
{
    
    loadLogoutTransferList(transferListEmpty, document.bulklogoutform);
	var selectedIndex = document.getElementById('logoutfromselect').value;
	loadMatchingBulkLogoutRecords(selectedIndex);
	updateMatchingButtons("logout");
	
	logoutChooser = new YAHOO.widget.Calendar("logoutChooser","logoutCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    logoutChooser.render();
    logoutChooser.selectEvent.subscribe(handleLogoutSelect, logoutChooser, true); 
    document.getElementById('transferformmenuitem').style.display = 'block';
  	initTransferDialog();
}


function showLoginChooser()
{
	if (loginChooser !== null)
	{
		loginChooser.show();
	}
}


function showLogoutChooser()
{
	if (logoutChooser !== null)
	{
		logoutChooser.show();
	}
}
