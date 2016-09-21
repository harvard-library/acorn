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
 * PeopleNamespace
 *
 * @author vcrema
 * Created April 13, 2009
 *
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