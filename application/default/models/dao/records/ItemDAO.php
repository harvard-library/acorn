<?php
/**
 * ItemDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class ItemDAO extends Zend_Db_Table
{
	const ITEM_ID = 'ItemID';
	const IDENTIFICATION_ID = 'IdentificationID';
	const COORDINATOR_ID = 'CoordinatorID';
	const CURATOR_ID = 'CuratorID';
	const APPROVING_CURATOR_ID = 'ApprovingCuratorID';
	const WORK_ASSIGNED_TO_ID = 'WorkAssignedToID';
	const EXPECTED_DATE_OF_RETURN = 'ExpectedDateOfReturn';
	const INSURANCE_VALUE = 'InsuranceValue';
	const FUND_MEMO = 'FundMemo';
	const AUTHOR_ARTIST = 'AuthorArtist';
	const DATE_OF_OBJECT = 'DateOfObject';
	const HOLLIS_NUMBER = 'HOLLISNumber';
	const COLLECTION_NAME = 'CollectionName';
	const STORAGE = 'Storage';
	const COMMENTS = 'Comments';
	const HOME_LOCATION_ID = 'HomeLocationID';
	const TITLE = 'Title';
	const INACTIVE = 'Inactive';
	const NON_DIGITAL_IMAGES_EXIST = 'NonDigitalImagesExist';
	const IS_NON_COLLECTION_MATERIAL = 'IsNonCollectionMaterial';
	const IS_BEING_EDITED = 'IsBeingEdited';
	const EDITED_BY_ID = 'EditedByID';
	const MANUALLY_CLOSED = 'ManuallyClosed';
	const MANUALLY_CLOSED_DATE = 'ManuallyClosedDate';
	const CHARGE_TO_ID = 'ChargeToID';
	
	//Counts
	const TOTAL_COUNT = 'TotalCount';
	const COUNT_DESCRIPTION = 'Description';
	const COUNT_TYPE = 'CountType';
	
	const ITEM_STATUS_PENDING = 'Pending';
	const ITEM_STATUS_LOGGED_IN = 'Logged In';
	const ITEM_STATUS_TEMP_OUT = 'Temp Out';
	const ITEM_STATUS_DONE = 'Done';
	const ITEM_STATUS_MANUALLY_MARKED_AS_DONE = 'Manually Marked as Done';
	
	/* The name of the table */
	protected $_name = "Items";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "ItemID";

    private static $itemDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return ItemDAO
     */
    public static function getItemDAO()
	{
		if (!isset(self::$itemDAO))
		{
			self::$itemDAO = new ItemDAO();
		}
		return self::$itemDAO;
	}
	
	public function buildBaseSelect()
	{
		$select = $this->select();
		$select->setIntegrityCheck(FALSE);
		$select->from($this, array('*'));
		$select->joinInner('ItemIdentification', 
		    		'Items.IdentificationID = ItemIdentification.IdentificationID', 
		    		array('CuratorID', 'ApprovingCuratorID', 'PurposeID', 'HomeLocationID', 'Title', 'DepartmentID', 'GroupID', 'ProjectID',
		    			'Comments', 'Inactive', 'NonDigitalImagesExist', 'IsBeingEdited', 'EditedByID', 'ManuallyClosed', 'ManuallyClosedDate', 'ChargeToID'));
		return $select;
	}
	
	
    /**
     * Returns the item with the given item id.
     *
     * @access public
     * @param  Integer itemID
     * @return Item
     */
    public function getItem($itemID)
    {
    	$item = NULL;
	    if (!empty($itemID))
    	{
	    	$itemID = $this->getAdapter()->quote($itemID, 'INTEGER');
	    	$select = $this->buildBaseSelect();
	    	$select->where('ItemID=' . $itemID);
	    	$row = $this->fetchRow($select);
	    	if (!is_null($row))
	    	{
	    		$item = $this->buildItem($row->toArray());
	    	}
    	}
    	return $item;
    }
    
    /*
     * @access private
     * @param  array values from the database
     * @return Item
     */
    private function buildItem(array $values)
    {
    	$item = new Item($values[self::IDENTIFICATION_ID], $values[self::INACTIVE], $values[self::IS_BEING_EDITED]);
    	$item->setAuthorArtist($values[self::AUTHOR_ARTIST]);
    	$item->setComments($values[self::COMMENTS]);
    	$item->setCoordinatorID($values[self::COORDINATOR_ID]);
    	$item->setCuratorID($values[self::CURATOR_ID]);
    	$item->setApprovingCuratorID($values[self::APPROVING_CURATOR_ID]);
    	$item->setDateOfObject($values[self::DATE_OF_OBJECT]);
    	$item->setDepartmentID($values[DepartmentDAO::DEPARTMENT_ID]);
    	$item->setFormatID($values[FormatDAO::FORMAT_ID]);
    	$item->setExpectedDateOfReturn($values[self::EXPECTED_DATE_OF_RETURN]);
    	$item->setFundMemo($values[self::FUND_MEMO]);
    	$item->setGroupID($values[GroupDAO::GROUP_ID]);
    	$item->setHomeLocationID($values[self::HOME_LOCATION_ID]);
    	$item->setChargeToID($values[self::CHARGE_TO_ID]);
    	$item->setInsuranceValue($values[self::INSURANCE_VALUE]);
    	$item->setItemID($values[self::ITEM_ID]);
    	$item->setNonCollectionMaterial($values[self::IS_NON_COLLECTION_MATERIAL]);
    	$item->setProjectID($values[ProjectDAO::PROJECT_ID]);
    	$item->setPurposeID($values[PurposeDAO::PURPOSE_ID]);
    	$item->setStorage($values[self::STORAGE]);
    	$item->setTitle($values[self::TITLE]);
    	$item->setHOLLISNumber($values[self::HOLLIS_NUMBER]);
    	$item->setCollectionName($values[self::COLLECTION_NAME]);
    	$item->setIsBeingEditedByID($values[self::EDITED_BY_ID]);
    	$item->setManuallyClosed($values[self::MANUALLY_CLOSED]);
    	$item->setManuallyClosedDate($values[self::MANUALLY_CLOSED_DATE]);
    	return $item;
    }
    
	

    /**
     * Returns the items based on the search criteria
     *
     * @access public
     * @param  array searchparameters (SearchParameter objects)
     * @return JSON ready array
     */
    public function getItems(array $searchparameters, $startIndex = 0, $count = 50, $additionalwhere = NULL, $order = 'ItemID')
    {
    	$retval = array();
	    if (!empty($searchparameters) || !empty($additionalwhere))
    	{
	        $select = $this->buildBaseSelect();
		    $select = $this->appendSearchClause($select, $searchparameters);
		    if (is_null($select))
		    {
		    	return $retval;
		    }
		    if (!empty($additionalwhere))
		    {
		    	$select->where($additionalwhere);
		    }
		    
		    $select = $this->populateOrderBy($select, $order);
		    $select->limit($count, $startIndex);
		     Logger::log($select->__toString(), Zend_Log::DEBUG);
		   
		   	$searchresults = $this->fetchAll($select);
		    if (!is_null($searchresults))
		    {
		    	$rowarray = $searchresults->toArray();
		    	foreach ($rowarray as $row)
		    	{
		    		$item = $this->buildItem($row);
		    		$retval[$item->getItemID()] = $item;
		    	}
		    }
		}
	    return $retval;
    }
    
    private function populateOrderBy($select, $order)
    {
    	$updatedorder = array();
	    $coordinatororder = FALSE;
	    $curatororder = FALSE;
	    $departmentsorder = FALSE;
	    $tuborder = FALSE;
	    $projectorder = FALSE;
	    $repositoryorder = FALSE;
	    $callorder = FALSE;
	    if (is_array($order))
	    {
	    	foreach ($order as $o)
	    	{
	    		$orderarray = explode(' ', $o);
	    		$o = $orderarray[0];
	    		$sort = 'asc';
	    		if (isset($orderarray[1]))
	    		{
	    			$sort = $orderarray[1];
	    		}
	    		if ($o == 'Coordinator')
	    		{
	    			$coordinatororder = TRUE;
	    			array_push($updatedorder, 'Coordinator.DisplayName' . ' ' . $sort);
	    		}
	    		elseif ($o == 'Curator')
	    		{
	    			$curatororder = TRUE;
	    			array_push($updatedorder, 'Curator.DisplayName' . ' ' . $sort);
	    		}
    			elseif ($o == 'DepartmentName')
	    		{
	    			$departmentsorder = TRUE;
	    			array_push($updatedorder, 'LocationsOrder.Location' . ' ' . $sort);
    				array_push($updatedorder, 'DepartmentsOrder.DepartmentName' . ' ' . $sort);
	    		}
	    		elseif ($o == 'ProjectName')
	    		{
	    			$projectorder = TRUE;
	    			array_push($updatedorder, 'ProjectsOrder.ProjectName' . ' ' . $sort);
    			}
	    		elseif ($o == 'Repository')
	    		{
	    			$repositoryorder = TRUE;
	    			array_push($updatedorder, 'LocationsOrder.Location' . ' ' . $sort);
    			}
	    		elseif ($o == 'CallNumber')
	    		{
	    			$callorder = TRUE;
	    			array_push($updatedorder, 'CallOrder.CallNumber' . ' ' . $sort);
	    		}
	    		elseif ($o == 'TUB')
	    		{
	    			$tuborder = TRUE;
	    			array_push($updatedorder, 'LocationsOrder.TUB' . ' ' . $sort);
    			}
	    		else 
	    		{
	    			array_push($updatedorder, $this->determineOrder($o) . ' ' . $sort);
	    		}
	    	}
	    }
	    else 
	    {
	    	$orderarray = explode(' ', $order);
	    	$order = $orderarray[0];
	    	$sort = 'asc';
	    	if (isset($orderarray[1]))
	    	{
	    		$sort = $orderarray[1];
	    	}
	    	
	    	if ($order == 'Coordinator')
    		{
    			$coordinatororder = TRUE;
    			array_push($updatedorder, 'Coordinator.DisplayName' . ' ' . $sort);
    		}
    		elseif ($order == 'Curator')
    		{
    			$curatororder = TRUE;
    			array_push($updatedorder, 'Curator.DisplayName' . ' ' . $sort);
    		}
    		elseif ($order == 'DepartmentName')
    		{
    			$departmentsorder = TRUE;
    			array_push($updatedorder, 'LocationsOrder.Location' . ' ' . $sort);
    			array_push($updatedorder, 'DepartmentsOrder.DepartmentName' . ' ' . $sort);
    		}
    		elseif ($order == 'ProjectName')
	    	{
	    		$projectorder = TRUE;
	    		array_push($updatedorder, 'ProjectsOrder.ProjectName' . ' ' . $sort);
    		}
	    	elseif ($order == 'Repository')
	    	{
	    		$repositoryorder = TRUE;
	    		array_push($updatedorder, 'LocationsOrder.Location' . ' ' . $sort);
    		}
	    	elseif ($order == 'CallNumber')
	    	{
	    		$callorder = TRUE;
	    		array_push($updatedorder, 'CallOrder.CallNumber' . ' ' . $sort);
	    	}
	    	elseif ($order == 'TUB')
    		{
    			$tuborder = TRUE;
    			array_push($updatedorder, 'LocationsOrder.TUB' . ' ' . $sort);
    		}
    		else 
    		{
    			array_push($updatedorder, $this->determineOrder($order) . ' ' . $sort);
    		}
	    }
	    
	    if ($coordinatororder)
	    {
	    	$select->joinInner(array('Coordinator' => 'People'),
	    		'Coordinator.PersonID = Items.CoordinatorID', array());
	    }
	    if ($curatororder)
	    {
	    	$select->joinInner(array('Curator' => 'People'),
	    		'Curator.PersonID = ItemIdentification.CuratorID', array());
	    }
	    if ($departmentsorder)
	    {
	    	$select->joinLeft(array('DepartmentsOrder' => 'Departments'),
	    		'ItemIdentification.DepartmentID = DepartmentsOrder.DepartmentID', array());
	    	$select->joinInner(array('LocationsOrder' => 'Locations'),
	    		'ItemIdentification.HomeLocationID = LocationsOrder.LocationID', array());
	    }
	    if ($projectorder)
	    {
	    	$select->joinLeft(array('ProjectsOrder' => 'Projects'),
	    		'ItemIdentification.ProjectID = ProjectsOrder.ProjectID', array());
	    }
	    if ($tuborder || $repositoryorder)
	    {
	    	if (!$departmentsorder)
	    	{
	    		$select->joinInner(array('LocationsOrder' => 'Locations'),
	    		'ItemIdentification.HomeLocationID = LocationsOrder.LocationID', array());
	    	}
	    }
	    if ($callorder)
	    {
	    	$select->joinLeft(array('CallOrder' => 'CallNumbers'),
	    		'ItemIdentification.IdentificationID = CallOrder.IdentificationID', array());
	    }
	    $select->order($updatedorder);
	    return $select;
    }
    
    private function determineOrder($order)
    {
    	if ($order == 'RecordID')
    	{
    		$order = 'ItemID';
    	}
    	return $order;
    }
    
	/**
     * Returns the items based on the search criteria
     *
     * @access public
     * @param  array searchparameters (SearchParameter objects)
     * @return JSON ready array
     */
    public function getItemCount(array $searchparameters, $additionalwhere = NULL)
    {
    	if (!empty($searchparameters) || !empty($additionalwhere))
    	{
	        $select = $this->select();
			$select->setIntegrityCheck(FALSE);
			$select->from($this, array('Count' => 'COUNT(*)'));
			$select->joinInner('ItemIdentification', 
		    		'Items.IdentificationID = ItemIdentification.IdentificationID', 
		    		array());
		
		    $select = $this->appendSearchClause($select, $searchparameters, TRUE);
		    if (is_null($select))
		    {
		    	return 0;
		    }
		    
		    if (!empty($additionalwhere))
		    {
		    	$select->where($additionalwhere);
		    }
		    Logger::log($select->__toString());
		    $searchresult = $this->fetchRow($select);
		    return $searchresult->Count;
    	}
    	return 0;
    }
    
    /*
     * TODO: Have to include functionality for other search types
     * and boolean comparisons.
     */
    private function appendSearchClause($select, array $searchparameters, $countonly = FALSE)
    {
    	$loginexists = FALSE;
    	$proposalexists = FALSE;
    	$reportexists = FALSE;
    	$logoutexists = FALSE;
    	$callnumbersexist = FALSE;
    	$importanceexists = FALSE;
    	$temptransferexists = FALSE;
    	$conservatorsexists = FALSE;
    	$workassignedexists = FALSE;
    	$proposedbyexists = FALSE;
    	 
    	$loginappended = FALSE;
    	$proposalappended = FALSE;
    	$reportappended = FALSE;
    	$logoutappended = FALSE;
    	$callnumbersappended = FALSE;
    	$importanceappended = FALSE;
    	$temptransferappended = FALSE;
    	$conservatorsappended = FALSE;
    	$workassignedappended = FALSE;
    	$proposedbyappended = FALSE;
    	 
    	$where = "";
    	$delimiter = "";
    	$countopen = 0;
    	$isfirst = TRUE;
    	foreach ($searchparameters as $param)
    	{
    		$value = $param->getValue();
    		$column = $param->getColumnName();
    		//Get only the column name
    		if (strpos($column, ".") > 0)
    		{
	    		$columncompare = substr($column, strpos($column, ".") + 1);
	    		$tablename = substr($column, 0, strpos($column, "."));
    		}
    		else 
    		{
    			$columncompare = $column;
    			$tablename = NULL;
    		}
    		if ($column == 'RecordID')
    		{
    			$column = ItemDAO::ITEM_ID;
    		}
    		
    		$open = $param->getOpenParenthesis();
    		//Increment the number of open parens when one is opened.
    		if (!empty($open))
    		{
    			$countopen++;
    		}
    		
    		if (!$isfirst)
    		{
    			$delimiter = $param->getBooleanValue();
	    		if (!empty($delimiter))
	    		{
	    			$delimiter = ' ' . $delimiter . ' ';
	    		}
    		}
    		$isfirst = FALSE;
    		
    		$closed = $param->getClosedParenthesis();
    		//Decrement the number of open parens when one is closed.
    		if (!empty($closed))
    		{
    			$countopen--;
    		}
    		
    		//If the tablename is the work type, it is an osw search.
    		if ($tablename == 'OSWWorkTypes' || $columncompare == 'OSWStatus' || ($columncompare == 'Activity' && $value == 'On-site'))
    		{
    			return NULL;
    		}
    		
    		
    		//If the column is the item status, then
    		if ($column == 'ItemStatus')
    		{
    			$statusclause = "";
    			switch ($value)
    			{
    				//Not logged in
    				case self::ITEM_STATUS_PENDING:
    					$statusclause = "Items.IdentificationID NOT IN (SELECT DISTINCT IdentificationID FROM ItemLogin)";
    					break;
    				//Logged in but not logged out
    				case self::ITEM_STATUS_LOGGED_IN:
    					//Join inner on temp transfers and set the where clause 
    					$select->joinLeft(array('Transfers' => 'TemporaryTransfers'), 
					    	'Items.ItemID = Transfers.ItemID AND Transfers.TransferType = \'Transfer\'', 
					    	array());
    					//Join inner on temp transfer return and set the where clause 
    					$select->joinLeft(array('Returns' => 'TemporaryTransfers'), 
					    	'Items.ItemID = Returns.ItemID AND Returns.TransferType = \'Return\' AND Transfers.ToLocationID = Returns.FromLocationID AND Transfers.TransferDate <= Returns.TransferDate', 
					    	array());
    					$statusclause = "(Items.IdentificationID IN (SELECT DISTINCT IdentificationID FROM ItemLogin)";
    					$statusclause .= " AND Items.IdentificationID NOT IN (SELECT DISTINCT IdentificationID FROM ItemLogout)";
    					$statusclause .= " AND (Transfers.TemporaryTransferID IS NULL OR (Transfers.TemporaryTransferID = (SELECT MAX(TemporaryTransferID) FROM TemporaryTransfers WHERE ItemID = Items.ItemID AND TransferType = 'Transfer')";
    					$statusclause .= " AND Returns.TemporaryTransferID IS NOT NULL)))";
    					break;
    				//Temp out
    				case self::ITEM_STATUS_TEMP_OUT:
    					//Join inner on temp transfers and set the where clause 
    					$select->joinInner(array('Transfers' => 'TemporaryTransfers'), 
					    	'Items.ItemID = Transfers.ItemID AND Transfers.TransferType = \'Transfer\'', 
					    	array());
    					//Join inner on temp transfer return and set the where clause 
    					$select->joinLeft(array('Returns' => 'TemporaryTransfers'), 
					    	'Items.ItemID = Returns.ItemID AND Returns.TransferType = \'Return\' AND Transfers.ToLocationID = Returns.FromLocationID AND Transfers.TransferDate <= Returns.TransferDate', 
					    	array());
    					$statusclause = "(Items.IdentificationID NOT IN (SELECT DISTINCT IdentificationID FROM ItemLogout)";
    					$statusclause .= " AND Transfers.TemporaryTransferID = (SELECT MAX(TemporaryTransferID) FROM TemporaryTransfers WHERE ItemID = Items.ItemID AND TransferType = 'Transfer')";
    					$statusclause .= " AND Returns.TemporaryTransferID IS NULL)";
    					break;
    				//Logged Out
    				case self::ITEM_STATUS_DONE:
    					$statusclause = "Items.IdentificationID IN (SELECT DISTINCT IdentificationID FROM ItemLogout)";
    					break;	
    			}
    			if (!empty($statusclause))
    			{
	    			$delimiter = ' ' . $delimiter . ' ';
		    		$where .= $delimiter . $open . $statusclause . $closed;
    			}
    		
    		}
    		elseif ($column == 'Activity')
    		{
    			$activityclause = "";
    			$reportexists = TRUE;
    			switch ($value)
    			{
    				//Not logged in
    				case 'Treatment':
    					$activityclause = "(ItemReport.AdminOnly = 0 AND ItemReport.ExamOnly = 0 AND ItemReport.CustomHousingOnly = 0)";
    					break;
    				//Logged in but not logged out
    				case 'Admin Only':
    					$activityclause = "ItemReport.AdminOnly = 1";
    					break;
    				//Temp out
    				case 'Custom Housing Only':
    					$activityclause = "ItemReport.CustomHousingOnly = 1";
    					break;
    				//Logged Out
    				case 'Exam Only':
    					$activityclause = "ItemReport.ExamOnly = 1";
    					break;	
    			}
    			if (!empty($activityclause))
    			{
	    			$delimiter = ' ' . $delimiter . ' ';
		    		$where .= $delimiter . $open . $activityclause . $closed;
    			}
    		}
    		else
    		{
	    		$datatype = $this->getDataType($columncompare, $tablename);
	    		if ($tablename == 'ItemLogin')
	    		{
	    			$loginexists = TRUE;
	    		}
	    		elseif ($tablename == 'ItemProposal')
	    		{
	    			$proposalexists = TRUE;
	    		}
	    		elseif ($tablename == 'ItemProposal')
	    		{
	    			$proposalexists = TRUE;
	    		}
	    		elseif ($tablename == 'ItemReport')
	    		{
	    			$reportexists = TRUE;
	    		}
	    		elseif ($tablename == 'ItemLogout')
	    		{
	    			$logoutexists = TRUE;
	    		}
	    		elseif ($tablename == 'CallNumbers')
	    		{
	    			$callnumbersexist = TRUE;
	    		}
	    		elseif ($tablename == 'ItemImportances')
	    		{
	    			$importanceexists = TRUE;
	    		}	
	    		elseif ($tablename == 'TemporaryTransfers')
	    		{
	    			$temptransferexists = TRUE;
	    		}	
	    		elseif ($tablename == 'ItemConservators')
	    		{
	    			$reportexists = TRUE;
	    			$conservatorsexists = TRUE;
	    		}	
    			elseif ($tablename == 'ProposedBy')
	    		{
	    			$proposalexists = TRUE;
	    			$proposedbyexists = TRUE;
	    		}	
	    		elseif ($tablename == 'WorkAssignedTo')
	    		{
	    			$workassignedexists = TRUE;
	    		}	
	    		$searchtype = $param->getSearchType();							

	    		if ($searchtype == SearchParameters::SEARCH_TYPE_BEGINS_WITH)
	    		{
	    			if ($datatype == 'int' || $datatype == 'decimal')
	    			{
	    				$searchtype = SearchParameters::SEARCH_TYPE_GREATER_THAN;
	    			}
	    			else 
	    			{
	    				$searchtype = "LIKE";
	    				$value = $value . '%';	
	    			}
	    		}
	    		
	    		if ($searchtype == SearchParameters::SEARCH_TYPE_CONTAINS)
	    		{
	    			if ($datatype != 'int' && $datatype != 'decimal')
	    			{
	    				$searchtype = "LIKE";
	    				$value = '%' . $value . '%';
	    			}
	    			else 
	    			{
	    				$searchtype = '=';
	    			}
	    		}
	    		
	    		if ($searchtype == SearchParameters::SEARCH_TYPE_EQUALS)
	    		{
	    			$searchtype = '=';
	    			if (!$param->isZeroOrBlankSearch() && ($value == '' || $value == 0)) 
	    			{
	    				$value = NULL;
	    			}
	    		}
	    		
	    		$searchtype = ' ' . $searchtype . ' ';
	    		$delimiter = ' ' . $delimiter . ' ';
	    		
	    		if (!is_null($value))
	    		{
	    			$value = $this->getAdapter()->quote($value, $datatype);
	    			$where .= $delimiter . $open . $column . $searchtype . $value . $closed;
	    		}
	    		else
	    		{
	    			$where .= $delimiter . $open . $column . ' IS NULL ' . $closed;
	    		}
	    		Logger::log($where);
    		}
    	}
    	
    	//Close all remaining open parens.
    	while ($countopen > 0)
    	{
    		$where .= ')';
    		$countopen--;
    	}
    	
    	Logger::log($where);
    	
    	//If the login exists, then add the join
    	if ($loginexists && !$loginappended)
    	{
    		$columnarray = array();
    		if (!$countonly)
    		{
    			$columnarray = array('LoginByID', 'LoginDate', 'FromLocationID', 'ToLocationID');
    		}
    		$select->joinInner('ItemLogin', 
		    	'Items.IdentificationID = ItemLogin.IdentificationID', 
		    	$columnarray);
		    $loginappended = TRUE;
    	}
    	//If the proposal exists, then add the join
    	if ($proposalexists && !$proposalappended)
    	{
    		$columnarray = array();
    		if (!$countonly)
    		{
    			$columnarray = array('ProposalDate');
    		}
    		$select->joinInner('ItemProposal', 
		    	'Items.IdentificationID = ItemProposal.IdentificationID', 
		    	$columnarray);
		    $proposalappended = TRUE;
    	}
    	//If the report exists, then add the join
    	if ($reportexists && !$reportappended)
    	{
    		$columnarray = array();
    		if (!$countonly)
    		{
    			$columnarray = array('ReportByID', 'ReportDate');
    		}
    		$select->joinInner('ItemReport', 
		    	'Items.IdentificationID = ItemReport.IdentificationID', 
		    	$columnarray);
		    $reportappended = TRUE;
    	}
    	//If the logout exists, then add the join
    	if ($logoutexists && !$logoutappended)
    	{
    		$columnarray = array();
    		if (!$countonly)
    		{
    			$columnarray = array('LogoutByID', 'LogoutDate', 'FromLocationID', 'ToLocationID');
    		}
    		$select->joinInner('ItemLogout', 
		    	'Items.IdentificationID = ItemLogout.IdentificationID', 
		    	$columnarray);
		    $logoutappended = TRUE;
    	}
    	//If the call numbers exists, then add the join
    	if ($callnumbersexist && !$callnumbersappended)
    	{
    		$select->joinInner('CallNumbers', 
		    	'ItemIdentification.IdentificationID = CallNumbers.IdentificationID', 
		    	array());
		    $callnumbersappended = TRUE;
    	}
    	//If the logout exists, then add the join
    	if ($importanceexists && !$importanceappended)
    	{
    		//NEed the report to append the importances.
    		if (!$reportappended)
    		{
	    		$columnarray = array();
	    		if (!$countonly)
	    		{
	    			$columnarray = array('ReportByID', 'ReportDate');
	    		}
	    		$select->joinInner('ItemReport', 
			    	'Items.IdentificationID = ItemReport.IdentificationID', 
			    	$columnarray);
			   	$reportappended = TRUE;
    		}
    		$select->joinInner('ItemImportances', 
		    	'ItemReport.ReportID = ItemImportances.ReportID', 
		    	array());
		    $importanceappended = TRUE;
    	}
    	//If the conservators item exists, then add the join
    	if ($conservatorsexists && !$conservatorsappended)
    	{
    		$columnarray = array();
    		if (!$countonly)
    		{
    			$columnarray = array();
    		}
    		$select->joinInner('ItemConservators', 
		    	'ItemReport.ReportID = ItemConservators.ReportID', 
		    	$columnarray);
		    $conservatorsappended = TRUE;
    	}
    	
    	//If the proposed by exists, then add the join
    	if ($proposedbyexists && !$proposedbyappended)
    	{
    		$columnarray = array();
    		if (!$countonly)
    		{
    			$columnarray = array('ProposedBy.PersonID');
    		}
    		$select->joinLeft('ProposedBy',
    				'ItemProposal.ProposalID = ProposedBy.ProposalID',
    				$columnarray);
    		$proposedbyappended = TRUE;
    	}
    	 
    	
    	//If the temp transfer exists, then add the join
    	if ($temptransferexists && !$temptransferappended)
    	{
    		$columnarray = array();
    		$select->joinInner('TemporaryTransfers', 
		    	'Items.ItemID = TemporaryTransfers.ItemID', 
		    	$columnarray);
		    $temptransferappended = TRUE;
    	}
    	//If the work assigned exists, then add the join
    	if ($workassignedexists && !$workassignedappended)
    	{
    		$select->joinLeft('WorkAssignedTo', 
		    	'Items.ItemID = WorkAssignedTo.ItemID', 
		    	array());
		    $workassignedappended = TRUE;
    	}
    	
    	if (!empty($where))
    	{
    		$select->where($where);
    		$select->distinct();
    	}
    	return $select;
    }
    
    private function getDataType($column, $tablename = NULL)
    {
    	$datatype = 'VARCHAR';
		if (!is_null($tablename))
    	{
	    	$table = $this->getAdapter()->describeTable($tablename);
	    	
	    	if (array_key_exists($column, $table))
	    	{
		    	$datatype = $table[$column]['DATA_TYPE'];
	    	}
    	}
    	elseif ($column == 'RecordID')
    	{
    		$datatype = 'int';
    	}
   		return $datatype;		
    }
    
    /*
     * Creates multiple items based on the model item
     * 
     */
    public function createMultipleItems(Item $modelitem, $numbertocreate)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		$itemscreated = 0;
   		try {
   			for ($counter = 0; $counter < $numbertocreate; $counter++)
    		{
    			$itemid = $this->saveItemIdentification($modelitem, NULL, $db);
    			if (!is_null($itemid))
    			{
    				$itemscreated++;
    			}
    		}
	        
   			$db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$itemscreated = 0;
	   	}
	   	return $itemscreated;
    }
    
	/*
     * Creates multiple items based on the model item
     * 
     */
    public function updateMultipleItems(array $items, array $olditems)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		$itemsupdated = 0;
   		try {
   			foreach ($items as $item)
    		{
    			$olditem = $olditems[$item->getItemID()];
    			$itemid = $this->saveItemIdentification($item, $olditem, $db);
    			if (!is_null($itemid))
    			{
    				$itemsupdated++;
    			}
    		}
	        
   			$db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$itemsupdated = 0;
	   	}
	   	return $itemsupdated;
    }

    /**
     * Saves the Item Identification information
     * @access public
     * @param  Item item
     * @param  Item oldItem - the item currently in the database
     */
    public function saveItemIdentification(Item $item, Item $oldItem = NULL, $db = NULL)
    {
        //Build the array to save
        $itemid = $item->getItemID();
        Logger::log('saving item ' . $itemid, Zend_Log::DEBUG);
        if (is_null($db))
        {
	        $db = $this->getAdapter();
	   		$db->beginTransaction();
	   		try {
	   			
		        //If the item isn't in the database,
		        //insert it
		        if (is_null($itemid) || !$this->itemExists($itemid))
		        {
		        	$itemid = $this->insertNewItem($item, $db);
		        }
		        //otherwise, update it
		        else 
		        {
		        	$itemid = $this->updateItem($item, $oldItem, $db);
		        }
		        
	   			$db->commit();
	   		}
	   		catch (Exception $e)
	   		{
	   			Logger::log($e->getMessage(), Zend_Log::ERR);
	   			$db->rollBack();
	   			$itemid = NULL;
	   		}
        }
        else 
        {
        	//If the item isn't in the database,
	        //insert it
	        if (is_null($itemid) || !$this->itemExists($itemid))
	        {
	        	$itemid = $this->insertNewItem($item, $db);
	        }
	        //otherwise, update it
	        else 
	        {
	        	$itemid = $this->updateItem($item, $oldItem, $db);
		    }
        }
   		return $itemid; 
    }
    
	/**
     * Saves the Fund information
     * @access public
     * @param  Item item
     * @param  Item oldItem - the item currently in the database
     */
    public function saveFundInformation(Item $item)
    {
        //Build the array to save
        $itemid = $item->getItemID();
        $db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try {
	   		$itemarray = array(
    			self::EXPECTED_DATE_OF_RETURN => $item->getExpectedDateOfReturn(),
    			self::FUND_MEMO => $item->getFundMemo(),
    			self::INSURANCE_VALUE => $item->getInsuranceValue()
    		);
    		Logger::log('Saving Fund info', Zend_Log::DEBUG);
		    Logger::log($itemarray, Zend_Log::DEBUG);
		    $db->update($this->_name, $itemarray, 'ItemID=' . $itemid);	
	   		$db->commit();
	   	}
	   	catch (Exception $e)
	   	{
	   		Logger::log($e->getMessage(), Zend_Log::ERR);
	   		$db->rollBack();
	   		$itemid = NULL;
	   	}
        
   		return $itemid; 
    }
    
    /*
     * Inserts the new item into the database
     * 
     * @return int itemid
     */
    private function insertNewItem(Item $item, $db)
    {    
	    $identificationarray = $this->buildIdentificationArray($item);
        $itemarray = $this->buildItemArray($item);
        
        Logger::log($identificationarray, Zend_Log::DEBUG);
	    $db->insert('ItemIdentification', $identificationarray);
	    $identificationid = $db->lastInsertId();
	    $itemarray[self::IDENTIFICATION_ID] = $identificationid;
	    Logger::log($itemarray, Zend_Log::DEBUG);
	    $db->insert($this->_name, $itemarray);
	    $itemid = $db->lastInsertId();   
	    
	    //Related arrays
	    $callnumbers = $item->getCallNumbers();
	    $insertcounts = $this->buildInsertCounts($item->getInitialCounts());
	    $workassignedto = $item->getWorkAssignedTo();
	    
	    $this->insertCallNumbers($db, $identificationid, $callnumbers);
		$this->insertCounts($db, $itemid, $insertcounts);
		$this->insertWorkAssignedTo($db, $itemid, $workassignedto);
		
		$auth = Zend_Auth::getInstance();
  		$identity = $auth->getIdentity();
   		$audittrail = new AuditTrail();
        $zenddate = new Zend_Date();
		$audittrail->setDate($zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT));
        $audittrail->setTableName('ItemIdentification');
        $audittrail->setPersonID($identity[PeopleDAO::PERSON_ID]);
	    $audittrail->setActionType(AuditTrailDAO::ACTION_INSERT);
	    $audittrail->setPrimaryKey($identificationid);
		AuditTrailDAO::getAuditTrailDAO()->insertAuditTrailItem($audittrail, $db);
   			
	    return $itemid;
    }
    
	/*
     * Updates the item in the database
     * 
     * @return Item the item with the updated ids.
     */
    private function updateItem(Item $item, Item $oldItem, $db)
    {
    	$itemid = $item->getItemID();
        $identificationid = $item->getPrimaryKey();
        
        $identificationarray = $this->buildIdentificationArray($item);
        $itemarray = $this->buildItemArray($item);
        Logger::log($itemarray, Zend_Log::DEBUG);
        $db->update('ItemIdentification', $identificationarray, 'IdentificationID=' . $identificationid);	
       	$db->update($this->_name, $itemarray, 'ItemID=' . $itemid);	
       	
       	$callnumbers = $item->getCallNumberDifference($oldItem->getCallNumbers());
       	$removalcallnumbers = $oldItem->getCallNumberDifference($item->getCallNumbers());
       	$this->removeCallNumbers($db, $identificationid, $removalcallnumbers);
       	
       	$workassignedto = $item->getWorkAssignedToDifference($oldItem->getWorkAssignedTo());
       	$removalworkassignedto = $oldItem->getWorkAssignedToDifference($item->getWorkAssignedTo());
       	$this->removeWorkAssignedTo($db, $itemid, $removalworkassignedto);
       	
       	$insertcounts = $this->buildInsertCounts($item->getInitialCounts());
       	$removalcounts = $this->buildRemovalCounts($item->getInitialCounts());
	    $this->removeCounts($db, $itemid, $removalcounts);
	   
	    $this->insertCallNumbers($db, $identificationid, $callnumbers);
		$this->insertCounts($db, $itemid, $insertcounts);
 		$this->insertWorkAssignedTo($db, $itemid, $workassignedto);
		
		$auth = Zend_Auth::getInstance();
  		$identity = $auth->getIdentity();
   		$audittrail = new AuditTrail();
        $zenddate = new Zend_Date();
		$audittrail->setDate($zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT));
        $audittrail->setTableName('ItemIdentification');
        $audittrail->setPersonID($identity[PeopleDAO::PERSON_ID]);
	    $audittrail->setActionType(AuditTrailDAO::ACTION_UPDATE);
	    $audittrail->setPrimaryKey($identificationid);
		AuditTrailDAO::getAuditTrailDAO()->insertAuditTrailItem($audittrail, $db);
   		
	    return $itemid;
    }
    
	/**
     * Saves the Comments since they can be updated from multiple screens
     * @access public
     * @param  Item item
     * @param  db - the database object if it exists
     */
    public function updateComments(Item $item, $db = NULL)
    {
        //Build the array to save
        $identificationid = $item->getPrimaryKey();
        $rowsupdate = 0;
        $commentsarray = array(self::COMMENTS => $item->getComments());
        if (is_null($db))
        {
	        $db = $this->getAdapter();
	   		$db->beginTransaction();
	   		try {
		   		$rowsupdate = $db->update('ItemIdentification', $commentsarray, 'IdentificationID=' . $identificationid);	
		        $db->commit();
	   		}
	   		catch (Exception $e)
	   		{
	   			Logger::log($e->getMessage(), Zend_Log::ERR);
	   			$db->rollBack();
	   			$rowsupdate = 0;
	   		}
        }
        else 
        {
        	$rowsupdate = $db->update('ItemIdentification', $commentsarray, 'IdentificationID=' . $identificationid);	
        }
   		return $rowsupdate; 
    }
    
    private function buildInsertCounts(Counts $counts)
    {
    	$countsarray = array();
    	if (($counts->getOtherCount() != 0 || !is_null($counts->getOtherDescription())))
    	{
    		$countsarray["Other"] = array(
    			self::COUNT_TYPE => "Other",
    			self::COUNT_DESCRIPTION => $counts->getOtherDescription(),
    			self::TOTAL_COUNT => $counts->getOtherCount()
    		);
    	}
    	if ($counts->getVolumeCount() != 0 || !is_null($counts->getVolumeDescription()))
    	{
    		$countsarray["Volumes"] = array(
    			self::COUNT_TYPE => "Volumes",
    			self::COUNT_DESCRIPTION => $counts->getVolumeDescription(),
    			self::TOTAL_COUNT => $counts->getVolumeCount()
    		);
    	}
    	if ($counts->getSheetCount() != 0 || !is_null($counts->getSheetDescription()))
    	{
    		$countsarray["Sheets"] = array(
    			self::COUNT_TYPE => "Sheets",
    			self::COUNT_DESCRIPTION => $counts->getSheetDescription(),
    			self::TOTAL_COUNT => $counts->getSheetCount()
    		);
    	}
    	if ($counts->getPhotoCount() != 0 || !is_null($counts->getPhotoDescription()))
    	{
    		$countsarray["Photos"] = array(
    			self::COUNT_TYPE => "Photos",
    			self::COUNT_DESCRIPTION => $counts->getPhotoDescription(),
    			self::TOTAL_COUNT => $counts->getPhotoCount()
    			);
    	}
    	if ($counts->getBoxCount() != 0 || !is_null($counts->getBoxDescription()))
    	{
    		$countsarray["Boxes"] = array(
    			self::COUNT_TYPE => "Boxes",
    			self::COUNT_DESCRIPTION => $counts->getBoxDescription(),
    			self::TOTAL_COUNT => $counts->getBoxCount()
    			);
    	}
    	return $countsarray;
    }
    
	private function buildRemovalCounts(Counts $counts)
    {
    	$countsarray = array();
    	if ($counts->getOtherCount() == 0 && is_null($counts->getOtherDescription()))
    	{
    		array_push($countsarray, "Other");
    	}
    	if ($counts->getVolumeCount() == 0 && is_null($counts->getVolumeDescription()))
    	{
    		array_push($countsarray, "Volumes");
    	}
    	if ($counts->getSheetCount() == 0 && is_null($counts->getSheetDescription()))
    	{
    		array_push($countsarray, "Sheets");
    	}
    	if ($counts->getPhotoCount() == 0 && is_null($counts->getPhotoDescription()))
    	{
    		array_push($countsarray, "Photos");
    	}
    	if ($counts->getBoxCount() == 0 && is_null($counts->getBoxDescription()))
    	{
    		array_push($countsarray, "Boxes");
    	}
    	return $countsarray;
    }
    
    /*
     * Insert related call numbers
     */
    private function insertCallNumbers($db, $identificationid, array $callnumbers)
    {
    	foreach ($callnumbers as $callnumber)
       	{
       		$callnumberarray = array(
       			CombinedRecordsDAO::CALL_NUMBER => trim($callnumber),
       			self::IDENTIFICATION_ID => $identificationid
       		);
       		$db->insert('CallNumbers', $callnumberarray);
       	}
    }
    
	/*
     * Remove related call numbers
     */
    private function removeCallNumbers($db, $identificationid, array $callnumbers)
    {
    	foreach ($callnumbers as $callnumber)
       	{
       		$callnumber = $this->getAdapter()->quote($callnumber);
       		$db->delete('CallNumbers', 'IdentificationID=' . $identificationid . ' AND CallNumber=' . $callnumber);
       	}
    }
    
	/*
     * Insert related call numbers
     */
    private function insertWorkAssignedTo($db, $itemid, array $workassignedto)
    {
    	foreach ($workassignedto as $work)
       	{
       		$workassignedtoarray = array(
       			PeopleDAO::PERSON_ID => $work->getPrimaryKey(),
       			self::ITEM_ID => $itemid
       		);
       		$db->insert('WorkAssignedTo', $workassignedtoarray);
       	}
    }
    
	/*
     * Remove related work assigned to
     */
    private function removeWorkAssignedTo($db, $itemid, array $workassignedto)
    {
    	foreach ($workassignedto as $work)
       	{
       		$db->delete('WorkAssignedTo', 'ItemID=' . $itemid . ' AND PersonID=' . $work->getPrimaryKey());
       	}
    }
    
	/*
     * Insert related counts
     */
    private function insertCounts($db, $itemid, array $counts)
    {
    	foreach ($counts as $count)
       	{
       		if ($this->countTypeExists($count[self::COUNT_TYPE], $itemid))
       		{
       			$counttype = $this->getAdapter()->quote($count[self::COUNT_TYPE]);
       			$db->update('InitialCounts', $count, 'ItemID=' . $itemid . ' AND CountType=' . $counttype);
       		}
       		else 
       		{
	       		$count[self::ITEM_ID] = $itemid;
	       		$db->insert('InitialCounts', $count);
       		}
       	}
    }
    
	private function countTypeExists($counttype, $itemid)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from('InitialCounts', array('Count' => 'Count(*)'));
    	$counttype = $this->getAdapter()->quote($counttype);
    	$select->where('CountType=' . $counttype . ' AND ItemID=' . $itemid);
    	$row = $this->fetchRow($select);
    	return $row->Count > 0;
    }
    
	/*
     * Remove related counts
     */
    private function removeCounts($db, $itemid, array $counts)
    {
    	foreach ($counts as $count)
       	{
       		$db->delete('InitialCounts', 'ItemID=' . $itemid . ' AND CountType="' . $count . '"');
       	}
    }
    
	/*
     * Insert curators
     */
    private function insertCurators($db, $identificationid, array $curators)
    {
    	foreach ($curators as $curator)
       	{
       		$curatorarray = array(
       			self::IDENTIFICATION_ID => $identificationid,
       			PeopleDAO::PERSON_ID => $curator->getPrimaryKey()
       		);
       		$db->insert('Curators', $curatorarray);
       	}
    }
    
	/*
     * Remove related curators
     */
    private function removeCurators($db, $identificationid, array $curators)
    {
    	foreach ($curators as $curator)
       	{
       		$db->delete('Curators', 'IdentificationID=' . $identificationid . ' AND PersonID=' . $curator->getPrimaryKey());
       	}
    }
    
    /*
     * Data to be loaded into the Item table
     */
    private function buildItemArray(Item $item)
    {
    	$returndate = $item->getExpectedDateOfReturn();
    	if (!empty($returndate))
    	{
    		$zendexamdate = new Zend_Date($returndate, ACORNConstants::$ZEND_DATE_FORMAT);
    		$returndate = $zendexamdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	}
    	
    	$idarray = array(
    		self::AUTHOR_ARTIST => $item->getAuthorArtist(),
    		self::COLLECTION_NAME => $item->getCollectionName(),
    		self::COORDINATOR_ID => $item->getCoordinatorID(),
    		self::DATE_OF_OBJECT => $item->getDateOfObject(),
    		FormatDAO::FORMAT_ID => $item->getFormatID(),
    		self::EXPECTED_DATE_OF_RETURN => $returndate,
    		self::FUND_MEMO => $item->getFundMemo(),
    		self::HOLLIS_NUMBER => $item->getHOLLISNumber(),
    		self::INSURANCE_VALUE => $item->getInsuranceValue(),
    		self::IS_NON_COLLECTION_MATERIAL => $item->isNonCollectionMaterial(),
    		self::STORAGE => $item->getStorage()
    	);
    	return $idarray;
    }
    
    /*
     * Data to be loaded into the ItemIdentification table
     */
	private function buildIdentificationArray(Item $item)
    {
    	$idarray = array(
    		self::COMMENTS => $item->getComments(),
    		DepartmentDAO::DEPARTMENT_ID => $item->getDepartmentID(),
    		GroupDAO::GROUP_ID => $item->getGroupID(),
    		ProjectDAO::PROJECT_ID => $item->getProjectID(),
    		self::HOME_LOCATION_ID => $item->getHomeLocationID(),
    		self::CHARGE_TO_ID => $item->getChargeToID(),
    		PurposeDAO::PURPOSE_ID => $item->getPurposeID(),
    		self::TITLE => $item->getTitle(),
    		self::CURATOR_ID => $item->getCuratorID(),
    		self::APPROVING_CURATOR_ID => $item->getApprovingCuratorID(),
    		self::MANUALLY_CLOSED => $item->getManuallyClosed(),
    		self::MANUALLY_CLOSED_DATE => $item->getManuallyClosedDate()
    	);
    	return $idarray;
    }
    
    public function itemExists($itemID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(ItemID)'));
    	$select->where('ItemID=' . $itemID);
    	Logger::log($select->__toString());
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }

    
    
	/**
     * Returns the initial counts with the given id.
     *
     * @access public
     * @param  Integer itemID
     * @return Counts object
     */
    public function getInitialCounts($itemID)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from('InitialCounts', array('CountType', 'TotalCount', 'Description'));
    	$itemID = $this->getAdapter()->quote($itemID, 'INTEGER');
	    $select->where('ItemID=' . $itemID);
	    $rows = $this->fetchAll($select);
    	$count = new Counts();
	    if (!is_null($rows))
	    {
	    	$rowarray = $rows->toArray();
	    	foreach ($rowarray as $value)
	    	{
	    		switch ($value[self::COUNT_TYPE])
	    		{
	    			case "Volumes":
	    				$count->setVolumeCount($value[self::TOTAL_COUNT]);
	    				$count->setVolumeDescription($value[self::COUNT_DESCRIPTION]);
	    				break;
	    			case "Sheets":
	    				$count->setSheetCount($value[self::TOTAL_COUNT]);
	    				$count->setSheetDescription($value[self::COUNT_DESCRIPTION]);
	    				break;
	    			case "Photos":
	    				$count->setPhotoCount($value[self::TOTAL_COUNT]);
	    				$count->setPhotoDescription($value[self::COUNT_DESCRIPTION]);
	    				break;
	    			case "Boxes":
	    				$count->setBoxCount($value[self::TOTAL_COUNT]);
	    				$count->setBoxDescription($value[self::COUNT_DESCRIPTION]);
	    				break;
	    			case "Other":
	    				$count->setOtherCount($value[self::TOTAL_COUNT]);
	    				$count->setOtherDescription($value[self::COUNT_DESCRIPTION]);
	    				break;
	    		}
	    	}
	    }
	    return $count;
    }
    
    public function deleteItem(Item $item)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		try {
	   		$rowsdeleted = $db->delete('ItemIdentification', 'IdentificationID=' . $item->getPrimaryKey());	
	        $db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$rowsdeleted = 0;
	   	}
	   	return $rowsdeleted;
    }
    
    /**
     * Marks the given item as closed manually.  This is used if the item is not
     * going to come to WPC.  Otherwise, the logout dates give the item
     * a status of done.
     * @param Item $item - the item to close
     * @return TRUE if the item was successfully closed.
     */
    public function markAsManuallyClosed(Item $item)
    {
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	$updated = TRUE;
    	try {
    		$updatearray = array(self::MANUALLY_CLOSED => 1, self::MANUALLY_CLOSED_DATE => date(ACORNConstants::$DATABASE_DATETIME_FORMAT));
    		$db->update('ItemIdentification', $updatearray, 'IdentificationID=' . $item->getPrimaryKey());
    		$db->commit();
    	}
    	catch (Exception $e)
    	{
    		Logger::log($e->getMessage(), Zend_Log::ERR);
    		$db->rollBack();
    		$updated = FALSE;
    	}
    	
    	//If it was successful, then also lock the item if it was unlocked.
    	if ($updated && $item->isUnlocked())
    	{
    		$updated = UnlockedItemsDAO::getUnlockedItemsDAO()->lockItem($item->getItemID());
    	}
    	return $updated;
    }
    
    public function getItemID($identificationid)
    {
    	$select = $this->select();
    	$select->from($this, array('ItemID'));
    	$select->where('IdentificationID=' . $identificationid);
    	$row = $this->fetchRow($select);
    	$itemid = NULL;
    	if (!is_null($row))
    	{
    		$itemid = $row->ItemID;
    	}
    	return $itemid;
    }
    
    public function setEditStatus(Item $item, $status, $editedByID = NULL)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		try 
   		{
   			$updatearray = array(self::EDITED_BY_ID => $editedByID, self::IS_BEING_EDITED => $status);
   			Logger::log('Edit status for ' . $item->getPrimaryKey(), Zend_Log::DEBUG);
	   		Logger::log($updatearray, Zend_Log::DEBUG);
	   		$rowsupdated = $db->update('ItemIdentification', $updatearray, 'IdentificationID=' . $item->getPrimaryKey());	
	        $db->commit();
	        if ($rowsupdated == 0)
	        {
	        	$rowsupdated = 1;
	        }
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$rowsupdated = 0;
	   	}
	   	return $rowsupdated;
    }
    
    public function clearEdits($personid)
    {
    	$rowsupdated = 0;
    	if (!is_null($personid))
    	{
    		$db = $this->getAdapter();
	   		$db->beginTransaction();
	   		try 
	   		{
	   			$updatearray = array(self::EDITED_BY_ID => NULL, self::IS_BEING_EDITED => 0);
	   			Logger::log('removing edited by: ' . $personid, Zend_Log::DEBUG);
	   			$rowsupdated = $db->update('ItemIdentification', $updatearray, 'EditedByID=' . $personid);	
		        $db->commit();
		        if ($rowsupdated == 0)
		        {
		        	$rowsupdated = 1;
		        }
	   		}
	   		catch (Exception $e)
	   		{
	   			Logger::log($e->getMessage(), Zend_Log::ERR);
	   			$db->rollBack();
	   			$rowsupdated = 0;
		   	}
    	}
	   	return $rowsupdated;
    }
    
    public function getCurrentUserItemIDs()
    {
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$personid = $identity[PeopleDAO::PERSON_ID];
    	
    	$select = $this->select();
    	$select->from($this, array('ItemID'));
    	$select->setIntegrityCheck(FALSE);
    	$select->joinLeft('ItemLogout', 'Items.IdentificationID = ItemLogout.IdentificationID',
    		array());
    	$select->joinLeft('WorkAssignedTo', 'Items.ItemID = WorkAssignedTo.ItemID',
    		array());
    	$select->where('LogoutID IS NULL AND (CoordinatorID=' . $personid . ' OR WorkAssignedTo.PersonID=' . $personid . ')');
    	$rows = $this->fetchAll($select);
    	$retval = array();
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }
    
	public function getCurrentUserItems()
    {
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$personid = $identity[PeopleDAO::PERSON_ID];
    	
    	$select = $this->buildBaseSelect();
    	$select->joinLeft('ItemLogout', 'Items.IdentificationID = ItemLogout.IdentificationID',
    		array());
    	$select->joinLeft('WorkAssignedTo', 'Items.ItemID = WorkAssignedTo.ItemID',
    		array());
    	$select->where('LogoutID IS NULL AND (CoordinatorID=' . $personid . ' OR WorkAssignedTo.PersonID=' . $personid . ')');
    	
    	$rows = $this->fetchAll($select);
    	$retval = array();
    	if (!is_null($rows))
    	{
    		$rowarray = $rows->toArray();
    		
    		foreach($rowarray as $row)
    		{
    			$item = $this->buildItem($row);
    			array_push($retval, $item);
    		}
    	}
    	return $retval;
    }
    
	/**
     * Returns an array of author artists
     *
     * @access public
     * @param string author
     * @return JSON ready array
     */
    public function getAuthorArtists($author)
    {
    	$select = $this->select();
    	$select->from($this, array(self::AUTHOR_ARTIST));
    	$author = $this->getAdapter()->quote($author . '%');
    	$select->where(self::AUTHOR_ARTIST . ' LIKE ' . $author);
    	$select->order(self::AUTHOR_ARTIST);
    	$select->distinct();
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$rows = $this->fetchAll($select);
    	$retval = array();
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }
    
	/**
     * Returns a list of storage formatted for a select list
     *
     * @access public
     * @return array to be used with JSON, NULL if none found
     */
    public function getAllStorageForSelect()
    {
    	$select = $this->select();
    	$select->from($this, array('Storage'));
    	$select->distinct();
    	$select->where('Storage <> \'\' AND Storage IS NOT NULL');
    	$select->order('Storage');
    	$rows = $this->fetchAll($select);
    	$retval = NULL;
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }
    
	/**
     * Work Assigned To with the given id.
     *
     * @access public
     * @param  Integer itemID
     * @return array of People objects
     */
    public function getWorkAssignedTo($itemID)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from('WorkAssignedTo', array('PersonID'));
    	$select->joinInner('People', 'WorkAssignedTo.PersonID = People.PersonID', array('DisplayName', 'Initials', 'Inactive'));
    	$itemID = $this->getAdapter()->quote($itemID, 'INTEGER');
	    $select->where('WorkAssignedTo.ItemID=' . $itemID);
	    Logger::log($select->__toString());
	    $rows = $this->fetchAll($select);
    	$workassignedarray = array();
	    if (!is_null($rows))
	    {
	    	$rowarray = $rows->toArray();
	    	foreach ($rowarray as $row)
	    	{
	    		$person = new Person($row[PeopleDAO::PERSON_ID], $row[PeopleDAO::INACTIVE]);
	    		$person->setDisplayName($row[PeopleDAO::DISPLAY_NAME]);
	    		$person->setInitials($row[PeopleDAO::INITIALS]);
	    		$workassignedarray[$row[PeopleDAO::PERSON_ID]] = $person;
	    	}
	    }
	    return $workassignedarray;
    }
    
	public function getCurrentCuratorRecords()
    {
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$personid = $identity[PeopleDAO::PERSON_ID];
    	
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from($this->_name, array('*'));
    	$select->joinInner('ItemIdentification', 'Items.IdentificationID = ItemIdentification.IdentificationID',
    		array('*'));
    	$select->joinInner('ProposalApprovalHistory', 'Items.ItemID = ProposalApprovalHistory.PKID AND ProposalApprovalHistory.RecordType = \'Item\'',
    		array());
    	$where = 'CuratorID=' . $personid;
    	$select->where($where);
    	$select->order('ItemID');
    	$select->distinct();
    	Logger::log($select->__toString());
    	$rows = $this->fetchAll($select);
    	$retval = array();
    	if (!is_null($rows))
    	{
    		$rowarray = $rows->toArray();
    		
    		foreach($rowarray as $row)
    		{
    			$item = $this->buildItem($row);
    			array_push($retval, $item);
    		}
    	}
    	return $retval;
    }
    
    /**
     * Determines if the given HOLLIS number exists in the database for
     * an Item.
     * 
     * @param $value - the Integer HOLLIS number to search for.
     * @param $exclusionIDs OPTIONAL array - excludes the given IDs from the query.
     * @return the ACORN # if the HOLLIS number exists, NULL otherwise.
     */
    public function hollisNumberExists($value, array $exclusionIDs = NULL)
    {
    	$select = $this->select();
    	$select->from($this, array('ItemID'));
    	$value = $this->getAdapter()->quote($value, 'INTEGER');
    	$select->where('HOLLISNumber=' . $value);
    	if (!empty($exclusionIDs))
    	{
    		$exclusionstring = implode(",", $exclusionIDs);
    		if (!empty($exclusionstring))
    		{
    			$select->where('ItemID NOT IN (' . $exclusionstring . ')');
    		}
    	}
    	$row = $this->fetchRow($select);
    	if (!is_null($row))
    	{
    		return $row->ItemID;	
    	}
    	return NULL;
    }
    
    
    
    /**
     * Determines if the given title exists in the database for
     * an Item.
     *
     * @param $value - the string title to search for.
     * @param $exclusionIDs OPTIONAL array - excludes the given IDs from the query.
     * @return the ACORN # if the title exists, NULL otherwise.
     */
    public function titleExists($value, array $exclusionIDs = NULL)
    {
    	$select = $this->select();
    	$select->from($this, array('ItemID'));
    	$select->setIntegrityCheck(FALSE);
    	$select->joinInner('ItemIdentification', 'Items.IdentificationID = ItemIdentification.IdentificationID',
    		array());
    	$value = $this->getAdapter()->quote($value, 'VARCHAR');
    	$select->where('Title=' . $value);
    	if (!empty($exclusionIDs))
    	{
    		$exclusionstring = implode(",", $exclusionIDs);
    		if (!empty($exclusionstring))
    		{
    			$select->where('ItemID NOT IN (' . $exclusionstring . ')');
    		}
    	}
    	$row = $this->fetchRow($select);
    	if (!is_null($row))
    	{
    		return $row->ItemID;
    	}
    	return NULL;
    }
    
   
} 

?>