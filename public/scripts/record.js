function transferListEmpty(buttonname)
{
	this.buttonname = buttonname;
	var method = this;
	this.invoke = function()
	{
		document.getElementById(method.buttonname).style.display = "block";
	};
}

function initTabViewBrowserHistory(startTab)
{
	// The initially selected tab will be chosen in the following order:
    //
    // URL fragment identifier (it will be there if the user previously
    // bookmarked the application in a specific state)
    //
    //         or
    //
    // "tab0" (default)

    var bookmarkedTabViewState = YAHOO.util.History.getBookmarkedState("tabview");
    var initialTabViewState = bookmarkedTabViewState || startTab;
    refreshTabData(initialTabViewState.substr(3));
    
    var tabView;

    // Register our TabView module. Module registration MUST
    // take place before calling YAHOO.util.History.initialize.
    YAHOO.util.History.register("tabview", initialTabViewState, function (state) {
        // This is called after calling YAHOO.util.History.navigate, or after the user
        // has trigerred the back/forward button. We cannot discrminate between
        // these two situations.

        // "state" can be "tab0", "tab1" or "tab2".
        // Select the right tab:
    	tabView.set("activeIndex", state.substr(3));
    }); 
    
    function handleTabViewActiveTabChange (e) {
        var newState, currentState, tabIndex;

        tabIndex = this.getTabIndex(e.newValue);
        newState = "tab" + tabIndex;
        try {
            currentState = YAHOO.util.History.getCurrentState("tabview");
            // The following test is crucial. Otherwise, we end up circling forever.
            // Indeed, YAHOO.util.History.navigate will call the module onStateChange
            // callback, which will call tabView.set, which will call this handler
            // and it keeps going from here...
            if (newState != currentState) {
            	if (tabChangeCheckForModifications())
            	{
            		YAHOO.util.History.navigate("tabview", newState);
            		refreshTabData(tabIndex);
            	}
            	else
            	{
            		YAHOO.util.History.navigate("tabview", currentState);
            		refreshTabData(currentState.substr(3));
            		tabView.set("activeIndex", currentState.substr(3));
                	
            	}
            }
        } catch (e) {
            tabView.set("activeIndex", newState.substr(3));
        }

    } 
    
    function refreshTabData(tabIndex)
    {
    	if (tabIndex == 0 || tabIndex == 2 || tabIndex == 3 || tabIndex == 5){
			document.getElementById("transferlistarea").style.display = "none";
		}
		else{
			document.getElementById("transferlistarea").style.display = "block";
		}
    	document.getElementById('transferformmenuitem').style.display = "block";
		document.getElementById('proposalformmenuitem').style.display = "block";
		document.getElementById('reportformmenuitem').style.display = "block";

		if (tabIndex == 0)
		{
			loadRecordIdentificationIncludes();
			initDuplicateOverrideButtons();
			calendarChooser = unlockChooser;
		}
		else if (tabIndex == 1)
		{
			loadLoginTransferList(transferListEmpty('logintransferbuttondiv'), document.recordloginform);
			updateTransferButtons("login");
			calendarChooser = loginChooser;
		}
		else if (tabIndex == 2)
		{
			loadProposalReportIncludes();
			calendarChooser = proposalChooser;
		}
		else if (tabIndex == 3)
		{
			loadRecordReportIncludes();
			calendarChooser = reportChooser;
		}
		else if (tabIndex == 4)
		{
			loadLogoutTransferList(transferListEmpty('logouttransferbuttondiv'), document.recordlogoutform);
			updateTransferButtons("logout");
			calendarChooser = logoutChooser;
		}
		else if (tabIndex == 5)
		{
			loadTableFormat("item");
		}
		else if (tabIndex == 6)
		{
			loadTempTransferList(transferListEmpty('temptransferbuttondiv'), document.recordtemporarytransferform);
			updateTransferButtons("temptransfer");
			calendarChooser = temptransferChooser;
		}
		else if (tabIndex == 7)
		{
			loadTempTransferReturnList(transferListEmpty('returntransferbuttondiv'), document.recordtemporarytransferreturnform);
			updateTransferButtons("temptransferreturn");
			calendarChooser = tempreturnChooser;
		}
		
		var renderTab = 'tab' + tabIndex;
		var myAjax = new Ajax.Request(baseUrl + "record/setrendertab",
	    		{method: 'get',
				parameters: {renderTab: renderTab}}
				);
    }
	
	function initTabView () {
        // Instantiate the TabView control...
        tabView = new YAHOO.widget.TabView("demo");
        tabView.addListener("activeTabChange", handleTabViewActiveTabChange);
    }
	
	// Use the Browser History Manager onReady method to instantiate the TabView widget.
    YAHOO.util.History.onReady(function () {
        var currentState;
        initTabView();

        // This is the tricky part... The onLoad event is fired when the user
        // comes back to the page using the back button. In this case, the
        // actual tab that needs to be selected corresponds to the last tab
        // selected before leaving the page, and not the initially selected tab.
        // This can be retrieved using getCurrentState:
        currentState = YAHOO.util.History.getCurrentState("tabview");
        tabView.set("activeIndex", currentState.substr(3));
    });

    // Initialize the browser history management library.
    try {
    	YAHOO.util.History.initialize("yui-history-field", "yui-history-iframe");
    } catch (e) {
        // The only exception that gets thrown here is when the browser is
        // not supported (Opera, or not A-grade) Degrade gracefully.
        initTabView();
    }
}

