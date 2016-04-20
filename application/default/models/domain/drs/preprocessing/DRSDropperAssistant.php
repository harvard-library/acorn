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
 * DRSDropperAssistant is the high-level model for transferring the files to the DRS.
 *
 * @access public
 * @author Valdeva Crema
 */
class DRSDropperAssistant extends DRSService
{
   
	
    /**
     * Run the program
     *
     * @access protected
     * @return mixed
     */
    protected function execute()
    {
    	$stagingfilesdir = $this->getConfiguration()->getStagingFileDirectory();
    	Logger::log('executing drs dropper ' . $stagingfilesdir);
    	//each user has a separate project directory in which the batches are created
    	$projectdirectories = scandir($stagingfilesdir);
    	//Loop through all of the projects and send the batches to the DRS
    	foreach ($projectdirectories as $projectDirectory)
    	{
    		if (is_dir($stagingfilesdir . "/" . $projectDirectory) && $projectDirectory != "." && $projectDirectory != "..")
    		{
    			$this->findDRSObjects($stagingfilesdir . "/" . $projectDirectory);
    		}
    	}
    	
    	
    }
    
    private function findDRSObjects($projectDirectory)
    {
    	Logger::log("Project dir: " . $projectDirectory);
    	//Now read through each file and attempt to send to the DRS
    	$handle=opendir($projectDirectory);

    	$exclusionDirs = array(".", "..", $this->getConfiguration()->getAuxillaryDirectoryName(), 'template');
    	
    	while (($name = readdir($handle))!==false)
    	{
    		if (is_dir($projectDirectory . "/" . $name) && !in_array($name, $exclusionDirs) && file_exists($projectDirectory . "/" . $name . "/batch.xml"))
    		{
    			$this->sendObjectsToDRS($projectDirectory . "/" . $name, $name);
    		}
    	}
    	 
    	closedir($handle);
    }
    
    /**
     * 1. Checks to see if the batch has already been sent
     * 2. If the batch has not been sent, it sends it and places a TRANSFERRED marker file for the batch
     * @param String $batchName - the name of the batch to be delivered
     */
    private function sendObjectsToDRS($directoryToTransfer, $batchName)
    {
    	//Get the list of files and directories as an array
    	$transferredFileMarkerList = scandir($this->getConfiguration()->getStagingFileDirectory());
    	
    	$transferredmarker = "TRANSFERRED_" . $batchName;
    	//If the batch has not been transferred, transfer it.
    	if (!in_array($transferredmarker, $transferredFileMarkerList))
    	{
    		 
    		//Transfer to the DRS
    		$command = "scp -r " . $directoryToTransfer . " " . $this->getConfiguration()->getDRSDropboxString();
    		Logger::log($command);
    		$output = shell_exec($command);
    		
    		//Place a marker file
    		if (!touch($this->getConfiguration()->getStagingFileDirectory() . "/" . $transferredmarker)) {
    			throw new DRSServiceException('Could not place transfer marker file: ' . $this->getConfiguration()->getStagingFileDirectory() . "/" . $transferredmarker);
    		}
    	}
    }
    
} /* end of class DRSDropperAssistant */

?>