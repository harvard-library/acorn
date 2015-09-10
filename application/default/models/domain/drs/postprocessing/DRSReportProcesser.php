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
 * Parses the reports in the staging files directory and updatest the database.
 *
 * @access public
 * @author Valdeva Crema
 */
class DRSReportProcesser extends DRSService
{
	/**
	 * An array of DRSLoadReports that was created.
	 *
	 * @var array
	 */
	private $reports = array();
	
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
	 * Allow optional batch names to be passed in.
	 * 
	 * @param array of batch names
	 */
	public function __construct(array $batchNames = NULL)
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
    	$filelist = $this->getLoadReportList();
    	$this->processBatchReports($filelist);
    }
    
    
    /**
     * Puts the load reports into an array to parse
     * @return array - the load report names
     */
    private function getLoadReportList()
    {
    	//If no batch name was passed in, then get all files/directories in the staging directory
    	//area.  All load reports will then be extracted.
    	if (is_null($this->batchNames))
    	{
    		$filelist = scandir($this->getConfiguration()->getStagingFileDirectory());
    	}
    	//Otherwise, extract only the load reports with the supplied batch names.
    	else
    	{
    		$filelist = array();
    		foreach ($this->batchNames as $batchName)
    		{
    			$reportName = "LOADREPORT_" . $batchName;
    			Logger::log($reportName);
    			//If the report is in the staging directory, then add it to the list to be parsed.
    			if (file_exists($this->getConfiguration()->getStagingFileDirectory() . "/" . $reportName))
    			{
    				array_push($filelist, $reportName);
    			}
    		}
    	}
    	return $filelist;
    }
    
    
    private function processBatchReports(array $filelist)
    {
    	//The files to update the DRS info
    	$reportfiles = array();
    	//The batch names for the reports.
    	$reportbatchnames = array();
    	Logger::log("Processing batch reports");
    	foreach ($filelist as $filename)
     	{
     		//Verify that the file is a LOADREPORT file.
     		if (strpos($filename, "LOADREPORT") === 0)
     		{
     			$drsLoadReport = new DRSLoadReport($this->getConfiguration()->getStagingFileDirectory() . "/" . $filename, $this->getConfiguration()->getDRSVersion());
     			$files = $drsLoadReport->getFiles();
     			$reportfiles = array_merge($reportfiles, $files);
     			array_push($reportbatchnames, $drsLoadReport->getBatchDirectoryName());
     			
     			array_push($this->reports, $drsLoadReport);
      		}
     	}
     	$success = FilesDAO::getFilesDAO()->updateDRSInfo($reportfiles);
     	
     	//If the database updates properly, then clean up the directories.
     	if ($success)
     	{
     		$cleaner = new DRSStagingDirectoryCleanupAssistant($reportbatchnames);
     		$cleaner->runService();
     	}
    }

	/**
	 * @return array
	 */
	public function getReports()
	{
		return $this->reports;
	}
	
	/**
	 * @param array $reports
	 */
	public function setReports($reports)
	{
		$this->reports = $reports;
	}


} /* end of class DRSReportProcesser */

?>