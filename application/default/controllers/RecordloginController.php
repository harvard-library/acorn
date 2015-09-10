<?php

/**
 * RecordloginController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class RecordloginController extends Zend_Controller_Action
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
		$this->view->placeholder("loginerrormessage")->set($message);
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('recordnumber')->set($recordnumberlabel);
		$this->view->placeholder('recordinfo')->set($recordinfolabel);

		$this->renderScript('record/index.phtml');
    }
         
    public function saveloginAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
	if ($this->_request->isPost())
	{
            $formData = $this->_request->getPost();
			
            $item = RecordNamespace::getCurrentItem();
            $login = $item->getFunctions()->getLogin();
			
            //If the login being saved is not the item in the session, 
            //set the correct session item.
            if (!empty($formData['hiddenloginitemid']) 
		&& isset($login) 
		&& $item->getItemID() != $formData['hiddenloginitemid'])
            {
                $item = ItemDAO::getItemDAO()->getItem($formData['hiddenloginitemid']);
                
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
			
            $loginid = $this->validateLoginFormAndSave($formData);
			
            if (!is_null($loginid))
            {
                $item = RecordNamespace::getCurrentItem();
		RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);
                                          	
		//Ensure it goes to the proper tab
		$itemID = $item->getItemID();
		$recnum = $itemID . '#tabview=tab1';
                                
                // Email from here, TME
		$item->emailCurator($itemID, 'login');
                
		$this->getAcornRedirector()->setGoto('index', 'record', NULL, array('recordnumber' => $recnum));
            }	
	}
	else
	{
            //If the url was typed directly, go to the DE screen.
            $this->_helper->redirector('newitem');
	}
    }
        
    private function validateLoginFormAndSave($formData)
    {
    	$form = $this->getRecordForm()->getSubForm("recordloginform");
		$loginid = NULL;
		
    	RecordNamespace::setRenderTab(RecordNamespace::LOGIN);
		if ($form->isValid($formData))
		{
			//Save the data
			$loginid = $this->saveItemLogin($form->getValues());
			if (!is_null($loginid))
			{
				$item = RecordNamespace::getCurrentItem();
				$item->getFunctions()->getLogin()->setPrimaryKey($loginid);
				RecordNamespace::setCurrentItem($item);
				
				//Clone the current item login and make it the 
				//original item
				$origitem = RecordNamespace::getOriginalItem();
				$clonedlogin = clone $item->getFunctions()->getLogin();
				//Since we only saved the login, we are only
				//updating that piece of information in the original item
				$origitem->getFunctions()->setLogin($clonedlogin);
				RecordNamespace::setOriginalItem($origitem);
				
			}
			//display an error message regarding the save
			else 
			{
				
				$this->displayRecordErrors($formData, 'There was a problem saving the login.  Please try again.');
			}
		}
		else
		{
			$this->displayRecordErrors($formData, 'Changes were not saved.  Please correct the errors in the form below.');
		}
		return $loginid;
    }
    
    private function saveItemLogin(array $formvalues)
    {
    	//Get the current item
    	$item = RecordNamespace::getCurrentItem();
    	
    	$login = $item->getFunctions()->getLogin();
    	if (is_null($login))
    	{
    		$login = new Login();
    	}
    	$login->setLoginBy($formvalues['loginbyselect']);
    	$zenddate = new Zend_Date($formvalues['logindateinput'], ACORNConstants::$ZEND_DATE_FORMAT);		
    	$logindate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	$login->setLoginDate($logindate);
    	if (!empty($formvalues['loginfromselect']))
    	{
    		$login->setTransferFrom($formvalues['loginfromselect']);
    	}
    	$login->setTransferTo($formvalues['logintoselect']);
    	$item->getFunctions()->setLogin($login);
    	
    	$loginid = LoginDAO::getLoginDAO()->saveItemLogin($item);
    	return $loginid;
    }
    
	public function addtotransferlistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			$resultid = $this->validateLoginFormAndSave($formData);
		    $transferonly = FALSE;
			//If the date isn't set, we are only performing a transfer list add
		    //and not updating the login information
		    if (!isset($formData['logindateinput']))
		    {
		    	$transferonly = TRUE;
		    }
	
			//As long as it saved properly, then continue
			if (!is_null($resultid) || $transferonly)
			{
				$item = RecordNamespace::getCurrentItem();
		    	if (isset($item) && $this->isTransferrable($item))
				{
					TransferNamespace::addToTransferList(TransferNamespace::LOGIN, $item);
			    	//Ensure it goes to the proper tab
					$recnum = $item->getItemID() . '#tabview=tab1';
				    $this->getAcornRedirector()->setGoto('index', 'record', NULL, array('recordnumber' => $recnum));
				}
				else 
				{
					$this->displayRecordErrors($formData, 'The login data, curator, and department must match the login transfer list data.');
				}
		    }
    	}
		
    }
    
	private function isTransferrable(Item $item)
    {
    	$transferlist = TransferNamespace::getTransferList(TransferNamespace::LOGIN);
    	//If it exists, get the login information from one item in the transfer list
    	if (isset($transferlist) && count($transferlist) > 0)
    	{
    		$compareitem = current($transferlist);
    		$itemlogin = $item->getFunctions()->getLogin();
    		$equal = $itemlogin->equals($compareitem->getFunctions()->getLogin());
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
    
	public function deleteloginAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$item = RecordNamespace::getCurrentItem();
    	if (isset($item) && !is_null($item->getFunctions()->getLogin()))
    	{
    		LoginDAO::getLoginDAO()->deleteLogin($item->getFunctions()->getLogin());
    	}
    	$recnum = $item->getItemID() . '#tabview=tab1';
		$this->getAcornRedirector()->setGoto('index', 'record', NULL, array('recordnumber' => $recnum));
    }
    
}
?>

