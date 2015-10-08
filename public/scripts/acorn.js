//PROD
//var baseUrl = "http://acorn.lib.harvard.edu:8881/";
//DEV
var baseUrl = "http://sal.hul.harvard.edu:8883/";
//QA
//var baseUrl = "http://sal.hul.harvard.edu:8884/";
var accesslevel = 'None';
var curators;

function setAccessLevel(newaccesslevel)
{
	accesslevel = newaccesslevel;
	//var myLogReader = new YAHOO.widget.LogReader("logcontainer", {verboseOutput:false, newestOnTop:false});
	
}

var storageAutocomplete;
function loadStorageValues()
{
	function onStorageTextBoxReady() {
		var arrayStorage = [
		"Book vault",
		"Fume hood",
		"Paper vault",
		"Large flat files",
		"Salvage room",
		"Room 018"
		];
	    // Use a LocalDataSource
	    var oDS = new YAHOO.util.LocalDataSource(arrayStorage);
	    // Optional to define fields for single-dimensional array
	    oDS.responseSchema = {fields : ["storage"]};
	
	    // Instantiate the AutoComplete
	    storageAutocomplete = new YAHOO.widget.AutoComplete("storageinput", "storagecontainer", oDS);
	    storageAutocomplete.prehighlightClassName = "yui-ac-prehighlight";
	    storageAutocomplete.minQueryLength = 0;  
	    storageAutocomplete.useShadow = true;
	    storageAutocomplete.useIFrame = true;
	    storageAutocomplete.textboxFocusEvent.subscribe(function(){
	    	// Is open
	        if(storageAutocomplete.isContainerOpen()) {
	        	storageAutocomplete.collapseContainer();
	        }
	        // Is closed
	        else {
	        	storageAutocomplete.getInputEl().focus(); // Needed to keep widget active
	            setTimeout(function() { // For IE
	            	storageAutocomplete.sendQuery("");
	            },0);
	        }
	    });
	}
	YAHOO.util.Event.onContentReady("storageinput", onStorageTextBoxReady);
}

function loadAuthorArtist()
{
	function onArtistTextBoxReady() {
		var myDataSource = new YAHOO.util.DataSource(baseUrl + "recordincludes/findauthorartists");
        myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myDataSource.responseSchema = {
            resultsList: "Result",
            fields: ["AuthorArtist"]
         };
	    // Instantiate the AutoComplete
	    var oAC = new YAHOO.widget.AutoComplete("authorinput", "authorcontainer", myDataSource);
	    oAC.prehighlightClassName = "yui-ac-prehighlight";
	    oAC.useShadow = true;
		oAC.useIFrame = true;
	}
	
	YAHOO.util.Event.onContentReady("authorinput", onArtistTextBoxReady);
}


