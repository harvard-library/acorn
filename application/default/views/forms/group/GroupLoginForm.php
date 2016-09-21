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
 * GroupLoginForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class GroupLoginForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$transferlistname = new Zend_Form_Element_Hidden('transferlistname');
    	$transferlistname->setName('transferlistname');
		$transferlistname->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$transferlistname->setValue(TransferNamespace::LOGIN);
    	$this->addElement($transferlistname);
    	
    	$transferids = new Zend_Form_Element_Hidden('transferids1');
    	$transferids->setName('transferids1');
		$transferids->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->addElement($transferids);
		
    	$logindateinput = new DateTextField('logindateinput');
    	$logindateinput->setLabel('Login Date*');
		$logindateinput->setName('logindateinput');
		$logindateinput->setRequired(TRUE);;
    	$logindateinput->setAttrib('onfocus', 'showCalendarChooser(\'logindateinput\')');
		$logindateinput->setAttrib('readonly', 'readonly');
		$this->addElement($logindateinput);
		
		$loginbyselectform = new PersonSelectForm('loginbyselect');
    	$loginbyselectform->setRoleTypes(array('Staff'));
    	$loginbyselectform->setName('loginbyselectform');
    	$loginbyselectform->setRequired(TRUE);;
    	$loginbyselectform->setLabel('Login By*');
    	$this->addSubForm($loginbyselectform, 'loginbyselectform');
		
		$loginfromselectform = new LocationSelectForm('loginfromselect');
    	$loginfromselectform->setName('loginfromselectform');
    	$loginfromselectform->setRequired(TRUE);
    	$loginfromselectform->setLabel('Transfer From*');
    	$loginfromselectform->addSelectAttrib('onchange', 'loadMatchingGroupRecords(this.value); updateMatchingButtons(\'' . TransferNamespace::LOGIN . '\', this.value, document.getElementById(\'logintoselect\').value)');
    	$this->addSubForm($loginfromselectform, 'loginfromselectform');
		
		$logintoselectform = new LocationSelectForm('logintoselect');
    	$logintoselectform->setName('logintoselectform');
    	$logintoselectform->setRequired(TRUE);;
    	$logintoselectform->setLabel('Transfer To*');
    	$logintoselectform->addSelectAttrib('onchange', 'updateMatchingButtons(\'' . TransferNamespace::LOGIN . '\', document.getElementById(\'loginfromselect\').value, this.value)');
    	$this->addSubForm($logintoselectform, 'logintoselectform');
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'group/grouplogin.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
	
}
?>