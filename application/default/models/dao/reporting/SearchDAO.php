<?php
/**
 * SearchDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class SearchDAO extends Zend_Db_Table
{
	const SEARCH_ID = 'SearchID';
	const SEARCH_NAME = 'SearchName';
	const FILE_NAME = 'FileName';
	const IS_GLOBAL = 'IsGlobal';
	
    /* The name of the table */
	protected $_name = "SavedSearches";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "SearchID";

    private static $searchDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return SearchDAO
     */
    public static function getSearchDAO()
	{
		if (!isset(self::$searchDAO))
		{
			self::$searchDAO = new SearchDAO();
		}
		return self::$searchDAO;
	}

	/**
     * Returns a list of searches formatted for a select list
     *
     * @access public
     * @param personid the current user
     * @return JSON ready array
     */
    public function getAllSearchesForSelect($personID, $includeall = FALSE, $includeglobal = TRUE)
    {
    	$select = $this->select();
    	$select->from($this, array('*'));
    	$select->setIntegrityCheck(FALSE);
    	$select->joinLeft('People', 'SavedSearches.PersonID = People.PersonID', array('DisplayName'));
    	
    	//If we don't want to include all, then get only the current person.
		if (!$includeall)
		{
			$personID = $this->getAdapter()->quote($personID, 'INTEGER');
			if ($includeglobal)
			{
        		$where =  'SavedSearches.PersonID = ' . $personID . ' OR SavedSearches.IsGlobal = 1';
			}
			else 
			{
				$where = 'SavedSearches.PersonID = ' . $personID;
			}
			$select->where($where);
    	}
    	$select->order(array('DisplayName', self::SEARCH_NAME));
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
     * @access public
     * @param  searchID
     * @return mixed
     */
    public function deleteSearch($searchID)
    {
    	$db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try {
	   		$rowsdeleted = $db->delete('SavedSearches', 'SearchID=' . $searchID);	
	        $db->commit();
	   	}
    	catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$rowsdeleted = NULL;
   		}
        return $rowsdeleted;
    }
    
	/**
     * Returns a search for the selected caption
     *
     * @access public
     * @param int searchid the searchid for the selected caption
     * @return SavedSearch
     */
    public function getSavedSearchFromID($searchid)
    {
    	$select = $this->select();
    	$searchid = $this->getAdapter()->quote($searchid, 'INTEGER');

        $select->where('SearchID=' . $searchid);
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$row = $this->fetchRow($select);
    	$retval = NULL;
    	if (!is_null($row))
    	{
    		$retval = $this->buildSavedSearch($row->toArray());
    	}
    	return $retval;
    }
    
    public function searchnameExists($searchName)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(SearchID)'));
    	$searchName = $this->getAdapter()->quote($searchName);
    	$select->where('SearchName=' . $searchName);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }
    
    private function buildSavedSearch(array $data)
    {
    	$search = new SavedSearch();
    	$search->setSearchID($data[self::SEARCH_ID]);
    	$search->setSearchName($data[self::SEARCH_NAME]);
    	$search->setGlobal($data[self::IS_GLOBAL]);
    	$search->setOwner($data[PeopleDAO::PERSON_ID]);
    	$search->setFileName($data[self::FILE_NAME]);
    	return $search;
    }

    /**
     * @access public
     * @param  SavedSearch search
     * @return mixed
     */
    public function saveSearch(SavedSearch $search)
    {
    	$array = array(
        	self::SEARCH_NAME => $search->getSearchName(),
        	self::FILE_NAME => $search->getFileName(),
        	PeopleDAO::PERSON_ID => $search->getOwner(),
        	self::IS_GLOBAL => $search->isGlobal()
        );
        $db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try {
	   		$searchid = $search->getSearchID();
	        if (is_null($searchid) || !$this->searchExists($searchid))
	        {
	        	$db->insert('SavedSearches', $array);
	        	$searchid = $db->lastInsertId();
	        }
	        else 
	        {
	        	$db->update('SavedSearches', $array, 'SearchID=' . $searchid);
	        }
	        $db->commit();
	   	}
    	catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$searchid = NULL;
   		}
        return $searchid;
    }
    
	private function searchExists($searchID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(SearchID)'));
    	$select->where('SearchID=' . $searchID);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }
} 

?>