<?php
/**
 * TemporaryTransfersDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class TemporaryTransfersDAO extends Zend_Db_Table 
{
	const TEMPORARY_TRANSFER_ID = 'TemporaryTransferID';
	const TRANSFER_DATE = 'TransferDate';
	const FROM_LOCATION_ID = 'FromLocationID';
	const TO_LOCATION_ID = 'ToLocationID';
	const TRANSFER_TYPE = 'TransferType';
	
	const TRANSFER_TYPE_TRANSFER = 'Transfer';
	const TRANSFER_TYPE_RETURN = 'Return';
	
    /* The name of the table */
	protected $_name = "TemporaryTransfers";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "TemporaryTransferID";
    
    private static $temporaryTransferDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return TemporaryTransfersDAO
     */
    public static function getTemporaryTransfersDAO()
	{
		if (!isset(self::$temporaryTransferDAO))
		{
			self::$temporaryTransferDAO = new TemporaryTransfersDAO();
		}
		return self::$temporaryTransferDAO;
	}
	
	public function getTemporaryTransfer($identificationID)
	{
		$select = $this->select();
		$select->from($this, array('*'));
		$select->setIntegrityCheck(FALSE);
		$select->joinInner('Items', 'TemporaryTransfers.ItemID = Items.ItemID', array());
		$select->where('TransferType = \'Transfer\' AND IdentificationID=' . $identificationID);
    	$select->order('TransferDate DESC');
    	$select->limit(1);
    	$row = $this->fetchRow($select);
    	$temptransfer = NULL;
    	if (!is_null($row))
    	{
    		$temptransfer = $this->buildTemporaryTransfer($row->toArray());
    	}
    	return $temptransfer;
	}
	
	public function getTemporaryTransferReturn($identificationID)
	{
		$previoustolocationid = $this->getTransferToLocationID($identificationID);
		$temptransfer = NULL;
	    	
		if (!is_null($previoustolocationid))
		{
			$select = $this->select();
			$select->from($this, array('*'));
			$select->setIntegrityCheck(FALSE);
			$select->joinInner('Items', 'TemporaryTransfers.ItemID = Items.ItemID', array());
			$select->where('FromLocationID = ' . $previoustolocationid . ' AND TransferType = \'Return\' AND IdentificationID=' . $identificationID);
	    	$select->order('TransferDate DESC');
	    	$select->limit(1);
	    	$row = $this->fetchRow($select);
	    	if (!is_null($row))
	    	{
	    		$temptransfer = $this->buildTemporaryTransfer($row->toArray());
	    	}
		}
    	return $temptransfer;
	}
	
	public function getTransferToLocationID($identificationID)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
		$select->from($this, array(self::TO_LOCATION_ID));
    	$select->joinInner('Items', 'TemporaryTransfers.ItemID = Items.ItemID', array());
		$select->where('TransferType = \'Transfer\' AND IdentificationID=' . $identificationID);
    	$select->order('TransferDate DESC');
    	$select->limit(1);
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$row = $this->fetchRow($select);
    	$locID = NULL;
    	if (!is_null($row))
    	{
    		$locID = $row->ToLocationID;
    	}
    	return $locID;
    }
    
	public function saveGroupTemporaryTransfers(array $records, TemporaryTransfer $modeltransfer, $comments)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		$recordssaved = 0;
   		try {
   			foreach ($records as $record)
   			{
   				$temptransferid = $record->getFunctions()->getTemporaryTransfer()->getPrimaryKey();
   				$transfer = clone $modeltransfer;
   				if (!is_null($temptransferid))
   				{
   					$transfer->setPrimaryKey($temptransferid);
   				}
   				$transfer->setItemID($record->getItemID());
   				$temptransferid = $this->saveTemporaryTransfer($transfer, $db);
   				if (!is_null($temptransferid))
   				{
   					$recordssaved++;
   					$record->appendComments($comments, TRUE);
   					ItemDAO::getItemDAO()->updateComments($record, $db);
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
    
	public function saveGroupTemporaryTransferReturns(array $records, TemporaryTransfer $modeltransfer, $comments)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		$recordssaved = 0;
   		try {
   			foreach ($records as $record)
   			{
   				$temptransferid = $record->getFunctions()->getTemporaryTransferReturn()->getPrimaryKey();
   				$transfer = clone $modeltransfer;
   				if (!is_null($temptransferid))
   				{
   					$transfer->setPrimaryKey($temptransferid);
   				}
   				$transfer->setItemID($record->getItemID());
   				$temptransferid = $this->saveTemporaryTransfer($transfer, $db);
   				if (!is_null($temptransferid))
   				{
   					$recordssaved++;
   					$record->appendComments($comments, TRUE);
   					ItemDAO::getItemDAO()->updateComments($record, $db);
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

    /*
     * Insert the temp transfer.
     * 
     * @param TemporaryTransfer temporaryTransfer
     */
    public function saveTemporaryTransfer(TemporaryTransfer $temporaryTransfer, $db = NULL)
    {
    	$temptransferid = $temporaryTransfer->getPrimaryKey();
    	$array = array(
    		self::FROM_LOCATION_ID => $temporaryTransfer->getTransferFrom(),
    		self::TO_LOCATION_ID => $temporaryTransfer->getTransferTo(),
    		ItemDAO::ITEM_ID => $temporaryTransfer->getItemID(),
    		self::TRANSFER_TYPE => $temporaryTransfer->getTransferType(),
    		self::TRANSFER_DATE => $temporaryTransfer->getTransferDate()
    	);
    	if (is_null($db))
    	{
	    	$db = $this->getAdapter();
	   		$db->beginTransaction();
	   		try {
	   			if (is_null($temptransferid) || !$this->tempTransferExists($temptransferid))
	   			{
	   				$db->insert($this->_name, $array);
	   				$temptransferid = $db->lastInsertId();
   				}
	   			else
	   			{
	   				$db->update($this->_name, $array, self::TEMPORARY_TRANSFER_ID . '=' . $temptransferid);
	   			}
		        $db->commit();
	   		}
	   		catch (Exception $e)
	   		{
	   			Logger::log($e->getMessage(), Zend_Log::ERR);
	   			$db->rollBack();
	   			$temptransferid = NULL;
	   		}
    	}
    	else 
    	{
    		if (is_null($temptransferid) || !$this->tempTransferExists($temptransferid))
   			{
   				$db->insert($this->_name, $array);
   				$temptransferid = $db->lastInsertId();
   			}
   			else
   			{
   				$db->update($this->_name, $array, self::TEMPORARY_TRANSFER_ID . '=' . $temptransferid);
   			}
    	}
    	return $temptransferid;
    }
    
    private function tempTransferExists($temptransferid)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(TemporaryTransferID)'));
    	$select->where('TemporaryTransferID=' . $temptransferid);
    	Logger::log($select->__toString());
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }

    
    private function buildTemporaryTransfer(array $data)
    {
    	$temptransfer = new TemporaryTransfer($data[self::TEMPORARY_TRANSFER_ID]);
		$temptransfer->setTransferDate($data[self::TRANSFER_DATE]);
		$temptransfer->setTransferFrom($data[self::FROM_LOCATION_ID]);
		$temptransfer->setTransferTo($data[self::TO_LOCATION_ID]);
		$temptransfer->setTransferType($data[self::TRANSFER_TYPE]);
		$temptransfer->setItemID($data[ItemDAO::ITEM_ID]);
    	return $temptransfer;
    }
    
    public function deleteTemporaryTransfer($temptransferid)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		try {
   			$rowsdeleted = $db->delete($this->_name, self::TEMPORARY_TRANSFER_ID . '=' . $temptransferid);
   			
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