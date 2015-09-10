<?php

class Department extends AcornClass
{
    private $locationID = NULL;
	private $departmentName = NULL;
	private $shortName = NULL;
	private $acronym = NULL;

    /**
     * @access public
     * @return mixed
     */
    public function getLocationID()
    {
        return $this->locationID;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getDepartmentName()
    {
    	return $this->departmentName;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getAcronym()
    {
        return $this->acronym;
    }

    /**
     * @access public
     * @param  String departmentName
     */
    public function setDepartmentName($departmentName)
    {
    	$this->departmentName = $departmentName;
    }

    /**
     * @access public
     * @param  String shortName
     */
    public function setShortName($shortName)
    {
    	$this->shortName = $shortName;
    }

    /**
     * @access public
     * @param  String acronym
     */
    public function setAcronym($acronym)
    {
    	$this->acronym = $acronym;
    }

	/**
     * @access public
     * @param  int locationID
     */
    public function setLocationID($locationID)
    {
    	$this->locationID = $locationID;
    }
    
    public function updateField($columnname, $newdata)
    {
    	switch ($columnname)
    	{
    		case DepartmentDAO::DEPARTMENT_NAME:
    			$this->setDepartmentName($newdata);
    			break;
    		case DepartmentDAO::ACRONYM:
    			$this->setAcronym($newdata);
    			break;
    		case DepartmentDAO::INACTIVE:
    			$this->setInactive($newdata);
    			break;
    		case DepartmentDAO::SHORT_NAME:
    			$this->setShortName($newdata);
    			break;
    		case LocationDAO::LOCATION_ID:
    			$this->setLocationID($newdata);
    			break;
    	}
    }
} 

?>