<?php
/**
 * ProposalSummaryForm
 *
 * @author vcrema
 * Created September 30, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class ProposalSummaryForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hiddenapprovaltype = new Zend_Form_Element_Hidden('hiddenapprovaltype');
    	$hiddenapprovaltype->setName('hiddenapprovaltype');
    	$hiddenapprovaltype->setValue(ProposalApprovalDAO::APPROVAL_TYPE_APPROVE);
    	$hiddenapprovaltype->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenapprovaltype);
    	
    	$approvalcommentstextarea = new ACORNTextArea('approvalcommentstextarea');
    	$approvalcommentstextarea->setLabel('Comments');
    	$approvalcommentstextarea->setName('approvalcommentstextarea');
    	$this->addElement($approvalcommentstextarea);
    	
    	$authorselect = new ACORNSelect('summaryauthorselect');
    	$authorselect->setLabel('Author');
    	$authorselect->setName('summaryauthorselect');
    	$authorselect->setAttrib('onclick', 'loadAuthorOptions(\'summaryauthorselect\')');
		$this->addElement($authorselect);
    	
    	$toinput = new ACORNTextField('summarytoinput');
    	$toinput->setLabel('Notification To*');
    	$toinput->setName('summarytoinput');
    	$toinput->setRequired(TRUE);
    	$this->addElement($toinput);
    	
    	$ccinput = new ACORNTextField('summaryccinput');
    	$ccinput->setLabel('Notification Cc');
    	$ccinput->setName('summaryccinput');
    	$this->addElement($ccinput);
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'proposalapproval/proposalsummary.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
    public function isValid($values)
    {
    	$isvalid = parent::isValid($values);
    	
    	if ($isvalid && empty($values['approvalcommentstextarea']) && $values['hiddenapprovaltype'] != ProposalApprovalDAO::APPROVAL_TYPE_APPROVE)
    	{
    		$isvalid = FALSE;
    		$this->getElement('approvalcommentstextarea')->addError('You must enter a comment.');
 		}
 		return $isvalid;
    }
}
?>