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
 * AcornNamespace
 *
 * @author vcrema
 * Created September 29, 2009
 *
 */


class AcornNamespace extends Zend_Session_Namespace
{
	
	const ACORN_NAMESPACE_NAME = 'acorn';
	
	private static $ACORN_NAMESPACE;

	/*
	 * The Namespace holds general namespace items.
	 * 
	 * @return AcornNamespace
	 */
	public static function getAcornNamespace()
	{
		if (is_null(self::$ACORN_NAMESPACE))
		{
			self::$ACORN_NAMESPACE = new AcornNamespace(self::ACORN_NAMESPACE_NAME);
		}
		return self::$ACORN_NAMESPACE;
	}
	
	public static function clearAll()
	{
		self::getAcornNamespace()->unsetAll();
	}
	

	/*
	 * Get the action to redirect to.
	 */
	public static function getRedirectAction()
	{
		return self::getAcornNamespace()->redirectaction;
	}
	
	/*
	 * Set the action to redirect to.
	 */
	public static function setRedirectAction($redirectaction)
	{
		self::getAcornNamespace()->redirectaction = $redirectaction;
	}

	/*
	 * Get the controller to redirect to.
	 */
	public static function getRedirectController()
	{
		return self::getAcornNamespace()->redirectcontroller;
	}
	
	/*
	 * Set the controller to redirect to.
	 */
	public static function setRedirectController($redirectcontroller)
	{
		self::getAcornNamespace()->redirectcontroller = $redirectcontroller;
	}
	
	/*
	 * Get the params to redirect to.
	 */
	public static function getRedirectParams()
	{
		return self::getAcornNamespace()->redirectparams;
	}
	
	/*
	 * Set the params to redirect to.
	 */
	public static function setRedirectParams($redirectparams)
	{
		self::getAcornNamespace()->redirectparams = $redirectparams;
	}

}

?>