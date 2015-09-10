<?php

/**
 * GrouploginController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class GrouploginController extends Zend_Controller_Action
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
		$this->view->placeholder("loginerrormessage")->set($message);
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('groupnumber')->set($groupnumberlabel);
		
		$this->renderScript('group/index.phtml');
    }
    
	public function saveloginAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			$jsondata = $this->getRequest()->getParam("transferids1");
			$recordids = Zend_Json::decode($jsondata);
			
			$savedrecords = $this->validateLoginFormAndSave($recordids, $formData);
			
			if (!is_null($savedrecords))
			{
				$group = GroupNamespace::getCurrentGroup();
				GroupNamespace::setSaveStatus(GroupNamespace::SAVE_STATE_SUCCESS);
					
				//Ensure it goes to the proper tab
				$groupnum = $group->getPrimaryKey() . '#tabview=tab1';
				AcornRedirector::getAcornRedirector()->setGoto('index', 'group', NULL, array('groupnumber' => $groupnum));
			}	
		}
		else
		{
			//If the url was typed directly, go to the DE screen.
			$this->_helper->redirector('newgroup', 'group');
		}
    }
    
    private function validateLoginFormAndSave(array $recordids, $formData)
    {
    	$form = $this->getGroupForm()->getSubForm("grouploginform");
		
    	GroupNamespace::setRenderTab(GroupNamespace::LOGIN);
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
			
			$recordssaved = $this->saveGroupLogin($grouprecordstosave, $form->getValues());
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
    
    private function saveGroupLogin(array $recordstosave, array $formvalues)
    {
    	$modellogin = new Login();
    	$modellogin->setLoginBy($formvalues['loginbyselect']);
    	$zenddate = new Zend_Date($formvalues['logindateinput'], ACORNConstants::$ZEND_DATE_FORMAT);		
    	$logindate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	$modellogin->setLoginDate($logindate);
    	if (!empty($formvalues['loginfromselect']))
    	{
    		$modellogin->setTransferFrom($formvalues['loginfromselect']);
    	}
    	$modellogin->setTransferTo($formvalues['logintoselect']);
    	
    	$recordssaved = LoginDAO::getLoginDAO()->saveGroupLogins($recordstosave, $modellogin);
    	return $recordssaved;
    }
    
	public function addtotransferlistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			
			$jsondata = $this->getRequest()->getParam("transferids1");
			$recordids = Zend_Json::decode($jsondata);
			
			$savedrecords = $this->validateLoginFormAndSave($recordids, $formData);
		    
			//As long as it saved properly, then continue
			if (!is_null($savedrecords))
			{
				//Add the data to the transfer list
				foreach ($savedrecords as $record)
				{
					TransferNamespace::addToTransferList(TransferNamespace::LOGIN, $record);
				}
				GroupNamespace::setSaveStatus(GroupNamespace::SAVE_STATE_SUCCESS);
		    	$group = GroupNamespace::getCurrentGroup();
		    	$groupnum = $group->getPrimaryKey() . '#tabview=tab1';
			    AcornRedirector::getAcornRedirector()->setGoto('index', 'group', NULL, array('groupnumber' => $groupnum));
		    }
    	}
		
    }    
}
?>