acornDropdownCellEditor = function(sType, oConfigs) {
	   this._sId = "acorn-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   acornDropdownCellEditor.superclass.constructor.call(this, sType, oConfigs); 
};


	// acornDropdownCellEditor extends BaseCellEditor
	YAHOO.lang.extend(acornDropdownCellEditor, YAHOO.widget.BaseCellEditor, {

		acornUrl : null,
		acornDropdownOptions : null,
		acornDropdown : null,
		
		/* Saves value of CellEditor and hides UI.
		 *
		 * @method save
		 */
		save : function() {
		    // Get new value
		    var inputValue = this.getInputValue();
		    var validValue = inputValue;
		    var textValue = this.acornDropdown.options[this.acornDropdown.options.selectedIndex].text;
		    // Validate new value
		    if(this.validator) {
		        validValue = this.validator.call(this.getDataTable(), inputValue, this.value, this);
		        if(validValue === undefined ) {
		            if(this.resetInvalidData) {
		                this.resetForm();
		            }
		            this.fireEvent("invalidDataEvent",
		                    {editor:this, oldData:this.value, newData:inputValue});
		            YAHOO.log("Could not save Cell Editor input due to invalid data " +
		                    YAHOO.lang.dump(inputValue), "warn", this.toString());
		            return;
		        }
		    }
		        
		    var oSelf = this;
		    var finishSave = function(bSuccess, oNewValue, textValue) {
		    	var oOrigValue = oSelf.value;
		    	
		        if(bSuccess) {
		            // Update new value
		            oSelf.value = oNewValue;
		            oSelf.getDataTable().updateCell(oSelf.getRecord(), oSelf.getColumn(), textValue);
		            
		            // Hide CellEditor
		            oSelf.getContainerEl().style.display = 'none';
		            oSelf.isActive = false;
		            oSelf.getDataTable()._oCellEditor =  null;
		            
		            oSelf.fireEvent("saveEvent",
		                    {editor:oSelf, oldData:oOrigValue, newData:oSelf.value});
		            
		            //Set the PersonID to the new value
		            oSelf.getDataTable().updateCell(oSelf.getRecord(), "ImportanceID", oNewValue);
		            
		            YAHOO.log("Cell Editor input saved", "info", this.toString());
		        }
		        else {
		            oSelf.resetForm();
		            oSelf.fireEvent("revertEvent",
		                    {editor:oSelf, oldData:oOrigValue, newData:oNewValue});
		            YAHOO.log("Could not save Cell Editor input " +
		                    YAHOO.lang.dump(oNewValue), "warn", oSelf.toString());
		        }
		        oSelf.unblock();
		    };
		    
		    this.block();
		    if(YAHOO.lang.isFunction(this.asyncSubmitter)) {
		        this.asyncSubmitter.call(this, finishSave, validValue, textValue);
		    } 
		    else {   
		        finishSave(true, validValue, textValue);
		    }
		},
		
		cancel : function() {
		    if(this.isActive) {
		        this.getContainerEl().style.display = 'none';
		        this.isActive = false;
		        this.getDataTable()._oCellEditor =  null;
		        this.fireEvent("cancelEvent", {editor:this});
		        this.resetForm();
		        YAHOO.log("CellEditor canceled", "info", this.toString());
		    }
		    else {
		        YAHOO.log("Unable to cancel CellEditor", "warn", this.toString());
		    }
		},

	/**
	 * Render a form with select element.
	 *
	 * @method renderForm
	 */
	renderForm : function() {
		//To be implemented by the subclass.
	},

	    /**
	     * After rendering form, if disabledBtns is set to true, then sets up a mechanism
	     * to save input without them. 
	     *
	     * @method handleDisabledBtns
	     */
	    handleDisabledBtns : function() {
			YAHOO.util.Event.addListener(this.acornDropdown, "change", function(v){
	            // Save on change
	            this.save();
	        }, this, true);        
	    },

	    /**
	     * Resets acornDropdownCellEditor UI to initial state.
	     *
	     * @method resetForm
	     */
	    resetForm : function() {
	        for(var i=0, j=this.acornDropdown.options.length; i<j; i++) {
	            if(this.value === this.acornDropdown.options[i].text) {
	                this.acornDropdown.options[i].selected = true;
	            }
	        }    
	    },

	    /**
	     * Sets focus in acornDropdownCellEditor.
	     *
	     * @method focus
	     */
	    focus : function() {
	    	this.getDataTable()._focusEl(this.acornDropdown);
	    },

	    /**
	     * Retrieves input value from acornDropdownCellEditor.
	     *
	     * @method getInputValue
	     */
	    getInputValue : function() {
	        return this.acornDropdown.options[this.acornDropdown.options.selectedIndex].value;
	    }
	});

//Copy static members to acornDropdownCellEditor class
YAHOO.lang.augmentObject(acornDropdownCellEditor, YAHOO.widget.BaseCellEditor);


personDropdownCellEditor = function(oConfigs) {
	this._sId = "person-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	personDropdownCellEditor.superclass.constructor.call(this, "persondropdown", oConfigs); 
};

// personDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(personDropdownCellEditor, acornDropdownCellEditor, {
	
	/**
	 * Render a form with select element.
	 *
	 * @method renderForm
	 */
	renderForm : function() {
		var elDropdown = this.getContainerEl().appendChild(document.createElement("select"));
	    elDropdown.style.zoom = 1;
	    this.acornDropdown = elDropdown;
	    if (this.acornDropdownOptions === null)
	    {
	    	var myAjax = new Ajax.Request(baseUrl + this.acornUrl,
	    		{method: 'get', 
				parameters: {includeinactive: 0, includeblank: 1},
				onSuccess: function(transport) {
					if (transport.responseJSON !== null)
					{
						var value = YAHOO.lang.JSON.parse(transport.responseText);
			    		this.acornDropdownOptions = value.Result;
			    		for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
							dropdownOption = this.acornDropdownOptions[i];
							elOption = document.createElement("option");
				            elOption.value = dropdownOption.PersonID;
				            elOption.innerHTML = dropdownOption.DisplayName;
				            elOption = elDropdown.appendChild(elOption);
				            
				            
				        }
					}
					else
					{
						this.acornDropdownOptions = null;
					}
				  }
					
	    		});
	    }
	    else
	    {
	    	for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
				dropdownOption = this.acornDropdownOptions[i];
	            elOption = document.createElement("option");
	            elOption.value = dropdownOption.PersonID;
	            elOption.innerHTML = dropdownOption.DisplayName;
	            elOption = elDropdown.appendChild(elOption);   
	            
	            
	        }
	    }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
});

