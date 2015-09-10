<?php
/**
 * PeopleNamespace
 *
 * @author vcrema
 * Created April 13, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */


class PeopleNamespace extends Zend_Session_Namespace
{
	
	const PEOPLE_NAMESPACE_NAME = 'people';
	
	const SAVE_STATE_IDLE = 'idle';
	const SAVE_STATE_SUCCESS = 'success';
	const SAVE_STATE_FAILED = 'failed';
	
	private static $PEOPLE_NAMESPACE;

	/*
	 * The Namespace holds the items specific to the
	 * people.
	 */
	public static function getPeopleNamespace()
	{
		if (!isset(self::$PEOPLE_NAMESPACE))
		{
			self::$PEOPLE_NAMESPACE = new PeopleNamespace(self::PEOPLE_NAMESPACE_NAME);
		}
		return self::$PEOPLE_NAMESPACE;
	}
	
	public static function clearAll()
	{
		self::getPeopleNamespace()->unsetAll();
	}
	
	public static function clearCurrentPerson()
	{
		unset(self::getPeopleNamespace()->currentperson);
	}
	
	public static function clearOriginalPerson()
	{
		unset(self::getPeopleNamespace()->originalperson);
	}

	
	/*
	 * Get the current person.
	 * 
	 * @return People
	 */
	public static function getCurrentPerson()
	{
		return self::getPeopleNamespace()->currentperson;
	}
	
	/*
	 * Set the current person.
	 */
	public static function setCurrentPerson($currentperson)
	{
		self::getPeopleNamespace()->currentperson = $currentperson;
	}
	
	/*
	 * Get the original person.
	 * 
	 * @return People
	 */
	public static function getOriginalPerson()
	{
		return self::getPeopleNamespace()->originalperson;
	}
	
	/*
	 * Set the original person.
	 */
	public static function setOriginalPerson($originalperson)
	{
		self::getPeopleNamespace()->originalperson = $originalperson;
	}
	
	/*
	 * Get  the save state
	 */
	public static function getSaveStatus()
	{
		if (!isset(self::getPeopleNamespace()->savestatus))
		{
			self::getPeopleNamespace()->savestatus = self::SAVE_STATE_IDLE;
		}
		return self::getPeopleNamespace()->savestatus;
	}
	
	/*
	 * Set  the save status
	 */
	public static function setSaveStatus($savestatus)
	{
		self::getPeopleNamespace()->savestatus = $savestatus;
	}
}

?>