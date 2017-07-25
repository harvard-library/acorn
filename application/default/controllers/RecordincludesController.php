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
 * RecordincludesController
 *
 * @author vcrema
 * Created April 17, 2009
 *
 */
	

class RecordincludesController extends Zend_Controller_Action
{	
	public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
        {
            $this->_helper->redirector('index', 'index');
        }
    }
    
    
	/**
	 * Finds a list of author/artists that begin with the given data
	 */
    public function findauthorartistsAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the entered data
		$author = $this->getRequest()->getParam("query");
    	
		$authorartists = ItemDAO::getItemDAO()->getAuthorArtists($author);
		$resultset = array('Result' => $authorartists);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	/**
	 * Finds a list of work types for the current osw
	 */
    public function findworktypesAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$item = RecordNamespace::getCurrentOSW();
    	
    	$worktypes = array();
    	if (isset($item) && !is_null($item))
    	{
    		$worktypeobjects = $item->getWorkTypes(TRUE);
    		foreach ($worktypeobjects as $worktypeobject)
    		{
    			array_push($worktypes,
    				array(WorkTypeDAO::WORK_TYPE_ID => $worktypeobject->getPrimaryKey(), 
    					WorkTypeDAO::WORK_TYPE => $worktypeobject->getWorkType()) 
    				);
    		}
    	}
		// Don't allow more than 1 work type to be added (bug 4210)
    	if (!isset($item) || is_null($item) || $item->isEditable() and count($worktypes) == 0)
    	{
		    //Add a row to use as an 'add' row
	    	array_push($worktypes, array(WorkTypeDAO::WORK_TYPE_ID => 0, WorkTypeDAO::WORK_TYPE => '(Double click to add)'));
    	}
    	//Only use the results
    	$resultset = array('Result' => $worktypes);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	/**
	 * Finds a list of work assigned to for the current group or item
	 */
    public function findworkassignedtoAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the record type (item, osw, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	
		$item = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	
    	$workassignedto = array();
    	if (isset($item) && !is_null($item))
    	{
    		$workassignedtoobjects = $item->getWorkAssignedTo();
    			
    		foreach ($workassignedtoobjects as $workassignedtoobject)
    		{
    			array_push($workassignedto,
    				array(PeopleDAO::PERSON_ID => $workassignedtoobject->getPrimaryKey(), 
    				PeopleDAO::DISPLAY_NAME => $workassignedtoobject->getDisplayName())
    			);
    		}
    	}
		elseif ($recordtype == "group")
		{
			$item = GroupNamespace::getCurrentGroup();
			$workassignedtoobjects = $item->getWorkAssignedTo();
			foreach ($workassignedtoobjects as $workassignedtoobject)
   			{
   				array_push($workassignedto,
   					array(PeopleDAO::PERSON_ID => $workassignedtoobject->getPrimaryKey(), 
   					PeopleDAO::DISPLAY_NAME => $workassignedtoobject->getDisplayName()) 
   				);
   			}
		}
    	
		if ($recordtype == "group" 
    		|| (isset($item) && !is_null($item) && $item->isEditable()))
    	{
	    	//Add a row to use as an 'add' row
    		array_push($workassignedto, array(PeopleDAO::PERSON_ID => 0, PeopleDAO::DISPLAY_NAME => '(Double click to add)'));
    	}
    	//Only use the results
    	$resultset = array('Result' => $workassignedto);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }

    /**
     * Finds a list of people that wrote the proposal for the current item.
     */
    public function findproposedbyAction()
    {
    	//Don't display a new view
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    
    	//Determine the record type (item, osw, group)
    	$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	 
    	$item = NULL;
    	if ($recordtype == "item")
    	{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    	}
    	 
    	$proposedby = array();
    	if (isset($item) && !is_null($item))
    	{
    		$proposal = $item->getFunctions()->getProposal();
    		if (!is_null($proposal))
    		{
    			$proposedbyobjects = $proposal->getProposedBy();
    			 
    			foreach ($proposedbyobjects as $proposedbyobject)
    			{
    				array_push($proposedby,
    				array(PeopleDAO::PERSON_ID => $proposedbyobject->getPrimaryKey(),
    				PeopleDAO::DISPLAY_NAME => $proposedbyobject->getDisplayName())
    				);
    			}
    		}
    	}
    	elseif ($recordtype == "group")
    	{
    		$item = GroupNamespace::getCurrentGroup();
    		$proposedbyobjects = $item->getProposedBy();
    		foreach ($proposedbyobjects as $proposedbyobject)
    		{
    			array_push($proposedby,
    			array(PeopleDAO::PERSON_ID => $proposedbyobject->getPrimaryKey(),
    			PeopleDAO::DISPLAY_NAME => $proposedbyobject->getDisplayName())
    			);
    		}
    	}
    	 
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$accesslevel = $identity[PeopleDAO::ACCESS_LEVEL];
    	if ($recordtype == "group"
    			|| ((!isset($item) || is_null($item) || $item->isEditable())
    					&& $accesslevel != PeopleDAO::ACCESS_LEVEL_REPOSITORY
    					&& $accesslevel != PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN))
    	{
    		//Add a row to use as an 'add' row
    		array_push($proposedby, array(PeopleDAO::PERSON_ID => 0, PeopleDAO::DISPLAY_NAME => '(Double click to add)'));
    	}
    	//Only use the results
    	$resultset = array('Result' => $proposedby);
    	$retval = Zend_Json::encode($resultset);
    	//Make sure JS knows it is in JSON format.
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
    	->setBody($retval);
    }
    
	/**
	 * Finds a list of curators for the current osw or item
	 */
    public function findworkdonebyAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the record type (item, osw, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	
		$item = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    	}
    	
    	$workdoneby = array();
    	if (isset($item) && !is_null($item))
    	{
    		$report = $item->getFunctions()->getReport(TRUE);
    		if (!is_null($report))
    		{
    			$workdonebyobjects = $report->getReportConservators();
    			
    			//The ConservatorID is simply the array key of where the conservator is stored.
    			//This is used for updating or deleting the conservator from the array.
    			foreach ($workdonebyobjects as $conservatorid => $workdonebyobject)
    			{
    				array_push($workdoneby,
    					array(PeopleDAO::PERSON_ID => $workdonebyobject->getConservator()->getPrimaryKey(), 
    					PeopleDAO::DISPLAY_NAME => $workdonebyobject->getConservator()->getDisplayName(),
    					ReportDAO::DATE_COMPLETED => $workdonebyobject->getDateCompleted(),
    					ReportDAO::COMPLETED_HOURS => $workdonebyobject->getHoursWorked(),
    					"ConservatorID" => $conservatorid) 
    				);
    			}
    		}
    	}
		elseif ($recordtype == "group")
		{
			$item = GroupNamespace::getCurrentGroup();
			$workdonebyobjects = $item->getReportConservators();
			foreach ($workdonebyobjects as $conservatorid => $workdonebyobject)
   			{
   				array_push($workdoneby,
   					array(PeopleDAO::PERSON_ID => $workdonebyobject->getConservator()->getPrimaryKey(), 
   					PeopleDAO::DISPLAY_NAME => $workdonebyobject->getConservator()->getDisplayName(),
   					ReportDAO::DATE_COMPLETED => $workdonebyobject->getDateCompleted(),
    				ReportDAO::COMPLETED_HOURS => $workdonebyobject->getHoursWorked(),
   					"ConservatorID" => $conservatorid) 
   				);
   			}
		}
    	
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		$accesslevel = $identity[PeopleDAO::ACCESS_LEVEL];
    	if ($recordtype == "group" 
    		|| ((!isset($item) || is_null($item) || $item->isEditable()) 
    			&& $accesslevel != PeopleDAO::ACCESS_LEVEL_REPOSITORY
    			&& $accesslevel != PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN))
    	{
	    	//Add a row to use as an 'add' row
    		array_push($workdoneby, array(ReportDAO::DATE_COMPLETED => NULL, PeopleDAO::PERSON_ID => 0, PeopleDAO::DISPLAY_NAME => '(Double click to add)', "ConservatorID" => "-1"));
    	}
    	//Only use the results
    	$resultset = array('Result' => $workdoneby);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	/**
	 * Finds a list of importances for the current item
	 */
    public function findimportancesAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
		$item = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	
    	$importances = array();
    	if (isset($item) && !is_null($item))
    	{
    		$report = $item->getFunctions()->getReport(TRUE);
    		if (!is_null($report))
    		{
    			$importanceobjects = $report->getImportances();
    			foreach ($importanceobjects as $importanceobject)
    			{
    				array_push($importances,
    					array(ImportanceDAO::IMPORTANCE_ID => $importanceobject->getPrimaryKey(), 
    					ImportanceDAO::IMPORTANCE => $importanceobject->getImportance()) 
    				);
    			}
    		}
    	}
		elseif ($recordtype == "group")
		{
			$item = GroupNamespace::getCurrentGroup();
			$importanceobjects = $item->getImportances();
   			foreach ($importanceobjects as $importanceobject)
   			{
   				array_push($importances,
   					array(ImportanceDAO::IMPORTANCE_ID => $importanceobject->getPrimaryKey(), 
   					ImportanceDAO::IMPORTANCE => $importanceobject->getImportance()) 
   				);
   			}
		}
    	
    	$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		$accesslevel = $identity[PeopleDAO::ACCESS_LEVEL];
    	if ($recordtype == "group"
    		|| ($item->isEditable() 
    			&& $accesslevel != PeopleDAO::ACCESS_LEVEL_REPOSITORY
    			&& $accesslevel != PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN))
    	
    	{
	    	//Add a row to use as an 'add' row
    		array_push($importances, array(ImportanceDAO::IMPORTANCE_ID => 0, ImportanceDAO::IMPORTANCE => '(Double click to add)'));
    	}
    	//Only use the results
    	$resultset = array('Result' => $importances);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
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
		
    	$recordtype = $this->getRequest()->getParam("recordtype", "item");
    	
    	$item = NULL;
    	if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    	}
    	
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
    
	/**
	 * Finds a the final counts for the current item
	 */
    public function findfinalcountsAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
    	//Determine the record type (item, osw, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	
		$item = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    	}
    	
    	$counts = array();
    	$countsobject = NULL;
    	if (isset($item) && !is_null($item))
    	{
    		$report = $item->getFunctions()->getReport(TRUE);
    		if (!is_null($report) && !is_null($report->getPrimaryKey()))
    		{
    			$countsobject = $report->getCounts();
    		}
    	}
    	elseif ($recordtype == "group")
    	{
    		$item = GroupNamespace::getCurrentGroup();
    		$countsobject = $item->getFinalCounts();
    	}
    	
    	//If no counts object was found, use the counts
    	//from the identification
    	if (is_null($countsobject))
    	{
    		if ($recordtype == "item")
    		{
    			$countsobject = $item->getInitialCounts();
   				$item->getFunctions()->getReport()->setCounts($countsobject);
   				RecordNamespace::setCurrentItem($item);
   			}
    		elseif ($recordtype == "osw") 
    		{
    			$countsobject = new Counts();
    			$item->getFunctions()->getReport()->setCounts($countsobject);
   				RecordNamespace::setCurrentOSW($item);
    		}
    		else 
    		{
    			$countsobject = new Counts();
    			$item->setFinalCounts($countsobject);
    		}
    	}
    	
    	array_push($counts, array(
			ReportDAO::COUNT_TYPE => "Volumes",
			ReportDAO::TOTAL_COUNT => $countsobject->getVolumeCount(),
			ReportDAO::COUNT_DESCRIPTION => $countsobject->getVolumeDescription()
		));
		array_push($counts, array(
			ReportDAO::COUNT_TYPE => "Sheets",
			ReportDAO::TOTAL_COUNT => $countsobject->getSheetCount(),
			ReportDAO::COUNT_DESCRIPTION => $countsobject->getSheetDescription()
		));
		array_push($counts, array(
			ReportDAO::COUNT_TYPE => "Photos",
			ReportDAO::TOTAL_COUNT => $countsobject->getPhotoCount(),
			ReportDAO::COUNT_DESCRIPTION => $countsobject->getPhotoDescription()
		));
		array_push($counts, array(
			ReportDAO::COUNT_TYPE => "Boxes",
			ReportDAO::TOTAL_COUNT => $countsobject->getBoxCount(),
			ReportDAO::COUNT_DESCRIPTION => $countsobject->getBoxDescription()
		));
		array_push($counts, array(
			ReportDAO::COUNT_TYPE => "Other",
			ReportDAO::TOTAL_COUNT => $countsobject->getOtherCount(),
			ReportDAO::COUNT_DESCRIPTION => $countsobject->getOtherDescription()
		));
		array_push($counts, array(
			ReportDAO::COUNT_TYPE => "Housings",
			ReportDAO::TOTAL_COUNT => $countsobject->getHousingCount(),
			ReportDAO::COUNT_DESCRIPTION => $countsobject->getHousingDescription()
		));
    	//Only use the results
    	$resultset = array('Result' => $counts);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	/**
	 * Finds a the initial counts for the current item
	 */
    public function findinitialcountsAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
    	//Determine the record type (item, osw, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	
		$item = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    	}
		elseif ($recordtype == "group")
		{
			$item = GroupNamespace::getCurrentGroup();
		}
    	
    	$counts = array();
    	$countsobject = NULL;
    	if (isset($item) && !is_null($item))
    	{
    		$countsobject = $item->getInitialCounts(TRUE);
    	}
    	else
    	{
    		$countsobject = new Counts();
    	}
    	
    	array_push($counts, array(
			ItemDAO::COUNT_TYPE => "Volumes",
			ItemDAO::TOTAL_COUNT => $countsobject->getVolumeCount(),
			ItemDAO::COUNT_DESCRIPTION => $countsobject->getVolumeDescription()
		));
		array_push($counts, array(
			ItemDAO::COUNT_TYPE => "Sheets",
			ItemDAO::TOTAL_COUNT => $countsobject->getSheetCount(),
			ItemDAO::COUNT_DESCRIPTION => $countsobject->getSheetDescription()
		));
		array_push($counts, array(
			ItemDAO::COUNT_TYPE => "Photos",
			ItemDAO::TOTAL_COUNT => $countsobject->getPhotoCount(),
			ItemDAO::COUNT_DESCRIPTION => $countsobject->getPhotoDescription()
		));
		array_push($counts, array(
			ItemDAO::COUNT_TYPE => "Boxes",
			ItemDAO::TOTAL_COUNT => $countsobject->getBoxCount(),
			ItemDAO::COUNT_DESCRIPTION => $countsobject->getBoxDescription()
		));
		array_push($counts, array(
			ItemDAO::COUNT_TYPE => "Other",
			ItemDAO::TOTAL_COUNT => $countsobject->getOtherCount(),
			ItemDAO::COUNT_DESCRIPTION => $countsobject->getOtherDescription()
		));
		//Only use the results
    	$resultset = array('Result' => $counts);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
    public function addworktypeAction()
    {
    	//Don't display a new view
	$this->_helper->viewRenderer->setNoRender();
	//Don't use the default layout since this isn't a view call
	$this->_helper->layout->disableLayout();
		
	//Determine the work type
	$worktypeid = $this->getRequest()->getParam("worktypeid");
    	
	$item = RecordNamespace::getCurrentOSW();
    	
    	if (isset($worktypeid) && isset($item) && !is_null($item))
    	{
            $worktype = WorkTypeDAO::getWorkTypeDAO()->getWorkType($worktypeid);
            $item->setWorkType($worktype);
    	}
    }
    
    public function removeworktypeAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the work type
		$worktypeid = $this->getRequest()->getParam("worktypeid");
    	
		$item = RecordNamespace::getCurrentOSW();
    	
    	if (isset($worktypeid) && isset($item) && !is_null($item))
    	{
    		$item->removeWorkType($worktypeid);
    	} 	
    }
    
	public function addworkassignedtoAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the record type (item, osw, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	$item = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    	}
    	else 
    	{
    		$item = GroupNamespace::getCurrentGroup();
    	}
    	
    	//Determine the work assigned to
		$workassignedtoid = $this->getRequest()->getParam("workassignedtoid");
	    if (isset($workassignedtoid) && isset($item) && !is_null($item))
    	{
    		$workassignedto = PeopleDAO::getPeopleDAO()->getPerson($workassignedtoid);
	    	$item->addWorkAssignedTo($workassignedto);
    	}
    	
    }
    
    public function removeworkassignedtoAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the record type (item, osw, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	$item = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    	}
    	else 
    	{
    		$item = GroupNamespace::getCurrentGroup();
    	}
    	
    	//Determine the work assigned to
		$workassignedtoid = $this->getRequest()->getParam("workassignedtoid");
    	if (isset($workassignedtoid) && isset($item) && !is_null($item))
    	{
    		$item->removeWorkAssignedTo($workassignedtoid);
    	} 	
    }
    
    public function addproposedbyAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		Logger::log($this->getRequest()->getParams());
		//Determine the record type (item, osw, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	
    	$item = NULL;
    	$proposal = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    		$functions = $item->getFunctions();
    		$proposal = $functions->getProposal();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    		$functions = $item->getFunctions();
    		$proposal = $functions->getProposal();
    	}
    	else 
    	{
    		$item = GroupNamespace::getCurrentGroup();
    	}
    	
    	//Determine the work done by
		$proposedbyid = $this->getRequest()->getParam("proposedbyid");
	    if (isset($proposedbyid) && isset($item) && !is_null($item))
    	{
    		$proposedby = PeopleDAO::getPeopleDAO()->getPerson($proposedbyid);
    		
    		if (!is_null($proposal))
    		{
	    		$proposal->addProposedBy($proposedby);
	    		$functions->setProposal($proposal);
	    		$item->setFunctions($functions);
		    	if ($recordtype == "item")
				{
		    		RecordNamespace::setCurrentItem($item);
		    	}
		    	elseif ($recordtype == "osw")
		    	{
		    		RecordNamespace::setCurrentOSW($item);
		    	}
    		}
    		else 
    		{
    			$item->addProposedBy($proposedby);
    			GroupNamespace::setCurrentGroup($item);
    		}
    	}
    }
    
    public function removeproposedbyAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the record type (item, osw, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	$item = NULL;
    	$proposal = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    		//Determine the functions/proposal
			$functions = $item->getFunctions();
    		$proposal = $functions->getProposal();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    		//Determine the functions/proposal
			$functions = $item->getFunctions();
    		$proposal = $functions->getProposal();
    	}
    	else 
    	{
    		$item = GroupNamespace::getCurrentGroup();
    	}
    	
    	$totalhours = '';
    	
    	//Determine the work done by
		$proposedbyid = $this->getRequest()->getParam("proposedbyid");
	    if (isset($proposedbyid) && isset($item) && !is_null($item))
    	{
    		if (!is_null($proposal))
    		{
	    		$proposal->removeProposedBy($proposedbyid);
	    		$functions->setProposal($proposal);
	    		$item->setFunctions($functions);
		    	if ($recordtype == "item")
				{
		    		RecordNamespace::setCurrentItem($item);
		    	}
		    	elseif ($recordtype == "osw")
		    	{
		    		RecordNamespace::setCurrentOSW($item);
		    	}
		    }
    		else 
    		{
    			$item->removeProposedBy($proposedbyid);
    			GroupNamespace::setCurrentGroup($item);
    		}
    	}
    }
    
    public function addworkdonebyAction()
    {
    	//Don't display a new view
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    
    	//Determine the record type (item, osw, group)
    	$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	 
    	$item = NULL;
    	$report = NULL;
    	if ($recordtype == "item")
    	{
    		$item = RecordNamespace::getCurrentItem();
    		$functions = $item->getFunctions();
    		$report = $functions->getReport();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    		$functions = $item->getFunctions();
    		$report = $functions->getReport();
    	}
    	else
    	{
    		$item = GroupNamespace::getCurrentGroup();
    	}
    	 
    	$totalhours = '';
    	 
    	//Determine the work done by
    	$conservatorid = $this->getRequest()->getParam("conservatorid");
    	$workdonebyid = $this->getRequest()->getParam("workdonebyid");
    	$completedhours = $this->getRequest()->getParam("completedhours", 0);
    	$completeddate = $this->getRequest()->getParam("completeddate", NULL);
    	Logger::log($this->getRequest()->getParams());
    	/*if (!empty($completeddate))
    	{
	  		$zenddate = new Zend_Date($completeddate, ACORNConstants::$ZEND_DATE_FORMAT);
	    	$completeddate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	}
    	else
    	{
    		$completeddate = NULL;
    	}*/
    	if (empty($completeddate))
    	{
    		$completeddate = NULL;
    	}
    	if ($workdonebyid > 0 && isset($item) && !is_null($item))
    	{
    		$workdoneby = PeopleDAO::getPeopleDAO()->getPerson($workdonebyid);
    		$conservator = new ReportConservator();
    		$conservator->setConservator($workdoneby);
    		$conservator->setHoursWorked($completedhours);
    		$conservator->setDateCompleted($completeddate);
    
    		if (!is_null($report))
    		{
    			$conservatorid = $report->addConservator($conservator, $conservatorid);
    			$functions->setReport($report);
    			$item->setFunctions($functions);
    			if ($recordtype == "item")
    			{
    				RecordNamespace::setCurrentItem($item);
    			}
    			elseif ($recordtype == "osw")
    			{
    				RecordNamespace::setCurrentOSW($item);
    			}
    		  
    			$totalhours = $report->getTotalHours();
    		}
    		else
    		{
    			$conservatorid = $item->addReportConservator($conservator, $conservatorid);
    		}
    	}
    	 
    	 
    	$retval = Zend_Json::encode(array('CompletedHours' => $totalhours, 'ConservatorID' => $conservatorid));
    	//Make sure JS knows it is in JSON format.
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
    	->setBody($retval);
    }
    
    public function removeworkdonebyAction()
    {
    	//Don't display a new view
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    
    	//Determine the record type (item, osw, group)
    	$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	$item = NULL;
    	if ($recordtype == "item")
    	{
    		$item = RecordNamespace::getCurrentItem();
    		//Determine the functions/report
    		$functions = $item->getFunctions();
    		$report = $functions->getReport();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    		//Determine the functions/report
    		$functions = $item->getFunctions();
    		$report = $functions->getReport();
    	}
    	else
    	{
    		$item = GroupNamespace::getCurrentGroup();
    	}
    	 
    	$totalhours = '';
    	 
    	Logger::log($this->getRequest()->getParams());
    	//Determine the work done by
    	$conservatorid = $this->getRequest()->getParam("conservatorid");
    	//$workdonebyid = $this->getRequest()->getParam("workdonebyid");
    	//$date = $this->getRequest()->getParam("completeddate");
    	//$zendcompletedate = new Zend_Date($date, ACORNConstants::$ZEND_DATE_FORMAT);
		//$date = $zendcompletedate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
		if (isset($conservatorid) && isset($item) && !is_null($item))
    	{
    		if (!is_null($report))
    		{
    			$report->removeConservator($conservatorid);
    			$functions->setReport($report);
    			$item->setFunctions($functions);
    			if ($recordtype == "item")
    			{
    				RecordNamespace::setCurrentItem($item);
    			}
    			elseif ($recordtype == "osw")
    			{
    				RecordNamespace::setCurrentOSW($item);
    			}
    			$totalhours = $report->getTotalHours();
    			 
    		}
    		else
    		{
    			$item->removeConservator($conservatorid);
    		}
    	}
    
    	 
    	$retval = Zend_Json::encode(array('CompletedHours' => $totalhours));
    	//Make sure JS knows it is in JSON format.
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
    	->setBody($retval);
    }
    
	public function addimportanceAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
    	//Determine the record type (item, osw, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	$item = NULL;
    	$report = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    		//Determine the functions/report
			$functions = $item->getFunctions();
    		$report = $functions->getReport();
    	}
    	else 
    	{
    		$item = GroupNamespace::getCurrentGroup();
    	}
    	
		//Determine the importance
		$importanceid = $this->getRequest()->getParam("importanceid");
    	
		if (isset($importanceid) && isset($item) && !is_null($item))
		{
			$importance = ImportanceDAO::getImportanceDAO()->getImportance($importanceid);
	    	if (!is_null($report))
	    	{
	    		$report->addImportance($importance);
	    		$functions->setReport($report);
	    		$item->setFunctions($functions);
		    	RecordNamespace::setCurrentItem($item);
	    	}
	    	elseif ($recordtype == "group")
	    	{
	    		$item->addImportance($importance);
	    	}
		}
    }
    
    public function removeimportanceAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the record type (item, osw, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	$item = NULL;
    	$report = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    		//Determine the functions/report
			$functions = $item->getFunctions();
    		$report = $functions->getReport();
    	}
    	else 
    	{
    		$item = GroupNamespace::getCurrentGroup();
    	}
    	
		//Determine the importance
		$importanceid = $this->getRequest()->getParam("importanceid");
    	
    	if (isset($importanceid) && isset($item) && !is_null($item))
    	{
    		if (!is_null($report))
    		{
	    		$report->removeImportance($importanceid);
	    		$functions->setReport($report);
	    		$item->setFunctions($functions);
		    	RecordNamespace::setCurrentItem($item);
    		}
    		else 
    		{
    			$item->removeImportance($importanceid);
    		}
    	} 	
    }
    
    public function updateinitialcountAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the record type (item, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	$item = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	elseif ($recordtype == "group") 
    	{
    		$item = GroupNamespace::getCurrentGroup();
    	}
    	
    	//Determine the counttype, total count and description
		$counttype = $this->getRequest()->getParam("counttype");
	    $totalcount = $this->getRequest()->getParam("totalcount", 0);
	    $description = $this->getRequest()->getParam("description", NULL);
	    if (empty($description))
	    {
	    	$description = NULL;
	    }
	    if (isset($counttype) && isset($item) && !is_null($item))
    	{
    		$counts = $item->getInitialCounts();
    		switch ($counttype)
    		{
    			case "Volumes":
    				$counts->setVolumeCount($totalcount);
    				$counts->setVolumeDescription($description);
    				break;
    			case "Sheets":
    				$counts->setSheetCount($totalcount);
    				$counts->setSheetDescription($description);
    				break;
    			case "Photos":
    				$counts->setPhotoCount($totalcount);
    				$counts->setPhotoDescription($description);
    				break;
    			case "Boxes":
    				$counts->setBoxCount($totalcount);
    				$counts->setBoxDescription($description);
    				break;
    			case "Other":
    				$counts->setOtherCount($totalcount);
    				$counts->setOtherDescription($description);
    				break;
    		}
    		$item->setInitialCounts($counts);
    		if ($recordtype == "item")
    		{
    			RecordNamespace::setCurrentItem($item);
    		}
    		elseif ($recordtype == "group")
    		{
    			GroupNamespace::setCurrentGroup($item);
    		}
    	}
    }
    
	public function updatefinalcountAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the record type (item, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "group");
    	$item = NULL;
    	$report = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    		//Determine the functions/report
			$functions = $item->getFunctions();
    		$report = $functions->getReport();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    		//Determine the functions/report
			$functions = $item->getFunctions();
    		$report = $functions->getReport();
    	
    	}
    	else 
    	{
    		$item = GroupNamespace::getCurrentGroup();
    	}
    	//Determine the counttype, total count and description
		$counttype = $this->getRequest()->getParam("counttype");
	    $totalcount = $this->getRequest()->getParam("totalcount", 0);
	    $description = $this->getRequest()->getParam("description", NULL);
	    if (empty($description))
	    {
	    	$description = NULL;
	    }
	    if (isset($counttype) && isset($item) && !is_null($item))
    	{
    		if(!is_null($report))
    		{ 
    			$counts = $report->getCounts();
    		}
    		else 
    		{
    			$counts = $item->getFinalCounts();
    		}
    		switch ($counttype)
    		{
    			case "Volumes":
    				$counts->setVolumeCount($totalcount);
    				$counts->setVolumeDescription($description);
    				break;
    			case "Sheets":
    				$counts->setSheetCount($totalcount);
    				$counts->setSheetDescription($description);
    				break;
    			case "Photos":
    				$counts->setPhotoCount($totalcount);
    				$counts->setPhotoDescription($description);
    				break;
    			case "Boxes":
    				$counts->setBoxCount($totalcount);
    				$counts->setBoxDescription($description);
    				break;
    			case "Other":
    				$counts->setOtherCount($totalcount);
    				$counts->setOtherDescription($description);
    				break;
    			case "Housings":
    				$counts->setHousingCount($totalcount);
    				$counts->setHousingDescription($description);
    				break;
    		}
    		if ($recordtype == "item")
			{
	    		$report->setCounts($counts);
    			$functions->setReport($report);
    			$item->setFunctions($functions);
    			RecordNamespace::setCurrentItem($item);
    		}
	    	elseif ($recordtype == "osw")
	    	{
	    		$report->setCounts($counts);
    			$functions->setReport($report);
    			$item->setFunctions($functions);
    			RecordNamespace::setCurrentOSW($item);
    		}
    		else 
    		{
    			$item->setFinalCounts($counts);
    			GroupNamespace::setCurrentGroup($item);
    		}
    	}
    }
    
    /**
     * Attempts to add a call number to the item.
     * Determines if the given call number is a duplicate of a call number
     * that exists in the database.
     * Sends a JSON response of 'Duplicate'=>$itemID if it is a duplicate, 'Duplicate'=>NULL otherwise.
     */
	public function addcallnumberAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    	
    	$callnumber = $this->getRequest()->getParam('callnumber', NULL);
    	 
    	$recordtype = $this->getRequest()->getParam("recordtype", "item");
    	
    	$item = NULL;
    	if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    	}
    	
    	$existingduplicatecallnumbers = $item->getDuplicateCallNumbers();
    	
    	$recordid = NULL;
    	$dup = NULL;
    	//If the item hasn't been overridden already, then check to 
    	//see if it is in another Item.
    	if (!is_null($callnumber) && !in_array($callnumber, $existingduplicatecallnumbers))
    	{
    		$exclusionids = !is_null($item->getPrimaryKey()) ? array($item->getPrimaryKey()) : NULL;
    		$dup = CombinedRecordsDAO::getCombinedRecordsDAO()->callNumberExists($callnumber, $exclusionids);
    	}
    	
    	//As long as it is not a duplicate, then it can be added to the call number array.
    	if (is_null($dup))
    	{
    		$item->addCallNumber($callnumber);
    	}
    	
    	if ($recordtype == "item")
    	{
    		RecordNamespace::setCurrentItem($item);
    	}
    	elseif ($recordtype == "osw")
    	{
    		RecordNamespace::setCurrentOSW($item);
    	}
    	 
    	$data = array('Duplicate' => $dup);
    	$jsonstring = Zend_Json::encode($data);
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
    	->setBody($jsonstring);
    }
    
    public function removecallnumberAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the work type
		$callnumber = $this->getRequest()->getParam("callnumber");
    	
    	$recordtype = $this->getRequest()->getParam("recordtype", "item");
    	$item = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    	}
    	
    	if (isset($callnumber) && isset($item) && !is_null($item))
    	{
    		$item->removeCallNumber($callnumber);
    	} 	
    	
    	if ($recordtype == "item")
    	{
    		RecordNamespace::setCurrentItem($item);
    	}
    	elseif ($recordtype == "osw")
    	{
    		RecordNamespace::setCurrentOSW($item);
    	}
    }
}
?>