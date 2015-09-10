<?php

class Group extends AcornClass
{
    private $records = NULL;
    private $groupName = NULL;
    private $files = NULL;
    private $counts = NULL;
    private $workdoneby = NULL;
    private $workassignedto = NULL;
    private $finalcounts = NULL;
	private $importances = NULL;
	private $isUnlocked = NULL;
	private $proposalBy = NULL;
	
    /*
     * Clears out the records from the namespace.
     */
    public function clearRecords($updateNamespace = FALSE)
    {
    	$this->records = NULL;
    	if ($updateNamespace)
    	{
    		$this->updateNamespaceGroup();	
    	}
    }
    
    /**
     * @access public
     * @return mixed
     */
    public function getRecords($updateNamespace = FALSE)
    {
    	if (is_null($this->records) && !is_null($this->getPrimaryKey()))
    	{
    		$param = new SearchParameters();
			$param->setColumnName('ItemIdentification.' . GroupDAO::GROUP_ID);
			$param->setValue($this->getPrimaryKey());
			$param->setBooleanValue('');
			$this->records = ItemDAO::getItemDAO()->getItems(array($param));
    		if ($updateNamespace)
    		{
    			$this->updateNamespaceGroup();
    		}
    	}
    	elseif (is_null($this->records))
    	{
    		$this->records = array();
    	}
    	return $this->records;
    }
    
	/**
     * @access public
     * @return Record
     */
    public function getRecord($recordID)
    {
    	$records = $this->getRecords();
    	$record = NULL;
    	if (isset($records[$recordID]))
    	{
    		$record = $records[$recordID];
    	}
    	return $record;
    }

    /**
     * @access public
     * @param  Item record
     */
    public function addRecord(Item $record)
    {
    	$records = $this->getRecords();
    	$records[$record->getItemID()] = $record;
    	$this->setRecords($records);
    	$this->updateNamespaceGroup();
    }

    /**
     * @access public
     * @param  Integer itemID
     * @return Item the removed item
     */
    public function removeRecord($itemID)
    {
    	$records = $this->getRecords();
    	$item = $records[$itemID];
    	unset($records[$itemID]);
    	$this->setRecords($records);
    	$this->updateNamespaceGroup();
    	return $item;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getGroupName()
    {
    	return $this->groupName;
    }


    /**
     * @access public
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     * @access public
     * @param  array records
     */
    public function setRecords(array $records)
    {
    	$this->records = $records;
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
    		$this->files = FilesDAO::getFilesDAO()->getFiles($this->getPrimaryKey(), FilesDAO::LINK_TYPE_GROUP, $narrowByName);
    		if ($updateNamespace)
    		{
    			$this->updateNamespaceGroup();
    		}
    		$this->checkForDRSFileReports($updateNamespace);
    		$this->files = AcornFile::getSortedFileNames($this->files);	
    	}
    	elseif (is_null($this->files))
    	{
    		$this->files = array();
    	}
    	return $this->files;
    }
    
	private function checkForDRSFileReports($updateNamespace)
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
   				$this->files = FilesDAO::getFilesDAO()->getFiles($this->getPrimaryKey(), FilesDAO::LINK_TYPE_GROUP);
	    		if ($updateNamespace)
	    		{
	    			$this->updateNamespaceGroup();
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
    	$this->updateNamespaceGroup();
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
    	$this->updateNamespaceGroup();
    }
    
	/*
     * Returns the array of files that are in this group's files
     * and are not in the array of compare files
     * @param array - files to compare
     * @return array of ids - fileids not in given array
     */
    public function getFileDifference(array $comparefiles)
    {
    	return array_diff_key($this->getFiles(), $comparefiles);
    }
    
	public function getAuditTrail($actiontype = NULL)
    {
        return AuditTrailDAO::getAuditTrailDAO()->getAuditTrailItems('Groups', $this->getPrimaryKey(), $actiontype);
    }
    
	private function updateNamespaceGroup()
    {
    	$currentgroup = GroupNamespace::getCurrentGroup();
    	if ($currentgroup->getPrimaryKey() == $this->getPrimaryKey())
    	{
    		GroupNamespace::setCurrentGroup($this);
    	}
    }
 
