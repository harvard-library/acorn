<?php

class AuditTrail extends AcornClass
{
    private $actionType = NULL;
	private $date = NULL;
    private $details = NULL;
	private $tableName = NULL;
	private $objectPKID = NULL;
	private $person = NULL;
	private $personID = NULL;
    /**
     *
     * @access public
     * @return mixed
     */
    public function getActionType()
    {
    	return $this->actionType;
    }

    /**
     * @access public
     * @param  String format
     */
    public function setActionType($actionType)
    {
    	$this->actionType = $actionType;
    }
    
/**
     *
     * @access public
     * @return mixed
     */
    public function getDate()
    {
    	return $this->date;
    }

    /**
     * @access public
     * @param mixed
     */
    public function setDate($date)
    {
    	$this->date = $date;
    }
    
/**
     *
     * @access public
     * @return mixed
     */
    public function getDetails()
    {
    	return $this->details;
    }

    /**
     * @access public
     * @param  String details
     */
    public function setDetails($details)
    {
    	$this->details = $details;
    }
    
/**
     *
     * @access public
     * @return mixed
     */
    public function getTableName()
    {
    	return $this->tableName;
    }

    /**
     * @access public
     * @param  String tableName
     */
    public function setTableName($tableName)
    {
    	$this->tableName = $tableName;
    }
    
	/**
     *
     * @access public
     * @return mixed
     */
    public function getObjectPKID()
    {
    	return $this->objectPKID;
    }

    /**
     * @access public
     * @param  Int objectPKID
     */
    public function setObjectPKID($objectPKID)
    {
    	$this->objectPKID = $objectPKID;
    }
    
	/**
     *
     * @access public
     * @return mixed
     */
    public function getPerson()
    {
    	return $this->person;
    }

    /**
     * @access public
     * @param  Person person
     */
    public function setPerson(Person $person)
    {
    	$this->person = $person;
    }
    
	/**
     *
     * @access public
     * @return mixed
     */
    public function getPersonID()
    {
    	return $this->personID;
    }

    /**
     * @access public
     * @param  personID
     */
    public function setPersonID($personID)
    {
    	$this->personID = $personID;
    }

} 

?>