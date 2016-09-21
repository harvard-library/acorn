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
 * GroupNamespace
 *
 * @author vcrema
 * Created April 13, 2009
 *
 */


class GroupNamespace extends Zend_Session_Namespace
{
	
	const GROUP_NAMESPACE_NAME = 'groups';
	
	const IDENTIFICATION = 'tab0';
	const LOGIN = 'tab1';
	const PROPOSAL = 'tab2';
	const REPORT = 'tab3';
	const LOGOUT = 'tab4';
	const FILES = 'tab5';
	const TEMP_TRANSFER = 'tab6';
	const TEMP_TRANSFER_RETURN = 'tab7';
	
	const SAVE_STATE_IDLE = 'idle';
	const SAVE_STATE_SUCCESS = 'success';
	const SAVE_STATE_FAILED = 'failed';
	
	const DEFAULT_STATUS_MESSAGE = 'Your group record has been saved!';
	
	private static $GROUP_NAMESPACE;

	/*
	 * The Namespace holds the items specific to the
	 * group.
	 */
	public static function getGroupNamespace()
	{
		if (is_null(self::$GROUP_NAMESPACE))
		{
			self::$GROUP_NAMESPACE = new GroupNamespace(self::GROUP_NAMESPACE_NAME);
		}
		return self::$GROUP_NAMESPACE;
	}
	
	public static function clearAll()
	{
		self::getGroupNamespace()->unsetAll();
	}
	
	public static function clearCurrentGroup()
	{
		unset(self::getGroupNamespace()->currentgroup);
	}
	
	public static function clearOriginalGroup()
	{
		unset(self::getGroupNamespace()->originalgroup);
	}

	
	public static function clearRenderTab()
	{
		unset(self::getGroupNamespace()->rendertab);
	}

	/*
	 * Get the current group.
	 * 
	 * @return Group
	 */
	public static function getCurrentGroup()
	{
		return self::getGroupNamespace()->currentgroup;
	}
	
	/*
	 * Set the current group.
	 */
	public static function setCurrentGroup($currentgroup)
	{
		self::getGroupNamespace()->currentgroup = $currentgroup;
	}
	
	/*
	 * Get the original group.
	 * 
	 * @return Group
	 */
	public static function getOriginalGroup()
	{
		return self::getGroupNamespace()->originalgroup;
	}
	
	/*
	 * Set the original group.
	 */
	public static function setOriginalGroup($originalgroup)
	{
		self::getGroupNamespace()->originalgroup = $originalgroup;
	}
	
	/*
	 * Get  the render tab
	 */
	public static function getRenderTab()
	{
		if (!isset(self::getGroupNamespace()->rendertab))
		{
			self::getGroupNamespace()->rendertab = self::IDENTIFICATION;
		}
		return self::getGroupNamespace()->rendertab;
	}
	
	/*
	 * Set  the render tab
	 */
	public static function setRenderTab($rendertab)
	{
		self::getGroupNamespace()->rendertab = $rendertab;
	}
	
	/*
	 * Get  the save state
	 */
	public static function getSaveStatus()
	{
		if (!isset(self::getGroupNamespace()->savestatus))
		{
			self::getGroupNamespace()->savestatus = self::SAVE_STATE_IDLE;
		}
		return self::getGroupNamespace()->savestatus;
	}
	
	/*
	 * Set  the save status
	 */
	public static function setSaveStatus($savestatus)
	{
		self::getGroupNamespace()->savestatus = $savestatus;
	}
	
	/*
	 * Get  the save state
	*/
	public static function getStatusMessage()
	{
		if (!isset(self::getGroupNamespace()->statusmessage))
		{
			self::getGroupNamespace()->statusmessage = self::DEFAULT_STATUS_MESSAGE;
		}
		return self::getGroupNamespace()->statusmessage;
	}
	
	/*
	 * Set  the save status
	*/
	public static function setStatusMessage($statusmessage)
	{
		self::getGroupNamespace()->statusmessage = $statusmessage;
	}
}

?>