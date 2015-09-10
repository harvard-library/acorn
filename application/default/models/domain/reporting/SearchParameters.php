<?php

class SearchParameters
{
	const SEARCH_TYPE_EQUALS = 'Equals';
	const SEARCH_TYPE_BEGINS_WITH = 'Begins With';
	const SEARCH_TYPE_CONTAINS = 'Contains';
	const SEARCH_TYPE_LESS_THAN = '<';
	const SEARCH_TYPE_GREATER_THAN = '>';
	
	const SEARCH_RECORD_TYPE = 'Record';
	const SEARCH_OSW_TYPE = 'OSW';
	const SEARCH_ITEM_TYPE = 'Item';
	
	private $columnname;
	private $value;
	private $booleanValue = 'AND';
	private $searchType = 'Equals';
	private $openparen = '';
	private $closeparen = '';
	private $table;
	private $valuename;
	private $columntext;
	private $searchRecordType;
	private $zeroOrBlankSearch = FALSE;
	private $columnOrder;
	
	
	
	/**
	 * @return mixed
	 */
	public function isZeroOrBlankSearch()
	{
		return $this->zeroOrBlankSearch;
	}
	
	/**
	 * @param mixed zeroOrBlankSearch
	 */
	public function setZeroOrBlankSearch($zeroOrBlankSearch)
	{
		$this->zeroOrBlankSearch = $zeroOrBlankSearch;
	}

	
	public function getTableName()
	{
		return $this->table;
	}
	
	public function getValueName()
	{
		return $this->valuename;
	}
	
	public function getColumnText()
	{
		return $this->columntext;
	}
	
	public function getColumnName()
	{
		return $this->columnname;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	public function getBooleanValue()
	{
		return $this->booleanValue;
	}
	
	public function getSearchType()
	{
		return $this->searchType;
	}
	
	public function getOpenParenthesis()
	{
		return $this->openparen;
	}
	
	public function getClosedParenthesis()
	{
		return $this->closeparen;
	}
	
	public function setOpenParenthesis($openparen)
	{
		$this->openparen = $openparen;
	}
	
	public function setClosedParenthesis($closedparen)
	{
		$this->closeparen = $closedparen;
	}
	
	public function setTableName($table)
	{
		$this->table = $table;
	}
	
	public function setValueName($valuename)
	{
		$this->valuename = $valuename;
	}
	
	public function setColumnText($columntext)
	{
		$this->columntext = $columntext;
	}
	
	public function setColumnName($columnname)
	{
		$this->columnname = $columnname;
	}
	
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	public function setBooleanValue($booleanValue)
	{
		$this->booleanValue = $booleanValue;
	}
	
	public function setSearchType($searchType)
	{
		$this->searchType = $searchType;
	}
	
	public function getSearchRecordType()
	{
		$column = $this->getColumnName();
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
    	
    	$searchrectype = self::SEARCH_RECORD_TYPE;
		if ($columncompare == 'ItemStatus' || $tablename == 'ItemLogin' || $tablename == 'ItemProposal'
    			|| $tablename == 'ItemLogout' || $tablename == 'ItemImportances'
    			|| $tablename == 'Items'|| ($columncompare == 'Activity' && $this->getValue() != 'On-site'))
    	{
    		$searchrectype = self::SEARCH_ITEM_TYPE;
    	}
    	elseif ($tablename == 'OSWWorkTypes' || $columncompare == 'OSWStatus' || ($columncompare == 'Activity' && $this->getValue() == 'On-site'))
    	{
    		$searchrectype = self::SEARCH_OSW_TYPE;
    	}
    	return $searchrectype;
	}
	
	public function getStringSearchSummary()
	{
		$colname = $this->getColumnText();
		if (strpos($colname, ".") > 0)
		{
			$colname = substr($colname, strpos($colname, ".")+1);
		}
		$summary = $colname . ' ' . $this->getSearchType() . ' ' . $this->getValueName();
		return $summary;
	}
} 

?>