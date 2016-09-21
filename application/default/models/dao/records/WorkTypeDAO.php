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
 * WorkTypeDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
class WorkTypeDAO extends Zend_Db_Table
{
	const WORK_TYPE_ID = "WorkTypeID";
	const WORK_TYPE = "WorkType";
	const INACTIVE = "Inactive";
	
    /* The name of the table */
	protected $_name = "WorkTypes";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "WorkTypeID";

    private static $worktypeDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return WorkTypeDAO
     */
    public static function getWorkTypeDAO()
	{
		if (!isset(self::$worktypeDAO))
		{
			self::$worktypeDAO = new WorkTypeDAO();
		}
		return self::$worktypeDAO;
	}

	/**
     * Returns a list of worktypes
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return array, NULL if no rows found  (key = WorkTypeID, array of other values)
     */
    public function getAllWorkTypes($includeinactive = FALSE)
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
     * Returns a list of worktype formatted for a select list
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return JSON ready array
     */
    public function getAllWorkTypesForSelect($includeinactive = FALSE)
    {
    	$select = $this->select();
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	$select->order('WorkType');
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$rows = $this->fetchAll($select);
    	$retval = NULL;
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }

    /**
     * @access public
     * @param  Integer workTypeID
     * @return WorkType
     */
    public function getWorkType($workTypeID)
    {
        $select = $this->select();
        $workTypeID = $this->getAdapter()->quote($workTypeID, 'INTEGER');
	    $select->where('WorkTypeID=' . $workTypeID);
	    Logger::log($select->__toString(), Zend_Log::DEBUG);
	    $worktype = NULL;
	    $row = $this->fetchRow($select);
	    if (!is_null($row))
	    {
	    	$worktype = $this->buildWorkType($row->toArray());
	    }
	    return $worktype;
    }

    /**
     * @access public
     * @param  WorkType worktype
     * @return mixed
     */
    public function saveWorkType(WorkType $workType)
    {
        $array = array(
        	self::WORK_TYPE => $workType->getWorkType(),
        	self::INACTIVE => $workType->isInactive()
        );
        $db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try {
	   		$workTypeid = $workType->getPrimaryKey();
	        if (is_null($workTypeid) || !$this->workTypeExists($workTypeid))
	        {
	        	$db->insert('WorkTypes', $array);
	        	$workTypeid = $db->lastInsertId();
	        }
	        else 
	        {
	        	$db->update('WorkTypes', $array, 'WorkTypeID=' . $workTypeid);
	        }
	        $db->commit();
	   	}
    	catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$workTypeid = NULL;
   		}
        return $workTypeid;
    }
    
	private function workTypeExists($workTypeID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(WorkTypeID)'));
    	$select->where('WorkTypeID=' . $workTypeID);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }

	/**
     * Returns the work types with the given oswid.
     *
     * @access public
     * @param  Integer oswID
     * @return array of ReportConservator objects
     */
    public function getWorkTypes($oswID)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from($this, array('*'));
    	$select->joinInner('OSWWorkTypes', 'WorkTypes.WorkTypeID = OSWWorkTypes.WorkTypeID', array());
    	$oswID = $this->getAdapter()->quote($oswID, 'INTEGER');
	    $select->where('OSWID=' . $oswID);
    	$rows = $this->fetchAll($select);
    	$worktypearray = array();
	    if (!is_null($rows))
	    {
	    	$rowarray = $rows->toArray();
	    	foreach ($rowarray as $row)
	    	{
	    		$worktype = $this->buildWorkType($row);
	    		$worktypearray[$row[self::WORK_TYPE_ID]] = $worktype;
	    	}
	    }
	    return $worktypearray;
    }
    
    private function buildWorkType($data)
    {
    	$worktype = new WorkType($data[self::WORK_TYPE_ID], $data[self::INACTIVE]);
	    $worktype->setWorkType($data[self::WORK_TYPE]);
	    return $worktype;
    }
} 

?>