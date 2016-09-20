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

function loadMyRecords()
{
	function onMyRecordListReady() {
		this.recordCustomFormatter = function(elCell, oRecord, oColumn, oData) {
			var type = oRecord.getData("RecordType");
			var recnum = oRecord.getData("RecordID");
			if (type == "Item")
        	{
        		elCell.innerHTML = oData;
        	}
        	else
        	{
        		elCell.innerHTML = "OSW-" + oData;
            }
        };
        
        // Add the custom formatter to the shortcuts
        YAHOO.widget.DataTable.Formatter.recordCustom = this.recordCustomFormatter;
        
		var myRecordColumnDefs = [
              {key:"RecordID",label:"My Records", formatter: "recordCustom"},
              {key:"RecordType"}
              ];
  		
          var myRecordDataSource = new YAHOO.util.DataSource(baseUrl + "record/findcurrentuserrecords");
          myRecordDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
          myRecordDataSource.responseSchema = {
              resultsList: "Result",
              fields: ["RecordID", "RecordType"]
           };

          var myRecordDataTable = new YAHOO.widget.DataTable("myrecords", myRecordColumnDefs, myRecordDataSource, {scrollable:true, height:"35em"});
  		
  			
  		var panel1 = new YAHOO.widget.Panel("panel1", { width:"320px", draggable:true, visible:false} ); 
  		var onMouseOverRecord = function(oArgs) {
  			var elCell = oArgs.target; 
  			var oRecord = this.getRecord(elCell); 
  			var recordID = oRecord.getData("RecordID");
  			var recordType = oRecord.getData("RecordType");

  			panel1.setHeader("<b>Information about Record #" + recordID + "</b>");
  			var myAjax = new Ajax.Request(baseUrl + "record/getrecorddialogdata",
		    		{method: 'get', 
  					parameters: {recordID: recordID, recordType: recordType},
  					onSuccess: function(transport)
  					{
  						if (transport.responseJSON !== null)
  						{
  							var val = JSON.parse(transport.responseText);
  							var author = val.Author;
  							var title = val.Title;
  							var call = val.CallNumbers;
  							var status = val.Status;
  							var approval = val.ProposalApproval;
  							var body = "<p>";
  							if (recordType == 'Item')
  							{
	  							body = body + "<a href=\"" + baseUrl + "record/index/recordnumber/" + recordID + "\">Record #" + recordID + "</a>";
	  							body = body + "<br>" + author;
	  							body = body + "<br>" + title;
	  							body = body + "<br>" + call;
	  							body = body + "<br><a href=\"" + baseUrl + "record/recordstatus/recordnumber/" + recordID + "\">" + status + "</a>";
	  							if (approval != undefined && approval != null && approval != "")
	  							{
	  								body = body + "<br><a href=\"" + baseUrl + "proposalapproval/index/recordnumber/" + recordID + "\">Proposal Approval: " + approval + "</a></p>";
		  						}
	  							else
	  							{
	  								body = body + "</p>";
		  						}
  							}
  							else
  							{
  								body = body + "<a href=\"" + baseUrl + "record/osw/recordnumber/" + recordID + "\">OSW #" + recordID + "</a>";
	  							body = body + "<br>" + title;
  							}
  							panel1.setBody(body);
	  			  		}
  						else
  						{
  							panel1.setBody("There was a problem accessing the data for Record#" + recordID + ". Please try again.");
  		  			  	}
  						panel1.render("paneldiv"); 
  			  			panel1.show();	
  					}
		    		});
  			};
  			
  		myRecordDataTable.subscribe("cellClickEvent", onMouseOverRecord);
  		myRecordDataTable.hideColumn("RecordType");
	}
	
	YAHOO.util.Event.onContentReady("myrecords", onMyRecordListReady);
}