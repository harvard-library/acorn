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
function loadLoginTransferList(transferCallback, formname)
{
	var myTransferColumnDefs = [
            {key:"ItemID",label:"Record #", width: 50},
            {key:"Title"}
        ];
		
        var myTransferDataSource = new YAHOO.util.DataSource(baseUrl + "transfers/findlogintransfers");
        myTransferDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myTransferDataSource.responseSchema = {
            resultsList: "Result",
    		fields: ["ItemID", "Title"]
    	};

        var myTransferDataTable = new YAHOO.widget.DataTable("transferlist", myTransferColumnDefs, myTransferDataSource, {scrollable:true, height:"5.5em", width: "100%"});
        myTransferDataTable.subscribe("rowClickEvent",myTransferDataTable.onEventSelectRow);
	
	var oRemoveFromGroupButton = new YAHOO.widget.Button("removetransfersbutton");
	oRemoveFromGroupButton.on("click", function() {
		var rows = myTransferDataTable.getSelectedRows();
		var recordset = myTransferDataTable.getRecordSet();
		var listname = formname.transferlistname.value;
		for(var i = 0; i < rows.length; i++)
		{
				var oRecord = recordset.getRecord(rows[i]);
				var itemID = oRecord.getData("ItemID");
				var myAjax = new Ajax.Request(baseUrl + "record/removefromtransferlist",
			    		{method: 'get', 
						parameters: {itemID: itemID, listname: listname}
						});
				myTransferDataTable.deleteRow(oRecord);
	    		
		}
		if (recordset.getLength() == 0)
		{
			transferCallback.invoke();
		}
	});
	
	var myAjax = new Ajax.Request(baseUrl + "transfers/findcurrenttransferinfo",
    		{method: 'get',
			parameters: {listname: 'login'},
			onComplete: function (transport)
			{
				fromto = "";
				if (transport.responseJSON !== null)
	        	{
					var val = JSON.parse(transport.responseText);
	        		var f = val.FromLocationID;
	        		if (f.length > 0)
	        		{
		        		var t = val.ToLocationID;
		        		var ftext = val.FromLocation;
		        		var ttext = val.ToLocation;
		        		var dtext = val.Department;
		        		var curtext = val.Curator;
		        		fromto = "(" + ftext + " to " + ttext + ", " + dtext + ", " + curtext + ")";
	        		}
	        		
	        	}
	        	document.getElementById("transferlisttitle").innerHTML = "Login Transfer List " + fromto;
			}
    	});
	
}

function loadLogoutTransferList(transferCallback, formname)
{
	var myTransferColumnDefs = [
            {key:"ItemID",label:"Record #", width: 50},
            {key:"Title"}
        ];
		
        var myTransferDataSource = new YAHOO.util.DataSource(baseUrl + "transfers/findlogouttransfers");
        myTransferDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myTransferDataSource.responseSchema = {
            resultsList: "Result",
    		fields: ["ItemID", "Title"]
    	};

        var myTransferDataTable = new YAHOO.widget.DataTable("transferlist", myTransferColumnDefs, myTransferDataSource, {scrollable:true, height:"5.5em", width: "100%"});
        myTransferDataTable.subscribe("rowClickEvent",myTransferDataTable.onEventSelectRow);
	
	var oRemoveFromGroupButton = new YAHOO.widget.Button("removetransfersbutton");
	oRemoveFromGroupButton.on("click", function() {
		var rows = myTransferDataTable.getSelectedRows();
		var recordset = myTransferDataTable.getRecordSet();
		var listname = formname.transferlistname.value;
		for(var i = 0; i < rows.length; i++)
		{
				var oRecord = recordset.getRecord(rows[i]);
				var itemID = oRecord.getData("ItemID");
				var myAjax = new Ajax.Request(baseUrl + "record/removefromtransferlist",
			    		{method: 'get', 
						parameters: {itemID: itemID, listname: listname}});
				myTransferDataTable.deleteRow(oRecord);
		}
		if (recordset.getLength() == 0)
		{
			transferCallback.invoke();
		}
	});
	
	var myAjax = new Ajax.Request(baseUrl + "transfers/findcurrenttransferinfo",
    		{method: 'get',
			parameters: {listname: 'logout'},
			onComplete: function (transport)
			{
				fromto = "";
				if (transport.responseJSON !== null)
	        	{
					var val = JSON.parse(transport.responseText);
	        		var f = val.FromLocationID;
	        		if (f.length > 0)
	        		{
		        		var t = val.ToLocationID;
		        		var ftext = val.FromLocation;
		        		var ttext = val.ToLocation;
		        		var dtext = val.Department;
		        		var curtext = val.Curator;
		        		fromto = "(" + ftext + " to " + ttext + ", " + dtext + ", " + curtext + ")";
	        		}
	        		
	        	}
	        	document.getElementById("transferlisttitle").innerHTML = "Logout Transfer List " + fromto;
			}
    	});
	
}

