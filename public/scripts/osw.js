
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
    	document.getElementById('oswformmenuitem').style.display = "block";

    	if (tabIndex == 0)
        {
        	loadOSWIncludes();
        }
        if (tabIndex == 1)
		{
			loadTableFormat("osw");
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

var currentOSWID = null;
var currentUserID = null;
var isBeingEdited = false;
function initButtons(newaccesslevel, userid, recordnumber, isbeingedited)
{
	setRecordEdit();
	currentOSWID = recordnumber;
	currentUserID = userid;
	if (isbeingedited == 1)
	{
		isBeingEdited = true;
	}
	setAccessLevel(newaccesslevel);
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
}

function initWPC()
{
	initIdentificationButtons();
	initFileButtons();
}

function initRepository()
{
	if (accesslevel == 'Repository' || accesslevel == 'Repository Admin' || isBeingEdited)
	{
		document.getElementById("identificationbuttons").style.display = "none";
	}
	document.getElementById("editprojectbutton").style.display = "none";
	document.getElementById("filebuttons").style.display = "none";
}

var workstartChooser = null;
var workendChooser = null;
function initIdentificationButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSaveButton = new YAHOO.widget.Button("saveoswbutton", {type: "submit"});
		var oClearChangesButton = new YAHOO.widget.Button("clearoswbutton");
		oClearChangesButton.on('click', resetOsw);
		var oDeleteButton = new YAHOO.widget.Button("deleteoswbutton");
		oDeleteButton.on('click', deleteRecord);
		if (accesslevel != 'Admin')
	    {
			oDeleteButton.setStyle('display', 'none');
		}
		
		var oOverrideCallButton = new YAHOO.widget.Button("savecallnumberbutton");
		oOverrideCallButton.on('click', overrideDuplicateCallNumber);
	}

    YAHOO.util.Event.onContentReady("identificationbuttons", onButtonsReady);
    
    function onProjectButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oEditProjectButton3 = new YAHOO.widget.Button("editprojectbutton");
		oEditProjectButton3.on('click', editProject);
    }

    YAHOO.util.Event.onContentReady("editprojectbutton", onProjectButtonReady);
    
    workstartChooser = new YAHOO.widget.Calendar("workstartChooser","workstartCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
    workstartChooser.render();
    workstartChooser.selectEvent.subscribe(handleWorkStartSelect, workstartChooser, true);
  	
  	workendChooser = new YAHOO.widget.Calendar("workendChooser","workendCalendarChooserContainer", { title:"Choose a date:", close:true, navigator:true } );
  	workendChooser.render();
  	workendChooser.selectEvent.subscribe(handleWorkEndSelect, workendChooser, true);
}

function editProject()
{
	window.open(baseUrl + 'admin/editlists');
}


function deleteRecord()
{
	window.location = baseUrl + "/osw/deleteoswrecord";
}

function resetOsw()
{
	document.oswidentificationform.reset();
	var myAjax = new Ajax.Request(baseUrl + "osw/resetosw",
    		{method: 'get',
			onComplete: loadOSWIncludes}
			);
}

function setRecordEdit()
{
	var myAjax = new Ajax.Request(baseUrl + "user/setrecordedit",
    		{method: 'get',
    		parameters: {recordtype: "osw"}});
}

function clearEdits(oswID)
{
	var myAjax = new Ajax.Request(baseUrl + "record/clearoswedits",
    		{method: 'get', 
			parameters: {oswID: oswID}});
}

function catchSubmit(formname)
{
	mustConfirmLeave = false;
	formname.submit();
}

function createOSWForm()
{
	var myAjax = new Ajax.Request(baseUrl + "reports/oswform",
    		{method: 'get', 
			onSuccess: function(transport)
			{
				//Open the report.
				var filename = transport.responseText.trim();
				window.open(baseUrl + 'userreports/pdfreports/' + filename);
			}
    		});
	
}

function handleWorkStartSelect(type, args, obj) {
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
	
	document.getElementById('workstartdateinput').value = month + "-" + day + "-" + year;
	workstartChooser.hide();
}

function showWorkStartChooser()
{
	if (workstartChooser != null)
	{
		workstartChooser.show();
	}
}

function handleWorkEndSelect(type, args, obj) {
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
	
	document.getElementById('workenddateinput').value = month + "-" + day + "-" + year;
	workendChooser.hide();
}

function showWorkEndChooser()
{
	if (workendChooser != null)
	{
		workendChooser.show();
	}
}

function checkWorkEndDate(element) {
    
    // Empty field is fine
    if (element.value == '') {
        return true;
    }
    // Otherwise, insure that it's uses the proper format
    else {

        var format  = /\d\d-\d\d-\d\d\d\d/;
        var result = element.value.match(format);
        
        if (result == null) {
            alert( "Work End Date must be in the form of MM-DD-YYYY" );
            element.focus();
            return false;
        }
        else {
            return true;
        }
    }
}

function overrideDuplicateCallNumber()
{
	var callnumber = document.getElementById("callnumberhidden").value;
	var myAjax = new Ajax.Request(baseUrl + "osw/overridecallnumber",
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

function initDuplicateOverrideButtons()
{
	if (document.getElementById('callnumbererrorhidden').value)
	{
		document.getElementById('idsavecallbuttondiv').style.display = 'block';
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
