<?php
/**
 * LocationHistoryDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class LocationHistoryDAO extends Zend_Db_Table 
{
	const LOCATION_HISTORY_ID = 'LocationHistoryID';
	const DATE_IN = 'DateIn';
	const DATE_OUT = 'DateOut';
	const IS_TEMPORARY = 'IsTemporary';
	
    /* The name of the table */
	protected $_name = "LocationHistory";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "LocationHistoryID";
    
    private static $locationHistoryDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return LocationHistoryDAO
     */
    public static function getLocationHistoryDAO()
	{
		if (!isset(self::$locationHistoryDAO))
		{
			self::$locationHistoryDAO = new LocationHistoryDAO();
		}
		return self::$locationHistoryDAO;
	}
	
	public function getTemporaryTransfer($identificationID)
	{
		//Get the max temp date in
		$mostrecendtemptransferdate = $this->getMostRecentTemporaryTransferDate($identificationID);
		$temptransfer = NULL;
		//If a temp transfer exists, get the dates.
		if (!is_null($mostrecendtemptransferdate))
		{
			$fromlocation = $this->getTransferFromLocationID($identificationID, $mostrecendtemptransferdate);
			$tolocation = $this->getTransferToLocationID($identificationID, $mostrecendtemptransferdate);
			$temptransfer = new TemporaryTransfer();
			$temptransfer->setTransferDate($mostrecendtemptransferdate);
			$temptransfer->setTransferFrom($fromlocation);
			$temptransfer->setTransferTo($tolocation);
		}
		return $temptransfer;
	}
	
	private function getMostRecentTemporaryTransferDate($identificationID)
	{
		$select = $this->select();
		$select->setIntegrityCheck(FALSE);
		$select->from($this, array('DateIn' => 'DateIn'));
		$select->joinInner('Items', 'LocationHistory.ItemID = Items.ItemID', array());
		$select->where('IsTemporary = 1 AND IdentificationID=' . $identificationID);
		$select->order('LocationHistoryID DESC');
		$select->limit(1);
		$row = $this->fetchRow($select);
		$date = NULL;
		if (!is_null($row))
		{
			$date = $row->DateIn;
		}
		return $date;
	}
	
	public function getTemporaryTransferReturn($identificationID)
	{
		//Get the max temp date in
		$mostrecendtemptransferdate = $this->getMostRecentTemporaryTransferDate($identificationID);
		//Get the most recent return date after it
		$mostrecendtemptransferreturndate = $this->getMostRecentTemporaryTransferReturnDate($identificationID, $mostrecendtemptransferdate);
		
		$temptransferreturn = NULL;
		//If a temp transfer exists, get the dates.
		if (!is_null($mostrecendtemptransferreturndate))
		{
			$fromlocation = $this->getTransferFromLocationID($identificationID, $mostrecendtemptransferreturndate);
			$tolocation = $this->getTransferToLocationID($identificationID, $mostrecendtemptransferreturndate);
			$temptransferreturn = new TemporaryTransfer();
			$temptransferreturn->setTransferDate($mostrecendtemptransferreturndate);
			$temptransferreturn->setTransferFrom($fromlocation);
			$temptransferreturn->setTransferTo($tolocation);
		}
		return $temptransferreturn;
	}
	
	private function getMostRecentTemporaryTransferReturnDate($identificationID, $mindate)
	{
		$mindate = $this->getAdapter()->quote($mindate);
		$select = $this->select();
		$select->setIntegrityCheck(FALSE);
		$select->from($this, array('DateOut' => 'DateOut'));
		$select->joinInner('Items', 'LocationHistory.ItemID = Items.ItemID', array());
		$select->where('IsTemporary = 1 AND DateOut >=' . $mindate . ' AND IdentificationID=' . $identificationID);
		$select->order('LocationHistoryID DESC');
		$select->limit(1);
		$row = $this->fetchRow($select);
		$date = NULL;
		if (!is_null($row))
		{
			$date = $row->DateOut;
		}
		return $date;
	}

	public function getTransferFromLocationID($identificationID, $date)
    {
    	$date = $this->getAdapter()->quote($date);
    	
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
		$select->from($this, array('LocationID'));
    	$select->joinInner('Items', 'LocationHistory.ItemID = Items.ItemID', array());
		$select->where('IdentificationID=' . $identificationID . ' AND DateOut=' . $date);
    	$select->order(array('DateOut DESC', 'LocationHistoryID DESC'));
    	$select->limit(1);
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$row = $this->fetchRow($select);
    	$locID = NULL;
    	if (!is_null($row))
    	{
    		$locID = $row->LocationID;
    	}
    	return $locID;
    }
    
	public function getTransferToLocationID($identificationID, $date)
    {
    	$date = $this->getAdapter()->quote($date);
    	
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
		$select->from($this, array('LocationID'));
    	$select->joinInner('Items', 'LocationHistory.ItemID = Items.ItemID', array());
		$select->where('IdentificationID=' . $identificationID . ' AND DateIn=' . $date);
    	$select->order(array('DateIn DESC', 'DateOut DESC', 'LocationHistoryID DESC'));
    	$select->limit(1);
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$row = $this->fetchRow($select);
    	$locID = NULL;
    	if (!is_null($row))
    	{
    		$locID = $row->LocationID;
    	}
    	return $locID;
    }
    
    /*
     * OLD LOGIC STARTED AND SAVED
     * 
     * Insert the location history to have the new location on the given date in 
     * the proper hierarchy of the location history.
     * 
     * @param Int previous location
     * @param Int new location
     * @param Date the move date.
     * @param Int identification id
     */
    /*public function insertLocationHistory($oldLocationID, $newLocationID, $date, $identificationid)
    {
    	$successful = TRUE;
    	//Get the current location data
    	$currentlocdata = $this->getCurrentLocData($identificationid);
    	
    	$currentloczenddate = new Zend_Date($currentlocdata[self::DATE_IN], 'yyyy-MM-dd HH:mm:ss');
    	$newzenddate = new Zend_Date($date, 'yyyy-MM-dd HH:mm:ss');
    	
    	//As long as the new date is later than or equal to the current loc date,
    	//Just insert the history
    	if ($newzenddate->later($currentloczenddate) || $newzenddate->equals($currentloczenddate))
    	{
    		//If the old location is not the current location, insert that first.
    		if ($currentlocdata[LocationDAO::LOCATION_ID] != $oldLocationID)
    		{
    			//Insert the new data
	    		$insertdata = array(
	    			ItemDAO::IDENTIFICATION_ID => $identificationid,
	    			self::DATE_IN => $date,
	    			LocationDAO::LOCATION_ID => $oldLocationID
	    		);
	    		//Update the date out with the new data.
	    		$updatedata = array(
	    			self::DATE_OUT => $date
	    		);
	    		
	    		$successful = $this->performInsertAndUpdates($updatedata, $currentlocdata[self::LOCATION_HISTORY_ID], $insertdata);
	    		//Then update current loc data
	    		$currentlocdata = $this->getCurrentLocData($identificationid);
    		}
    		
    		//Insert the new data
    		$insertdata = array(
    			ItemDAO::IDENTIFICATION_ID => $identificationid,
    			self::DATE_IN => $date,
    			LocationDAO::LOCATION_ID => $newLocationID
    		);
    		//Update the date out with the new data.
    		$updatedata = array(
    			self::DATE_OUT => $date
    		);

    		$successful = $this->performInsertAndUpdates($updatedata, $currentlocdata[self::LOCATION_HISTORY_ID], $insertdata);
    	}
    	//Otherwise, the history will have to be inserted and other history updated
    	else 
    	{
    		$greaterdata = $this->getClosestLaterThanData($identificationid, $date);
    		$lessdata = $this->getClosestBeforeData($identificationid, $date);
    		
    		//If there are no dates less than the given date, then the new history will be inserted 
    		//as the first item
    		if (is_null($lessdata))
    		{
	    		//If the old location is not the 'greater than' location, insert that first.
	    		if ($greaterdata[LocationDAO::LOCATION_ID] != $oldLocationID)
	    		{
	    			//Insert the new data
		    		$insertdata = array(
		    			ItemDAO::IDENTIFICATION_ID => $identificationid,
		    			self::DATE_IN => $date,
		    			LocationDAO::LOCATION_ID => $oldLocationID
		    		);
		    		//Update the date out with the new data.
		    		$updatedata = array(
		    			self::DATE_OUT => $date
		    		);
		    		
		    		$successful = $this->performInsertAndUpdates($updatedata, $greaterdata[self::LOCATION_HISTORY_ID], $insertdata);
		    		//Then update current loc data
		    		$greaterdata = $this->getClosestLaterThanData($identificationid);
	    		}
    		
    			//Insert the new data, having a date out
    			//of the 'greater data'
	    		$insertdata = array(
	    			ItemDAO::IDENTIFICATION_ID => $identificationid,
	    			self::DATE_IN => $date,
	    			self::DATE_OUT => $greaterdata[self::DATE_IN],
	    			LocationDAO::LOCATION_ID => $newLocationID
	    		);
	    		
	    		$successful = $this->performInsertAndUpdates(NULL, $greaterdata[self::LOCATION_HISTORY_ID], $insertdata);
    		}
    		//Otherwise, the history will be inserted at the proper location.
    		else 
    		{
    			//Insert the new data, having a date out
    			//of the 'greater data'
	    		$insertdata = array(
	    			ItemDAO::IDENTIFICATION_ID => $identificationid,
	    			self::DATE_IN => $date,
	    			self::DATE_OUT => $greaterdata[self::DATE_IN],
	    			LocationDAO::LOCATION_ID => $newLocationID
	    		);
	    		
	    		//Update the 'lesser data' to have a date out of the new date
	    		$updatedata = array(
	    			self::DATE_OUT => $date
	    		);
	    		
	    		$successful = $this->performInsertAndUpdates($updatedata, $lessdata[self::LOCATION_HISTORY_ID], $insertdata);
    		}
    	}
    	return $successful;
    }*/
    
    public function insertInitialLocationHistory(LocationHistory $locationhistory, $db = NULL)
    {
    	$temp = $this->getAdapter()->quote($locationhistory->isTemporary(), 'TINYINT');
    	$newinsertdata = array(
		    	ItemDAO::ITEM_ID => $locationhistory->getItemID(),
			    LocationDAO::LOCATION_ID => $locationhistory->getLocationID(),
		    	self::DATE_IN => $locationhistory->getDateIn(),
		    	self::DATE_OUT => NULL,
		    	self::IS_TEMPORARY => $temp
		    );
		$this->performInsertAndUpdates(NULL, NULL, $newinsertdata, $db);
    }
    
    /*
     * Insert the location history to have the new location on the given date in 
     * the proper hierarchy of the location history.
     * 
     * @param Int new location
     * @param Date the move date.
     * @param Int $itemid
     * @param mixed <optional> db - the transaction object to use
     */
    public function insertLocationHistory($fromLocationID, $newLocationID, $date, $itemid, $db = NULL, $isTemporary = FALSE)
    {
    	$successful = TRUE;
		$zenddate = new Zend_Date($date, ACORNConstants::$ZEND_DATE_FORMAT);		
    	$date = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	
    	//Get the current location data
    	$currentlocdata = $this->getCurrentLocData($itemid);
    	
    	$currentloczenddate = new Zend_Date($currentlocdata->getDateIn(), ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
    	$newzenddate = new Zend_Date($date, ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
    	
    	//As long as the new date is later than or equal to the current loc date,
    	//Just insert the history
    	if ($newzenddate->isLater($currentloczenddate) || $newzenddate->equals($currentloczenddate))
    	{   
    		//If the old from loc id is not the same as the new from loc id,
	    	//Insert a new history value for this day.
	    	if ($currentlocdata->getLocationID() != $fromLocationID)
    		{
    			$newinsertdata = array(
		    		ItemDAO::ITEM_ID => $itemid,
			    	LocationDAO::LOCATION_ID => $fromLocationID,
		    		self::DATE_IN => $date,
		    		self::DATE_OUT => NULL,
		    		self::IS_TEMPORARY => $isTemporary
		    	);
		    	
		    	$updatedata = array(self::DATE_OUT => $date);
		    	$this->performInsertAndUpdates($updatedata, $currentlocdata->getPrimaryKey(), $newinsertdata, $db);
				//Refresh the current loc data.
		    	$currentlocdata = $this->getCurrentLocData($itemid);
    		}
    			
    		//Insert the new data
    		$insertdata = array(
    			ItemDAO::ITEM_ID => $itemid,
    			self::DATE_IN => $date,
    			LocationDAO::LOCATION_ID => $newLocationID,
    			self::IS_TEMPORARY => $isTemporary
    		);
    		//Update the date out with the new data.
    		$updatedata = array(
    			self::DATE_OUT => $date
    		);

    		$successful = $this->performInsertAndUpdates($updatedata, $currentlocdata->getPrimaryKey(), $insertdata, $db);
    	}
    	//Otherwise, the history will have to be inserted and other history updated
    	else 
    	{
    		$greaterdata = $this->getClosestLaterThanData($itemid, $date);
    		$lessdata = $this->getClosestBeforeData($itemid, $date);
    		
    		//If there are no dates less than the given date, then the new history will be inserted 
    		//as the first item
    		if (is_null($lessdata))
    		{   
	    		$newinsertdata = array(
	    				ItemDAO::ITEM_ID => $itemid,
			    		LocationDAO::LOCATION_ID => $fromLocationID,
			    		self::DATE_IN => $date,
			    		self::DATE_OUT => $date
			    );
		    	
		    	$this->performInsertAndUpdates(NULL, NULL, $newinsertdata, $db);

		    	$zenddate = new Zend_Date($greaterdata->getDateIn(), ACORNConstants::$ZEND_DATE_FORMAT);		
    			$dateout = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	
    			//Insert the new data, having a date out
    			//of the 'greater data'
	    		$insertdata = array(
	    			ItemDAO::ITEM_ID => $itemid,
	    			self::DATE_IN => $date,
	    			self::DATE_OUT => $dateout,
	    			LocationDAO::LOCATION_ID => $newLocationID,
			    	self::IS_TEMPORARY => $isTemporary
	    		);
	    		
	    		$successful = $this->performInsertAndUpdates(NULL, NULL, $insertdata, $db);
    		}
    		//Otherwise, the history will be inserted at the proper location.
    		else 
    		{
	    		//If the 'less than' loc id is not the same as the new from loc id,
		    	//Insert a new history value for this day.
		    	if ($lessdata->getLocationID() != $fromLocationID)
	    		{
	    			$newinsertdata = array(
			    		ItemDAO::ITEM_ID => $itemid,
				    	LocationDAO::LOCATION_ID => $fromLocationID,
			    		self::DATE_IN => $date,
			    		self::DATE_OUT => $date
			    	);
			    	
			    	$updatedata = array(self::DATE_OUT => $date);
			    	$this->performInsertAndUpdates($updatedata, $lessdata->getPrimaryKey(), $newinsertdata, $db);
					//Refresh the current loc data.
			    	$lessdata = $this->getClosestBeforeData($itemid, $date);
	    		}
    		
	    		$zenddate = new Zend_Date($greaterdata->getDateIn(), ACORNConstants::$ZEND_DATE_FORMAT);		
    			$dateout = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	
    			//Insert the new data, having a date out
    			//of the 'greater data'
	    		$insertdata = array(
	    			ItemDAO::ITEM_ID => $itemid,
	    			self::DATE_IN => $date,
	    			self::DATE_OUT => $dateout,
	    			LocationDAO::LOCATION_ID => $newLocationID,
			    	self::IS_TEMPORARY => $isTemporary
	    		);
	    		
	    		//Update the 'lesser data' to have a date out of the new date
	    		$updatedata = array(
	    			self::DATE_OUT => $date
	    		);
	    		
	    		$successful = $this->performInsertAndUpdates($updatedata, $lessdata->getPrimaryKey(), $insertdata, $db);
    		}
    	}
    	return $successful;
    }
    
	/*
     * Insert the location history to have the new location on the given date in 
     * the proper hierarchy of the location history.
     * 
     * @param Int old location
     * @param Int new location
     * @param Date the old move date.
     * @param Date the new move date.
     * @param Int item id
     * @param mixed <optional> db - the transaction object to use
     */
    public function updateLocationHistory($oldFromLocID, $newFromLocID, $oldLocationID, $newLocationID, $olddate, $newdate, $itemid, $db = NULL, $isTemporary = FALSE)
    {
    	$successful = TRUE;
    	
    	$zenddate = new Zend_Date($newdate, ACORNConstants::$ZEND_DATE_FORMAT);		
    	$newdate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	
    	//If the old from loc id is not the same as the new from loc id,
	    //Insert a new history value for this day.
	    if ($oldFromLocID != $newFromLocID)
    	{
	    	$newinsertdata = array(
	    		ItemDAO::ITEM_ID => $itemid,
	    		LocationDAO::LOCATION_ID => $newFromLocID,
	    		self::DATE_IN => $newdate,
	    		self::DATE_OUT => $newdate
	    	);
	    	$this->performInsertAndUpdates(NULL, NULL, $newinsertdata, $db);
    	}	
    	
    	//Get the location history
    	$locationhistory = $this->getLocationHistory($itemid);
    	
    	$prevdataupdate = NULL;
    	$datatouse = NULL;
    	$oldzenddate = new Zend_Date($olddate, ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
    	
    	foreach ($locationhistory as $lochistitem)
    	{
    		$loc = $lochistitem[LocationDAO::LOCATION_ID];
    		$date = $lochistitem[self::DATE_IN];
    		$lochistdate = new Zend_Date($date, ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
    		if ($loc == $oldLocationID && $oldzenddate->equals($lochistdate))
    		{
    			$datatouse = $lochistitem;
    		}
    		elseif (is_null($datatouse)) 
    		{
    			$prevdataupdate = $lochistitem;
    		}
    	}

    	//If we found the history to update, then continue
    	if (!is_null($datatouse))
    	{
	    	//If there is a previous history item to update,
	    	//then update the date out with the new date
	    	if (!is_null($prevdataupdate))
	    	{
	    		//Update the date out with the new data.
	    		$updatedata = array(
	    			self::DATE_OUT => $newdate
	    		);
	
	    		$this->performInsertAndUpdates($updatedata, $prevdataupdate[self::LOCATION_HISTORY_ID], NULL, $db);
	    	}
	    	
    		//Update the date in with the new data.
    		$updatedata = array(
    			self::DATE_IN => $newdate,
    			LocationDAO::LOCATION_ID => $newLocationID,
			    self::IS_TEMPORARY => $isTemporary
	    	);

    		$successful = $this->performInsertAndUpdates($updatedata, $datatouse[self::LOCATION_HISTORY_ID], NULL, $db);
    	}
    	return $successful;
    }
    
    private function performInsertAndUpdates($updatearray, $updatelocationhistoryid, $insertarray, $db = NULL)
    {
    	$successful = TRUE;
    	if (is_null($db))
    	{
	    	$db = $this->getAdapter();
	   		$db->beginTransaction();
	   		try {
	   			if (!is_null($insertarray))
	   			{
		   			$db->insert('LocationHistory', $insertarray);
	   			}
		   		if (!is_null($updatearray))
		   		{
		        	$db->update('LocationHistory', $updatearray, 'LocationHistoryID=' . $updatelocationhistoryid);	
		   		}
		        $db->commit();
	   		}
	   		catch (Exception $e)
	   		{
	   			Logger::log($e->getMessage(), Zend_Log::ERR);
	   			$db->rollBack();
	   			$successful = FALSE;
	   		}
    	}
    	else 
    	{
    		if (!is_null($insertarray))
	   		{
		   		$db->insert('LocationHistory', $insertarray);
	   		}
		   	if (!is_null($updatearray))
		   	{
		        $db->update('LocationHistory', $updatearray, 'LocationHistoryID=' . $updatelocationhistoryid);	
		   	}
    	}
   		return $successful;
    }
    
	/*
     * Returns the location history data for the given item id
     * 
     * @param Int the $itemid
     */
    public function getLocationHistory($itemid)
    {
    	$select = $this->select();
    	$select->from($this, array('*'));
    	$select->where(ItemDAO::ITEM_ID . '=' . $itemid);
    	$select->order('DateIn', 'DateOut');
    	$rows = $this->fetchAll($select);
    	$data = NULL;
    	if (!is_null($rows))
    	{
    		$data = $rows->toArray();
    	}
    	return $data;
    }
    
    /*
     * Returns the data for the most recent location.
     * 
     * @param Int the $itemid
     */
    public function getCurrentLocData($itemid)
    {
    	$select = $this->select();
    	$select->from($this, array('*'));
    	$select->where(ItemDAO::ITEM_ID . '=' . $itemid . ' AND DateOut IS NULL');
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$row = $this->fetchRow($select);
    	$lochist = NULL;
    	if (!is_null($row))
    	{
    		$lochist = $this->buildLocationHistory($row->toArray());
    	}
    	return $lochist;
    }
    
	/*
     * Returns the data that is closest to but has a date in greater than the given date
     * 
     * @param Int the $itemid
     * @param mixed the date
     */
    private function getClosestLaterThanData($itemid, $date)
    {
    	$select = $this->select();
    	$select->from($this, array('*'));
    	$date = $this->getAdapter()->quote($date);
    	$select->where(ItemDAO::ITEM_ID . '=' . $itemid . ' AND DateIn >= ' . $date);
    	$select->limit(1);
    	$select->order(array('ItemID', 'DateIn DESC'));
    	$row = $this->fetchRow($select);
    	$lochist = NULL;
    	if (!is_null($row))
    	{
    		$lochist = $this->buildLocationHistory($row->toArray());
    	}
    	return $lochist;
    }
    
	/*
     * Returns the data that is closest to but has a date out less than the given date
     * 
     * @param Int the $itemid
     * @param mixed the date
     */
    private function getClosestBeforeData($itemid, $date)
    {
    	$select = $this->select();
    	$select->from($this, array('*'));
    	$date = $this->getAdapter()->quote($date);
    	$select->where(ItemDAO::ITEM_ID . '=' . $itemid . ' AND DateOut <= ' . $date);
    	$select->limit(1);
    	$select->order(array('ItemID', 'DateOut'));
    	$row = $this->fetchRow($select);
    	$lochist = NULL;
    	if (!is_null($row))
    	{
    		$lochist = $this->buildLocationHistory($row->toArray());
    	}
    	return $lochist;
    }
    
    private function buildLocationHistory(array $data)
    {
    	$lochist = new LocationHistory($data[self::LOCATION_HISTORY_ID]);
    	$lochist->setLocationID($data[LocationDAO::LOCATION_ID]);
    	$lochist->setDateIn($data[self::DATE_IN]);
    	$lochist->setDateOut($data[self::DATE_OUT]);
    	$lochist->setItemID($data[ItemDAO::ITEM_ID]);
    	$lochist->setTemporary($data[self::IS_TEMPORARY]);
    	return $lochist;
    }
    
    public function deleteTemporaryTransfer(TemporaryTransfer $temptransfer)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		$rowsdeleted = 0;
   		try {
   			$transferto = $temptransfer->getTransferTo();
   			$transferdate = $temptransfer->getTransferDate();
   			$itemid = ItemDAO::getItemDAO()->getItemID($temptransfer->getIdentificationID());
   			
   			//Get the item before the temp transfer
   			$databefore = $this->getClosestBeforeData($itemid, $transferdate);
   			if (!is_null($databefore))
   			{
	   			//Get the item right after the temp transfer
	   			$dataafter = $this->getClosestLaterThanData($itemid, $transferdate);
	   			//If the current location is the temp location, a little more updating will have to be done.
	   			if(!is_null($dataafter))
	   			{
	   				$databeforearraytoupdate = array(self::DATE_OUT => NULL);
	   				$db->update('LocationHistory', $databeforearraytoupdate, 'LocationHistoryID=' . $databefore->getPrimaryKey());
	   			}
	   			else 
	   			{
	   				//Get the item right after the temp transfer
	   				$dataafter = $this->getClosestLaterThanData($itemid, $transferdate);
	   				$dataafterarraytoupdate = array(self::DATE_OUT => $databefore->getDateIn());
	   				$db->update('LocationHistory', $dataafterarraytoupdate, 'LocationHistoryID=' . $dataafter->getPrimaryKey());
	   			}
   			}
   			
   			$transferdate = $this->getAdapter()->quote($transferdate);
   			//Now delete the temp transer
   			$rowsdeleted = $db->delete('LocationHistory', 'LocationID=' . $transferto . ' AND IsTemporary = 1 AND DateIn=' . $transferdate);
   			
   			$db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$rowsdeleted = NULL;
   		}
   		return $rowsdeleted;
    }
} 

?>