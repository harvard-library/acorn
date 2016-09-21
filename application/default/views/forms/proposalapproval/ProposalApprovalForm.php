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
 * Created September 30, 2009
 *
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