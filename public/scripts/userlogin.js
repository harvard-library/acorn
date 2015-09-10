function initContent()
{
	checkDisplayLoginAs();
	document.getElementById('loginoption').style.display = "none";
}

function initButtons()
{
	function onButtonsReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oUserLoginButton = new YAHOO.widget.Button("userloginbutton", {type: "submit"});
	}

    YAHOO.util.Event.onContentReady("userloginbutton", onButtonsReady);
}

function userTypeChanged(usertype)
{
	if (usertype == 'Repository' || usertype == 'Repository Admin')
	{
		document.getElementById('repositorydiv').style.display = "block";
	}
	else
	{
		document.getElementById('repositorydiv').style.display = "none";
	}
}

function loadRepositoryValues()
{
   
}

function checkDisplayLoginAs()
{
	var username = document.getElementById("usernameinput").value;
	if (username.length > 0)
	{
		var myAjax = new Ajax.Request(baseUrl + '/index/verifyadmin',
    		{method: 'get', 
    		parameters: {username: username},
    		onComplete: showHideLoginAs});
	}
	else
	{
		document.getElementById('usertypediv').style.display = "none";
		document.getElementById('repositorydiv').style.display = "none";
	}
}

function showHideLoginAs(transport)
{
	if (transport.responseJSON != null)
	{
		var val = JSON.parse(transport.responseText);
		var isadmin = val.isadmin;
		if (isadmin)
		{
			document.getElementById('usertypediv').style.display = "block";		
			document.getElementById("usertypeselect").value = "Admin";
		}
		else
		{
			document.getElementById('usertypediv').style.display = "none";
		}
	}
	else
	{
		document.getElementById('usertypediv').style.display = "none";
	}
	document.getElementById('repositorydiv').style.display = "none";
}

function initChangePasswordButton()
{
	function onButtonReady() {

		//Makes the buttons YUI widgets for a nicer look.
		var oSaveButton = new YAHOO.widget.Button("savepasswordbutton", {type: "submit"});
	}

    YAHOO.util.Event.onContentReady("savepasswordbutton", onButtonReady);
}