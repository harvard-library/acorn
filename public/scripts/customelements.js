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

function clearList(listname)
{
	var selectlist = document.getElementById(listname);
    for (var index=selectlist.options.length-1; index >= 0; index--) 
    {
    	selectlist.options[index] = null; // remove the option
	}
}

function setProjectsInList(transport)
{
	var selectedvalue = document.getElementById('projectselect').value;
	clearList('projectselect');
	if (transport.responseJSON !== null)
	{
		var projectselect = document.getElementById('projectselect');
		var retval = JSON.parse(transport.responseText);
		var val = retval.Result;
		for (var i = 0; i < val.length; i++)
		{
			var newoption = new Option(val[i].ProjectName, val[i].ProjectID);
			projectselect.options[i] = newoption;
		}
		document.body.style.cursor = 'default';
		projectselect.value = selectedvalue;
	}
}

function loadProjects(includeinactive, includeblank)
{
	var projectselect = document.getElementById('projectselect');
	if (projectselect.options.length < 2)
	{
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl + '/list/populateprojects',
	    		{method: 'get', 
				parameters: {includeinactive: includeinactive, includeblank: includeblank},
	    		onComplete: setProjectsInList});
	}
}

function loadChargeTo(value)
{
	var chargetoselect = document.getElementById('chargetoselect');
	if (chargetoselect.options.length < 2)
	{
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl + '/list/populatechargeto',
	    		{method: 'get', 
				onComplete: function(transport){
					setChargeToInList(transport, value);
				}});
	}
}

function setChargeToInList(transport, value)
{
	var selectedvalue = document.getElementById('chargetoselect').value;
	clearList('chargetoselect');
	if (transport.responseJSON !== null)
	{
		var selectlist = document.getElementById('chargetoselect');
		var retval = JSON.parse(transport.responseText);
		var val = retval.Result;
		for (var i = 0; i < val.length; i++)
		{
			var newoption = new Option(val[i].Location, val[i].LocationID);
			selectlist.options[i] = newoption;
		}
		document.body.style.cursor = 'default';
		if (value != null && value != '')
		{
			selectedvalue = value;
		}
		selectlist.value = selectedvalue;
	}
}


function setGroupsInList(transport)
{
	var selectedvalue = document.getElementById('groupselect').value;
	clearList('groupselect');
	if (transport.responseJSON !== null)
	{
		var selectlist = document.getElementById('groupselect');
		var retval = JSON.parse(transport.responseText);
		var val = retval.Result;
		for (var i = 0; i < val.length; i++)
		{
			var newoption = new Option(val[i].GroupName, val[i].GroupID);
			selectlist.options[i] = newoption;
		}
		document.body.style.cursor = 'default';
		selectlist.value = selectedvalue;
	}
}


function loadGroups(includeinactive, includeblank)
{
	var selectlist = document.getElementById('groupselect');
	if (selectlist.options.length < 2)
	{
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl + '/list/populategroups',
	    		{method: 'get', 
				parameters: {includeinactive: includeinactive, includeblank: includeblank},
	    		onComplete: setGroupsInList});
	}
}


function setPurposesInList(transport)
{
	var selectedvalue = document.getElementById('purposeselect').value;
	clearList('purposeselect');
	if (transport.responseJSON !== null)
	{
		var selectlist = document.getElementById('purposeselect');
		var retval = JSON.parse(transport.responseText);
		var val = retval.Result;
		for (var i = 0; i < val.length; i++)
		{
			var newoption = new Option(val[i].Purpose, val[i].PurposeID);
			selectlist.options[i] = newoption;
		}
		document.body.style.cursor = 'default';
		selectlist.value = selectedvalue;
	}
}
function loadPurposes(includeinactive, includeblank)
{
	var selectlist = document.getElementById('purposeselect');
	if (selectlist.options.length < 2)
	{
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl + '/list/populatepurposes',
	    		{method: 'get', 
				parameters: {includeinactive: includeinactive, includeblank: includeblank},
	    		onComplete: setPurposesInList});
	}
}


