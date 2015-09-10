
function initButtons()
{
	function onButtonsReady() {

		var oDoneButton = new YAHOO.widget.Button("donebutton");
		oDoneButton.on('click', function(){
			window.location = baseUrl;
		});
		var oGoToRecordButton = new YAHOO.widget.Button("gotorecordbutton");
		oGoToRecordButton.on('click', function(){
			window.location = "/record/gotocurrentrecord";
		});
	}

    YAHOO.util.Event.onContentReady("statusbuttons", onButtonsReady);
    
    document.getElementById('statusformmenuitem').style.display = "block";
}

function loadStatus()
{
	function onStatusListReady() {
		
		this.stringDateFormatter = function(elCell, oRecord, oColumn, oData) 
	    {
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
			else
			{
				elCell.innerHTML = oData;
			}
	    };
	    // Add the custom formatter to the shortcuts
	    YAHOO.widget.DataTable.Formatter.stringDate = this.stringDateFormatter;
	    
		var myStatusColumnDefs = [
	      {key:"Date",label:"Date",width: 50, formatter:"stringDate"},
	      {key:"Activity",label:"Message"}
	    ];
		
		var myStatusDataSource = new YAHOO.util.DataSource(baseUrl + "record/recordactivity");
		myStatusDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myStatusDataSource.responseSchema = {
            resultsList: "Result",
            fields: ["Date", "Activity"]
         };

		var myStatusDataTable = new YAHOO.widget.DataTable("statuslist", myStatusColumnDefs, myStatusDataSource, {scrollable:true, height:"120px"});
	}

    YAHOO.util.Event.onDOMReady(onStatusListReady); 
}

function createStatusForm()
{
	var myAjax = new Ajax.Request(baseUrl + "reports/statusreportform",
    		{method: 'get', 
			onSuccess: function(transport)
			{
				//Open the report.
				var filename = transport.responseText.trim();
				window.open(baseUrl + 'userreports/pdfreports/' + filename);
			}
    		});
	
}
