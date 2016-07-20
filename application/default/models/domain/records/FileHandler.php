<?php

/**
 * FileHandler
 *
 * @author vcrema & telliott
 * Created Mar 13, 2016
 *
 * Copyright 2016 The President and Fellows of Harvard College
 */

class FileHandler
{
	private $recordForm;
	
	const DB_ERROR_MESSAGE = "There was a problem saving the file information to the database. Please try again or contact an administrator if the problem continues.";
	const FILE_COPY_ERROR_MESSAGE = "There was a problem saving the file to the file server. Please try again or contact an administrator if the problem continues.";
	
	private function getRecordForm()
	{
		if (is_null($this->recordForm))
		{
			$this->recordForm = new RecordForm();
		}
		return $this->recordForm;
	}
		
    private function verifyCurrentRecord($recordtype, $pkid)
    {
    	$id = 0;
    	//Make sure that the file list that we are adding to is the one associated with the document in view
    	if ($recordtype == "item")
    	{
    		$item = RecordNamespace::getCurrentItem();
    		if (!isset($item) || $item->getPrimaryKey() != $pkid)
    		{
    			$id = ItemDAO::getItemDAO()->getItemID($pkid);
    			$item = ItemDAO::getItemDAO()->getItem($id);
    			if (!is_null($item))
    			{
    				RecordNamespace::setCurrentItem($item);
    				RecordNamespace::setOriginalItem(ItemDAO::getItemDAO()->getItem($id));
    			}
    		}
    		$id = $item->getItemID();
    			
    	}
    	else 
    	{
    		$item = RecordNamespace::getCurrentOSW();
    		if (!isset($item) || $item->getPrimaryKey() != $pkid)
    		{
    			$id = OnSiteWorkDAO::getOnSiteWorkDAO()->getOSWID($pkid);
    			$item = OnSiteWorkDAO::getOnSiteWorkDAO()->getOSW($id);
    			if (!is_null($item))
    			{
    				RecordNamespace::setCurrentOSW($item);
    				RecordNamespace::setOriginalOSW(OnSiteWorkDAO::getOnSiteWorkDAO()->getOSW($id));
    			}
    		}
    		$id = $item->getOSWID();
    	}
    	return $id;
    }    
    
    private function updateItemWithNewFile($recordtype, $pkid, $filetype, $filename, $config)
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
	   		$newfile->setPath($config->getACORNUrl() . $config->getFilesDirectory(TRUE) . '/' . $pkid . '/' . $filename);  	
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
     * Check to see if the filename already exists. 
     * If so, then append a number (_1, _2, etc) so that it has a unique filename.
     * @param string $filename
     * @param string $filesdirectory
     * @return string
     */
    private function processFilename($filename, $filesdirectory)
    {
    	//Assume that the file is a text file.
    	if (strrpos($filename, ".") === FALSE)
    	{
    		$filename .= ".txt";
    	}
    	    	
    	//Get the base name of the file
    	$strippedfilename = substr($filename, 0, strrpos($filename, "."));
    	//Get the file suffix
	    $suffix = substr($filename, strrpos($filename, ".")+1);
	    	
	    //Files are being stored on the local server.
    	$targetpath = $filesdirectory;
   		$fullpath = $targetpath . "/" . $filename;
    	$newfilename = $filename;
   		$counter = 1;

    	//Loop through the files until the filename name doesn't exist.
	    while (file_exists($fullpath))
	    {
	       	$newfilename = $strippedfilename . "_" . $counter . "." . $suffix;
	       	$fullpath = $targetpath . "/" . $newfilename;
	       	$counter++;
	    }
	        
    	$filename = $newfilename;
    	return $filename;
    }
    
    /**
     * Moves the files from the temp directory to the file upload destination 
     * @throws FileUploadException
     */
    public function processFiles($recordtype, $pkid, array $filelist)
    {    	
    	//Make sure that the file list that we are adding to is the one associated with the document in view
    	$id = $this->verifyCurrentRecord($recordtype, $pkid);

    	//Get the config so the necessary variables and configs can be found
    	$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
    		
    	//Get the userid so the temp directory can be found
    	$identity = Zend_Auth::getInstance()->getIdentity();
    	$userid = $identity[PeopleDAO::PERSON_ID];
    	
    	$filesdirectory  = $config->getFilesDirectory();
    	$fulltemppath    = $filesdirectory . "/temp" . $userid;
    	$filesdirectory .= '/' . $pkid;

    	// Create files directory for record if it doesn't exist
    	if (!file_exists($filesdirectory))
    	{
    		mkdir($filesdirectory);
    	}
    	 
    	//Does the file exist?
    	if (file_exists($fulltemppath))
    	{
    		if (is_array($filelist))
    		{
                foreach ($filelist as $filename)
    			{
    				if (is_file($fulltemppath . "/" . $filename))
    				{
    					$filetype = AcornFile::parseFileType($filename);
    					$newfilename = $this->processFilename($filename, $filesdirectory);
    					 
   						//Move the file to the proper destination.
   						$moved = rename($fulltemppath . "/" . $filename, $filesdirectory . "/" . $newfilename);
    					$this->updateItemWithNewFile($recordtype, $pkid, $filetype, $newfilename, $config);
    				}
    			}    	
    		}
    		else
    		{
    			return self::FILE_COPY_ERROR_MESSAGE;
    		}
    	}
    	else
    	{
    		return self::FILE_COPY_ERROR_MESSAGE;
    	}    	
		return TRUE;
    }
}
?>