var currentItemID = null;
var isBeingEdited = false;
var currentUserID = null;
function initButtons(newaccesslevel, userid, recordnumber, isbeingedited)
{
	setRecordEdit();
	currentUserID = userid;
	currentItemID = recordnumber;
	if (isbeingedited == 1)
	{
		isBeingEdited = true;
	}
	setAccessLevel(newaccesslevel);
	if (document.getElementById('temptransfermenuitem'))
	{
		document.getElementById('temptransfermenuitem').style.display = "block";
	}

	if (accesslevel == 'Admin' || accesslevel == 'Regular')
	{
		if (isBeingEdited)
		{
			initRepository();
		}
		else
		{
			initWPC();
		}
	}
	else
	{
		initRepository();
		document.getElementById('repositoryselect').disabled = true;
	}
	initTransferDialog();
}

var calendarChooser = null;
var loginChooser = null;
var logoutChooser = null;
var proposalChooser = null;
var examChooser = null;
var reportChooser = null;
var temptransferChooser = null;
var tempreturnChooser = null;
var unlockChooser = null;
var returnDateChooser = null;

function initWPC()
{
	initIdentificationButtons();
	initProposalButtons();
	initReportButtons();
	initFileButtons();
	initTransferButtons();
	initTransferReturnButtons();
	initLoginButtons();
	initLogoutButtons();
}

