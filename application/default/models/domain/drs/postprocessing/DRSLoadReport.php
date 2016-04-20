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
 * Parses the text DRS Load Report into an object.
 *
 * @access public
 * @author Valdeva Crema
 */
class DRSLoadReport
{
	const BATCH_ID_LINE_PREFIX = "Batch id:";
	const BATCH_NAME_LINE_PREFIX = "Batch name:";
	const BATCH_DIRECTORY_LINE_PREFIX = "Batch directory name:";
	const BATCH_LOADING_START_TIME_PREFIX = "Loading start time:";
	const BATCH_LOADING_END_TIME_PREFIX = "Loading end time:";
	const OWNER_LINE_PREFIX = "Owner(s):";
	const DRS2_FILELIST_SECTION_LINE_PREFIX = "OBJ-ID";
	const RELATION_LIST_SECTION_LINE_PREFIX = "A_OBJECTID";
	
	//The column location of the fields in the report (DRS2).
	const DRS2_OBJECT_ID_INDEX = 0;
	const DRS2_OBJ_DELIVERABLE_URI_INDEX = 1;
	const DRS2_OBJECT_URN_INDEX = 2;
	const DRS2_DEPOSITOR_NAME_INDEX = 3;
	const DRS2_OBJECT_OWNER_SUPPLIED_NAME_INDEX = 4;
	const DRS2_BILLING_INDEX = 5;
	const DRS2_OWNER_INDEX = 6;
	const DRS2_OBJECT_TYPE_INDEX = 7;
	const DRS2_OBJECT_ROLES_INDEX = 8;
	const DRS2_FILE_ID_INDEX = 9;
	const DRS2_FILE_URN_INDEX= 10;
	const DRS2_FILE_FORMAT_INDEX = 11;
	const DRS2_FILE_SIZE_INDEX = 12;
	const DRS2_FILE_OWNER_SUPPLIED_NAME_INDEX = 13;
	const DRS2_FILE_ORIGINAL_PATH_INDEX = 14;
	
    /**
     * The name of the batch directory
     *
     * @access private
     * @var String
     */
    private $batchDirectoryName;
    
	/**
     * The name of the batch
     *
     * @access private
     * @var String
     */
    private $batchName;
    
    /**
     * The owner of the deposit
     *
     * @access private
     * @var String
     */
    private $owner;

    /**
     * The metadata for the objects deposited into the drs.
     *
     * @access private
     * @var array of DRSLoadReportFile objects
     */
    private $files = array();

    /**
     * The batch ID from the report
     *
     * @access private
     * @var String
     */
    private $batchID;
    
    /**
	 * The path to the report.
	 */
    private $pathToReport;
    
   
    public function __construct($pathtoreport)
    {
    	$this->pathToReport = $pathtoreport;
    	$this->parseTextReport();
    }
    
    /**
     * This method is modeled after the method in the WAXi project
     * in the RepositoryBatchLoadReport file.
     * 
     * Parse the report and extract all batch
     * and file information.
     * 
     * There are 3 different sections in the loader report:
     * 1. batch summary
     * 2. file list
     * 3. relationship list
     * 
     * This method relies on knowing which of the 3 sections it is in.
     * 
     * @access private
     * @return DRSLoadReport
     */
    private function parseTextReport()
    {
        $lines = file($this->pathToReport, FILE_IGNORE_NEW_LINES);
        $loadReport = NULL;
		if (is_array($lines))
		{
			$section = "batch_summary";
			foreach ($lines as $line)
			{
				if ($section == "batch_summary") 
				{
                	if (strpos($line, self::BATCH_ID_LINE_PREFIX) === 0)
                	{
                    	// BATCH ID
                    	$batchID = trim(substr($line, strlen(self::BATCH_ID_LINE_PREFIX)));
                    	$this->setBatchID($batchID);
                	} 
                	elseif (strpos($line, self::BATCH_DIRECTORY_LINE_PREFIX) === 0)
                	{
                    	// BATCH DIRECTORY NAME
                    	$batchDir = trim(substr($line, strlen(self::BATCH_DIRECTORY_LINE_PREFIX)));
                    	$this->setBatchDirectoryName($batchDir);
                	} 
                	elseif (strpos($line, self::BATCH_NAME_LINE_PREFIX) === 0)
                	{
                    	// BATCH NAME
                    	$batchName = trim(substr($line, strlen(self::BATCH_NAME_LINE_PREFIX)));
                    	$this->setBatchName($batchName);
                	} 
                	elseif (strpos($line, self::OWNER_LINE_PREFIX) === 0)
                	{
                    	// OWNERS
                    	$owner = trim(substr($line, strlen(self::OWNER_LINE_PREFIX)));
                    	$this->setOwner($owner);
                	} 
                	elseif (strpos($line, self::DRS2_FILELIST_SECTION_LINE_PREFIX) === 0)
                	{
                    	// done with batch summary section -
                    	// entering the file list section
                    	$section = "file_list";
                	} 
            	} 
            	elseif ($section == "file_list")
            	{ 
                	if (strpos($line, self::RELATION_LIST_SECTION_LINE_PREFIX) === 0){
                    	// done with file list section -
                    	// entering the relationship list section
                    	$section = "rel_list";
                	} 
                	else 
                	{
                		
                		// treat it as a file line
                    	$fields= explode("\t", $line);
                    	
                    	if (count($fields) > 11)
                    	{
                    		if (!$this->isDescriptor($fields))
                    		{
                    			$reportFile = $this->getDRS2LoadReportFile($fields);
								$this->addFile($reportFile);
                    		}
                    	} 
                    } 
                }
                elseif ($section == "rel_list")
                {
                	//Don't need this area
                	break;
                }
			}
		}
        return $loadReport;
    }
    