function loadTempTransferList(transferCallback, formname)
{
	var myTransferColumnDefs = [
            {key:"ItemID",label:"Record #", width: 50},
            {key:"Title"}
        ];
		
        var myTransferDataSource = new YAHOO.util.DataSource(baseUrl + "transfers/findtemptransfers");
        myTransferDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myTransferDataSource.responseSchema = {
            resultsList: "Result",
    		fields: ["ItemID", "Title"]
    	};

        var myTransferDataTable = new YAHOO.widget.DataTable("transferlist", myTransferColumnDefs, myTransferDataSource, {scrollable:true, height:"5.5em", width: "100%"});
        myTransferDataTable.subscribe("rowClickEvent",myTransferDataTable.onEventSelectRow);
	
	var oRemoveFromGroupButton = new YAHOO.widget.Button("removetransfersbutton");
	oRemoveFromGroupButton.on("click", function() {
		var rows = myTransferDataTable.getSelectedRows();
		var recordset = myTransferDataTable.getRecordSet();
		var listname = formname.transferlistname.value;
		for(var i = 0; i < rows.length; i++)
		{
				var oRecord = recordset.getRecord(rows[i]);
				var itemID = oRecord.getData("ItemID");
				var myAjax = new Ajax.Request(baseUrl + "record/removefromtransferlist",
			    		{method: 'get', 
						parameters: {itemID: itemID, listname: listname}});
				myTransferDataTable.deleteRow(oRecord);
		}
		if (recordset.getLength() == 0)
		{
			transferCallback.invoke();
		}
	});
	
	var myAjax = new Ajax.Request(baseUrl + "transfers/findcurrenttransferinfo",
    		{method: 'get',
			parameters: {listname: 'temptransfer'},
			onComplete: function (transport)
			{
				fromto = "";
				if (transport.responseJSON !== null)
	        	{
					var val = JSON.parse(transport.responseText);
	        		var f = val.FromLocationID;
	        		if (f.length > 0)
	        		{
		        		var t = val.ToLocationID;
		        		var ftext = val.FromLocation;
		        		var ttext = val.ToLocation;
		        		var dtext = val.Department;
		        		var curtext = val.Curator;
		        		fromto = "(" + ftext + " to " + ttext + ", " + dtext + ", " + curtext + ")";
	        		}
	        		
	        	}
	        	document.getElementById("transferlisttitle").innerHTML = "Temp Transfer List " + fromto;
			}
    	});
}

function loadTempTransferReturnList(transferCallback, formname)
{
	var myTransferColumnDefs = [
            {key:"ItemID",label:"Record #", width: 50},
            {key:"Title"}
        ];
		
        var myTransferDataSource = new YAHOO.util.DataSource(baseUrl + "transfers/findtemptransferreturns");
        myTransferDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myTransferDataSource.responseSchema = {
            resultsList: "Result",
    		fields: ["ItemID", "Title"]
    	};

        var myTransferDataTable = new YAHOO.widget.DataTable("transferlist", myTransferColumnDefs, myTransferDataSource, {scrollable:true, height:"5.5em", width: "100%"});
        myTransferDataTable.subscribe("rowClickEvent",myTransferDataTable.onEventSelectRow);
	
	var oRemoveFromGroupButton = new YAHOO.widget.Button("removetransfersbutton");
	oRemoveFromGroupButton.on("click", function() {
		var rows = myTransferDataTable.getSelectedRows();
		var recordset = myTransferDataTable.getRecordSet();
		var listname = formname.transferlistname.value;
		for(var i = 0; i < rows.length; i++)
		{
				var oRecord = recordset.getRecord(rows[i]);
				var itemID = oRecord.getData("ItemID");
				var myAjax = new Ajax.Request(baseUrl + "record/removefromtransferlist",
			    		{method: 'get', 
						parameters: {itemID: itemID, listname: listname}});
				myTransferDataTable.deleteRow(oRecord);
		}
		
		if (recordset.getLength() == 0)
		{
			transferCallback.invoke();
		}
	});
	
	var myAjax = new Ajax.Request(baseUrl + "transfers/findcurrenttransferinfo",
    		{method: 'get',
			parameters: {listname: 'temptransferreturn'},
			onComplete: function (transport)
			{
				fromto = "";
				if (transport.responseJSON !== null)
	        	{
					var val = JSON.parse(transport.responseText);
	        		var f = val.FromLocationID;
	        		if (f.length > 0)
	        		{
		        		var t = val.ToLocationID;
		        		var ftext = val.FromLocation;
		        		var ttext = val.ToLocation;
		        		var dtext = val.Department;
		        		var curtext = val.Curator;
		        		fromto = "(" + ftext + " to " + ttext + ", " + dtext + ", " + curtext + ")";
	        		}
	        		
	        	}
	        	document.getElementById("transferlisttitle").innerHTML = "Temp Return Transfer List " + fromto;
			}
    	});
	
}

