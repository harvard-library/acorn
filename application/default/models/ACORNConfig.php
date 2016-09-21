<?php
/**********************************************************************
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
 */

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
	const FILE_HANDLER = 'filehandler';
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
	public function getFileHandler()
	{
	    $fileHandler = $this->get(self::FILE_HANDLER);
	    
	    if (is_null($fileHandler))
		{
			throw new AcornConfigException('Missing "' . self::FILE_HANDLER . '" configuration setting.');
		}
		else
		{
		    return $fileHandler;
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
