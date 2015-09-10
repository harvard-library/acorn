<?php
/**
 * AcornNamespace
 *
 * @author vcrema
 * Created September 29, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
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