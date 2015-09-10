<?php

/**
 * UserController - The default controller class
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class UserController extends Zend_Controller_Action 
{
	private $changePasswordForm;
	
	private function getChangePasswordForm()
	{
		if (!isset($this->changePasswordForm))
		{
			$this->changePasswordForm = new ChangePasswordForm();
		}
		return $this->changePasswordForm;
	}
	
	/**
	 * The default action - show the home page
	 */
    public function indexAction() 
    {
    	RecordNamespace::clearAll();
    }
    
	/**
	 * Finds the list of records for the current user
	 */
    public function finduserrecordsAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$records = CombinedRecordsDAO::getCombinedRecordsDAO()->getCurrentUserRecords();
		//$items = ItemDAO::getItemDAO()->getCurrentUserItems();
		$userrecords = array();
		foreach ($records as $item)
		{
			$callnumlist = $item->getCallNumbers();
			$callnumbers = implode(', ',$callnumlist);
			$approval = NULL;
			if ($item->getRecordType() == 'Item')
			{
				$approval = ProposalApprovalDAO::getProposalApprovalDAO()->getProposalApprovalStatus($item->getRecordID());
			}
			array_push($userrecords, array(
					ItemDAO::AUTHOR_ARTIST => $item->getAuthorArtist(),
					ItemDAO::TITLE => $item->getTitle(),
					ItemDAO::DATE_OF_OBJECT => $item->getDateOfObject(),
					"RecordID" => $item->getRecordID(),
					'CallNumbers' => $callnumbers,
					'ItemStatus' => $item->getItemStatus(),
					'RecordType' => $item->getRecordType(),
					'ProposalApproval' => $approval
				)
			);
		}
		
    	//Only use the results
    	$resultset = array('Result' => $userrecords);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	/**
	 * Finds the list of curator records for the current user
	 */
    public function findcuratorrecordsAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$records = ItemDAO::getItemDAO()->getCurrentCuratorRecords();
		$userrecords = array();
		foreach ($records as $item)
		{
			$callnumlist = $item->getCallNumbers();
			$callnumbers = implode(',',$callnumlist);
			$approval = NULL;
			$approval = ProposalApprovalDAO::getProposalApprovalDAO()->getProposalApprovalStatus($item->getItemID());
			array_push($userrecords, array(
					ItemDAO::AUTHOR_ARTIST => $item->getAuthorArtist(),
					ItemDAO::TITLE => $item->getTitle(),
					ItemDAO::DATE_OF_OBJECT => $item->getDateOfObject(),
					"RecordID" => $item->getItemID(),
					'CallNumbers' => $callnumbers,
					'ProposalApproval' => $approval
				)
			);
		}
		
    	//Only use the results
    	$resultset = array('Result' => $userrecords);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
    public function cleareditsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$auth = Zend_Auth::getInstance();
    	$personid = NULL;
    	//If there is authorization, clear the edits
  		if ($auth->hasIdentity()) 
    	{
    		$identity = $auth->getIdentity();
    		$personid = $identity[PeopleDAO::PERSON_ID];
    	}
    	
    	if (!is_null($personid))
    	{
    		ItemDAO::getItemDAO()->clearEdits($personid);
    	}
    }
    
	public function setrecordeditAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$auth = Zend_Auth::getInstance();
    	
    	$personid = NULL;
    	
    	//If there is authorization, get the person logged in
    	if ($auth->hasIdentity())
    	{
    		$identity = $auth->getIdentity();
    		$personid = $identity[PeopleDAO::PERSON_ID];
    	}
    	
    	$recordtype = $this->getRequest()->getParam("recordtype", "item");
    	
    	if ($recordtype == "item")
    	{
    		$item = RecordNamespace::getCurrentItem();
    		if (!is_null($item))
    		{
    			//If the person is logged in, lock the record
    			if ($auth->hasIdentity()) 
		    	{
		    		ItemDAO::getItemDAO()->setEditStatus($item, TRUE, $personid);
		    	}  	
    		}
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    		if (!is_null($item))
    		{
    			//If the person is logged in, lock the record
    			if ($auth->hasIdentity()) 
		    	{
		    		OnSiteWorkDAO::getOnSiteWorkDAO()->setEditStatus($item, TRUE, $personid);
		    	}
    		}
    	}
    }
    
    public function changepasswordAction()
    {
    	$this->view->form = $this->getChangePasswordForm();
    }
    
    public function savepasswordchangeAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getChangePasswordForm();
			$formData = $this->_request->getPost();
			
			if ($form->isValid($formData))
			{
				//Get the current person id
				$auth = Zend_Auth::getInstance();
	        	$identity = $auth->getIdentity();
	        	$currentpersonid = $identity[PeopleDAO::PERSON_ID];
				
	        	//Save the new password
	        	$updatedpersonid = PeopleDAO::getPeopleDAO()->savePassword($currentpersonid, $form->getValue('newpassword'));
				
	        	if (!is_null($updatedpersonid))
				{
					PeopleNamespace::setSaveStatus(PeopleNamespace::SAVE_STATE_SUCCESS);
					
					//Go back to the record screen.
					$this->_helper->redirector('changepassword');
				}
				//TODO otherwise display an error message regarding the save
				else 
				{
						$this->displayPasswordErrors('There was a problem saving your new password.  Please try again.');
				}
			}
			else
			{
				$this->displayPasswordErrors(ACORNConstants::MESSAGE_INVALID_FORM);
			}
		}
		else
		{
			//If the url was typed directly, go to the DE screen.
			$this->_helper->redirector('changepassword');
		}
    }
    
    
	private function displayPasswordErrors($message)
    {
    	$form = $this->getChangePasswordForm();
    	$this->view->form = $form;
    	$this->view->placeholder('passworderrormessage')->set($message);
		$this->render('changepassword');
    }
}
