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
 * BulkLogoutForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class BulkLogoutForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$transferlistname = new Zend_Form_Element_Hidden('transferlistname');
    	$transferlistname->setName('transferlistname');
		$transferlistname->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$transferlistname->setValue(TransferNamespace::LOGOUT);
    	$this->addElement($transferlistname);
    	
    	$transferids = new Zend_Form_Element_Hidden('transferids');
    	$transferids->setName('transferids');
		$transferids->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->addElement($transferids);
		
    	$logoutdateinput = new DateTextField('logoutdateinput');
    	$logoutdateinput->setLabel('Logout Date*');
		$logoutdateinput->setName('logoutdateinput');
		$logoutdateinput->setRequired(TRUE);;
    	$logoutdateinput->setAttrib('onfocus', 'showLogoutChooser()');
		$logoutdateinput->setAttrib('readonly', 'readonly');
		$this->addElement($logoutdateinput);
		
		$logoutbyselectform = new PersonSelectForm('logoutbyselect');
    	$logoutbyselectform->setRoleTypes(array('Staff'));
    	$logoutbyselectform->setName('logoutbyselectform');
    	$logoutbyselectform->setRequired(TRUE);;
    	$logoutbyselectform->setLabel('Logout By*');
    	$this->addSubForm($logoutbyselectform, 'logoutbyselectform');
		
		$logoutfromselectform = new LocationSelectForm('logoutfromselect');
    	$logoutfromselectform->setName('logoutfromselectform');
    	$logoutfromselectform->setRequired(TRUE);
    	$logoutfromselectform->setLabel('Transfer From*');
    	$logoutfromselectform->addSelectAttrib('onchange', 'refreshLogoutTable()');
    	$this->addSubForm($logoutfromselectform, 'logoutfromselectform');
		
		$logouttoselectform = new LocationSelectForm('logouttoselect');
    	$logouttoselectform->setName('logouttoselectform');
    	$logouttoselectform->setRequired(TRUE);;
    	$logouttoselectform->setLabel('Transfer To*');
    	$this->addSubForm($logouttoselectform, 'logouttoselectform');
    	
    	$repositoryselectform = new LocationSelectForm(array(ACORNSelect::INCLUDE_BLANK => TRUE, LocationSelectForm::LOCATION_SELECT_NAME => 'repositoryselect'));
    	$repositoryselectform->setName('repositoryselectform');
    	$repositoryselectform->setLabel('Display Repository');
    	$repositoryselectform->addSelectAttrib('onchange', 'refreshLogoutTable()');
    	$this->addSubForm($repositoryselectform, 'repositoryselectform');
    	
    	$transferdialogform = new TransferDialogForm();
    	$transferdialogform->setAction('bulklogouttransferreport');
    	$transferdialogform->setName('transferdialogform');
    	$this->addSubForm($transferdialogform, 'transferdialogform');
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'transfers/bulklogout.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
    
}
?>