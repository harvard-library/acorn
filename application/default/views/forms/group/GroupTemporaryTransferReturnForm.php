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
 * GroupTemporaryTransferReturnForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class GroupTemporaryTransferReturnForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$transferlistname = new Zend_Form_Element_Hidden('transferlistname');
    	$transferlistname->setName('transferlistname');
		$transferlistname->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$transferlistname->setValue(TransferNamespace::TEMP_TRANSFER_RETURN);
    	$this->addElement($transferlistname);
    	
    	$transferids = new Zend_Form_Element_Hidden('transferids7');
    	$transferids->setName('transferids7');
		$transferids->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->addElement($transferids);
		
		
    	$transferreturndateinput = new DateTextField('transferreturndateinput');
    	$transferreturndateinput->setLabel('Return Date*');
		$transferreturndateinput->setName('transferreturndateinput');
		$transferreturndateinput->setRequired(TRUE);;
    	$transferreturndateinput->setAttrib('onfocus', 'showCalendarChooser(\'transferreturndateinput\')');
		$transferreturndateinput->setAttrib('readonly', 'readonly');
		$this->addElement($transferreturndateinput);
		
		$transferreturnfromselectform = new LocationSelectForm('transferreturnfromselect');
    	$transferreturnfromselectform->setName('transferreturnfromselectform');
    	$transferreturnfromselectform->setRequired(TRUE);
    	$transferreturnfromselectform->setLabel('Return From*');
    	$transferreturnfromselectform->addSelectAttrib('onchange', 'loadMatchingGroupRecords(this.value); updateMatchingButtons(\'' . TransferNamespace::TEMP_TRANSFER_RETURN . '\', this.value, document.getElementById(\'transferreturntoselect\').value)');
    	$this->addSubForm($transferreturnfromselectform, 'transferreturnfromselectform');
		
		$transferreturntoselectform = new LocationSelectForm('transferreturntoselect');
    	$transferreturntoselectform->setName('transferreturntoselectform');
    	$transferreturntoselectform->setRequired(TRUE);;
    	$transferreturntoselectform->setLabel('Return To*');
    	$transferreturntoselectform->addSelectAttrib('onchange', 'updateMatchingButtons(\'' . TransferNamespace::TEMP_TRANSFER_RETURN . '\', document.getElementById(\'transferreturnfromselect\').value, this.value)');
    	$this->addSubForm($transferreturntoselectform, 'transferreturntoselectform');
    	
    	$returncommentstextarea = new ACORNTextArea('returncommentstextarea');
		$returncommentstextarea->setName('returncommentstextarea');
		$returncommentstextarea->setLabel('Comments');
    	$this->addElement($returncommentstextarea);
		
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'group/grouptemporarytransferreturn.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
}
?>