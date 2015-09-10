
function loadMyRecordList()
{
	function onMyRecordListReady() {
		
		this.recordCustomFormatter = function(elCell, oRecord, oColumn, oData) {
			var type = oRecord.getData("RecordType");
			var recnum = oRecord.getData("RecordID");
			if (type == "Item")
        	{
        		elCell.innerHTML = ' <a href="' +baseUrl + 'record/index/recordnumber/' + recnum + '">' + oData + '</a>';
        	}
        	else
        	{
        		elCell.innerHTML = ' <a href="' +baseUrl + 'record/osw/recordnumber/' + recnum + '">' + oData + '</a>';
            }
        };
        
        // Add the custom formatter to the shortcuts
        YAHOO.widget.DataTable.Formatter.recordCustom = this.recordCustomFormatter;

        this.statusCustomFormatter = function(elCell, oRecord, oColumn, oData) {
        	var type = oRecord.getData("RecordType");
        	if (type == "Item")
        	{
        		var recnum = oRecord.getData("RecordID");
        		elCell.innerHTML = ' <a href="' +baseUrl + 'record/recordstatus/recordnumber/' + recnum + '">' + oData + '</a>';
        	}
        	else
        	{
        		elCell.innerHTML = oData;
        	}
        };
        
        // Add the custom formatter to the shortcuts
        YAHOO.widget.DataTable.Formatter.statusCustom = this.statusCustomFormatter;
        
        this.proposalStatusCustomFormatter = function(elCell, oRecord, oColumn, oData) {
        	var stat = oRecord.getData("ProposalApproval");
        	if (stat != "" && stat != null && stat != undefined)
        	{
        		var recnum = oRecord.getData("RecordID");
        		elCell.innerHTML = ' <a href="' +baseUrl + 'proposalapproval/index/recordnumber/' + recnum + '">' + oData + '</a>';
        	}
        	else
        	{
        		elCell.innerHTML = '';
        	}
        };
        
        // Add the custom formatter to the shortcuts
        YAHOO.widget.DataTable.Formatter.proposalStatusCustom = this.proposalStatusCustomFormatter;

		var myRecordColumnDefs = [
              {key:"RecordType",label:"Type", width: 50},
              {key:"RecordID",label:"Record #", formatter: "recordCustom", width: 50},
              {key:"Title",label:"Title", width: 150},
              {key:"AuthorArtist",label:"Author/Artist", maxAutoWidth: 150},
              {key:"DateOfObject",label:"Date of Object", maxAutoWidth: 150},
              {key:"CallNumbers",label:"Call Numbers", width: 300, resizeable: true},
              {key:"ItemStatus",label:"Status", formatter: "statusCustom", width: 50},
              {key:"ProposalApproval",label:"Proposal Approval", formatter: "proposalStatusCustom", width: 85}
              ];
  		
		
          var myRecordDataSource = new YAHOO.util.DataSource(baseUrl + "user/finduserrecords");
          myRecordDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
          myRecordDataSource.responseSchema = {
              resultsList: "Result",
              fields: ["RecordType", "RecordID", "Title", "AuthorArtist", "DateOfObject", "CallNumbers", "ItemStatus", "ProposalApproval"]
           };

          var myRecordDataTable = new YAHOO.widget.DataTable("myrecordlist", 
        		  myRecordColumnDefs, myRecordDataSource, {scrollable:true, height:"25em", width: "100%"});
  		
	}
	
	YAHOO.util.Event.onContentReady("myrecordlist", onMyRecordListReady);
}

function loadCuratorRecordList()
{
	function onCuratorRecordListReady() {
		this.proposalStatusCustomFormatter = function(elCell, oRecord, oColumn, oData) {
        	var stat = oRecord.getData("ProposalApproval");
        	if (stat != "" && stat != null && stat != undefined)
        	{
        		var recnum = oRecord.getData("RecordID");
        		elCell.innerHTML = ' <a href="' +baseUrl + 'proposalapproval/index/recordnumber/' + recnum + '">' + oData + '</a>';
        	}
        	else
        	{
        		elCell.innerHTML = '';
        	}
        };
        
        // Add the custom formatter to the shortcuts
        YAHOO.widget.DataTable.Formatter.proposalStatusCustom = this.proposalStatusCustomFormatter;

		var myRecordColumnDefs = [
              {key:"RecordID",label:"Record #", width: 50},
              {key:"Title",label:"Title", width: 150},
              {key:"AuthorArtist",label:"Author/Artist"},
              {key:"DateOfObject",label:"Date of Object"},
              {key:"CallNumbers",label:"Call Numbers"},
              {key:"ProposalApproval",label:"Proposal Approval", formatter: "proposalStatusCustom", width: 85}
              ];
  		
		
          var myRecordDataSource = new YAHOO.util.DataSource(baseUrl + "user/findcuratorrecords");
          myRecordDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
          myRecordDataSource.responseSchema = {
              resultsList: "Result",
              fields: ["RecordID", "Title", "AuthorArtist", "DateOfObject", "CallNumbers", "ProposalApproval"]
           };

          var myRecordDataTable = new YAHOO.widget.DataTable("myrecordlist", myRecordColumnDefs, myRecordDataSource, {scrollable:true, height:"25em"});
  		
	}
	
	YAHOO.util.Event.onContentReady("myrecordlist", onCuratorRecordListReady);
}