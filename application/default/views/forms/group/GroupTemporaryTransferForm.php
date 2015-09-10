<?php
/**
 * GroupTemporaryTransferForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
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