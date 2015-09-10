function initTabViewBrowserHistory()
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
    var initialTabViewState = bookmarkedTabViewState || "tab0";
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
    	if (tabIndex == 1)
		{
    		loadHistoryList();
		}
    	else if (tabIndex == 2 || tabIndex == 3)
    	{
    		setFileReadOnly(true);
    		loadTableFormat("item");
    	}
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
var isReadOnly = null;
var isLoggedIn = 0;
function initButtons(recordnumber, readOnly, loggedIn)
{
	setRecordEdit();
	currentItemID = recordnumber;
	isLoggedIn = loggedIn;
	isReadOnly = readOnly;

	initApprovalButtons();
	initHistoryButtons();
	initReportButtons();
}

function initApprovalButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oApproveButton = new YAHOO.widget.Button("approvebutton");
		var oDenyButton = new YAHOO.widget.Button("denybutton");
		if (isReadOnly == 0)
		{
			oApproveButton.on('click', function(){
				document.getElementById('hiddenapprovaltype').value = 'Approved';
				mustConfirmLeave = false;
				document.approvalform.submit();
			});
			oDenyButton.on('click', function(){
				document.getElementById('hiddenapprovaltype').value = 'Denied';
				mustConfirmLeave = false;
				document.approvalform.submit();
			});
			document.getElementById('summaryauthordiv').style.display = "none";
			document.getElementById('historyauthordiv').style.display = "none";
			
		}
		else
		{
			oApproveButton.setStyle('display', 'none');
			oDenyButton.setStyle('display', 'none');
			document.approvalform.action = baseUrl + "proposalapproval/saveapproval/readonly/1";
			document.historyform.action = baseUrl + "proposalapproval/savecomment/readonly/1#tabview=tab1";
			if (isLoggedIn)
			{
				document.getElementById('summaryauthordiv').style.display = "none";
				document.getElementById('historyauthordiv').style.display = "none";
			}
		}
		var oAmendButton = new YAHOO.widget.Button("amendbutton");
		oAmendButton.on('click', function(){
			document.getElementById('hiddenapprovaltype').value = 'Comment Added';
			mustConfirmLeave = false;
			document.approvalform.submit();
		});
		
		var oPrintButton = new YAHOO.widget.Button("printproposalbutton");
		oPrintButton.on('click', function(){
			createProposalReport();
		});
		
	}

    YAHOO.util.Event.onContentReady("approvalbuttons", onButtonsReady);
}

function initHistoryButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oAddCommentButton = new YAHOO.widget.Button("commentbutton", {type: "submit"});
	}

    YAHOO.util.Event.onContentReady("historybuttons", onButtonsReady);
}

function initReportButtons()
{
	function onButtonsReady() {

		var oPrintButton = new YAHOO.widget.Button("printreportbutton");
		oPrintButton.on('click', function(){
			createReportForm();
		});
	}

    YAHOO.util.Event.onContentReady("printreportbuttondiv", onButtonsReady);
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

function loadHistoryList()
{
	function onHistoryListReady() {
		var historyColumnDefs = [
              {key:"DateEntered",label:"Date", width: 50},
              {key:"ActivityType",label:"Activity", width: 100},
              {key:"Details",label:"Comments/Details", width: 250},
              {key:"Author",label:"Author"}
              ];
  		
		
          var historyDataSource = new YAHOO.util.DataSource(baseUrl + "proposalapproval/findhistory");
          historyDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
          historyDataSource.responseSchema = {
              resultsList: "Result",
              fields: ["DateEntered", "ActivityType", "Details", "Author"]
           };

          var historyDataTable = new YAHOO.widget.DataTable("historydiv", historyColumnDefs, historyDataSource, {scrollable:true, height:"25em"});
  		
	}
	
	YAHOO.util.Event.onContentReady("historydiv", onHistoryListReady);
}


function initEmailButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oEmailButton = new YAHOO.widget.Button("emailbutton", {type: "submit"});
		//Makes the buttons YUI widgets for a nicer look.
		var oCancelButton = new YAHOO.widget.Button("cancelbutton");
		oCancelButton.on('click', function(){
			history.back();
		});
	}

    YAHOO.util.Event.onContentReady("emailbuttons", onButtonsReady);
}

var authorselectname;
function setAuthorsInList(transport)
{
	var selectlist = document.getElementById(authorselectname);
    var selectedvalue = selectlist.value;
	for (var index=selectlist.options.length-1; index >= 0; index--) 
    {
    	selectlist.options[index] = null; // remove the option
	}
	if (transport.responseJSON !== null)
	{
		var authorselect = document.getElementById(authorselectname);
		var retval = JSON.parse(transport.responseText);
		var val = retval.Result;
		for (var i = 0; i < val.length; i++)
		{
			var newoption = new Option(val[i].DisplayName, val[i].PersonID);
			authorselect.options[i] = newoption;
		}
		document.body.style.cursor = 'default';
		authorselect.value = selectedvalue;
	}
}

function loadAuthorOptions(selectname)
{
	authorselectname = selectname;
	var authorselect = document.getElementById(selectname);
	if (authorselect.options.length < 2)
	{
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl + '/proposalapproval/populatepermittedauthors',
	    		{method: 'get', 
				onComplete: setAuthorsInList});
	}
}
