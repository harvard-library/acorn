<?php
/**
 * EmailProposalForm
 *
 * @author vcrema
 * Created September 30, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class EmailProposalForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hiddenactivity = new Zend_Form_Element_Hidden('hiddenactivity');
    	$hiddenactivity->setName('hiddenactivity');
    	$hiddenactivity->setValue('Proposal Submitted');
    	$hiddenactivity->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenactivity);
    	
    	$hiddendetails = new Zend_Form_Element_Hidden('hiddendetails');
    	$hiddendetails->setName('hiddendetails');
    	$hiddendetails->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddendetails);
    	
    	$hiddenemailtype = new Zend_Form_Element_Hidden('hiddenemailtype');
    	$hiddenemailtype->setName('hiddenemailtype');
    	$hiddenemailtype->setValue('Proposal');
    	$hiddenemailtype->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenemailtype);
    	
    	$toinput = new ACORNTextField('toinput');
    	$toinput->setLabel('To*');
    	$toinput->setName('toinput');
    	$toinput->setRequired(TRUE);
    	$this->addElement($toinput);
    	
    	$ccinput = new ACORNTextField('ccinput');
    	$ccinput->setLabel('Cc');
    	$ccinput->setName('ccinput');
    	$this->addElement($ccinput);
    	
    	$subjectinput = new ACORNTextField('subjectinput');
    	$subjectinput->setLabel('Subject*');
    	$subjectinput->setName('subjectinput');
    	$subjectinput->setRequired(TRUE);
    	$this->addElement($subjectinput);
    	
    	$messageinput = new ACORNTextArea('messageinput');
    	$messageinput->setLabel('Message*');
    	$messageinput->setName('messageinput');
    	$messageinput->setRequired(TRUE);
    	$this->addElement($messageinput);
    }
}
?>