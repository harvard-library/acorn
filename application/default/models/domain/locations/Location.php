<?php

class Location extends AcornClass
{
    private $location = NULL;
	private $TUB = NULL;
	private $shortName = NULL;
	private $acronym = NULL;
	private $isTemporary = FALSE;
	private $isRepository = FALSE;
	private $isWorkLocation = FALSE;
	private $departments = NULL;

    /**
     * @access public
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getTUB()
    {
        return $this->TUB;
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
     * @return mixed
     */
    public function isTemporary()
    {
        return $this->isTemporary;
    }
    
	/**
     * @access public
     * @return mixed
     */
    public function isRepository()
    {
        return $this->isRepository;
    }
    
	/**
     * @access public
     * @return mixed
     */
    public function isWorkLocation()
    {
        return $this->isWorkLocation;
    }

    /**
     * @access public
     * @param  String location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @access public
     * @param  String TUB
     */
    public function setTUB($TUB)
    {
        $this->TUB = $TUB;
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
     * @param  boolean isTemporary
     */
    public function setTemporary($isTemporary)
    {
        $this->isTemporary = $isTemporary;
    }
    
	/**
     * @access public
     * @param  boolean isRepository
     */
    public function setRepository($isRepository)
    {
        $this->isRepository = $isRepository;
    }
    
	/**
     * @access public
     * @param  boolean isWorkLocation
     */
    public function setWorkLocation($isWorkLocation)
    {
        $this->isWorkLocation = $isWorkLocation;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getDepartments($includeinactive = FALSE)
    {
        if (is_null($this->departments) && !is_null($this->getPrimaryKey()))
        {
        	$this->departments = DepartmentDAO::getDepartmentDAO()->getDepartments($includeinactive, $this->getPrimaryKey());
        }
        else
        {
        	$this->departments = array();
        }
        return $this->departments;
    }
    
    /**
     * @access public
     * @param  array departments
     */
    public function setDepartments(array $departments)
    {
    	$this->departments = $departments;
    }

	public function updateField($columnname, $newdata)
    {
    	switch ($columnname)
    	{
    		case LocationDAO::LOCATION:
    			$this->setLocation($newdata);
    			break;
    		case LocationDAO::ACRONYM:
    			$this->setAcronym($newdata);
    			break;
    		case LocationDAO::INACTIVE:
    			$this->setInactive($newdata);
    			break;
    		case LocationDAO::SHORT_NAME:
    			$this->setShortName($newdata);
    			break;
    		case LocationDAO::TUB:
    			$this->setTUB($newdata);
    			break;
    		case LocationDAO::IS_REPOSITORY:
    			$this->setRepository($newdata);
    			break;
    		case LocationDAO::IS_WORK_LOCATION:
    			$this->setWorkLocation($newdata);
    			break;
    	}
    }
} 

?>