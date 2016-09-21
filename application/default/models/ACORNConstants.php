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
 * Class ACORNConstants
 * Created on Sep 4, 2008
 */
 
 class ACORNConstants
 {
 	/* ex. 10-03-2008 */
 	public static $DATE_FORMAT = 'm-d-Y';
 	/* ex. 15:05 AM */
 	public static $TIME_FORMAT = 'H:i A';
 	/* Group input format */
 	public static $GROUP_DATE_FORMAT = 'Y.m.d';
 	
 	public static $DATABASE_DATE_FORMAT = 'Y-m-d';
 	public static $DATABASE_DATETIME_FORMAT = 'Y-m-d H:i:s';
 	
 	public static $DATE_FILENAME_FORMAT = 'YmdHi';
 	
 	public static $ZEND_DATE_FORMAT = 'MM-dd-yyyy';
 	public static $ZEND_INTERNAL_DATE_FORMAT = 'yyyy-MM-dd';
 	public static $ZEND_INTERNAL_DATETIME_FORMAT = 'yyyy-MM-dd HH:mm:ss';
 	public static $ZEND_DATE_FORMAT_FLEXIBLE = 'M-d-yy';
 	public static $ZEND_INTERNAL_DATE_FORMAT_FLEXIBLE = 'yy-M-d';
 	
 	public static $MONTH_NAME_DATE_FORMAT = 'F j, Y';
 	
 	const MESSAGE_INVALID_FORM = 'Your form is missing or has incorrect data.  Please correct the errors in the form below.';
	const MESSAGE_PROBLEM_SAVING = 'There was a problem saving your data.  Please try to save again.';
	
	const CONFIG_NAME = 'config';
	const DB_CONFIG_NAME = 'dbconfig';
 }
 
 ?>
