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
	
    private function updateItemWithNewFile($recordtype, $filetype, $filename)
    {
    	$item = NULL;
    	$linktype = FilesDAO::LINK_TYPE_ITEM;
    	if ($recordtype == "item")
    	{
    		$item = RecordNamespace::getCurrentItem();
    		$linktype = FilesDAO::LINK_TYPE_ITEM;
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    		$linktype = FilesDAO::LINK_TYPE_OSW;
    	}
    	
    	$fileid = "";
		$message = "Success";
    	if (isset($item) && !is_null($item))
    	{
    		$files = $item->getFiles();
    		
    		$fileid = count($files) * -1;
    		
    		$newfile = new AcornFile();
    	
	    	$newfile->setRelatedPrimaryKeyID($item->getPrimaryKey());
	    	$newfile->setPrimaryKey($fileid);
	    	$newfile->setLinkType($linktype);
	    	$newfile->setFileType($filetype);
	    	$newfile->setFileName($filename);
	    	$identity = Zend_Auth::getInstance()->getIdentity();
	    	$enteredby = PeopleDAO::getPeopleDAO()->getPerson($identity[PeopleDAO::PERSON_ID]);
	    	$newfile->setEnteredBy($enteredby);
	    	
	    	//For the images, make them temporary.
	    	if ($filetype == "Image")
	    	{
	    		$path = $config->getFilesDirectory(TRUE) . '/' . $fileid;
	    		$newfile->setUploadStatus(FilesDAO::UPLOAD_STATUS_COMPLETE);	    	
	    		$newfile->setPath($config->getACORNUrl() . $config->getFilesDirectory(TRUE) . "/" . $filename);
	    	}
	    	else
	    	{
	    		$newfile->setPath($config->getACORNUrl() . $config->getFilesDirectory(TRUE) . "/" . $filename);
	    	}
	    	$files[$fileid] = $newfile;
	    	$newfileid = FilesDAO::getFilesDAO()->saveAcornFile($newfile);
	    	if (!is_null($newfileid))
	    	{
	    		$files[$newfileid] = clone $files[$fileid];
	    		$files[$newfileid]->setPrimaryKey($newfileid);
	    		unset($files[$fileid]);
	    		$item->setFiles($files);
	    		if ($recordtype == "item")
	    		{
	    			RecordNamespace::setCurrentItem($item);
	    		}
	    		elseif ($recordtype == "osw")
	    		{
	    			RecordNamespace::setCurrentOSW($item);
	    		}
	    		 
	    		$message = "Success";
	    		 
	    	}
	    	else
	    	{
	    		$message = self::FILE_COPY_ERROR_MESSAGE;
	    	}
    	}
    	return $message;
    }
    
    /**
     * Moves the files from the temp directory to the file upload destination 
     */
    public function processFiles(array $filelist)
    {
    	$recordtype = $this->getRequest()->getParam("recordtype", "item");
    	$pkid = $this->getRequest()->getParam("pkid");
    	
    	//Make sure that the file list that we are adding to is the one associated with the document in view
    	$id = $this->verifyCurrentRecord($recordtype, $pkid);
    	
    	//Get the config so the necessary variables and configs can be found
    	$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
    		
    	//Get the userid so the temp directory can be found
    	$identity = Zend_Auth::getInstance()->getIdentity();
    	$userid = $identity[PeopleDAO::PERSON_ID];
    	
    	$fulltemppath = $config->getFilesDirectory() . "/temp" . $userid;
    	
    	//Does the file exist?
    	if (file_exists($fulltemppath))
    	{
    		//Get the list of files in the temp directory
    		$filelist = scandir($fulltemppath);

    		if (is_array($filelist))
    		{
                foreach ($filelist as $filename)
    			{
    				if (is_file($fulltemppath . "/" . $filename))
    				{
    					$filetype = AcornFile::parseFileType($filename);
    					$filesdirectory = $config->getFilesDirectory();
    					$newfilename = $this->processFilename($filename, $filesdirectory);
    					 
   						//Move the file to the proper destination.
   						$moved = rename($fulltemppath . "/" . $filename, $filesdirectory . "/" . $newfilename);
    					$this->updateItemWithNewFile($recordtype, $filetype, $newfilename, $config);
    					 
    				}
    			}    	
    		}
    		else
    		{
    			$this->displayFileErrors(array('hiddenfilepkid'=>$pkid), self::FILE_COPY_ERROR_MESSAGE);
    		}
    	}
    	else
    	{
    		$this->displayFileErrors(array('hiddenfilepkid'=>$pkid), self::FILE_COPY_ERROR_MESSAGE);
    	}    	
    }    
}
?>

