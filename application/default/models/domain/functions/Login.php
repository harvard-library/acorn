<?php

class Login extends AcornClass
{
    private $identificationID = NULL;
	private $loginBy = NULL;
	private $loginDate = NULL;
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
    public function getLoginBy()
    {
    	return $this->loginBy;
    }

    /**
     * @access public
     * @param  Person loginBy
     */
    public function setLoginBy($loginBy)
    {
    	$this->loginBy = $loginBy;
    }

    /**
     * @access public
     * @param  loginDate
     */
    public function setLoginDate($loginDate)
    {
    	$this->loginDate = $loginDate;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getLoginDate()
    {
    	return $this->loginDate;
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
     * @param Login login
     */
    public function equals(Login $login)
    {
    	//First compare basic fields to determine if they are equal.
    	$equal = $this->getLoginBy() == $login->getLoginBy();
    	$equal = $equal && $this->getLoginDate() == $login->getLoginDate();
    	$equal = $equal && $this->getTransferFrom() == $login->getTransferFrom();
    	$equal = $equal && $this->getTransferTo() == $login->getTransferTo();
    	return $equal;
    }
} 

?>