function initRepository()
{
	function onIdButtonsReady() {

			//Makes the buttons YUI widgets for a nicer look.
			var oSaveButton = new YAHOO.widget.Button("saveidentificationbutton", {type: "submit"});
			var oClearChangesButton = new YAHOO.widget.Button("clearidentificationbutton");
			oClearChangesButton.on('click', resetIdentification);
			var oDeleteButton = new YAHOO.widget.Button("deletebutton");
			oDeleteButton.setStyle('display', 'none');
			var oStatusButton = new YAHOO.widget.Button("statusbutton");
			oStatusButton.on('click', function(){
				var recid = document.getElementById('hiddenitemid').value;
				window.location = baseUrl + "record/recordstatus/recordnumber/"+recid;
			});
			document.getElementById("saveidentificationbutton").disabled = false;
			document.getElementById("clearidentificationbutton").disabled = false;
			
	}

	YAHOO.util.Event.onContentReady("identificationbuttons", onIdButtonsReady);	    
		
	document.getElementById("editprojectbutton").style.display = "none";
	
    function onButtonsReady() {

		var oViewProposalApproval = new YAHOO.widget.Button("viewproposalapprovalbutton");
		oViewProposalApproval.on('click', function(){
			window.location = baseUrl + "proposalapproval/index/recordnumber/"+currentItemID+"#tabview=tab1";
		});
	}

    YAHOO.util.Event.onContentReady("proposalbuttons", onButtonsReady);
    
    returnDateChooser = new YAHOO.widget.Calendar("returnDateChooser","returnCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    returnDateChooser.render();
    returnDateChooser.selectEvent.subscribe(handleReturnDateSelect, returnDateChooser, true);
    
    document.getElementById("loginbuttons").style.display = "none";
	document.getElementById("proposalbuttons").style.display = "none";
	document.getElementById("emailproposaldiv").style.display = "none";
	document.getElementById("reportbuttons").style.display = "none";
	document.getElementById("reportsummarybuttons").style.display = "none";
	document.getElementById("logoutbuttons").style.display = "none";
	document.getElementById("transferbuttons").style.display = "none";
	document.getElementById("returnbuttons").style.display = "none";
}

function saveIdentification()
{
	mustConfirmLeave = false;
	document.identificationform.submit();
}

function overrideDuplicateCallNumber()
{
	var callnumber = document.getElementById("callnumberhidden").value;
	var myAjax = new Ajax.Request(baseUrl + "recordidentification/overridecallnumber",
    		{method: 'get',
			parameters: {callnumber: callnumber},
			onComplete: function(transport){
				if (transport.responseJSON != null)
	        	{
					var val = JSON.parse(transport.responseText);
	        		var success = val.Success;
	        		if (success)
	        		{
	        			document.getElementById('callnumbermessage').innerHTML = "";
	        			document.getElementById('callnumberhidden').value = "";
		        		
		        		document.getElementById('idsavecallbuttondiv').style.display = "none";
	        		}
	        	}
			}
    		}
		);
}

function overrideDuplicateTitle()
{
	var myAjax = new Ajax.Request(baseUrl + "recordidentification/overridetitle",
    		{method: 'get',
			parameters: {title: document.getElementById('titleinput').value},
			onComplete: function(transport){
				if (transport.responseJSON != null)
	        	{
					var val = JSON.parse(transport.responseText);
	        		var success = val.Success;
	        		if (success)
	        		{
		        		var titlediv = document.getElementById('titlediv');
		        		var ul = titlediv.getElementsByTagName('ul');
		        		ul[0].style.display = "none";
		        		
		        		document.getElementById('idsavetitlebuttondiv').style.display = "none";
	        		}
	        	}
			}
    		}
		);
}

function initIdentificationButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSaveButton = new YAHOO.widget.Button("saveidentificationbutton");
		oSaveButton.on('click', saveIdentification);
		var oClearChangesButton = new YAHOO.widget.Button("clearidentificationbutton");
		oClearChangesButton.on('click', resetIdentification);
		var oStatusButton = new YAHOO.widget.Button("statusbutton");
		oStatusButton.on('click', function(){
			var recid = document.getElementById('hiddenitemid').value;
			window.location = baseUrl + "record/recordstatus/recordnumber/"+recid;
		});
		var oDoneButton = new YAHOO.widget.Button("donebutton");
		oDoneButton.on('click', markRecordAsDone);
		if (accesslevel != 'Admin')
	    {
			oDoneButton.setStyle('display', 'none');
		}
		var oDeleteButton = new YAHOO.widget.Button("deletebutton");
		oDeleteButton.on('click', deleteRecord);
		if (accesslevel != 'Admin')
	    {
			oDeleteButton.setStyle('display', 'none');
		}
		var oUnlockButton = new YAHOO.widget.Button("unlockrecordbutton");
		oUnlockButton.on('click', unlockRecord);
		if (accesslevel != 'Admin')
	    {
			oUnlockButton.setStyle('display', 'none');
		}
		
		var oOverrideCallButton = new YAHOO.widget.Button("savecallnumberbutton");
		oOverrideCallButton.on('click', overrideDuplicateCallNumber);
		
		var oOverrideTitleButton = new YAHOO.widget.Button("savetitlebutton");
		oOverrideTitleButton.on('click', overrideDuplicateTitle);
		
	}

    YAHOO.util.Event.onContentReady("identificationbuttons", onButtonsReady);
    
    function onProjectButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oEditProjectButton3 = new YAHOO.widget.Button("editprojectbutton");
		oEditProjectButton3.on('click', editProject);
    }

    YAHOO.util.Event.onContentReady("editprojectbutton", onProjectButtonReady);
    
    unlockChooser = new YAHOO.widget.Calendar("unlockChooser","unlockCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    unlockChooser.render();
    unlockChooser.selectEvent.subscribe(handleSelect, unlockChooser, true);
    
    returnDateChooser = new YAHOO.widget.Calendar("returnDateChooser","returnCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    returnDateChooser.render();
    returnDateChooser.selectEvent.subscribe(handleReturnDateSelect, returnDateChooser, true);

}

function unlockRecord()
{
	var date = document.getElementById('unlockuntildateinput').value;
	var myAjax = new Ajax.Request(baseUrl + "record/unlockrecord",
    		{method: 'get',
			parameters: {unlockuntildateinput: date},
			onComplete: function(transport){
				if (transport.responseJSON != null)
	        	{
					var val = JSON.parse(transport.responseText);
	        		var success = val.UnlockSuccessful;
	        		if (!success)
	        		{
		        		document.getElementById('iderrors').innerHTML = "There was a problem unlocking the record.";
	        		}
	        		else
	        		{
	        			window.location.reload(true);
	        		}
	        	}
			}
    		}
		);
}

function resetIdentification()
{
	document.identificationform.reset();
	var myAjax = new Ajax.Request(baseUrl + "record/resetidentification",
    		{method: 'get',
			onComplete: loadRecordIdentificationIncludes}
			);
}

function editProject()
{
	window.open(baseUrl + 'admin/editlists');
}

function deleteRecord()
{
	window.location = baseUrl + "record/deleterecord";
}

function markRecordAsDone()
{
	window.location = baseUrl + "record/markrecorddone";
}

function updateTransferButtons(listname)
{
	var myAjax = new Ajax.Request(baseUrl + "transfers/findcurrenttransferinfo",
    		{method: 'get',
			parameters: {listname: listname},
			onComplete: function (transport)
			{
				var fromto = "";
				if (transport.responseJSON != null)
	        	{
					var val = JSON.parse(transport.responseText);
	        		var f = val.FromLocationID;
	        		if (f.length > 0)
	        		{
		        		var t = val.ToLocationID;
		        		var d = val.DepartmentID;
		        		var c = val.CuratorID;
		        		var ftext = val.FromLocation;
		        		var ttext = val.ToLocation;
		        		var dtext = val.Department;
		        		var curtext = val.Curator;
		        		fromto = "(" + ftext + " to " + ttext + ", " + dtext + ", " + curtext + ")";
	        		}
	        	}
	        	
	        	if (listname == "login")
	        	{
	        		document.getElementById("transferlisttitle").innerHTML = "Login Transfer List " + fromto;
		        	initLoginButtons();
	        	}
	        	else if (listname == "logout")
	        	{
	        		document.getElementById("transferlisttitle").innerHTML = "Logout Transfer List " + fromto;
		        	initLogoutButtons();
	        	}
	        	else if (listname == "temptransfer")
	        	{
	        		document.getElementById("transferlisttitle").innerHTML = "Temp Transfer List " + fromto;
		        	initTransferButtons();
	        	}
	        	else if (listname == "temptransferreturn")
	        	{
	        		document.getElementById("transferlisttitle").innerHTML = "Temp Transfer Return List " + fromto;
		        	initTransferReturnButtons();
	        	}
			}
    	});
}
	

var initialloginfromvalue;
function initLoginButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oTransferButton = new YAHOO.widget.Button("addtotransferlistbutton");
		oTransferButton.on("click", addToLoginTransfer);
        var oLoginSaveButton = new YAHOO.widget.Button("saveloginbutton");
		oLoginSaveButton.on("click", saveLogin);
		var oLoginDeleteButton = new YAHOO.widget.Button("deleteloginbutton");
		oLoginDeleteButton.on("click", deleteLogin);
		if (accesslevel != 'Admin')
	    {
			oLoginDeleteButton.setStyle('display', 'none');
		}
	}

    YAHOO.util.Event.onContentReady("loginbuttons", onButtonsReady);
    
    document.getElementById("loginfromselect").disabled = true;
  	document.getElementById("loginfromoverridecheckbox").checked = false;
    
  	loginChooser = new YAHOO.widget.Calendar("loginChooser","loginCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
  	loginChooser.render();
  	loginChooser.selectEvent.subscribe(handleSelect, loginChooser, true);
}