    /**
     * In DRS2, the descriptor files come back as report entries.  
     * @param array $fields
     * @param boolean - true if the entry is a descriptor.xml file, false otherwise.
     */
    private function isDescriptor(array $fields)
    {
    	return $fields[self::DRS2_FILE_ORIGINAL_PATH_INDEX] == 'descriptor.xml';	
    }

	
	/**
	 * Populates the DRSLoadReportFile with the proper DRS2 fields
	 * @param array - the values from the text report
	 * @param DRSLoadReportFile
	 */
	private function getDRS2LoadReportFile(array $fields)
	{
		$reportFile = new DRSLoadReportFile();
		$reportFile->setFilePath($fields[self::DRS2_FILE_ORIGINAL_PATH_INDEX]);
		$reportFile->setOwnerSuppliedName($fields[self::DRS2_FILE_OWNER_SUPPLIED_NAME_INDEX]);
		$reportFile->setFileSize($fields[self::DRS2_FILE_SIZE_INDEX]);
		$reportFile->setFileType($fields[self::DRS2_FILE_FORMAT_INDEX]);
		$reportFile->setObjectID($fields[self::DRS2_FILE_ID_INDEX]);
		$reportFile->setOwner($fields[self::DRS2_OWNER_INDEX]);
		$reportFile->setRole($fields[self::DRS2_OBJECT_TYPE_INDEX]);
		$reportFile->setUrn($fields[self::DRS2_FILE_URN_INDEX]);
		return $reportFile;
	}

    
	
	/**
	 * @return String
	 */
	public function getBatchDirectoryName()
	{
		return $this->batchDirectoryName;
	}
	
	/**
	 * @return String
	 */
	public function getBatchID()
	{
		return $this->batchID;
	}
	
	/**
	 * @return String
	 */
	public function getBatchName()
	{
		return $this->batchName;
	}
	
	/**
	 * @return array
	 */
	public function getFiles()
	{
		return $this->files;
	}
	
	/**
	 * @return String
	 */
	public function getOwner()
	{
		return $this->owner;
	}
	
	
	/**
	 * @param String $batchDirectoryName
	 */
	public function setBatchDirectoryName($batchDirectoryName)
	{
		$this->batchDirectoryName = $batchDirectoryName;
	}
	
	/**
	 * @param String $batchID
	 */
	public function setBatchID($batchID)
	{
		$this->batchID = $batchID;
	}
	
	/**
	 * @param String $batchName
	 */
	public function setBatchName($batchName)
	{
		$this->batchName = $batchName;
	}
	
	/**
	 * @param array $files
	 */
	public function setFiles($files)
	{
		$this->files = $files;
	}
	
	/**
	 * @param String $owner
	 */
	public function setOwner($owner)
	{
		$this->owner = $owner;
	}
	
	
    public function addFile(DRSLoadReportFile $file)
    {
    	$this->files[$file->getOwnerSuppliedName()] = $file;
    }
    
    public function removeFile($ownerSuppliedName)
    {
    	unset($this->files[$ownerSuppliedName]);
    }
	
	/**
	 * @return string
	 */
	public function getPathToReport()
	{
		return $this->pathToReport;
	}
	
	/**
	 * @param string $pathToReport
	 */
	public function setPathToReport($pathToReport)
	{
		$this->pathToReport = $pathToReport;
	}


} /* end of class DRSLoadReport */

?>