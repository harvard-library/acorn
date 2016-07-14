
function loadGroupRecords(tabIndex)
{
	function onGroupRecordListReady() {
		this.recordCustomFormatter = function(elCell, oRecord, oColumn, oData) {
			var recnum = oRecord.getData("ItemID");
			elCell.innerHTML = ' <a href="' +baseUrl + 'record/index/recordnumber/' + recnum + '">' + oData + '</a>';
        };
        
        // Add the custom formatter to the shortcuts
        YAHOO.widget.DataTable.Formatter.recordCustom = this.recordCustomFormatter;
        
        var dateParser = function(oData) 
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
	    
		var myGroupColumnDefs = null;
		var fieldList = null;
		if (tabIndex != 2 && tabIndex != 3)
		{
			myGroupColumnDefs = [
              {key:"ItemID",label:"Rec #", formatter: "recordCustom", width: 20},
              {key:"CallNumbers", label: "Call Nums", editor: "textbox", width: 50},
              {key: "Title", label: "Title",editor: "textarea", width: 100},
              {key: "AuthorArtist", label: "Author/Artist",editor: "textbox", width: 75},
              {key: "DateOfObject", label: "Date of Obj",editor: "textbox", width: 50},
              {key: "VolumeCount", label: "Vol",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "SheetCount", label: "Sht",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "PhotoCount", label: "Ph",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "BoxCount", label: "Bx",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "OtherCount", label: "Oth",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "Comments", label: "Comments", editor: "textarea", width: 100}
              ];
			
			fieldList = ["ItemID", "CallNumbers", "Title", "AuthorArtist", "DateOfObject", 
                     "VolumeCount", "SheetCount", "PhotoCount", "BoxCount", "OtherCount", "Comments"];
		}
		else if (tabIndex == 2)
		{
			myGroupColumnDefs = [
              {key:"ItemID",label:"Rec #", formatter: "recordCustom", width: 20},
              {key:"CallNumbers", label: "Call Nums", editor: "textbox", width: 50},
              {key: "Title", label: "Title",editor: "textarea", width: 100},
              {key: "AuthorArtist", label: "Author/Artist",editor: "textbox", width: 75},
              {key: "DateOfObject", label: "Date of Obj",editor: "textbox", width: 50},
              {key: "VolumeCount", label: "Vol",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "SheetCount", label: "Sht",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "PhotoCount", label: "Ph",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "BoxCount", label: "Bx",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "OtherCount", label: "Oth",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "Comments", label: "Comments", editor: "textarea", width: 100},
              {key: "ExamDate", label: "Exam Date", formatter:YAHOO.widget.DataTable.formatDate, editor: new YAHOO.widget.DateCellEditor({disableBtns: true}), width: 100},
              {key: "DimensionUnit", label: "Unit", editor: new YAHOO.widget.DropdownCellEditor({dropdownOptions:['cm', 'in'],disableBtns:true}), width: 50},
              {key: "Height", label: "H",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "Width", label: "W",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "Thickness", label: "T",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10}
              ];
			
			fieldList = ["ItemID", "CallNumbers", "Title", "AuthorArtist", "DateOfObject", 
	                     "VolumeCount", "SheetCount", "PhotoCount", "BoxCount", "OtherCount", "Comments", 
	                     {key:"ExamDate", parser: dateParser}, "DimensionUnit", "Height", "Width", "Thickness"];
		}
		else if (tabIndex == 3)
		{
			myGroupColumnDefs = [
              {key:"ItemID",label:"Rec #", formatter: "recordCustom", width: 20},
              {key:"CallNumbers", label: "Call Nums", editor: "textbox", width: 50},
              {key: "Title", label: "Title",editor: "textarea", width: 100},
              {key: "AuthorArtist", label: "Author/Artist",editor: "textbox", width: 75},
              {key: "DateOfObject", label: "Date of Obj",editor: "textbox", width: 50},
              {key: "VolumeCount", label: "Vol",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "SheetCount", label: "Sht",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "PhotoCount", label: "Ph",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "BoxCount", label: "Bx",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "OtherCount", label: "Oth",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "Comments", label: "Comments", editor: "textarea", width: 100},
              {key: "ReportDate", label: "Report Date", formatter:YAHOO.widget.DataTable.formatDate, editor: new YAHOO.widget.DateCellEditor({disableBtns: true}), width: 100},
              {key: "TotalHours", label: "Hours", width: 15},
              {key: "ReportUnit", label: "Unit", editor: new YAHOO.widget.DropdownCellEditor({dropdownOptions:['cm', 'in'],disableBtns:true}), width: 50},
              {key: "ReportHeight", label: "H",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "ReportWidth", label: "W",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "ReportThickness", label: "T",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "FinalVolumeCount", label: "Vol",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "FinalSheetCount", label: "Sht",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "FinalPhotoCount", label: "Ph",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "FinalBoxCount", label: "Bx",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "FinalOtherCount", label: "Oth",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10},
              {key: "FinalHousingCount", label: "Oth",editor:new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber}), width: 10}      
              ];
			
			fieldList = ["ItemID", "CallNumbers", "Title", "AuthorArtist", "DateOfObject", 
	                     "VolumeCount", "SheetCount", "PhotoCount", "BoxCount", "OtherCount", "Comments",
	                     {key:"ReportDate", parser: dateParser}, "TotalHours", "ReportUnit", "ReportHeight", "ReportWidth", "ReportThickness", 
	                     "FinalVolumeCount", "FinalSheetCount", "FinalPhotoCount", "FinalBoxCount", "FinalOtherCount", "FinalHousingCount"];
		}
  		
          var myGroupDataSource = new YAHOO.util.DataSource(baseUrl + "group/findgrouprecords");
          myGroupDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
          myGroupDataSource.responseSchema = {
              resultsList: "Result",
              fields: fieldList
           };

          var myGroupDataTable = new YAHOO.widget.ScrollingDataTable("grouprecords", myGroupColumnDefs, myGroupDataSource, {height:"5.5em", width: "100%"});
          myGroupDataTable.subscribe("rowClickEvent",myGroupDataTable.onEventSelectRow);
          myGroupDataTable.subscribe("cellDblclickEvent",myGroupDataTable.onEventShowCellEditor);
          myGroupDataTable.subscribe("editorBlurEvent", myGroupDataTable.onEventSaveCellEditor);
          
          var onGroupCellEdit = function(oArgs) {
	            
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
				if (oOldData != oNewData)
				{
					var elCellColumn = oArgs.editor.getColumn();
					var elRecord = myGroupDataTable.getRecord(elRow);
					
					var recordID = elRecord.getData("ItemID");
					var columnname = elCellColumn.getKey();
					if (columnname == "VolumeCount" || columnname == "SheetCount" || columnname == "PhotoCount" || columnname == "BoxCount" || columnname == "OtherCount" || columnname == "InusranceValue")
					{
						if (oNewData == "" || oNewData < 0)
						{
							myGroupDataTable.updateCell(elRecord, elCellColumn, 0);
							oNewData = 0;
						}
					}
					else if (columnname == "ReportDate" || columnname == "ExamDate")
					{
						if (oNewData instanceof Date)
						{
							var year = oNewData.getFullYear();
							var month = oNewData.getMonth()+1;
							if (month < 10)
							{
								month = '0' + month;
							}
							var day = oNewData.getDate();
							if (day < 10)
							{
								day = '0' + day;
							}
							oNewData = year + "-" + month + "-" + day;
						}
					}
					
					var myAjax = new Ajax.Request(baseUrl + "group/updaterecorddata",
					{
							method: 'get', 
							parameters: {itemID: recordID, column: columnname, newdata: oNewData},
							onSuccess: function(transport)
							{
								
							}});
				}
		};
        myGroupDataTable.subscribe("editorSaveEvent", onGroupCellEdit);

        deleteGivenRow = function(oRecord)
        {
        	myGroupDataTable.deleteRow(oRecord);
        };
        
          var oRemoveFromGroupButton = new YAHOO.widget.Button("removefromgroupbutton");
          oRemoveFromGroupButton.on("click", function() {
      		var rows = myGroupDataTable.getSelectedRows();
      		var recordset = myGroupDataTable.getRecordSet();
      		for(var i = 0; i < rows.length; i++)
      		{
      				var oRecord = recordset.getRecord(rows[i]);
      				var itemID = oRecord.getData("ItemID");
      				var myAjax = new Ajax.Request(baseUrl + "group/removefromgroup",
      			    		{method: 'get', 
      						parameters: {itemID: itemID},
      						onSuccess: deleteGivenRow(oRecord)
      			    		});
      				
      		}
      	});
	}
	
	YAHOO.util.Event.onContentReady("grouprecords", onGroupRecordListReady);
}