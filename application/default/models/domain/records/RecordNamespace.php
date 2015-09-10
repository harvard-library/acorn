<?php
/**
 * RecordNamespace
 *
 * @author vcrema
 * Created April 13, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */


class RecordNamespace extends Zend_Session_Namespace
{
	
	const RECORD_NAMESPACE_NAME = 'records';
	
	const IDENTIFICATION = 'tab0';
	const LOGIN = 'tab1';
	const PROPOSAL = 'tab2';
	const REPORT = 'tab3';
	const LOGOUT = 'tab4';
	const FILES = 'tab5';
	const TEMP_TRANSFER = 'tab6';
	const TEMP_TRANSFER_RETURN = 'tab7';
	
	const OSW_FILES = 'tab1';
	
	const SAVE_STATE_IDLE = 'idle';
	const SAVE_STATE_SUCCESS = 'success';
	const SAVE_STATE_FAILED = 'failed';
	
	const DEFAULT_STATUS_MESSAGE = 'Your record has been saved!';
	
	const RECORD_TYPE_ITEM = 'item';
	const RECORD_TYPE_OSW = 'osw';
	
	private static $RECORD_NAMESPACE;

	/*
	 * The Namespace holds the items specific to the
	 * items and osw.
	 */
	public static function getRecordNamespace()
	{
		if (is_null(self::$RECORD_NAMESPACE))
		{
			self::$RECORD_NAMESPACE = new RecordNamespace(self::RECORD_NAMESPACE_NAME);
		}
		return self::$RECORD_NAMESPACE;
	}
	
	public static function clearAll()
	{
		self::getRecordNamespace()->unsetAll();
	}
	
	public static function clearCurrentItem()
	{
		unset(self::getRecordNamespace()->currentitem);
	}
	
	public static function clearCurrentOSW()
	{
		unset(self::getRecordNamespace()->currentosw);
	}
	
	public static function clearOriginalItem()
	{
		unset(self::getRecordNamespace()->originalitem);
	}
	
	public static function clearOriginalOSW()
	{
		unset(self::getRecordNamespace()->originalosw);
	}
	
	public static function clearRenderTab()
	{
		unset(self::getRecordNamespace()->rendertab);
	}
	
	

	/**
	 * Get the current item.
	 * @return Item
	 */
	public static function getCurrentItem()
	{
		return self::getRecordNamespace()->currentitem;
	}
	
	/*
	 * Set the current item.
	 */
	public static function setCurrentItem($currentitem)
	{
		self::getRecordNamespace()->currentitem = $currentitem;
	}
	
	/**
	 * Get the current osw.
	 * @return OnSiteWork
	 */
	public static function getCurrentOSW()
	{
		return self::getRecordNamespace()->currentosw;
	}
	
	/*
	 * Set the current osw.
	 */
	public static function setCurrentOSW($currentosw)
	{
		self::getRecordNamespace()->currentosw = $currentosw;
	}
	
	/**
	 * Get the original item that matches the database.
	 * @return Item - the item before it was modified.
	 */
	public static function getOriginalItem()
	{
		return self::getRecordNamespace()->originalitem;
	}
	
	/*
	 * Set the original item.
	 */
	public static function setOriginalItem($originalitem)
	{
		self::getRecordNamespace()->originalitem = $originalitem;
	}
	
	/*
	 * Get the original osw.
	 */
	public static function getOriginalOSW()
	{
		return self::getRecordNamespace()->originalosw;
	}
	
	/*
	 * Set the original osw.
	 */
	public static function setOriginalOSW($originalosw)
	{
		self::getRecordNamespace()->originalosw = $originalosw;
	}
	
	/*
	 * Get  the render tab
	 */
	public static function getRenderTab()
	{
		if (!isset(self::getRecordNamespace()->rendertab))
		{
			self::getRecordNamespace()->rendertab = self::IDENTIFICATION;
		}
		return self::getRecordNamespace()->rendertab;
	}
	
	/*
	 * Set  the render tab
	 */
	public static function setRenderTab($rendertab)
	{
		self::getRecordNamespace()->rendertab = $rendertab;
	}
	
	
	/*
	 * Get  the save state
	 */
	public static function getSaveStatus()
	{
		if (!isset(self::getRecordNamespace()->savestatus))
		{
			self::getRecordNamespace()->savestatus = self::SAVE_STATE_IDLE;
		}
		return self::getRecordNamespace()->savestatus;
	}
	
	/*
	 * Set  the save status
	 */
	public static function setSaveStatus($savestatus)
	{
		self::getRecordNamespace()->savestatus = $savestatus;
	}
	
	/*
	 * Get  the save state
	*/
	public static function getStatusMessage()
	{
		if (!isset(self::getRecordNamespace()->statusmessage))
		{
			self::getRecordNamespace()->statusmessage = self::DEFAULT_STATUS_MESSAGE;
		}
		return self::getRecordNamespace()->statusmessage;
	}
	
	/*
	 * Set  the save status
	*/
	public static function setStatusMessage($statusmessage)
	{
		self::getRecordNamespace()->statusmessage = $statusmessage;
	}
	
	/*
	 * Get  the record type
	 */
	public static function getRecordType()
	{
		if (!isset(self::getRecordNamespace()->recordtype))
		{
			self::getRecordNamespace()->recordtype = self::RECORD_TYPE_ITEM;
		}
		return self::getRecordNamespace()->recordtype;
	}
	
	/*
	 * Set  the save status
	 */
	public static function setRecordType($recordtype)
	{
		self::getRecordNamespace()->recordtype = $recordtype;
	}
}

?>