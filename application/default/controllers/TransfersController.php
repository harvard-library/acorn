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
 * TransfersController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
	

class TransfersController extends Zend_Controller_Action
{
	private $bulkLoginForm;
	private $bulkLogoutForm;
	
	private function getBulkLoginForm()
	{
		if (is_null($this->bulkLoginForm))
		{
			$this->bulkLoginForm = new BulkLoginForm();
		}
		return $this->bulkLoginForm;
	}
	
	private function getBulkLogoutForm()
	{
		if (is_null($this->bulkLogoutForm))
		{
			$this->bulkLogoutForm = new BulkLogoutForm();
		}
		return $this->bulkLogoutForm;
	}
	
	public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
        {
            $this->_helper->redirector('index', 'index');
        }
    }
	
	/**
	 * The default action - show the bulk login
	 */
    public function indexAction() 
    {
        $this->_helper->redirector('bulklogin');
    }
    
    public function bulkloginAction()
    {
    	$form = $this->getBulkLoginForm();
    	$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
    	$zenddate = new Zend_Date();
		$data = array(
    		'logindateinput' => $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT),
    		'loginbyselect' => $identity[PeopleDAO::PERSON_ID]
    	);
    	$form->populate($data);
    	TransferNamespace::clearBulkLocation();
    	$this->view->form = $form;	
    }
    
    public function bulklogoutAction()
    {
    	$form = $this->getBulkLogoutForm();
    	$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
    	$zenddate = new Zend_Date();
		$data = array(
    		'logoutdateinput' => $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT),
    		'logoutbyselect' => $identity[PeopleDAO::PERSON_ID]
    	);
    	$form->populate($data);
    	TransferNamespace::clearBulkLocation();
    	$this->view->form = $form;	
    }
    
	/*
     * Display the errors on the form
     */
	private function displayLoginErrors(array $formData, $message)
    {
    	$form = $this->getBulkLoginForm();
		//Populate the data with what the user
		//gave but also display the error messages.
		$form->populate($formData);
		$this->view->form = $form;

		$this->view->placeholder("errormessage")->set($message);
		
		$this->renderScript('transfers/bulklogin.phtml');
    }
    
	/*
     * Display the errors on the form
     */
	private function displayLogoutErrors(array $formData, $message)
    {
    	$form = $this->getBulkLogoutForm();
		//Populate the data with what the user
		//gave but also display the error messages.
		$form->populate($formData);
		$this->view->form = $form;

		$this->view->placeholder("errormessage")->set($message);
		
		$this->renderScript('transfers/bulklogout.phtml');
    }
    
    public function findlogintransfersAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$list = TransferNamespace::getTransferList(TransferNamespace::LOGIN);
		$transfers = array();
		if (isset($list))
		{
			foreach ($list as $item)
			{
				array_push($transfers, array(ItemDAO::ITEM_ID => $item->getItemID(),
					ItemDAO::TITLE => $item->getTitle()));
			}
		}
		
		//Only use the results
    	$resultset = array('Result' => $transfers);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	public function findlogouttransfersAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$list = TransferNamespace::getTransferList(TransferNamespace::LOGOUT);
		$transfers = array();
		if (isset($list))
		{
			foreach ($list as $item)
			{
				array_push($transfers, array(ItemDAO::ITEM_ID => $item->getItemID(),
					ItemDAO::TITLE => $item->getTitle()));
			}
		}
		
		//Only use the results
    	$resultset = array('Result' => $transfers);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	public function findtemptransfersAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$list = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER);
		$transfers = array();
		if (isset($list))
		{
			foreach ($list as $item)
			{
				array_push($transfers, array(ItemDAO::ITEM_ID => $item->getItemID(),
					ItemDAO::TITLE => $item->getTitle()));
			}
		}
		
		//Only use the results
    	$resultset = array('Result' => $transfers);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	public function findtemptransferreturnsAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$list = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER_RETURN);
		$transfers = array();
		if (isset($list))
		{
			foreach ($list as $item)
			{
				array_push($transfers, array(ItemDAO::ITEM_ID => $item->getItemID(),
					ItemDAO::TITLE => $item->getTitle()));
			}
		}
		
		//Only use the results
    	$resultset = array('Result' => $transfers);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	/**
	 * Finds the list of matching records for the current selected location
	 */
    public function findmatchingloginrecordsAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		Logger::log($this->getRequest()->getParams());
		$selectedloc = $this->getRequest()->getParam('selectedlocation');
		$repository = $this->getRequest()->getParam('repository', '');
		$startIndex = $this->getRequest()->getParam('startIndex', 0);
		$count = $this->getRequest()->getParam('results', 100);
		
		$availablerecords = array();
    	if (isset($selectedloc) && !empty($selectedloc))
		{
			TransferNamespace::setBulkLocation($selectedloc);	
		}
		else 
		{
			$selectedloc = TransferNamespace::getBulkLocation();
		}
		
		$searchparameters = array();
		//Login matches should include:
		//1. Repository (Home Location) matches the selected location.
		$locsearchparam = new SearchParameters();
		$locsearchparam->setColumnName('ItemIdentification.' . ItemDAO::HOME_LOCATION_ID);
		$locsearchparam->setValue($selectedloc);
		$locsearchparam->setBooleanValue('');
		
		//2. OR Repository, if it exists
		if (!empty($repository))
		{
			$locsearchparam->setOpenParenthesis('(');
			array_push($searchparameters, $locsearchparam);
		
			$repositorysearchparam = new SearchParameters();
			$repositorysearchparam->setColumnName('ItemIdentification.' . ItemDAO::HOME_LOCATION_ID);
			$repositorysearchparam->setValue($repository);
			$repositorysearchparam->setBooleanValue('OR');
			$repositorysearchparam->setClosedParenthesis(')');		
			array_push($searchparameters, $repositorysearchparam);
		}
		else
		{
			array_push($searchparameters, $locsearchparam);
		}
		
		
		//3. Pending (not logged in)
		$statussearchparam = new SearchParameters();
		$statussearchparam->setColumnName('ItemStatus');
		$statussearchparam->setValue(ItemDAO::ITEM_STATUS_PENDING);
		array_push($searchparameters, $statussearchparam);
		
			
		$additionalwhere = NULL;

    	$departmentid = TransferNamespace::getTransferDepartmentID(TransferNamespace::LOGIN);
		$list = TransferNamespace::getTransferList(TransferNamespace::LOGIN);
		//If the department is actually null then add it to the where clause
		if (is_null($departmentid) && isset($list) && count($list) > 0)
		{
			$additionalwhere = 'ItemIdentification.DepartmentID IS NULL';
		}
		elseif (!is_null($departmentid)) 
		{
			//3. Item departments match the transfer list department
			$searchparam = new SearchParameters();
			$searchparam->setColumnName("ItemIdentification." . DepartmentDAO::DEPARTMENT_ID);
			$searchparam->setValue($departmentid);
			array_push($searchparameters, $searchparam);
		}
		
		$curatorid = TransferNamespace::getTransferCuratorID(TransferNamespace::LOGIN);
		
		//The curator must exist so if there is anything in the transfer list, it will have a curator.
		if (!is_null($curatorid))
		{
			//4. Item curators match the transfer list curator
			$searchparam = new SearchParameters();
			$searchparam->setColumnName("ItemIdentification." . ItemDAO::CURATOR_ID);
			$searchparam->setValue($curatorid);
			array_push($searchparameters, $searchparam);
		}
		$totalcount = ItemDAO::getItemDAO()->getItemCount($searchparameters, $additionalwhere);
		$records = ItemDAO::getItemDAO()->getItems($searchparameters, $startIndex, $count, $additionalwhere, array('DepartmentName', 'Curator'));
		foreach ($records as $record)
		{
			$disablemessage = $record->getDisableLoginMessage();
			if (empty($disablemessage))
			{
				$callnumbers = $record->getCallNumbers();
				$stringcallnumbers = implode("; ", $callnumbers);
				$counts = $record->getInitialCounts();
				$vol = $counts->getVolumeCount();
				$sheet = $counts->getSheetCount();
				$photo = $counts->getPhotoCount();
				$box = $counts->getBoxCount();
				$other = $counts->getOtherCount();
				
				$repository = LocationDAO::getLocationDAO()->getLocation($record->getHomeLocationID())->getLocation();
				$department = NULL;
				if (!is_null($record->getDepartmentID()))
				{
					$department = DepartmentDAO::getDepartmentDAO()->getDepartment($record->getDepartmentID())->getDepartmentName();
				}
				if (!is_null($department))
				{
					$repository .= '-' . $department;
				}
				
				$curator = NULL;
				if (!is_null($record->getCuratorID()))
				{
					$curator = PeopleDAO::getPeopleDAO()->getPerson($record->getCuratorID())->getDisplayName();
				}
				
				array_push($availablerecords, array(
					ItemDAO::ITEM_ID => $record->getItemID(),
					"CallNumbers" => $stringcallnumbers,
					ItemDAO::TITLE => $record->getTitle(),
					ItemDAO::AUTHOR_ARTIST => $record->getAuthorArtist(),
					ItemDAO::DATE_OF_OBJECT => $record->getDateOfObject(),
					"VolumeCount" => $vol,
					"SheetCount" => $sheet,
					"PhotoCount" => $photo,
					"BoxCount" => $box,
					"OtherCount" => $other,
					ItemDAO::COMMENTS => $record->getComments(),
					"Department" => $repository,
					"Curator" => $curator
				));
			}
		}
		//Only use the results
    	$resultset = array('startIndex' => $startIndex, 'results' => $count, 'totalRecords' => $totalcount, 'Result' => $availablerecords);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	/**
	 * Finds the list of matching records for the current selected location
	 */
    public function findmatchinglogoutrecordsAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$selectedloc = $this->getRequest()->getParam('selectedlocation');
		Logger::log($this->getRequest()->getParams());
		$startIndex = $this->getRequest()->getParam('startIndex', 0);
		$count = $this->getRequest()->getParam('results', 100);
		
		$totalcount = 0;	
		$availablerecords = array();
		if (isset($selectedloc) && !empty($selectedloc))
		{
			TransferNamespace::setBulkLocation($selectedloc);	
		}
		else 
		{
			$selectedloc = TransferNamespace::getBulkLocation();
		}
		
		$searchparameters = array();
		//Logout matches should include:
		//1. Login to location = selected logout from location.
		$locsearchparam = new SearchParameters();
		$locsearchparam->setColumnName("ItemLogin." . LoginDAO::TO_LOCATION_ID);
		$locsearchparam->setValue($selectedloc);
		array_push($searchparameters, $locsearchparam);
		
		//3. Item status is logged in.
		$statsearchparam = new SearchParameters();
		$statsearchparam->setColumnName("ItemStatus");
		$statsearchparam->setValue(ItemDAO::ITEM_STATUS_LOGGED_IN);
		array_push($searchparameters, $statsearchparam);
		
		$additionalwhere = NULL;
		
		$departmentid = TransferNamespace::getTransferDepartmentID(TransferNamespace::LOGOUT);
		$list = TransferNamespace::getTransferList(TransferNamespace::LOGOUT);
		//If the department is actually null then add it to the where clause
		if (is_null($departmentid) && isset($list) && count($list) > 0)
		{
			$additionalwhere = 'ItemIdentification.DepartmentID IS NULL';
		}
		elseif (!is_null($departmentid))
		{
			//3. Item departments match the transfer list department
			$deptsearchparam = new SearchParameters();
			$deptsearchparam->setColumnName("ItemIdentification." . DepartmentDAO::DEPARTMENT_ID);
			$deptsearchparam->setValue($departmentid);
			array_push($searchparameters, $deptsearchparam);
		}
		
		$curatorid = TransferNamespace::getTransferCuratorID(TransferNamespace::LOGOUT);
		//The curator must exist so if there is anything in the transfer list, it will have a curator.
		if (!is_null($curatorid))
		{
			//4. Item curators match the transfer list curator
			$cursearchparam = new SearchParameters();
			$cursearchparam->setColumnName("ItemIdentification." . ItemDAO::CURATOR_ID);
			$cursearchparam->setValue($curatorid);
			array_push($searchparameters, $cursearchparam);
		}
	
		$totalcount = ItemDAO::getItemDAO()->getItemCount($searchparameters, $additionalwhere);
		$records = ItemDAO::getItemDAO()->getItems($searchparameters, $startIndex, $count, $additionalwhere, array('DepartmentName', 'Curator'));
		foreach ($records as $record)
		{
			$disablemessage = $record->getDisableLogoutMessage();
			if (empty($disablemessage))
			{
				$callnumbers = $record->getCallNumbers();
				$stringcallnumbers = implode("; ", $callnumbers);
				$counts = $record->getInitialCounts();
				$vol = $counts->getVolumeCount();
				$sheet = $counts->getSheetCount();
				$photo = $counts->getPhotoCount();
				$box = $counts->getBoxCount();
				$other = $counts->getOtherCount();
				
				$repository = LocationDAO::getLocationDAO()->getLocation($record->getHomeLocationID())->getLocation();
				
				$department = NULL;
				if (!is_null($record->getDepartmentID()))
				{
					$department = DepartmentDAO::getDepartmentDAO()->getDepartment($record->getDepartmentID())->getDepartmentName();
				}
				if (!is_null($department))
				{
					$repository .= '-' . $department;
				}
				
				$curator = NULL;
				if (!is_null($record->getCuratorID()))
				{
					$curator = PeopleDAO::getPeopleDAO()->getPerson($record->getCuratorID())->getDisplayName();
				}
				
				array_push($availablerecords, array(
					ItemDAO::ITEM_ID => $record->getItemID(),
					"CallNumbers" => $stringcallnumbers,
					ItemDAO::TITLE => $record->getTitle(),
					ItemDAO::AUTHOR_ARTIST => $record->getAuthorArtist(),
					ItemDAO::DATE_OF_OBJECT => $record->getDateOfObject(),
					"VolumeCount" => $vol,
					"SheetCount" => $sheet,
					"PhotoCount" => $photo,
					"BoxCount" => $box,
					"OtherCount" => $other,
					ItemDAO::COMMENTS => $record->getComments(),
					"Department" => $repository,
					"Curator" => $curator
				));
			}
		}

		//Only use the results
    	$resultset = array('startIndex' => $startIndex, 'results' => $count, 'totalRecords' => $totalcount, 'Result' => $availablerecords);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
    public function savebulkloginAction()
    {
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			$jsondata = $this->getRequest()->getParam("transferids");
			$recordids = Zend_Json::decode($jsondata);
			
			$savedrecords = $this->validateLoginFormAndSave($recordids, $formData);
			
			if (!is_null($savedrecords))
			{
				TransferNamespace::setSaveStatus(TransferNamespace::SAVE_STATE_SUCCESS);
			}	
		}
		$this->_helper->redirector('bulklogin');
    }
    
	private function validateLoginFormAndSave(array $recordids, $formData)
    {
    	$form = $this->getBulkLoginForm();
		
    	if ($form->isValidPartial($formData))
		{
			$recordstosave = array();
			foreach ($recordids as $recordid)
			{
				$recordstosave[$recordid] = ItemDAO::getItemDAO()->getItem($recordid);
			}
			
			$recordssaved = $this->saveLogin($recordstosave, $form->getValues());
			if ($recordssaved == 0 && count($recordstosave) > 0)
			{
				$this->displayLoginErrors($formData, ACORNConstants::MESSAGE_PROBLEM_SAVING);
				$recordstosave = NULL;
			}
		}
		else
		{
			$this->displayLoginErrors($formData, ACORNConstants::MESSAGE_INVALID_FORM);
			$recordstosave = NULL;
		}
		return $recordstosave;
    }
    
    private function saveLogin(array $recordstosave, array $formvalues)
    {
    	Logger::log($formvalues);
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
    
	public function savebulklogoutAction()
    {
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			$jsondata = $this->getRequest()->getParam("transferids");
			$recordids = Zend_Json::decode($jsondata);
			
			$savedrecords = $this->validateLogoutFormAndSave($recordids, $formData);
			
			if (!is_null($savedrecords))
			{
				TransferNamespace::setSaveStatus(TransferNamespace::SAVE_STATE_SUCCESS);
			}	
		}
		$this->_helper->redirector('bulklogout');
    }
    
	private function validateLogoutFormAndSave(array $recordids, $formData)
    {
    	$form = $this->getBulkLogoutForm();
		
    	if ($form->isValidPartial($formData))
		{
			$recordstosave = array();
			foreach ($recordids as $recordid)
			{
				$recordstosave[$recordid] = ItemDAO::getItemDAO()->getItem($recordid);
			}
			
			$recordssaved = $this->saveLogout($recordstosave, $form->getValues());
			if ($recordssaved == 0 && count($recordstosave) > 0)
			{
				$this->displayLogoutErrors($formData, ACORNConstants::MESSAGE_PROBLEM_SAVING);
				$recordstosave = NULL;
			}
		}
		else
		{
			$this->displayLogoutErrors($formData, ACORNConstants::MESSAGE_INVALID_FORM);
			$recordstosave = NULL;
		}
		return $recordstosave;
    }
    
    private function saveLogout(array $recordstosave, array $formvalues)
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
    
    public function addtologintransferlistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			Logger::log($_POST);
			$jsondata = $this->getRequest()->getParam("transferids");
			$recordids = Zend_Json::decode($jsondata);
			
			$savedrecords = $this->validateLoginFormAndSave($recordids, $formData);
		    
			//As long as it saved properly, then continue
			if (!is_null($savedrecords))
			{
				//Add the data to the transfer list
				foreach ($savedrecords as $record)
				{
					if ($this->isLoginTransferrable($record))
					{
						TransferNamespace::addToTransferList(TransferNamespace::LOGIN, $record);
					}
				}
				TransferNamespace::setSaveStatus(TransferNamespace::SAVE_STATE_SUCCESS);
				$this->_helper->redirector('bulklogin');
		    }
    	}
		
    }  

	private function isLoginTransferrable(Item $item)
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

    public function addtologouttransferlistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			
			$jsondata = $this->getRequest()->getParam("transferids");
			$recordids = Zend_Json::decode($jsondata);
			
			$savedrecords = $this->validateLogoutFormAndSave($recordids, $formData);
		    
			//As long as it saved properly, then continue
			if (!is_null($savedrecords))
			{
				//Add the data to the transfer list
				foreach ($savedrecords as $record)
				{
					if ($this->isLogoutTransferrable($record))
					{
						TransferNamespace::addToTransferList(TransferNamespace::LOGOUT, $record);
					}
				}
				TransferNamespace::setSaveStatus(TransferNamespace::SAVE_STATE_SUCCESS);
				$this->_helper->redirector('bulklogout');
		    }
    	}
		
    }

	private function isLogoutTransferrable(Item $item)
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

	public function findcurrenttransferinfoAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$listname = $this->getRequest()->getParam('listname');
		$from = TransferNamespace::getTransferFrom($listname);
		$to = TransferNamespace::getTransferTo($listname);
		$deptid = TransferNamespace::getTransferDepartmentID($listname);
		$curid = TransferNamespace::getTransferCuratorID($listname);
		
		if (!empty($from))
		{
			$fromloc = LocationDAO::getLocationDAO()->getLocation($from);
			$fromtext = $fromloc->getLocation();
			$toloc = LocationDAO::getLocationDAO()->getLocation($to);
			$totext = $toloc->getLocation();
			$depttext = '';
			$curtext = PeopleDAO::getPeopleDAO()->getPerson($curid)->getDisplayName();
			if (!is_null($deptid))
			{
				$depttext = DepartmentDAO::getDepartmentDAO()->getDepartment($deptid)->getDepartmentName();
			}
			
		}
		else 
		{
			$from = "";
			$to = "";
			$fromtext = "";
			$totext = "";
			$depttext = '';
			$curtext = '';
		}

		$result = array('FromLocationID' => $from, 
			'ToLocationID' => $to, 
			'FromLocation' => $fromtext, 
			'ToLocation' => $totext, 
			'DepartmentID' => $deptid,
			'Department' => $depttext, 
			'CuratorID' => $curid,
			'Curator' => $curtext);
		
		$retval = Zend_Json::encode($result);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
}
?>