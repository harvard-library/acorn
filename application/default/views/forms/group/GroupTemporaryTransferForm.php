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
 * GroupTemporaryTransferForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class GroupTemporaryTransferForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$transferlistname = new Zend_Form_Element_Hidden('transferlistname');
    	$transferlistname->setName('transferlistname');
		$transferlistname->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$transferlistname->setValue(TransferNamespace::TEMP_TRANSFER);
    	$this->addElement($transferlistname);
    	
    	$transferids = new Zend_Form_Element_Hidden('transferids6');
    	$transferids->setName('transferids6');
		$transferids->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->addElement($transferids);
		
		
    	$transferdateinput = new DateTextField('transferdateinput');
    	$transferdateinput->setLabel('Transfer Date*');
		$transferdateinput->setName('transferdateinput');
		$transferdateinput->setRequired(TRUE);;
    	$transferdateinput->setAttrib('onfocus', 'showCalendarChooser(\'transferdateinput\')');
		$transferdateinput->setAttrib('readonly', 'readonly');
		$this->addElement($transferdateinput);
		
		$transferfromselectform = new LocationSelectForm('transferfromselect');
    	$transferfromselectform->setName('transferfromselectform');
    	$transferfromselectform->setRequired(TRUE);
    	$transferfromselectform->setLabel('Transfer From*');
    	$transferfromselectform->addSelectAttrib('onchange', 'loadMatchingGroupRecords(this.value); updateMatchingButtons(\'' . TransferNamespace::TEMP_TRANSFER . '\', this.value, document.getElementById(\'transfertoselect\').value)');
    	$this->addSubForm($transferfromselectform, 'transferfromselectform');
		
		$transfertoselectform = new LocationSelectForm('transfertoselect');
    	$transfertoselectform->setName('transfertoselectform');
    	$transfertoselectform->setRequired(TRUE);;
    	$transfertoselectform->setLabel('Transfer To*');
    	$transfertoselectform->addSelectAttrib('onchange', 'updateMatchingButtons(\'' . TransferNamespace::TEMP_TRANSFER . '\', document.getElementById(\'transferfromselect\').value, this.value)');
    	$this->addSubForm($transfertoselectform, 'transfertoselectform');
    	
    	$temptransfercommentstextarea = new ACORNTextArea('temptransfercommentstextarea');
		$temptransfercommentstextarea->setName('temptransfercommentstextarea');
		$temptransfercommentstextarea->setLabel('Comments');
    	$this->addElement($temptransfercommentstextarea);
		
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'group/grouptemporarytransfer.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
	
}
?>