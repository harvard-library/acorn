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
 * GroupForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
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