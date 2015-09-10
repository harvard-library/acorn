<?php
/**
 * RecordLogoutForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class RecordLogoutForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hiddenlogoutid = new Zend_Form_Element_Hidden('hiddenlogoutitemid');
    	$hiddenlogoutid->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenlogoutid);
    	
    	$transferlistname = new Zend_Form_Element_Hidden('transferlistname');
    	$transferlistname->setName('transferlistname');
		$transferlistname->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$transferlistname->setValue(TransferNamespace::LOGOUT);
    	$this->addElement($transferlistname);
		
    	$logoutdateinput = new DateTextField('logoutdateinput');
    	$logoutdateinput->setLabel('Logout Date*');
		$logoutdateinput->setName('logoutdateinput');
		$logoutdateinput->setRequired(TRUE);;
    	$logoutdateinput->setAttrib('onfocus', 'showCalendarChooser(\'logoutdateinput\')');
		$this->addElement($logoutdateinput);
		
		$logoutbyselectform = new PersonSelectForm('logoutbyselect');
    	$logoutbyselectform->setRoleTypes(array('Staff'));
    	$logoutbyselectform->setName('logoutbyselectform');
    	$logoutbyselectform->setRequired(TRUE);;
    	$logoutbyselectform->setLabel('Logout By*');
    	$this->addSubForm($logoutbyselectform, 'logoutbyselectform');
		
    	//Get the current record's info if it exists
    	$item = RecordNamespace::getCurrentItem();
    	$deptid = NULL;
    	$curid = NULL;
    	if (isset($item))
    	{
    		$deptid = $item->getDepartmentID();
    		$curid = $item->getCuratorID();
    	}
    	
		$logoutfromselectform = new LocationSelectForm('logoutfromselect');
    	$logoutfromselectform->setName('logoutfromselectform');
    	$logoutfromselectform->setLabel('Transfer From*');
    	$logoutfromselectform->setRequired(TRUE);;
    	$logoutfromselectform->setAttrib('disabled', 'disabled');
    	$logoutfromselectform->addSelectAttrib('onchange', 'updateTransferButtons(\'' . TransferNamespace::LOGOUT . '\')');
    	$this->addSubForm($logoutfromselectform, 'logoutfromselectform');
		
    	$logoutfromoverridecheckbox = new Zend_Form_Element_Checkbox('logoutfromoverridecheckbox');
        $logoutfromoverridecheckbox->setLabel('Override Location');
        $logoutfromoverridecheckbox->setAttribs(array('class' => 'checkboxinput',
        	'onclick' => 'toggleLogoutFromDisabled(this.checked)'));
        $logoutfromoverridecheckbox->setDecorators(Decorators::$CHECKBOX_DECORATORS);
    	$this->addElement($logoutfromoverridecheckbox);
    	
		$logouttoselectform = new LocationSelectForm('logouttoselect');
    	$logouttoselectform->setName('logouttoselectform');
    	$logouttoselectform->setRequired(TRUE);;
    	$logouttoselectform->setLabel('Transfer To*');
    	$logouttoselectform->addSelectAttrib('onchange', 'updateTransferButtons(\'' . TransferNamespace::LOGOUT . '\')');
    	$this->addSubForm($logouttoselectform, 'logouttoselectform');
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'record/recordlogout.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
    
}
?>