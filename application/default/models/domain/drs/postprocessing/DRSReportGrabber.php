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
 * Utilized by a cronjob to grab the success reports from the dropbox.
 *
 * @access public
 * @author Valdeva Crema
 */
class DRSReportGrabber extends DRSReportProcesser
{
		
    /**
     * Run the program
     *
     * @access protected
     * @return mixed
     */
    protected function execute()
    {    
    	//Find the load reports and bring them to the staging files directory.
    	$success = $this->getLoadReportsFromDropbox();
    	
    	parent::execute();
    }
    
    
    /**
     * Moves any load reports in the dropbox to the staging files directory
     */
    private function getLoadReportsFromDropbox()
    {
    	$dropboxstring = $this->getConfiguration()->getDRSDropboxString();
    	//In DRS1, the load reports are in the directories.
    	if ($this->getConfiguration()->getDRSVersion() == DRSDropperConfig::DRS)
    	{
    		$command = "scp " . $dropboxstring . "/LOADREPORT* " . $this->getConfiguration()->getStagingFileDirectory() . "/.";
    	}
    	//In DRS2, the load reports are under the batch names.
    	else
    	{
    		$command = "scp " . $dropboxstring . "/*/LOADREPORT* " . $this->getConfiguration()->getStagingFileDirectory() . "/.";
    	}
    	exec($command);
    	
    	$this->removeDropboxReports();
    }
    
    /**
     * Removes the successfully copied reports from the dropboxes
     */
    private function removeDropboxReports()
    {
    	$dropboxstring = $this->getConfiguration()->getDRSDropboxString();
    	$dropboxstringwithoutdirectory = substr($dropboxstring, 0, strpos($dropboxstring, ":"));
    	$filelist = scandir($this->getConfiguration()->getStagingFileDirectory());
    	foreach ($filelist as $filename)
    	{
    		//Verify that the file is a LOADREPORT file.
    		if (strpos($filename, "LOADREPORT") === 0)
    		{
    			//In DRS1, the load reports are in the directories.
		    	if ($this->getConfiguration()->getDRSVersion() == DRSDropperConfig::DRS)
		    	{
		    		$command = "ssh " . $dropboxstringwithoutdirectory . " 'rm incoming/" . $filename . "'";
		    	}
		    	//In DRS2, the load reports are under the batch names.
		    	else
		    	{
		    		$batchname = substr($filename, 11);
		    		$batchname = str_replace(".txt", "", $batchname);
		    		$command = "ssh " . $dropboxstringwithoutdirectory . " 'rm -r incoming/" . $batchname . "'";
		    	}
		    	Logger::log($command);
		    	exec($command);	
    		}
    	}
    }


} /* end of class DRSReportGrabber */

?>