function saveLogin()
{
	mustConfirmLeave = false;
	document.getElementById("loginfromselect").disabled = false;
	document.recordloginform.submit();
}

function addToLoginTransfer()
{
	mustConfirmLeave = false;
	document.getElementById("loginfromselect").disabled = false;
	document.recordloginform.action = baseUrl + "record/addtotransferlist";
	document.recordloginform.submit();
}

function deleteLogin()
{
	mustConfirmLeave = false;
	window.location = baseUrl + "recordlogin/deletelogin";
}

function toggleLoginFromDisabled(enable)
{
	document.getElementById("loginfromselect").disabled = !enable;
	if (!enable)
	{
		document.getElementById("loginfromselect").value = initialloginfromvalue;
	}
	else
	{
		initialloginfromvalue = document.getElementById("loginfromselect").value;
	}
}

function initProposalButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSaveButton = new YAHOO.widget.Button("saveproposalbutton");
		oSaveButton.on('click', function(){
			mustConfirmLeave = false;
			document.proposalform.submit();
		});
		var oClearChangesButton2 = new YAHOO.widget.Button("clearproposalbutton");
		oClearChangesButton2.on("click", resetProposal);
		
		var oEmailProposal = new YAHOO.widget.Button("emailproposalbutton");
		oEmailProposal.on('click', function(){
			window.location = baseUrl + "proposalapproval/email";
		});
		var oViewProposalApproval = new YAHOO.widget.Button("viewproposalapprovalbutton");
		oViewProposalApproval.on('click', function(){
			window.location = baseUrl + "proposalapproval/index/recordnumber/"+currentItemID+"#tabview=tab1";
		});
	}

    YAHOO.util.Event.onContentReady("proposalbuttons", onButtonsReady);
    
    proposalChooser = new YAHOO.widget.Calendar("proposalChooser","proposalCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    proposalChooser.render();
    proposalChooser.selectEvent.subscribe(handleSelect, proposalChooser, true);
  	
  	examChooser = new YAHOO.widget.Calendar("examChooser","examCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
  	examChooser.render();
  	examChooser.selectEvent.subscribe(handleExamSelect, examChooser, true);
}


function initReportButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSaveButton = new YAHOO.widget.Button("savereportbutton");
		oSaveButton.on('click', function(){
			mustConfirmLeave = false;
			document.reportform.submit();
		});
		var oClearChangesButton2 = new YAHOO.widget.Button("clearreportbutton");
		oClearChangesButton2.on("click", resetReport);
		
		var oEmailReport = new YAHOO.widget.Button("emailreportbutton");
		oEmailReport.on('click', function(){
			window.location = baseUrl + "proposalapproval/emailreport";
		});
		var oViewReport = new YAHOO.widget.Button("viewreportsummarybutton");
		oViewReport.on('click', function(){
			window.location = baseUrl + "proposalapproval/index/recordnumber/"+currentItemID+"#tabview=tab2";
		});
		
	}

    YAHOO.util.Event.onContentReady("reportbuttons", onButtonsReady);
    
    reportChooser = new YAHOO.widget.Calendar("reportChooser","reportCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    reportChooser.render();
    reportChooser.selectEvent.subscribe(handleSelect, reportChooser, true);
}

