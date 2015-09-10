<?php
/**
 * CombinedRecord
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class CombinedRecord extends Record
{
    private $recordID = NULL;
	private $coordinatorID = NULL;
	private $isNonCollectionMaterial = NULL;
	private $workAssignedTo = NULL;
	private $storage = NULL;
	private $expectedDateOfReturn = NULL;
	private $insuranceValue = NULL;
	private $fundMemo = NULL;
	private $authorArtist = NULL;
	private $dateOfObject = NULL;
	private $callNumbers = NULL;
	private $counts = NULL;
	private $hollisNumber = NULL;
	private $collectionName = NULL;
	private $currentLocation  = NULL;
	private $isUnlocked = NULL;
	
    /**
     * Either the OSW ID or the ItemID
     * @return int
     */
    public function getRecordID()
    {
    	return $this->recordID;
    }
    
	
    /**
     * Sets the OSW ID or the ItemID based on the type.
     * @param  int
     */
    public function setRecordID($recordID)
    {
    	$this->recordID = $recordID;
    }

	/**
     * @access public
     * @return int
     */
    public function getCoordinatorID()
    {
    	return $this->coordinatorID;
    }

    /**
     * @access public
     * @param  int coordinatorID
     */
    public function setCoordinatorID($coordinatorID)
    {
    	$this->coordinatorID = $coordinatorID;
    }
    
    /**
     * @access public
     * @param  boolean isNonCollectionMaterial
     */
    public function setNonCollectionMaterial($isNonCollectionMaterial)
    {
    	$this->isNonCollectionMaterial = $isNonCollectionMaterial;
    }
    
	/**
     * @access public
     * @return boolean
     */
    public function isNonCollectionMaterial()
    {
    	return $this->isNonCollectionMaterial;
    }
    
	/**
     * @access public
     * @return mixed
     */
    public function getWorkAssignedTo()
    {
    	if ($this->getRecordType() == 'Item' && is_null($this->workAssignedTo) && !is_null($this->getRecordID()))
    	{
    		$this->workAssignedTo = ItemDAO::getItemDAO()->getWorkAssignedTo($this->getRecordID());
    	}
    	elseif (is_null($this->workAssignedTo))
    	{
    		$this->workAssignedTo = array();
    	}
    	return $this->workAssignedTo;
    }
    
	/**
     * @access public
     * @param  array workAssignedTo
     */
    public function setWorkAssignedTo($workAssignedTo)
    {
    	$this->workAssignedTo = $workAssignedTo;
    }
    
	/**
     * @access public
     * @param  Person workAssignedTo
     */
    public function addWorkAssignedTo(Person $workAssignedTo)
    {
    	$workAssignedToList = $this->getWorkAssignedTo();
    	$workAssignedToList[$workAssignedTo->getPrimaryKey()] = $workAssignedTo;
    	$this->setWorkAssignedTo($workAssignedToList);
    }

    /**
     * @access public
     * @param  Integer personID
     */
    public function removeWorkAssignedTo($personID)
    {
    	$workAssignedToList = $this->getWorkAssignedTo();
    	unset($workAssignedToList[$personID]);
    	$this->setWorkAssignedTo($workAssignedToList);
    }    

