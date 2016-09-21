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
 * ProposalApprovalDAO
 *
 * @author vcrema
 * Created September 30, 2009
 *
 */
class ProposalApprovalDAO extends Zend_Db_Table
{
	const HISTORY_ID = 'HistoryID';
	const PKID = 'PKID';
	const RECORD_TYPE = 'RecordType';
	const ACTIVITY_TYPE = 'ActivityType';
	const DETAILS = 'Details';
	const AUTHOR_ID = 'AuthorID';
	const DATE_ENTERED = 'DateEntered';
	
	const RECORD_TYPE_ITEM = 'Item';
	const RECORD_TYPE_GROUP = 'Group';
	
	const APPROVAL_TYPE_APPROVE = 'Approved';
	const APPROVAL_TYPE_DENY = 'Denied';
	const APPROVAL_TYPE_AMEND = 'Added Comment';
	
	/* The name of the table */
	protected $_name = "ProposalApprovalHistory";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "HistoryID";

    private static $proposalApproval;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return ProposalApprovalDAO
     */
    public static function getProposalApprovalDAO()
	{
		if (!isset(self::$proposalApproval))
		{
			self::$proposalApproval = new ProposalApprovalDAO();
		}
		return self::$proposalApproval;
	}
	
	
    /**
     * Returns the approval with the given id.
     *
     * @access public
     * @param  Integer PKID
     * @param  mixed recordType (Item or Group)
     * @return ProposalApproval
     */
    public function getProposalApproval($PKID, $recordType = 'Item')
    {
    	$proposalapproval = new ProposalApproval();
	    if (!empty($PKID))
    	{
	    	$PKID = $this->getAdapter()->quote($PKID, 'INTEGER');
	    	$recordType = $this->getAdapter()->quote($recordType, 'VARCHAR');
	    	$select = $this->select();
	    	$select->where('PKID=' . $PKID);
	    	$select->where('RecordType=' . $recordType);
	    	$select->order(array('DateEntered DESC', 'HistoryID DESC'));
	    	$rows = $this->fetchAll($select);
	    	if (!is_null($rows))
	    	{
	    		$rowarray = $rows->toArray();
	    		foreach ($rowarray as $row)
	    		{
	    			$history = $this->buildHistoryItem($row);
	    			$proposalapproval->addHistoryItem($history);
	    		}
	    	}
    	}
    	return $proposalapproval;
    }
    
	/**
     * Returns the item proposal with the given id.
     *
     * @access public
     * @param  Integer historyID
     * @return ProposalApprovalHistoryItem
     */
    public function getHistoryItem($historyid)
    {
    	$historyitem = NULL;
	    $historyid = $this->getAdapter()->quote($historyid, 'INTEGER');
    	$select = $this->select();
    	$select->where('HistoryID=' . $historyid);
    	$row = $this->fetchRow($select);
    	if (!is_null($row))
    	{
    		$historyitem = $this->buildHistoryItem($row->toArray());
    	}

    	return $historyitem;
    }
    
    /*
     * @access private
     * @param  array values from the database
     * @return ProposalApprovalHistoryItem
     */
    private function buildHistoryItem(array $values)
    {
    	$historyitem = new ProposalApprovalHistoryItem($values[self::HISTORY_ID]);
    	$historyitem->setActivityType($values[self::ACTIVITY_TYPE]);
    	$author = PeopleDAO::getPeopleDAO()->getPerson($values[self::AUTHOR_ID]);
    	$historyitem->setAuthor($author);
    	$historyitem->setDetails($values[self::DETAILS]);
    	$historyitem->setHistoryDate($values[self::DATE_ENTERED]);
    	return $historyitem;
    }
    
	
	/**
     * Saves the ProposalApprovalHistoryItem information
     * @access public
     * @param  int PKID
     * @param  ProposalApprovalHistoryItem item
     * @param  mixed recordType (Item or Group)
     * 
     * @return int - the history id.
     */
    public function saveHistoryItem($PKID, ProposalApprovalHistoryItem $item, $recordType = 'Item')
    {
    	if ($recordType != 'Item' && $recordType != 'Group')
    	{
    		throw new Exception('Record Type must be either Item or Group');
    	}
    	//Build the array to save
        $historyarray = $this->buildHistoryArray($PKID, $item, $recordType);
       	$db = $this->getAdapter();
   		$db->beginTransaction();
   		try {
	   		$db->insert($this->_name, $historyarray);
	        $historyid = $db->lastInsertId();
	        
	        $db->commit();
   		}
	   	catch (Exception $e)
	   	{
	   		Logger::log($e->getMessage(), Zend_Log::ERR);
	   		$db->rollBack();
	   		$historyid = NULL;
	   	}
        
   		return $historyid; 
    }

