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
 * The configuration file for the DRSDropper
 *
 * @access public
 * @author Valdeva Crema
 */
class ACORNConfig extends DRSDropperConfig 
{
	//Configuration variables
	const ACORN_APPLICATION_DIRECTORY_NAME = 'appdirectory';
	const ACORN_URL_NAME = 'acornurl';
	const USER_REPORTS_DIRECTORY_NAME = 'userreportsdirectory';
	const USER_FILES_DIRECTORY_NAME = 'userfilesdirectory';
	const NRS_PATH = 'nrspath';
	
	protected function verifyConfigSettings()
	{
		parent::verifyConfigSettings();
		if (is_null($this->get(self::ACORN_APPLICATION_DIRECTORY_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::ACORN_APPLICATION_DIRECTORY_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::ACORN_URL_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::ACORN_URL_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::USER_FILES_DIRECTORY_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::USER_FILES_DIRECTORY_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::USER_REPORTS_DIRECTORY_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::USER_REPORTS_DIRECTORY_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::NRS_PATH))) 
		{
			throw new DRSConfigException('Missing "' . self::NRS_PATH . '" configuration setting.');
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
	
	public function getNRSPath()
	{
		$url = $this->get(self::NRS_PATH);
		$length = strlen($url);
		//If the last character is not a "/", then append it.
    	if (strrpos($url, "/") != $length-1)
    	{
    		$url .= "/";
    	}
		return $url;
	}
	
	public function getDRSVersion()
	{
		return $this->get(self::DRS_VERSION);
	}
	
} /* end of class ACORNConfig */

?>