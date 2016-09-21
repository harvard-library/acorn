<?php

/*
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
 * @author Valdeva Crema
 * Class DateFilter
 * Created on March 6, 2009
 * 
 * Returns in the form of MM-DD-YYYY
 */
class DateFilter implements Zend_Filter_Interface
{
	public function filter($value)
    {
    	if (empty($value))
    	{
    		return $value;
    	}
    	$valuearray = explode(" ", $value);
    	if (count($valuearray) > 0)
    	{
    		$value = current($valuearray);
    		$format = DateFormat::determineDateFormat($value);
    		if (!is_null($format))
    		{
    			$zenddate = new Zend_Date($value, $format);		
    			$value = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    		}	
			if (!$value)
    		{
    			$value = '';
    		}
    	}
    	else 
    	{
    		$value = '';
    	}
    	return $value;
    }
}

?>