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
var mustConfirmLeave = false;
var continueLeavingPage = true;
function initCheckingForModifications() {
    //var elems = $(formId).elements;
    var inputs = document.getElementsByTagName("input");
    for(var i = 0; i < inputs.length; i++) {
        var type = inputs[i].getAttribute("type");
        if(type == "checkbox" || type == "radio") {
            Event.observe(inputs[i], "change", somethingHasChanged);
        } else {
            Event.observe(inputs[i], "keypress", somethingHasChanged);
        }
    }
    var textareas = document.getElementsByTagName("textarea");
    for(var i = 0; i < textareas.length; i++) {
        Event.observe(textareas[i], "keypress", somethingHasChanged);
    }
    var selects = document.getElementsByTagName("select");
    for(var i = 0; i < selects.length; i++) {
        Event.observe(selects[i], "change", somethingHasChanged);
    }
 
    // for all a-s - intercept onclick
    var as = document.getElementsByTagName("a");
    for(var i = 0; i < as.length; i++) {
        var href = as[i].getAttribute("href");
        as[i]._href = href;
        //as[i].removeAttribute("href");
        Event.observe(as[i], "click", navigateAway.bindAsEventListener(as[i]));
    }
}
 
function somethingHasChanged(e) {
    if (e.keyCode != Event.KEY_TAB) {
        mustConfirmLeave = true;
    }
}
 
function navigateAway(url) {
    if(checkForModifications()) {
        window.location.href = this._href;//url;
    }
}
 
function checkForModifications() {
	continueLeavingPage = true;
    if(mustConfirmLeave) {
    	return "You've made changes in the page. Are you sure you want to leave this page without saving the changes?";
    }
    //Disable the onbeforeunload event so we get no dialog.
    else {
    	window.onbeforeunload=null;
    }

}

function tabChangeCheckForModifications() {
	if(mustConfirmLeave) {
    	if(confirm("You've made changes in the page. Are you sure you want to leave this page without saving the changes?")) {
    		mustConfirmLeave = false;
    		return true;
        } else {
        	return false;
        }                          
    }
    return true;
}