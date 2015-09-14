<?php

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
    	elseif (is_null($this->workTypes)) 
    	{
    		$this->workTypes = array();
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
     * @access public
     * @param  WorkType workType
     */
    public function addWorkType(WorkType $workType)
    {
    	$worktypes = $this->getWorkTypes();
    	
		// Don't allow more than 1 work type to be added (bug 4210)
    	if (count($worktypes) == 0) 
    	{
	    	$worktypes[$workType->getPrimaryKey()] = $workType;
    		$this->setWorkTypes($worktypes);
    		$this->updateNamespaceRecord();
    	}
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