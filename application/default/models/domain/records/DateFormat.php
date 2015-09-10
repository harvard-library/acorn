<?php
/**********************************************************************
 * Copyright (c) 2011 by the President and Fellows of Harvard College
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA.
 *
 * Contact information
 *
 * Library Technology Services, Harvard University Information Technology
 * Harvard Library
 * Harvard University
 * Cambridge, MA  02138
 * (617)495-3724
 * hulois@hulmail.harvard.edu
 **********************************************************************/
 /**
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