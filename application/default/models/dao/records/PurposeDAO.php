<?php
/**
 * PurposeDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class PurposeDAO extends Zend_Db_Table
{
	const PURPOSE_ID = 'PurposeID';
	const PURPOSE = 'Purpose';
	const INACTIVE = 'Inactive';
	
    /* The name of the table */
	protected $_name = "Purposes";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "PurposeID";

    private static $purposeDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return PurposeDAO
     */
    public static function getPurposeDAO()
	{
		if (!isset(self::$purposeDAO))
		{
			self::$purposeDAO = new PurposeDAO();
		}
		return self::$purposeDAO;
	}

	/**
     * Returns a list of purposes
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return array, NULL if no rows found  (key = PurposeID, array of all inpurposeion)
     */
    public function getAllPurposes($includeinactive = FALSE)
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
     * Returns a list of purpose formatted for a select list
     *
     * @access public
     * @return array to be used with JSON, NULL if none found
     */
    public function getAllPurposesForSelect($includeinactive = FALSE)
    {
    	$select = $this->select();
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	$select->order('Purpose');
    	$rows = $this->fetchAll($select);
    	$retval = NULL;
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }

    /**
     * @access public
     * @param  Integer purposeID
     * @return Purpose purpose
     */
    public function getPurpose($purposeID)
    {
        $select = $this->select();
    	$purposeID = $this->getAdapter()->quote($purposeID, 'INTEGER');
    	$select->where('PurposeID=' . $purposeID);
    	$row = $this->fetchRow($select);
    	$retval = NULL;
    	if (!is_null($row))
    	{
    		$retval = $this->buildPurpose($row->toArray());
    	}
    	return $retval;
    }

    private function buildPurpose(array $data)
    {
    	$purpose = new Purpose($data[self::PURPOSE_ID], $data[self::INACTIVE]);
    	$purpose->setPurpose($data[self::PURPOSE]);
    	return $purpose;
    }

    /**
     * @access public
     * @param  Purpose purpose
     * @return mixed
     */
    public function savePurpose(Purpose $purpose)
    {
        $array = array(
        	self::PURPOSE => $purpose->getPurpose(),
        	self::INACTIVE => $purpose->isInactive()
        );
        $db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try {
	   		$purposeid = $purpose->getPrimaryKey();
	        if (is_null($purposeid) || !$this->purposeExists($purposeid))
	        {
	        	$db->insert('Purposes', $array);
	        	$purposeid = $db->lastInsertId();
	        }
	        else 
	        {
	        	$db->update('Purposes', $array, 'PurposeID=' . $purposeid);
	        }
	        $db->commit();
	   	}
    	catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$purposeid = NULL;
   		}
        return $purposeid;
    }
    
	private function purposeExists($purposeID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(PurposeID)'));
    	$select->where('PurposeID=' . $purposeID);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }

}

?>