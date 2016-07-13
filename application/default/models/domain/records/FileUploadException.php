<?php

/**
 * FileUploadException
 *
 * @author vcrema & telliott
 * Created Mar 13, 2016
 *
 * Copyright 2016 The President and Fellows of Harvard College
 */

class FileUploadException extends Exception
{
	public function __toString()
	{
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}
?>

