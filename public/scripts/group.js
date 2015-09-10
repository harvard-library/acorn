function transferListEmpty()
{
	this.invoke = function()
	{
		document.getElementById("matchingaddtotransferlistbutton").style.display = "block";
	};
}

function initTabViewBrowserHistory(startTab)
{
	//document.getElementById("temptransfer").style.display = "none";
	//document.getElementById("temptransferreturn").style.display = "none";
	
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
            	var myAjax = new Ajax.Request(baseUrl + "group/istransferfunctiondisabled",
        	    		{method: 'get',
        				parameters: {transferfunction: newState},
        				onComplete: function(transport) {
        					if (transport.responseJSON != null)
        		        	{
        						var val = JSON.parse(transport.responseText);
        		        		var disabled = val.IsDisabled;
        		        		if (disabled)
        		        		{
        			        		document.getElementById('matchingtransferbuttons').style.display = 'none';
        		        		}
        		        		else
        		        		{
        		        			document.getElementById('matchingtransferbuttons').style.display = 'block';
            		        	}
        		        	}
        					YAHOO.util.History.navigate("tabview", newState);
        	                refreshTabData(tabIndex);
        				}
        				}
        				);
                
            }
        } catch (e) {
            tabView.set("activeIndex", newState.substr(3));
        }
    } 
    
    function refreshTabData(tabIndex)
    {
    	if (tabIndex == 0 || tabIndex == 2 || tabIndex == 3 || tabIndex == 5){
			document.getElementById("transferlistarea").style.display = "none";
			document.getElementById("matchingtransfergrouprecordarea").style.display = "none";
		}
		else{
			document.getElementById("transferlistarea").style.display = "block";
			document.getElementById("matchingtransfergrouprecordarea").style.display = "block";
		}
    	document.getElementById('transferformmenuitem').style.display = "block";
		document.getElementById('groupproposalformmenuitem').style.display = "block";
		document.getElementById('groupreportformmenuitem').style.display = "block";

		if (tabIndex == 0)
		{
			loadGroupIdentificationIncludes();
			calendarChooser = unlockChooser;
		}
		else if (tabIndex == 1)
		{
			loadLoginTransferList(transferListEmpty, document.grouploginform);
			var selectedIndex = document.getElementById('loginfromselect').value;
			loadMatchingGroupRecords(selectedIndex);
			updateMatchingButtons("login", document.grouploginform.loginfromselect.value, document.grouploginform.logintoselect.value);
			calendarChooser = loginChooser;
		}
		else if (tabIndex == 2)
		{
			loadGroupProposalIncludes();
			calendarChooser = proposalChooser;
		}
		else if (tabIndex == 3)
		{
			loadGroupReportIncludes();
			calendarChooser = reportChooser;
		}
		else if (tabIndex == 4)
		{
			loadLogoutTransferList(transferListEmpty, document.grouplogoutform);
			var selectedIndex = document.getElementById('logoutfromselect').value;
			loadMatchingGroupRecords(selectedIndex);
			updateMatchingButtons("logout", document.grouplogoutform.logoutfromselect.value, document.grouplogoutform.logouttoselect.value);
			calendarChooser = logoutChooser;
		}
		else if (tabIndex == 5)
		{
			loadTableFormat("group");
		}
		else if (tabIndex == 6)
		{
			loadTempTransferList(transferListEmpty, document.grouptemporarytransferform);
			var selectedIndex = document.getElementById('transferfromselect').value;
			loadMatchingGroupRecords(selectedIndex);
			updateMatchingButtons("temptransfer", document.grouptemporarytransferform.transferfromselect.value, document.grouptemporarytransferform.transfertoselect.value);
			calendarChooser = temptransferChooser;
		}
		else if (tabIndex == 7)
		{
			loadTempTransferReturnList(transferListEmpty, document.grouptemporarytransferreturnform);
			var selectedIndex = document.getElementById('transferreturnfromselect').value;
			loadMatchingGroupRecords(selectedIndex);
			updateMatchingButtons("temptransferreturn", document.grouptemporarytransferreturnform.transferreturnfromselect.value, document.grouptemporarytransferreturnform.transferreturntoselect.value);
			calendarChooser = tempreturnChooser;
		}
		loadGroupRecords(tabIndex);
		var renderTab = 'tab' + tabIndex;
		var myAjax = new Ajax.Request(baseUrl + "group/setrendertab",
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
        
        var myAjax = new Ajax.Request(baseUrl + "group/istransferfunctiondisabled",
	    		{method: 'get',
				parameters: {transferfunction: currentState},
				onComplete: function(transport) {
					if (transport.responseJSON != null)
		        	{
						var val = JSON.parse(transport.responseText);
		        		var disabled = val.IsDisabled;
		        		if (disabled)
		        		{
			        		document.getElementById('matchingtransferbuttons').style.display = 'none';
		        		}
		        		else
		        		{
		        			document.getElementById('matchingtransferbuttons').style.display = 'block';
    		        	}
		        	}
					tabView.set("activeIndex", currentState.substr(3));
				}
				}
				);
        
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

var isBeingEdited = false;
var currentGroupID = null;
var currentUserID = null;
function initButtons(newaccesslevel, userid, groupnumber)
{
	currentGroupID = groupnumber;
	currentUserID = userid;
	setAccessLevel(newaccesslevel);
	if (document.getElementById('temptransfermenuitem') != null)
	{
		document.getElementById('temptransfermenuitem').style.display = "block";
	}

	if (accesslevel == 'Admin' || accesslevel == 'Regular')
	{
		initWPC();
	}
	else
	{
		initRepository();
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

	loginChooser = new YAHOO.widget.Calendar("loginChooser","loginCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
  	loginChooser.render();
  	loginChooser.selectEvent.subscribe(handleSelect, loginChooser, true);
  	
  	proposalChooser = new YAHOO.widget.Calendar("proposalChooser","proposalCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    proposalChooser.render();
    proposalChooser.selectEvent.subscribe(handleSelect, proposalChooser, true);
  	
  	examChooser = new YAHOO.widget.Calendar("examChooser","examCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
  	examChooser.render();
  	examChooser.selectEvent.subscribe(handleExamSelect, examChooser, true);
  	
  	reportChooser = new YAHOO.widget.Calendar("reportChooser","reportCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    reportChooser.render();
    reportChooser.selectEvent.subscribe(handleSelect, reportChooser, true);
    
    logoutChooser = new YAHOO.widget.Calendar("logoutChooser","logoutCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    logoutChooser.render();
    logoutChooser.selectEvent.subscribe(handleSelect, logoutChooser, true);
    
    temptransferChooser = new YAHOO.widget.Calendar("temptransferChooser","temptransferCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    temptransferChooser.render();
    temptransferChooser.selectEvent.subscribe(handleSelect, temptransferChooser, true);

    tempreturnChooser = new YAHOO.widget.Calendar("tempreturnChooser","tempreturnCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    tempreturnChooser.render();
    tempreturnChooser.selectEvent.subscribe(handleSelect, tempreturnChooser, true);
}

function initRepository()
{
	document.getElementById("groupidentificationbuttons").style.display = "none";
	document.getElementById("editprojectbutton").style.display = "none";
	
	function onHollisButtonReady() {

		//Keep the hollis button enabled.
		document.getElementById("gotohollisbutton").disabled = false;
		
    	//Makes the buttons YUI widgets for a nicer look.
    	var oGoToHollisButton3 = new YAHOO.widget.Button("gotohollisbutton");
    }

    YAHOO.util.Event.onContentReady("gotohollisbutton", onHollisButtonReady);
    
    returnDateChooser = new YAHOO.widget.Calendar("returnDateChooser","returnCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    returnDateChooser.render();
    returnDateChooser.selectEvent.subscribe(handleReturnDateSelect, returnDateChooser, true);

	document.getElementById("proposalbuttons").style.display = "none";
	document.getElementById("reportbuttons").style.display = "none";
}

function initIdentificationButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oUpdateButton = new YAHOO.widget.Button("updaterecordidsbutton");
		oUpdateButton.on('click', updateRecords);
		var oClearChangesButton = new YAHOO.widget.Button("clearidentificationbutton");
		oClearChangesButton.on('click', resetIdentification);
		var oDeleteButton = new YAHOO.widget.Button("deletebutton");
		oDeleteButton.on('click', deleteGroup);
		if (accesslevel != 'Admin')
	    {
			oDeleteButton.setStyle('display', 'none');
		}
		var oUnlockButton = new YAHOO.widget.Button("unlockgroupbutton");
		oUnlockButton.on('click', unlockGroup);
		if (accesslevel != 'Admin')
	    {
			oUnlockButton.setStyle('display', 'none');
		}
	}
	
    YAHOO.util.Event.onContentReady("groupidentificationbuttons", onButtonsReady);
    
    function onProjectButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
    	var oEditProjectButton = new YAHOO.widget.Button("editprojectbutton");
    	oEditProjectButton.on('click', editProject);
    }

    YAHOO.util.Event.onContentReady("editprojectbutton", onProjectButtonReady);
    
    function onGroupButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
    	var oSaveGroupButton = new YAHOO.widget.Button("savegroupbutton");
    	oSaveGroupButton.on('click', saveGroup);
    }

    YAHOO.util.Event.onContentReady("savegroupbutton", onGroupButtonReady);
    
    function onHollisButtonReady() {

    	//Makes the buttons YUI widgets for a nicer look.
    	var oGoToHollisButton = new YAHOO.widget.Button("gotohollisbutton");
    	oGoToHollisButton.on('click', goToHOLLIS);
    }

    YAHOO.util.Event.onContentReady("gotohollisbutton", onHollisButtonReady);
    
    unlockChooser = new YAHOO.widget.Calendar("unlockChooser","unlockCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    unlockChooser.render();
    unlockChooser.selectEvent.subscribe(handleSelect, unlockChooser, true);
    
    returnDateChooser = new YAHOO.widget.Calendar("returnDateChooser","returnCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    returnDateChooser.render();
    returnDateChooser.selectEvent.subscribe(handleReturnDateSelect, returnDateChooser, true);

    function onNewRecordsButtonReady() {

    	//Makes the buttons YUI widgets for a nicer look.
    	var oCreateNewRecordsButton = new YAHOO.widget.Button("createnewrecordsbutton", {type: "submit"});
    }

    YAHOO.util.Event.onContentReady("createnewrecordsbutton", onNewRecordsButtonReady);
}

function resetIdentification()
{
	document.identificationform.reset();
	var myAjax = new Ajax.Request(baseUrl + "groupidentification/resetidentification",
    		{method: 'get',
			onComplete: loadGroupIdentificationIncludes}
			);
}

function unlockGroup()
{
	var date = document.getElementById('unlockuntildateinput').value;
	var myAjax = new Ajax.Request(baseUrl + "group/unlockgroup",
    		{method: 'get',
			parameters: {unlockuntildateinput: date},
			onComplete: function(transport){
				if (transport.responseJSON != null)
	        	{
					var val = JSON.parse(transport.responseText);
	        		var success = val.UnlockSuccessful;
	        		if (!success)
	        		{
		        		document.getElementById('iderrors').innerHTML = "There was a problem unlocking the group.";
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

function goToHOLLIS()
{
	var hollisNum = document.getElementById('hollisnumberinput').value;
	while (hollisNum.length < 9) hollisNum = "0" + hollisNum;
	window.open("http://id.lib.harvard.edu/aleph/" + hollisNum + "/catalog");
}

function editProject()
{
	window.open(baseUrl + 'admin/editlists');
}


function saveGroup()
{
	document.identificationform.action = baseUrl + "group/savegroup";
	document.identificationform.submit();
}

function deleteGroup()
{
	window.location = baseUrl + "group/deletegroup";
}

function updateRecords()
{
	document.identificationform.action = baseUrl + "groupidentification/updaterecords";
	document.identificationform.submit();
}

function initProposalButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSaveButton = new YAHOO.widget.Button("saveproposalbutton", {type: "submit"});
		var oClearChangesButton2 = new YAHOO.widget.Button("clearproposalbutton");
		oClearChangesButton2.on("click", resetProposal);
		
	}

    YAHOO.util.Event.onContentReady("proposalbuttons", onButtonsReady);
}

function initReportButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSaveButton = new YAHOO.widget.Button("savereportbutton", {type: "submit"});
		var oClearChangesButton = new YAHOO.widget.Button("clearreportbutton");
		oClearChangesButton.on('click', resetReport);
	}

    YAHOO.util.Event.onContentReady("reportbuttons", onButtonsReady);
}

function resetProposal()
{
	document.proposalform.reset();
	var myAjax = new Ajax.Request(baseUrl + "groupproposal/resetproposal",
    		{method: 'get',
			onComplete: loadProposalReportIncludes}
			);
}

function resetReport()
{
	document.groupreportform.reset();
	var myAjax = new Ajax.Request(baseUrl + "groupreport/resetreport",
    		{method: 'get',
			onComplete: loadGroupReportIncludes}
			);
}

function createGroupProposalReport()
{
	var myAjax = new Ajax.Request(baseUrl + "reports/groupproposalform",
    		{method: 'get', 
			onSuccess: function(transport)
			{
				//Open the report.
				var filename = transport.responseText.trim();
				window.open(baseUrl + 'userreports/pdfreports/' + filename);
			}
    		});
}

function createGroupReportForm()
{
	var myAjax = new Ajax.Request(baseUrl + "reports/groupreportform",
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

