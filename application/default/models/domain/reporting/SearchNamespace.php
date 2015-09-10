<?php
/**
 * SearchNamespace
 *
 * @author vcrema
 * Created April 13, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */


class SearchNamespace extends Zend_Session_Namespace
{
	
	const SEARCH_NAMESPACE_NAME = 'groups';
	
	const SAVE_STATE_IDLE = 'idle';
	const SAVE_STATE_SUCCESS = 'success';
	const SAVE_STATE_FAILED = 'failed';
	
	private static $SEARCH_NAMESPACE;

	/*
	 * The Namespace holds the items specific to the
	 * (advanced) search.
	 */
	public static function getSearchNamespace()
	{
		if (is_null(self::$SEARCH_NAMESPACE))
		{
			self::$SEARCH_NAMESPACE = new SearchNamespace(self::SEARCH_NAMESPACE_NAME);
		}
		return self::$SEARCH_NAMESPACE;
	}
	
	public static function clearAll()
	{
		self::getSearchNamespace()->unsetAll();
	}
	
	public static function clearCurrentSearch()
	{
		unset(self::getSearchNamespace()->currentsearch);
	}

	/**
	 * Get the current search.
	 * 
	 * @return Search
	 */
	public static function getCurrentSearch()
	{
		return self::getSearchNamespace()->currentsearch;
	}
	
	/*
	 * Set the current search.
	 */
	public static function setCurrentSearch($currentsearch)
	{
		self::getSearchNamespace()->currentsearch = $currentsearch;
	}

	/*
	 * Get  the save state
	 */
	public static function getSaveStatus()
	{
		if (!isset(self::getSearchNamespace()->savestatus))
		{
			self::getSearchNamespace()->savestatus = self::SAVE_STATE_IDLE;
		}
		return self::getSearchNamespace()->savestatus;
	}
	
	/*
	 * Set  the save status
	 */
	public static function setSaveStatus($savestatus)
	{
		self::getSearchNamespace()->savestatus = $savestatus;
	}
}

?>