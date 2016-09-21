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