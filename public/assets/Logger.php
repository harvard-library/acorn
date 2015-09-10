<?php
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