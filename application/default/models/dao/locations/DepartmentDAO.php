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
 * DepartmentDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
class DepartmentDAO extends Zend_Db_Table 
{
	const DEPARTMENT_ID = 'DepartmentID';
	const DEPARTMENT_NAME = 'DepartmentName';
	const SHORT_NAME = 'ShortName';
	const ACRONYM = 'Acronym';
	const INACTIVE = 'Inactive';
	
    /* The name of the table */
	protected $_name = "Departments";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "DepartmentID";

    private static $departmentDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return DepartmentDAO
     */
    public static function getDepartmentDAO()
	{
		if (!isset(self::$departmentDAO))
		{
			self::$departmentDAO = new DepartmentDAO();
		}
		return self::$departmentDAO;
	}

	/**
     * Returns a list of departments
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @param  Integer locationID <opt>, if null, returns all departments
     * @return array (key = DepartmentID, value = Department)
     */
    public function getDepartments($includeinactive = FALSE, $locationID = NULL)
    {
        $select = $this->select();
    	if (!is_null($locationID))
        {
        	$locationID = $this->getAdapter()->quote($locationID, 'INT');
        	$select->where('LocationID = ?', $locationID);
        }
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	$select->order(self::DEPARTMENT_NAME);
    	Logger::log($select->__toString());
    	$rows = $this->fetchAll($select);
    	$departments = array();
    	if (!is_null($rows))
    	{
    		$rowarray = $rows->toArray();
    		foreach ($rowarray as $row)
    		{
    			$dept = $this->buildDepartment($row);
    			$departments[$dept->getPrimaryKey()] = $dept;
    		}
    	}
    	return $departments;
    }
    
	/**
     * Returns a list of departments formatted for a select list
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @param  Integer locationID <opt>, if null, returns all departments
     * @return array to be used with JSON, NULL if none found
     */
    public function getDepartmentsForSelect($includeinactive = FALSE, $locationID = NULL, $orderby = 'DepartmentName')
    {
        $select = $this->select();
        $select->from($this->_name, array('*'));
        $select->setIntegrityCheck(FALSE);
        $select->joinInner('Locations', 'Departments.LocationID = Locations.LocationID', array('Location'));
        if (!is_null($locationID))
        {
        	$locationID = $this->getAdapter()->quote($locationID, 'INTEGER');
        	$select->where('Departments.LocationID = ?', $locationID);
        }
    	if (!$includeinactive)
    	{
    		$select->where('Departments.Inactive = 0');
    	}
    	$select->order($orderby);
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
     * Returns a list of repository-departments formatted for a select list
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @param  Integer locationID <opt>, if null, returns all departments
     * @return array to be used with JSON, NULL if none found
     */
    public function getRepositoryDepartmentsForSelect($includeinactive = FALSE)
    {
        $sql = 'SELECT DepartmentID, CONCAT(Location, \' - \', DepartmentName) AS DepartmentName FROM Departments d';
        $sql .= ' INNER JOIN Locations l ON d.LocationID = l.LocationID';
        if(!$includeinactive)
        {
        	$sql .= ' WHERE d.Inactive = 0';
        }
        $sql .= ' ORDER BY Location, DepartmentName';
        Logger::log($sql, Zend_Log::DEBUG);
    	$db = $this->getAdapter();
    	$stmt = $db->query($sql);
    	$rows = $stmt->fetchAll();
    	
        return $rows;
    }
    
	/**
     * Gets the department with the given id
     *
     * @access public
     * @param  Integer departmentID
     * @return Department department
     */
    public function getDepartment($departmentID)
    {
    	$select = $this->select();
    	$departmentID = $this->getAdapter()->quote($departmentID, 'INTEGER');
    	$select->where('DepartmentID=' . $departmentID);
    	$row = $this->fetchRow($select);
    	$department = NULL;
    	if (!is_null($row))
    	{
    		$department = $this->buildDepartment($row->toArray());
    	}
    	return $department;
    }
    
    private function buildDepartment(array $data)
    {
    	$department = new Department($data[self::DEPARTMENT_ID], $data[self::INACTIVE]);
    	$department->setAcronym($data[self::ACRONYM]);
    	$department->setDepartmentName($data[self::DEPARTMENT_NAME]);
    	$department->setLocationID($data[LocationDAO::LOCATION_ID]);
    	$department->setShortName($data[self::SHORT_NAME]);
    	return $department;
    }

    /**
     * @access public
     * @param  Department department
     * @return mixed
     */
    public function saveDepartment(Department $department)
    {
        $array = array(
        	self::DEPARTMENT_NAME => $department->getDepartmentName(),
        	self::SHORT_NAME => $department->getShortName(),
        	self::ACRONYM => $department->getAcronym(),
        	self::INACTIVE => $department->isInactive(),
        	LocationDAO::LOCATION_ID => $department->getLocationID()
        );
        $db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try {
	   		$departmentid = $department->getPrimaryKey();
	        if (is_null($departmentid) || !$this->departmentExists($departmentid))
	        {
	        	$db->insert('Departments', $array);
	        	$departmentid = $db->lastInsertId();
	        }
	        else 
	        {
	        	$db->update('Departments', $array, 'DepartmentID=' . $departmentid);
	        }
	        $db->commit();
	   	}
    	catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$departmentid = NULL;
   		}
        return $departmentid;
    }
    
	private function departmentExists($departmentID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(DepartmentID)'));
    	$select->where('DepartmentID=' . $departmentID);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }

} 

?>