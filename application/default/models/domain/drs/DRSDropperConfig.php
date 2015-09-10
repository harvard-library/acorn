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
class DRSDropperConfig
    extends DRSServiceConfig
{
	const STAGING_FILE_DIRECTORY_NAME = 'stagingpath';
	const DRS2_STAGING_FILE_DIRECTORY_NAME = 'drs2stagingpath';
	const BB_CLIENT_PATH_NAME = 'bbclientpath';
	const BB2_CLIENT_PATH_NAME = 'bb2clientpath';
	const BB_SCRIPT_NAME = 'bbscriptname';
	const BB2_SCRIPT_NAME = 'bb2scriptname';
	const BATCH_TYPE_NAME = 'batchtype';
	const DRS_VERSION = 'drsversion';
	const DRS_DROPBOX_STRING = 'drsdropboxstring';
	const DRS2_DROPBOX_STRING = 'drs2dropboxstring';
	
	const DRS = 'drs';
	const DRS2 = 'drs2';
	const SYNC_DIRECTORY = 'sync';
	const AUX_DIRECTORY = '_aux';
	
    /**
     * The location of the batches on the local machine.
     *
     * @access private
     * @var String
     */
    private $stagingFileDirectory;

    /**
     * The location of the Batch Builder 1 command line client.
     *
     * @access private
     * @var String
     */
    private $bbClientPath;
    
    /**
     * The batch type being created by batch builder (eg deliverable)
     *
     * @var String
     */
    private $batchType;
	
	
	protected function setConfigs($conf)
	{
		parent::setConfigs($conf);
	}
	
	protected function verifyConfigSettings()
	{
		parent::verifyConfigSettings();
		if (is_null($this->get(self::BB_CLIENT_PATH_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::BB_CLIENT_PATH_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::BB2_CLIENT_PATH_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::BB2_CLIENT_PATH_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::BB_SCRIPT_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::BB_SCRIPT_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::BB2_SCRIPT_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::BB2_SCRIPT_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::STAGING_FILE_DIRECTORY_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::STAGING_FILE_DIRECTORY_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::DRS2_STAGING_FILE_DIRECTORY_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::DRS2_STAGING_FILE_DIRECTORY_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::BATCH_TYPE_NAME))) 
		{
			throw new DRSConfigException('Missing "' . self::BATCH_TYPE_NAME . '" configuration setting.');
		}
		elseif (is_null($this->get(self::DRS_VERSION)))
		{
			throw new DRSConfigException('Missing "' . self::DRS_VERSION . '" configuration setting.');
		}
		elseif (is_null($this->get(self::DRS_DROPBOX_STRING)))
		{
			throw new DRSConfigException('Missing "' . self::DRS_DROPBOX_STRING . '" configuration setting.');
		}
		elseif (is_null($this->get(self::DRS2_DROPBOX_STRING)))
		{
			throw new DRSConfigException('Missing "' . self::DRS2_DROPBOX_STRING . '" configuration setting.');
		}
	}
	
	/**
	 * @return String
	 */
	public function getBbClientPath()
	{
		if ($this->get(self::DRS_VERSION) == self::DRS)
		{
			return $this->get(self::BB_CLIENT_PATH_NAME);
		}
		elseif ($this->get(self::DRS_VERSION) == self::DRS2)
		{
			return $this->get(self::BB2_CLIENT_PATH_NAME);
		}
	}
	
	/**
	 * @return String
	 */
	public function getBbScriptName()
	{
		if ($this->get(self::DRS_VERSION) == self::DRS)
		{
			return $this->get(self::BB_SCRIPT_NAME);
		}
		elseif ($this->get(self::DRS_VERSION) == self::DRS2)
		{
			return $this->get(self::BB2_SCRIPT_NAME);
		}
	}
	
	/**
	 * @return String
	 */
	public function getStagingFileDirectory($stripfullpath = FALSE)
	{
		$stagingpath = "";
		if ($this->get(self::DRS_VERSION) == self::DRS)
		{
			$stagingpath = $this->get(self::STAGING_FILE_DIRECTORY_NAME);
		}
		//DRS2/BB2 must use image-<suffix>
		elseif ($this->get(self::DRS_VERSION) == self::DRS2)
		{
			$stagingpath = $this->get(self::DRS2_STAGING_FILE_DIRECTORY_NAME);
		}
		if ($stripfullpath)
		{
			return $this->stripFullPath($stagingpath);
		}
		return $stagingpath;
	}
	
	/**
	 * @return String
	 */
	public function getBatchType()
	{
		if ($this->get(self::DRS_VERSION) == self::DRS)
		{
			return $this->get(self::BATCH_TYPE_NAME);
		}
		//DRS2/BB2 must use image-<suffix>
		elseif ($this->get(self::DRS_VERSION) == self::DRS2)
		{
			return "image" . $this->get(self::BATCH_TYPE_NAME);
		}
	}
	
	/**
	 * Returns the auxillary directory name
	 * @return String
	 */
	public function getAuxillaryDirectoryName()
	{
		//sync for DRS1/BB1
		if ($this->get(self::DRS_VERSION) == self::DRS)
		{
			return self::SYNC_DIRECTORY;
		}
		//_aux for DRS2/BB2
		elseif ($this->get(self::DRS_VERSION) == self::DRS2)
		{
			return self::AUX_DIRECTORY;
		}
	}

	/**
	 * Returns the current DRS version to use (drs or drs2)
	 * @return String
	 */
	public function getDRSVersion()
	{
		return $this->get(self::DRS_VERSION);
	}

	/**
	 * Returns the string for sftp-ing the files
	 * @return String
	 */
	public function getDRSDropboxString()
	{
		//DRS1 dropbox
		if ($this->get(self::DRS_VERSION) == self::DRS)
		{
			return $this->get(self::DRS_DROPBOX_STRING);
		}
		//DRS2 dropbox
		elseif ($this->get(self::DRS_VERSION) == self::DRS2)
		{
			return $this->get(self::DRS2_DROPBOX_STRING);
		}
		
	}

} /* end of class DRSDropperConfig */

?>