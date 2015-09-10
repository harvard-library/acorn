<?php
/**
 * GroupForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class GroupForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$groupidentificationform = new GroupIdentificationForm();
    	$groupidentificationform->setName('groupidentificationform');
    	$this->addSubForm($groupidentificationform, 'groupidentificationform');
    	
    	$grouploginform = new GroupLoginForm();
    	$grouploginform->setName('grouploginform');
    	$this->addSubForm($grouploginform, 'grouploginform');
    	
    	$groupproposalform = new GroupProposalForm();
    	$groupproposalform->setName('groupproposalform');
    	$this->addSubForm($groupproposalform, 'groupproposalform');
    	
    	$groupreportform = new GroupReportForm();
    	$groupreportform->setName('groupreportform');
    	$this->addSubForm($groupreportform, 'groupreportform');
    	
    	$grouplogoutform = new GroupLogoutForm();
    	$grouplogoutform->setName('grouplogoutform');
    	$this->addSubForm($grouplogoutform, 'grouplogoutform');
    	
    	$groupfileform = new GroupFilesForm();
    	$groupfileform->setName('groupfileform');
    	$this->addSubForm($groupfileform, 'groupfileform');
    	
    	$temporarytransferform = new GroupTemporaryTransferForm();
    	$temporarytransferform->setName('grouptemporarytransferform');
    	$this->addSubForm($temporarytransferform, 'grouptemporarytransferform');
    	
    	$temporarytransferreturnform = new GroupTemporaryTransferReturnForm();
    	$temporarytransferreturnform->setName('grouptemporarytransferreturnform');
    	$this->addSubForm($temporarytransferreturnform, 'grouptemporarytransferreturnform');
    
    	$transferdialogform = new TransferDialogForm();
    	$transferdialogform->setAction('grouptransferreport');
    	$transferdialogform->setName('transferdialogform');
    	$this->addSubForm($transferdialogform, 'transferdialogform');
    
    }
}
?>