<?php
/**
 * RecordLoginForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class RecordLoginForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hiddenloginid = new Zend_Form_Element_Hidden('hiddenloginitemid');
    	$hiddenloginid->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenloginid);
    	
    	$transferlistname = new Zend_Form_Element_Hidden('transferlistname');
    	$transferlistname->setName('transferlistname');
		$transferlistname->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$transferlistname->setValue(TransferNamespace::LOGIN);
    	$this->addElement($transferlistname);
		
    	$logindateinput = new DateTextField('logindateinput');
    	$logindateinput->setLabel('Login Date*');
		$logindateinput->setName('logindateinput');
		$logindateinput->setAttrib('onfocus', 'showCalendarChooser(\'logindateinput\')');
		$logindateinput->setAttrib('readonly', 'readonly');
		$logindateinput->setRequired(TRUE);;
    	$this->addElement($logindateinput);
		
		$loginbyselectform = new PersonSelectForm('loginbyselect');
    	$loginbyselectform->setRoleTypes(array('Staff'));
    	$loginbyselectform->setName('loginbyselectform');
    	$loginbyselectform->setRequired(TRUE);;
    	$loginbyselectform->setLabel('Login By*');
    	$this->addSubForm($loginbyselectform, 'loginbyselectform');
		
    	//Get the current record's info if it exists
    	$item = RecordNamespace::getCurrentItem();
    	$deptid = NULL;
    	$curid = NULL;
    	if (isset($item))
    	{
    		$deptid = $item->getDepartmentID();
    		$curid = $item->getCuratorID();
    	}
    	
		$loginfromselectform = new LocationSelectForm('loginfromselect');
    	$loginfromselectform->setName('loginfromselectform');
    	$loginfromselectform->setLabel('Transfer From*');
    	$loginfromselectform->setRequired(TRUE);
    	$loginfromselectform->setAttrib('disabled', 'disabled');
    	$loginfromselectform->addSelectAttrib('onchange', 'updateTransferButtons(\'' . TransferNamespace::LOGIN . '\')');
    	$this->addSubForm($loginfromselectform, 'loginfromselectform');
    	
    	$loginfromoverridecheckbox = new Zend_Form_Element_Checkbox('loginfromoverridecheckbox');
        $loginfromoverridecheckbox->setLabel('Override Location');
        $loginfromoverridecheckbox->setAttribs(array('class' => 'checkboxinput',
        	'onclick' => 'toggleLoginFromDisabled(this.checked)'));
        $loginfromoverridecheckbox->setDecorators(Decorators::$CHECKBOX_DECORATORS);
    	$this->addElement($loginfromoverridecheckbox);
    	
		$logintoselectform = new LocationSelectForm('logintoselect');
    	$logintoselectform->setName('logintoselectform');
    	$logintoselectform->setRequired(TRUE);;
    	$logintoselectform->setLabel('Transfer To*');
    	$logintoselectform->addSelectAttrib('onchange', 'updateTransferButtons(\'' . TransferNamespace::LOGIN . '\')');
    	$this->addSubForm($logintoselectform, 'logintoselectform');
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'record/recordlogin.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
    
}
?>