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
 * Class SelectBlankToNullFilter
 * Created on September 10, 2008
 * 
 * Returns empty values as null.
 */
class SelectBlankToNullFilter implements Zend_Filter_Interface
{
	/*
	 * Return the value or null if the selected field is blank.
	 */
    public function filter($value)
    {
    	$valueFiltered = $value;
    	if (empty($value) || $value == "-1")
    	{
    		$valueFiltered = NULL;
    	}
    	return $valueFiltered;
    }
}

?>