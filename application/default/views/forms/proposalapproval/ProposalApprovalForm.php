<?php
/**
 * RecordForm
 *
 * @author vcrema
 * Created September 30, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
class ProposalApprovalForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$proposalsummaryform = new ProposalSummaryForm();
    	$proposalsummaryform->setName('proposalsummaryform');
    	$this->addSubForm($proposalsummaryform, 'proposalsummaryform');
    	
    	$historyform = new ProposalApprovalHistoryForm();
    	$historyform->setName('historyform');
    	$this->addSubForm($historyform, 'historyform');
    	
    	$reportsummaryform = new ReportSummaryForm();
    	$reportsummaryform->setName('reportsummaryform');
    	$this->addSubForm($reportsummaryform, 'reportsummaryform');
    	
    	$recordfileform = new FilesForm();
    	$recordfileform->setName('recordfileform');
    	$this->addSubForm($recordfileform, 'recordfileform');
    	
    }
}
?>