	/*
     * Data to be loaded into the History table
     * @param  int PKID
     * @param  ProposalApprovalHistoryItem item
     * @param  mixed recordType (Item or Group)
     * 
     * @return array
     */
    private function buildHistoryArray($PKID, ProposalApprovalHistoryItem $item, $recordType)
    {
    	$zenddate = new Zend_Date($item->getHistoryDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$date = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	
    	$array = array(
    		self::ACTIVITY_TYPE => $item->getActivityType(),
    		self::AUTHOR_ID => $item->getAuthor()->getPrimaryKey(),
    		self::DATE_ENTERED => $date,
    		self::DETAILS => $item->getDetails(),
    		self::RECORD_TYPE => $recordType,
    		self::PKID => $PKID
    	);
    	return $array;
    }
    
	/**
     * Returns the approval status
     *
     * @access public
     * @param  Integer PKID
     * @param  mixed recordType (Item or Group)
     * @return mixed the status
     */
    public function getProposalApprovalStatus($PKID, $recordType = 'Item')
    {
    	$status = NULL;
	    if (!empty($PKID))
    	{
	    	$PKID = $this->getAdapter()->quote($PKID, 'INTEGER');
	    	$recordType = $this->getAdapter()->quote($recordType, 'VARCHAR');
	    	$select = $this->select();
	    	$select->where('PKID=' . $PKID);
	    	$select->where('RecordType=' . $recordType);
	    	$select->where(self::ACTIVITY_TYPE . ' IN (\'' . self::APPROVAL_TYPE_APPROVE . '\',\'' . self::APPROVAL_TYPE_DENY . '\')');
	    	$select->order(array('DateEntered DESC', 'HistoryID DESC'));
	    	$select->limit(1);
	    	Logger::log($select->__toString());
		    $row = $this->fetchRow($select);
	    	
	    	if (is_null($row))
	    	{
	    		$select = $this->select();
	    		$select->from($this, array('Count' => 'COUNT(*)'));
		    	$select->where('PKID=' . $PKID);
		    	$select->where('RecordType=' . $recordType);
		    	Logger::log($select->__toString());
		    	$row = $this->fetchRow($select);
		    	if ($row->Count > 0)
		    	{
		    		$status = 'Pending';
		    	}
	    	}
	    	elseif ($row->ActivityType == self::APPROVAL_TYPE_APPROVE)
	    	{
	    		$status = 'Approved';
	    	}
    		elseif ($row->ActivityType == self::APPROVAL_TYPE_DENY)
	    	{
	    		$status = 'Denied';
	    	}
	    	else 
	    	{
	    		$status = 'Pending';
	    	}
    	}
    	return $status;
    }
    
	/**
     * Returns the date that the proposal was approved
     *
     * @access public
     * @param  Integer PKID
     * @param  mixed recordType (Item or Group)
     * @return mixed the status
     */
    public function getProposalApprovalDate($PKID, $recordType = 'Item')
    {
    	$approvaldate = NULL;
	    if (!empty($PKID))
    	{
	    	$PKID = $this->getAdapter()->quote($PKID, 'INTEGER');
	    	$recordType = $this->getAdapter()->quote($recordType, 'VARCHAR');
	    	$select = $this->select();
	    	$select->where('PKID=' . $PKID);
	    	$select->where('RecordType=' . $recordType);
	    	$select->where(self::ACTIVITY_TYPE . ' = \'' . self::APPROVAL_TYPE_APPROVE . '\'');
	    	$select->order(array('DateEntered DESC', 'HistoryID DESC'));
	    	$select->limit(1);
	    	Logger::log($select->__toString());
		    $row = $this->fetchRow($select);
	    	
	    	if (!is_null($row))
	    	{
	    		$zenddate = new Zend_Date($row->DateEntered, ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    			$approvaldate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    		}
	    	
    	}
    	return $approvaldate;
    }
} 

?>