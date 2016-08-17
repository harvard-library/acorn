<?php

/**
 * RecordidentificationController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class RecordidentificationController extends Zend_Controller_Action
{
	private $recordForm;
	private $acornRedirector;
	
	private function getRecordForm()
	{
		if (is_null($this->recordForm))
		{
			$this->recordForm = new RecordForm();
		}
		return $this->recordForm;
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
    
    public function saveidentificationAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getRecordForm()->getSubForm("recordidentificationform");
			$formData = $this->_request->getPost();
			RecordNamespace::setRenderTab(RecordNamespace::IDENTIFICATION);
			
			$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
			//If it is a repository user or a repository admin user and the
			//item is logged in, the only savable items are the repository info items.
			$item = RecordNamespace::getCurrentItem();
			
			//If the item being saved is not the item in the session, 
			//set the correct session item.
			if (!empty($formData['hiddenitemid']) 
					&& $item->getItemID() != $formData['hiddenitemid'])
			{
				$item = ItemDAO::getItemDAO()->getItem($formData['hiddenitemid']);
				RecordNamespace::setCurrentItem($item);
				RecordNamespace::setOriginalItem(ItemDAO::getItemDAO()->getItem($formData['hiddenitemid']));
			}
			
			//If the person is from the repository and the item is logged in, then
			//the only fields they may edit are the expected date of return, repository memo, and insurance fields.
			if ($identity[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_REPOSITORY
				|| ($identity[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN
					&& !is_null($item->getFunctions()->getLogin()->getPrimaryKey())))
			{
				$isvalid = $form->isValidPartial(
					array($formData['expecteddateofreturninput'],
					$formData['repositorymemotextarea'],
    				$formData['insuranceinput']));	
			}
			else 
			{
				//If this is a repository admin user, the repository is disabled
				//so we have to add it dynamically.
				if ($identity[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN)
				{
					$formData['repositoryselect'] = $identity[LocationDAO::LOCATION_ID];
					$formData['chargetoselect'] = $identity[LocationDAO::LOCATION_ID];
				}
 		
				$isvalid = $form->isValid($formData);	
			}
			
			
			if ($isvalid)
			{
				if ($identity[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_REPOSITORY
					|| ($identity[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN
						&& !is_null($item->getFunctions()->getLogin()->getPrimaryKey())
						))
					{
						//Save the data
						$itemid = $this->saveFundInformation($formData);
					}
					else 
					{
						$itemid = $this->saveItemIdentification($form->getValues());
					}
				
				if (!is_null($itemid))
				{
					if (!is_null($item->getItemID()))
					{
						//Clear the duplicate values.
						$item->clearAllDuplicates();
						//Clone the current item and make it the 
						//original item
						$origitem = RecordNamespace::getOriginalItem();
						$origfunctions = $origitem->getFunctions();
						$origitem = clone $item;
						//Since we only saved the identification, keep the
						//original item's functions instead of using the cloned one.
						$origitem->setFunctions($origfunctions);
						RecordNamespace::setOriginalItem($origitem);
					}
					RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);
					//Go back to the record screen.
					$this->_helper->redirector('index', 'record', NULL, array('recordnumber' => $itemid));
				}
				//TODO otherwise display an error message regarding the save
				else 
				{
						$this->displayRecordErrors($formData, ACORNConstants::MESSAGE_PROBLEM_SAVING);
				}
			}
			else
			{
				$this->displayRecordErrors($formData, ACORNConstants::MESSAGE_INVALID_FORM);
			}
		}
		else
		{
			//If the url was typed directly, go to the DE screen.
			$this->_helper->redirector('newitem');
		}
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
    	$item = RecordNamespace::getCurrentItem();
    	$olditem = RecordNamespace::getOriginalItem();
    	
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
     * If the title attempting to be saved exist in another item,
     * the user can 'override' it so that it is still saved.  This method places
     * the title into a variable that is stored in the Item record so that the
     * validator ignores it on the next save.
     */
    public function overridetitleAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    
    	$title = $this->getRequest()->getParam('title');
    	
    	$item = RecordNamespace::getCurrentItem();
    	
    	$exclusionids = !is_null($item->getItemID()) ? array($item->getItemID()) : NULL;
    	$itemid = ItemDAO::getItemDAO()->titleExists($title, $exclusionids);
    	if (!is_null($itemid))
    	{
    		//Add the title to the Item so that the error messages will
    		//be ignored when it is being saved.
    		$item->setDuplicateTitle($title);
    		$this->getRecordForm()->getSubForm('recordidentificationform')->getElement('titleinput')->clearErrorMessages();
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
    	$item = RecordNamespace::getCurrentItem();
    	$item->addCallNumber($callnumber);
    	$item->addDuplicateCallNumber($callnumber);
    	RecordNamespace::setCurrentItem($item); 
    	
    	$data = array('Success' => TRUE);
    	$jsonstring = Zend_Json::encode($data);
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
    	->setBody($jsonstring);
    }
    
    
    private function displayRecordErrors(array $formData, $message)
    {
    	$form = $this->getRecordForm();
		//Populate the data with what the user
		//gave but also display the error messages.
    	$titleerrors = $form->getSubForm('recordidentificationform')->getElement('titleinput')->getMessages();
    	    	
    	$formData['titleerrorhidden'] = FALSE;
    	foreach ($titleerrors as $error)
    	{
    		//If the string is found in any of the messages, add it to the item.
    		if (stripos($error, "exists in Acorn record") !== FALSE)
    		{
    			$formData['titleerrorhidden'] = TRUE;
    			break;
    		}
    	}
    	
    	$form->populate($formData);
		$this->view->form = $form;
				
		$item = RecordNamespace::getCurrentItem();
		$recordnumberlabel = "Record #" . $item->getItemID();
		$recordinfolabel = $item->getInfoForLabel();
		$createdate = "";
		$createdby = "";
		$audittrailitems = $item->getAuditTrail("Insert");
		if (!empty($audittrailitems))
		{
			$audittrailitem = current($audittrailitems);
			$createdate = $audittrailitem->getDate();
			$createdby = $audittrailitem->getPerson()->getDisplayName();
		}
		$this->view->placeholder("identificationerrormessage")->set($message);
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('recordnumber')->set($recordnumberlabel);
		$this->view->placeholder('recordinfo')->set($recordinfolabel);

		$this->renderScript('record/index.phtml');
    }
    
    private function saveItemIdentification(array $formvalues)
    {
    	//Get the current item
    	$item = RecordNamespace::getCurrentItem();
    	$item->setAuthorArtist($formvalues['authorinput']);
    	$item->setCollectionName($formvalues['collectionnameinput']);
    	$item->setComments($formvalues['commentstextarea']);
    	$item->setCoordinatorID($formvalues['coordinatorselect']);
    	$item->setCuratorID($formvalues['curatorselect']);
    	$item->setApprovingCuratorID($formvalues['approvingcuratorselect']);
    	$item->setDateOfObject($formvalues['dateofobjectinput']);
    	$item->setDepartmentID($formvalues['departmentselect']);
    	$item->setFormatID($formvalues['formatselect']);
    	$item->setFundMemo($formvalues['repositorymemotextarea']);
    	$item->setGroupID($formvalues['groupselect']);
    	$item->setHomeLocationID($formvalues['repositoryselect']);
    	$item->setChargeToID($formvalues['chargetoselect']);
    	$item->setExpectedDateOfReturn($formvalues['expecteddateofreturninput']);
    	$item->setInsuranceValue($formvalues['insuranceinput']);
    	$item->setNonCollectionMaterial($formvalues['noncollectioncheckbox']);
    	$item->setProjectID($formvalues['projectselect']);
    	$item->setPurposeID($formvalues['purposeselect']);
    	$item->setStorage($formvalues['storageinput']);
    	$item->setTitle($formvalues['titleinput']);
    	
    	$olditem = RecordNamespace::getOriginalItem();
    	$itemid = ItemDAO::getItemDAO()->saveItemIdentification($item, $olditem);
    	return $itemid;
    }
    
	private function saveFundInformation(array $formvalues)
    {
    	//Get the current item
    	$item = RecordNamespace::getCurrentItem();
    	$item->setFundMemo($formvalues['repositorymemotextarea']);
    	$item->setExpectedDateOfReturn($formvalues['expecteddateofreturninput']);
    	$item->setInsuranceValue($formvalues['insuranceinput']);
    	
    	$itemid = ItemDAO::getItemDAO()->saveFundInformation($item);
    	return $itemid;
    }
}
?>