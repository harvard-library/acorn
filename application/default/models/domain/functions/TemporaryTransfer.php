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

class TemporaryTransfer extends AcornClass
{
    private $itemID = NULL;
	private $transferDate = NULL;
	private $transferFromID = NULL;
	private $transferToID = NULL;
	private $transferType = NULL;
	
	/**
     * @access public
     * @return mixed
     */
    public function getItemID()
    {
    	return $this->itemID;
    }
    
	/**
     * @access public
     * @param  int itemID
     */
    public function setItemID($itemID)
    {
    	$this->itemID = $itemID;
    }
    
    /**
     * @access public
     * @param  loginDate
     */
    public function setTransferDate($transferDate)
    {
    	$this->transferDate = $transferDate;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getTransferDate()
    {
    	return $this->transferDate;
    }
    
	/**
     * @access public
     * @param  transferType
     */
    public function setTransferType($transferType)
    {
    	$this->transferType = $transferType;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getTransferType()
    {
    	return $this->transferType;
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
     * @param TemporaryTransfer transfer
     */
    public function equals(TemporaryTransfer $transfer)
    {
    	//First compare basic fields to determine if they are equal.
    	$equal = $this->getTransferDate() == $transfer->getTransferDate();
    	$equal = $equal && $this->getTransferFrom() == $transfer->getTransferFrom();
    	$equal = $equal && $this->getTransferTo() == $transfer->getTransferTo();
    	return $equal;
    }
} 

?>