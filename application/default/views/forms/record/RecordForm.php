<?php
/**
 * RecordForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
class RecordForm extends ACORNForm
{
	
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$recordidentificationform = new RecordIdentificationForm($this->item);
    	$recordidentificationform->setName('recordidentificationform');
    	$this->addSubForm($recordidentificationform, 'recordidentificationform');
    	
    	$recordloginform = new RecordLoginForm();
    	$recordloginform->setName('recordloginform');
    	$this->addSubForm($recordloginform, 'recordloginform');
    	
    	$recordproposalform = new RecordProposalForm();
    	$recordproposalform->setName('recordproposalform');
    	$this->addSubForm($recordproposalform, 'recordproposalform');
    	
    	$recordreportform = new RecordReportForm();
    	$recordreportform->setName('recordreportform');
    	$this->addSubForm($recordreportform, 'recordreportform');
    	
    	$recordlogoutform = new RecordLogoutForm();
    	$recordlogoutform->setName('recordlogoutform');
    	$this->addSubForm($recordlogoutform, 'recordlogoutform');
    	
    	$recordfileform = new FilesForm();
    	$recordfileform->setName('recordfileform');
    	$this->addSubForm($recordfileform, 'recordfileform');
    	
    	$temporarytransferform = new RecordTemporaryTransferForm();
    	$temporarytransferform->setName('recordtemporarytransferform');
    	$this->addSubForm($temporarytransferform, 'recordtemporarytransferform');
    	
    	$temporarytransferreturnform = new RecordTemporaryTransferReturnForm();
    	$temporarytransferreturnform->setName('recordtemporarytransferreturnform');
    	$this->addSubForm($temporarytransferreturnform, 'recordtemporarytransferreturnform');
    	
    	$transferdialogform = new TransferDialogForm();
    	$transferdialogform->setAction('transferreport');
    	$transferdialogform->setName('transferdialogform');
    	$this->addSubForm($transferdialogform, 'transferdialogform');
    }
}
?>