<?php

/**
 * UnlockedItemsDAO
 *
 * @author Valdeva Crema
 * Created Nov 10, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class UnlockedItemsDAO extends Zend_Db_Table_Abstract
{
	const UNLOCKED_ID = 'UnlockedID';
	const EXPIRATION_DATE = 'ExpirationDate';
	const UNLOCKED_BY_ID = 'UnlockedByID';
	
	/**
	 * The default table name 
	 */
    protected $_name = 'UnlockedItems';
    /* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "UnlockedID";
    
    private static $unlockedItemsDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return UnlockedItemsDAO
     */
    public static function getUnlockedItemsDAO()
	{
		if (!isset(self::$unlockedItemsDAO))
		{
			self::$unlockedItemsDAO = new UnlockedItemsDAO();
		}
		return self::$unlockedItemsDAO;
	}
	
	/*
	 * Determines if the item is unlocked.
	 */
	public function isItemUnlocked($itemID)
	{
		$select = $this->select();
		$itemID = $this->getAdapter()->quote($itemID, 'INTEGER');
		$select->where('ItemID=' . $itemID);
		$select->order(self::EXPIRATION_DATE . ' DESC');
		$select->limit(1);
		
		$row = $this->fetchRow($select);
		
		$unlocked = FALSE;
		if (!is_null($row))
		{
			$currentzenddate = new Zend_Date();
	    	$expirationzenddate = new Zend_Date($row->ExpirationDate, ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
	    	
	    	//If we haven't reached the expiration date, the item is unlocked.
	    	if ($expirationzenddate->isLater($currentzenddate))
	    	{
	    		$unlocked = TRUE;
	    	}
		}
		return $unlocked;
	}
	
	/*
	 * Unlock the item.
	 */
	public function unlockItem($itemID, $expirationDate)
	{
		$successful = TRUE;
		$db = $this->getAdapter();
   		$db->beginTransaction();
   		try {
   			$identity = Zend_Auth::getInstance()->getIdentity();
   			$insertarray = array(ItemDAO::ITEM_ID => $itemID,
   								self::UNLOCKED_BY_ID => $identity[PeopleDAO::PERSON_ID],
   								self::EXPIRATION_DATE => $expirationDate);
   			$db->insert('UnlockedItems', $insertarray);
	        $db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$successful = FALSE;
   		}
   		return $successful;
	}
	
	/**
	 * Locks the item by removing it from the UnlockedItems.  This is only called when
	 * the item is manually closed.
	*/
	public function lockItem($itemID)
	{
		//If the item is not locked, then return TRUE
		if (!$this->isItemUnlocked($itemID))
		{
			return TRUE;
		}
		$successful = TRUE;
		$db = $this->getAdapter();
		$db->beginTransaction();
		try {
			$itemID = $this->getAdapter()->quote($itemID, 'INTEGER');
			$zendExpirationDate = new Zend_Date($this->getExpirationDate($itemID), ACORNConstants::$ZEND_DATE_FORMAT);
			$expirationDate = $zendExpirationDate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
			$expirationDate = $this->getAdapter()->quote($expirationDate);
			Logger::log('ItemID=' . $itemID . ' AND ExpirationDate=' . $expirationDate);
			$db->delete('UnlockedItems', 'ItemID=' . $itemID . ' AND ExpirationDate=' . $expirationDate);
			$db->commit();
		}
		catch (Exception $e)
		{
			Logger::log($e->getMessage(), Zend_Log::ERR);
			$db->rollBack();
			$successful = FALSE;
		}
		return $successful;
	}
	
	/*
	 * Unlock the items.
	*/
	public function unlockItems(array $items, $expirationDate)
	{
		$successful = TRUE;
		$db = $this->getAdapter();
		$db->beginTransaction();
		try {
			$identity = Zend_Auth::getInstance()->getIdentity();
			foreach ($items as $item)
			{
				$insertarray = array(ItemDAO::ITEM_ID => $item->getItemID(),
						self::UNLOCKED_BY_ID => $identity[PeopleDAO::PERSON_ID],
						self::EXPIRATION_DATE => $expirationDate);
				$db->insert('UnlockedItems', $insertarray);
			}
			$db->commit();
		}
		catch (Exception $e)
		{
			Logger::log($e->getMessage(), Zend_Log::ERR);
			$db->rollBack();
			$successful = FALSE;
		}
		return $successful;
	}
	
	public function getExpirationDate($itemID)
	{
		$select = $this->select();
		$itemID = $this->getAdapter()->quote($itemID, 'INTEGER');
		$select->where('ItemID=' . $itemID);
		$select->order(self::EXPIRATION_DATE . ' DESC');
		$select->limit(1);
		
		$row = $this->fetchRow($select);
		
		$expirationdate = NULL;
		if (!is_null($row))
		{
			$zenddate = new Zend_Date($row->ExpirationDate, ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    		$expirationdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
		}
		return $expirationdate;
	}
    
}
