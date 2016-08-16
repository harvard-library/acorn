<?php

/**
 * SearchController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class SearchController extends Zend_Controller_Action
{
	private $searchForm;
	private $groupSearchForm;
	
	private function getSearchForm()
	{
		if (is_null($this->searchForm))
		{
			$this->searchForm = new SearchForm();
		}
		return $this->searchForm;
	}
	
	private function getGroupSearchForm()
	{
		if (is_null($this->groupSearchForm))
		{
			$this->groupSearchForm = new GroupSearchForm();
		}
		return $this->groupSearchForm;
	}
	
	public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
        {
            $this->_helper->redirector('index', 'index');
        }
    }
	
	/**
	 * The default action - show the home page
	 */
    public function indexAction() 
    {
    	$this->_helper->redirector('searchrecord');
    }
    
    public function searchrecordAction()
    {
    	$form = $this->getSearchForm();
    	$this->view->form = $form;
    }
    
 	public function searchgroupAction()
    {
    	$form = $this->getGroupSearchForm();
    	$this->view->form = $form;
    }
    
    public function searchresultsAction()
    {
    	$search = SearchNamespace::getCurrentSearch();
    	
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	//Repository users can't search outside of their repository so change/add any repository entries
    	if ($identity[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_REPOSITORY
    		|| $identity[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN)
    		{
    			$locname = '';
    			if (!empty($identity[LocationDAO::LOCATION_ID]))
    			{
    				$locname = LocationDAO::getLocationDAO()->getLocation($identity[LocationDAO::LOCATION_ID])->getLocation();
    			}
    			$searchparameters = $search->getSearchParameters();
    			$modifiedsearchparameters = array();
    			foreach ($searchparameters as $param)
    			{
    				//Put anything but repository entries into the array
    				if ($param->getColumnName() != "ItemIdentification.HomeLocationID")
    				{
    					array_push($modifiedsearchparameters, $param);
    				}
    			}
    			//Force the user's repository
    			$repositoryparam = new SearchParameters();
    			$repositoryparam->setBooleanValue('AND');
    			$repositoryparam->setColumnName('ItemIdentification.HomeLocationID');
    			$repositoryparam->setColumnText('Repository');
    			$repositoryparam->setSearchType(SearchParameters::SEARCH_TYPE_EQUALS);
    			$repositoryparam->setValue($identity[LocationDAO::LOCATION_ID]);
    			$repositoryparam->setValueName($locname);
    			array_push($modifiedsearchparameters, $repositoryparam);
    			
    			//Update the namespace item
    			$search->setSearchParameters($modifiedsearchparameters);
    			SearchNamespace::setCurrentSearch($search);
    		}
    	   	
    	$searchname = '';
    	$searchinfo = '';
    	if (isset($search))
    	{
    		$searchname = $search->getSearchName();
    		$searchinfo = $search->getSearchInfo();
    	}
    	$this->view->placeholder("searchresultsname")->set($searchname);
		$this->view->placeholder("searchinfo")->set($searchinfo);		
    }
    
    public function findsearchresultsAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		
		$sort = $this->getRequest()->getParam('sort', 'RecordID');
		$sortdir = $this->getRequest()->getParam('dir', 'asc');
		$startIndex = $this->getRequest()->getParam('startIndex', 0);
		$count = $this->getRequest()->getParam('results', 50);
		$availablerecords = array();
		$totalcount = 0;
		
		$search = SearchNamespace::getCurrentSearch();
    	if (isset($search))
    	{
    		$orderby = array($sort => $sort . ' ' . $sortdir);
    		//Update the order by in the namespace
    		$search->setOrderBy($orderby);
    		SearchNamespace::setCurrentSearch($search);
    		
    		$tempsearchparams = $search->getSearchParameters();
    		$searchparams = array();
    		$additionalwhere = NULL;
    		//Extract the tub into the additional where if it exists
    		foreach ($tempsearchparams as $param)
    		{
    			if ($param->getColumnName() != 'Locations.TUB')
    			{
    				array_push($searchparams, $param);
    			}
    			else
    			{
    				$additionalwhere = 'HomeLocationID IN (SELECT LocationID FROM Locations WHERE TUB=\'' . $param->getValue() . '\')';
    			}
    		}
    		$totalcount = CombinedRecordsDAO::getCombinedRecordsDAO()->getRecordCount($searchparams, $additionalwhere);
    		$records = CombinedRecordsDAO::getCombinedRecordsDAO()->getRecords($searchparams, $startIndex, $count, $additionalwhere, $orderby);
    		foreach ($records as $record)
			{
				array_push($availablerecords, $this->buildSearchResultArray($record));
			}
    	}
    	//Only use the results
    	$resultset = array('startIndex' => $startIndex, 'results' => $count, 'totalRecords' => $totalcount, 'Result' => $availablerecords, 'sort' => $sort, 'sortdir' => $sortdir);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
    private function buildSearchResultArray(Record $record)
    {
    	$results = array();
    	$project = NULL;
		if (!is_null($record->getProjectID()))
		{
			$project = ProjectDAO::getProjectDAO()->getProject($record->getProjectID())->getProjectName();
		}
		$results[ProjectDAO::PROJECT_NAME] = $project;
    	
		$group = NULL;
		if (!is_null($record->getGroupID()))
		{
			$group = GroupDAO::getGroupDAO()->getGroup($record->getGroupID())->getGroupName();
		}
		$results[GroupDAO::GROUP_NAME] = $group;
    	
		$repository = NULL;
		$tub = NULL;
		if (!is_null($record->getHomeLocationID()))
		{
			$location = LocationDAO::getLocationDAO()->getLocation($record->getHomeLocationID());
			$repository = $location->getLocation();
			$tub = $location->getTUB();
		}
		$results['Repository'] = $repository;
    	$results['TUB'] = $tub;
    	
		$department = NULL;
		if (!is_null($record->getDepartmentID()))
		{
			$department = DepartmentDAO::getDepartmentDAO()->getDepartment($record->getDepartmentID())->getDepartmentName();
		}
		$results[DepartmentDAO::DEPARTMENT_NAME] = $department;
    	
		$chargeto = NULL;
		if (!is_null($record->getChargeToID()))
		{
			$chargeto = $record->getChargeToName();
		}
		$results[ItemDAO::CHARGE_TO_ID] = $chargeto;
		 
		$format = NULL;
		if (!is_null($record->getFormatID()))
		{
			$format = FormatDAO::getFormatDAO()->getFormat($record->getFormatID())->getFormat();
		}
		$results[FormatDAO::FORMAT] = $format;
    	
		$purpose = NULL;
		if (!is_null($record->getPurposeID()))
		{
			$purpose = PurposeDAO::getPurposeDAO()->getPurpose($record->getPurposeID())->getPurpose();
		}
		$results[PurposeDAO::PURPOSE] = $purpose;
    	
		$results[ItemDAO::TITLE] = $record->getTitle();
    	$results[ItemDAO::COMMENTS] = $record->getComments();
    	$results["Activity"] = $record->getActivityStatus();
    	$results['Type'] = $record->getRecordType();
    	
    	$actualhours = '';
    	$reportby = NULL;
    	$workdoneby = NULL;
    	if (!is_null($record->getFunctions()->getReport()->getPrimaryKey()))
    	{
    		$actualhours = $record->getFunctions()->getReport()->getTotalHours();
    		if (!is_null($record->getFunctions()->getReport()->getReportBy()))
			{
				$reportby = PeopleDAO::getPeopleDAO()->getPerson($record->getFunctions()->getReport()->getReportBy())->getDisplayName();
			}
			$conservators = $record->getFunctions()->getReport()->getReportConservators();
			$workdonebyarray = array();
			foreach($conservators as $c)
			{
				array_push($workdonebyarray, $c->getConservator()->getDisplayName());
			}
			$workdoneby = implode('; ', $workdonebyarray);
			
			$workloc = LocationDAO::getLocationDAO()->getLocation($record->getFunctions()->getReport()->getWorkLocation())->getLocation();
    		$results['WorkLocation'] = $workloc;
    		
    		$results[ReportDAO::SUMMARY] = $record->getFunctions()->getReport()->getSummary();
    	} 
    	$results['ReportHours'] = $actualhours;
    	$results['ReportBy'] = $reportby;
    	$results['WorkDoneBy'] = $workdoneby;
    	
    	$reportDate = NULL;
    	if (!is_null($record->getFunctions()->getReport()->getReportDate()))
    	{
	    	$zenddate = new Zend_Date($record->getFunctions()->getReport()->getReportDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    		$reportDate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    	}
    	$results[ReportDAO::REPORT_DATE] = $reportDate;
    	 
    	if ($record instanceof CombinedRecord)
    	{
    		$results['RecordID'] = $record->getRecordID();
    	}
    	elseif ($record instanceof Item)
    	{
    		$results['RecordID'] = $record->getItemID();
    	}
    	elseif ($record instanceof OnSiteWork)
    	{
    		$results['RecordID'] = $record->getOSWID();
    	}
    	
    	$results['WorkType'] = NULL;
    	$results['OSWStatus'] = NULL;
    	$results['WorkStartDate'] = NULL;
    	$results['WorkEndDate'] = NULL;
    	
    	//Item specific
    	if ($record instanceof Item || $record instanceof CombinedRecord)
    	{
    		$callnumbers = $record->getCallNumbers();
    		$stringcallnumbers = implode("; ", $callnumbers);
			$results['CallNumbers'] = $stringcallnumbers;
    		$importances = $record->getFunctions()->getReport()->getImportances();
    		$imparray = array();
    		foreach ($importances as $importance)
    		{
    			array_push($imparray, $importance->getImportance());
    		}
    		$stringimportances = implode("; ", $imparray);
			$results['Importances'] = $stringimportances;
    		$results[ItemDAO::AUTHOR_ARTIST] = $record->getAuthorArtist();
    		$results[ItemDAO::DATE_OF_OBJECT] = $record->getDateOfObject();
    		
    		$coordinator = NULL;
			if (!is_null($record->getCoordinatorID()))
			{
				$coordinator = PeopleDAO::getPeopleDAO()->getPerson($record->getCoordinatorID())->getDisplayName();
			}
			$results['Coordinator'] = $coordinator;
			
			$curator = NULL;
			if (!is_null($record->getCuratorID()))
			{
				$curator = PeopleDAO::getPeopleDAO()->getPerson($record->getCuratorID())->getDisplayName();
			}
			$results['Curator'] = $curator;
			
			$workassignedto = NULL;
			$workassignedtolist = $record->getWorkAssignedTo();
    		if (!empty($workassignedtolist))
			{
				$workassignedtoarray = array();
				foreach($workassignedtolist as $w)
				{
					array_push($workassignedtoarray, $w->getDisplayName());
				}
				$workassignedto = implode('; ', $workassignedtoarray);
			}
	    	$results['WorkAssignedTo'] = $workassignedto;
			
			$results[LoginDAO::LOGIN_DATE] = $record->getFunctions()->getLogin()->getLoginDate();
	    	$results['Status'] = $record->getItemStatus();	
	    	$results['Storage'] = $record->getStorage();
	    	$results['CollectionName'] = $record->getCollectionName();
	 		$results[ItemDAO::INSURANCE_VALUE] = $record->getInsuranceValue();
	 		$report =$record->getFunctions()->getReport();
	 		$counts = 0;
	 		$volcounts = 0;
	 		$sheetcounts = 0;
	 		$photocounts = 0;
	 		$boxcounts = 0;
	 		$housingcounts = 0;
	 		if (is_null($report->getPrimaryKey()))
	 		{
	 			$counts = $record->getInitialCounts()->getTotalItemCount();
	 			$volcounts = $record->getInitialCounts()->getVolumeCount();
	 			$sheetcounts = $record->getInitialCounts()->getSheetCount();
	 			$photocounts = $record->getInitialCounts()->getPhotoCount();
	 			$boxcounts = $record->getInitialCounts()->getBoxCount();
	 			$housingcounts = $record->getInitialCounts()->getHousingCount();
	 		}
	 		else
	 		{
	 			$counts = $report->getCounts()->getTotalItemCount();
	 			$volcounts = $report->getCounts()->getVolumeCount();
	 			$sheetcounts = $report->getCounts()->getSheetCount();
	 			$photocounts = $report->getCounts()->getPhotoCount();
	 			$boxcounts = $report->getCounts()->getBoxCount();
	 			$housingcounts = $report->getCounts()->getHousingCount();
	 		}
	    	$results['ItemCount'] = $counts;
    		$results['VolumeCount'] = $volcounts;
    		$results['SheetCount'] = $sheetcounts;
    		$results['PhotoCount'] = $photocounts;
    		$results['BoxCount'] = $boxcounts;
    		$results['HousingCount'] = $housingcounts;
    		$results['DateOfObject'] = $record->getDateOfObject();
    		
    		$proposalhours = '';
    		$proposedby = NULL;
    		if (!is_null($record->getFunctions()->getProposal()->getPrimaryKey()))
    		{
    			$proposalhours = $record->getFunctions()->getProposal()->getHoursAsString();
    			if (!is_null($record->getFunctions()->getProposal()->getProposedBy()))
				{
					$people = $record->getFunctions()->getProposal()->getProposedBy();
					$proposedbyarray = array();
					foreach($people as $p)
					{
						array_push($proposedbyarray, $p->getDisplayName());
					}
					$proposedby = implode('; ', $proposedbyarray);
				}
    		}
    		
    		
    		$results['ProposedHours'] = $proposalhours;
    		$results['ProposedBy'] = $proposedby;
    		$proposalDate = NULL;
    		if (!is_null($record->getFunctions()->getProposal()->getProposalDate()))
    		{	 
    			$zenddate = new Zend_Date($record->getFunctions()->getProposal()->getProposalDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    			$proposalDate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    		}
    		$results[ProposalDAO::PROPOSAL_DATE] = $proposalDate;
    		
    		$temploc = '';
    		if (!is_null($record->getFunctions()->getTemporaryTransfer()->getPrimaryKey()))
    		{
    			$temploc = LocationDAO::getLocationDAO()->getLocation($record->getFunctions()->getTemporaryTransfer()->getTransferTo())->getLocation();
    		}
    		$results['TemporaryToLocation'] = $temploc;
    		$tempdate = NULL;
    		if (!is_null($record->getFunctions()->getTemporaryTransfer()->getTransferDate()))
    		{
	    		$zenddate = new Zend_Date($record->getFunctions()->getTemporaryTransfer()->getTransferDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
	    		$tempdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    		}
    		$results['TemporaryDate'] = $tempdate;
    		
    		$results[LogoutDAO::LOGOUT_DATE] = $record->getFunctions()->getLogout()->getLogoutDate();
    		
    		if ($record->getRecordType() == 'OSW')
    		{
    			$worktypes = WorkTypeDAO::getWorkTypeDAO()->getWorkTypes($record->getRecordID());
    			$worktypestringarray = array();
    			foreach ($worktypes as $wt)
    			{
    				array_push($worktypestringarray, $wt->getWorkType());
    			}
    			$results['WorkType'] = implode(',', $worktypestringarray);
    			$results['OSWStatus'] = OnSiteWorkDAO::getOnSiteWorkDAO()->getOSWStatus($record->getRecordID());
    			$osw = OnSiteWorkDAO::getOnSiteWorkDAO()->getOSW($record->getRecordID());
    			$daterange = $osw->getDateRange();
    			if (!is_null($daterange))
    			{
    				$results['WorkStartDate'] = $daterange->getStartDate();
    				$results['WorkEndDate'] = $daterange->getEndDate();
    			}
    		}
    	}
    	else
    	{
    		$results['CallNumbers'] = NULL;
    		$results['Importances'] = NULL;
    		$results[ItemDAO::AUTHOR_ARTIST] = NULL;
    		$results[ItemDAO::DATE_OF_OBJECT] = NULL;
    		$results['Coordinator'] = NULL;
			$results['WorkAssignedTo'] = NULL;
			$results[LoginDAO::LOGIN_DATE] = NULL;
	    	$results['Status'] = NULL;	
	    	$results['Storage'] = NULL;
	    	$results['CollectionName'] = NULL;
	    	$results['ItemCount'] = NULL;
    		$results['Volumeount'] = NULL;
    		$results['SheetCount'] = NULL;
    		$results['Photoount'] = NULL;
    		$results['HousingCount'] = NULL;
    		$results['DateOfObject'] = NULL;
    		$results['ProposedHours'] = NULL;
    		$results[ProposalDAO::PROPOSAL_DATE] = NULL;
    		$results['TemporaryToLocation'] = NULL;
    		$results['TemporaryDate'] = NULL;
    		$results[LogoutDAO::LOGOUT_DATE] = NULL;
    		
    		$results['ProposedBy'] = NULL;
    	}
    	return $results;
    }
    
    public function fallbackAction()
    {
    }
    
    public function savedsearchesAction()
    {
    	//No form since it is just a Javascript data list and button.
    }
    
    public function findrecordAction()
    {
   	$this->_helper->viewRenderer->setNoRender();
    	
    	$formData = $this->getRequest()->getParams();
    	$form = $this->getSearchForm();
    	$form->setSearchType(SearchForm::RECORD_SEARCH);
    	
    	if ($form->isValidPartial(array($formData['recordnumberinput'])))
    	{
            $itemid = $formData['recordnumberinput'];
            $this->_helper->redirector('index', 'record', NULL, array('recordnumber' => $itemid));
        }
    	else
	{
            //Populate the data with what the user
            //gave but also display the error messages.
            $form->populate($formData);
            $this->view->form = $form;
            $this->render('searchrecord');
	}	
    }
    
    public function findoswAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$formData = $this->getRequest()->getParams();
    	$form = $this->getSearchForm();
    	$form->setSearchType(SearchForm::OSW_SEARCH);
    	
    	if ($form->isValidPartial(array($formData['recordnumberinput'])))
    	{
    		$itemid = $formData['recordnumberinput'];
    		$this->_helper->redirector('osw', 'record', NULL, array('recordnumber' => $itemid));
    	}
    	else
		{
			//Populate the data with what the user
			//gave but also display the error messages.
			$form->populate($formData);
			$this->view->form = $form;
			$this->render('searchrecord');
		}	
    }
    
	public function findgroupAction()
    {
   		$this->_helper->viewRenderer->setNoRender();
    	
    	$formData = $this->getRequest()->getParams();
    	
    	$form = $this->getGroupSearchForm();
    	
    	if ($form->isValid($formData))
    	{
    		$groupid = $formData['groupselect'];
    		$this->_helper->redirector('index', 'group', NULL, array('groupnumber' => $groupid));
    	}
    	else
		{
			//Populate the data with what the user
			//gave but also display the error messages.
			$form->populate($formData);
			$this->view->form = $form;
			$this->render('searchgroup');
		}	
    }
    
    public function populatesavedsearchesAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$includeblank = $this->getRequest()->getParam('includeblank', 0);
		$includeall = $this->getRequest()->getParam('includeall', 0);
		$includeglobal = $this->getRequest()->getParam('includeglobal', 1);
		
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		
		$searches = SearchDAO::getSearchDAO()->getAllSearchesForSelect($identity[PeopleDAO::PERSON_ID], $includeall, $includeglobal);
		
		if ($includeblank == 1)
		{
			array_unshift($searches, array('SearchID' => 0, 'SearchName' => ''));
		}
		
		$retval = Zend_Json::encode(array('Result' => $searches));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	/*
     * Removes a search.
     */
	public function removesearchAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$searchID = $this->getRequest()->getParam('searchID');
    	
    	$rowsdeleted = SearchDAO::getSearchDAO()->deleteSearch($searchID);
    		
   		//If rows deleted = 0, return an error
   		if ($rowsdeleted == 0)
   		{
   			$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem deleting the search.  Please try again.'));
		}
		else 
		{
			$retval = Zend_Json::encode(array());
		}
			
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    }
    
    public function updatesavedsearchAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$column = $this->getRequest()->getParam('column');
    	$pk = $this->getRequest()->getParam('pk');
    	$newdata = $this->getRequest()->getParam('newdata');

   		$search = SearchDAO::getSearchDAO()->getSavedSearchFromID($pk);
   		$search->updateField($column, $newdata);
   		$pk = SearchDAO::getSearchDAO()->saveSearch($search);
  
   		//If the primary key is not null, then the save was successful
   		if (!is_null($pk))
   		{
   			$retval = Zend_Json::encode(array('PrimaryKey' => $pk));
		}
		else 
		{
			$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem saving your changes.  Please try again.'));
		}
			
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    }
    
	public function updatevisiblecolumnorderAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$search = SearchNamespace::getCurrentSearch();
		$columns = $this->getRequest()->getParam('columns');
		$data = Zend_Json::decode($columns);
		$visibleColumns = array();
		foreach ($data as $datacol)
		{
			$visibleColumns[$datacol] = $datacol;
		}
		$search->setVisibleColumns($visibleColumns);
		SearchNamespace::setCurrentSearch($search);
    }
    
    public function advancedsearchAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$formData = $this->getRequest()->getParams();
    	$jsondata = $this->getRequest()->getParam("advancedsearchparameters");
		$advancedsearchparameters = Zend_Json::decode($jsondata);
		
		$searchparams = $this->buildSearchParameters($advancedsearchparameters);
		
		$search = SearchNamespace::getCurrentSearch();
		if (!isset($search))
		{
			$search = new Search();
		}
		$search->setSearchParameters($searchparams);
		SearchNamespace::setCurrentSearch($search);
    
    	//Redirect to the search results
    	$this->_helper->redirector('searchresults', NULL, NULL, array());
    }
    
    public function clearadvancedsearchAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	SearchNamespace::clearCurrentSearch();
    }
    
    /*
     * Builds the search object.
     * 
     * @return array
     */
    private function buildSearchParameters(array $advancedsearchparameters)
    {
    	$searchparams = array();
    	//Build the search
    	foreach ($advancedsearchparameters as $params)
    	{
    		$openparen = $params['openparen'];
    		$booltype = $params['booltype'];
			$column = $params['column'];
			$searchtype = $params['searchtype'];
			$value = $params['value'];
			$closeparen = $params['closeparen'];
			$colname = $params['colname'];
			$valuename = $params['valuename'];
			
			$searchparam = new SearchParameters();
			$searchparam->setBooleanValue($booltype);
			$searchparam->setColumnName($column);
			$searchparam->setSearchType($searchtype);
			$searchparam->setValue($value);
			$searchparam->setOpenParenthesis($openparen);
			$searchparam->setClosedParenthesis($closeparen);
			$searchparam->setColumnText($colname);
			$searchparam->setValueName($valuename);
			array_push($searchparams, $searchparam);
		}
		return $searchparams;
	}
    
	/*
     * Builds the search and sets it in the namespace.
     */
    public function savesearchAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$searchform = $this->getSearchForm();
    	$formData = $this->getRequest()->getParams();
	    		
    	if ($searchform->isValidPartial($formData))
    	{
    		$values = $searchform->getValues();
	    	$searchname = $values["savesearchinput"];
			$jsondata = $values["advancedsearchparameters"];
			$this->saveSearch($searchname, $jsondata);
    	}
    	else 
    	{
    		//Assign the override input name
    		$formData['hiddenoverridesearchnameinput'] = $formData['savesearchinput'];
    		$this->displaySearchErrors($formData, 'There is a problem with your search name.  Please try again.');
		}
    }
    
    public function overwritesearchAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$formData = $this->getRequest()->getParams();
	    $searchname = $formData["savesearchinput"];
		$jsondata = $formData["advancedsearchparameters"];
			
    	$this->saveSearch($searchname, $jsondata, FALSE);
    }
    
    private function saveSearch($searchname, $jsondata, $savetodatabase = TRUE)
    {
   		$advancedsearchparameters = Zend_Json::decode($jsondata);
		
		$auth = Zend_Auth::getInstance();
   		$identity = $auth->getIdentity();
   	
		$searchparams = $this->buildSearchParameters($advancedsearchparameters);
		$currentsearch = SearchNamespace::getCurrentSearch();
		$search = new SavedSearch();
		if (isset($currentsearch))
		{
			$search->setHiddenColumns($currentsearch->getHiddenColumns());
			$search->setVisibleColumns($currentsearch->getVisibleColumns());
			$search->setOrderBy($currentsearch->getOrderBy());
		}
		$search->setSearchParameters($searchparams);
		$search->setSearchName($searchname);
		$search->setOwner($identity[PeopleDAO::PERSON_ID]);
		
		SearchNamespace::setCurrentSearch($search);
		$success = $search->saveSearch();
		if ($success)
		{
			if ($savetodatabase)
			{
				//Add it to the search info to the database
				SearchDAO::getSearchDAO()->saveSearch($search);
			}
			SearchNamespace::setSaveStatus(SearchNamespace::SAVE_STATE_SUCCESS);
			$this->_helper->redirector('searchrecord');
		}
		else 
		{
			$this->displaySearchErrors(array('savesearchinput' => $searchname, 'advancedsearchparameters' => $advancedsearchparameters), 
				'There was a problem saving your search.  Please try again or contact the ACORN administrator if the problem continues.');
		}
    }
    
	public function loadcurrentsearchAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$this->_helper->layout->disableLayout();
		$formdata = $this->getRequest()->getParams();
		if (!isset($formdata['advancedsearchparameters']) || empty($formdata['advancedsearchparameters']))
    	{
	    	$search = SearchNamespace::getCurrentSearch();
	    	if (!isset($search))
	    	{
	    		$search = new Search();
	    	}
	   	}
	   	//IF there are advanced search parameters, this is being loaded for the
	   	//error message
    	else 
    	{
    		//Decode
    		$advancedsearchparameters = Zend_Json::decode($formdata['advancedsearchparameters']);
    		//Build
    		$searchparams = $this->buildSearchParameters($advancedsearchparameters);
    		//New search to build the parameter response
    		$search = new Search();
    		$search->setSearchParameters($searchparams);
		}
    	$params = $search->getJSONParameters();
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($params);
		
    }
    
    public function loadsavedsearchAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$this->_helper->layout->disableLayout();
		
    	$searchid = $this->getRequest()->getParam("loadsavedsearchselect");
    	//Get the info from the database
    	$search = SearchDAO::getSearchDAO()->getSavedSearchFromID($searchid);
    	$json = $search->getJSONParameters();
    	SearchNamespace::setCurrentSearch($search);
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($json);
	}
    
	private function displaySearchErrors(array $formData, $message)
    {
    	$form = $this->getSearchForm();
		//Populate the data with what the user
		//gave but also display the error messages.
		$form->populate($formData);
		$this->view->form = $form;
		
		$this->view->placeholder("saveerrormessage")->set($message);
		$this->renderScript('search/searchrecord.phtml');
    }
    
    public function getvisiblecolumnsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$columns = array();
		$search = SearchNamespace::getCurrentSearch();
		if (isset($search))
		{
			$columns = array_values($search->getVisibleColumns());
		}
		$retval = Zend_Json::encode(array('Result' => $columns));
			
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    }
    
	public function gethiddencolumnsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$columns = array();
		$search = SearchNamespace::getCurrentSearch();
		if (isset($search))
		{
			$columns = array_values($search->getHiddenColumns());
		}
		$retval = Zend_Json::encode(array('Result' => $columns));
			
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    }
    
    public function hideresultcolumnAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$search = SearchNamespace::getCurrentSearch();
		$columnname = $this->getRequest()->getParam('column');
		if (isset($search))
		{
			$search->removeVisibleColumn($columnname);
			$search->addHiddenColumn($columnname);
			SearchNamespace::setCurrentSearch($search);
		}
    }
    
	public function showresultcolumnAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
    	$search = SearchNamespace::getCurrentSearch();
		$columnname = $this->getRequest()->getParam('column');
		if (isset($search))
		{
			$search->addVisibleColumn($columnname);
			$search->removeHiddenColumn($columnname);
			SearchNamespace::setCurrentSearch($search);
		}
    }
    
    public function savecolumnorderAction()
    {
    	$search = SearchNamespace::getCurrentSearch();
    	if (isset($search) && $search instanceof SavedSearch)
    	{
    		$success = $search->saveSearch();
    		if ($success)
    		{
    			SearchNamespace::setSaveStatus(SearchNamespace::SAVE_STATE_SUCCESS);
    			$this->_helper->redirector('searchresults');
    		}
    		else 
    		{
    			$this->view->placeholder("searchresultsname")->set($search->getSearchName());
				$this->view->placeholder("searchinfo")->set($search->getSearchInfo());
				$this->view->placeholder("saveerrormessage")->set('There was a problem saving your search.');
				$this->renderScript('search/searchresults.phtml');
    		}
    	}
    	else
    	{
    		$searchname = '';
    		$searchinfo = '';
    		if (isset($search))
    		{
    			$searchname = $search->getSearchName();
    			$searchinfo = $search->getSearchInfo();
    		}
    		$this->view->placeholder("searchresultsname")->set($searchname);
			$this->view->placeholder("searchinfo")->set($searchinfo);
			$this->view->placeholder("saveerrormessage")->set('This is not a saved search.');
			$this->renderScript('search/searchresults.phtml');
    	}
    }
    
    /**
     * Exports all of the data to Excel by:
     * 1. Searching for the data
     * 2. Parsing out the visible column data and placing it in a spreadsheet
     * 3. Sending a message back to the JavaScript message that it is ready to open.
     */
    public function exportalltoexcelAction()
    {
    	ini_set('memory_limit', '3072M');
		ini_set('max_execution_time', 300);
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$sort = $this->getRequest()->getParam('sort', 'RecordID');
		$sortdir = $this->getRequest()->getParam('dir', 'asc');
		$search = SearchNamespace::getCurrentSearch();
		$data = $this->getExportSearchResults($search, $sort, $sortdir, 0, 10000);
		$fullfilename = "";
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		$personid = $identity[PeopleDAO::PERSON_ID];
		$date = date(ACORNConstants::$DATE_FILENAME_FORMAT);
		
		$filename = $personid . '_' . $date . '.csv';
		$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
	    			
	    $fullfilename = $config->getReportsDirectory() . '/cvsreports/' . $filename;
	    $filehandler = fopen($fullfilename, 'w');

		$cols = $search->getVisibleColumns();
		
		fputcsv($filehandler, $cols);
		
		if (!empty($data))
		{
			foreach ($data as $result)
			{
				fputcsv($filehandler, $result);
			}
		}
		fclose($filehandler);
		
		echo $filename;
    }
    
    /**
     * Finds the search results
      
     * @return array
     */
    private function getExportSearchResults(Search $search, $sort, $sortdir, $startIndex = 0, $count = 50)
    {
    	$availablerecords = array();
    
    	$orderby = array($sort => $sort . ' ' . $sortdir);
    	//Update the order by in the namespace
    	$search->setOrderBy($orderby);
    	SearchNamespace::setCurrentSearch($search);
    	 
    	$searchparams = $search->getSearchParameters();
    	$tempsearchparams = $search->getSearchParameters();
    	$searchparams = array();
    	$additionalwhere = NULL;
    	//Extract the tub into the additional where if it exists
    	foreach ($tempsearchparams as $param)
    	{
    		if ($param->getColumnName() != 'Locations.TUB')
    		{
    			array_push($searchparams, $param);
    		}
    		else
    		{
    			$additionalwhere = 'HomeLocationID IN (SELECT LocationID FROM Locations WHERE TUB=\'' . $param->getValue() . '\')';
    		}
    	}
    	$records = CombinedRecordsDAO::getCombinedRecordsDAO()->getRecords($searchparams, $startIndex, $count, $additionalwhere, $orderby);
    	$cols = array_keys($search->getVisibleColumns());
    	foreach ($records as $record)
    	{
    		array_push($availablerecords, $this->buildExportSearchResultArray($cols,$record));
    	}
    
    	return $availablerecords;
    }
    
    /**
     * Builds an array of results based on the visible columns columns. 
     * @param array $columns - the visible columns
     * @param CombinedRecord $record - the record in which to extract the data.
     * @return array - the data to be placed into the Excel spreadsheet.
     */
    private function buildExportSearchResultArray(array $columns, CombinedRecord $record)
    {
    	$results = array();
    	foreach ($columns as $col)
    	{
    		switch ($col)
    		{
    			case "Type":
    				$results['Type'] = $record->getRecordType();
    				break;
    			case CombinedRecordsDAO::RECORD_ID:
    				$results[CombinedRecordsDAO::RECORD_ID] = $record->getRecordID();
    				break;
    			case "Status":
    				$results["Status"] = $record->getItemStatus();
    				break;
    			case "CallNumbers":
    				$callnumbers = $record->getCallNumbers();
    				$callnumberstring = implode(",", $callnumbers);
    				$results["CallNumbers"] = $callnumberstring;
    				break;
    			case ItemDAO::TITLE:
    				$results[ItemDAO::TITLE] = $record->getTitle();
    				break;
    			case ItemDAO::AUTHOR_ARTIST:
    				$results[ItemDAO::AUTHOR_ARTIST] = $record->getAuthorArtist();
    				break;
    			case ProjectDAO::PROJECT_NAME:
    				$project = NULL;
    				if (!is_null($record->getProjectID()))
    				{
    					$project = ProjectDAO::getProjectDAO()->getProject($record->getProjectID())->getProjectName();
    				}
    				$results[ProjectDAO::PROJECT_NAME] = $project;
    				break;
    			case GroupDAO::GROUP_NAME:
    				$group = NULL;
    				if (!is_null($record->getProjectID()))
    				{
    					$group = GroupDAO::getGroupDAO()->getGroup($record->getGroupID());
    				}
    				$results[GroupDAO::GROUP_NAME] = $group;
    				break;
    			case LoginDAO::LOGIN_DATE:
    				$logindate = NULL;
    				if (!is_null($record->getFunctions()->getLogin()->getPrimaryKey()))
    				{
    					$zenddate = new Zend_Date($record->getFunctions()->getLogin()->getLoginDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    					$logindate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    				}
    				$results[LoginDAO::LOGIN_DATE] = $logindate;
    				break;
    			case "Coordinator":
    				$coordinator = NULL;
    				if (!is_null($record->getCoordinatorID()))
    				{
    					$coordinator = PeopleDAO::getPeopleDAO()->getPerson($record->getCoordinatorID())->getDisplayName();
    				}
    				$results["Coordinator"] = $coordinator;
    				break;    				
    			case "Repository":
    				$repository = NULL;
					if (!is_null($record->getHomeLocationID()))
					{
						$location = LocationDAO::getLocationDAO()->getLocation($record->getHomeLocationID());
						$repository = $location->getLocation();
					}
					$results['Repository'] = $repository;
			    	break;
    			case "TUB":
    				$tub = NULL;
					if (!is_null($record->getHomeLocationID()))
					{
						$location = LocationDAO::getLocationDAO()->getLocation($record->getHomeLocationID());
						$tub = $location->getTUB();
					}
					$results['TUB'] = $tub;
			    	break;
    			case "WorkAssignedTo":
		    		$workassignedto = NULL;
					$workassignedtolist = $record->getWorkAssignedTo();
		    		if (!empty($workassignedtolist))
					{
						$workassignedtoarray = array();
						foreach($workassignedtolist as $w)
						{
							array_push($workassignedtoarray, $w->getDisplayName());
						}
						$workassignedto = implode('; ', $workassignedtoarray);
					}
			    	$results["WorkAssignedTo"] = $workassignedto;
    				break;
    			case "WorkDoneBy":
    				$workdoneby = NULL;
			    	if (!is_null($record->getFunctions()->getReport()->getPrimaryKey()))
			    	{
			    		$conservators = $record->getFunctions()->getReport()->getReportConservators();
						$workdonebyarray = array();
						foreach($conservators as $c)
						{
							array_push($workdonebyarray, $c->getConservator()->getDisplayName());
						}
						$workdoneby = implode('; ', $workdonebyarray);
					} 
					$results["WorkDoneBy"] = $workdoneby;
    				break;
    			case "ReportHours":
    				$results["ReportHours"] = $record->getFunctions()->getReport()->getTotalHours();
    				break;
    			case "Activity":
    				$results["Activity"] = $record->getActivityStatus();;
    				break;
    			case "CollectionName":
    				$results["CollectionName"] = $record->getCollectionName();
    				break;
    			case "DateOfObject":
    				$results["DateOfObject"] = $record->getDateOfObject();
    				break;
    			case DepartmentDAO::DEPARTMENT_NAME:
    				$department = NULL;
    				if (!is_null($record->getDepartmentID()))
    				{
    					$department = DepartmentDAO::getDepartmentDAO()->getDepartment($record->getDepartmentID())->getDepartmentName();
    				}
    				$results[DepartmentDAO::DEPARTMENT_NAME] = $department;
    				break;
    			case FormatDAO::FORMAT:
    				$format = NULL;
    				if (!is_null($record->getFormatID()))
    				{
    					$format = FormatDAO::getFormatDAO()->getFormat($record->getFormatID())->getFormat();
    				}
    				$results[FormatDAO::FORMAT] = $format;
    				break;
    			case "ItemCount":
    				$counts = 0;
			 		if (is_null($record->getFunctions()->getReport()->getPrimaryKey()))
			 		{
			 			$counts = $record->getInitialCounts()->getTotalItemCount();
			 		}
			 		else
			 		{
			 			$counts = $record->getFunctions()->getReport()->getCounts()->getTotalItemCount();
			 		}
			    	$results['ItemCount'] = $counts;
    				break;
    			case "VolumeCount":
    				$volcounts = 0;
			 		if (is_null($record->getFunctions()->getReport()->getPrimaryKey()))
			 		{
			 			$volcounts = $record->getInitialCounts()->getVolumeCount();
			 		}
			 		else
			 		{
			 			$volcounts = $record->getFunctions()->getReport()->getCounts()->getVolumeCount();
			 		}
			    	$results['VolumeCount'] = $volcounts;
    				break;
    			case "SheetCount":
    				$sheetcounts = 0;
    				if (is_null($record->getFunctions()->getReport()->getPrimaryKey()))
    				{
    					$sheetcounts = $record->getInitialCounts()->getSheetCount();
    				}
    				else
    				{
    					$sheetcounts = $record->getFunctions()->getReport()->getCounts()->getSheetCount();
    				}
    				$results['SheetCount'] = $sheetcounts;
    				break;
    			case "PhotoCount":
    				$photocounts = 0;
    				if (is_null($record->getFunctions()->getReport()->getPrimaryKey()))
    				{
    					$photocounts = $record->getInitialCounts()->getPhotoCount();
    				}
    				else
    				{
    					$photocounts = $record->getFunctions()->getReport()->getCounts()->getPhotoCount();
    				}
    				$results['PhotoCount'] = $photocounts;
    				break;
    			case "BoxCount":
    				$boxcounts = 0;
    				if (is_null($record->getFunctions()->getReport()->getPrimaryKey()))
    				{
    					$boxcounts = $record->getInitialCounts()->getBoxCount();
    				}
    				else
    				{
    					$boxcounts = $record->getFunctions()->getReport()->getCounts()->getBoxCount();
    				}
    				$results['BoxCount'] = $boxcounts;
    				break;
    			case "HousingCount":
    				$housingcounts = 0;
    				if (is_null($record->getFunctions()->getReport()->getPrimaryKey()))
    				{
    					$housingcounts = $record->getInitialCounts()->getHousingCount();
    				}
    				else
    				{
    					$housingcounts = $record->getFunctions()->getReport()->getCounts()->getHousingCount();
    				}
    				$results['HousingCount'] = $housingcounts;
    				break;
    			case "OSWStatus":
    				$stat = NULL;
    				if ($record->getRecordType() == 'OSW')
		    		{
		    			$stat = OnSiteWorkDAO::getOnSiteWorkDAO()->getOSWStatus($record->getRecordID());
		    		}
    				$results['OSWStatus'] = $stat;
    				break;
    			case "ProposedBy":
    				$proposedby = NULL;
    				if (!is_null($record->getFunctions()->getProposal()->getPrimaryKey()))
    				{
    					if (!is_null($record->getFunctions()->getProposal()->getProposedBy()))
    					{
    						$people = $record->getFunctions()->getProposal()->getProposedBy();
    						$proposedbyarray = array();
    						foreach($people as $p)
    						{
    							array_push($proposedbyarray, $p->getDisplayName());
    						}
    						$proposedby = implode('; ', $proposedbyarray);
    					}
    				}
    				$results['ProposedBy'] = $proposedby;
    				break;
    			case "ProposedHours":
    				$proposalhours = 0;
    				if (!is_null($record->getFunctions()->getProposal()->getPrimaryKey()))
    				{
    					$proposalhours = $record->getFunctions()->getProposal()->getHoursAsString();
    				}
    				$results['ProposedHours'] = $proposalhours;
    				break;
    			case ProposalDAO::PROPOSAL_DATE:
    				$proposalDate = NULL;
    				if (!is_null($record->getFunctions()->getProposal()->getProposalDate()))
    				{	 
	    				$zenddate = new Zend_Date($record->getFunctions()->getProposal()->getProposalDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
	    				$proposalDate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    				}
    				$results[ProposalDAO::PROPOSAL_DATE] = $proposalDate;
    				break;
    			case PurposeDAO::PURPOSE:
    				$purpose = NULL;
    				if (!is_null($record->getPurposeID()))
    				{
    					$purpose = PurposeDAO::getPurposeDAO()->getPurpose($record->getPurposeID())->getPurpose();
    				}
    				$results[PurposeDAO::PURPOSE] = $purpose;
    				break;
    			case "ReportBy":
    				$reportby = NULL;
    				if (!is_null($record->getFunctions()->getReport()->getPrimaryKey()))
    				{
    					if (!is_null($record->getFunctions()->getReport()->getReportBy()))
    					{
    						$reportby = PeopleDAO::getPeopleDAO()->getPerson($record->getFunctions()->getReport()->getReportBy())->getDisplayName();
    					}
    				}
    				$results['ReportBy'] = $reportby;
    				break;
    			case ReportDAO::REPORT_DATE:
    				$reportDate = NULL;
    				if (!is_null($record->getFunctions()->getReport()->getReportDate()))
    				{	 
		    			$zenddate = new Zend_Date($record->getFunctions()->getReport()->getReportDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
	    				$reportDate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    				}
    				$results[ReportDAO::REPORT_DATE] = $reportDate;
    				break;
    			case LogoutDAO::LOGOUT_DATE:
    				$logoutdate = NULL;
    				if (!is_null($record->getFunctions()->getLogout()->getPrimaryKey()))
    				{
    					$zenddate = new Zend_Date($record->getFunctions()->getLogout()->getLogoutDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    					$logoutdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    				}
    				$results[LogoutDAO::LOGOUT_DATE] = $logoutdate;
    					 
		    		break;
    			case "Importances":
    				$importances = $record->getFunctions()->getReport()->getImportances();
    				$imparray = array();
    				foreach ($importances as $importance)
    				{
    					array_push($imparray, $importance->getImportance());
    				}
    				$stringimportances = implode("; ", $imparray);
    				$results['Importances'] = $stringimportances;
    				break;
    			case ItemDAO::STORAGE:
    				$results[ItemDAO::STORAGE] = $record->getStorage();
    				break;
    			case "TemporaryToLocation":
    				$temploc = '';
    				if (!is_null($record->getFunctions()->getTemporaryTransfer()->getPrimaryKey()))
    				{
    					$temploc = LocationDAO::getLocationDAO()->getLocation($record->getFunctions()->getTemporaryTransfer()->getTransferTo())->getLocation();
    				}
    				$results['TemporaryToLocation'] = $temploc;
    				break;
    			case "TemporaryDate":
    				$tempdate = NULL;
    				if (!is_null($record->getFunctions()->getTemporaryTransfer()->getTransferDate()))
    				{
	    				$zenddate = new Zend_Date($record->getFunctions()->getTemporaryTransfer()->getTransferDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
	    				$tempdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    				}
    				$results['TemporaryDate'] = $tempdate;
    				break;
    			case "WorkType":
    				$worktypestring = NULL;
    				if ($record->getRecordType() == 'OSW')
    				{
    					$worktypes = WorkTypeDAO::getWorkTypeDAO()->getWorkTypes($record->getRecordID());
    					$worktypestringarray = array();
    					foreach ($worktypes as $wt)
    					{
    						array_push($worktypestringarray, $wt->getWorkType());
    					}
    					$worktypestring = implode(',', $worktypestringarray);
    				}
    				$results['WorkType'] = $worktypestring;
    				break;
    			case OnSiteWorkDAO::WORK_START_DATE:
    				$date = NULL;
    				if ($record->getRecordType() == 'OSW')
    				{
    					$osw = OnSiteWorkDAO::getOnSiteWorkDAO()->getOSW($record->getRecordID());
    					$daterange = $osw->getDateRange();
    					if (!is_null($daterange))
    					{
    						$date = $daterange->getStartDate();
    					}
    				}
    				$results[OnSiteWorkDAO::WORK_START_DATE] = $date;
    				break;
    			case OnSiteWorkDAO::WORK_END_DATE:
    				$date = NULL;
    				if ($record->getRecordType() == 'OSW')
    				{
    					$osw = OnSiteWorkDAO::getOnSiteWorkDAO()->getOSW($record->getRecordID());
    					$daterange = $osw->getDateRange();
    					if (!is_null($daterange))
    					{
    						$date = $daterange->getEndDate();
    					}
    				}
    				$results[OnSiteWorkDAO::WORK_END_DATE] = $date;
    				break;
    			case "WorkLocation":
    				$workloc = NULL;
    				if (!is_null($record->getFunctions()->getReport()->getPrimaryKey()))
    				{
    					$workloc = LocationDAO::getLocationDAO()->getLocation($record->getFunctions()->getReport()->getWorkLocation())->getLocation();
    				}
    				$results['WorkLocation'] = $workloc;
    				break;
    			case "Curator":
    				$curator = NULL;
    				if (!is_null($record->getCuratorID()))
    				{
    					$curator = PeopleDAO::getPeopleDAO()->getPerson($record->getCuratorID())->getDisplayName();
    				}
    				$results['Curator'] = $curator;	
    				break;
    			case ItemDAO::INSURANCE_VALUE:
    				$results[ItemDAO::INSURANCE_VALUE] = $record->getInsuranceValue();
    				break;
    			case ReportDAO::SUMMARY:
    				$summary = NULL;
    				if (!is_null($record->getFunctions()->getReport()->getPrimaryKey()))
			    	{
			    		$summary = $record->getFunctions()->getReport()->getSummary();
			    	} 
			    	$results[ReportDAO::SUMMARY] = $summary;
			    	break;
    		}
    	}
    
    	return $results;
    }
    
    public function exporttoexcelAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$sort = $this->getRequest()->getParam('sort', 'RecordID');
		$sortdir = $this->getRequest()->getParam('dir', 'asc');
		$startIndex = $this->getRequest()->getParam('startIndex', 0);
		$search = SearchNamespace::getCurrentSearch();
		$data = $this->getExportSearchResults($search, $sort, $sortdir, $startIndex, 50);
		$fullfilename = "";
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		$personid = $identity[PeopleDAO::PERSON_ID];
		$date = date(ACORNConstants::$DATE_FILENAME_FORMAT);
		
		$filename = $personid . '_' . $date . '.csv';
		$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
		
		$fullfilename = $config->getReportsDirectory() . '/cvsreports/' . $filename;
		$filehandler = fopen($fullfilename, 'w');
		
		$cols = $search->getVisibleColumns();
		fputcsv($filehandler, $cols);
		
		if (!empty($data))
		{
			foreach ($data as $result)
			{
				fputcsv($filehandler, $result);
			}
		}
		fclose($filehandler);
		
		echo $filename;
		
    }
    
	public function promptforsaveAction()
	{
		//Don't display a new view, only the search area is being updated.
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout for the search area (the
		//default layout is only needed for the entire form).
		$this->_helper->layout->disableLayout();
		
		$filename = $this->getRequest()->getParam('filename');
		if (isset($filename))
		{
			//$fc = Zend_Controller_Front::getInstance();
	        //$initializer = $fc->getPlugin('Initializer');
	        $config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
	    	$path = $config->getReportsDirectory() . '/cvsreports/' . $filename;
	        
			if (!is_file($path)) { die("<b>404 File not found!</b> " . $path); }
	
			//Gather relevent info about file
			$len = filesize($path);
			$filename = basename($path);
			
			//Begin writing headers
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: public");
			header("Content-Description: File Transfer");
	
			//Use the switch-generated Content-Type
			header("Content-Type: application/force-download");
	
			//Force the download
			$header="Content-Disposition: attachment; filename=".$filename.";";
			header($header );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$len);
			readfile($path);
		}
	}
}
?>