var transferDialog;
function initTransferDialog()
{
	function initDialog()
	{
		// Define various event handlers for Dialog
		var handleSubmit = function() {
			this.submit();
		};
		var handleCancel = function() {
			this.cancel();
		};
		var handleSuccess = function(o) {
			//Open the report.
			var filename = o.responseText.trim();
			window.open(baseUrl + 'userreports/pdfreports/' + filename);
		};
		var handleFailure = function(o) {
			alert("There was a problem generating the report.  Please try again. " + o.status);
		};

		// Instantiate the Dialog
		transferDialog = new YAHOO.widget.Dialog("transferdialog", 
								{ width : "30em",
								  fixedcenter : true,
								  visible : false, 
								  constraintoviewport : true,
								  buttons : [ { text:"Generate Transfer Form", handler:handleSubmit, isDefault:true },
									      { text:"Cancel", handler:handleCancel } ]
								});

		// Validate the entries in the form to require that both first and last name are entered
		transferDialog.validate = function() {
			var data = this.getData();
			if (data.courierinput == "" || data.courierinput === null) {
				alert("Please enter a courier.");
				return false;
			} else if (data.transportcontainertextarea == "" || data.transportcontainertextarea === null) {
				alert("Please enter a transport container.");
				return false;
			} else {
				return true;
			}
		};

		// Wire up the success and failure handlers
		transferDialog.callback = { success: handleSuccess,
				failure: handleFailure };
		
		// Render the Dialog
		transferDialog.render();
	}
	YAHOO.util.Event.onContentReady("transferdialog", initDialog);
	prepareCourierInput();
}


function showTransferDialog()
{
	transferDialog.show();
	document.getElementById("transportcontainertextarea").focus();
	
}

function hideTransferDialog()
{
	transferDialog.hide();
}

function prepareCourierInput()
{
	function onCourierInputReady()
	{
		var myDataSource = new YAHOO.util.DataSource(baseUrl + "people/findstaff");
        myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myDataSource.responseSchema = {
            resultsList: "Result",
            fields: ["DisplayName", "PersonID"]
         };
        
	    // Instantiate the AutoComplete
	    var oAC = new YAHOO.widget.AutoComplete("courierinput", "couriercontainer", myDataSource);
	    oAC.prehighlightClassName = "yui-ac-prehighlight";
	    oAC.minQueryLength = 0;  
	    oAC.useShadow = true;
		oAC.useIFrame = true; 
		oAC.textboxFocusEvent.subscribe(function(){
	    	// Is open
	        if(oAC.isContainerOpen()) {
	        	oAC.collapseContainer();
	        }
	        // Is closed
	        else {
	        	oAC.getInputEl().focus(); // Needed to keep widget active
	            setTimeout(function() { // For IE
	            	oAC.sendQuery(document.getElementById("courierinput").value);
	            },0);
	        }
	    });
		
		var oAC2 = new YAHOO.widget.AutoComplete("courier2input", "courier2container", myDataSource);
	    oAC2.prehighlightClassName = "yui-ac-prehighlight";
	    oAC2.minQueryLength = 0;  
	    oAC2.useShadow = true;
		oAC2.useIFrame = true; 
		oAC2.textboxFocusEvent.subscribe(function(){
	    	// Is open
	        if(oAC2.isContainerOpen()) {
	        	oAC2.collapseContainer();
	        }
	        // Is closed
	        else {
	        	oAC2.getInputEl().focus(); // Needed to keep widget active
	            setTimeout(function() { // For IE
	            	oAC2.sendQuery(document.getElementById("courier2input").value);
	            },0);
	        }
	    });
	}
	YAHOO.util.Event.onContentReady("courierinput", onCourierInputReady);
}