function setDepartmentsInList(transport)
{
	var selectedvalue = document.getElementById('departmentselect').value;
	clearList('departmentselect');
	if (transport.responseJSON !== null)
	{
		var selectlist = document.getElementById('departmentselect');
		selectlist.disabled = false;
		var retval = JSON.parse(transport.responseText);
		var val = retval.Result;
		for (var i = 0; i < val.length; i++)
		{
			var newoption = new Option(val[i].DepartmentName, val[i].DepartmentID);
			selectlist.options[i] = newoption;
		}
		document.body.style.cursor = 'default';
		selectlist.value = selectedvalue;
	}
	else
	{
		document.getElementById('departmentselect').disabled = true;
	}
}
function loadDepartments(includeinactive, includeblank)
{
	var isdisabled = document.getElementById('repositoryselect').disabled;
	document.getElementById('repositoryselect').disabled = false;
	var repositoryvalue = document.getElementById('repositoryselect').value;
	var selectlist = document.getElementById('departmentselect');
	if (repositoryvalue != 0 && selectlist.options.length < 2)
	{
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl + '/list/populatedepartments',
	    		{method: 'get', 
				parameters: {includeinactive: includeinactive,
					repositoryid: repositoryvalue, includeblank: includeblank},
	    		onComplete: setDepartmentsInList});
	}
	else if (repositoryvalue == 0)
	{
		document.getElementById('departmentselect').disabled = true;
	}
	document.getElementById('repositoryselect').disabled = isdisabled;
}

var formatlistname;

function setFormatsInList(transport)
{
	var selectedvalue = document.getElementById(formatlistname).value;
	clearList(formatlistname);
	if (transport.responseJSON !== null)
	{
		var selectlist = document.getElementById(formatlistname);
		var retval = JSON.parse(transport.responseText);
		var val = retval.Result;
		for (var i = 0; i < val.length; i++)
		{
			var newoption = new Option(val[i].Format, val[i].FormatID);
			selectlist.options[i] = newoption;
		}
		document.body.style.cursor = 'default';
		selectlist.value = selectedvalue;
	}
}

function loadFormats(formatselectname, includeinactive, includeblank)
{
	formatlistname = formatselectname;
	var selectlist = document.getElementById(formatselectname);
	if (selectlist.options.length < 2)
	{
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl + '/list/populateformats',
	    		{method: 'get', 
				parameters: {includeinactive: includeinactive, includeblank: includeblank},
	    		onComplete: setFormatsInList});
	}
}

var locationlistname;
function setLocationsInList(transport)
{
	var selectedvalue = document.getElementById(locationlistname).value;
	clearList(locationlistname);
	if (transport.responseJSON !== null)
	{
		var selectlist = document.getElementById(locationlistname);
		var retval = JSON.parse(transport.responseText);
		var val = retval.Result;
		for (var i = 0; i < val.length; i++)
		{
			var newoption = new Option(val[i].Location, val[i].LocationID);
			selectlist.options[i] = newoption;
		}
		document.body.style.cursor = 'default';
		selectlist.value = selectedvalue;
	}
}
function loadLocations(listname, includeinactive, isrepositorysearch, includeblank)
{
	locationlistname = listname;
	var selectlist = document.getElementById(locationlistname);
	if (selectlist.options.length < 2)
	{
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl + 'list/populatelocations',
	    		{method: 'get', 
				parameters: {includeinactive: includeinactive, isrepositorysearch: isrepositorysearch, includeblank: includeblank},
	    		onComplete: setLocationsInList});
	}
}

var autotextlistname;

function setAutotextCaptionsInList(transport)
{
	var selectedvalue = document.getElementById(autotextlistname).value;
	clearList(autotextlistname);
	if (transport.responseJSON !== null)
	{
		var selectlist = document.getElementById(autotextlistname);
		var retval = JSON.parse(transport.responseText);
		var val = retval.Result;
		for (var i = 0; i < val.length; i++)
		{
			var newoption = new Option(val[i].Caption, val[i].AutotextID);
			selectlist.options[i] = newoption;
		}
		document.body.style.cursor = 'default';
		selectlist.value = selectedvalue;
	}
}
function loadAutotextCaptions(listname, autotexttype, includeblank, dependentautotexttypeid)
{
	autotextlistname = listname;
	var selectlist = document.getElementById(autotextlistname);
	if (selectlist.options.length < 2)
	{
		document.body.style.cursor = 'wait';
		if (dependentautotexttypeid != -1)
		{
			var myAjax = new Ajax.Request(baseUrl + '/list/populateautotext',
	    		{method: 'get', 
				parameters: {autotexttype: autotexttype, includeblank: includeblank, dependentautotexttypeid: dependentautotexttypeid},
				onComplete: setAutotextCaptionsInList});
		}
		else
		{
			var myAjax = new Ajax.Request(baseUrl + '/list/populateautotext',
		    		{method: 'get', 
					parameters: {autotexttype: autotexttype, includeblank: includeblank},
					onComplete: setAutotextCaptionsInList});
		}
	}
}

