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
 * Class DateOrYearValidator
 * Created On Sep 5, 2008
 *
 * This Validator allows the date to be null, to be
 * in the format of m-d-yyyy or m/d/yyyy (where month and days can
 * be one or two digits), or to be a four digit year.
 */
class DateOrYearValidator extends Zend_Validate_Abstract
{
	const MESSAGE = 'date';

	protected $_messageTemplates = array(
	self::MESSAGE => "'%value%' is not a correct date or year (mm-dd-yyyy or yyyy-mm-dd or yyyy)"
	);

	public function isValid($value)
	{
		if (!empty($value) && strlen($value) != 4)
		{
			$this->_setValue($value);
			$zenddate = new Zend_Date($value, ACORNConstants::$ZEND_DATE_FORMAT);		
    		$valueFiltered = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
			
			if (!$valueFiltered)
			{
				$this->_error();
				return false;
			}
		}
		return true;	 
	}
}
?>