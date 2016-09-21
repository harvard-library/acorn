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