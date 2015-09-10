<?php

/**
 * Project
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class ProjectDAO extends Zend_Db_Table 
{
	const PROJECT_ID = 'ProjectID';
	const PROJECT_NAME = 'ProjectName';
	const PROJECT_DESCRIPTION = 'ProjectDescription';
	const START_DATE = 'StartDate';
	const END_DATE = 'EndDate';
	const INACTIVE = 'Inactive';
	
	/* The name of the table */
	protected $_name = "Projects";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "ProjectID";

    private static $projectDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return ProjectDAO
     */
    public static function getProjectDAO()
	{
		if (!isset(self::$projectDAO))
		{
			self::$projectDAO = new ProjectDAO();
		}
		return self::$projectDAO;
	}
	
    /**
     * Returns a list of projects
     *
     * @access public
     * @return array, NULL if no rows found
     */
    public function getAllProjects($includeinactive = FALSE)
    {
    	$select = $this->select();
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	$rows = $this->getAdapter()->fetchAssoc($select);
    	return $rows;
    }
    
	/**
     * Returns a list of projects formatted for a select list
     *
     * @access public
     * @return array to be used with JSON, NULL if none found
     */
    public function getAllProjectsForSelect($includeinactive = FALSE)
    {
    	$select = $this->select();
    	$select->from($this->_name, array('*'));
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$accesslevel = $identity[PeopleDAO::ACCESS_LEVEL];
    	$userrepository = $identity[LocationDAO::LOCATION_ID];
    	//If the user is not an admin or regular WPC user, then they should be limited to their repository.
    	if ($accesslevel != PeopleDAO::ACCESS_LEVEL_ADMIN && $accesslevel != PeopleDAO::ACCESS_LEVEL_REGULAR)
    	{
    		$select->setIntegrityCheck(FALSE);
    		$select->joinInner("CombinedRecords", 'CombinedRecords.ProjectID = Projects.ProjectID', array());
    		$select->where('HomeLocationID = ' . $userrepository);
    	}
    	if (!$includeinactive)
    	{
    		$select->where('Projects.Inactive = 0');
    	}
    	$select->distinct();
    	$select->order('ProjectName');
    	Logger::log($select->__toString());
    	$rows = $this->fetchAll($select);
    	$retval = NULL;
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }

    /**
     * Get a specific project
     *
     * @access public
     * @param  Integer projectID
     * @return Project project
     */
    public function getProject($projectID)
    {
        $select = $this->select();
    	$projectID = $this->getAdapter()->quote($projectID, 'INTEGER');
    	$select->where('ProjectID=' . $projectID);
    	$row = $this->fetchRow($select);
    	$retval = NULL;
    	if (!is_null($row))
    	{
    		$retval = $this->buildProject($row->toArray());
    	}
    	return $retval;
    }
    
	private function buildProject(array $data)
    {
    	$project = new Project($data[self::PROJECT_ID], $data[self::INACTIVE]);
    	$project->setProjectName($data[self::PROJECT_NAME]);
    	$project->setDescription($data[self::PROJECT_DESCRIPTION]);
    	$daterange = new DateRange();
    	$daterange->setStartDate($data[self::START_DATE], ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	$daterange->setEndDate($data[self::END_DATE], ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	$project->setDateRange($daterange);
    	return $project;
    }

    /**
     * @access public
     * @param  Project project
     * @return mixed
     */
    public function saveProject(Project $project)
    {
    	$start = $project->getDateRange()->getDatabaseStartDate();
    	if (empty($start))
    	{
    		$start = NULL;
    	}
        $end = $project->getDateRange()->getDatabaseEndDate();
    	if (empty($end))
    	{
    		$end = NULL;
    	}
        $array = array(
        	self::PROJECT_NAME => $project->getProjectName(),
        	self::PROJECT_DESCRIPTION => $project->getDescription(),
        	self::START_DATE => $start,
        	self::END_DATE => $end,
        	self::INACTIVE => $project->isInactive()
        );
        $db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try {
	   		$projectid = $project->getPrimaryKey();
	        if (is_null($projectid) || !$this->projectExists($projectid))
	        {
	        	$db->insert('Projects', $array);
	        	$projectid = $db->lastInsertId();
	        }
	        else 
	        {
	        	$db->update('Projects', $array, 'ProjectID=' . $projectid);
	        }
	        $db->commit();
	   	}
    	catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$projectid = NULL;
   		}
        return $projectid;
    }
    
	private function projectExists($projectID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(ProjectID)'));
    	$select->where('ProjectID=' . $projectID);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }

} 

?>