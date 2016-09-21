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
 * ListController
 * @author vcrema
 * Created Mar 5, 2009
 *
 */

class ListController extends Zend_Controller_Action 
{
	/**
	 * The default action - do nothing
	 */
    public function indexAction() 
    {
        $this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
    }
    
	public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity() && $this->getRequest()->getActionName() != 'populatelocations') 
        {
            $this->_helper->redirector('index', 'index');
        }
    }
    
    public function populateprojectsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$inactive = $this->getRequest()->getParam('includeinactive');
    	$blank = $this->getRequest()->getParam('includeblank');
    	$projectsDAO = new ProjectDAO();
		if (!isset($inactive) || $inactive == 0)
		{
			$projects = $projectsDAO->getAllProjectsForSelect();
		}
		else 
		{
			$projects = $projectsDAO->getAllProjectsForSelect(TRUE);
		}
		
		if (isset($blank) && $blank == 1)
		{
			array_unshift($projects, array('ProjectID' => 0, 'ProjectName' => ''));
		}
		$retval = Zend_Json::encode(array('Result' => $projects));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
    /**
     * Retrieves the ChargeTo items via the ChargeToDAO and encodes them
     * into JSON format to be used by JavaScript to populate the dropdown box.
     */
    public function populatechargetoAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$this->_helper->layout->disableLayout();
    
    	$locations = LocationDAO::getLocationDAO()->getAllLocationsForSelect(FALSE);
    	array_unshift($locations, array('LocationID' => 'Project', 'Location' => 'Project'));
    	array_unshift($locations, array('LocationID' => 'Patron', 'Location' => 'Patron'));
    	 
    	$retval = Zend_Json::encode(array('Result' => $locations));
    	//Make sure JS knows it is in JSON format.
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
    	->setBody($retval);
    }
    
	public function populategroupsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$inactive = $this->getRequest()->getParam('includeinactive', 0);
    	$blank = $this->getRequest()->getParam('includeblank', 0);
    	$groupsDAO = new GroupDAO();
		if (!isset($inactive) || $inactive == 0)
		{
			$groups = $groupsDAO->getAllGroupsForSelect(FALSE);
		}
		else 
		{
			$groups = $groupsDAO->getAllGroupsForSelect(TRUE);
		}
		
		if (isset($blank) && $blank == 1)
		{
			array_unshift($groups, array('GroupID' => 0, 'GroupName' => ''));
		}
		$retval = Zend_Json::encode(array('Result' => $groups));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function populatepurposesAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$inactive = $this->getRequest()->getParam('includeinactive', 0);
    	$blank = $this->getRequest()->getParam('includeblank', 0);
    	$purposesDAO = new PurposeDAO();
		if (!isset($inactive) || $inactive == 0)
		{
			$purposes = $purposesDAO->getAllPurposesForSelect();
		}
		else 
		{
			$purposes = $purposesDAO->getAllPurposesForSelect(TRUE);
		}
		
		if (isset($blank) && $blank == 1)
		{
			array_unshift($purposes, array('PurposeID' => 0, 'Purpose' => ''));
		}
		$retval = Zend_Json::encode(array('Result' => $purposes));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function populatestorageAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$blank = $this->getRequest()->getParam('includeblank', 0);
    	$storage = ItemDAO::getItemDAO()->getAllStorageForSelect();
		
		if (isset($blank) && $blank == 1)
		{
			array_unshift($storage, array('Storage' => ''));
		}
		$retval = Zend_Json::encode(array('Result' => $storage));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function populateformatsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$inactive = $this->getRequest()->getParam('includeinactive', 0);
    	$blank = $this->getRequest()->getParam('includeblank', 0);
    	$formatsDAO = new FormatDAO();
		if (!isset($inactive) || $inactive == 0)
		{
			$formats = $formatsDAO->getAllFormatsForSelect();
		}
		else 
		{
			$formats = $formatsDAO->getAllFormatsForSelect(TRUE);
		}
		
		if (isset($blank) && $blank == 1)
		{
			array_unshift($formats, array('FormatID' => 0, 'Format' => ''));
		}
		$retval = Zend_Json::encode(array('Result' => $formats));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function populatedepartmentsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		Logger::log($_GET, Zend_Log::DEBUG);
		$inactive = $this->getRequest()->getParam('includeinactive');
		$repositoryid = $this->getRequest()->getParam('repositoryid');
		$orderby = $this->getRequest()->getParam('orderby');
		
		if (!isset($repositoryid))
		{
			$repositoryid = NULL;
		}
		if (!isset($orderby))
		{
			$orderby = 'DepartmentName';
		}
		else 
		{
			$orderby = explode(',', $orderby);
		}
		
    	$blank = $this->getRequest()->getParam('includeblank');
    	if (!isset($inactive) || $inactive == 0)
		{
			$departments = DepartmentDAO::getDepartmentDAO()->getDepartmentsForSelect(FALSE, $repositoryid, $orderby);
		}
		else 
		{
			$departments = DepartmentDAO::getDepartmentDAO()->getDepartmentsForSelect(TRUE, $repositoryid, $orderby);
		}
		
		if (isset($blank) && $blank == 1)
		{
			array_unshift($departments, array('DepartmentID' => 0, 'DepartmentName' => ''));
		}
		$retval = Zend_Json::encode(array('Result' => $departments));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function populaterepositorydepartmentsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$inactive = $this->getRequest()->getParam('includeinactive');
		$blank = $this->getRequest()->getParam('includeblank');
		
		if (!isset($inactive) || $inactive == 0)
		{
			$departments = DepartmentDAO::getDepartmentDAO()->getRepositoryDepartmentsForSelect(FALSE);
		}
		else 
		{
			$departments = DepartmentDAO::getDepartmentDAO()->getRepositoryDepartmentsForSelect(TRUE);
		}
		
		if (isset($blank) && $blank == 1)
		{
			array_unshift($departments, array('DepartmentID' => 0, 'DepartmentName' => ''));
		}
		
		$retval = Zend_Json::encode(array('Result' => $departments));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function populatelocationsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$inactive = $this->getRequest()->getParam('includeinactive', 0);
		$isrepositorysearch = $this->getRequest()->getParam('isrepositorysearch', 0);
		$limittorepository = $this->getRequest()->getParam('limittorepository', 1);
		
		Logger::log ($this->getRequest()->getParams());
		
		$blank = $this->getRequest()->getParam('includeblank', 0);
    	$locations = NULL;
    	$auth = Zend_Auth::getInstance();
    	$hasIdentity = $auth->hasIdentity();
    	$identity = $auth->getIdentity();
    	//If the user is a repository user, then it may be limited to only the user's repository.
    	if ($limittorepository && $hasIdentity &&
    			$identity[PeopleDAO::ACCESS_LEVEL] != PeopleDAO::ACCESS_LEVEL_ADMIN 
    			&& $identity[PeopleDAO::ACCESS_LEVEL] != PeopleDAO::ACCESS_LEVEL_REGULAR)
    	{
    		$repositoryid = $identity[LocationDAO::LOCATION_ID];
    		$loc = LocationDAO::getLocationDAO()->getLocation($repositoryid);
    		$locations = array(array("LocationID" => $loc->getPrimaryKey(), "Location" => $loc->getLocation()));
    	}
    	else 
    	{
			//Only get repositories if this is a repository search
			if (isset($isrepositorysearch) && $isrepositorysearch == 1)
			{
				if (!isset($inactive) || $inactive == 0)
				{
					$locations = LocationDAO::getLocationDAO()->getAllRepositoriesForSelect(FALSE);
				}
				else 
				{
					$locations = LocationDAO::getLocationDAO()->getAllRepositoriesForSelect(TRUE);
				}
			}
			//Otherwise get the entire location list
			else 
			{
				if (!isset($inactive) || $inactive == 0)
				{
					$locations = LocationDAO::getLocationDAO()->getAllLocationsForSelect(FALSE);
				}
				else 
				{
					$locations = LocationDAO::getLocationDAO()->getAllLocationsForSelect(TRUE);
				}
			}
    	}

		if (isset($blank) && $blank == 1)
		{
			array_unshift($locations, array('LocationID' => 0, 'Location' => ''));
		}
		
		$retval = Zend_Json::encode(array('Result' => $locations));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
    
	public function populateautotextAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$autotexttype = $this->getRequest()->getParam('autotexttype');
		$dependentautotexttypeid = $this->getRequest()->getParam('dependentautotexttypeid');
		$includecopytreatment = $this->getRequest()->getParam('includecopytreatment', 1);
		$includeall = $this->getRequest()->getParam('includeall', 0);
		$includeglobal = $this->getRequest()->getParam('includeglobal', 1);
		
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		$personid = $identity[PeopleDAO::PERSON_ID];
		
		if (!isset($dependentautotexttypeid))
		{
			$dependentautotexttypeid = NULL;
		}
		
    	$blank = $this->getRequest()->getParam('includeblank');
    	$autotextDAO = new AutotextDAO();
		$autotext = $autotextDAO->getAllAutotextForSelect($autotexttype, $personid, $dependentautotexttypeid, $includeall, $includeglobal);
		
		if ($includecopytreatment == 1 && $autotexttype == 'Treatment')
		{
			array_unshift($autotext, array('AutotextID' => 0, 'Caption' => 'Copy Proposed Treatment'));
		}
		if (isset($blank) && $blank == 1)
		{
			array_unshift($autotext, array('AutotextID' => -1, 'Caption' => ''));
		}
		$retval = Zend_Json::encode(array('Result' => $autotext));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function getautotextAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$autotextid = $this->getRequest()->getParam('autotextid');
		
		$autotextDAO = new AutotextDAO();
		$autotext = $autotextDAO->getAutotextFromID($autotextid);
		
		$retval = Zend_Json::encode(array(AutotextDAO::AUTOTEXT => $autotext->getAutoText()));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
    /*
     * When 'Copy Proposed Treatment' is selected,
     * this gets called.
     */
	public function getproposedtreatmenttextAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$item = RecordNamespace::getCurrentItem();
		$treatment = "";
		if (isset($item) && !is_null($item))
		{
			$proposal = $item->getFunctions()->getProposal();
			$treatment = $proposal->getTreatment();
		}
		else 
		{
			$treatment = "(Proposed Treatment Will Be Copied For All Records)";
		}
		$retval = Zend_Json::encode(array('Autotext' => $treatment));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function populatestaffAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$inactive = $this->getRequest()->getParam('includeinactive', 0);
		$blank = $this->getRequest()->getParam('includeblank', 1);
		$includecontractors = $this->getRequest()->getParam('includecontractors', 0);
    	$includecurators = $this->getRequest()->getParam('includecurators', 0);
    	$additionalwhere = $this->getRequest()->getParam('additionalwhere', "");
    	
		$roletypes = array('Staff');
		if (isset($includecontractors) && $includecontractors == 1)
		{
			array_push($roletypes, 'Contractor');
		}
		if ($includecurators == 1)
		{
			array_push($roletypes, 'Curator');
		}
		if (!isset($inactive) || $inactive == 0)
		{
			$people = PeopleDAO::getPeopleDAO()->getAllPeopleForSelect($roletypes, FALSE, $additionalwhere);
		}
		else 
		{
			$people = PeopleDAO::getPeopleDAO()->getAllPeopleForSelect($roletypes, TRUE, $additionalwhere);
		}
		
		if (isset($blank) && $blank == 1)
		{
			array_unshift($people, array('PersonID' => 0, 'DisplayName' => ''));
		}
		$retval = Zend_Json::encode(array('Result' => $people));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function populatecuratorsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$inactive = $this->getRequest()->getParam('includeinactive');
		$blank = $this->getRequest()->getParam('includeblank');
		$repositoryid = $this->getRequest()->getParam('repositoryid');
		
		$additionalwhere = NULL;
		if (isset($repositoryid))
		{
			$additionalwhere = 'People.LocationID=' . $repositoryid;
		}
		
		$roletypes = array('Curator');
		if (!isset($inactive) || $inactive == 0)
		{
			$people = PeopleDAO::getPeopleDAO()->getAllPeopleForSelect($roletypes, FALSE, $additionalwhere);
		}
		else 
		{
			$people = PeopleDAO::getPeopleDAO()->getAllPeopleForSelect($roletypes, TRUE, $additionalwhere);
		}
		
		if (isset($blank) && $blank == 1)
		{
			array_unshift($people, array('PersonID' => 0, 'DisplayName' => ''));
		}
		$retval = Zend_Json::encode(array('Result' => $people));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function populateworktypesAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$inactive = $this->getRequest()->getParam('includeinactive');
    	$blank = $this->getRequest()->getParam('includeblank');
    	if (!isset($inactive) || $inactive == 0)
		{
			$worktypes = WorkTypeDAO::getWorkTypeDAO()->getAllWorkTypesForSelect();
		}
		else 
		{
			$worktypes = WorkTypeDAO::getWorkTypeDAO()->getAllWorkTypesForSelect(TRUE);
		}
		
		if (isset($blank) && $blank == 1)
		{
			array_unshift($worktypes, array('WorkTypeID' => 0, 'WorkType' => ''));
		}
		$retval = Zend_Json::encode(array('Result' => $worktypes));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function populateimportancesAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$inactive = $this->getRequest()->getParam('includeinactive', 0);
    	$blank = $this->getRequest()->getParam('includeblank', 0);
    	if ($inactive == 0)
		{
			$importances = ImportanceDAO::getImportanceDAO()->getAllImportancesForSelect();
		}
		else 
		{
			$importances = ImportanceDAO::getImportanceDAO()->getAllImportancesForSelect(TRUE);
		}
		
		if ($blank == 1)
		{
			array_unshift($importances, array(ImportanceDAO::IMPORTANCE_ID => 0, ImportanceDAO::IMPORTANCE => ''));
		}
		$retval = Zend_Json::encode(array('Result' => $importances));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function populateallpeopleAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$inactive = $this->getRequest()->getParam('includeinactive', 0);
		$blank = $this->getRequest()->getParam('includeblank', 0);
		$additionalwhere = $this->getRequest()->getParam('additionalwhere', "");
    	
		if (!isset($inactive) || $inactive == 0)
		{
			$people = PeopleDAO::getPeopleDAO()->getAllPeopleForSelect(NULL, FALSE, $additionalwhere);
		}
		else 
		{
			$people = PeopleDAO::getPeopleDAO()->getAllPeopleForSelect(NULL, TRUE, $additionalwhere);
		}
		
		if (isset($blank) && $blank == 1)
		{
			array_unshift($people, array('PersonID' => 0, 'DisplayName' => '', 'Inactive' => 0));
		}
		$retval = Zend_Json::encode(array('Result' => $people));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
    public function populatelistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$listname = $this->getRequest()->getParam('listname', '');
    	if (!empty($listname))
    	{
    		switch ($listname)
    		{
    			case "Departments":
    				$this->getRequest()->setParam('orderby', 'Location,DepartmentName,ShortName');
    				$this->_forward('populatedepartments');
    				break;
    			case "Formats":
    				$this->_forward('populateformats');
    				break;
    			case "Importances":
    				$this->_forward('populateimportances');
    				break;
    			case "Locations":
    				$this->_forward('populatelocations');
    				break;
    			case "People":
    				$this->_forward('populateallpeople');
    				break;
    			case "Projects":
    				$this->_forward('populateprojects');
    				break;
    			case "Purposes":
    				$this->_forward('populatepurposes');
    				break;
    			case "WorkTypes":
    				$this->_forward('populateworktypes');
    				break;
    		}
    	}
    }
    
    /*
     * Saves a new item to the given list.
     */
    public function saveitemAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$listname = $this->getRequest()->getParam('listname', '');
    	if (!empty($listname))
    	{
    		$params = $this->getRequest()->getParams();
    		Logger::log($params);
			switch ($listname)
    		{
    			case "Departments":
    				$department = new Department($params[DepartmentDAO::DEPARTMENT_ID], $params[DepartmentDAO::INACTIVE]);
    				$department->setAcronym($params[DepartmentDAO::ACRONYM]);
    				$department->setDepartmentName($params[DepartmentDAO::DEPARTMENT_NAME]);
    				$department->setLocationID($params[LocationDAO::LOCATION_ID]);
    				$department->setShortName($params[DepartmentDAO::SHORT_NAME]);
    				$pk = DepartmentDAO::getDepartmentDAO()->saveDepartment($department);
    				break;
    			case "Formats":
    				$format = new Format($params[FormatDAO::FORMAT_ID], $params[FormatDAO::INACTIVE]);
    				$format->setFormat($params[FormatDAO::FORMAT]);
    				$pk = FormatDAO::getFormatDAO()->saveFormat($format);
    				break;
    			case "Importances":
    				$importance = new Importance($params[ImportanceDAO::IMPORTANCE_ID], $params[ImportanceDAO::INACTIVE]);
    				$importance->setImportance($params[ImportanceDAO::IMPORTANCE]);
    				$pk = ImportanceDAO::getImportanceDAO()->saveImportance($importance);
    				break;
    			case "Locations":
    				$location = new Location($params[LocationDAO::LOCATION_ID], $params[LocationDAO::INACTIVE]);
    				$location->setAcronym($params[LocationDAO::ACRONYM]);
    				$location->setLocation($params[LocationDAO::LOCATION]);
    				$location->setTUB($params[LocationDAO::TUB]);
    				$location->setShortName($params[LocationDAO::SHORT_NAME]);
    				$location->setRepository($params[LocationDAO::IS_REPOSITORY]);
    				$location->setWorkLocation($params[LocationDAO::IS_WORK_LOCATION]);
    				$pk = LocationDAO::getLocationDAO()->saveLocation($location);
    				break;
    			case "Projects":
    				$project = new Project($params[ProjectDAO::PROJECT_ID], $params[ProjectDAO::INACTIVE]);
    				$project->setProjectName($params[ProjectDAO::PROJECT_NAME]);
    				$project->setDescription($params[ProjectDAO::PROJECT_DESCRIPTION]);
    				$daterange = new DateRange();
    				$daterange->setStartDate($params[ProjectDAO::START_DATE]);
    				$daterange->setEndDate($params[ProjectDAO::END_DATE]);
    				$project->setDateRange($daterange);
    				$pk = ProjectDAO::getProjectDAO()->saveProject($project);
    				break;
    			case "Purposes":
    				$purpose = new Purpose($params[PurposeDAO::PURPOSE_ID], $params[PurposeDAO::INACTIVE]);
    				$purpose->setPurpose($params[PurposeDAO::PURPOSE]);
    				$pk = PurposeDAO::getPurposeDAO()->savePurpose($purpose);
    				break;
    			case "WorkTypes":
    				$worktype = new WorkType($params[WorkTypeDAO::WORK_TYPE_ID], $params[WorkTypeDAO::INACTIVE]);
    				$worktype->setWorkType($params[WorkTypeDAO::WORK_TYPE]);
    				$pk = WorkTypeDAO::getWorkTypeDAO()->saveWorkType($worktype);
    				break;
    		}
    		//If the primary key is not null, then the save was successful
    		if (!is_null($pk))
    		{
    			$retval = Zend_Json::encode(array('PrimaryKey' => $pk));
			}
			else 
			{
				$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem saving your changes.  Please try again.'));
			}
		}
		else 
		{
			$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem saving your changes.  Please try again.'));
		}
			
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    		
    	
    }
    
    public function populatetubsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$inactive = $this->getRequest()->getParam('includeinactive', 0);
    	$blank = $this->getRequest()->getParam('includeblank', 0);
    	if (!isset($inactive) || $inactive == 0)
		{
			$tubs = LocationDAO::getLocationDAO()->getAllTUBSForSelect(FALSE);
		}
		else 
		{
			$tubs = LocationDAO::getLocationDAO()->getAllTUBSForSelect(TRUE);
		}
		if (empty($tubs))
		{
			$tubs = array();
		}
		if (isset($blank) && $blank == 1)
		{
			array_unshift($tubs, array('TUB' => ''));
		}
		$retval = Zend_Json::encode(array('Result' => $tubs));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
    
    /*
     * Updates a particular field in the given list.
     */
	public function updateitemAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$listname = $this->getRequest()->getParam('listname');
    	$column = $this->getRequest()->getParam('column');
    	$pk = $this->getRequest()->getParam('pk');
    	$newdata = $this->getRequest()->getParam('newdata');
    	
    	if (!empty($listname))
    	{
    		switch ($listname)
    		{
    			case "Departments":
    				$department = DepartmentDAO::getDepartmentDAO()->getDepartment($pk);
    				//If the location has been updated, we are actually updating the location id.
    				if ($column == LocationDAO::LOCATION)
    				{
    					$column = LocationDAO::LOCATION_ID;
    				}
    				$department->updateField($column, $newdata);
    				$pk = DepartmentDAO::getDepartmentDAO()->saveDepartment($department);
    				break;
    			case "Formats":
    				$format = FormatDAO::getFormatDAO()->getFormat($pk);
    				$format->updateField($column, $newdata);
    				$pk = FormatDAO::getFormatDAO()->saveFormat($format);
    				break;
    			case "Importances":
    				$importance = ImportanceDAO::getImportanceDAO()->getImportance($pk);
    				$importance->updateField($column, $newdata);
    				$pk = ImportanceDAO::getImportanceDAO()->saveImportance($importance);
    				break;
    			case "Locations":
    				$location = LocationDAO::getLocationDAO()->getLocation($pk);
    				$location->updateField($column, $newdata);
    				$pk = LocationDAO::getLocationDAO()->saveLocation($location);
    				break;
    			case "People":
    				$person = PeopleDAO::getPeopleDAO()->getPerson($pk);
    				$person->updateField($column, $newdata);
    				$pk = PeopleDAO::getPeopleDAO()->savePerson($person, $person);
    				//Remove any people stored in the namespace
    				PeopleNamespace::clearCurrentPerson();
    				PeopleNamespace::clearOriginalPerson();
    				break;
    			case "Projects":
    				$project = ProjectDAO::getProjectDAO()->getProject($pk);
    				$project->updateField($column, $newdata);
    				$pk = ProjectDAO::getProjectDAO()->saveProject($project);
    				break;
    			case "Purposes":
    				$purpose = PurposeDAO::getPurposeDAO()->getPurpose($pk);
    				$purpose->updateField($column, $newdata);
    				$pk = PurposeDAO::getPurposeDAO()->savePurpose($purpose);
    				break;
    			case "WorkTypes":
    				$worktype = WorkTypeDAO::getWorkTypeDAO()->getWorkType($pk);
    				$worktype->updateField($column, $newdata);
    				$pk = WorkTypeDAO::getWorkTypeDAO()->saveWorkType($worktype);
    				break;
    		}
    		//If the primary key is not null, then the save was successful
    		if (!is_null($pk))
    		{
    			$retval = Zend_Json::encode(array('PrimaryKey' => $pk));
			}
			else 
			{
				$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem saving your changes.  Please try again.'));
			}
		}
		else 
		{
			$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem saving your changes.  Please try again.'));
		}
			
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    }
    
    
	/*
     * Saves a new autotext.
     */
	public function saveautotextAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$listname = $this->getRequest()->getParam(AutotextDAO::AUTOTEXT_TYPE, '');
    	
    	if (!empty($listname))
    	{
    		$params = $this->getRequest()->getParams();
	    	$auth = Zend_Auth::getInstance();
    		$identity = $auth->getIdentity();
    		$owner = $identity[PeopleDAO::PERSON_ID];
    		
    		$autotext = new AutoText($params[AutotextDAO::AUTOTEXT_ID]);
    		$autotext->setCaption($params[AutotextDAO::CAPTION]);
    		$autotext->setDependentAutoTextID($params[AutotextDAO::DEPENDENT_AUTOTEXT_ID]);
    		$autotext->setOwnerID($owner);
    		$autotext->setAutoText($params[AutotextDAO::AUTOTEXT]);
    		$autotext->setAutoTextType($listname);
    		$autotext->setGlobal($params[AutotextDAO::IS_GLOBAL]);
    		$pk = AutotextDAO::getAutotextDAO()->saveAutotext($autotext);
   
    		//If the primary key is not null, then the save was successful
    		if (!is_null($pk))
    		{
    			$retval = Zend_Json::encode(array('PrimaryKey' => $pk));
			}
			else 
			{
				$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem saving your changes.  Please try again.'));
			}
		}
		else 
		{
			$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem saving your changes.  Please try again.'));
		}
			
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    }
    
	/*
     * Updates a particular field in the given autotext.
     */
	public function updateautotextAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$listname = $this->getRequest()->getParam('autotexttype');
    	$column = $this->getRequest()->getParam('column');
    	$pk = $this->getRequest()->getParam('pk');
    	$newdata = $this->getRequest()->getParam('newdata');
    	
    	Logger::log($this->getRequest()->getParams());
    	
    	if ($column == "DependentAutotext")
    	{
    		$column = AutotextDAO::DEPENDENT_AUTOTEXT_ID;
    	}

    	if (!empty($listname))
    	{
    		$autotext = AutotextDAO::getAutotextDAO()->getAutotextFromID($pk);
    		$autotext->updateField($column, $newdata);
    		$pk = AutotextDAO::getAutotextDAO()->saveAutotext($autotext);
   
    		//If the primary key is not null, then the save was successful
    		if (!is_null($pk))
    		{
    			$retval = Zend_Json::encode(array('PrimaryKey' => $pk));
			}
			else 
			{
				$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem saving your changes.  Please try again.'));
			}
		}
		else 
		{
			$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem saving your changes.  Please try again.'));
		}
			
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    }
    
	/*
     * Removes an autotext item.
     */
	public function removeautotextAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$listname = $this->getRequest()->getParam('autotexttype');
    	$autotextID = $this->getRequest()->getParam('autotextID');
    	
    	if (!empty($listname))
    	{
    		$rowsdeleted = AutotextDAO::getAutotextDAO()->deleteAutotext($autotextID);
    		
    		//If rows deleted = 0, return an error
    		if ($rowsdeleted == 0)
    		{
    			$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem deleting your rows.  Please try again.'));
			}
			else 
			{
				$retval = Zend_Json::encode(array());
			}
		}
		else 
		{
			$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem deleting your rows.  Please try again.'));
		}
			
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    }
}