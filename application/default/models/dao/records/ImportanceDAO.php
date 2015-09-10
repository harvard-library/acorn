<?php

/**
 * ImportanceDAO
 *
 * @author vcrema
 * Created April 16, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class ImportanceDAO extends Zend_Db_Table
{
	const IMPORTANCE_ID = "ImportanceID";
	const IMPORTANCE = "Importance";
	const INACTIVE = "Inactive";
	
    /* The name of the table */
	protected $_name = "Importances";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "ImportanceID";

    private static $importanceDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return ImportanceDAO
     */
    public static function getImportanceDAO()
	{
		if (!isset(self::$importanceDAO))
		{
			self::$importanceDAO = new ImportanceDAO();
		}
		return self::$importanceDAO;
	}

	/**
     * Returns a list of importances
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return array, NULL if no rows found  (key = ImportanceID, array of other values)
     */
    public function getAllImportances($includeinactive = FALSE)
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
     * Returns a list of importances formatted for a select list
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return JSON ready array
     */
    public function getAllImportancesForSelect($includeinactive = FALSE)
    {
    	$select = $this->select();
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	$select->order('Importance');
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
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
     * @param  Integer workTypeID
     * @return Importance
     */
    public function getImportance($importanceid)
    {
		$select = $this->select();
		$importanceid = $this->getAdapter()->quote($importanceid, 'INTEGER');
	    $select->where('ImportanceID=' . $importanceid);
	    $row = $this->fetchRow($select);
	    $retval = NULL;
	    if (!is_null($row))
	    {
	    	$retval = $this->buildImportance($row->toArray());
	    }
	    return $retval;
    }

    /**
     * @access public
     * @param  Importance importance
     * @return mixed
     */
    public function saveImportance(Importance $importance)
    {
        $array = array(
        	self::IMPORTANCE => $importance->getImportance(),
        	self::INACTIVE => $importance->isInactive()
        );
        $db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try {
	   		$importanceid = $importance->getPrimaryKey();
	        if (is_null($importanceid) || !$this->importanceExists($importanceid))
	        {
	        	$db->insert('Importances', $array);
	        	$importanceid = $db->lastInsertId();
	        }
	        else 
	        {
	        	$db->update('Importances', $array, 'ImportanceID=' . $importanceid);
	        }
	        $db->commit();
	   	}
    	catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$importanceid = NULL;
   		}
        return $importanceid;
    }
    
	private function importanceExists($importanceID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(ImportanceID)'));
    	$select->where('ImportanceID=' . $importanceID);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }

	/**
     * Returns the importances with the given reportid.
     *
     * @access public
     * @param  Integer importanceID
     * @return array of Importance objects
     */
    public function getImportances($reportID)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from($this, array('*'));
    	$select->joinInner('ItemImportances', 'Importances.ImportanceID = ItemImportances.ImportanceID', array());
    	$reportID = $this->getAdapter()->quote($reportID, 'INTEGER');
	    $select->where('ReportID=' . $reportID);
	    $rows = $this->fetchAll($select);
    	$importancearray = array();
	    if (!is_null($rows))
	    {
	    	$rowarray = $rows->toArray();
	    	foreach ($rowarray as $row)
	    	{
	    		$importance = $this->buildImportance($row);
	    		$importancearray[$importance->getPrimaryKey()] = $importance;
	    	}
	    }
	    return $importancearray;
    }
    
    private function buildImportance(array $data)
    {
    	$importance = new Importance($data[self::IMPORTANCE_ID], $data[self::INACTIVE]);
	   	$importance->setImportance($data[self::IMPORTANCE]);
	   	return $importance;
	    		
    }
} 

?>