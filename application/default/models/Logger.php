<?php
/*
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
 			//$keys = array_keys($message);
 			//$values = array_values($message);
 			//$value = current($values);
 			foreach ($message as $key => $value)
 			{
 				self::getLogger()->getZendLogger()->log($key . ': ' . $value, $priority);
 				//$value = next($values);
 			}
 		}
 		else
 		{
 			self::getLogger()->getZendLogger()->log($message, $priority);
 		}
 	}
 }

 ?>