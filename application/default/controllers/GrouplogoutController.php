<?php

/**
 * GrouplogoutController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class GrouplogoutController extends Zend_Controller_Action
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
	private function displayGroupErrors(array $formData, $message)
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
		$this->view->placeholder("logouterrormessage")->set($message);
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('groupnumber')->set($groupnumberlabel);
		
		$this->renderScript('group/index.phtml');
    }
    
	public function savelogoutAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			$jsondata = $this->getRequest()->getParam("transferids4");
			$recordids = Zend_Json::decode($jsondata);
			
			$savedrecords = $this->validateLogoutFormAndSave($recordids, $formData);
			
			if (!is_null($savedrecords))
			{
				$group = GroupNamespace::getCurrentGroup();
				GroupNamespace::setSaveStatus(GroupNamespace::SAVE_STATE_SUCCESS);
					
				//Ensure it goes to the proper tab
				$groupnum = $group->getPrimaryKey() . '#tabview=tab4';
				AcornRedirector::getAcornRedirector()->setGoto('index', 'group', NULL, array('groupnumber' => $groupnum));
			}	
		}
		else
		{
			//If the url was typed directly, go to the DE screen.
			$this->_helper->redirector('newgroup', 'group');
		}
    }
    
    private function validateLogoutFormAndSave(array $recordids, $formData)
    {
    	$form = $this->getGroupForm()->getSubForm("grouplogoutform");
    	GroupNamespace::setRenderTab(GroupNamespace::LOGOUT);
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
			
			$recordssaved = $this->saveGroupLogout($grouprecordstosave, $form->getValues());
			if ($recordssaved == 0 && count($grouprecordstosave) > 0)
			{
				$this->displayGroupErrors($formData, ACORNConstants::MESSAGE_PROBLEM_SAVING);
				$grouprecordstosave = NULL;
			}
			else 
			{
				$group->clearRecords(TRUE);
			}
		}
		else
		{
			$this->displayGroupErrors($formData, ACORNConstants::MESSAGE_INVALID_FORM);
			$grouprecordstosave = NULL;
		}
		return $grouprecordstosave;
    }
    
    private function saveGroupLogout(array $recordstosave, array $formvalues)
    {
    	$modellogout = new Logout();
    	$modellogout->setLogoutBy($formvalues['logoutbyselect']);
    	$zenddate = new Zend_Date($formvalues['logoutdateinput'], ACORNConstants::$ZEND_DATE_FORMAT);		
    	$logoutdate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	$modellogout->setLogoutDate($logoutdate);
    	if (!empty($formvalues['logoutfromselect']))
    	{
    		$modellogout->setTransferFrom($formvalues['logoutfromselect']);
    	}
    	$modellogout->setTransferTo($formvalues['logouttoselect']);
    	
    	$recordssaved = LogoutDAO::getLogoutDAO()->saveGroupLogouts($recordstosave, $modellogout);
    	return $recordssaved;
    }
    
	public function addtotransferlistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			
			$jsondata = $this->getRequest()->getParam("transferids4");
			$recordids = Zend_Json::decode($jsondata);
			
			$savedrecords = $this->validateLogoutFormAndSave($recordids, $formData);
		    
			//As long as it saved properly, then continue
			if (!is_null($savedrecords))
			{
				//Add the data to the transfer list
				foreach ($savedrecords as $record)
				{
					TransferNamespace::addToTransferList(TransferNamespace::LOGOUT, $record);
				}
				GroupNamespace::setSaveStatus(GroupNamespace::SAVE_STATE_SUCCESS);
		    	$group = GroupNamespace::getCurrentGroup();
		    	$groupnum = $group->getPrimaryKey() . '#tabview=tab4';
			    AcornRedirector::getAcornRedirector()->setGoto('index', 'group', NULL, array('groupnumber' => $groupnum));
		    }
    	}
		
    }
  
}
?>

