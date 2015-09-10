<?php
/**
 * AuditTrailDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class AuditTrailDAO extends Zend_Db_Table
{
	const AUDIT_TRAIL_ID = 'AuditTrailID';
	const ACTION_TYPE = 'ActionType';
	const DATE = 'Date';
	const DETAILS = 'Details';
	const TABLE_NAME = 'TableName';
	const PKID = "PKID";
	
	const ACTION_INSERT = "Insert";
	const ACTION_UPDATE = "Update";
	const ACTION_DELETE = "Delete";
	
	/* The name of the table */
	protected $_name = "AuditTrail";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "AuditTrailID";

    private static $audittrailDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return AuditTrailDAO
     */
    public static function getAuditTrailDAO()
	{
		if (!isset(self::$audittrailDAO))
		{
			self::$audittrailDAO = new AuditTrailDAO();
		}
		return self::$audittrailDAO;
	}
	
	
    public function getAuditTrailItems($tablename, $pkid, $actiontype = NULL)
    {
    	$select = $this->select();
    	$select->from($this, array(self::AUDIT_TRAIL_ID, self::ACTION_TYPE, self::DETAILS, self::PKID, self::TABLE_NAME, self::DATE => "DATE_FORMAT(" . self::DATE . ", '%m-%d-%Y')"));
    	$select->setIntegrityCheck(FALSE);
    	$select->joinInner("People", "AuditTrail.PersonID=People.PersonID", array("PersonID", "DisplayName"));
    	$tablename = $this->getAdapter()->quote($tablename);
    	$pkid = $this->getAdapter()->quote($pkid, "INTEGER");
    	$select->where("TableName=" . $tablename . " AND PKID=" . $pkid);
    	if (!is_null($actiontype))
    	{
    		$actiontype = $this->getAdapter()->quote($actiontype);
    		$select->where("ActionType=" . $actiontype);
    	}
    	$rows = $this->fetchAll($select);
    	$retval = array();
    	if (!is_null($rows))
    	{
    		$rowarray = $rows->toArray();
    		foreach ($rowarray as $row)
    		{
    			$audittrail = $this->buildAuditTrail($row);
    			$retval[$audittrail->getPrimaryKey()] = $audittrail;
    		}
    	}
    	return $retval;
    }
    
    private function buildAuditTrail($data)
    {
    	$audittrail = new AuditTrail($data[self::AUDIT_TRAIL_ID]);
    	$audittrail->setActionType($data[self::ACTION_TYPE]);
    	$audittrail->setDate($data[self::DATE]);
    	$audittrail->setDetails($data[self::DETAILS]);
    	$audittrail->setObjectPKID($data[self::PKID]);
    	$audittrail->setTableName($data[self::TABLE_NAME]);
    	$person = new Person($data[PeopleDAO::PERSON_ID]);
    	$person->setDisplayName($data[PeopleDAO::DISPLAY_NAME]);
    	$audittrail->setPerson($person);
    	return $audittrail;
    }
    
    public function insertAuditTrailItem(AuditTrail $auditTrail, $db)
    {
    	$insertarray = array(
    		self::ACTION_TYPE => $auditTrail->getActionType(),
    		self::DATE => $auditTrail->getDate(),
    		self::DETAILS => $auditTrail->getDetails(),
    		self::PKID => $auditTrail->getPrimaryKey(),
    		self::TABLE_NAME => $auditTrail->getTableName(),
    		PeopleDAO::PERSON_ID => $auditTrail->getPersonID()
    	);
    	
    	$db->insert($this->_name, $insertarray);
    }
} 

?>