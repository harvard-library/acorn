<?php
/**
 * Item
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class Item extends Record
{
    private $itemID = NULL;
	private $coordinatorID = NULL;
	private $isNonCollectionMaterial = NULL;
	private $workAssignedTo = NULL;
	private $storage = NULL;
	private $expectedDateOfReturn = NULL;
	private $insuranceValue = NULL;
	private $fundMemo = NULL;
	private $authorArtist = NULL;
	private $dateOfObject = NULL;
	private $counts = NULL;
	private $collectionName = NULL;
	private $currentLocation  = NULL;
	private $isUnlocked = NULL;
	
	private $duplicateTitle = NULL;
	
	public function __construct($primaryKey = NULL, $isInactive = FALSE, $isBeingEdited = FALSE)
	{
		$this->setRecordType('Item');
		parent::__construct($primaryKey, $isInactive, $isBeingEdited);
	}
	
    /**
     * @access public
     * @return int
     */
    public function getItemID()
    {
    	return $this->itemID;
    }

    /**
     * @access public
     * @param  int
     */
    public function setItemID($itemID)
    {
    	$this->itemID = $itemID;
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
     * @return mixed
     */
    public function getWorkAssignedTo()
    {
    	if (is_null($this->workAssignedTo) && !is_null($this->getItemID()))
    	{
    		$this->workAssignedTo = ItemDAO::getItemDAO()->getWorkAssignedTo($this->getItemID());
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
    
	/*
     * Returns the array of work assigned to that are in this item's work assigned to
     * and are not in the array of compare work assigned to
     * @param array - work assigned to compare
     * @return array - work assigned not in given array
     */
    public function getWorkAssignedToDifference(array $compareworkassignedto)
    {
    	return array_diff($this->getWorkAssignedTo(), $compareworkassignedto);
    }
    
	/**
     * @access public
     * @return Counts
     */
    public function getInitialCounts($updateNamespace = FALSE)
    {
    	if (is_null($this->counts) && !is_null($this->getItemID()))
    	{
    		$this->counts = ItemDAO::getItemDAO()->getInitialCounts($this->getItemID());
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
    	$currentitem = RecordNamespace::getCurrentItem();
    	if ($currentitem->getItemID() == $this->getItemID())
    	{
    		RecordNamespace::setCurrentItem($this);
    	}
    }
    
	public function getAuditTrail($actiontype = NULL)
    {
        return AuditTrailDAO::getAuditTrailDAO()->getAuditTrailItems('ItemIdentification', $this->getPrimaryKey(), $actiontype);
    }

	/*
	 * The identification information is disabled if the item
	 * is:
	 * 	Was logged out > 30 days ago.
	 */
	public function getDisableIdentificationMessage()
	{
		if (!is_null($this->getItemID()))
		{
			$disablemessage = array();
			if ($this->loggedOutPastThirtyDays())
			{
				array_push($disablemessage, "Item was Logged Out More Than 30 Days Ago");
			}
			if (!$this->isUnlocked() && $this->getManuallyClosed())
			{
				
				$zenddate = new Zend_Date($this->getManuallyClosedDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
				$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
				array_push($disablemessage, "Item was Manually Closed on " . $date);
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
		if (!is_null($this->getItemID()))
		{
			$disablemessage = array();
			if (!is_null($this->getFunctions()->getLogout()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item is Logged Out");
			}
			
			$list = TransferNamespace::getTransferList(TransferNamespace::LOGIN);
			if (isset($list))
			{
				if(array_key_exists($this->getItemID(), $list))
				{
					array_push($disablemessage, "Item is in the Login Transfer List");
				}
			}
			if (!$this->isUnlocked() && $this->getManuallyClosed())
			{
				
				$zenddate = new Zend_Date($this->getManuallyClosedDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
				$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
				array_push($disablemessage, "Item was Manually Closed on " . $date);
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
		if (!is_null($this->getItemID()))
		{
			$disablemessage = array();
			if ($this->loggedOutPastThirtyDays())
			{
				array_push($disablemessage, "Item was Logged Out More Than 30 Days Ago");
			}
			if (!$this->isUnlocked() && $this->getManuallyClosed())
			{
				
				$zenddate = new Zend_Date($this->getManuallyClosedDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
				$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
				array_push($disablemessage, "Item was Manually Closed on " . $date);
			}
			return implode('; ',$disablemessage);
		}
		return "This is an unsaved, new item";
	}
	
	/*
	 * The report information is disabled if the item:
	 * 	Does not have a proposal
	 */
	public function getDisableReportMessage()
	{
		if (!is_null($this->getItemID()))
		{
			$disablemessage = array();
			if(is_null($this->getFunctions()->getProposal()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item does not have a Proposal");
			}
			if ($this->loggedOutPastThirtyDays())
			{
				array_push($disablemessage, "Item was Logged Out More Than 30 Days Ago");
			}
			if (!$this->isUnlocked() && $this->getManuallyClosed())
			{
				
				$zenddate = new Zend_Date($this->getManuallyClosedDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
				$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
				array_push($disablemessage, "Item was Manually Closed on " . $date);
			}
			return implode('; ',$disablemessage);
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
		if (!is_null($this->getItemID()))
		{
			$disablemessage = array();
			if (is_null($this->getFunctions()->getLogin()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item is not Logged In");
			}
			if ($this->loggedOutPastThirtyDays())
			{
				array_push($disablemessage, "Item was Logged Out More Than 30 Days Ago");
			}
			if (!is_null($this->getFunctions()->getTemporaryTransfer()->getPrimaryKey()) && is_null($this->getFunctions()->getTemporaryTransferReturn()->getPrimaryKey()))
			{
				array_push($disablemessage, "Item is Temporarily Transferred");
			}
			
			$list = TransferNamespace::getTransferList(TransferNamespace::LOGOUT);
			if (isset($list))
			{
				if (array_key_exists($this->getItemID(), $list))
				{
					array_push($disablemessage, "Item is in the Logout Transfer List");
				}
			}
			if (!$this->isUnlocked() && $this->getManuallyClosed())
			{
				
				$zenddate = new Zend_Date($this->getManuallyClosedDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
				$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
				array_push($disablemessage, "Item was Manually Closed on " . $date);
			}
			return implode('; ',$disablemessage);
		}
		return "This is an unsaved, new item";
	}
	
	public function loggedOutPastThirtyDays()
	{
		$thirtyDays = FALSE;
		if (!is_null($this->getFunctions()->getLogout()->getPrimaryKey()))
		{
			$logoutdate = $this->getFunctions()->getLogout()->getLogoutDate();
			
			$currentzenddate = new Zend_Date();
    		$logoutzenddate = new Zend_Date($logoutdate, ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
    	
    		$comparedate = $logoutzenddate->addDay(30);
    		
    		//If the current date is more than 30 days past the logout date, then the record can not be edited.
    		if ($currentzenddate->isLater($comparedate))
    		{
    			//If the item has been unlocked, then the thirty day rule does not matter.
    			$thirtyDays = TRUE && !$this->isUnlocked();
    		}
		}
		return $thirtyDays;
	}
	
	public function isUnlocked()
	{
		if (is_null($this->isUnlocked))
		{
			$this->isUnlocked = UnlockedItemsDAO::getUnlockedItemsDAO()->isItemUnlocked($this->getItemID());
		}
		return $this->isUnlocked;
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
		if (!is_null($this->getItemID()))
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
				if (array_key_exists($this->getItemID(), $list))
				{
					array_push($disablemessage, "Item is in the Temp Transfer List");
				}
			}
			if (!$this->isUnlocked() && $this->getManuallyClosed())
			{
				
				$zenddate = new Zend_Date($this->getManuallyClosedDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
				$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
				array_push($disablemessage, "Item was Manually Closed on " . $date);
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
		if (!is_null($this->getItemID()))
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
				if (array_key_exists($this->getItemID(), $list))
				{
					array_push($disablemessage, "Item is in the Temp Transfer Return List");
				}
			}
			if (!$this->isUnlocked() && $this->getManuallyClosed())
			{
				
				$zenddate = new Zend_Date($this->getManuallyClosedDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
				$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
				array_push($disablemessage, "Item was Manually Closed on " . $date);
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
	
	/*
     * Determines if the instance passed in is the same as the current instance.
     * 
     * @param Record record
     */
    public function equals(Item $record)
    {
    	//First compare basic fields to determine if they are equal.
    	$equal = $this->getAuthorArtist() == $record->getAuthorArtist();
    	$equal = $equal && $this->getCollectionName() == $record->getCollectionName();
    	$equal = $equal && $this->getComments() == $record->getComments();
    	$equal = $equal && $this->getCoordinatorID() == $record->getCoordinatorID();
    	$equal = $equal && $this->getDateOfObject() == $record->getDateOfObject();
    	$equal = $equal && $this->getDepartmentID() == $record->getDepartmentID();
    	$equal = $equal && $this->getFormatID() == $record->getFormatID();
    	$equal = $equal && $this->getExpectedDateOfReturn() == $record->getExpectedDateOfReturn();
    	$equal = $equal && $this->getFundMemo() == $record->getFundMemo();
    	$equal = $equal && $this->getGroupID() == $record->getGroupID();
    	$equal = $equal && $this->getHomeLocationID() == $record->getHomeLocationID();
    	$equal = $equal && $this->getInsuranceValue() == $record->getInsuranceValue();
    	$equal = $equal && $this->getProjectID() == $record->getProjectID();
    	$equal = $equal && $this->getPurposeID() == $record->getPurposeID();
    	$equal = $equal && $this->getStorage() == $record->getStorage();
    	$equal = $equal && $this->getTitle() == $record->getTitle();
    	$equal = $equal && $this->getWorkAssignedToID() == $record->getWorkAssignedToID();
    	$equal = $equal && $this->getCuratorID() == $record->getCuratorID();
    	
    	//If these fields are equal, determine whether the identification
    	//lists have changed
    	//Call Numbers
    	if ($equal)
    	{
    		$equal = $equal && $this->arraysDifferent($this->getCallNumbers(), $record->getCallNumbers());
    	}
    	//Counts
    	if ($equal)
    	{
    		$counts = $this->getInitialCounts();
    		$equal = $equal && $counts->equals($record->getInitialCounts());
    	}
    	//If the identification lists are equal, move on to the functions.
    	if ($equal)
    	{
    		$functions = $this->getFunctions();
    		$equal = $equal && $functions->equals($record->getFunctions());
    	}
    	return $equal;
    }
    
    public function getItemStatus()
    {
    	$status = ItemDAO::ITEM_STATUS_PENDING;
    	//Logged out
    	if (!is_null($this->getFunctions()->getLogout()->getPrimaryKey()) || ($this->getManuallyClosed() && !$this->isUnlocked()))
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
    	return $status;
    }
    
    public function getActivity()
    {
    	$activity = array();
    	$creationactivity = $this->getCreationActivity();
    	array_push($activity, $creationactivity);
    	$loginactivity = $this->getLoginActivity();
    	if (!empty($loginactivity))
    	{
    		array_push($activity, $loginactivity);
    	}
    	 
    	$examactivity = $this->getExamActivity();
    	if (!empty($examactivity))
    	{
    		array_push($activity, $examactivity);
    	}
    	 
    	$proposalactivity = $this->getProposalActivity();
    	if (!empty($proposalactivity))
    	{
    		array_push($activity, $proposalactivity);
    	}
    	 
    	$reportactivity = $this->getReportActivity();
    	if (!empty($reportactivity))
    	{
    		array_push($activity, $reportactivity);
    	}
    	 
    	$temptransferactivity = $this->getTemporaryTransferActivity();
    	if (!empty($temptransferactivity))
    	{
    		array_push($activity, $temptransferactivity);
    	}
    	 
    	$temptransferreturnactivity = $this->getTemporaryTransferReturnActivity();
    	if (!empty($temptransferreturnactivity))
    	{
    		array_push($activity, $temptransferreturnactivity);
    	}
    	 
    	$logoutactivity = $this->getLogoutActivity();
    	if (!empty($logoutactivity))
    	{
    		array_push($activity, $logoutactivity);
    	}
    	 
    	return $activity;
    }
    
    /*
     * Gets the creation activity.
     * 
     * @return array with two key-value pairs: Date and Activity.
     */
    private function getCreationActivity()
    {
    	$inserts = $this->getAuditTrail(AuditTrailDAO::ACTION_INSERT);
    	$insert = current($inserts);
    	$activity = "";
    	if (!is_null($this->getProjectID()))
    	{
    		$activity = "Record entered into ACORN as part of " . ProjectDAO::getProjectDAO()->getProject($this->getProjectID())->getProjectName();
    	}
    	else 
    	{
    		$activity = "Record entered into ACORN ";
    	}
    	if (!is_null($this->getGroupID()))
    	{
    		if (!is_null($this->getProjectID()))
    		{
    			$activity .= " / ";
    		}
    		$activity .= GroupDAO::getGroupDAO()->getGroup($this->getGroupID())->getGroupName();
    	}
    	$activity .= " by " . $insert->getPerson()->getDisplayName();
    	
    	$date = $insert->getDate();
    	
    	return array('Date' => $date, 'Activity' => $activity, 'Weight' => 1);
    }
    
    /*
     * Gets the login activity.
     * 
     * @return array with two key-value pairs: Date and Activity.
     */
    private function getLoginActivity()
    {
    	$login = $this->getFunctions()->getLogin();
    	$loginactivity = NULL;
    	if (!is_null($login->getPrimaryKey()))
    	{
    		$from = LocationDAO::getLocationDAO()->getLocation($login->getTransferFrom())->getLocation();
    		$to = LocationDAO::getLocationDAO()->getLocation($login->getTransferTo())->getLocation();
    		$by = PeopleDAO::getPeopleDAO()->getPerson($login->getLoginBy())->getDisplayName();
    		$zenddate = new Zend_Date($login->getLoginDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    		$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
			$loginactivity = array(
    			'Date' => $date,
    			'Activity' => "Transferred from " . $from . " to " . $to . " and logged into ACORN by " . $by,
    			'Weight' => 2
    		);
    	}
    	return $loginactivity;
    }
    
    /*
     * Gets the exam activity.
     * 
     * @return array with two key-value pairs: Date and Activity.
     */
    private function getExamActivity()
    {
    	$proposal = $this->getFunctions()->getProposal();
    	$examactivity = NULL;
    	if (!is_null($proposal->getPrimaryKey()) && !is_null($proposal->getExamDate()))
    	{
    		$zenddate = new Zend_Date($proposal->getExamDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    		$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
			$by = $proposal->getProposedByString();
    		$activity = "Examination by " . $by;
    		if (!is_null($proposal->getExamLocation()))
    		{
    			$loc = LocationDAO::getLocationDAO()->getLocation($proposal->getExamLocation())->getLocation();
    			$activity .= " at " . $loc; 
    		}
    		$examactivity = array(
    			'Date' => $date,
    			'Activity' => $activity,
    			'Weight' => 3
    		);
    	}
    	return $examactivity;
    }
    
	/*
     * Gets the proposal activity.
     * 
     * @return array with two key-value pairs: Date and Activity.
     */
    private function getProposalActivity()
    {
    	$proposal = $this->getFunctions()->getProposal();
    	$proposalactivity = NULL;
    	if (!is_null($proposal->getPrimaryKey()))
    	{
    		$zenddate = new Zend_Date($proposal->getProposalDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    		$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
			$by = $proposal->getProposedByString();
    		$minhours = $proposal->getMinHours();
    		$maxhours = $proposal->getMaxHours();
    		$hours = $minhours . ' hour';
    		if ($minhours < $maxhours)
    		{
    			$hours = $minhours . '-' . $maxhours . ' hours';
    		}
    		elseif ($minhours > 1)
    		{
    			$hours .= 's';
    		}
    		$proposalactivity = array(
    			'Date' => $date,
    			'Activity' => "Proposal by " . $by . "; proposed hours: " . $hours,
    			'Weight' => 4
    		);
    	}
    	return $proposalactivity;
    }
    
	/*
     * Gets the report activity.
     * 
     * @return array with two key-value pairs: Date and Activity.
     */
    private function getReportActivity()
    {
    	$report = $this->getFunctions()->getReport();
    	$reportactivity = NULL;
    	
    	if (!is_null($report->getPrimaryKey()))
    	{
    		$zenddate = new Zend_Date($report->getReportDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    		$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
			$by = PeopleDAO::getPeopleDAO()->getPerson($report->getReportBy())->getDisplayName();
    		
    		$treatments = array();
    		if ($report->isAdminOnly())
    		{
    			array_push($treatments, 'Administrative work');
    		}
    		if ($report->isExamOnly())
    		{
    			array_push($treatments, 'Examination');
    		}
    		if ($report->isCustomHousingOnly())
    		{
    			array_push($treatments, 'Housing');
    		}
    		//If it isn't specialized (above three options)
    		//then it is a basic treatment.
    		if (count($treatments) == 0)
    		{
    			$treatment = 'Treatment';
    		}
    		else 
    		{
    			$treatment = implode('+', $treatments);
    		}
    		
    		$activity = '';
    		
    		$conservators = $report->getReportConservators();
    		//If there is one conservator, use that to compare
    		if (count($conservators) == 1)
    		{
    			$conservator = current($conservators);
    			if ($report->getReportBy() == $conservator->getConservator()->getPrimaryKey())
    			{
    				$activity = $treatment . " and report by " . $by . "; completed hours: " . $report->getTotalHours();
    			}
    			else 
    			{
    				$activity = $treatment . " by ". $conservator->getConservator()->getDisplayName() . "; report by " . $by . "; completed hours: " . $report->getTotalHours();
    			}
    		}
    		else 
    		{
    			//Should never happen but just in case
    			if (count($conservators) == 0)
    			{
    				$activity = $treatment . " by unknown person; report by " . $by . "; completed hours: " . $report->getTotalHours();
    			}
    			else 
    			{
    				$conservatorlist = array();
    				foreach($conservators as $conservator)
    				{
    					array_push($conservatorlist, $conservator->getConservator()->getDisplayName());
    				}
    				$conservatorstring = implode(", ", $conservatorlist);
    				$activity = $treatment . " by ". $conservatorstring . "; report by " . $by . "; completed hours: " . $report->getTotalHours();
    			}
    		}
    		
    		$reportactivity = array(
    			'Date' => $date,
    			'Activity' => $activity,
    			'Weight' => 5
    		);
    	}
    	return $reportactivity;
    }
    
    /*
     * Gets the temp transfer activity.
     * 
     * @return array with two key-value pairs: Date and Activity.
     */
    private function getTemporaryTransferActivity()
    {
    	$temptransfer = $this->getFunctions()->getTemporaryTransfer();
    	$temptransferactivity = NULL;
    	if (!is_null($temptransfer->getPrimaryKey()))
    	{
    		$to = LocationDAO::getLocationDAO()->getLocation($temptransfer->getTransferTo())->getLocation();
    		$zenddate = new Zend_Date($temptransfer->getTransferDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    		$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
			$temptransferactivity = array(
    			'Date' => $date,
    			'Activity' => "Temporarily transferred to " . $to,
    			'Weight' => 6
    		);
    	}
    	return $temptransferactivity;
    }
    
	/*
	 * Gets the temp transfer return activity.
     * 
     * @return array with two key-value pairs: Date and Activity.
     */
    private function getTemporaryTransferReturnActivity()
    {
    	$temptransfer = $this->getFunctions()->getTemporaryTransferReturn();
    	$temptransferactivity = NULL;
    	if (!is_null($temptransfer->getPrimaryKey()))
    	{
    		$to = LocationDAO::getLocationDAO()->getLocation($temptransfer->getTransferTo())->getLocation();
    		$zenddate = new Zend_Date($temptransfer->getTransferDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    		$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
			$temptransferactivity = array(
    			'Date' => $date,
    			'Activity' => "Returned to " . $to,
    			'Weight' => 7
    		);
    	}
    	return $temptransferactivity;
    }
    
	/*
	 * Gets the logout activity.
     * 
     * @return array with two key-value pairs: Date and Activity.
     */
    private function getLogoutActivity()
    {
    	$logout = $this->getFunctions()->getLogout();
    	$logoutactivity = NULL;
    	if (!is_null($logout->getPrimaryKey()))
    	{
    		$to = LocationDAO::getLocationDAO()->getLocation($logout->getTransferTo())->getLocation();
    		$zenddate = new Zend_Date($logout->getLogoutDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    		$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
			$logoutactivity = array(
    			'Date' => $date,
    			'Activity' => "Logged out and transferred to " . $to,
    			'Weight' => 8
    		);
    	}
    	return $logoutactivity;
    }
    
    public function getActivityStatus()
    {
    	return $this->getFunctions()->getReport()->getActivity();
    }
    
        
    /**
     * Returns the title that exists in the database in another item.
     * This exists only for the title that is attempting to be added
     * during this instance of the record identification.
     *
     * @return string
     */
    public function getDuplicateTitle()
    {
    	return $this->duplicateTitle;
    }
    
    /**
     * Sets the title that exists in the database in another item.
     * This exists only for the title that is attempting to be added
     * during this instance of the record identification.
      
     * @param  string title
     */
    public function setDuplicateTitle($title)
    {
    	$this->duplicateTitle = $title;
    	$this->updateNamespaceRecord();
    }
    
    public function clearAllDuplicates()
    {
    	parent::clearAllDuplicates();
    	$this->duplicateTitle = NULL;
    }
    
    /**
     * Set whether the item is unlocked.
     * @param boolean $isUnlocked
     */
    public function setUnlocked($isUnlocked)
    {
    	$this->isUnlocked = $isUnlocked;
    }
    
    /**
     * Send curator and approving curator email when an item is logged in, logged out or a temporary transfer is done
     * @param type $itemID
     * @param type $transferAction
     * @return type
     */
    public function emailCurator($itemID, $transferAction)
    {

        //Calls the People DAO and uses the Item’s curator and approving curator IDs.
        $people = PeopleDAO::getPeopleDAO();
        $approvingCuratorID = $people->getPerson($this->getApprovingCuratorID());
        $curatorID = $people->getPerson($this->getCuratorID());
        
        $greeting = 'Dear ';
        $mailTo   = '';
        
        // Curator and approving curator might or might not be two different people
        if (!is_null($curatorID))
        {
            $greeting .= $curatorID->getDisplayName();
            $mailTo   .= $curatorID->getEmailAddress();
        }     
        if (!is_null($approvingCuratorID))
        {
            if (!is_null($curatorID))
            {
                if ($approvingCuratorID != $curatorID)
                {
                    $greeting .= ' and ';
                    $greeting .= $approvingCuratorID->getDisplayName();
                    $mailTo   .= ', ';
                    $mailTo   .= $approvingCuratorID->getEmailAddress();               
                }
            }
            else {
                $greeting .= $approvingCuratorID->getDisplayName();
                $mailTo   .= $approvingCuratorID->getEmailAddress();               
            }
        }

        $greeting .= ",\n";
       
        $itemTitle = $this->getTitle();

        $callNumbers = '';
        
        foreach ($this->getCallNumbers() as $callNumber)
        {
            $callNumbers .= "$callNumber,";
        }

        $function = $this->getFunctions();
               
        $message = "$greeting\nACORN Record $itemID, Call $callNumbers $itemTitle, was ";
        
        // Transfer type object is dependent on transfer action performed
        if ($transferAction === 'login' or $transferAction === 'logout')
        {
            if ($transferAction === 'login')
            {
                $loggedByID = $function->getLogin()->getLoginBy();
                $transferType = $function->getLogin();
                $subject = "ACORN Record $itemID was logged in";
                $message .= "logged in and transferred ";
                $transferDate = $transferType->getLoginDate();
            }
            elseif ($transferAction === 'logout')
            {
                $loggedByID = $function->getLogout()->getLogoutBy();
                $transferType = $function->getLogout();
                $subject = "ACORN Record $itemID was logged out";
                $message .= "logged out and transferred ";
                $transferDate = $transferType->getLogoutDate();
            }

            $loggedByPerson = $people->getPerson($loggedByID)->getDisplayName();
            $mailFrom       = $people->getPerson($loggedByID)->getEmailAddress();
        }
        elseif ($transferAction === 'transferOut' or $transferAction === 'transferReturn')
        {
            
            //This returns an array of data about the person logged in
            $identity = Zend_Auth::getInstance()->getIdentity();
            //Use the PeopleDAO to get the Person object
            $loggedByID = $people->getPerson($identity[PeopleDAO::PERSON_ID]);
            //Get the display name and email address from the Person object.
            $loggedByPerson = $loggedByID->getDisplayName();         
            $mailFrom       = $loggedByID->getEmailAddress();         

            $transferType = $function->getTemporaryTransfer();
            $transferDate = $transferType->getTransferDate();
 
            if ($transferAction === 'transferOut')
            {
                $subject = "ACORN Record $itemID was transferred out";
                $message .= "transferred ";
            }
            elseif ($transferAction === 'transferReturn')
            {
                $subject = "ACORN Record $itemID was returned";
                $message .= "returned ";
            }
        }
        
        $locationDAO = LocationDAO::getLocationDAO();
        
        $transferredFromID = $transferType->getTransferFrom();
	$transferredFrom = $locationDAO->getLocation($transferredFromID)->getLocation();
        
        $transferredToID = $transferType->getTransferTo();
	$transferredTo = LocationDAO::getLocationDAO()->getLocation($transferredToID)->getLocation();

        $message .= "from $transferredFrom to $transferredTo on $transferDate.\n\n";
        $message .= "Sincerely,\n\n$loggedByPerson\n";
        
	$headers = 'From: '     . $mailFrom . "\n" .
		   'Reply-To: ' . $mailFrom . "\n";

        $sent = mail($mailTo, $subject, $message, $headers);
	return $sent;
    }
 
}

?>