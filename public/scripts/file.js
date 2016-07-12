var filesDataTable;
var linkFilesDataTable;
var currentRecordType = "item";
var filereadonly;
function setFileReadOnly(readonly)
{
	filereadonly = readonly;
}

function loadTableFormat(recordtype)
{
	currentRecordType = recordtype;
	function onFileListReady() {
		
		this.myCustomThumbnailFormatter1 = function(elCell, oRecord, oColumn, oData) {
			if (oRecord.getData("FileType1") == "Image")
			{
				var url = oRecord.getData("Path1");
				var date = oRecord.getData("DateEntered1");
				var filename = oRecord.getData("FileName1");
				var html = "<div style='text-align: center'><a href=\"" + url + "\" target=\"_blank\" ><img class=\"uploadedimage\" src=\"" + oData + "\" alt=\"" + oData + "\"  /></a></div>";
				if (filereadonly || accesslevel != "Admin")
				{
					html += "<div class=\"filenamediv\">" + date;
					html += "<br/>" + filename;
					html += "</div>";
				}
				else
				{
					html += "<div class=\"adminfilenamediv\">" + date;
					html += "<br/>" + filename;
					var fileid = oRecord.getData("FileID1");
					html += "</div><div class=\"deletediv\"><img src=\"/images/delete-icon.png\" alt=\"Delete\" width=\"20px\" onclick=\"removeFile(" + fileid + ")\" onmouseover=\"document.body.style.cursor = 'pointer'\" onmouseout=\"document.body.style.cursor = 'default'\"></div>";
				}
				elCell.innerHTML = html;
			}
			else if (oRecord.getData("FileType1") !== undefined)
			{
				var url = oRecord.getData("Path1");
				var date = oRecord.getData("DateEntered1");
				var filename = oRecord.getData("FileName1");
				var html = "<div style='text-align: center'><a href=\"" + url + "\" target=\"_blank\" >" + oData + "</a></div>";
				if (filereadonly || accesslevel != "Admin")
				{
					html += "<div class=\"filenamediv\">" + date;
					html += "<br/>" + filename;
				}
				else
				{
					html += "<div class=\"adminfilenamediv\">" + date;
					html += "<br/>" + filename;
					var fileid = oRecord.getData("FileID1");
					html += "</div><div class=\"deletediv\"><img src=\"/images/delete-icon.png\" alt=\"Delete\" width=\"20px\" onclick=\"removeFile(" + fileid + ")\" onmouseover=\"document.body.style.cursor = 'pointer'\" onmouseout=\"document.body.style.cursor = 'default'\"></div>";
				}
				elCell.innerHTML = html;
			}
			else 
			{
				elCell.innerHTML = "";
			}
	     };
		
	     this.myCustomThumbnailFormatter2 = function(elCell, oRecord, oColumn, oData) {
			if (oRecord.getData("FileType2") == "Image")
			{
				var url = oRecord.getData("Path2");
				var date = oRecord.getData("DateEntered2");
				var filename = oRecord.getData("FileName2");
				var html = "<div style='text-align: center'><a href=\"" + url + "\" target=\"_blank\" ><img class=\"uploadedimage\" src=\"" + oData + "\" alt=\"" + oData + "\"  /></a></div>";
				if (filereadonly || accesslevel != "Admin")
				{
					html += "<div class=\"filenamediv\">" + date;
					html += "<br/>" + filename;
					html += "</div>";
				}
				else
				{
					html += "<div class=\"adminfilenamediv\">" + date;
					html += "<br/>" + filename;
					var fileid = oRecord.getData("FileID2");
					html += "</div><div class=\"deletediv\"><img src=\"/images/delete-icon.png\" alt=\"Delete\" width=\"20px\" onclick=\"removeFile(" + fileid + ")\" onmouseover=\"document.body.style.cursor = 'pointer'\" onmouseout=\"document.body.style.cursor = 'default'\"></div>";
				}
				elCell.innerHTML = html;
			}
			else if (oRecord.getData("FileType2") !== undefined)
			{
				var url = oRecord.getData("Path2");
				var date = oRecord.getData("DateEntered2");
				var filename = oRecord.getData("FileName2");
				var html = "<div style='text-align: center'><a href=\"" + url + "\" target=\"_blank\" >" + oData + "</a></div>";	 
				if (filereadonly || accesslevel != "Admin")
				{
					html += "<div class=\"filenamediv\">" + date;
					html += "<br/>" + filename;
				}
				else
				{
					html += "<div class=\"adminfilenamediv\">" + date;
					html += "<br/>" + filename;
					var fileid = oRecord.getData("FileID2");
					html += "</div><div class=\"deletediv\"><img src=\"/images/delete-icon.png\" alt=\"Delete\" width=\"20px\" onclick=\"removeFile(" + fileid + ")\" onmouseover=\"document.body.style.cursor = 'pointer'\" onmouseout=\"document.body.style.cursor = 'default'\"></div>";
				}
				elCell.innerHTML = html;
			}
			else 
			{
				elCell.innerHTML = "";
			}
	     };
	     
	     this.myCustomThumbnailFormatter3 = function(elCell, oRecord, oColumn, oData) {
			if (oRecord.getData("FileType3") == "Image")
			{
				var url = oRecord.getData("Path3");
				var date = oRecord.getData("DateEntered3");
				var filename = oRecord.getData("FileName3");
				var html = "<div style='text-align: center'><a href=\"" + url + "\" target=\"_blank\" ><img class=\"uploadedimage\" src=\"" + oData + "\" alt=\"" + oData + "\"  /></a></div>";
				if (filereadonly || accesslevel != "Admin")
				{
					html += "<div class=\"filenamediv\">" + date;
					html += "<br/>" + filename;
					html += "</div>";
				}
				else
				{
					html += "<div class=\"adminfilenamediv\">" + date;
					html += "<br/>" + filename;
					var fileid = oRecord.getData("FileID3");
					html += "</div><div class=\"deletediv\"><img src=\"/images/delete-icon.png\" alt=\"Delete\" width=\"20px\" onclick=\"removeFile(" + fileid + ")\" onmouseover=\"document.body.style.cursor = 'pointer'\" onmouseout=\"document.body.style.cursor = 'default'\"></div>";
				}
				elCell.innerHTML = html;
			}
			else if (oRecord.getData("FileType3") !== undefined)
			{
				var url = oRecord.getData("Path3");
				var date = oRecord.getData("DateEntered3");
				var filename = oRecord.getData("FileName3");
				var html = "<div style='text-align: center'><a href=\"" + url + "\" target=\"_blank\" >" + oData + "</a></div>";
				if (filereadonly || accesslevel != "Admin")
				{
					html += "<div class=\"filenamediv\">" + date;
					html += "<br/>" + filename;
				}
				else
				{
					html += "<div class=\"adminfilenamediv\">" + date;
					html += "<br/>" + filename;
					var fileid = oRecord.getData("FileID3");
					html += "</div><div class=\"deletediv\"><img src=\"/images/delete-icon.png\" alt=\"Delete\" width=\"20px\" onclick=\"removeFile(" + fileid + ")\" onmouseover=\"document.body.style.cursor = 'pointer'\" onmouseout=\"document.body.style.cursor = 'default'\"></div>";
				}
				elCell.innerHTML = html;
			}
			else 
			{
				elCell.innerHTML = "";
			}
			
	     };
	     
		// Add the custom formatter to the shortcuts
	  	YAHOO.widget.DataTable.Formatter.myCustomThumbnail1 = this.myCustomThumbnailFormatter1;
	  	YAHOO.widget.DataTable.Formatter.myCustomThumbnail2 = this.myCustomThumbnailFormatter2;
	  	YAHOO.widget.DataTable.Formatter.myCustomThumbnail3 = this.myCustomThumbnailFormatter3;
	  	
	  	var myColumnDefs = [
		        {key: "Thumbnail1", label: "", formatter: "myCustomThumbnail1", editor: new YAHOO.widget.TextboxCellEditor(), maxAutoWidth: 300},
		        {key: "Thumbnail2", label: "", formatter: "myCustomThumbnail2", editor: new YAHOO.widget.TextboxCellEditor(),maxAutoWidth: 300},
		        {key: "Thumbnail3", label: "", formatter: "myCustomThumbnail3", editor: new YAHOO.widget.TextboxCellEditor()}
		];
	  	
	
		var myDataSource = new YAHOO.util.DataSource(baseUrl + "recordfiles/findfiles?");
        myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myDataSource.responseSchema = {
            resultsList: "Result",
            fields: ["Thumbnail1", "Thumbnail2", "Thumbnail3", "Path1", "Path2", "Path3", "FileName1", "FileName2", "FileName3", 
                     "FileType1", "FileType2", "FileType3", "DateEntered1", "DateEntered2", "DateEntered3", "FileID1", "FileID2", "FileID3"]
         };
	
        var initialReq = "recordtype=" + recordtype;
        if (document.getElementById('filesearchinput').value != "")
        {
        	initialReq += "&filesearch=" + document.getElementById('filesearchinput').value;
        }
        filesDataTable = new YAHOO.widget.ScrollingDataTable("files", myColumnDefs, myDataSource, 
        		{initialRequest: initialReq, dynamicData: true, 
	 			scrollable:true, height:"43em", width:"100%"});
        
        //Set the proper value in the text box before showing.
        filesDataTable.doBeforeShowCellEditor = function(oEditor) {

        	var record = oEditor.getRecord();
        	var col = oEditor.getColumn();
        	if (col.key == "Thumbnail1")
        	{
        		oEditor.value = record.getData("FileName1");
            }
        	else if (col.key == "Thumbnail2")
        	{
        		oEditor.value = record.getData("FileName2");
            }
        	else if (col.key == "Thumbnail3")
        	{
        		oEditor.value = record.getData("FileName3");
            }
        	oEditor.show();
        };
        
        if (window.isEditable && isEditable() && !filereadonly && accesslevel == "Admin")
        {
	        filesDataTable.subscribe("cellDblclickEvent",filesDataTable.onEventShowCellEditor);
	    	filesDataTable.subscribe("editorBlurEvent", filesDataTable.onEventSaveCellEditor); 
	    	
	    	// When cell is edited, pulse the color of the row yellow
	        var onCellEdit = function(oArgs) {
	        	var elCell = oArgs.editor.getTdEl();
				var oOldData = oArgs.oldData;
				var oNewData = oArgs.newData;
				var elRow = this.getTrEl(elCell);
				
				var elCellColumn = oArgs.editor.getColumn();
				
				var elRecord = filesDataTable.getRecord(elRow);
				var filename = oNewData;
				var fileid = "";
				if (elCellColumn.key == "Thumbnail1")
				{
					fileid = elRecord.getData("FileID1");
				}
				else if (elCellColumn.key == "Thumbnail2")
				{
					fileid = elRecord.getData("FileID2");
				}
				else if (elCellColumn.key == "Thumbnail3")
				{
					fileid = elRecord.getData("FileID3");
				}
				
				var pkid = document.getElementById("hiddenfilepkid").value;
				var myAjax = new Ajax.Request(baseUrl + "recordfiles/renamefile",
				    	{method: 'get', 
							parameters: {fileID: fileid, fileName: filename, recordtype: currentRecordType, pkid: pkid},
							onComplete: function(transport)
							{
								//Get the returned primary key
								if (transport.responseJSON != null)
								{
									var val = JSON.parse(transport.responseText);
									//If there is an error message, show it and
									//put the values back
									if (val.ErrorMessage != undefined)
									{
										alert(val.ErrorMessage);
									}
									else
									{
										loadTableFormat(currentRecordType);
									}
								}
							}
				    	}
					);
				
	        };
	        filesDataTable.subscribe("editorSaveEvent", onCellEdit);
        }
        
        
        Event.stopObserving("filesearchinput", "keypress", somethingHasChanged);
	};

	YAHOO.util.Event.onDOMReady(onFileListReady);
	   
}
function removeFile(fileID)
{
	var myAjax = new Ajax.Request(baseUrl + "recordfiles/removefile",
			{method: 'get', 
			 parameters: {fileID: fileID, recordtype: currentRecordType},
			 onComplete: function() {
			 loadTableFormat(currentRecordType);}
			});
}

function initFileButtons()
{	
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oFileUpload = new YAHOO.widget.Button("fileuploadbutton");
		oFileUpload.on('click', fileUpload);
	}

    YAHOO.util.Event.onContentReady("filebuttons", onButtonsReady);
}

function fileUpload()
{
	var pkid = document.getElementById("hiddenfilepkid").value;
	window.location = baseUrl + "recordfiles/fileupload/pkid/"+pkid+"/recordtype/"+currentRecordType;
}

function updateFilesTable(event)
{
	if (event.keyCode == 13) {
		loadTableFormat(currentRecordType);
	}	
}

function removeFileFromTempDirectory(filename)
{
	var myAjax = new Ajax.Request(baseUrl + "recordfiles/removefromtemporaryfiles",
	    	{method: 'get', 
				parameters: {filename: filename}
	});	
}