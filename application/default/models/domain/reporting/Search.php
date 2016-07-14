<?php

class Search
{
    private $searchParameters = NULL;
    private $visibleColumns = NULL;
	private $hiddenColumns = NULL;
	private $orderby = NULL;
	

	public function __construct()
    {
    	//Default columns
    	$this->visibleColumns = array(
    		'Type' => 'Type',
    		'RecordID' => 'RecordID',
    		'Status' => 'Status',
    		'CallNumbers' => 'CallNumbers',
    		'Title' => 'Title',
    		'AuthorArtist' => 'AuthorArtist',
    		'Repository' => 'Repository',
    		'ProjectName' => 'ProjectName',
    		'ReportHours' => 'ReportHours',
    		'LoginDate' => 'LoginDate',
    		'LogoutDate' => 'LogoutDate',
    		'Coordinator' => 'Coordinator',
    		'WorkAssignedTo' => 'WorkAssignedTo',
    		'WorkDoneBy' => 'WorkDoneBy'
    	);
    	$this->hiddenColumns = array(
    		'Activity' => 'Activity',
    		'BoxCount' => 'BoxCount',
    		'ChargeToID' => 'ChargeToID',
    		'CollectionName' => 'CollectionName',
    		'Curator' => 'Curator',
    		'DepartmentName' => 'DepartmentName',
    		'Importances' => 'Importances',
    		'ItemCount' => 'ItemCount',
    		'DateOfObject' => 'DateOfObject',
    		'Format' => 'Format',
    		'GroupName' => 'GroupName',
    		'HousingCount' => 'HousingCount',
    		'InsuranceValue' => 'InsuranceValue',
    		'OSWStatus' => 'OSWStatus',
    		'PhotoCount' => 'PhotoCount',
    		'ProposedBy' => 'ProposedBy',
    		'ProposalDate' => 'ProposalDate',
    		'ProposedHours' => 'ProposedHours',
    		'Purpose' => 'Purpose',
    		'ReportBy' => 'ReportBy',
    		'ReportDate' => 'ReportDate',
    		'SheetCount' => 'SheetCount',
    		'Storage' => 'Storage',
    		'Summary' => 'Summary',
    		'TemporaryToLocation' => 'TemporaryToLocation',
    		'TemporaryDate' => 'TemporaryDate',
    		'TUB' => 'TUB',
    		'VolumeCount' => 'VolumeCount',	
    		'WorkStartDate' => 'WorkStartDate',
    		'WorkEndDate' => 'WorkEndDate',
    		'WorkLocation' => 'WorkLocation',
    		'WorkType' => 'WorkType'
    	);
    	$this->orderby = array('RecordID' => 'RecordID');
    }
    
    /**
     * @access public
     * @return array
     */
    public function getSearchParameters()
    {
    	return $this->searchParameters;
    }
    
    public function getSearchRecordType()
    {
    	if (!is_null($this->searchParameters))
    	{
    		foreach ($this->searchParameters as $param)
    		{
    			if ($param->getSearchRecordType() != SearchParameters::SEARCH_RECORD_TYPE)
    			{
    				return $param->getSearchRecordType();
    			}
    		}
    	}
    	return SearchParameters::SEARCH_RECORD_TYPE;
    }

    /**
     * @access public
     * @param  array searchParameters
     */
    public function setSearchParameters(array $searchParameters)
    {
    	$this->searchParameters = $searchParameters;
    }

	/**
     * @access public
     * @return array
     */
    public function getVisibleColumns()
    {
    	return $this->visibleColumns;
    }

    /**
     * @access public
     * @param  array visibleColumns
     */
    public function setVisibleColumns(array $visibleColumns)
    {
    	$this->visibleColumns = $visibleColumns;
    }
    
    /*
     * Determines if a column is visible.
     */
    public function isVisible($column)
    {
    	return in_array($column, $this->getVisibleColumns());
    }
    
    public function addVisibleColumn($column)
    {
    	$this->visibleColumns[$column] = $column;
    }
    
    public function removeVisibleColumn($column)
    {
    	unset($this->visibleColumns[$column]);
    }
    
/**
     * @access public
     * @return array
     */
    public function getOrderBy()
    {
    	return $this->orderby;
    }

    /**
     * @access public
     * @param  array visibleColumns
     */
    public function setOrderBy(array $order)
    {
    	$this->orderby = $order;
    }
    
    public function addOrderBy($column, $order = 'ASC')
    {
    	Logger::log('orderby: ' . $column);
    	$this->orderby[$column] = $column . ' ' . $order;
    }
    
    public function removeOrderBy($column)
    {
    	unset($this->orderby[$column]);
    }
    
	/**
     * @access public
     * @return array
     */
    public function getHiddenColumns()
    {
    	return $this->hiddenColumns;
    }

    /**
     * @access public
     * @param  array hiddenColumns
     */
    public function setHiddenColumns(array $hiddenColumns)
    {
    	$this->hiddenColumns = $hiddenColumns;
    }
    
    /*
     * Determines if a column is hidden.
     */
    public function isHidden($column)
    {
    	return in_array($column, $this->getHiddenColumns());
    }
    
    public function addHiddenColumn($column)
    {
    	$this->hiddenColumns[$column] = $column;
    }
    
