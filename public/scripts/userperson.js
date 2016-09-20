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
function initContent()
{
	loadRoleTypes(); 
	var access = document.getElementById('usertypeselect').value;
	userTypeChanged(access);
}

function loadRoleTypes()
{
	function onRoleTypeListReady() {

		var roles = ["", "Contractor", "Curator", "Donor", "Staff"];
		var myColumnDefs = [
            {key:"RoleType",label:"Roles",editor: new YAHOO.widget.DropdownCellEditor({dropdownOptions:roles})}
        ];
		
    	var myDataSource = new YAHOO.util.DataSource(baseUrl + 'people/populateuserroles');
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myDataSource.responseSchema = {
	    	resultsList: "Result",
	    	fields: ["RoleType"]
		};

        var myDataTable = new YAHOO.widget.DataTable("roletypearea", myColumnDefs, myDataSource, {scrollable:true, height:"4em"});
        myDataTable.subscribe("rowClickEvent",myDataTable.onEventSelectRow);
        myDataTable.subscribe("cellDblclickEvent",myDataTable.onEventShowCellEditor);
        myDataTable.subscribe("editorBlurEvent", myDataTable.onEventSaveCellEditor);

        // When cell is edited, pulse the color of the row yellow
        var onCellEdit = function(oArgs) {
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
    		
            if (oOldData != oNewData || oNewData == '')
    		{
    			//If the blank column was filled, add a new row
				if (oOldData == "(Double click to add)")
				{
					myDataTable.addRow({RoleType: "(Double click to add)"}); 
				}
				//If it was changed remove the old one from the array
				else
				{
					var myAjax = new Ajax.Request(baseUrl + "people/removerole",
				    		{method: 'get', 
							parameters: {roletype: oOldData}});
				}
				//If the new column is blank, delete it
				if (oNewData == '')
				{
					myDataTable.deleteRow(elRow);
				}
				//Otherwise add it to the array
				else
				{
					var recordset = myDataTable.getRecordSet();
		    		//var records = myDataTable.getRecordSet().getRecords();
		    		var rolecount = 0;
		    		//Determine if there is more than one of the same role type
		    		for (var count = 0; count < recordset.getLength(); count++)
		    		{
		    			if (recordset.getRecord(count).getData("RoleType") == oNewData)
		    			{
		    				rolecount++;
		    			}
		    		}
		    		//If there more than one role of this type, remove the row
					if (rolecount > 1)
					{
						myDataTable.deleteRow(elRow);
					}
					//Otherwise, add it.
					else
					{
						var myAjax = new Ajax.Request(baseUrl + "people/addrole",
				    		{method: 'get', 
							parameters: {roletype: oNewData}});
					}
				}
    		}
        };
        myDataTable.subscribe("editorSaveEvent", onCellEdit);

	}

    YAHOO.util.Event.onContentReady("roletypearea", onRoleTypeListReady);
    
}

function userTypeChanged(usertype)
{
	if (usertype == 'Repository' || usertype == 'Repository Admin' || usertype == 'Curator')
	{
		document.getElementById('repositorydiv').style.display = "block";
	}
	else
	{
		document.getElementById('repositorydiv').style.display = "none";
	}
}

function initButtons()
{
	function onCreateNewButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oCreateNewButton = new YAHOO.widget.Button("createnewbutton");
		//create new just redirects to the new user
		oCreateNewButton.on('click', function(){
			window.location = baseUrl + 'people/newuser';
		});
	}

    YAHOO.util.Event.onContentReady("createnewbutton", onCreateNewButtonReady);
    
    function onSaveProfileButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSaveProfileButton = new YAHOO.widget.Button("saveprofilebutton", {type: "submit"});
		var oClearChangesButton = new YAHOO.widget.Button("clearchangesbutton");
		oClearChangesButton.on('click', resetPerson);
	}

    YAHOO.util.Event.onContentReady("saveprofilebutton", onSaveProfileButtonReady);
    
    function onSavePasswordButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSavePasswordButton = new YAHOO.widget.Button("savepasswordbutton", {type: "submit"});
	}

    YAHOO.util.Event.onContentReady("savepasswordbutton", onSavePasswordButtonReady);
}

function resetPerson()
{
	document.personprofileform.reset();
	var myAjax = new Ajax.Request(baseUrl + "people/resetperson",
    		{method: 'get',
			onComplete: loadRoleTypes}
			);
}

function preparePersonInput()
{
	function onPersonInputReady()
	{
		var myDataSource = new YAHOO.util.DataSource(baseUrl + "people/findpeople");
        myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myDataSource.responseSchema = {
            resultsList: "Result",
            fields: ["DisplayName", "PersonID"]
         };
        
	    // Instantiate the AutoComplete
	    var oAC = new YAHOO.widget.AutoComplete("personinput", "personcontainer", myDataSource);
	    oAC.prehighlightClassName = "yui-ac-prehighlight";
	    oAC.useShadow = true;
		oAC.useIFrame = true; 
		
		var itemSelected = function(sType, aArgs)
		{
			var personid = aArgs[2][1]; // object literal of selected item's result data
	        // update hidden form field with the selected item's ID
	        document.getElementById("personidinput").value = personid;
	        
	        //Forward this to the person screen to populate the data.
	        window.location = baseUrl + 'people/index/personid/' + personid;
		};
		oAC.itemSelectEvent.subscribe(itemSelected);
	}
	YAHOO.util.Event.onContentReady("personinput", onPersonInputReady);
}