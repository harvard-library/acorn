<?php

/**
 * RecordlogoutController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class RecordlogoutController extends Zend_Controller_Action
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

    
    private function displayRecordErrors(array $formData, $message)
    {
    	$form = $this->getRecordForm();
		//Populate the data with what the user
		//gave but also display the error messages.
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
		$this->view->placeholder("logouterrormessage")->set($message);
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('recordnumber')->set($recordnumberlabel);
		$this->view->placeholder('recordinfo')->set($recordinfolabel);

		$this->renderScript('record/index.phtml');
    }
    
    public function savelogoutAction()
    {
        $this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
	if ($this->_request->isPost())
	{
            $formData = $this->_request->getPost();
			
            $item = RecordNamespace::getCurrentItem();
            $logout = $item->getFunctions()->getLogout();
            
            //If the logout being saved is not the item in the session, 
            //set the correct session item.
            if (!empty($formData['hiddenlogoutitemid']) 
		&& isset($logout) 
		&& $item->getItemID() != $formData['hiddenlogoutitemid'])
            {
                $item = ItemDAO::getItemDAO()->getItem($formData['hiddenlogoutitemid']);
		
                if (is_null($item))
		{
                    RecordNamespace::setCurrentItem(new Item());
                    RecordNamespace::setOriginalItem(new Item());
		}
		else 
		{
                    RecordNamespace::setCurrentItem($item);
                    RecordNamespace::setOriginalItem(ItemDAO::getItemDAO()->getItem($item->getItemID()));
		}
            }
			
            $logoutid = $this->validateLogoutFormAndSave($formData);
	
            if (!is_null($logoutid))
            {
                $item = RecordNamespace::getCurrentItem();
                RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);
					
		//Ensure it goes to the proper tab
		$itemID = $item->getItemID();
		$recnum = $itemID . '#tabview=tab4';
                                
                // Email from here, TME
		$item->emailCurator($itemID, 'logout');
                                
		$this->getAcornRedirector()->setGoto('index', 'record', NULL, array('recordnumber' => $recnum));
            }
        }
	else
	{
            //If the url was typed directly, go to the DE screen.
            $this->_helper->redirector('newitem');
	}
    }
    
	private function validateLogoutFormAndSave($formData)
    {
    	$form = $this->getRecordForm()->getSubForm("recordlogoutform");
		$logoutid = NULL;
		
    	RecordNamespace::setRenderTab(RecordNamespace::LOGOUT);
		if ($form->isValid($formData))
		{
			//Save the data
			$logoutid = $this->saveItemLogout($form->getValues());
			if (!is_null($logoutid))
			{
				$item = RecordNamespace::getCurrentItem();
				$item->getFunctions()->getLogout()->setPrimaryKey($logoutid);
				RecordNamespace::setCurrentItem($item);
				
				//Clone the current item login and make it the 
				//original item
				$origitem = RecordNamespace::getOriginalItem();
				$clonedlogout = clone $item->getFunctions()->getLogout();
				//Since we only saved the login, we are only
				//updating that piece of information in the original item
				$origitem->getFunctions()->setLogout($clonedlogout);
				RecordNamespace::setOriginalItem($origitem);
				
			}
			//display an error message regarding the save
			else 
			{
				
				$this->displayRecordErrors($formData, 'There was a problem saving the logout.  Please try again.');
			}
		}
		else
		{
			$this->displayRecordErrors($formData, 'Changes were not saved.  Please correct the errors in the form below.');
		}
		return $logoutid;
    }
    
	private function saveItemLogout(array $formvalues)
    {
    	//Get the current item
    	$item = RecordNamespace::getCurrentItem();
    	
    	$logout = $item->getFunctions()->getLogout();
    	if (is_null($logout))
    	{
    		$logout = new Logout();
    	}
    	
    	$logout->setLogoutBy($formvalues['logoutbyselect']);
    	$zenddate = new Zend_Date($formvalues['logoutdateinput'], ACORNConstants::$ZEND_DATE_FORMAT);		
    	$logoutdate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	$logout->setLogoutDate($logoutdate);
    	if (!empty($formvalues['logoutfromselect']))
    	{
    		$logout->setTransferFrom($formvalues['logoutfromselect']);
    	}
    	$logout->setTransferTo($formvalues['logouttoselect']);
    	$item->getFunctions()->setLogout($logout);
    	
    	$logoutid = LogoutDAO::getLogoutDAO()->saveItemLogout($item);
    	return $logoutid;
    }
    
	public function addtotransferlistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			$resultid = $this->validateLogoutFormAndSave($formData);
			$transferonly = FALSE;
			//If the date isn't set, we are only performing a transfer list add
		    //and not updating the logout information
		    if (!isset($formData['logoutdateinput']))
		    {
		    	$transferonly = TRUE;
		    }
	
			//As long as it saved properly, then continue
			if (!is_null($resultid) || $transferonly)
			{
		    	$item = RecordNamespace::getCurrentItem();
		    	if (isset($item) && $this->isTransferrable($item))
				{
					TransferNamespace::addToTransferList(TransferNamespace::LOGOUT, $item);
			    	//Ensure it goes to the proper tab
					$recnum = $item->getItemID() . '#tabview=tab4';
				    $this->getAcornRedirector()->setGoto('index', 'record', NULL, array('recordnumber' => $recnum));
				}
				else 
				{
					$this->displayRecordErrors($formData, 'The logout data, curator, and department must match the logout transfer list data.');
				}
		    }
    	}
		
    }
    

	private function isTransferrable(Item $item)
    {
    	$transferlist = TransferNamespace::getTransferList(TransferNamespace::LOGOUT);
    	//If it exists, get the logout information from one item in the transfer list
    	if (isset($transferlist) && count($transferlist) > 0)
    	{
    		$compareitem = current($transferlist);
    		$itemlogout = $item->getFunctions()->getLogout();
    		$equal = $itemlogout->equals($compareitem->getFunctions()->getLogout());
    		//Also compare the curator and department
    		if ($equal)
    		{
    			$equal = $item->getCuratorID() == $compareitem->getCuratorID();
    			$equal = $equal && $item->getDepartmentID() == $compareitem->getDepartmentID();
    		}
    		return $equal;
    	}
    	return TRUE;
    }
    
	public function deletelogoutAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$item = RecordNamespace::getCurrentItem();
    	if (isset($item) && !is_null($item->getFunctions()->getLogout()))
    	{
    		LogoutDAO::getLogoutDAO()->deleteLogout($item->getFunctions()->getLogout());
    	}
    	$recnum = $item->getItemID() . '#tabview=tab1';
		$this->getAcornRedirector()->setGoto('index', 'record', NULL, array('recordnumber' => $recnum));
    }
    
}
?>