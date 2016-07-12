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
 * The configuration file for the filehander
 *
 * @access public
 * @author Valdeva Crema
 */
class ACORNConfig extends Zend_Config_Ini 
{
	//Configuration variables
	const ACORN_APPLICATION_DIRECTORY_NAME = 'appdirectory';
	const ACORN_URL_NAME = 'acornurl';
	const USER_REPORTS_DIRECTORY_NAME = 'userreportsdirectory';
	const USER_FILES_DIRECTORY_NAME = 'userfilesdirectory';
	const FILE_UPLOAD_PATH = 'fileuploadpath';
	const FILE_HANDLER = 'filehander';
	const LOG_PATH_NAME = 'logpath';
	const LOG_LEVEL_NAME = 'loglevel';
	const ERROR_EMAILS_NAME = 'erroremails';
	
	protected function verifyConfigSettings()
	{
		parent::verifyConfigSettings();
		if (is_null($this->get(self::ACORN_APPLICATION_DIRECTORY_NAME))) 
		{
			throw new AcornConfigException('Missing "' . self::ACORN_APPLICATION_DIRECTORY_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::ACORN_URL_NAME))) 
		{
			throw new AcornConfigException('Missing "' . self::ACORN_URL_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::USER_FILES_DIRECTORY_NAME))) 
		{
			throw new AcornConfigException('Missing "' . self::USER_FILES_DIRECTORY_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::USER_REPORTS_DIRECTORY_NAME))) 
		{
			throw new AcornConfigException('Missing "' . self::USER_REPORTS_DIRECTORY_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::FILE_UPLOAD_PATH))) 
		{
			throw new AcornConfigException('Missing "' . self::FILE_UPLOAD_PATH . '" configuration setting.');
		}
		elseif (is_null($this->get(self::FILE_HANDLER)))
		{
			throw new AcornConfigException('Missing "' . self::FILE_HANDLER . '" configuration setting.');
		}
		elseif (is_null($this->get(self::LOG_PATH_NAME)))
		{
			throw new AcornConfigException('Missing "' . self::LOG_PATH_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::LOG_LEVEL_NAME)))
		{
			throw new AcornConfigException('Missing "' . self::LOG_LEVEL_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::ERROR_EMAILS_NAME)))
		{
		throw new AcornConfigException('Missing "' . self::ERROR_EMAILS_NAME . '" configuration setting.');
		}
	}
	
	/**
	 * @return String
	 */
	public function getACORNApplicationDirectory()
	{
		return $this->get(self::ACORN_APPLICATION_DIRECTORY_NAME);
	}
	
	/**
	 * @return String
	 */
	public function getACORNUrl()
	{
		$url = $this->get(self::ACORN_URL_NAME);
		$length = strlen($url);
		//If the last character is not a "/", then append it.
    	if (strrpos($url, "/") != $length-1)
    	{
    		$url .= "/";
    	}
		return $url;
	}
	
	/**
	 * @return String
	 */
	public function getFilesDirectory($stripfullpath = FALSE)
	{
		if ($stripfullpath)
		{
			return $this->stripFullPath($this->get(self::USER_FILES_DIRECTORY_NAME));
		}
		return $this->get(self::USER_FILES_DIRECTORY_NAME);
	}
	
	/**
	 * @return String
	 */
	public function getReportsDirectory($stripfullpath = FALSE)
	{
		if ($stripfullpath)
		{
			return $this->stripFullPath($this->get(self::USER_REPORTS_DIRECTORY_NAME));
		}
		return $this->get(self::USER_REPORTS_DIRECTORY_NAME);
	}
	
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
	
} /* end of class ACORNConfig */

?>