function resetProposal()
{
	document.proposalform.reset();
	var myAjax = new Ajax.Request(baseUrl + "record/resetproposal",
    		{method: 'get',
			onComplete: loadProposalReportIncludes}
			);
}

function resetReport()
{
	document.reportform.reset();
	var myAjax = new Ajax.Request(baseUrl + "record/resetreport",
    		{method: 'get',
			onComplete: loadRecordReportIncludes}
			);
}

var initiallogoutfromvalue;
function initLogoutButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oTransferButton = new YAHOO.widget.Button("addtologouttransferlistbutton");
		oTransferButton.on("click", addToLogoutTransfer);
        var oSaveButton = new YAHOO.widget.Button("savelogoutbutton");
		oSaveButton.on("click", saveLogout);
		var oDeleteButton3 = new YAHOO.widget.Button("deletelogoutbutton");
		oDeleteButton3.on("click", deleteLogout);
		if (accesslevel != 'Admin')
	    {
			oDeleteButton3.setStyle('display', 'none');
		}
	}

    YAHOO.util.Event.onContentReady("logoutbuttons", onButtonsReady);
    
    document.getElementById("logoutfromselect").disabled = true;
    document.getElementById("logoutfromoverridecheckbox").checked = false;
    
    logoutChooser = new YAHOO.widget.Calendar("logoutChooser","logoutCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    logoutChooser.render();
    logoutChooser.selectEvent.subscribe(handleSelect, logoutChooser, true);
}