    public function removeHiddenColumn($column)
    {
    	unset($this->hiddenColumns[$column]);
    }
    
	protected function buildXMLDoc()
    {
    	$doc = new DOMDocument();
		$doc->formatOutput = true;
		
    	//The root node is called 'searches'
		$root = $doc->createElement("search");
		$doc->appendChild($root);
		
		$params = $this->getSearchParameters();
		if (!is_null($params))
		{
	    	foreach($params as $param)
			{
				//Each parameter is called a 'searchclause' in the xml file
				$paramnode = $doc->createElement("searchclause");
			
				//Attach all of the attributes to the search clause.
				$openparennode = $doc->createElement("openparen");
				$openparennode->appendChild(
					$doc->createTextNode($param->getOpenParenthesis())
				);
				$paramnode->appendChild($openparennode);
				
				$booltypenode = $doc->createElement("booltype");
				$booltypenode->appendChild(
					$doc->createTextNode($param->getBooleanValue())
				);
				$paramnode->appendChild($booltypenode);
				
				$colnamenode = $doc->createElement("colname");
				$colnamenode->appendChild(
					$doc->createTextNode($param->getColumnText())
				);
				$paramnode->appendChild($colnamenode);
				
				$colidnode = $doc->createElement("ColumnID");
				$colidnode->appendChild(
					$doc->createTextNode($param->getColumnName())
				);
				$paramnode->appendChild($colidnode);
				
				$searchtypenode = $doc->createElement("searchtype");
				$searchtypenode->appendChild(
					$doc->createTextNode($param->getSearchType())
				);
				$paramnode->appendChild($searchtypenode);
	
				$selectedvaluenode = $doc->createElement("SelectedValue");
				$selectedvaluenode->appendChild(
					$doc->createTextNode($param->getValue())
				);
				$paramnode->appendChild($selectedvaluenode);
				
				$selectedvaluenamenode = $doc->createElement("val");
				$selectedvaluenamenode->appendChild(
					$doc->createTextNode($param->getValueName())
				);
				$paramnode->appendChild($selectedvaluenamenode);
				
				$closeparennode = $doc->createElement("closeparen");
				$closeparennode->appendChild(
					$doc->createTextNode($param->getClosedParenthesis())
				);
				$paramnode->appendChild($closeparennode);
				
				//Attach the searchclause node to the root node
				$root->appendChild($paramnode);
			}
		}
		
		//Add visible fields
		$visible = $this->getVisibleColumns();
		$visiblenode = $doc->createElement("visiblecolumns");
		foreach ($visible as $col)
		{
			$columnnode = $doc->createElement("visiblecolumn");
			$columnnode->appendChild(
				$doc->createTextNode($col)
			);
			$visiblenode->appendChild($columnnode);	
		}
		//Attach the visible columns node to the root node
		$root->appendChild($visiblenode);
		
		//Add hidden fields
		$hidden = $this->getHiddenColumns();
		$hiddennode = $doc->createElement("hiddencolumns");
		foreach ($hidden as $col)
		{
			$columnnode = $doc->createElement("hiddencolumn");
			$columnnode->appendChild(
				$doc->createTextNode($col)
			);
			$hiddennode->appendChild($columnnode);	
		}
		//Attach the hidden columns node to the root node
		$root->appendChild($hiddennode);
		
		//Add order by fields
		$orderby = $this->getOrderBy();
		$orderbynode = $doc->createElement("orderby");
		foreach ($orderby as $order)
		{
			$ordernode = $doc->createElement("orderbycolumn");
			$ordernode->appendChild(
				$doc->createTextNode($order)
			);
			$orderbynode->appendChild($ordernode);	
		}
		//Attach the order by columns node to the root node
		$root->appendChild($orderbynode);
		
		return $doc;
    }
    
    public function getXMLParameters()
    {
    	$doc = $this->buildXMLDoc();
		return $doc->saveXML();
    }
    
	protected function buildJSON()
    {
    	$searchparameters = array();
    	$params = $this->getSearchParameters();
		if (!is_null($params))
		{
	    	foreach($params as $param)
			{
				array_push($searchparameters, array(
					'openparen' => $param->getOpenParenthesis(),
					'booltype' => $param->getBooleanValue(),
					'colname' => $param->getColumnText(),
					'ColumnID' => $param->getColumnName(),
					'searchtype' => $param->getSearchType(),
					'SelectedValue' => $param->getValue(),
					'val' => $param->getValueName(),
					'closeparen' => $param->getClosedParenthesis()
				));
			}
		}
		//Now add the blank row
		array_push($searchparameters, array(
			'openparen' => '',
			'booltype' => 'AND',
			'colname' => '',
			'ColumnID' => '',
			'searchtype' => 'Equals',
			'SelectedValue' => '',
			'val' => '',
			'closeparen' => ''
		));
		return Zend_Json::encode(array('Result' => $searchparameters));
    }
    
    public function getJSONParameters()
    {
    	$jsonparams = $this->buildJSON();
		return $jsonparams;
    }
    
    public function getSearchName()
    {
    	return 'Search Results';
    }
    
	public function getSearchInfo()
    {
    	$params = $this->getSearchParameters();
    	$info = array();
    	foreach($params as $param)
    	{
    		array_push($info, $param->getStringSearchSummary());	
    	}
    	return implode('; ', $info);
    }
} 

?>