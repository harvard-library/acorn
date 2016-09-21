<?php
/**
 * Copyright 2016 The President and Fellows of Harvard College
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 * RecordForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
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