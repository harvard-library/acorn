<?php
/**
 * Record
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */

abstract class Record extends AcornClass
{
    private $purposeID = NULL;
	private $formatID = NULL;
	private $curatorID = NULL;
	private $approvingCuratorID = NULL;
	private $homeLocationID = NULL;
	private $title = NULL;
	private $departmentID = NULL;
	private $groupID = NULL;
	private $projectID = NULL;
	private $curators = NULL;
	private $comments = NULL;
	private $functions = NULL;
	private $nonDigitalImagesExist = NULL;
	private $files = NULL;
	private $recordType = NULL;
	private $manuallyClosed = FALSE;
	private $manuallyClosedDate = NULL;
	private $callNumbers = NULL;
	private $chargeToID = NULL;
	
	private $duplicateCallNumbers = NULL;
	
	/**
     * @access public
     * @return int
     */
    public function getPurposeID()
    {
    	return $this->purposeID;
    }
    
    /**
     * @access public
     * @return string
     */
    public function getRecordType()
    {
    	return $this->recordType;
    }
    
        
	/**
     * @access public
     * @param  string
     */
    public function setRecordType($recordType)
    {
    	$this->recordType = $recordType;
    }
    
	/**
     * @access public
     * @return int
     */
    public function getCuratorID()
    {
    	return $this->curatorID;
    }
    
	/**
     * @access public
     * @return int
     */
    public function getApprovingCuratorID()
    {
    	return $this->approvingCuratorID;
    }

    /**
     * @access public
     * @return int
     */
    public function getFormatID()
    {
    	return $this->formatID;
    }

    /**
     * @access public
     * @return int
     */
    public function getHomeLocationID()
    {
    	return $this->homeLocationID;
    }
    
    /**
     * @access public
     * @return int
     */
    public function getChargeToID()
    {
    	return $this->chargeToID;
    }
    
    /**
     * @access public
     * @return int
     */
    public function getChargeToName()
    {
    	$chargeto = NULL;
    	if (!is_null($this->getChargeToID()))
    	{
	    	if (is_numeric($this->getChargeToID()) && $this->getChargeToID() > 0)
	    	{
	    		$chargeto = LocationDAO::getLocationDAO()->getLocation($this->getChargeToID())->getLocation();
	    	}
	    	else
	    	{
	    		$chargeto = $this->getChargeToID();
	    	}
    	}
		return $chargeto;
    }
    
	/**
     * @access public
     * @return int
     */
    public function getDepartmentID()
    {
    	return $this->departmentID;
    }


    /**
     * @access public
     * @return mixed
     */
    public function getLocationHistory()
    {
    	return NULL;
    }

    /**
     * @access public
     * @return string
     */
    public function getTitle()
    {
    	return $this->title;
    }

    /**
     * @access public
     * @return int
     */
    public function getGroupID()
    {
    	return $this->groupID;
    }

    /**
     * @access public
     * @return int
     */
    public function getProjectID()
    {
    	return $this->projectID;
    }
    
	/**
     * @access public
     * @return int
     */
    public function nonDigitalImagesExist()
    {
    	return $this->nonDigitalImagesExist;
    }

    /**
     * @access public
     * @return string
     */
    public function getComments()
    {
    	return $this->comments;
    }

    /**
     * @access public
     * @param  int purposeID
     */
    public function setPurposeID($purposeID)
    {
    	$this->purposeID = $purposeID;
    }
    
	/**
     * @access public
     * @param  int curatorID
     */
    public function setCuratorID($curatorID)
    {
    	$this->curatorID = $curatorID;
    }
    
	/**
     * @access public
     * @param  int approvingCuratorID
     */
    public function setApprovingCuratorID($approvingCuratorID)
    {
    	$this->approvingCuratorID = $approvingCuratorID;
    }

    /**
     * @access public
     * @param  int formatID
     */
    public function setFormatID($formatID)
    {
    	$this->formatID = $formatID;
    }

    /**
     * @access public
     * @param  int homeLocationID
     */
    public function setHomeLocationID($homeLocationID)
    {
    	$this->homeLocationID = $homeLocationID;
    }
    
    /**
     * @access public
     * @param  int chargeToID
     */
    public function setChargeToID($chargeToID)
    {
    	$this->chargeToID = $chargeToID;
    }
    

    /**
     * @access public
     * @param  String title
     */
    public function setTitle($title)
    {
    	$this->title = $title;
    }

    /**
     * @access public
     * @param  int departmentID
     */
    public function setDepartmentID($departmentID)
    {
    	$this->departmentID = $departmentID;
    }

    /**
     * @access public
     * @param  int groupID
     */
    public function setGroupID($groupID)
    {
    	$this->groupID = $groupID;
    }

    /**
     * @access public
     * @param  int projectID
     */
    public function setProjectID($projectID)
    {
    	$this->projectID = $projectID;
    }
    
	/**
     * @access public
     * @param  boolean nonDigitalImagesExist
     */
    public function setNonDigitalImagesExist($nonDigitalImagesExist)
    {
    	$this->nonDigitalImagesExist = $nonDigitalImagesExist;
    }

