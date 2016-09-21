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
 * LogoutDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
class LogoutDAO extends Zend_Db_Table
{
	const IDENTIFICATION_ID = 'IdentificationID';
	const LOGOUT_ID = 'LogoutID';
	const LOGOUT_BY_ID = 'LogoutByID';
	const LOGOUT_DATE = 'LogoutDate';
	const FROM_LOCATION_ID = 'FromLocationID';
	const TO_LOCATION_ID = 'ToLocationID';
	
	/* The name of the table */
	protected $_name = "ItemLogout";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "LogoutID";

    private static $logoutDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return LogoutDAO
     */
    public static function getLogoutDAO()
	{
		if (!isset(self::$logoutDAO))
		{
			self::$logoutDAO = new LogoutDAO();
		}
		return self::$logoutDAO;
	}
	
	
    /**
     * Returns the item logout with the given id.
     *
     * @access public
     * @param  Integer identificationID
     * @return Logout
     */
    public function getLogout($identificationID)
    {
    	$item = NULL;
	    if (!empty($identificationID))
    	{
	    	$identificationID = $this->getAdapter()->quote($identificationID, 'INTEGER');
	    	$select = $this->select();
	    	$select->where('IdentificationID=' . $identificationID);
	    	$row = $this->fetchRow($select);
	    	if (!is_null($row))
	    	{
	    		$item = $this->buildItemLogout($row->toArray());
	    	}
    	}
    	return $item;
    }
    
    /*
     * @access private
     * @param  array values from the database
     * @return Logout
     */
    private function buildItemLogout(array $values)
    {
    	$logout = new Logout($values[self::LOGOUT_ID]);
    	$logout->setIdentificationID($values[self::IDENTIFICATION_ID]);
    	$logout->setLogoutBy($values[self::LOGOUT_BY_ID]);
    	$logout->setLogoutDate($values[self::LOGOUT_DATE]);
    	$logout->setTransferFrom($values[self::FROM_LOCATION_ID]);
    	$logout->setTransferTo($values[self::TO_LOCATION_ID]);
    	return $logout;
    }
    
	public function saveGroupLogouts(array $records, Logout $modellogout)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		$recordssaved = 0;
   		try {
   			foreach ($records as $record)
   			{
   				$logoutid = $record->getFunctions()->getLogout()->getPrimaryKey();
   				$logout = clone $modellogout;
   				if (!is_null($logoutid))
   				{
   					$logout->setPrimaryKey($logoutid);
   				}
   				$record->getFunctions()->setLogout($logout);
   				$logoutid = $this->saveItemLogout($record, $db);
   				if (!is_null($logoutid))
   				{
   					$recordssaved++;
   				}
   			}
   			$db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$recordssaved = 0;
   		}
   		return $recordssaved;
    }
    
	/**
     * Saves the Item Logout information
     * @access public
     * @param  Item item
     */
    public function saveItemLogout(Item $item, $db = NULL)
    {
    	$logout = $item->getFunctions()->getLogout();
    	$logoutid = $logout->getPrimaryKey();
        //Build the array to save
        $logoutarray = $this->buildLogoutArray($logout);
        
        if (is_null($db))
        {
	        $db = $this->getAdapter();
	   		$db->beginTransaction();
	   		try {
		   		//If the item isn't in the database,
		        //insert it
		        if (is_null($logoutid) || !$this->logoutExists($logoutid))
		        {
		        	$logoutarray[ItemDAO::IDENTIFICATION_ID] = $item->getPrimaryKey();
		        	$db->insert('ItemLogout', $logoutarray);
		        	$logoutid = $db->lastInsertId();
		        }
		        //otherwise, update it
		        else 
		        {
		        	$db->update('ItemLogout', $logoutarray, 'LogoutID=' . $logoutid);	
		        }
		        $db->commit();
	   		}
	   		catch (Exception $e)
	   		{
	   			Logger::log($e->getMessage(), Zend_Log::ERR);
	   			$db->rollBack();
	   			$logoutid = NULL;
	   		}
        }
        else 
        {
        	//If the item isn't in the database,
	        //insert it
	        if (is_null($logoutid) || !$this->logoutExists($logoutid))
	        {
	        	$logoutarray[ItemDAO::IDENTIFICATION_ID] = $item->getPrimaryKey();
	        	$db->insert('ItemLogout', $logoutarray);
	        	$logoutid = $db->lastInsertId();
	        }
	        //otherwise, update it
	        else 
	        {
	        	$db->update('ItemLogout', $logoutarray, 'LogoutID=' . $logoutid);	
	        }	
        }
   		return $logoutid; 
    }
    
	/*
     * Data to be loaded into the Item Logout table
     */
    private function buildLogoutArray(Logout $logout)
    {
    	//$zenddate = new Zend_Date($logout->getLogoutDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	//$date = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$idarray = array(
    		self::LOGOUT_BY_ID => $logout->getLogoutBy(),
    		self::LOGOUT_DATE => $logout->getLogoutDate(),
    		self::FROM_LOCATION_ID => $logout->getTransferFrom(),
    		self::TO_LOCATION_ID => $logout->getTransferTo()
    	);
    	return $idarray;
    }
    
	private function logoutExists($logoutID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(LogoutID)'));
    	$select->where('LogoutID=' . $logoutID);
    	$row = $this->fetchRow($select);
    	return $row->Count = 1;
    }
    
	public function deleteLogout(Logout $logout)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		try {
	   		$rowsdeleted = $db->delete('ItemLogout', 'LogoutID=' . $logout->getPrimaryKey());	
	        $db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$rowsdeleted = 0;
	   	}
	   	return $rowsdeleted;
    }

} 

?>