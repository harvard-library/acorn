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
 * @author Valdeva Crema
 * Class DateRange
 * 
 * A DateFormat object
 */
class DateFormat
{
	/**
	 * The regular expression for finding a date with the following forms:
	 * yyyy-MM-dd
	 * yyyy-M-d
	 * yyyy/MM/dd
	 * yyyy/M/d
	 * yyyy.MM.dd
	 * yyyy.M.d
	 * yyyy MM dd
	 * yyyy M d
	 */
	const PREG_INTERNAL_DATE = "/^(19|20)\d\d(-|\s|\/|\.)(0[1-9]|1[0-2]|[1-9])(-|\s|\/|\.)(0[1-9]|[1-2][0-9]|3[0-1]|[1-9])$/";

	/**
	 * The regular expression for finding a date with the following forms:
	 * MM-dd-yyyy
	 * M-d-yyyy
	 * MM/dd/yyyy
	 * M/d/yyyy
	 * MM.dd.yyyy
	 * M.d.yyyy
	 * MM dd yyyy
	 * M d yyyy
	 * MM-dd-yy
	 * M-d-yy
	 * MM/dd/yy
	 * M/d/yy
	 * MM.dd.yy
	 * M.d.yy
	 * MM dd yy
	 * M d yy
	 */
	const PREG_EXTERNAL_DATE = "/^(0[1-9]|1[0-2]|[1-9])(-|\s|\/|\.)(0[1-9]|[1-2][0-9]|3[0-1]|[1-9])(-|\s|\/|\.)(((19|20)\d\d)|(\d\d))$/";
	
	/**
	 * Determines the date format for the  given date string.
	 *
	 * @param String $datestring
	 * @return String the Zend_Date format, NULL if it can't be determined.
	 */
	public static function determineDateFormat($datestring)
	{
		//If it matches the internal pattern, then return the proper Zend pattern.
		if (preg_match(self::PREG_INTERNAL_DATE, $datestring))
		{
			return ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT_FLEXIBLE;
		}
		elseif (preg_match(self::PREG_EXTERNAL_DATE, $datestring))
		{
			return ACORNConstants::$ZEND_DATE_FORMAT_FLEXIBLE;
		}
		return NULL;
	}
}
?>
