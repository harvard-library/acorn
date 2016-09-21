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
 * Class Logger
 * Created on Sep 4, 2008
 */

 class Logger
 {
 	public static $LOGGER;

 	private $zendlogger;

 	private static function getLogger()
 	{
 		if (is_null(self::$LOGGER))
 		{
 			self::$LOGGER = new Logger;
 		}
 		return self::$LOGGER;
 	}

 	private function getZendLogger()
 	{
 		if (is_null($this->zendlogger))
 		{
 			$today = date(ACORNConstants::$DATABASE_DATE_FORMAT);
 			$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
 			$logpath = $config->getLogPath();
 			$logfile = $logpath . "/" . $today . "_acorn_log.txt";
 			if (file_exists($logfile) && !is_writable($logfile))
 			{
 				$logfile = $logpath . "/" . $today . "_2_acorn_log.txt";
 			}
 			
	 		$writer = new Zend_Log_Writer_Stream($logfile);
	    	$this->zendlogger = new Zend_Log($writer);
	    	$loglevel = $config->getLogLevel();
	    	if (!is_null($loglevel) && $loglevel == "ERR")
	    	{
	    		$this->zendlogger->addFilter(new Zend_Log_Filter_Priority(Zend_Log::ERR));
	    	}
 		}
 		return $this->zendlogger;
 	}

 	/*
 	 * Log a message.
 	 *
 	 * $message mixed - can be an array or string.
 	 */
 	public static function log($message, $priority = Zend_Log::INFO)
 	{
 		if (is_array($message))
 		{
 			foreach ($message as $key => $value)
 			{
				
				if (is_array($value))
				{
					$logentry = $key . ': ' . implode($value);
				}
				else			
				{
					$logentry = $key . ': ' . $value;
				}
				
 				self::getLogger()->getZendLogger()->log($logentry, $priority);
 			}
 		}
 		else
 		{
 			self::getLogger()->getZendLogger()->log($message, $priority);
 		}
 	}
 }

 ?>