/**
     * @access public
     * @param  string storage
     */
    public function setStorage($storage)
    {
    	$this->storage = $storage;
    }
    
	/**
     * @access public
     * @return string
     */
    public function getStorage()
    {
    	return $this->storage;
    }    

	/**
     * @access public
     * @param  string expectedDateOfReturn
     */
    public function setExpectedDateOfReturn($expectedDateOfReturn)
    {
    	$this->expectedDateOfReturn = $expectedDateOfReturn;
    }
    
	/**
     * @access public
     * @return string
     */
    public function getExpectedDateOfReturn()
    {
    	return $this->expectedDateOfReturn;
    }   

    /**
     * @access public
     * @param  decimal insuranceValue
     */
    public function setInsuranceValue($insuranceValue)
    {
    	$this->insuranceValue = $insuranceValue;
    }
    
	/**
     * @access public
     * @return decimal
     */
    public function getInsuranceValue()
    {
    	return $this->insuranceValue;
    }   
 
    
	/**
     * @access public
     * @param  string fundMemo
     */
    public function setFundMemo($fundMemo)
    {
    	$this->fundMemo = $fundMemo;
    }
    
	/**
     * @access public
     * @return string
     */
    public function getFundMemo()
    {
    	return $this->fundMemo;
    }   
 

	/**
     * @access public
     * @param  string authorArtist
     */
    public function setAuthorArtist($authorArtist)
    {
    	$this->authorArtist = $authorArtist;
    }
    
	/**
     * @access public
     * @return string
     */
    public function getAuthorArtist()
    {
    	return $this->authorArtist;
    } 
    
	/**
     * @access public
     * @param  string date
     */
    public function setDateOfObject($dateOfObject)
    {
    	$this->dateOfObject = $dateOfObject;
    }
    
	/**
     * @access public
     * @return string
     */
    public function getDateOfObject()
    {
    	return $this->dateOfObject;
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
     * @access public
     * @return Counts
     */
    public function getInitialCounts($updateNamespace = FALSE)
    {
    	if ($this->getRecordType() == 'Item' && is_null($this->counts) && !is_null($this->getRecordID()))
    	{
    		$this->counts = ItemDAO::getItemDAO()->getInitialCounts($this->getRecordID());
    		if ($updateNamespace)
    		{
    			$this->updateNamespaceRecord();
    		}
    	}
    	elseif (is_null($this->counts))
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

	/**
     * @access public
     * @param  string hollisNumber
     */
    public function setHOLLISNumber($hollisNumber)
    {
    	$this->hollisNumber = $hollisNumber;
    }
    
	/**
     * @access public
     * @return string
     */
    public function getHOLLISNumber()
    {
    	return $this->hollisNumber;
    }
    
	/**
     * @access public
     * @param  string collectionName
     */
    public function setCollectionName($collectionName)
    {
    	$this->collectionName = $collectionName;
    }
    
	/**
     * @access public
     * @return string
     */
    public function getCollectionName()
    {
    	return $this->collectionName;
    }
    
	protected function updateNamespaceRecord()
    {
    	if ($this->getRecordType() == 'Item')
    	{
	    	$currentitem = RecordNamespace::getCurrentItem();
	    	if ($currentitem->getRecordID() == $this->getRecordID())
	    	{
	    		RecordNamespace::setCurrentItem($this);
	    	}
    	}
    	else 
    	{
    		$currentitem = RecordNamespace::getCurrentOSW();
	    	if ($currentitem->getRecordID() == $this->getRecordID())
	    	{
	    		RecordNamespace::setCurrentOSW($this);
	    	}
    	}
    }
    
	public function getAuditTrail($actiontype = NULL)
    {
    	return AuditTrailDAO::getAuditTrailDAO()->getAuditTrailItems('ItemIdentification', $this->getPrimaryKey(), $actiontype);
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
		if (!is_null($this->getRecordID()))
		{
			$disablemessage = array();
			if (!is_null($this->getFunctions()->getLogout()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item is Logged Out");
			}
			
			$list = TransferNamespace::getTransferList(TransferNamespace::LOGIN);
			if (isset($list))
			{
				if(array_key_exists($this->getRecordID(), $list))
				{
					array_push($disablemessage, "Item is in the Login Transfer List");
				}
			}
			return implode('; ',$disablemessage);
		}
		return "This is an unsaved, new item";
	}
	
	/*
	 * The proposal information is disabled if the item:
	 * 	Is new and the identification hasn't been saved
	 */
	public function getDisableProposalMessage()
	{
		if (is_null($this->getRecordID()))
		{
				return "This is an unsaved, new item";
		}
		return "";
	}
	
	/*
	 * The report information is disabled if the item:
	 * 	Does not have a proposal
	 */
	public function getDisableReportMessage()
	{
		if (!is_null($this->getRecordID()))
		{
			$disablemessage = "";
			if(is_null($this->getFunctions()->getProposal()->getPrimaryKey()))
			{
				$disablemessage = "Item does not have a Proposal";
			}
			return $disablemessage;
		}
		return "This is an unsaved, new item";
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
		if (!is_null($this->getRecordID()))
		{
			$disablemessage = array();
			if (is_null($this->getFunctions()->getLogin()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item is not Logged In");
			}
			/*if (!is_null($this->getFunctions()->getLogout()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item is Logged Out");
			}*/
			if (!is_null($this->getFunctions()->getTemporaryTransfer()->getPrimaryKey()) && is_null($this->getFunctions()->getTemporaryTransferReturn()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item is Temporarily Transferred");
			}
			
			$list = TransferNamespace::getTransferList(TransferNamespace::LOGOUT);
			if (isset($list))
			{
				if (array_key_exists($this->getRecordID(), $list))
				{
					array_push($disablemessage, "Item is in the Logout Transfer List");
				}
			}
			return implode('; ',$disablemessage);
		}
		return "This is an unsaved, new item";
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
		if (!is_null($this->getRecordID()))
		{
			$disablemessage = array();
			if (is_null($this->getFunctions()->getLogin()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item is not Logged In");
			}
			if (!is_null($this->getFunctions()->getLogout()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item is Logged Out");
			}
			
			$list = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER);
			if (isset($list))
			{
				if (array_key_exists($this->getRecordID(), $list))
				{
					array_push($disablemessage, "Item is in the Temp Transfer List");
				}
			}
			return implode('; ',$disablemessage);
		}
		return "This is an unsaved, new item";
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
		if (!is_null($this->getRecordID()))
		{
			$disablemessage = array();
			if (is_null($this->getFunctions()->getLogin()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item is not Logged In");
			}
			if (!is_null($this->getFunctions()->getLogout()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item is Logged Out");
			}
			
			if (is_null($this->getFunctions()->getTemporaryTransfer()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item is not Temporarily Transferred Out");
			}
			
			$list = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER_RETURN);
			if (isset($list))
			{
				if (array_key_exists($this->getRecordID(), $list))
				{
					array_push($disablemessage, "Item is in the Temp Transfer Return List");
				}
			}
			return implode('; ',$disablemessage);
		}
		return "This is an unsaved, new item";
	}
	
	public function getInfoForLabel()
	{
		$info = array();
		$auth = $this->getAuthorArtist();
		if (!empty($auth))
		{
			array_push($info, $auth);
		}
		$title = $this->getTitle();
		if (!empty($title))
		{
			array_push($info, $title);
		}
		$callnums = $this->getCallNumbers();
		if (count($callnums) > 0)
		{
			array_push($info, implode('; ', $callnums));
		}
		return implode(", ", $info);
	}
	
	
    
    public function getItemStatus()
    {
    	$status = NULL;
    	if ($this->getRecordType() == 'Item')
    	{
	    	$status = ItemDAO::ITEM_STATUS_PENDING;
	    	//Logged out
	    	if (!is_null($this->getFunctions()->getLogout()->getPrimaryKey())  || ($this->getManuallyClosed() && !$this->isUnlocked()))
	    	{
	    		$status = ItemDAO::ITEM_STATUS_DONE;
	    	}
	    	//Temp Out
	    	elseif (!is_null($this->getFunctions()->getTemporaryTransfer()->getPrimaryKey()) && is_null($this->getFunctions()->getTemporaryTransferReturn()->getPrimaryKey()))
	    	{
	    		$status = ItemDAO::ITEM_STATUS_TEMP_OUT;
	    	}
	    	//Logged In
	    	elseif (!is_null($this->getFunctions()->getLogin()->getPrimaryKey()))
	    	{
	    		$status = ItemDAO::ITEM_STATUS_LOGGED_IN;
	    	}
    	}
    	return $status;
    }
    
    public function getActivityStatus()
    {
    	if ($this->getRecordType() == "OSW")
    	{
    		return "On-Site";
    	}
    	return $this->getFunctions()->getReport()->getActivity();
    }
    
    /**
     * If the type is an "Item", it determines if it is unlocked.  Since OSW doesn't have
     * that functionality, it returns NULL in all cases for OSW items.
     * @return boolean, NULL if OSW
     */
    public function isUnlocked()
    {
    	if (is_null($this->isUnlocked) && $this->getRecordType() == "Item")
    	{
    		$this->isUnlocked = UnlockedItemsDAO::getUnlockedItemsDAO()->isItemUnlocked($this->getRecordID());
    	}
    	return $this->isUnlocked;
    }
}

?>