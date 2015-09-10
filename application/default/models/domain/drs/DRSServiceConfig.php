<?php
/**********************************************************************
 * Copyright (c) 2010 by the President and Fellows of Harvard College
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA.
 *
 * Contact information
 *
 * Library Technology Services, Harvard University Information Technology
 * Harvard Library
 * Harvard University
 * Cambridge, MA  02138
 * (617)495-3724
 * hulois@hulmail.harvard.edu
 **********************************************************************/


/**
 * The basic configuration of a service.
 *
 * @access public
 * @author Valdeva Crema
 */
class DRSServiceConfig extends Zend_Config_Ini 
{
	const LOG_PATH_NAME = 'logpath';
	const LOG_LEVEL_NAME = 'loglevel';
	const ERROR_EMAILS_NAME = 'erroremails';
	
    /**
     * The place to write the logs.
     *
     * @access private
     * @var String
     */
    private $logPath;

    /**
     * The level of log to write.
     *
     * @access private
     * @var String
     */
    private $logLevel;

    /**
     * The addresses to send error emails to.
     *
     * @access private
     * @var array
     */
    private $errorEmailAddresses;

    public function __construct($filename, $section = 'dev')
    {
    	//$this->parseConfigFile();
    	parent::__construct($filename, $section, true);
    	$this->verifyConfigSettings();
    }
    
    /**
     * Parse the configuration file and verify that the mandatory settings exist.
     *
     * @param unknown_type $filepath
     */
	/*protected function parseConfigFile()
	{
		$conf = parse_ini_file(self::CONFIG_FILE_PATH);
		$this->verifyConfigSettings($conf);
		$this->setConfigs($conf);
	}*/
	
	protected function setConfigs($conf)
	{
		$this->setErrorEmailAddresses($conf[self::ERROR_EMAILS_NAME]);
		//$this->setLogLevel($conf[self::LOG_LEVEL_NAME]);
		//$this->setLogPath($conf[self::LOG_PATH_NAME]);
	}
	
	protected function verifyConfigSettings()
	{
		/*if (!array_key_exists(self::LOG_PATH_NAME, $conf)) 
		{
			throw new DRSConfigException('Missing "' . self::LOG_PATH_NAME . '" configuration setting.');
		}
		elseif (!array_key_exists(self::LOG_LEVEL_NAME, $conf)) 
		{
			throw new DRSConfigException('Missing "' . self::LOG_PATH_NAME . '" configuration setting.');
		}
		else*/
		if (is_null($this->get(self::ERROR_EMAILS_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::ERROR_EMAILS_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::LOG_PATH_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::LOG_PATH_NAME . '" configuration setting.');
		}
		
	}
	
	/**
	 * Strips the full path from the passed in path and
	 * returns the directory or filename.
	 * Ex: /home/acorn/test returns test
	 * 
	 * @param string - full path
	 * @param string - the stripped down directory/filename
	 */
	protected function stripFullPath($fullpath)
	{
		//If the fullpath ends with "/", then remove it.
		$name = rtrim($fullpath, "/");
		if (strrpos($name, "/public/") !== FALSE)
		{Logger::log("full path name: " . $name);
			//Get only the directory name, not the full path.
			$name = substr($name, strrpos($name, "/public/")+8);
			Logger::log("stripped name: " . $name);
		}
		return $name;
	}
	
	/**
	 * @return array
	 */
	public function getErrorEmailAddresses()
	{
		$erroremails = $this->get(self::ERROR_EMAILS_NAME);
		if (!is_array($erroremails))
		{
			$erroremails = array($erroremails);
		}
		return $erroremails;
	}
	
	/**
	 * @return String
	 */
	public function getLogLevel()
	{
		return $this->get(self::LOG_LEVEL_NAME);
	}
	
	/**
	 * @return String
	 */
	public function getLogPath()
	{
		return $this->get(self::LOG_PATH_NAME);
	}
	
	/**
	 * @param $errorEmailAddresses
	 */
	public function setErrorEmailAddresses($errorEmailAddresses)
	{
		if (!is_array($errorEmailAddresses))
		{
			$errorEmailAddresses = array($errorEmailAddresses);
		}
		$this->errorEmailAddresses = $errorEmailAddresses;
	}
	
	/**
	 * @param String $logLevel
	 */
	public function setLogLevel($logLevel)
	{
		$this->logLevel = $logLevel;
	}
	
	/**
	 * @param String $logPath
	 */
	public function setLogPath($logPath)
	{
		$this->logPath = $logPath;
	}
	
	
} /* end of class ServiceConfig */

?>