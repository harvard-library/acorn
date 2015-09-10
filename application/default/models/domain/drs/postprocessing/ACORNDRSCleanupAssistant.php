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
 * Finds all files that are still "Pending"
 * Verifies that a LOADREPORT exists for the batch (DRSReportGrabber)
 * Updates the status to "Complete"
 * Clears out the staging directories for processed images.
 *
 * @access public
 * @author Valdeva Crema
 */
class ACORNDRSCleanupAssistant extends DRSStagingDirectoryCleanupAssistant
{

	/**
	 * Determines all of the 'pending' batch names and then passes them to the superclass
	 * 
	 */
	public function __construct()
	{
		$batchNames = $this->getCompletedBatches();
		parent::__construct($batchNames);
	}
	
	/**
	 * 1. Grabs the "Pending" files
	 * 2. Uses the DRSReportGrabber to pull all of the LOADREPORTs from the directory.
	 * 3. For any completed files, updates the DRSStatus in the DB to "Complete"
	 * 4. Returns the batch names to use as an execute.
	 * 
	 * @return array of report names or an empty array
	 */
	private function getCompletedBatches()
    {
    	$files = FilesDAO::getFilesDAO()->getPendingDRSFiles();
    	//First get the batchNames of any pending batch names
   		$pendingBatchNames = array();
   		foreach ($files as $file)
   		{
   			$pendingBatchNames[$file->getDrsBatchName()] = $file->getDrsBatchName();
   		}
   		//If any of the files have a DRSStatus of pending, also see if the load has been completed.
   		if (!empty($pendingBatchNames))
   		{
   			$drsReportProcesser = new DRSReportProcesser($pendingBatchNames);
   			$drsReportProcesser->runService();
   			$loadReports = $drsReportProcesser->getReports();
   			//The files to update the DRS info
   			$reportfiles = array();
   			//The batch names for the reports.
   			$reportbatchnames = array();
   			//Put all of the load files into one array.
   			foreach ($loadReports as $loadReport)
   			{
   				$files = $loadReport->getFiles();
   				$reportfiles = array_merge($reportfiles, $files);
   				array_push($reportbatchnames, $loadReport->getBatchDirectoryName());
   			}
   			
   			$success = FilesDAO::getFilesDAO()->updateDRSInfo($reportfiles);
   			//If the DRS info was updated properly, then run the cleanup assistant
   			//to remove the directories from the staging file directory area.
   			if ($success)
   			{
   				return $reportbatchnames;  		
   			}
   		}
   		return array();
    }
}
?>