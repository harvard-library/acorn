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
 * GrouptemptransferController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
	

class GrouptemptransferController extends Zend_Controller_Action
{
	private $groupForm;
	
	private function getGroupForm()
	{
		if (is_null($this->groupForm))
		{
			$this->groupForm = new GroupForm();
		}
		return $this->groupForm;
	}
	
	public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
        {
            $this->_helper->redirector('index', 'index');
        }
    }

	/*
     * Display the errors on the form
     */
	private function displayGroupErrors(array $formData, $message, $errorelementname)
    {
    	$form = $this->getGroupForm();
		//Populate the data with what the user
		//gave but also display the error messages.
		$form->populate($formData);
		$this->view->form = $form;

		$group = GroupNamespace::getCurrentGroup();
		$groupnumberlabel = "Group " . $group->getPrimaryKey();
		$createdate = "";
		$createdby = "";
		$audittrailitems = $group->getAuditTrail("Insert");
		if (!empty($audittrailitems))
		{
			$audittrailitem = current($audittrailitems);
			$createdate = $audittrailitem->getDate();
			$createdby = $audittrailitem->getPerson()->getDisplayName();
		}
		$this->view->placeholder($errorelementname)->set($message);
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('groupnumber')->set($groupnumberlabel);
		
		$this->renderScript('group/index.phtml');
    }
    
	private function validateTempTransferFormAndSave(array $recordids, array $formData)
    {
    	$form = $this->getGroupForm()->getSubForm("grouptemporarytransferform");
		
    	GroupNamespace::setRenderTab(GroupNamespace::TEMP_TRANSFER);
		if ($form->isValid($formData))
		{
			$group = GroupNamespace::getCurrentGroup();
			$grouprecords = $group->getRecords();
			$grouprecordstosave = array();
			//Save the data
			foreach ($recordids as $recordid)
			{
				$grouprecordstosave[$recordid] = $grouprecords[$recordid];
			}
			
			//Save the data
			$recordssaved = $this->saveGroupTempTransfer($grouprecordstosave, $form->getValues());
			if ($recordssaved == 0 && count($grouprecordstosave) > 0)
			{
				$this->displayGroupErrors($formData, ACORNConstants::MESSAGE_PROBLEM_SAVING, 'temptransfererrormessage');
				$grouprecordstosave = NULL;
			}
			else 
			{
				$group->clearRecords(TRUE);
			}
		}
		else
		{
			$this->displayGroupErrors($formData, ACORNConstants::MESSAGE_INVALID_FORM, 'temptransfererrormessage');
			$grouprecordstosave = NULL;
		}
		return $grouprecordstosave;
    }
    
    private function saveGroupTempTransfer(array $recordstosave, $formvalues)
    {
    	$modeltemptransfer = new TemporaryTransfer();
    	$zenddate = new Zend_Date($formvalues['transferdateinput'], ACORNConstants::$ZEND_DATE_FORMAT);		
    	$transferdate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	$modeltemptransfer->setTransferDate($transferdate);
    	$modeltemptransfer->setTransferFrom($formvalues['transferfromselect']);
    	$modeltemptransfer->setTransferTo($formvalues['transfertoselect']);
    	$modeltemptransfer->setTransferType(TemporaryTransfersDAO::TRANSFER_TYPE_TRANSFER);
    	
    	$recordssaved = TemporaryTransfersDAO::getTemporaryTransfersDAO()->saveGroupTemporaryTransfers($recordstosave, $modeltemptransfer, $formvalues['temptransfercommentstextarea']);
    	return $recordssaved;
    }
    
 	private function validateTempTransferReturnFormAndSave(array $recordids, $formData)
    {
    	$form = $this->getGroupForm()->getSubForm("grouptemporarytransferreturnform");
		
    	GroupNamespace::setRenderTab(GroupNamespace::TEMP_TRANSFER_RETURN);
		if ($form->isValid($formData))
		{
			$group = GroupNamespace::getCurrentGroup();
			$grouprecords = $group->getRecords();
			$grouprecordstosave = array();
			//Save the data
			foreach ($recordids as $recordid)
			{
				$grouprecordstosave[$recordid] = $grouprecords[$recordid];
			}
			
			//Save the data
			$recordssaved = $this->saveGroupTempTransferReturn($grouprecordstosave, $form->getValues());
			if ($recordssaved == 0 && count($grouprecordstosave) > 0)
			{
				$this->displayGroupErrors($formData, ACORNConstants::MESSAGE_PROBLEM_SAVING, 'temptransferreturnerrormessage');
				$grouprecordstosave = NULL;
			}
			else 
			{
				$group->clearRecords(TRUE);
			}
		}
		else
		{
			$this->displayGroupErrors($formData, ACORNConstants::MESSAGE_INVALID_FORM, 'temptransferreturnerrormessage');
			$grouprecordstosave = NULL;
		}
		return $grouprecordstosave;
    }
    
	private function saveGroupTempTransferReturn(array $recordstosave, array $formvalues)
    {
    	$modeltemptransfer = new TemporaryTransfer();
    	$zenddate = new Zend_Date($formvalues['transferreturndateinput'], ACORNConstants::$ZEND_DATE_FORMAT);		
    	$transferdate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	$modeltemptransfer->setTransferDate($transferdate);
    	$modeltemptransfer->setTransferFrom($formvalues['transferreturnfromselect']);
    	$modeltemptransfer->setTransferTo($formvalues['transferreturntoselect']);
    	$modeltemptransfer->setTransferType(TemporaryTransfersDAO::TRANSFER_TYPE_RETURN);
    	
    	$recordssaved = TemporaryTransfersDAO::getTemporaryTransfersDAO()->saveGroupTemporaryTransferReturns($recordstosave, $modeltemptransfer, $formvalues['returncommentstextarea']);
    	return $recordssaved;
    }
    
	public function addtotemptransferlistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			$jsondata = $this->getRequest()->getParam("transferids6");
			$recordids = Zend_Json::decode($jsondata);
			
			$savedrecords = $this->validateTempTransferFormAndSave($recordids, $formData);
	
			//As long as it saved properly, then continue
			if (!is_null($savedrecords))
			{
		    	$group = GroupNamespace::getCurrentGroup();
		    	if (isset($group))
		    	{
		    		//Add the data to the transfer list
					foreach ($savedrecords as $record)
					{
						TransferNamespace::addToTransferList(TransferNamespace::TEMP_TRANSFER, $record);
					}
		    		GroupNamespace::setSaveStatus(GroupNamespace::SAVE_STATE_SUCCESS);
					
		    		//Ensure it goes to the proper tab
					$recnum = $group->getPrimaryKey() . '#tabview=tab6';
			    	AcornRedirector::getAcornRedirector()->setGoto('index', 'group', NULL, array('groupnumber' => $recnum));
		    	}
		    }
    	}
		
    }
    
	public function addtotemptransferreturnlistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			$jsondata = $this->getRequest()->getParam("transferids7");
			$recordids = Zend_Json::decode($jsondata);
			
			$savedrecords = $this->validateTempTransferReturnFormAndSave($recordids, $formData);
	
			//As long as it saved properly, then continue
			if (!is_null($savedrecords))
			{
		    	$group = GroupNamespace::getCurrentGroup();
		    	if (isset($group))
		    	{
		    		//Add the data to the transfer list
					foreach ($savedrecords as $record)
					{
						TransferNamespace::addToTransferList(TransferNamespace::TEMP_TRANSFER_RETURN, $record);
					}
		    		GroupNamespace::setSaveStatus(GroupNamespace::SAVE_STATE_SUCCESS);
					
		    		//Ensure it goes to the proper tab
					$recnum = $group->getPrimaryKey() . '#tabview=tab7';
			    	AcornRedirector::getAcornRedirector()->setGoto('index', 'group', NULL, array('groupnumber' => $recnum));
		    	}
		    }
    	}
		
    }    
}
?>