    /**
     * @access public
     * @param  String comments
     */
    public function setComments($comments)
    {
    	$this->comments = $comments;
    }

    /**
     * @access public
     * @param  String comments
     */
    public function appendComments($comments, $updateNamespace = FALSE)
    {
    	if (!empty($this->comments))
    	{
    		$this->comments .= ' ' . $comments;
    	}
    	else
    	{
    		$this->comments = $comments;
    	}
    	if ($updateNamespace)
    	{
    		$this->updateNamespaceRecord();
    	}
    }

    /**
     * @access public
     * @return Functions
     */
    public function getFunctions($updateNamespace = FALSE)
    {
    	if (is_null($this->functions) && !is_null($this->getPrimaryKey()))
    	{
    		$this->functions = new Functions($this->getPrimaryKey());
    		if ($updateNamespace)
    		{
    			$this->updateNamespaceRecord();
    		}
    	}
    	elseif (is_null($this->functions))
    	{
    		$this->functions = new Functions();
    	}
    	return $this->functions;
    }

    /**
     * @access public
     * @param  Functions functions
     */
    public function setFunctions(Functions $functions)
    {
    	$this->functions = $functions;
    }
    

    /**
	 * @return TRUE if the item was manually closed instead of logged out,
	 * FALSE otherwise.
	 */
	public function getManuallyClosed()
	{
		return $this->manuallyClosed;
	}

	/**
	 * @return date that the item was manually closed.
	 */
	public function getManuallyClosedDate()
	{
		return $this->manuallyClosedDate;
	}

	/**
	 * @param boolean $manuallyClosed - TRUE if the item is being manually closed.
	 */
	public function setManuallyClosed($manuallyClosed)
	{
		$this->manuallyClosed = $manuallyClosed;
	}

	/**
	 * @param date $manuallyClosedDate - the date that the item was manually closed
	 */
	public function setManuallyClosedDate($manuallyClosedDate)
	{
		$this->manuallyClosedDate = $manuallyClosedDate;
	}

	/**
     * @access public
     * @param  array files
     */
    public function setFiles($files)
    {
    	$this->files = $files;
    }

    /**
     * Dynamically gets the files if they haven't been set.
     *
     * @access public
     * @return array of AcornFile objects
     */
    public function getFiles($updateNamespace = FALSE, $narrowByName = NULL)
    {
    	if (!is_null($this->getPrimaryKey()))
    	{
    		$linktype = FilesDAO::LINK_TYPE_ITEM;
    		if ($this->recordType == "OSW")
    		{
    			$linktype = FilesDAO::LINK_TYPE_OSW;
    		}
    		$this->files = FilesDAO::getFilesDAO()->getFiles($this->getPrimaryKey(), $linktype, $narrowByName);
    		if ($updateNamespace)
    		{
    			$this->updateNamespaceRecord();
    		}
    		$this->checkForDRSFileReports($linktype, $updateNamespace);
    		$this->files = AcornFile::getSortedFileNames($this->files);	
    	}
    	elseif (is_null($this->files))
    	{
    		$this->files = array();
    	}
    	return $this->files;
    }
    
    /**
     * @access public
     * @param  array callNumbers
     */
    public function setCallNumbers(array $callNumbers)
    {
    	$this->callNumbers = $callNumbers;
    }
    
    /**
     * @access public
     * @param  string callnumber
     */
    public function addCallNumber($callnumber)
    {
    	$callnumbers = $this->getCallNumbers();
    	$callnumbers[$callnumber] = $callnumber;
    	$this->setCallNumbers($callnumbers);
    	$this->updateNamespaceRecord();
    }
    
    /**
     * @access public
     * @param  string callnumber
     */
    public function removeCallNumber($callnumber)
    {
    	$callnumbers = $this->getCallNumbers();
    	unset($callnumbers[$callnumber]);
    	$this->setCallNumbers($callnumbers);
    	$this->updateNamespaceRecord();
    }
    
    /**
     * @access public
     * @return array
     */
    public function getCallNumbers($updateNamespace = FALSE)
    {
    	if (is_null($this->callNumbers) && !is_null($this->getPrimaryKey()))
    	{
    		$this->callNumbers = CombinedRecordsDAO::getCombinedRecordsDAO()->getCallNumbers($this->getPrimaryKey());
    		if ($updateNamespace)
    		{
    			$this->updateNamespaceRecord();
    		}
    	}
    	elseif (is_null($this->callNumbers))
    	{
    		$this->callNumbers = array();
    	}
    	return $this->callNumbers;
    }
    
    /*
     * Returns the array of call numbers that are in this item's call numbers
    * and are not in the array of compare call numbers
    * @param array - call numbers to compare
    * @return array - call numbers not in given array
    */
    public function getCallNumberDifference(array $comparecallnumbers)
    {
    	return array_diff($this->getCallNumbers(), $comparecallnumbers);
    }
    