	/*
     * Clears out the report conservators from the namespace.
     */
    public function clearReportConservators($updateNamespace = FALSE)
    {
    	$this->workdoneby = NULL;
    	if ($updateNamespace)
    	{
    		$this->updateNamespaceGroup();	
    	}
    }
    
    /*
     * Clears out the proposed by from the namespace.
    */
    public function clearProposedBy($updateNamespace = FALSE)
    {
    	$this->proposalBy = NULL;
    	if ($updateNamespace)
    	{
    		$this->updateNamespaceGroup();
    	}
    }
    
	/**
     * @access public
     * @return array
     */
    public function getReportConservators()
    {
    	if (is_null($this->workdoneby))
    	{
    		$this->workdoneby = array();
    	}
    	return $this->workdoneby;
    }
    
	/**
     * @access public
     * @param  array curators
     */
    public function setReportConservators(array $conservators)
    {
    	$this->workdoneby = $conservators;
    }
    
	/**
     * @access public
     * @param  Person curator
     */
    public function addReportConservator(ReportConservator $conservator, $conservatorID)
    {
    	$conservators = $this->getReportConservators();
    	if (array_key_exists($conservatorID, $conservators))
    	{
    		$conservators[$conservatorID] = $conservator;
    	}
    	else
    	{
	    	array_push($conservators, $conservator);
	    	end($conservators);	
	    	$conservatorID = key($conservators);
	    }
    	$this->setReportConservators($conservators);
    	$this->setReportConservators($conservators);
    	$this->updateNamespaceGroup();
    }
    
	/**
     * @access public
     * @param  Int curatorID
     * @param String - The date in YYYY-MM-DD format
     */
    public function removeReportConservator($personID, $date)
    {
    	$conservators = $this->getReportConservators();
    	unset($conservators[$conservatorID]); 
    	
    	$this->setReportConservators($conservators);
    	$this->updateNamespaceGroup();
    }
    
    /**
     * @access public
     * @return array
     */
    public function getProposedBy()
    {
    	if (is_null($this->proposalBy))
    	{
    		$this->proposalBy = array();
    	}
    	return $this->proposalBy;
    }
    
    /**
     * @access public
     * @param  array - list of people writing the proposal
     */
    public function setProposedBy($proposalBy)
    {
    	$this->proposalBy = $proposalBy;
    }
    
    /**
     * @access public
     * @param  Person
     */
    public function addProposedBy(Person $proposalBy)
    {
    	$proposalByList = $this->getProposedBy();
    	$proposalByList[$proposalBy->getPrimaryKey()] = $proposalBy;
    	$this->setProposedBy($proposalByList);
    }
    
    /**
     * @access public
     * @param  Integer personID
     */
    public function removeProposedBy($personID)
    {
    	$proposalByList = $this->getProposedBy();
    	unset($proposalByList[$personID]);
    	$this->setProposedBy($proposalByList);
    }
    
	/*
     * Clears out the work assigned to from the namespace.
     */
    public function clearWorkAssignedTo($updateNamespace = FALSE)
    {
    	$this->workassignedto = NULL;
    	if ($updateNamespace)
    	{
    		$this->updateNamespaceGroup();	
    	}
    }
    
	/**
     * @access public
     * @return array
     */
    public function getWorkAssignedTo()
    {
    	if (is_null($this->workassignedto))
    	{
    		$this->workassignedto = array();
    	}
    	return $this->workassignedto;
    }
    
	/**
     * @access public
     * @param  array workassignedto
     */
    public function setWorkAssignedTo(array $workassignedto)
    {
    	$this->workassignedto = $workassignedto;
    }
    
	/**
     * @access public
     * @param  Person workassignedto
     */
    public function addWorkAssignedTo(Person $workassignedto)
    {
    	$workassignedtolist = $this->getWorkAssignedTo();
    	$workassignedtolist[$workassignedto->getPrimaryKey()] = $workassignedto;
    	$this->setWorkAssignedTo($workassignedtolist);
    	$this->updateNamespaceGroup();
    }
    
