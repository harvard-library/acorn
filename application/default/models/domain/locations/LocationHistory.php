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

class LocationHistory extends AcornClass
{
    private $itemID = NULL;
	private $dateIn = NULL;
	private $dateOut = NULL;
	private $locationID = NULL;
	private $isTemporary = NULL;
	
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
     * @param  dateIn
     */
    public function setDateIn($dateIn)
    {
    	$this->dateIn = $dateIn;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getDateIn()
    {
    	return $this->dateIn;
    }


    /**
     * @access public
     * @param  dateOut
     */
    public function setDateOut($dateOut)
    {
    	$this->dateOut = $dateOut;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getDateOut()
    {
    	return $this->dateOut;
    }
    

    /**
     * @access public
     * @param  isTemporary
     */
    public function setTemporary($isTemporary)
    {
    	$this->isTemporary = $isTemporary;
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
     * @param  locationID
     */
    public function setLocationID($locationid)
    {
    	$this->locationID = $locationid;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getLocationID()
    {
    	return $this->locationID;
    }
} 

?>