<?php
/**
 * RecordTemporaryTransferReturnForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class RecordTemporaryTransferReturnForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hiddenitemid = new Zend_Form_Element_Hidden('hiddentemptransferreturnitemid');
    	$hiddenitemid->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenitemid);
    	
    	$transferlistname = new Zend_Form_Element_Hidden('transferlistname');
    	$transferlistname->setName('transferlistname');
		$transferlistname->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$transferlistname->setValue(TransferNamespace::TEMP_TRANSFER_RETURN);
    	$this->addElement($transferlistname);
		
    	$transferreturndateinput = new DateTextField('transferreturndateinput');
    	$transferreturndateinput->setLabel('Return Date*');
		$transferreturndateinput->setName('transferreturndateinput');
		$transferreturndateinput->setRequired(TRUE);;
    	$transferreturndateinput->setAttrib('readonly', 'readonly');
		$transferreturndateinput->setAttrib('onfocus', 'showCalendarChooser(\'transferreturndateinput\')');
		$this->addElement($transferreturndateinput);
		
		//Get the current record's info if it exists
    	$item = RecordNamespace::getCurrentItem();
    	$deptid = NULL;
    	$curid = NULL;
    	if (isset($item))
    	{
    		$deptid = $item->getDepartmentID();
    		$curid = $item->getCuratorID();
    	}
    	
		$transferreturnfromselectform = new LocationSelectForm('transferreturnfromselect');
    	$transferreturnfromselectform->setName('transferreturnfromselectform');
    	$transferreturnfromselectform->setRequired(TRUE);
    	$transferreturnfromselectform->setLabel('Return From*');
    	$transferreturnfromselectform->addSelectAttrib('onchange', 'updateTransferButtons(\'' . TransferNamespace::TEMP_TRANSFER_RETURN . '\')');
    	$this->addSubForm($transferreturnfromselectform, 'transferreturnfromselectform');
		
		$transferreturntoselectform = new LocationSelectForm('transferreturntoselect');
    	$transferreturntoselectform->setName('transferreturntoselectform');
    	$transferreturntoselectform->setRequired(TRUE);;
    	$transferreturntoselectform->setLabel('Return To*');
    	$transferreturntoselectform->addSelectAttrib('onchange', 'updateTransferButtons(\'' . TransferNamespace::TEMP_TRANSFER_RETURN . '\')');
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
   			'ViewScript', array('viewScript' => 'record/recordtemporarytransferreturn.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
    
}
?>