	/**
     * @access public
     * @param  Int personID
     */
    public function removeWorkAssignedTo($personID)
    {
    	$workassignedtolist = $this->getWorkAssignedTo();
    	unset($workassignedtolist[$personID]);
    	$this->setWorkAssignedTo($workassignedtolist);
    	$this->updateNamespaceGroup();
    }
    
	/*
     * Clears out the importances from the namespace.
     */
    public function clearImportances($updateNamespace = FALSE)
    {
    	$this->importances = NULL;
    	if ($updateNamespace)
    	{
    		$this->updateNamespaceGroup();	
    	}
    }
    
	/**
     * @access public
     * @return array
     */
    public function getImportances()
    {
    	if (is_null($this->importances))
    	{
    		$this->importances = array();
    	}
    	return $this->importances;
    }
    
	/**
     * @access public
     * @param  array importances
     */
    public function setImportances(array $importances)
    {
    	$this->importances = $importances;
    }
    
	/**
     * @access public
     * @param  Importance importance
     */
    public function addImportance(Importance $importance)
    {
    	$importances = $this->getImportances();
    	$importances[$importance->getPrimaryKey()] = $importance;
    	$this->setImportances($importances);
    	$this->updateNamespaceGroup();
    }
    
	/**
     * @access public
     * @param  Int importanceID
     */
    public function removeImportance($importanceID)
    {
    	$importances = $this->getImportances();
    	unset($importances[$importanceID]);
    	$this->setImportances($importances);
    	$this->updateNamespaceGroup();
    }
    
	/*
     * Clears out the counts from the namespace.
     */
    public function clearCounts($updateNamespace = FALSE)
    {
    	$this->counts = NULL;
    	if ($updateNamespace)
    	{
    		$this->updateNamespaceGroup();	
    	}
    }
    
    
	/**
     * @access public
     * @return Counts
     */
    public function getInitialCounts()
    {
    	if (is_null($this->counts))
    	{
    		$this->counts = new Counts();
    	}
    	return $this->counts;
    }

    /**
     * @access public
     * @param  Counts counts
     */
    public function setInitialCounts(Counts $counts)
    {
    	$this->counts = $counts;
    }
    
	/*
     * Clears out the counts from the namespace.
     */
    public function clearFinalCounts($updateNamespace = FALSE)
    {
    	$this->finalcounts = NULL;
    	if ($updateNamespace)
    	{
    		$this->updateNamespaceGroup();	
    	}
    }
    
	/**
     * @access public
     * @return Counts
     */
    public function getFinalCounts()
    {
    	if (is_null($this->finalcounts))
    	{
    		$this->finalcounts = new Counts();
    	}
    	return $this->finalcounts;
    }

    /**
     * @access public
     * @param  Counts counts
     */
    public function setFinalCounts(Counts $counts)
    {
    	$this->finalcounts = $counts;
    }
    
