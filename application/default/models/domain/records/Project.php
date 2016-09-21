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
 *
 * Project
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
class Project extends AcornClass
{
    /**
     * Short description of attribute projectName
     *
     * @access private
     * @var String
     */
    private $projectName = NULL;

    /**
     * Short description of attribute projectDescription
     *
     * @access private
     * @var String
     */
    private $projectDescription = NULL;

    /**
     * Short description of attribute daterange
     *
     * @access public
     * @var DateRange
     */
    private $daterange = NULL;

    /**
     * Short description of attribute records
     *
     * @access private
     * @var array
     */
    private $records = array();

    /**
     * Short description of method getProjectName
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function getProjectName()
    {
    	return $this->projectName;
    }

    /**
     * Short description of method getDescription
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function getDescription()
    {
    	return $this->projectDescription;
    }

    /**
     * Short description of method getDateRange
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return DateRange daterange
     */
    public function getDateRange()
    {
    	return $this->daterange;
    }

    /**
     * Short description of method getRecords
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function getRecords()
    {
    	return $this->records;
    }

    /**
     * Short description of method addRecord
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Record record
     */
    public function addRecord(Record $record)
    {
    	array_push($this->records, $record);
    }

    /**
     * Short description of method removeRecord
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Integer recordID
     */
    public function removeRecord($recordID)
    {
    	unset($this->records[$recordID]);
    }

    /**
     * Short description of method setRecords
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array records
     */
    public function setRecords(array $records)
    {
    	$this->records = $records;
    }

    /**
     * Short description of method setProjectName
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  String name
     */
    public function setProjectName($name)
    {
    	$this->projectName = $name;
    }

    /**
     * Short description of method setDescription
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  String description
     */
    public function setDescription($description)
    {
    	$this->projectDescription = $description;
    }

    /**
     * @access public
     * @param  DateRange daterange
     */
    public function setDateRange(DateRange $daterange)
    {
    	$this->daterange = $daterange;
    }
    
	public function updateField($columnname, $newdata)
    {
    	switch ($columnname)
    	{
    		case ProjectDAO::PROJECT_NAME:
    			$this->setProjectName($newdata);
    			break;
    		case ProjectDAO::PROJECT_DESCRIPTION:
    			$this->setDescription($newdata);
    			break;
    		case ProjectDAO::START_DATE:
    			$this->getDateRange()->setStartDate($newdata);
    			break;
    		case ProjectDAO::END_DATE:
    			$this->getDateRange()->setEndDate($newdata);
    			break;
    		case ProjectDAO::INACTIVE:
    			$this->setInactive($newdata);
    			break;
    	}
    }

}

?>