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

class Format extends AcornClass
{
    private $format = NULL;

    /**
     * @access public
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @access public
     * @param  String format
     */
    public function setFormat($format)
    {
    	$this->format = $format;
    }

	public function updateField($columnname, $newdata)
    {
    	switch ($columnname)
    	{
    		case FormatDAO::FORMAT:
    			$this->setFormat($newdata);
    			break;
    		case FormatDAO::INACTIVE:
    			$this->setInactive($newdata);
    			break;
    	}
    }
    
    /**
     * Overrides the parent __toString() method.
     * Prints out the format object as a string.
     */
    public function __toString()
    {
    	$string = "";
    	if (!is_null($this->getPrimaryKey()) && $this->getPrimaryKey() > 0)
    	{
    		$string = $this->getPrimaryKey() . "_";
    	}
    	$string .= $this->getFormat();
    	return $string;
    }
    	 
} 

?>