    public function loggedOutPastThirtyDays()
    {
    	$items = $this->getRecords();
    	foreach ($items as $item)
    	{
	    	if (!is_null($item->getFunctions()->getLogout()->getPrimaryKey()))
	    	{
	    		$logoutdate = $item->getFunctions()->getLogout()->getLogoutDate();
	    			
	    		$currentzenddate = new Zend_Date();
	    		$logoutzenddate = new Zend_Date($logoutdate, ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
	    		 
	    		$comparedate = $logoutzenddate->addDay(30);
	    
	    		//If the current date is more than 30 days past the logout date, then the record can not be edited.
	    		if ($currentzenddate->isLater($comparedate))
	    		{
	    			//If the item has been unlocked, then the thirty day rule does not matter.
	    			if (!$item->isUnlocked())
	    			{
	    				return TRUE;
	    			}
	    		}
	    	}
    	}
    	return FALSE;
    }
    
    public function isUnlocked()
    {
    	if (is_null($this->isUnlocked))
    	{
    		$items = $this->getRecords();
    		$oneunlocked = NULL;
    		foreach ($items as $item)
    		{
    			$unlocked = UnlockedItemsDAO::getUnlockedItemsDAO()->isItemUnlocked($item->getItemID());
    			//If a single item is not unlocked then the whole group will be locked.
    			if (!$unlocked)
    			{
    				$oneunlocked = FALSE;
    				break;
    			}
    		}
    		//If one of the group is not unlocked, then the whole group is locked
    		if ((!is_null($oneunlocked) && !$oneunlocked))
    		{
    			$this->isUnlocked = FALSE;
    		}
    		//Otherwise, all are unlocked
    		else
    		{
    			$this->isUnlocked = TRUE;
    		}
    	}
    	return $this->isUnlocked;
    }
    
    /*
     * The identification information is disabled if any item
    * is:
    * 	Was logged out > 30 days ago.
    */
    public function getDisableIdentificationMessage()
    {
    	if (!is_null($this->getPrimaryKey()))
    	{
    		$disablemessage = array();
    		if ($this->loggedOutPastThirtyDays())
    		{
    			array_push($disablemessage, "One or more items were Logged Out More Than 30 Days Ago");
    		}
    			
    		return implode('; ',$disablemessage);
    	}
    	return "";
    }
    
    /*
     * The login information is disabled if the item
    * is:
    * 	In the login transfer list
    * 	Is temporarily transfere
    * 	Is logged out.
    */
    public function getDisableLoginMessage()
    {
    	if (!is_null($this->getPrimaryKey()))
    	{
    		$items = $this->getRecords();
    		$disablemessage = array();
    		foreach ($items as $item)
    		{
    			if (!is_null($item->getFunctions()->getLogout()->getPrimaryKey()))
    			{
    				array_push($disablemessage, "One or more items are Logged Out");
    				break;
    			}
    		}		
    		$list = TransferNamespace::getTransferList(TransferNamespace::LOGIN);
    		if (isset($list))
    		{
    			foreach ($items as $item)
    			{
    				if (array_key_exists($item->getItemID(), $list))
    				{
    					array_push($disablemessage, "One or more items are in the Login Transfer List");
    					break;
    				}
    			}
    		}
    		return implode('; ',$disablemessage);
    	}
    	return "This is an unsaved, new group";
    }
    
    /*
     * The proposal information is disabled if the one or more items:
    * 	Is new and the identification hasn't been saved
    */
    public function getDisableProposalMessage()
    {
    	if (!is_null($this->getPrimaryKey()))
    	{
    		$disablemessage = array();
    		if ($this->loggedOutPastThirtyDays())
    		{
    			array_push($disablemessage, "One or more items were Logged Out More Than 30 Days Ago");
    		}
    		return implode('; ',$disablemessage);
    	}
    	return "This is an unsaved, new group";
    }
    
    /*
     * The report information is disabled if one or more items:
    * 	Does not have a proposal
    */
    public function getDisableReportMessage()
    {
    	if (!is_null($this->getPrimaryKey()))
    	{
    		$items = $this->getRecords();
    		$disablemessage = array();
    		foreach ($items as $item)
    		{
    			if (is_null($item->getFunctions()->getProposal()->getPrimaryKey()))
    			{
    				array_push($disablemessage, "One or more items do not have a Proposal");
    				break;
    			}
    		}
    		if ($this->loggedOutPastThirtyDays())
    		{
    			array_push($disablemessage, "One or more items were Logged Out More Than 30 Days Ago");
    		}
    		return implode('; ',$disablemessage);
    	}
    	return "This is an unsaved, new group";
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
    		return "This is an unsaved, new group";
    	}
    	return "";
    }
    
