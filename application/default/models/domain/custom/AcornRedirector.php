<?php
/**
 *
 * @author vac765
 * Created Apr 20, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */

/**
 * AcornRedirector
 * 
 * 
 */
class AcornRedirector extends Zend_Controller_Action_Helper_Redirector
{
	private static $ACORN_REDIRECTOR;
	
	public static function getAcornRedirector()
	{
		if (!isset(self::$ACORN_REDIRECTOR))
		{
			self::$ACORN_REDIRECTOR = new AcornRedirector();
		}
		return self::$ACORN_REDIRECTOR;
	}
	
    /**
     * Set a redirect URL of the form /module/controller/action/params
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array  $params
     * @return void
     */
    public function setGoto($action, $controller = null, $module = null, array $params = array())
    {
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        $request    = $this->getRequest();

        if (null === $module) {
            $module = $request->getModuleName();
            if ($module == $dispatcher->getDefaultModule()) {
                $module = '';
            }
        }

        if (null === $controller) {
            $controller = $request->getControllerName();
            if (empty($controller)) {
                $controller = $dispatcher->getDefaultControllerName();
            }
        }

        $paramsNormalized = array();
        foreach ($params as $key => $value) {
        	Logger::log($key . ' ' . $value);
            $paramsNormalized[] = $key . '/' . $value;
        }
        $paramsString = implode('/', $paramsNormalized);

        if (!empty($paramsString)) {
            $url = $module . '/' . $controller . '/' . $action . '/' . $paramsString;
        } else {
            if ($action != $dispatcher->getDefaultAction()) {
                $url = $module . '/' . $controller . '/' . $action;
            } else {
                if ($controller != $dispatcher->getDefaultControllerName()) {
                    $url = $module . '/' . $controller;
                } else {
                    $url = $module;
                }
            }
        }

        $url = '/' . trim($url, '/');

        $url = $this->_prependBase($url);

        $this->_redirect($url);
    }

}

