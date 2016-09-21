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
 * OswController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
	

class OswController extends Zend_Controller_Action
{
	private $oswForm;
	private $acornRedirector;
	
	private function getOswForm()
	{
		if (is_null($this->oswForm))
		{
			$this->oswForm = new OswForm();
		}
		return $this->oswForm;
	}
	
	private function getAcornRedirector()
	{
		if (is_null($this->acornRedirector))
		{
			$this->acornRedirector = new AcornRedirector();
		}
		return $this->acornRedirector;
	}
	
	public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
        {
            $this->_helper->redirector('index', 'index');
        }
    }
    
    public function saveoswAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getOswForm()->getSubForm("oswidentificationform");
			$formData = $this->_request->getPost();
			
			if ($form->isValid($formData))
			{
				//Save the data
				$oswid = $this->saveOSW($form->getValues());
				
				if (!is_null($oswid))
				{
					RecordNamespace::setCurrentOSW(OnSiteWorkDAO::getOnSiteWorkDAO()->getOSW($oswid));
					RecordNamespace::setOriginalOSW(OnSiteWorkDAO::getOnSiteWorkDAO()->getOSW($oswid));
					
					RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);
					//Go back to the record screen.
					$this->_helper->redirector('osw', 'record', NULL, array('recordnumber' => $oswid));
				}
				//TODO otherwise display an error message regarding the save
				else 
				{
						$this->displayRecordErrors($formData);
				}
			}
			else
			{
				$this->displayRecordErrors($formData);
			}
		}
		else
		{
			//If the url was typed directly, go to the DE screen.
			$this->_helper->redirector('newosw');
		}
    }
    
    private function displayRecordErrors(array $formData)
    {
    	$form = $this->getOswForm();
		//Populate the data with what the user
		//gave but also display the error messages.
		$form->populate($formData);
		$this->view->form = $form;
		
		
		$item = RecordNamespace::getCurrentOSW();
		$recordnumberlabel = "OSW Record #" . $item->getOSWID();
		$createdate = "";
		$createdby = "";
		$audittrailitems = $item->getAuditTrail("Insert");
		if (!empty($audittrailitems))
		{
			$audittrailitem = current($audittrailitems);
			$createdate = $audittrailitem->getDate();
			$createdby = $audittrailitem->getPerson()->getDisplayName();
		}
		$this->view->placeholder("oswerrormessage")->set('Changes were not saved.  Please correct the errors in the form below.');
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('recordnumber')->set($recordnumberlabel);
		
		$this->renderScript('record/osw.phtml');
    }
    
    private function saveOSW(array $formvalues)
    {
    	//Get the current item
    	$item = RecordNamespace::getCurrentOSW();
    	$item->setComments($formvalues['commentstextarea']);
    	$item->setDepartmentID($formvalues['departmentselect']);
    	$item->setGroupID($formvalues['groupselect']);
    	$item->setHomeLocationID($formvalues['repositoryselect']);
    	$item->setChargeToID($formvalues['chargetoselect']);
    	$item->setProjectID($formvalues['projectselect']);
    	$item->setPurposeID($formvalues['purposeselect']);
    	$item->setTitle($formvalues['titleinput']);
    	$item->setCuratorID($formvalues['curatorselect']);
    	//This is not necessary for OSW so just default it
    	$item->setApprovingCuratorID($formvalues['curatorselect']);
    	
    	//OSW specific data
    	$daterange = new DateRange();
    	$daterange->setStartDate($formvalues['workstartdateinput']);
    	$daterange->setEndDate($formvalues['workenddateinput']);
    	$item->setDateRange($daterange);
    	$item->setFormatID($formvalues['formatselect']);
    	
    	$item->getFunctions()->getReport()->setTreatment($formvalues['workdescriptiontextarea']);
    	$item->setProposedHours($formvalues['proposedhours']);
    	$item->getFunctions()->getReport()->setWorkLocation($formvalues['worklocationselect']);
    	
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$item->getFunctions()->getReport()->setReportBy($identity[PeopleDAO::PERSON_ID]);
    	$zenddate = new Zend_Date();
		$item->getFunctions()->getReport()->setReportDate($zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT));
    	
    	$olditem = RecordNamespace::getOriginalOSW();
    	$oswid = OnSiteWorkDAO::getOnSiteWorkDAO()->saveOSW($item, $olditem);
    	return $oswid;
    }
    
	public function deleteoswrecordAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$item = RecordNamespace::getCurrentOSW();
    	if (isset($item))
    	{
    		OnSiteWorkDAO::getOnSiteWorkDAO()->deleteOSW($item);
    	}
    	$this->_helper->redirector('index', 'user');
    }
    
    public function resetoswAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$oldosw = RecordNamespace::getOriginalOSW();
    	if (isset($oldosw))
    	{
    		$currentosw = clone $oldosw;
    	}
    	else 
    	{
    		$currentosw = new OnSiteWork();
    	}
    	RecordNamespace::setCurrentOSW($currentosw);
    }
    
    /**
     * If any of the new call numbers attempting to be saved exist in another item,
     * the user can 'override' them so that they are still saved.  This method places
     * the call numbers into an array that is stored in the Item record so that the
     * validator ignores them on the next save.
     */
    public function overridecallnumbersAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    
    	//Add the call numbers to the override list so that the error messages will
    	//be ignored when it is being saved.
    	$item = RecordNamespace::getCurrentOSW();
    	$olditem = RecordNamespace::getOriginalOSW();
    	 
    	$newcallnumbers = $item->getCallNumbers();
    	if (isset($olditem))
    	{
    		$newcallnumbers = $item->getCallNumberDifference($olditem->getCallNumbers());
    	}
    	foreach ($newcallnumbers as $callnumber)
    	{
    		$exclusionids = !is_null($item->getPrimaryKey()) ? array($item->getPrimaryKey()) : NULL;
    		//Check if the call number exists in the database already and if so, add it to the item record.
    		$dup = CombinedRecordsDAO::getCombinedRecordsDAO()->callNumberExists($callnumber, $exclusionids);
    		if (!is_null($dup))
    		{
    			$this->getRecordForm()->clearErrorMessages();
    			$item->addDuplicateCallNumber($callnumber);
    			$this->getRecordForm()->getSubForm('recordidentificationform')->getElement('callnumberhidden')->clearErrorMessages();
    		}
    	}
    	 
    	$data = array('Success' => TRUE);
    	$jsonstring = Zend_Json::encode($data);
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
    	->setBody($jsonstring);
    }
    
    /**
     * If the new call number attempting to be saved exists in another item,
     * the user can 'override' it so that it is still saved.  This method places
     * the call number into an array that is stored in the Item record so that the
     * validator ignores it on the next save.
     */
    public function overridecallnumberAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    
    	$callnumber = $this->getRequest()->getParam('callnumber');
    
    	//Add the call number to the override list so that the error messages will
    	//be ignored next time a call number is validated
    	$item = RecordNamespace::getCurrentOSW();
    	$item->addCallNumber($callnumber);
    	$item->addDuplicateCallNumber($callnumber);
    	RecordNamespace::setCurrentOSW($item);
    	 
    	$data = array('Success' => TRUE);
    	$jsonstring = Zend_Json::encode($data);
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
    	->setBody($jsonstring);
    }
    
    /**
     * Finds a list of call numbers for the current item
     */
    public function findcallnumbersAction()
    {
    	//Don't display a new view
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    
    	$item = RecordNamespace::getCurrentOSW();
    	 
    	$callnumbers = array();
    	if (isset($item) && !is_null($item))
    	{
    		$callnumberarray = $item->getCallNumbers(TRUE);
    		foreach ($callnumberarray as $callnumber)
    		{
    			array_push($callnumbers, array(CombinedRecordsDAO::CALL_NUMBER => $callnumber));
    		}
    	}
    
    	if (isset($item) && !is_null($item) && $item->isEditable())
    	{
    		//Add a row to use as an 'add' row
    		array_push($callnumbers, array(CombinedRecordsDAO::CALL_NUMBER => '(Double click to add)'));
    	}
    	//Only use the results
    	$resultset = array('Result' => $callnumbers);
    	$retval = Zend_Json::encode($resultset);
    	//Make sure JS knows it is in JSON format.
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
    	->setBody($retval);
    }
}
?>