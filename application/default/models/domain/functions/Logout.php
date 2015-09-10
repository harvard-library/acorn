<?php

class Logout extends AcornClass
{
    private $identificationID = NULL;
	private $logoutBy = null;
	private $logoutDate = null;
	private $transferFromID = NULL;
	private $transferToID = NULL;
	
	/**
     * @access public
     * @return mixed
     */
    public function getIdentificationID()
    {
    	return $this->identificationID;
    }
    
	/**
     * @access public
     * @param  int identificationID
     */
    public function setIdentificationID($identificationID)
    {
    	$this->identificationID = $identificationID;
    }
    
    /**
     * @access public
     * @return mixed
     */
    public function getLogoutBy()
    {
    	return $this->logoutBy;
    }

    /**
     * @access public
     * @param  Person loginBy
     */
    public function setLogoutBy($logoutBy)
    {
    	$this->logoutBy = $logoutBy;
    }

    /**
     * @access public
     * @param  loginDate
     */
    public function setLogoutDate($logoutDate)
    {
    	$this->logoutDate = $logoutDate;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getLogoutDate()
    {
    	return $this->logoutDate;
    }

	/**
     * @access public
     * @param  transferFromID
     */
    public function setTransferFrom($transferFromID)
    {
    	$this->transferFromID = $transferFromID;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getTransferFrom()
    {
    	return $this->transferFromID;
    }
    

    /**
     * @access public
     * @param  transferToID
     */
    public function setTransferTo($transferToID)
    {
    	$this->transferToID = $transferToID;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getTransferTo()
    {
    	return $this->transferToID;
    }
    
	/*
     * Determines if the instance passed in is the same as the current instance.
     * 
     * @param Logout logout
     */
    public function equals(Logout $logout)
    {
    	//First compare basic fields to determine if they are equal.
    	$equal = $this->getLogoutBy() == $logout->getLogoutBy();
    	$equal = $equal && $this->getLogoutDate() == $logout->getLogoutDate();
    	$equal = $equal && $this->getTransferFrom() == $logout->getTransferFrom();
    	$equal = $equal && $this->getTransferTo() == $logout->getTransferTo();
    	return $equal;
    }
} 

?>