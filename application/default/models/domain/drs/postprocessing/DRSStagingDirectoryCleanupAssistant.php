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
 * Deletes the directories the batches that are supplied.
 *
 * @access public
 * @author Valdeva Crema
 */
class DRSStagingDirectoryCleanupAssistant extends DRSService
{
	/**
	 * The DRSDropperConfiguration
	 *
	 * @var DRSDropperConfig
	 */
	private $configuration = NULL;
	
	/**
	 * The batchName
	 * 
	 * @var array
	 */
	private $batchNames = NULL;
	
	/**
	 * Requires batch names to be passed in.
	 * 
	 * @param array of batch names
	 */
	public function __construct(array $batchNames)
	{
		$this->batchNames = $batchNames;
	}
	
    /**
     * Run the program
     *
     * @access protected
     * @return mixed
     */
    protected function execute()
    {
    	$stagingdir = $this->getConfiguration()->getStagingFileDirectory();
    	foreach ($this->batchNames as $batchName)
    	{
    		//All batches are in the same directory in DRS1
    		if ($this->getConfiguration()->getDRSVersion() == DRSDropperConfig::DRS)
    		{
    			$projectPath = $stagingdir;
    		}
    		//In DRS2, each user has a separate project directory
    		else
    		{
    			$filesInBatch = FilesDAO::getFilesDAO()->getFilesInBatch($batchName);
    			if (empty($filesInBatch))
    			{
    				Logger::log("Could not determine userID for batch " . $batchName . ". Skipping directory deletion for batch.", Zend_Log::ERR);
    				continue;
	     		}
    			$file = current($filesInBatch);
    			$userid = $file->getEnteredBy()->getPrimaryKey();
    			$projectName = "user" . $userid . "project";
    			$projectPath = $stagingdir . "/" . $projectName;	
    		}
    		
    		$batchPath = $projectPath . "/" . $batchName;
    		
    		
    		//If the batch no longer exists for some reason, skip it but log it
    		if (!file_exists($batchPath))
    		{
    			Logger::log("Batch does not exist " . $batchPath . ". Skipping directory deletion for batch.", Zend_Log::ERR);
    			$this->removeLoadReportAndTransferFiles($batchName);
    			continue;
    		}
    		
    		//Now remove the batch directories along with its subdirectories.
    		$success = $this->performDelete($batchPath);
    		//If it is successful, then also remove the said directory
	     	if ($success)
	     	{
	     		$success = rmdir($batchPath);
	     	}
	     	if (!$success) 
	     	{
	     		throw new DRSServiceException("Could not delete the directory " . $batchPath);
	     	}
    		
    		$this->removeAuxillaryDirectory($projectPath, $batchName);
    		$this->removeLoadReportAndTransferFiles($batchName);
    	}
	}
	
	private function removeAuxillaryDirectory($projectPath, $batchName)
	{
		$auxDir = $this->getConfiguration()->getAuxillaryDirectoryName();
    	//Also remove the sync directory for this batch
		$success = $this->performDelete($projectPath . "/" . $auxDir . "/" . $batchName);
		if ($success)
		{
			$success = rmdir($projectPath . "/" . $auxDir . "/" . $batchName);
		}
		if (!$success)
		{
			throw new DRSServiceException("Could not delete the directory " . $projectPath . "/" . $auxDir . "/" . $batchName);
		}
	}
	
	private function removeLoadReportAndTransferFiles($batchName)
	{
		$success = TRUE;
		$loadReport = $this->getConfiguration()->getStagingFileDirectory() . "/LOADREPORT_" . $batchName;
		//All batches are in the same directory in DRS1
		if ($this->getConfiguration()->getDRSVersion() == DRSDropperConfig::DRS2)
		{
			$loadReport .= ".txt";
		}
		$transferMarker = $this->getConfiguration()->getStagingFileDirectory() . "/TRANSFERRED_" . $batchName;
		
		if (file_exists($loadReport))
		{
			$success = unlink($loadReport);
		}
		else
		{
			Logger::log("Load report " . $loadReport . " does not exist.");
		}
		
		if (file_exists($transferMarker))
		{
			$success = $success && unlink($transferMarker);
		}
		else
		{
			Logger::log("Transfer marker " . $transferMarker . " does not exist.");
		}
		
		if (!$success)
		{
			throw new DRSServiceException("Could not delete the load report or transfer marker " . $loadReport . ", " . $transferMarker);
		}
	}
    
    /**
     * Recursively deletes the batch directory 
     */
    private function performDelete($path)
    {
    	$filelist = scandir($path);
    	
    	$success = TRUE;
    	foreach ($filelist as $filename)
     	{
     		if ($filename != "." && $filename != "..")
     		{
     			//If it is a file, delete it
	     		if (is_file($path . "/" . $filename))
	     		{
	     			Logger::log("Removing: " . $path . "/" . $filename);
	     			$success = $success && unlink($path . "/" . $filename);
	      		}
	      		//Otherwise, recurse and delete
	      		else 
	      		{
	      			$recursivesuccess = $this->performDelete($path . "/" . $filename);
	      			//If the files below were deleted, then go ahead and remove the directory.
	      			if ($recursivesuccess)
	      			{
	      				$success = $success && rmdir($path . "/" . $filename);
	      			}
	      			//Otherwise, this was not successful.
	      			else 
	      			{
	      				$success = FALSE;
	      			}
	      		}
     		}
     	}
     	return $success;
    }


} /* end of class DRSStagingFileCleanupAssistant */

?>