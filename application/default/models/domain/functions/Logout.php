<?php
/**
 * Copyright 2016 The President and Fellows of Harvard College
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */

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