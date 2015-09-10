<?php
/**
 * ProposalApprovalHistoryForm
 *
 * @author vcrema
 * Created September 30, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class ProposalApprovalHistoryForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$historycommentstextarea = new ACORNTextArea('historycommentstextarea');
    	$historycommentstextarea->setLabel('Comments*');
    	$historycommentstextarea->setName('historycommentstextarea');
    	$historycommentstextarea->setRequired(TRUE);
    	$this->addElement($historycommentstextarea);

    	$authorselect = new ACORNSelect('historyauthorselect');
    	$authorselect->setLabel('Author');
    	$authorselect->setName('historyauthorselect');
    	$authorselect->setAttrib('onclick', 'loadAuthorOptions(\'historyauthorselect\')');
		$this->addElement($authorselect);
    	
    	$toinput = new ACORNTextField('historytoinput');
    	$toinput->setLabel('Notification To*');
    	$toinput->setName('historytoinput');
    	$toinput->setRequired(TRUE);
    	$this->addElement($toinput);
    	
    	$ccinput = new ACORNTextField('historyccinput');
    	$ccinput->setLabel('Notification Cc');
    	$ccinput->setName('historyccinput');
    	$this->addElement($ccinput);
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'proposalapproval/proposalhistory.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
}
?>