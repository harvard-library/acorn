var matchingGroupDataTable;

function getMatchedTransfers()
{
	  var records = matchingGroupDataTable.getRecordSet().getRecords();
		var matching = new Array();
		for(var i = 0; i < records.length; i++)
		{
			var itemID = records[i].getData("ItemID");
			matching[itemID] = itemID;
		}
		return matching;
}

function transferMatchingGroup()
{
	var bookmarkedTabViewState = YAHOO.util.History.getBookmarkedState("tabview");
    var initialTabViewState = bookmarkedTabViewState || "tab0";
    var tabIndex = initialTabViewState.substr(3);
    
    var matching = getMatchedTransfers();
    if (matching.length > 0)
    {
		var jsonstring = Object.toJSON(matching);
		
		if (tabIndex == 1)
		{
			document.grouploginform.action = baseUrl + "grouplogin/addtotransferlist";
		    document.getElementById('transferids' + tabIndex).value = jsonstring;
		    document.grouploginform.submit();
		}
		else if (tabIndex == 4)
		{
			document.grouplogoutform.action = baseUrl + "grouplogout/addtotransferlist";
		    document.getElementById('transferids' + tabIndex).value = jsonstring;
		    document.grouplogoutform.submit();
		}
		else if (tabIndex == 6)
		{
			document.grouptemporarytransferform.action = baseUrl + "grouptemptransfer/addtotemptransferlist";
		    document.getElementById('transferids' + tabIndex).value = jsonstring;
		    document.grouptemporarytransferform.submit();
		}
		else if (tabIndex == 7)
		{
			document.grouptemporarytransferreturnform.action = baseUrl + "grouptemptransfer/addtotemptransferreturnlist";
		    document.getElementById('transferids' + tabIndex).value = jsonstring;
		    document.grouptemporarytransferreturnform.submit();
		}
    }
	
}

function saveMatchingTransferList(jsonstring)
{
	var bookmarkedTabViewState = YAHOO.util.History.getBookmarkedState("tabview");
    var initialTabViewState = bookmarkedTabViewState || "tab0";
    var tabIndex = initialTabViewState.substr(3);
    
  	if (tabIndex == 1)
	{
  		document.grouploginform.action = baseUrl + "grouplogin/savelogin";
  		document.getElementById('transferids' + tabIndex).value = jsonstring;
  		document.grouploginform.submit();
	}
	else if (tabIndex == 4)
	{
		document.grouplogoutform.action = baseUrl + "grouplogout/savelogout";
  		document.getElementById('transferids' + tabIndex).value = jsonstring;
  		document.grouplogoutform.submit();
	}
}


function removeFromMatchingGroup()
{
	var rows = matchingGroupDataTable.getSelectedRows();
	var recordset = matchingGroupDataTable.getRecordSet();
	for(var i = 0; i < rows.length; i++)
	{
			var oRecord = recordset.getRecord(rows[i]);
			matchingGroupDataTable.deleteRow(oRecord);	
	}
}

function saveMatchingGroup()
{
	var matching = getMatchedTransfers();
	if (matching.length > 0)
	{
		var jsonstring = Object.toJSON(matching);
		saveMatchingTransferList(jsonstring);
	}
}

function loadMatchingButtons()
{
	function onButtonsReady() {

	    var oRemoveFromGroupButton = new YAHOO.widget.Button("removefrommatchinggroupbutton");
        oRemoveFromGroupButton.on("click", removeFromMatchingGroup);
        
        var bookmarkedTabViewState = YAHOO.util.History.getBookmarkedState("tabview");
        var initialTabViewState = bookmarkedTabViewState || "tab0";
        var tabIndex = initialTabViewState.substr(3);
        
        //Temp transfers only have an add to transfer list option
        if (tabIndex == 6 || tabIndex == 7)
        {
        	document.getElementById("savematchinggroupbutton").style.display = 'none';
        }
        else
        {  
        	var oSaveButton = new YAHOO.widget.Button("savematchinggroupbutton");
        	oSaveButton.on("click", saveMatchingGroup);
        }

        var oAddtoTransfer = new YAHOO.widget.Button("matchingaddtotransferlistbutton");
        oAddtoTransfer.on("click", transferMatchingGroup);
	}
	
    YAHOO.util.Event.onContentReady("matchingtransferbuttons", onButtonsReady);
}

function loadMatchingGroupRecords(selectedvalue)
{
	loadMatchingButtons();
		var myGroupColumnDefs = [
              {key:"ItemID",label:"Rec #", width: 20},
              {key:"CallNumbers", label: "Call Nums", width: 50},
              {key: "Title", label: "Title",width: 100},
              {key: "AuthorArtist", label: "Author/Artist", width: 75},
              {key: "DateOfObject", label: "Date of Obj", width: 50},
              {key: "VolumeCount", label: "Vol", width: 10},
              {key: "SheetCount", label: "Sht", width: 10},
              {key: "PhotoCount", label: "Ph", width: 10},
              {key: "BoxCount", label: "Bx", width: 10},
              {key: "OtherCount", label: "Oth", width: 10},
              {key: "Comments", label: "Comments", width: 100}
              ];
  		
		var bookmarkedTabViewState = YAHOO.util.History.getBookmarkedState("tabview");
        var initialTabViewState = bookmarkedTabViewState || "tab0";
        var tabIndex = initialTabViewState.substr(3);
        
          var myGroupDataSource = new YAHOO.util.DataSource(baseUrl + "group/findmatchinggrouprecords/tab/"+tabIndex+"/selectedlocation/" + selectedvalue);
          myGroupDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
          myGroupDataSource.responseSchema = {
              resultsList: "Result",
              fields: ["ItemID", "CallNumbers", "Title", "AuthorArtist", "DateOfObject", 
                       "VolumeCount", "SheetCount", "PhotoCount", "BoxCount", "OtherCount", "Comments"]
           };

          matchingGroupDataTable = new YAHOO.widget.DataTable("matchingtransfergrouprecords", myGroupColumnDefs, myGroupDataSource, {scrollable:true, height:"5.5em"});
          matchingGroupDataTable.subscribe("rowClickEvent",matchingGroupDataTable.onEventSelectRow);

          
}


function updateMatchingButtons(listname, fromlocation, tolocation)
{
	var myAjax = new Ajax.Request(baseUrl + "transfers/findcurrenttransferinfo",
    		{method: 'get',
			parameters: {listname: listname},
			onComplete: function (transport)
			{
				fromto = "";
				if (transport.responseJSON != null)
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
	        	loadMatchingButtons();
	        	document.getElementById("transferlisttitle").innerHTML = "Transfer List " + fromto;
			}
    	});
}
