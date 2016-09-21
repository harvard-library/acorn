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

class ReportConservator
{
    private $conservator = NULL;
	private $hoursWorked = NULL;
	private $dateCompleted = NULL;
	
    /**
     * @access public
     * @return Person
     */
    public function getConservator()
    {
    	return $this->conservator;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getHoursWorked()
    {
    	return $this->hoursWorked;
    }

    /**
     * @access public
     * @param  Person conservator
     */
    public function setConservator($conservator)
    {
    	$this->conservator = $conservator;
    }

    /**
     * @access public
     * @param  Decimal hoursWorked
     */
    public function setHoursWorked($hoursWorked)
    {
    	$this->hoursWorked = $hoursWorked;
    }
    
    

    /**
	 * @return the $dateCompleted
	 */
	public function getDateCompleted()
	{
		return $this->dateCompleted;
	}

	/**
	 * @param NULL $dateCompleted
	 */
	public function setDateCompleted($dateCompleted)
	{
		$this->dateCompleted = $dateCompleted;
	}

	public function __toString()
    {
    	$string = $this->getConservator()->__toString();
    	if (!is_null($this->dateCompleted))
    	{
    		$string .= $this->getDateCompleted();
    	}
    	if (!is_null($this->hoursWorked))
    	{
    		$string .= $this->getHoursWorked();
    	}
    	return $string;
    }
} 

?>