function saveLogout()
{
	mustConfirmLeave = false;
	document.getElementById("logoutfromselect").disabled = false;
	document.recordlogoutform.submit();
}

function addToLogoutTransfer()
{
	mustConfirmLeave = false;
	document.getElementById("logoutfromselect").disabled = false;
	document.recordlogoutform.action = baseUrl + "/record/addtotransferlist";
	document.recordlogoutform.submit();
}

function deleteLogout()
{
	mustConfirmLeave = false;
	window.location = baseUrl + "/recordlogout/deletelogout";
}

function toggleLogoutFromDisabled(enable)
{
	document.getElementById("logoutfromselect").disabled = !enable;
	if (!enable)
	{
		document.getElementById("logoutfromselect").value = initiallogoutfromvalue;
	}
	else
	{
		initiallogoutfromvalue = document.getElementById("logoutfromselect").value;
	}
}

function initTransferButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oTransferButton = new YAHOO.widget.Button("addtotemptransferlistbutton");
		oTransferButton.on('click', function(){
			mustConfirmLeave = false;
			document.recordtemporarytransferform.submit();
		});
		var oDeleteButton3 = new YAHOO.widget.Button("deletetransferbutton");
		oDeleteButton3.on("click", deleteTempTransfer);
		if (accesslevel != 'Admin')
	    {
			oDeleteButton3.setStyle('display', 'none');
		}
	}

    YAHOO.util.Event.onContentReady("transferbuttons", onButtonsReady);
    
    temptransferChooser = new YAHOO.widget.Calendar("temptransferChooser","temptransferCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    temptransferChooser.render();
    temptransferChooser.selectEvent.subscribe(handleSelect, temptransferChooser, true);
}

function deleteTempTransfer()
{
	mustConfirmLeave = false;
	window.location = baseUrl + "/recordtemptransfer/deletetemptransfer";
}

function initTransferReturnButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oTransferButton = new YAHOO.widget.Button("addtotempreturnlistbutton");
		oTransferButton.on('click', function(){
			mustConfirmLeave = false;
			document.recordtemporarytransferreturnform.submit();
		});
		var oDeleteButton3 = new YAHOO.widget.Button("deletereturnbutton");
		oDeleteButton3.on("click", deleteTempTransferReturn);
		if (accesslevel != 'Admin')
	    {
			oDeleteButton3.setStyle('display', 'none');
		}
	}

    YAHOO.util.Event.onContentReady("returnbuttons", onButtonsReady);
    
    tempreturnChooser = new YAHOO.widget.Calendar("tempreturnChooser","tempreturnCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    tempreturnChooser.render();
    tempreturnChooser.selectEvent.subscribe(handleSelect, tempreturnChooser, true);
}


function deleteTempTransferReturn()
{
	mustConfirmLeave = false;
	window.location = baseUrl + "/recordtemptransfer/deletetemptransferreturn";
}


function setRecordEdit()
{
	var myAjax = new Ajax.Request(baseUrl + "user/setrecordedit",
    		{method: 'get',
    			parameters: {recordtype: "item"}});
}

function clearEdits()
{
	var myAjax = new Ajax.Request(baseUrl + "user/clearedits",
    		{method: 'get'});
}

function catchSubmit(formname)
{
	mustConfirmLeave = false;
	formname.submit();
}

function createProposalReport()
{
	var myAjax = new Ajax.Request(baseUrl + "reports/itemproposalform",
    		{method: 'get', 
			onSuccess: function(transport)
			{
				//Open the report.
				var filename = transport.responseText.trim();
				window.open(baseUrl + 'userreports/pdfreports/' + filename);
			}
    		});
}

function createReportForm()
{
	var myAjax = new Ajax.Request(baseUrl + "reports/itemreportform",
    		{method: 'get', 
			onSuccess: function(transport)
			{
				//Open the report.
				var filename = transport.responseText.trim();
				window.open(baseUrl + 'userreports/pdfreports/' + filename);
			}
    		});
	
}

var textfieldinputname = '';
function handleSelect(type, args, obj) {
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
	
	document.getElementById(textfieldinputname).value = month + "-" + day + "-" + year;
	calendarChooser.hide();
}

function showCalendarChooser(textfieldinput)
{
	textfieldinputname = textfieldinput;
	if (calendarChooser != null)
	{
		calendarChooser.show();
	}
}

function showExpectedReturnDateCalendarChooser(textfieldinput)
{
	textfieldinputname = textfieldinput;
	if (returnDateChooser != null)
	{
		returnDateChooser.show();
	}
}

function handleReturnDateSelect(type, args, obj) {
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
	
	document.getElementById(textfieldinputname).value = month + "-" + day + "-" + year;
	returnDateChooser.hide();
}

function showExamCalendarChooser(textfieldinput)
{
	textfieldinputname = textfieldinput;
	if (examChooser != null)
	{
		examChooser.show();
	}
}

function handleExamSelect(type, args, obj) {
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
	
	document.getElementById(textfieldinputname).value = month + "-" + day + "-" + year;
	examChooser.hide();
}

function setApprovingCurator()
{
	var appcurator = document.getElementById('approvingcuratorselect').value;
	if (appcurator == null || appcurator == "0")
	{
		document.getElementById('approvingcuratorselect').value = document.getElementById('curatorselect').value;
	}
}

function setChargeTo()
{
	var chargeto = document.getElementById('chargetoselect').value;
	
	if (chargeto == null || chargeto == "0" || chargeto == '')
	{
		loadChargeTo(document.getElementById('repositoryselect').value);
	}
}

function initDuplicateOverrideButtons()
{
	if (document.getElementById('callnumbererrorhidden').value)
	{
		document.getElementById('idsavecallbuttondiv').style.display = 'block';
	}
	if (document.getElementById('titleerrorhidden').value)
	{
		document.getElementById('idsavetitlebuttondiv').style.display = 'block';
	}
}
