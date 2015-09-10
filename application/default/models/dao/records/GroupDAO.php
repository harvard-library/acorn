<?php 
/**
 * GroupDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class GroupDAO extends Zend_Db_Table 
{
	const GROUP_ID = 'GroupID';
	const GROUP_NAME = 'GroupName';
	const INACTIVE = 'Inactive';
	
	/* The name of the table */
	protected $_name = "Groups";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "GroupID";

    private static $groupDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return GroupDAO
     */
    public static function getGroupDAO()
	{
		if (!isset(self::$groupDAO))
		{
			self::$groupDAO = new GroupDAO();
		}
		return self::$groupDAO;
	}
	
    /**
     * Returns a list of groups
     *
     * @access public
     * @param array (key = GroupID, value = array of all values)
     * @return array, NULL if no rows found
     */
    public function getAllGroups($includeinactive = FALSE)
    {
    	$select = $this->select();
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	$rows = $this->getAdapter()->fetchAssoc($select);
    	return $rows;
    }
    
	/**
     * Returns a list of groups formatted for a select list
     *
     * @access public
     * @return array to be used with JSON, NULL if none found
     */
    public function getAllGroupsForSelect($includeinactive = FALSE)
    {
    	$select = $this->select();
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	$select->order('GroupName');
    	$rows = $this->fetchAll($select);
    	$retval = NULL;
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }

    /**
     * Gets the group with the given id
     *
     * @access public
     * @param  Integer groupID
     * @return mixed
     */
    public function getGroup($groupID)
    {
    	$select = $this->select();
    	$groupID = $this->getAdapter()->quote($groupID, 'INTEGER');
    	$select->where('GroupID=' . $groupID);
    	$row = $this->fetchRow($select);
    	$retval = new Group();
    	if (!is_null($row))
    	{
    		$retval = $this->buildGroup($row->toArray());
    	}
    	return $retval;
    }
    
    private function buildGroup(array $values)
    {
    	$group = new Group($values[self::GROUP_ID], $values[self::INACTIVE]);
    	$group->setGroupName($values[self::GROUP_NAME]);
    	return $group;
    }

    /**
     * @access public
     * @param  Group group
     * @return groupid
     */
    public function saveGroup(Group $group)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		$groupid = $group->getPrimaryKey();
   		try {
   			$auth = Zend_Auth::getInstance();
   			$identity = $auth->getIdentity();
	   		$audittrail = new AuditTrail();
	        $zenddate = new Zend_Date();
			$audittrail->setDate($zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT));
	        $audittrail->setTableName('Groups');
	        $audittrail->setPersonID($identity[PeopleDAO::PERSON_ID]);
	        
	        $grouparray = array(self::GROUP_NAME => $group->getGroupName());
	        
	        //If the item isn't in the database,
	        //insert it
	        if (is_null($groupid) || !$this->groupExists($groupid))
	        {
	        	$db->insert('Groups', $grouparray);
	        	$groupid = $db->lastInsertId();
	        	
	        	$audittrail->setActionType(AuditTrailDAO::ACTION_INSERT);
	        }
	        //otherwise, update it
	        else 
	        {
	        	$db->update('Groups', $grouparray, 'GroupID=' . $groupid);	
	        	$audittrail->setActionType(AuditTrailDAO::ACTION_UPDATE);
	        }
	        
	        $audittrail->setPrimaryKey($groupid);
	        AuditTrailDAO::getAuditTrailDAO()->insertAuditTrailItem($audittrail, $db);
   			
   			$db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$groupid = NULL;
   		}
   		return $groupid; 
    }
    
	public function groupExists($groupID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(GroupID)'));
    	$select->where('GroupID=' . $groupID);
    	Logger::log($select->__toString());
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }
    
	public function deleteGroup(Group $group)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		try {
	   		$rowsdeleted = $db->delete($this->_name, 'GroupID=' . $group->getPrimaryKey());	
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
     * Short description of method getGroups
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array searchterms
     * @return mixed
     */
    public function getGroups($searchterms)
    {
        // section -128-103--105-22--42db858c:11ef9641268:-8000:0000000000001357 begin
        // section -128-103--105-22--42db858c:11ef9641268:-8000:0000000000001357 end
    }

}

?>