<?php
/**
 * Copyright 2016 The President and Fellows of Harvard College
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 * CombinedRecordsDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
class CombinedRecordsDAO extends Zend_Db_Table
{
	const RECORD_ID = "RecordID";
	const RECORD_TYPE = "RecordType";

	const CALL_NUMBER = 'CallNumber';
	
	/* The name of the table */
	protected $_name = "CombinedRecords";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "IdentificationID";
    

    private static $combinedrecordsDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return CombinedRecordsDAO
     */
    public static function getCombinedRecordsDAO()
	{
		if (!isset(self::$combinedrecordsDAO))
		{
			self::$combinedrecordsDAO = new CombinedRecordsDAO();
		}
		return self::$combinedrecordsDAO;
	}
	
/**
     * Returns the record with the given record id.
     *
     * @access public
     * @param  Integer recordID
     * @return CombinedRecord
     */
    public function getRecord($recordID, $recordType = 'Item')
    {
    	$item = NULL;
	    if (!empty($recordID))
    	{
	    	$recordID = $this->getAdapter()->quote($recordID, 'INTEGER');
	    	$select = $this->select();
	    	$select->where('RecordID=' . $recordID . " AND RecordType = '" . $recordType . "'");
	    	$row = $this->fetchRow($select);
	    	if (!is_null($row))
	    	{
	    		$item = $this->buildCombinedRecord($row->toArray());
	    	}
    	}
    	return $item;
    }

    /**
     * Returns the items based on the search criteria
     *
     * @access public
     * @param  array searchparameters (SearchParameter objects)
     * @return JSON ready array
     */
    public function getRecords(array $searchparameters, $startIndex = 0, $count = 50, $additionalwhere = NULL, $order = 'RecordID')
    {
    	$retval = array();
	    if (!empty($searchparameters) || !empty($additionalwhere))
    	{
	        $select = $this->select();
			$select->setIntegrityCheck(FALSE);
			$select->from($this, array('*'));
		
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
		    		$item = $this->buildCombinedRecord($row);
		    		array_push($retval, $item);
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
	    	$select->joinLeft(array('Coordinator' => 'People'),
	    		'CombinedRecords.CoordinatorID = Coordinator.PersonID', array());
	    }
	    if ($curatororder)
	    {
	    	$select->joinInner(array('Curator' => 'People'),
	    		'Curator.PersonID = CombinedRecords.CuratorID', array());
	    }
	    if ($projectorder)
	    {
	    	$select->joinLeft(array('ProjectsOrder' => 'Projects'),
	    		'CombinedRecords.ProjectID = ProjectsOrder.ProjectID', array());
	    }
	    if ($departmentsorder)
	    {
	    	$select->joinLeft(array('DepartmentsOrder' => 'Departments'),
	    		'CombinedRecords.DepartmentID = DepartmentsOrder.DepartmentID', array());
	    	$select->joinLeft(array('LocationsOrder' => 'Locations'),
	    		'DepartmentsOrder.LocationID = LocationsOrder.LocationID', array());
	    }
	    if ($tuborder || $repositoryorder)
	    {
	    	if (!$departmentsorder)
	    	{
	    		$select->joinInner(array('LocationsOrder' => 'Locations'),
	    		'CombinedRecords.HomeLocationID = LocationsOrder.LocationID', array());
	    	}
	    }
	    $select->order($updatedorder);
	    return $select;
    }
    
	/*
     * @access private
     * @param  array values from the database
     * @return CombinedRecord
     */
    private function buildCombinedRecord(array $values)
    {
    	$item = new CombinedRecord($values[ItemDAO::IDENTIFICATION_ID], $values[ItemDAO::INACTIVE], $values[ItemDAO::IS_BEING_EDITED]);
    	$item->setRecordID($values[self::RECORD_ID]);
    	$item->setAuthorArtist($values[ItemDAO::AUTHOR_ARTIST]);
    	$item->setComments($values[ItemDAO::COMMENTS]);
    	$item->setCoordinatorID($values[ItemDAO::COORDINATOR_ID]);
    	$item->setCuratorID($values[ItemDAO::CURATOR_ID]);
    	$item->setApprovingCuratorID($values[ItemDAO::APPROVING_CURATOR_ID]);
    	$item->setDateOfObject($values[ItemDAO::DATE_OF_OBJECT]);
    	$item->setDepartmentID($values[DepartmentDAO::DEPARTMENT_ID]);
    	$item->setFormatID($values[FormatDAO::FORMAT_ID]);
    	$item->setExpectedDateOfReturn($values[ItemDAO::EXPECTED_DATE_OF_RETURN]);
    	$item->setFundMemo($values[ItemDAO::FUND_MEMO]);
    	$item->setGroupID($values[GroupDAO::GROUP_ID]);
    	$item->setHomeLocationID($values[ItemDAO::HOME_LOCATION_ID]);
    	$item->setInsuranceValue($values[ItemDAO::INSURANCE_VALUE]);
    	$item->setNonCollectionMaterial($values[ItemDAO::IS_NON_COLLECTION_MATERIAL]);
    	$item->setProjectID($values[ProjectDAO::PROJECT_ID]);
    	$item->setPurposeID($values[PurposeDAO::PURPOSE_ID]);
    	$item->setStorage($values[ItemDAO::STORAGE]);
    	$item->setTitle($values[ItemDAO::TITLE]);
    	$item->setCollectionName($values[ItemDAO::COLLECTION_NAME]);
    	$item->setIsBeingEditedByID($values[ItemDAO::EDITED_BY_ID]);
    	$item->setRecordType($values[self::RECORD_TYPE]);
    	$item->setManuallyClosed($values[ItemDAO::MANUALLY_CLOSED]);
    	$item->setManuallyClosedDate($values[ItemDAO::MANUALLY_CLOSED_DATE]);
    	$item->setChargeToID($values[ItemDAO::CHARGE_TO_ID]);
    	return $item;
    }
    
    private function determineOrder($order)
    {
    	return $order;
    }
    
	/**
     * Returns the items based on the search criteria
     *
     * @access public
     * @param  array searchparameters (SearchParameter objects)
     * @return JSON ready array
     */
    public function getRecordCount(array $searchparameters, $additionalwhere = NULL)
    {
    	if (!empty($searchparameters) || !empty($additionalwhere))
    	{
	        $select = $this->select();
			$select->setIntegrityCheck(FALSE);
			$select->from($this, array('Count' => 'COUNT(DISTINCT CombinedRecords.IdentificationID)'));
		
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
    	$oswexists = FALSE;
    	$worktypesexist = FALSE;
    	$workassignedexists = FALSE;
    	$fileexists = FALSE;
    	$proposedbyexists = FALSE;
    	 
    	$loginappended = FALSE;
    	$proposalappended = FALSE;
    	$reportappended = FALSE;
    	$logoutappended = FALSE;
    	$callnumbersappended = FALSE;
    	$importanceappended = FALSE;
    	$temptransferappended = FALSE;
    	$conservatorsappended = FALSE;
    	$oswappended = FALSE;
    	$worktypesappended = FALSE;
    	$workassignedappended = FALSE;
    	$fileappended = FALSE;
    	$proposedbyappended = FALSE;
    	 
    	$where = "";
    	$delimiter = "";
    	$countopen = 0;
    	$isfirst = TRUE;
    	
    	$proposedbyarray = array();
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
    		
    		if ($tablename == "ItemIdentification" || $tablename == "Items")
    		{
    			$tablename = "CombinedRecords";
    			$column = "CombinedRecords." . $columncompare;
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
    		
    		//If the column is the item status, then
    		if ($column == 'ItemStatus')
    		{
    			$statusclause = "";
    			switch ($value)
    			{
    				//Not logged in
    				case ItemDAO::ITEM_STATUS_PENDING:
    					$today = date(ACORNConstants::$DATABASE_DATE_FORMAT);
    					$statusclause = "CombinedRecords.IdentificationID NOT IN (SELECT DISTINCT IdentificationID FROM ItemLogin)";
    					$statusclause .= " AND (CombinedRecords.ManuallyClosed = 0";
    					$statusclause .= " OR CombinedRecords.RecordID IN (SELECT DISTINCT ItemID FROM UnlockedItems WHERE ExpirationDate > '" . $today . "'))";
    					break;
    				//Logged in but not logged out
    				case ItemDAO::ITEM_STATUS_LOGGED_IN:
    					//Join inner on temp transfers and set the where clause 
    					$select->joinLeft(array('Transfers' => 'TemporaryTransfers'), 
					    	'CombinedRecords.RecordID = Transfers.ItemID AND Transfers.TransferType = \'Transfer\'', 
					    	array());
    					//Join inner on temp transfer return and set the where clause 
    					$select->joinLeft(array('Returns' => 'TemporaryTransfers'), 
					    	'CombinedRecords.RecordID = Returns.ItemID AND Returns.TransferType = \'Return\' AND Transfers.ToLocationID = Returns.FromLocationID AND Transfers.TransferDate <= Returns.TransferDate', 
					    	array());
    					$statusclause = "(CombinedRecords.IdentificationID IN (SELECT DISTINCT IdentificationID FROM ItemLogin)";
    					$statusclause .= " AND CombinedRecords.IdentificationID NOT IN (SELECT DISTINCT IdentificationID FROM ItemLogout)";
    					$statusclause .= " AND (Transfers.TemporaryTransferID IS NULL OR (Transfers.TemporaryTransferID = (SELECT MAX(TemporaryTransferID) FROM TemporaryTransfers WHERE ItemID = CombinedRecords.RecordID AND TransferType = 'Transfer')";
    					$statusclause .= " AND Returns.TemporaryTransferID IS NOT NULL)))";
    					break;
    				//Temp out
    				case ItemDAO::ITEM_STATUS_TEMP_OUT:
    					//Join inner on temp transfers and set the where clause 
    					$select->joinLeft(array('TempTransfers' => 'TemporaryTransfers'), 
					    	'CombinedRecords.RecordID = TempTransfers.ItemID AND TempTransfers.TransferType = \'Transfer\'', 
					    	array());
    					//Join inner on temp transfer return and set the where clause 
    					$select->joinLeft(array('TempReturns' => 'TemporaryTransfers'), 
					    	'CombinedRecords.RecordID = TempReturns.ItemID AND TempReturns.TransferType = \'Return\' AND TempTransfers.ToLocationID = TempReturns.FromLocationID AND TempTransfers.TransferDate <= TempReturns.TransferDate', 
					    	array());
    					$statusclause = "(CombinedRecords.IdentificationID NOT IN (SELECT DISTINCT IdentificationID FROM ItemLogout)";
    					$statusclause .= " AND TempTransfers.TemporaryTransferID = (SELECT MAX(TemporaryTransferID) FROM TemporaryTransfers WHERE ItemID = CombinedRecords.RecordID AND TransferType = 'Transfer')";
    					$statusclause .= " AND TempReturns.TemporaryTransferID IS NULL)";
    					break;
    				//Logged Out or Manually Closed and not Unlocked.
    				case ItemDAO::ITEM_STATUS_DONE:
    					$today = date(ACORNConstants::$DATABASE_DATE_FORMAT);
    					$statusclause = "(CombinedRecords.IdentificationID IN (SELECT DISTINCT IdentificationID FROM ItemLogout)";
    					$statusclause .= " OR (CombinedRecords.ManuallyClosed = 1 AND CombinedRecords.RecordType = 'Item'))";
    					//$statusclause .= " AND CombinedRecords.RecordID NOT IN (SELECT DISTINCT ItemID FROM UnlockedItems WHERE ExpirationDate > '" . $today . "')";
    					break;
    				//NOT Logged Out or Manually Closed and not Unlocked.
    				case ItemDAO::ITEM_STATUS_MANUALLY_MARKED_AS_DONE:
    					$today = date(ACORNConstants::$DATABASE_DATE_FORMAT);
    					$statusclause = "CombinedRecords.IdentificationID NOT IN (SELECT DISTINCT IdentificationID FROM ItemLogout)";
    					$statusclause .= " AND CombinedRecords.ManuallyClosed = 1 AND CombinedRecords.RecordType = 'Item'";
    					$statusclause .= " AND CombinedRecords.RecordID NOT IN (SELECT DISTINCT ItemID FROM UnlockedItems WHERE ExpirationDate > '" . $today . "')";
    					break;
    			}
    			if (!empty($statusclause))
    			{
	    			$delimiter = ' ' . $delimiter . ' ';
	    			$where .= $delimiter . $open . '(RecordType = "Item" AND ' . $statusclause  . ')' . $closed;
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
    					$activityclause = "(RecordType = 'Item' AND ItemReport.AdminOnly = 0 AND ItemReport.ExamOnly = 0 AND ItemReport.CustomHousingOnly = 0)";
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
    				//On site
    				case 'On-site':
    					$activityclause = "CombinedRecords.IdentificationID IN (SELECT IdentificationID FROM OSW)";
    					break;
    			}
    			if (!empty($activityclause))
    			{
	    			$delimiter = ' ' . $delimiter . ' ';
		    		$where .= $delimiter . $open . $activityclause . $closed;
    			}
    		}
    		elseif ($column == 'OSWStatus')
    		{
    			$statusclause = "";
    			switch ($value)
    			{
    				//Not logged in
    				case 'Open':
    					$statusclause = "(RecordType = 'OSW' AND OSW.WorkEndDate IS NULL)";
    					break;
    				//Logged in but not logged out
    				case 'Closed':
    					$statusclause = "(RecordType = 'OSW' AND OSW.WorkEndDate IS NOT NULL)";
    					break;
    			}
    			if (!empty($statusclause))
    			{
	    			$delimiter = ' ' . $delimiter . ' ';
		    		$where .= $delimiter . $open . $statusclause . $closed;
		    		$oswexists = TRUE;
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
	    			array_push($proposedbyarray, $value);
	    			$proposalexists = TRUE;
	    			$proposedbyexists = TRUE;
	    		}	
	    		elseif ($tablename == 'OSW')
	    		{
	    			$oswexists = TRUE;
	    		}
	    		elseif ($tablename == 'OSWWorkTypes')
		    	{
		    		$worktypesexist = TRUE;
		    	}	
	    		elseif ($tablename == 'WorkAssignedTo')
	    		{
	    			$workassignedexists = TRUE;
	    		}
	    		elseif ($tablename == 'Files')
	    		{
	    			$fileexists = TRUE;	
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
	    			if (empty($value)) 
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
    	
    	//If the login exists, then add the join
    	if ($loginexists && !$loginappended)
    	{
    		$columnarray = array();
    		if (!$countonly)
    		{
    			$columnarray = array('LoginByID', 'LoginDate', 'FromLocationID', 'ToLocationID');
    		}
    		$select->joinLeft('ItemLogin', 
		    	'CombinedRecords.IdentificationID = ItemLogin.IdentificationID', 
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
    		$select->joinLeft('ItemProposal', 
		    	'CombinedRecords.IdentificationID = ItemProposal.IdentificationID', 
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
    		$select->joinLeft('ItemReport', 
		    	'CombinedRecords.IdentificationID = ItemReport.IdentificationID', 
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
    		$select->joinLeft('ItemLogout', 
		    	'CombinedRecords.IdentificationID = ItemLogout.IdentificationID', 
		    	$columnarray);
		    $logoutappended = TRUE;
    	}
    	//If the call numbers exists, then add the join
    	if ($callnumbersexist && !$callnumbersappended)
    	{
    		$select->joinLeft('CallNumbers', 
		    	'CombinedRecords.IdentificationID = CallNumbers.IdentificationID', 
		    	array());
		    $callnumbersappended = TRUE;
    	}
    	//If the importance exists, then add the join
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
	    		$select->joinLeft('ItemReport', 
			    	'CombinedRecords.IdentificationID = ItemReport.IdentificationID', 
			    	$columnarray);
			   	$reportappended = TRUE;
    		}
    		$select->joinLeft('ItemImportances', 
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
    		$select->joinLeft('ItemConservators', 
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
    		$select->joinLeft('TemporaryTransfers', 
		    	'CombinedRecords.RecordID = TemporaryTransfers.ItemID', 
		    	$columnarray);
		    $temptransferappended = TRUE;
    	}
    	
    	//If the osw exists, then add the join
    	if ($oswexists && !$oswappended)
    	{
    		$columnarray = array();
    		$select->joinLeft('OSW', 
		    	'CombinedRecords.IdentificationID = OSW.IdentificationID', 
		    	$columnarray);
		    $oswappended = TRUE;
    	}
    	
    	//If the worktypes item exists, then add the join
    	if ($worktypesexist && !$worktypesappended)
    	{
    		$columnarray = array();
    		$select->joinLeft('OSWWorkTypes', 
		    	'CombinedRecords.RecordID = OSWWorkTypes.OSWID', 
		    	$columnarray);
		    $worktypesappended = TRUE;
    	}
    	//If the work assigned exists, then add the join
    	if ($workassignedexists && !$workassignedappended)
    	{
    		$select->joinLeft('WorkAssignedTo', 
		    	'CombinedRecords.RecordID = WorkAssignedTo.ItemID and CombinedRecords.RecordType =\'Item\'', 
		    	array());
		    $workassignedappended = TRUE;
    	}
    	//If the file exists, then add the join
    	if ($fileexists && !$fileappended)
    	{
    		$select->joinLeft('Files', 
		    	'CombinedRecords.IdentificationID = Files.PKID and Files.LinkType !=\'Group\'', 
		    	array());
		    $fileappended = TRUE;
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
    
	public function getCurrentUserRecords()
    {
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$personid = $identity[PeopleDAO::PERSON_ID];
    	$accesslevel = $identity[PeopleDAO::ACCESS_LEVEL];
    	Logger::log("IDENTITY:");
    	Logger::log($identity);
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from($this->_name, array('*'));
    	$select->joinLeft('ItemLogout', 'CombinedRecords.IdentificationID = ItemLogout.IdentificationID',
    		array());
    	$select->joinLeft(array('R' => 'ItemReport'), 'CombinedRecords.IdentificationID = R.IdentificationID',
    		array());
    	$select->joinLeft(array('O' => 'OSW'), 'CombinedRecords.IdentificationID = O.IdentificationID',
    		array());
    	$select->joinLeft(array('A' => 'AuditTrail'), 'CombinedRecords.IdentificationID = A.PKID',
    		array());
    	$select->joinLeft('WorkAssignedTo', 'CombinedRecords.RecordID = WorkAssignedTo.ItemID AND CombinedRecords.RecordType = \'Item\'',
    		array());
    	if ($accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN)
    	{
    		$repositoryid = $identity[LocationDAO::LOCATION_ID];
    		$where = 'LogoutID IS NULL AND HomeLocationID = ' . $repositoryid;
    	}
    	else 
    	{
    		$today = date(ACORNConstants::$DATABASE_DATE_FORMAT);
    		//Current records include those that have never been logged out and that are not currently closed
    		$where = 'LogoutID IS NULL AND CombinedRecords.ManuallyClosed = 0 ';
    		$where .= ' AND (CoordinatorID=' . $personid . ' OR WorkAssignedTo.PersonID=' . $personid . ' OR CombinedRecords.CuratorID=' . $personid . ' OR A.PersonID =' . $personid;
    		$where .= ' OR (SELECT COUNT(*) FROM ItemConservators WHERE PersonID = ' . $personid . ' AND ReportID = R.ReportID) > 0)';
    	}
    	$where .= ' AND O.WorkEndDate IS NULL';
    	$select->where($where);
    	$select->order(array('RecordType DESC', 'RecordID ASC'));
    	$select->distinct();
    	Logger::log($select->__toString());
    	$rows = $this->fetchAll($select);
    	$retval = array();
    	if (!is_null($rows))
    	{
    		$rowarray = $rows->toArray();
    		
    		foreach($rowarray as $row)
    		{
    			$item = $this->buildCombinedRecord($row);
    			array_push($retval, $item);
    		}
    	}
    	return $retval;
    }
    
	public function getCurrentUserRecordIDs()
    {
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$personid = $identity[PeopleDAO::PERSON_ID];
    	
    	$select = $this->select();
    	$select->from($this, array('RecordID', 'RecordType'));
    	$select->setIntegrityCheck(FALSE);
    	$select->joinLeft('ItemLogout', 'CombinedRecords.IdentificationID = ItemLogout.IdentificationID',
    		array());
    	$select->joinLeft(array('R' => 'ItemReport'), 'CombinedRecords.IdentificationID = R.IdentificationID',
    		array());
    	$select->joinLeft(array('O' => 'OSW'), 'CombinedRecords.IdentificationID = O.IdentificationID',
    		array());
    	$select->joinLeft('WorkAssignedTo', 'CombinedRecords.RecordID = WorkAssignedTo.ItemID AND CombinedRecords.RecordType = \'Item\'',
    		array());
    	$today = date(ACORNConstants::$DATABASE_DATE_FORMAT);
    	$where = 'LogoutID IS NULL AND CombinedRecords.ManuallyClosed = 0 ';
    	$where .= ' AND (CoordinatorID=' . $personid . ' OR WorkAssignedTo.PersonID=' . $personid;
    	$where .= ' OR (SELECT COUNT(*) FROM ItemConservators WHERE PersonID = ' . $personid . ' AND ReportID = R.ReportID) > 0)';
    	$where .= ' AND O.WorkEndDate IS NULL';
    	$select->where($where);
    	$select->order('RecordID');
    	$select->distinct();
    	$rows = $this->fetchAll($select);
    	$retval = array();
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }
    
    /**
     * @access public
     * @param  Integer itemID
     */
    public function getCallNumbers($identificationID)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from('CallNumbers', array(self::CALL_NUMBER, self::CALL_NUMBER));
    	$identificationID = $this->getAdapter()->quote($identificationID, 'INTEGER');
    	$select->where('IdentificationID=' . $identificationID);
    	$rows = $this->getAdapter()->fetchPairs($select);
    	 
    	$retval = array();
    	if (!is_null($rows))
    	{
    		$retval = $rows;
    	}
    	return $retval;
    }
    
    /**
     * Determines if the given call number exists in the database for
     * an Item.
     *
     * @param $value - the string call number to search for.
     * @param $exclusionIDs OPTIONAL array - excludes the given IDs from the query.
     * @return the Identification # if the call number exists, NULL otherwise.
     */
    public function callNumberExists($value, array $exclusionIDs = NULL)
    {
    	$select = $this->select();
    	$select->from($this, array('RecordID', 'RecordType'));
    	$select->setIntegrityCheck(FALSE);
    	$select->joinInner('CallNumbers', 'CombinedRecords.IdentificationID = CallNumbers.IdentificationID',
    			array());
    	$value = $this->getAdapter()->quote($value, 'VARCHAR');
    	$select->where('CallNumber=' . $value);
    	if (!empty($exclusionIDs))
    	{
    		$exclusionstring = implode(",", $exclusionIDs);
    		if (!empty($exclusionstring))
    		{
    			$select->where('CallNumbers.IdentificationID NOT IN (' . $exclusionstring . ')');
    		}
    	}
    	Logger::log($select->__toString());
    	$row = $this->fetchRow($select);
    	if (!is_null($row))
    	{
    		if ($row->RecordType == "Item")
    		{
    			$val = "Acorn #" . $row->RecordID;
    		}
    		else
    		{
    			$val = "OSW #" . $row->RecordID;
    		}
    		return $val;
    	}
    	return NULL;
    }
} 

?>