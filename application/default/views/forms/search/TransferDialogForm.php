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
 * TransferDialogForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
class TransferDialogForm extends ACORNForm 
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$courierinput = new ACORNTextField('courierinput');
		$courierinput->setLabel('Courier*');
		$courierinput->setName('courierinput');
		$courierinput->setRequired(TRUE);
    	$this->addElement($courierinput);
		
    	$courier2input = new ACORNTextField('courier2input');
		$courier2input->setLabel('Second Courier');
		$courier2input->setName('courier2input');
		$this->addElement($courier2input);
		
    	/*$courierselectform = new PersonSelectForm(array(PersonSelectForm::PERSON_SELECT_NAME => 'courierselect', ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => FALSE));
    	$courierselectform->setRoleTypes(array('Staff'));
    	$courierselectform->setLabel('Courier*');
    	$courierselectform->setName('courierselectform');
    	$courierselectform->setRequired(TRUE);
    	$this->addSubForm($courierselectform, 'courierselectform');
    	
    	$courier2selectform = new PersonSelectForm(array(PersonSelectForm::PERSON_SELECT_NAME => 'courier2select', ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$courier2selectform->setRoleTypes(array('Staff'));
    	$courier2selectform->setLabel('Second Courier');
    	$courier2selectform->setName('courier2selectform');
    	$this->addSubForm($courier2selectform, 'courier2selectform');*/
    	
    	$transportcontainertextarea = new ACORNTextArea('transportcontainertextarea');
    	$transportcontainertextarea->setLabel('Transport Container*');
    	$transportcontainertextarea->setName('transportcontainertextarea');
    	$transportcontainertextarea->setAttrib('rows', '5');
    	$transportcontainertextarea->setRequired(TRUE);
    	$this->addElement($transportcontainertextarea);
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'record/transferdialog.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
}
?>