//Copy static members to personDropdownCellEditor class
YAHOO.lang.augmentObject(personDropdownCellEditor, acornDropdownCellEditor);

workTypeDropdownCellEditor = function(oConfigs) {
	   this._sId = "worktype-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   workTypeDropdownCellEditor.superclass.constructor.call(this, "worktypedropdown", oConfigs); 
};

// workTypeDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(workTypeDropdownCellEditor, acornDropdownCellEditor, {

	/**
	 * Render a form with select element.
	 *
	 * @method renderForm
	 */
	renderForm : function() {
		var elDropdown = this.getContainerEl().appendChild(document.createElement("select"));
	    elDropdown.style.zoom = 1;
	    this.acornDropdown = elDropdown;
	    if (this.acornDropdownOptions === null)
	    {
	    	var myAjax = new Ajax.Request(baseUrl + this.acornUrl,
	    		{method: 'get', 
				parameters: {includeinactive: 0, includeblank: 1},
				onSuccess: function(transport) {
					if (transport.responseJSON !== null)
					{
						var value = YAHOO.lang.JSON.parse(transport.responseText);
			    		this.acornDropdownOptions = value.Result;
			    		for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
			    			dropdownOption = this.acornDropdownOptions[i];
				            elOption = document.createElement("option");
				            elOption.value = dropdownOption.WorkTypeID;
				            elOption.innerHTML = dropdownOption.WorkType;
				            elOption = elDropdown.appendChild(elOption);
				        }
					}
					else
					{
						this.acornDropdownOptions = null;
					}
				  }
	    		});
	    }
	    else
	    {
	    	for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
				dropdownOption = this.acornDropdownOptions[i];
	            elOption = document.createElement("option");
	            elOption.value = dropdownOption.WorkTypeID;
	            elOption.innerHTML = dropdownOption.WorkType;
	            elOption = elDropdown.appendChild(elOption);       
	        }
	    }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
	});

//Copy static members to workTypeDropdownCellEditor class
YAHOO.lang.augmentObject(workTypeDropdownCellEditor, acornDropdownCellEditor);

importanceDropdownCellEditor = function(oConfigs) {
	   this._sId = "importance-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   importanceDropdownCellEditor.superclass.constructor.call(this, "importancedropdown", oConfigs); 
};

// importanceDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(importanceDropdownCellEditor, acornDropdownCellEditor, {
	
	/**
	 * Render a form with select element.
	 *
	 * @method renderForm
	 */
	renderForm : function() {
		var elDropdown = this.getContainerEl().appendChild(document.createElement("select"));
	    elDropdown.style.zoom = 1;
	    this.acornDropdown = elDropdown;
	    if (this.acornDropdownOptions === null)
	    {
	    	var myAjax = new Ajax.Request(baseUrl + this.acornUrl,
	    		{method: 'get', 
				parameters: {includeinactive: 0, includeblank: 1},
				onSuccess: function(transport) {
					if (transport.responseJSON !== null)
					{
						var value = YAHOO.lang.JSON.parse(transport.responseText);
			    		this.acornDropdownOptions = value.Result;
			    		for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
			    			dropdownOption = this.acornDropdownOptions[i];
				            elOption = document.createElement("option");
				            elOption.value = dropdownOption.ImportanceID;
				            elOption.innerHTML = dropdownOption.Importance;
				            elOption = elDropdown.appendChild(elOption);
				        }
					}
					else
					{
						this.acornDropdownOptions = null;
					}
				  }
	    		});
	    }
	    else
	    {
	    	for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
				dropdownOption = this.acornDropdownOptions[i];
	            elOption = document.createElement("option");
	            elOption.value = dropdownOption.ImportanceID;
	            elOption.innerHTML = dropdownOption.Importance;
	            elOption = elDropdown.appendChild(elOption);       
	        }
	    }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
	});

//Copy static members to importanceDropdownCellEditor class
YAHOO.lang.augmentObject(importanceDropdownCellEditor, acornDropdownCellEditor);

locationDropdownCellEditor = function(oConfigs) {
	   this._sId = "location-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   locationDropdownCellEditor.superclass.constructor.call(this, "locationdropdown", oConfigs); 
};

// locationDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(locationDropdownCellEditor, acornDropdownCellEditor, {
	/**
	 * Render a form with select element.
	 *
	 * @method renderForm
	 */
	renderForm : function() {
		var elDropdown = this.getContainerEl().appendChild(document.createElement("select"));
	    elDropdown.style.zoom = 1;
	    this.acornDropdown = elDropdown;
	    if (this.acornDropdownOptions === null)
	    {
	    	var myAjax = new Ajax.Request(baseUrl + this.acornUrl,
	    		{method: 'get', 
				parameters: {includeinactive: 0, includeblank: 0},
				onSuccess: function(transport) {
					if (transport.responseJSON !== null)
					{
						var value = YAHOO.lang.JSON.parse(transport.responseText);
			    		this.acornDropdownOptions = value.Result;
			    		for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
			    			dropdownOption = this.acornDropdownOptions[i];
				            elOption = document.createElement("option");
				            elOption.value = dropdownOption.LocationID;
				            elOption.innerHTML = dropdownOption.Location;
				            elOption = elDropdown.appendChild(elOption);
				        }
					}
					else
					{
						this.acornDropdownOptions = null;
					}
				  }
	    		});
	    }
	    else
	    {
	    	for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
				dropdownOption = this.acornDropdownOptions[i];
	            elOption = document.createElement("option");
	            elOption.value = dropdownOption.LocationID;
	            elOption.innerHTML = dropdownOption.Location;
	            elOption = elDropdown.appendChild(elOption);       
	        }
	    }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
	});

//Copy static members to locationDropdownCellEditor class
YAHOO.lang.augmentObject(locationDropdownCellEditor, acornDropdownCellEditor);

formatDropdownCellEditor = function(oConfigs) {
	   this._sId = "format-dropdownceditor" + YAHOO.widget.BaseCellEditor._nCount++;
	   formatDropdownCellEditor.superclass.constructor.call(this, "formatdropdown", oConfigs); 
};

// formatDropdownCellEditor extends acornDropdownCellEditor
YAHOO.lang.extend(formatDropdownCellEditor, acornDropdownCellEditor, {

	/**
	 * Render a form with select element.
	 *
	 * @method renderForm
	 */
	renderForm : function() {
		var elDropdown = this.getContainerEl().appendChild(document.createElement("select"));
	    elDropdown.style.zoom = 1;
	    this.acornDropdown = elDropdown;
	    if (this.acornDropdownOptions === null)
	    {
	    	var myAjax = new Ajax.Request(baseUrl + this.acornUrl,
	    		{method: 'get', 
				parameters: {includeinactive: 0, includeblank: 0},
				onSuccess: function(transport) {
					if (transport.responseJSON !== null)
					{
						var value = YAHOO.lang.JSON.parse(transport.responseText);
			    		this.acornDropdownOptions = value.Result;
			    		for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
			    			dropdownOption = this.acornDropdownOptions[i];
				            elOption = document.createElement("option");
				            elOption.value = dropdownOption.FormatID;
				            elOption.innerHTML = dropdownOption.Format;
				            elOption = elDropdown.appendChild(elOption);
				        }
					}
					else
					{
						this.acornDropdownOptions = null;
					}
				  }
	    		});
	    }
	    else
	    {
	    	for(var i=0, j=this.acornDropdownOptions.length; i<j; i++) {
				dropdownOption = this.acornDropdownOptions[i];
	            elOption = document.createElement("option");
	            elOption.value = dropdownOption.FormatID;
	            elOption.innerHTML = dropdownOption.Format;
	            elOption = elDropdown.appendChild(elOption);       
	        }
	    }
	    
	    if(this.disableBtns) {
	        this.handleDisabledBtns();
	    }
	}
	});

//Copy static members to formatDropdownCellEditor class
YAHOO.lang.augmentObject(formatDropdownCellEditor, acornDropdownCellEditor);

function displaySaveStatus(savestatus)
{
	var divelement = document.getElementById("statusdiv");
	if (savestatus == "success")
	{
		var statusAnim = new YAHOO.util.Anim('statusdiv', {
	        style: {display: 'block'}}, 5);
		var onComplete = function() {
			statusAnim = null;
		    YAHOO.util.Dom.setStyle('statusdiv', "display", "none");
		};
		statusAnim.onComplete.subscribe(onComplete);
		statusAnim.animate();
	}
	else
	{
		divelement.style.display = 'none';
	}
		
}

String.prototype.trim = function() {
	a = this.replace(/^\s+/, '');
	return a.replace(/\s+$/, '');
};


/* This is the general acorn.js file that is included in all screens */

function loadAutoCompleteTextBoxes()
{
	loadStorageValues();
	loadAuthorArtist();
}

