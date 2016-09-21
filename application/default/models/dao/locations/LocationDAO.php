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
 * LocationDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
class LocationDAO extends Zend_Db_Table 
{
	const LOCATION_ID = 'LocationID';
	const LOCATION = 'Location';
	const INACTIVE = 'Inactive';
	const TUB = 'TUB';
	const SHORT_NAME = 'ShortName';
	const ACRONYM = 'Acronym';
	const IS_REPOSITORY = 'IsRepository';
	const IS_WORK_LOCATION = 'IsWorkLocation';
	
    /* The name of the table */
	protected $_name = "Locations";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "LocationID";
    
    private static $locationDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return LocationDAO
     */
    public static function getLocationDAO()
	{
		if (!isset(self::$locationDAO))
		{
			self::$locationDAO = new LocationDAO();
		}
		return self::$locationDAO;
	}

	/**
     * Returns a list of locations
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return array, NULL if no rows found  (key = LocationID, value = Location)
     */
    public function getAllLocations($includeinactive = FALSE, $orderby = array('LocationID'))
    {
    	$select = $this->select();
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	$select->order($orderby);
    	$rows = $this->fetchAll($select);
    	$retval = array();
    	if (!is_null($rows))
    	{
    		$rowarray = $rows->toArray();
    		foreach ($rowarray as $row)
    		{
    			$loc = $this->buildLocation($row);
    			$retval[$loc->getPrimaryKey()] = $loc;
    		}
    	}
    	return $retval;
    }
    
	/**
     * Returns a list of location formatted for a select list
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return array to be used with JSON, NULL if none found
     */
    public function getAllLocationsForSelect($includeinactive = FALSE)
    {
    	$select = $this->select();
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	$select->order('Location');
    	$rows = $this->fetchAll($select);
    	$retval = NULL;
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }
    
	/**
     * Returns a list of repositories formatted for a select list
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return array to be used with JSON, NULL if none found
     */
    public function getAllRepositoriesForSelect($includeinactive = FALSE)
    {
    	$select = $this->select();
    	$where = 'IsRepository = 1';
    	if (!$includeinactive)
    	{
    		$where .= ' AND Inactive = 0';
    	}
    	$select->where($where);
    	$select->order('Location');
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
     * Gets the location with the given id
     *
     * @access public
     * @param  Integer locationID
     * @return Location location
     */
    public function getLocation($locationID)
    {
    	$select = $this->select();
    	$locationID = $this->getAdapter()->quote($locationID, 'INTEGER');
    	$select->where('LocationID=' . $locationID);
    	$row = $this->fetchRow($select);
    	$retval = NULL;
    	if (!is_null($row))
    	{
    		$data = $row->toArray();
    		$retval = $this->buildLocation($data);
    	}
    	return $retval;
    }
    
    public function buildLocation(array $data)
    {
    	$location = new Location($data[self::LOCATION_ID], $data[self::INACTIVE]);
    	$location->setLocation($data[self::LOCATION]);
    	$location->setAcronym($data[self::ACRONYM]);
    	$location->setShortName($data[self::SHORT_NAME]);
    	$location->setTUB($data[self::TUB]);
    	return $location;
    }

    /**
     * @access public
     * @param  Location location
     * @return mixed locationID
     */
    public function saveLocation(Location $location)
    {
        $array = array(
        	self::LOCATION => $location->getLocation(),
        	self::SHORT_NAME => $location->getShortName(),
        	self::ACRONYM => $location->getAcronym(),
        	self::INACTIVE => $location->isInactive(),
        	self::TUB => $location->getTUB(),
        	self::IS_REPOSITORY => $location->isRepository(),
        	self::IS_WORK_LOCATION => $location->isWorkLocation()
        );
        $db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try {
	   		$locationid = $location->getPrimaryKey();
	        if (is_null($locationid) || !$this->locationExists($locationid))
	        {
	        	$db->insert('Locations', $array);
	        	$locationid = $db->lastInsertId();
	        }
	        else 
	        {
	        	$db->update('Locations', $array, 'LocationID=' . $locationid);
	        }
	        $db->commit();
	   	}
    	catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$locationid = NULL;
   		}
        return $locationid;
    }
    
	private function locationExists($locationID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(LocationID)'));
    	$select->where('LocationID=' . $locationID);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }
    
	/**
     * Returns a list of tubs formatted for a select list
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return array to be used with JSON, NULL if none found
     */
    public function getAllTUBSForSelect($includeinactive = FALSE)
    {
    	$select = $this->select();
    	$select->from($this->_name, array('TUB'));
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$accesslevel = $identity[PeopleDAO::ACCESS_LEVEL];
    	$userrepository = $identity[LocationDAO::LOCATION_ID];
    	//If the user is not an admin or regular WPC user, then they should be limited to their repository.
    	if ($accesslevel != PeopleDAO::ACCESS_LEVEL_ADMIN && $accesslevel != PeopleDAO::ACCESS_LEVEL_REGULAR)
    	{
    		$select->where('HomeLocationID = ' . $userrepository);
    	}
    	
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	
    	$select->distinct();
    	$select->order('TUB');
    	$rows = $this->fetchAll($select);
    	$retval = NULL;
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }
} 

?>