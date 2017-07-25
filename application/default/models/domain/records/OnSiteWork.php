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
 */

class OnSiteWork extends Record
{
    private $oswID = NULL;
	private $proposedHours = NULL;
	private $workDateRange = NULL;
	private $workTypes = NULL;
	private $formatID = NULL;
	
	public function __construct($primaryKey = NULL, $isInactive = FALSE, $isBeingEdited = FALSE)
	{
		$this->setRecordType('OSW');
		parent::__construct($primaryKey, $isInactive, $isBeingEdited);
	}
	
	
	/**
     * @access public
     * @return int
     */
    public function getOSWID()
    {
    	return $this->oswID;
    }

    /**
     * @access public
     * @param  int
     */
    public function setOSWID($oswID)
    {
    	$this->oswID = $oswID;
    }
	
    /**
     * @access public
     * @return mixed
     */
    public function getProposedHours()
    {
    	return $this->proposedHours;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getDateRange()
    {
    	return $this->workDateRange;
    }


    /**
     * @access public
     * @param  Integer proposedHours
     */
    public function setProposedHours($proposedHours)
    {
    	$this->proposedHours = $proposedHours;
    }

    /**
     * @access public
     * @param  DateRange dateRange
     */
    public function setDateRange(DateRange $dateRange)
    {
    	$this->workDateRange = $dateRange;
    }


    /**
     * @access public
     * @return mixed
     */
    public function getWorkTypes($updateNamespace = FALSE)
    {
    	if (is_null($this->workTypes))
    	{
            $this->workTypes = WorkTypeDAO::getWorkTypeDAO()->getWorkTypes($this->getOSWID());
            if ($updateNamespace)
            {
    		$this->updateNamespaceRecord();
            }
    	}
    	return $this->workTypes;
    }

    /**
     * @access public
     * @param  array workTypes
     */
    public function setWorkTypes($workTypes)
    {
    	$this->workTypes = $workTypes;
    }
    
    /**
     * Only one work type should be save (will change to scalar processing when time permits)
     * @access public
     * @param  a single workType object
     */
    public function setWorkType($workType)
    {        
    	$workTypes[$workType->getPrimaryKey()] = $workType;
    	$this->workTypes = $workTypes;
	$this->updateNamespaceRecord();
    }
    
    /*
     * Returns the array of work types that are in this item's work types
     * and are not in the array of compare work types
     * @param array - worktypes to compare
     * @return array - worktypes not in given array
     */
    public function getWorkTypeDifference(array $compareworktypes)
    {
    	return array_diff($this->getWorkTypes(), $compareworktypes);
    }

    /**
     * Don't allow more than 1 work type to be added (bug 4210 & 1080)
     * @access public
     * @param  WorkType workType
     */
    public function addWorkType(WorkType $workType)
    {	
    	$worktype[$workType->getPrimaryKey()] = $workType;
	$this->setWorkType($worktype);
	$this->updateNamespaceRecord();
    }

    /**
     * @access public
     * @param  Integer workTypeID
     */
    public function removeWorkType($workTypeID)
    {
    	$worktypes = $this->getWorkTypes();
    	unset($worktypes[$workTypeID]);
    	$this->setWorkTypes($worktypes);
    	$this->updateNamespaceRecord();
    }
    
    /**
     * @access public
     * @return int
     */
    public function getFormatID()
    {
    	return $this->formatID;
    }
    
    /**
     * @access public
     * @param  int formatID
     */
    public function setFormatID($formatID)
    {
    	$this->formatID = $formatID;
    }

	protected function updateNamespaceRecord()
    {
    	$currentosw = RecordNamespace::getCurrentOSW();
   		if ($currentosw->getOSWID() == $this->getOSWID())
   		{
   			RecordNamespace::setCurrentOSW($this);
   		}
    }
    
	
	public function getAuditTrail($actiontype = NULL)
    {
        return AuditTrailDAO::getAuditTrailDAO()->getAuditTrailItems('ItemIdentification', $this->getPrimaryKey(), $actiontype);
    }
    
	public function isEditable()
    {
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$accesslevel = $identity[PeopleDAO::ACCESS_LEVEL];
    	
    	//Repository/Repository Admin can never edit
    	if ($accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY 
    		|| $accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN) 
    	{
    		return false;
    	}
    	return parent::isEditable();
	}
	
	public function getActivityStatus()
    {
    	return "On-Site";
    }
}

?>