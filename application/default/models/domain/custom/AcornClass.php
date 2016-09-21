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

abstract class AcornClass
{
	private $primaryKey = NULL;
	private $isInactive = FALSE;
	private $isBeingEdited = FALSE;
    private $isBeingEditedByID = NULL;
    private $isBeingEditedBy = NULL;
    
    /**
     * @access public
     * @return int
     */
    public function getPrimaryKey()
    {
    	return $this->primaryKey;
    }

    /**
     * @access public
     * @return true/false
     */
    public function isInactive()
    {
    	return $this->isInactive;
    }

    /**
     * @access public
     * @param  Integer primaryKey
     */
    public function setPrimaryKey($primaryKey)
    {
    	$this->primaryKey = $primaryKey;
    }

    /**
     * @access public
     * @param  Boolean isInactive
     */
    public function setInactive($isInactive = FALSE)
    {
    	$this->isInactive = $isInactive;
    }

    /**
     * Initializes the object
     *
     * @access public
     * @param  Integer primaryKey
     * @param  Boolean isInactive
     * @param  Boolean isBEingEdited
     */
    public function __construct($primaryKey = NULL, $isInactive = FALSE, $isBeingEdited = FALSE)
    {
    	$this->primaryKey = $primaryKey;
    	$this->isInactive = $isInactive;
    	$this->isBeingEdited = $isBeingEdited;
    }

    /**
     * @access public
     * @return boolean
     */
    public function isBeingEdited()
    {
    	return $this->isBeingEdited;
    }

    /**
     * Short description of method isDirty
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function isDirty()
    {
        // section -128-103--105-22-19e48aa2:11ed073a350:-8000:0000000000001054 begin
        // section -128-103--105-22-19e48aa2:11ed073a350:-8000:0000000000001054 end
    }

    /**
     * @access public
     * @param  Boolean isBeingEdited
     */
    public function setIsBeingEdited($isBeingEdited)
    {
    	$this->isBeingEdited = $isBeingEdited;
    }
    
	/**
     * @access public
     * @return Person
     */
    public function getIsBeingEditedBy()
    {
    	if (is_null($this->isBeingEditedBy) && !is_null($this->isBeingEditedByID))
    	{
    		$this->isBeingEditedBy = PeopleDAO::getPeopleDAO()->getPerson($this->isBeingEditedByID);
    	}
    	return $this->isBeingEditedBy;
    }
    
	/**
     * @access public
     * @param  String isBeingEditedBy
     */
    public function setIsBeingEditedBy($isBeingEditedBy)
    {
    	$this->isBeingEditedBy = $isBeingEditedBy;
    }
    
	/**
     * @access public
     * @param  String isBeingEditedBy
     */
    public function setIsBeingEditedByID($isBeingEditedByID)
    {
    	$this->isBeingEditedByID = $isBeingEditedByID;
    }
    
    protected function arraysDifferent(array $array1, array $array2)
    {
    	$equals = TRUE;
    	if (count($array1) == count($array2))
    	{
    		$intersectarray = array_intersect($array1, $array2);
    		if (count($intersectarray) != count($array1))
    		{
    			$equals = FALSE;
    		}
    	}
    	else
    	{
    		$equals = FALSE;
    	}
    	return $equals;
    }
    
    /**
     * Overrides the parent method of __toString
     * @return the primary key
     */
    public function __toString()
    {
    	return $this->getPrimaryKey();
    }
    
} 

?>