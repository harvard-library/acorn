<?php
/**
 * RecordTemporaryTransferForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class RecordTemporaryTransferForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hiddenitemid = new Zend_Form_Element_Hidden('hiddentemptransferitemid');
    	$hiddenitemid->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenitemid);
    	
    	$transferlistname = new Zend_Form_Element_Hidden('transferlistname');
    	$transferlistname->setName('transferlistname');
		$transferlistname->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$transferlistname->setValue(TransferNamespace::TEMP_TRANSFER);
    	$this->addElement($transferlistname);
		
    	$transferdateinput = new DateTextField('transferdateinput');
    	$transferdateinput->setLabel('Transfer Date*');
		$transferdateinput->setName('transferdateinput');
		$transferdateinput->setRequired(TRUE);;
    	$transferdateinput->setAttrib('readonly', 'readonly');
		$transferdateinput->setAttrib('onfocus', 'showCalendarChooser(\'transferdateinput\')');
		$this->addElement($transferdateinput);
		
		//Get the current record's info if it exists
    	$item = RecordNamespace::getCurrentItem();
    	$deptid = NULL;
    	$curid = NULL;
    	if (isset($item))
    	{
    		$deptid = $item->getDepartmentID();
    		$curid = $item->getCuratorID();
    	}
    	
		$transferfromselectform = new LocationSelectForm('transferfromselect');
    	$transferfromselectform->setName('transferfromselectform');
    	$transferfromselectform->setRequired(TRUE);
    	$transferfromselectform->setLabel('Transfer From*');
    	$transferfromselectform->addSelectAttrib('onchange', 'updateTransferButtons(\'' . TransferNamespace::TEMP_TRANSFER . '\')');
    	$this->addSubForm($transferfromselectform, 'transferfromselectform');
		
		$transfertoselectform = new LocationSelectForm('transfertoselect');
    	$transfertoselectform->setName('transfertoselectform');
    	$transfertoselectform->setRequired(TRUE);;
    	$transfertoselectform->setLabel('Transfer To*');
    	$transfertoselectform->addSelectAttrib('onchange', 'updateTransferButtons(\'' . TransferNamespace::TEMP_TRANSFER . '\')');
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
   			'ViewScript', array('viewScript' => 'record/recordtemporarytransfer.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
    
}
?>