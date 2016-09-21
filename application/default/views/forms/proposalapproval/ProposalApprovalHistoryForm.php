<?php
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
 *
 * ProposalApprovalHistoryForm
 *
 * @author vcrema
 * Created September 30, 2009
 *
 */
 
 class ProposalApprovalHistoryForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$historycommentstextarea = new ACORNTextArea('historycommentstextarea');
    	$historycommentstextarea->setLabel('Comments*');
    	$historycommentstextarea->setName('historycommentstextarea');
    	$historycommentstextarea->setRequired(TRUE);
    	$this->addElement($historycommentstextarea);

    	$authorselect = new ACORNSelect('historyauthorselect');
    	$authorselect->setLabel('Author');
    	$authorselect->setName('historyauthorselect');
    	$authorselect->setAttrib('onclick', 'loadAuthorOptions(\'historyauthorselect\')');
		$this->addElement($authorselect);
    	
    	$toinput = new ACORNTextField('historytoinput');
    	$toinput->setLabel('Notification To*');
    	$toinput->setName('historytoinput');
    	$toinput->setRequired(TRUE);
    	$this->addElement($toinput);
    	
    	$ccinput = new ACORNTextField('historyccinput');
    	$ccinput->setLabel('Notification Cc');
    	$ccinput->setName('historyccinput');
    	$this->addElement($ccinput);
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'proposalapproval/proposalhistory.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
}
?>