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
 * Class EmptyToNullFilter
 * Created on August 13, 2008
 * 
 * Returns empty values as null.
 */
class EmptyToNullFilter implements Zend_Filter_Interface
{
	/*
	 * Return the password as an encrypted value.
	 */
    public function filter($value)
    {
    	$value = trim($value);
    	$valueFiltered = $value;
    	if (!isset($value) || empty($value))
    	{
    		$valueFiltered = NULL;
    	}
        return $valueFiltered;
    }
}

?>