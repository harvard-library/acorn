<?php

class SavedSearch extends Search
{
    private $searchID = NULL;
	private $searchName = NULL;
	private $fileName = NULL;
	private $isGlobal = FALSE;
	private $ownerID = NULL;
	
    /**
     * @access public
     * @return mixed
     */
    public function getSearchID()
    {
    	return $this->searchID;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getSearchName()
    {
    	return $this->searchName;
    }
    
	/**
	 * Generally, the file name is the search name without any spaces or punctuation with .xml at the end.
	 * It is the name of the xml file stored on the server.
	 * 
     * @access public
     * @return mixed
     */
    public function getFileName()
    {
    	if (is_null($this->fileName) && !is_null($this->searchName))
    	{
    		//Strip all non-alpha numeric characters.
			$this->fileName = preg_replace("/[^A-Za-z0-9]/", "", $this->searchName);
			$this->fileName .= ".xml";	
    	}
    	return $this->fileName;
    }

    /**
     * @access public
     * @return mixed
     */
    public function isGlobal()
    {
    	return $this->isGlobal;
    }
    
	/**
     * @access public
     * @return mixed
     */
    public function getOwner()
    {
    	return $this->ownerID;
    }

    /**
     * @access public
     * @param  Integer searchID
     */
    public function setSearchID($searchID)
    {
    	$this->searchID = $searchID;
    }

    /**
     * @access public
     * @param  String searchName
     */
    public function setSearchName($searchName)
    {
    	$this->searchName = $searchName;
    }
    
	/**
     * @access public
     * @param  String fileName
     */
    public function setFileName($fileName)
    {
    	$this->fileName = $fileName;
    }

    /**
     * @access public
     * @param  Boolean isGlobal
     */
    public function setGlobal($isGlobal)
    {
    	$this->isGlobal = $isGlobal;
    }
    
	/**
     * @access public
     * @param  int ownerid
     */
    public function setOwner($ownerID)
    {
    	$this->ownerID = $ownerID;
    }
    
    public static function getSavedSearchesDirectory($owner)
    {
    	//$fc = Zend_Controller_Front::getInstance();
        //$initializer = $fc->getPlugin('Initializer');
        $config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
        return $config->getReportsDirectory() . '/searches/' . $owner;
    }

    /**
     * Saves the XML file for this search.
     * 
     * @return bytes written or FALSE if the save failed.
     */
    public function saveSearch()
    {
    	Logger::log('saving search');
    	
		$doc = $this->buildXMLDoc();
		Logger::log($doc->saveXML(), Zend_Log::DEBUG);
		//Make the directory if it doesn't exist
		$userdir = self::getSavedSearchesDirectory($this->getOwner());
		if (!is_dir($userdir))
		{
			mkdir($userdir);
		}
		
		$filename = $this->getSavedSearchesDirectory($this->getOwner()) . '/' . $this->getFileName();
		Logger::log('saving to . ' . $filename, Zend_Log::DEBUG);
		return $doc->save($filename);
    }
    
    protected function buildJSON()
    {
    	if (is_null($this->getSearchParameters()) && !is_null($this->getSearchID()))
    	{
    		$this->loadParametersAndColumns();
    	}
    	return parent::buildJSON();
    }
    
    /*
     * Loads the saved parameters
     */
    private function loadParametersAndColumns()
    {
    	//Load the search from the saved file
   		$doc = new DOMDocument();
		$dir = self::getSavedSearchesDirectory($this->getOwner());
   		$successful = $doc->load($dir . '/' . $this->getFileName());
   		
		$searchparams = array();
		
		if ($successful)
		{
			$searchclauses = $doc->getElementsByTagName("searchclause");
			foreach($searchclauses as $param)
			{
				$searchparam = new SearchParameters();
				
				$openparens = $param->getElementsByTagName("openparen");
				$searchparam->setOpenParenthesis($openparens->item(0)->nodeValue);
			
				$booltype = $param->getElementsByTagName("booltype");
				$searchparam->setBooleanValue($booltype->item(0)->nodeValue);
				
				$colid = $param->getElementsByTagName("ColumnID");
				$searchparam->setColumnName($colid->item(0)->nodeValue);
				
				$colname = $param->getElementsByTagName("colname");
				$searchparam->setColumnText($colname->item(0)->nodeValue);
			
				$searchtype = $param->getElementsByTagName("searchtype");
				$searchparam->setSearchType($searchtype->item(0)->nodeValue);
			
				$val = $param->getElementsByTagName("SelectedValue");
				$searchparam->setValue($val->item(0)->nodeValue);
			
				$valtext = $param->getElementsByTagName("val");
				$searchparam->setValueName($valtext->item(0)->nodeValue);
			
				$closeparen = $param->getElementsByTagName("closeparen");
				$searchparam->setClosedParenthesis($closeparen->item(0)->nodeValue);
				
				array_push($searchparams, $searchparam);
			}
			
			$this->loadVisibleColumns($doc->getElementsByTagName('visiblecolumn'));
			$this->loadHiddenColumns($doc->getElementsByTagName('hiddencolumn'));
			$this->loadOrderby($doc->getElementsByTagName('orderbycolumn'));
		}
		$this->setSearchParameters($searchparams);
    }
    
    private function loadVisibleColumns($columns)
    {
    	$visiblecols = array();
    	for($i = 0; $i < $columns->length; $i++)
		{
			$visiblecols[$columns->item($i)->nodeValue] = $columns->item($i)->nodeValue;
		}
		$this->setVisibleColumns($visiblecols);
    }
    
	private function loadHiddenColumns($columns)
    {
    	$hiddencols = array();
    	for($i = 0; $i < $columns->length; $i++)
		{
			$hiddencols[$columns->item($i)->nodeValue] = $columns->item($i)->nodeValue;
		}
		$this->setHiddenColumns($hiddencols);
    }
    
	private function loadOrderby($columns)
    {
    	$orderbycols = array();
    	for($i = 0; $i < $columns->length; $i++)
		{
			$orderbycols[$columns->item($i)->nodeValue] = $columns->item($i)->nodeValue;
		}
		$this->setOrderBy($orderbycols);
    }

    /**
     * Deletes the XML file for this search.
     * 
     * @access public
     * @return mixed
     */
    public function deleteSearch()
    {
    }

	public function updateField($columnname, $newdata)
    {
    	switch ($columnname)
    	{
    		case SearchDAO::SEARCH_NAME:
    			$this->setSearchName($newdata);
    			break;
    		case PeopleDAO::PERSON_ID:
    			$this->setOwner($newdata);
    			break;
    		case SearchDAO::IS_GLOBAL:
    			$this->setGlobal($newdata);
    			break;
    	}
    }
} 

?>