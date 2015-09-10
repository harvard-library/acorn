<?php
/**
 * TransferNamespace
 *
 * @author vcrema
 * Created April 13, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */


class TransferNamespace extends Zend_Session_Namespace
{
	
	const TRANSFER_NAMESPACE_NAME = 'transfers';
	
	const LOGIN = 'login';
	const LOGOUT = 'logout';
	const BULK_LOGIN = 'bulklogin';
	const BULK_LOGOUT = 'bulklogout';
	const TEMP_TRANSFER = 'temptransfer';
	const TEMP_TRANSFER_RETURN = 'temptransferreturn';
	
	const SAVE_STATE_IDLE = 'idle';
	const SAVE_STATE_SUCCESS = 'success';
	const SAVE_STATE_FAILED = 'failed';
	
	const BULK_FROM_VALUE = 'bulkfromvalue';
	
	private static $TRANSFER_NAMESPACE;

	/*
	 * The Namespace holds the items specific to the
	 * items and osw.
	 */
	public static function getTransferNamespace()
	{
		if (is_null(self::$TRANSFER_NAMESPACE))
		{
			self::$TRANSFER_NAMESPACE = new TransferNamespace(self::TRANSFER_NAMESPACE_NAME);
		}
		return self::$TRANSFER_NAMESPACE;
	}
	
	public static function clearAll()
	{
		self::getTransferNamespace()->unsetAll();
	}
	
	public static function clearTransferList($listname)
	{
		unset(self::getTransferNamespace()->$listname);
	}

	/*
	 * Get the transfer list for the given name.
	 */
	public static function getTransferList($listname)
	{
		return self::getTransferNamespace()->$listname;
	}
	
	/*
	 * Set the transfer list for the given name.
	 */
	public static function setTransferList($listname, $list)
	{
		self::getTransferNamespace()->$listname = $list;
	}
	
	/*
	 * Adds an item to the transfer list with the given name.
	 * 
	 * @return the new list
	 */
	public static function addToTransferList($listname, Item $item)
	{
		$list = self::getTransferList($listname);
		if (!isset($list))
		{
			$list = array();
		}
		$list[$item->getItemID()] = $item;
		self::setTransferList($listname, $list);
		return $list;
	}
	
	/*
	 * Remove an item with the given identification id
	 * 
	 * @return the new list.
	 */
	public static function removeFromTransferList($listname, $itemID)
	{
		$list = self::getTransferList($listname);
		if (isset($list))
		{
			unset($list[$itemID]);
			self::setTransferList($listname, $list);
		}
		return $list;
	}
	
	public static function getTransferDate($listname)
	{
		$date = NULL;
		$list = self::getTransferList($listname);
		if (isset($list) && count($list) > 0)
		{
			$item = current($list);
			switch ($listname)
			{
				case self::LOGIN:
					$date = $item->getFunctions()->getLogin()->getLoginDate();
					break;
				case self::LOGOUT:
					$date = $item->getFunctions()->getLogout()->getLogoutDate();
					break;
				case self::TEMP_TRANSFER:
					$date = $item->getFunctions()->getTemporaryTransfer()->getTransferDate();
					break;
				case self::TEMP_TRANSFER_RETURN:
					$date = $item->getFunctions()->getTemporaryTransferReturn()->getTransferDate();
					break;
			}
		}
		return $date;
	}
	
	public static function getTransferBy($listname)
	{
		$personid = NULL;
		$list = self::getTransferList($listname);
		if (isset($list) && count($list) > 0)
		{
			$item = current($list);
			switch ($listname)
			{
				case self::LOGIN:
					$personid = $item->getFunctions()->getLogin()->getLoginBy();
					break;
				case self::LOGOUT:
					$personid = $item->getFunctions()->getLogout()->getLogoutBy();
					break;
			}
		}
		return $personid;
	}
	
	public static function getTransferFrom($listname)
	{
		$locid = NULL;
		$list = self::getTransferList($listname);
		if (isset($list) && count($list) > 0)
		{
			$item = current($list);
			switch ($listname)
			{
				case self::LOGIN:
					$locid = $item->getFunctions()->getLogin()->getTransferFrom();
					break;
				case self::LOGOUT:
					$locid = $item->getFunctions()->getLogout()->getTransferFrom();
					break;
				case self::TEMP_TRANSFER:
					$locid = $item->getFunctions()->getTemporaryTransfer()->getTransferFrom();
					break;
				case self::TEMP_TRANSFER_RETURN:
					$locid = $item->getFunctions()->getTemporaryTransferReturn()->getTransferFrom();
					break;
			}
		}
		return $locid;
	}
	
	
	public static function getTransferTo($listname)
	{
		$locid = NULL;
		$list = self::getTransferList($listname);
		if (isset($list) && count($list) > 0)
		{
			$item = current($list);
			switch ($listname)
			{
				case self::LOGIN:
					$locid = $item->getFunctions()->getLogin()->getTransferTo();
					break;
				case self::LOGOUT:
					$locid = $item->getFunctions()->getLogout()->getTransferTo();
					break;
				case self::TEMP_TRANSFER:
					$locid = $item->getFunctions()->getTemporaryTransfer()->getTransferTo();
					break;
				case self::TEMP_TRANSFER_RETURN:
					$locid = $item->getFunctions()->getTemporaryTransferReturn()->getTransferTo();
					break;
			}
		}
		return $locid;
	}
	
	public static function getTransferDepartmentID($listname)
	{
		$deptid = NULL;
		$list = self::getTransferList($listname);
		if (isset($list) && count($list) > 0)
		{
			$item = current($list);
			$deptid = $item->getDepartmentID();
		}
		
		return $deptid;
	}
	
	public static function getTransferCuratorID($listname)
	{
		$curatorid = NULL;
		$list = self::getTransferList($listname);
		if (isset($list) && count($list) > 0)
		{
			$item = current($list);
			$curatorid = $item->getCuratorID();
		}
		return $curatorid;
	}
	
	/*
	 * Get  the save state
	 */
	public static function getSaveStatus()
	{
		if (!isset(self::getTransferNamespace()->savestatus))
		{
			self::getTransferNamespace()->savestatus = self::SAVE_STATE_IDLE;
		}
		return self::getTransferNamespace()->savestatus;
	}
	
	/*
	 * Set  the save status
	 */
	public static function setSaveStatus($savestatus)
	{
		self::getTransferNamespace()->savestatus = $savestatus;
	}
	
	public static function clearBulkLocation()
	{
		unset(self::getTransferNamespace()->bulklocation);
	}
	
	/*
	 * Get  the save state
	 */
	public static function getBulkLocation()
	{
		if (!isset(self::getTransferNamespace()->bulklocation))
		{
			self::getTransferNamespace()->bulklocation = -1;
		}
		return self::getTransferNamespace()->bulklocation;
	}
	
	/*
	 * Set  the save status
	 */
	public static function setBulkLocation($bulklocation)
	{
		self::getTransferNamespace()->bulklocation = $bulklocation;
	}		
}

?>