var currenttextareaname;

function setAutotext(transport)
{
	if (transport.responseJSON !== null)
	{
		var val = JSON.parse(transport.responseText);
		var oldtext = document.getElementById(currenttextareaname).value;
		var newtext = oldtext + ' ' + val.Autotext;
		document.getElementById(currenttextareaname).value = newtext;
	}
}
function loadAutotextFromSelection(selectlistname, textareaname)
{
	currenttextareaname = textareaname;
	var value = document.getElementById(selectlistname).value;
	if (value > 0)
	{
		var myAjax = new Ajax.Request(baseUrl + '/list/getautotext',
	    		{method: 'get', 
				parameters: {autotextid: value},
				onComplete: setAutotext});
	}
	else if (value == 0)
	{
		var myAjax = new Ajax.Request(baseUrl + '/list/getproposedtreatmenttext',
	    		{method: 'get', 
				onComplete: setAutotext});
	}
	else
	{
		document.getElementById(currenttextareaname).value = '';
	}
}

function loadPresRec2AutotextCaptions()
{
	var presrec1value = document.getElementById("presrec1select").value;
	if (presrec1value != 0)
	{
		autotextlistname = "presrec2select";
		var presrec2selectlist = document.getElementById("presrec2select");
		presrec2selectlist.disabled = false;
		if (selectlist.options.length < 2)
		{
			document.body.style.cursor = 'wait';
			var myAjax = new Ajax.Request(baseUrl + '/list/populateautotext',
		    		{method: 'get', 
					parameters: {autotexttype: "Dependency",
					dependentautotexttypeid: presrec1value, includeblank: 0},
					onComplete: setAutotextCaptionsInList});
		}
	}
	else
	{
		document.getElementById("presrec2select").disabled = true;
	}
}

var stafflistname;

function setStaffInList(transport)
{
	var selectedvalue = document.getElementById(stafflistname).value;
	clearList(stafflistname);
	if (transport.responseJSON !== null)
	{
		var personselect = document.getElementById(stafflistname);
		var retval = JSON.parse(transport.responseText);
		var val = retval.Result;
		for (var i = 0; i < val.length; i++)
		{
			var newoption = new Option(val[i].DisplayName, val[i].PersonID);
			personselect.options[i] = newoption;
		}
		document.body.style.cursor = 'default';
		personselect.value = selectedvalue;
	}
}
function loadStaff(listname, includeinactive, includeblank, includecontractors)
{
	stafflistname = listname;
	var personselect = document.getElementById(stafflistname);
	if (personselect.options.length < 2)
	{
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(baseUrl + '/list/populatestaff',
	    		{method: 'get', 
				parameters: {includeinactive: includeinactive, 
				includeblank: includeblank,
				includecontractors: includecontractors},
	    		onComplete: setStaffInList});
	}
}

var curatorlistname;
function setCuratorsInList(transport)
{
	var selectedvalue = document.getElementById(curatorlistname).value;
	clearList(curatorlistname);
	if (transport.responseJSON !== null)
	{
		var personselect = document.getElementById(curatorlistname);
		var retval = JSON.parse(transport.responseText);
		var val = retval.Result;
		for (var i = 0; i < val.length; i++)
		{
			var newoption = new Option(val[i].DisplayName, val[i].PersonID);
			personselect.options[i] = newoption;
		}
		document.body.style.cursor = 'default';
		personselect.value = selectedvalue;
	}
}

function loadCurators(listname, includeinactive, includeblank)
{
	curatorlistname = listname;
	var personselect = document.getElementById(curatorlistname);
	var repositoryid = null;
	if (document.getElementById('repositoryselect') != undefined 
			&& document.getElementById('repositoryselect').value > 0)
	{
		repositoryid = document.getElementById('repositoryselect').value;
	}
	if (personselect.options.length < 2)
	{
		var url = baseUrl + '/list/populatecurators';
		if (repositoryid !== null)
		{
			url = url + '/repositoryid/' + repositoryid;
		}
		document.body.style.cursor = 'wait';
		var myAjax = new Ajax.Request(url,
	    		{method: 'get', 
				parameters: {includeinactive: includeinactive, 
				includeblank: includeblank},
	    		onComplete: setCuratorsInList});
	}
}