    /*
     * The logout information is disabled if the item
    * is:
    * 	In the logout transfer list
    * 	Is temporarily transfered
    * 	Is logged out.
    *  Is not logged in
    */
    public function getDisableLogoutMessage()
    {
    	if (!is_null($this->getPrimaryKey()))
    	{
    		$items = $this->getRecords();
    		$disablemessage = array();
    		foreach ($items as $item)
    		{
    			if (is_null($item->getFunctions()->getLogin()->getPrimaryKey()))
    			{
    				array_push($disablemessage, "One or more items are not Logged In");
    				break;
    			}
    		}		
    		if ($this->loggedOutPastThirtyDays())
    		{
    			array_push($disablemessage, "One or more items were Logged Out More Than 30 Days Ago");
    		}
    		foreach ($items as $item)
    		{
    			if (!is_null($item->getFunctions()->getTemporaryTransfer()->getPrimaryKey()) && is_null($item->getFunctions()->getTemporaryTransferReturn()->getPrimaryKey()))
    			{
    				array_push($disablemessage, "One or more items are Temporarily Transferred");
    				break;
    			}
    		}
    		
    		$list = TransferNamespace::getTransferList(TransferNamespace::LOGOUT);
    		if (isset($list))
    		{
	    		foreach ($items as $item)
	    		{
	    			if (array_key_exists($item->getItemID(), $list))
	    			{
	    				array_push($disablemessage, "One or more items are in the Logout Transfer List");
	    				break;
	    			}
	    		}
    		}
    		return implode('; ',$disablemessage);
    	}
    	return "This is an unsaved, new group";
    }
    
    /*
     * The temp transfer information is disabled if the item
    * is:
    * 	Logged out.
    *  Not logged in
    * 	In the temp transfer list
    */
    public function getDisableTempTransferMessage()
    {
    	if (!is_null($this->getPrimaryKey()))
    	{
    		$items = $this->getRecords();
    		$disablemessage = array();
    		foreach ($items as $item)
    		{
    			if (is_null($item->getFunctions()->getLogin()->getPrimaryKey()))
    			{
    				array_push($disablemessage, "One or more items are not Logged In");
    				break;
    			}
    		}		
    		foreach ($items as $item)
    		{
    			if (!is_null($item->getFunctions()->getLogout()->getPrimaryKey()))
    			{
    				array_push($disablemessage, "One or more items are Logged Out");
    				break;
    			}
    		}		
    		$list = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER);
    		if (isset($list))
    		{
	    		foreach ($items as $item)
	    		{
	    			if (array_key_exists($item->getItemID(), $list))
	    			{
	    				array_push($disablemessage, "One or more items are in the Temp Transfer List");
	    				break;
	    			}
	    		}
    		}
    		return implode('; ',$disablemessage);
    	}
    	return "This is an unsaved, new group";
    }
    
    /*
     * The temp transfer return information is disabled if the item
    * is:
    * 	Logged out
    *  Not logged in
    *  Not temp transferred out
    * 	In the temp transfer return list
    */
    public function getDisableTempTransferReturnMessage()
    {
    	if (!is_null($this->getPrimaryKey()))
    	{
    		$items = $this->getRecords();
    		$disablemessage = array();
    		foreach ($items as $item)
    		{
    			if (is_null($item->getFunctions()->getLogin()->getPrimaryKey()))
    			{
    				array_push($disablemessage, "One or more items are not Logged In");
    				break;
    			}
    		}		
    		foreach ($items as $item)
    		{
    			if (is_null($item->getFunctions()->getLogout()->getPrimaryKey()))
    			{
    				array_push($disablemessage, "One or more items ist Logged Out");
    				break;
    			}
    		}		
    		foreach ($items as $item)
    		{
    			if (is_null($item->getFunctions()->getTemporaryTransfer()->getPrimaryKey()))
    			{
    				array_push($disablemessage, "One or more items is not Temporarily Transferred Out");
    				break;
    			}
    		}
    			
    			
    		$list = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER_RETURN);
    		if (isset($list))
    		{
	    		foreach ($items as $item)
	    		{
	    			if (array_key_exists($item->getItemID(), $list))
	    			{
	    				array_push($disablemessage, "One or more items are in the Temp Transfer Return List");
	    				break;
	    			}
	    		}
    		}
    		return implode('; ',$disablemessage);
    	}
    	return "This is an unsaved, new group";
    }
    
    public function isEditable()
    {
    	$items = $this->getRecords();    	

    	foreach ($items as $item)
    	{
	    	if (!$item->isEditable())
	    	{
	    		return FALSE;
	    	}
    	}
    	return TRUE;
    }
} 


?>