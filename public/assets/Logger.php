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
date_default_timezone_set('America/New_York');
class Logger
{
	private static $LOGGER = NULL;
	
	private $filename;
	
	public function getLogger($docroot)
	{
		if (is_null(self::$LOGGER))
		{
			self::$LOGGER = new Logger($docroot);
		}
		return self::$LOGGER;
	}
	
	public function __construct($docroot)
	{
		$date = date("Y-m-d");
		$this->filename = "/home/acorn/dev/logs/" . $date . "_acornuploader_log.txt";
	}
	
	public static function log($message, $docroot)
	{
		$logger = self::getLogger($docroot);
		if (file_exists($logger->filename))
		{
			$fh = fopen($logger->filename, 'a');
		}
		else
		{
			$fh = fopen($logger->filename, 'w');
		}
		self::performWrite($fh, $message);
		fclose($fh);
	}
	
	private static function performWrite($fh, $message, $recurse = 0)
	{
		$datetime = date('Y-m-d H:i:s');
		if (is_array($message))
 		{
 			foreach ($message as $key => $value)
 			{
 				if (!is_array($value))
 				{
 					self::performWrite($fh, $key . ": " . $value);
 				}
 				elseif ($recurse < 3)
 				{
 					self::performWrite($fh, $value, $recurse++);
 				}
 			}
 		}
 		else 
 		{
			$message = $datetime . ":  " . $message . "\n";
			fwrite($fh, $message);
 		}
	}
}
?>