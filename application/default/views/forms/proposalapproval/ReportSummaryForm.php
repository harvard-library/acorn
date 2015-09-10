<?php
/**
 * ReportSummaryForm
 *
 * @author vcrema
 * Created September 30, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class ReportSummaryForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'proposalapproval/reportsummary.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
}
?>