    /**
     * Returns the call numbers that exist in the database in another item.
     * This list exists only for the call numbers that are attempted to be added
     * during this instance of the record identification.
     *
     * @return array - the list of call numbers
     */
    public function getDuplicateCallNumbers()
    {
    	if (is_null($this->duplicateCallNumbers))
    	{
    		$this->duplicateCallNumbers = array();
    	}
    	return $this->duplicateCallNumbers;
    }
    
    /**
     * Returns the array of duplicate call numbers that are in this item's duplicate call numbers
     * and are not in the array of compare duplicate call numbers
     * @param array - call numbers to compare
     * @return array - call numbers not in given array
     */
    public function getDuplicateCallNumberDifference(array $comparecallnumbers)
    {
    	return array_diff($this->getDuplicateCallNumbers(), $comparecallnumbers);
    }
    
    /**
     * Sets the array of call numbers that exist in the database in another item.
     * This list exists only for the call numbers that are attempted to be added
     * during this instance of the record identification.
     * @param  array callNumbers
     */
    public function setDuplicateCallNumbers(array $callNumbers)
    {
    	$this->duplicateCallNumbers = $callNumbers;
    }
    
    /**
     * Adds a call number that exists in the database in another item.
     * This list exists only for the call numbers that are attempted to be added
     * during this instance of the record identification.
     * @param  string callnumber
     */
    public function addDuplicateCallNumber($callnumber)
    {
    	$callnumbers = $this->getDuplicateCallNumbers();
    	$callnumbers[$callnumber] = $callnumber;
    	$this->setDuplicateCallNumbers($callnumbers);
    	$this->updateNamespaceRecord();
    }
    
    /**
     * Removes a call number that exists in the database in another item.
     * This list exists only for the call numbers that are attempted to be added
     * during this instance of the record identification.
     * @param  string callnumber
     */
    public function removeDuplicateCallNumber($callnumber)
    {
    	$callnumbers = $this->getDuplicateCallNumbers();
    	unset($callnumbers[$callnumber]);
    	$this->setDuplicateCallNumbers($callnumbers);
    	$this->updateNamespaceRecord();
    }
    
    private function checkForDRSFileReports($linktype, $updateNamespace)
    {
    	//First get the batchNames of any pending batch names
   		$pendingBatchNames = array();
   		foreach ($this->files as $file)
   		{
   			if ($file->getDrsStatus() == "Pending")
   			{
   				$pendingBatchNames[$file->getDrsBatchName()] = $file->getDrsBatchName();
   			}
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
   			if ($success && count($reportbatchnames) > 0)
   			{
   				$cleanupAssistant = new DRSStagingDirectoryCleanupAssistant($reportbatchnames);
   				$cleanupAssistant->runService();
   				
   				//Refresh the file data now that it has changed.
   				$this->files = FilesDAO::getFilesDAO()->getFiles($this->getPrimaryKey(), $linktype);
	    		if ($updateNamespace)
	    		{
	    			$this->updateNamespaceRecord();
	    		}
    		
   			}
   		}
    }

    /**
     * @access public
     * @param  AcornFile file
     */
    public function addFile(AcornFile $file)
    {
    	$files = $this->getFiles();
    	$files[$file->getPrimaryKey()] = $file;
    	$this->setFiles($files);
    	$this->updateNamespaceRecord();
    }

    /**
     * @access public
     * @param  Integer fileID
     */
    public function removeFile($fileID)
    {
    	$files = $this->getFiles();
    	unset($files[$fileID]);
    	$this->setFiles($files);
    	$this->updateNamespaceRecord();
    }
    
	/*
     * Returns the array of files that are in this item's files
     * and are not in the array of compare files
     * @param array - files to compare
     * @return array of ids - fileids not in given array
     */
    public function getFileDifference(array $comparefiles)
    {
    	return array_diff_key($this->getFiles(), $comparefiles);
    }
    
	public function isEditable()
    {
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$accesslevel = $identity[PeopleDAO::ACCESS_LEVEL];
    	
    	//Repository can never edit
    	if (($accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY) 
    		//Repository Admin can not edit once an item is logged in.
    		|| ($accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN 
    			&& !is_null($this->getFunctions()->getLogin()->getPrimaryKey())))
    	{
    		return FALSE;
    	}
    	
    	$iseditable = !$this->isBeingEdited();
    	if (!is_null($this->getIsBeingEditedBy()))
    	{
    		$iseditable = $iseditable || $this->getIsBeingEditedBy()->getPrimaryKey() == $identity[PeopleDAO::PERSON_ID];
    	}
		return $iseditable;
    }

	/*
	 * The file information is disabled if the record
	 * is:
	 * 	New and unsaved
	 */
	public function getDisableFilesMessage()
	{
		if (is_null($this->getPrimaryKey()))
		{
			return "This is an unsaved, new record";
		}
		return "";
	}
	
	public function clearAllDuplicates()
	{
		$this->duplicateCallNumbers = NULL;
	}
	
	/*
	 * Returns the reason that the item is in the lab.
	 */
	public abstract function getActivityStatus();
    
	/*
     * Updates the item or osw in the namespace if 
     * this instance is the same as the one in the namespace.
     */
    protected abstract function updateNamespaceRecord();
    
	
} 

?>