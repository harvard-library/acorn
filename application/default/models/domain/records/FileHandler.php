<?php

/**
 * FileHandler
 *
 * @author vcrema & telliott
 * Created Mar 13, 2016
 *
 * Copyright 2016 The President and Fellows of Harvard College
 */
	

class FileHandler extends Zend_Controller_Action
{
	private $oswForm;
	private $recordForm;
	private $acornRedirector;
	
	/**
	 * 
	 * @var BatchBuilderAssistant
	 */
	
	const DB_ERROR_MESSAGE = "There was a problem saving the file information to the database. Please try again or contact an administrator if the problem continues.";
	const FILE_COPY_ERROR_MESSAGE = "There was a problem saving the file to the file server. Please try again or contact an administrator if the problem continues.";

	const THUMBNAIL_COLUMN_COUNT = 3;
	
	private function getOswForm()
	{
		if (is_null($this->oswForm))
		{
			$this->oswForm = new OswForm();
		}
		return $this->oswForm;
	}
	
	private function getRecordForm()
	{
		if (is_null($this->recordForm))
		{
			$this->recordForm = new RecordForm();
		}
		return $this->recordForm;
	}
	
	
	private function getAcornRedirector()
	{
		if (is_null($this->acornRedirector))
		{
			$this->acornRedirector = new AcornRedirector();
		}
		return $this->acornRedirector;
	}
	
	public function preDispatch()
    {
    	if (!Zend_Auth::getInstance()->hasIdentity()) 
        {
        	if ($this->getRequest()->getActionName() != 'findfiles')
        	{
            	$this->_helper->redirector('index', 'index');
        	}
        }
        
    }
    
   /**
	 * Finds a list of files for the current osw, group, or item
	 */
    public function findfilesAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the record type (item, osw, group)
		$recordtype = $this->getRequest()->getParam("recordtype", "item");
		$narrowByName = $this->getRequest()->getParam("filesearch", NULL);
    	$item = NULL;
		if ($recordtype == "item")
		{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    	}
    	else 
    	{
    		$item = GroupNamespace::getCurrentGroup();
    	}
    	
    	$files = array();
    	if (is_null($item))
    	{
    		Logger::log("item is  null");
    	}
    	if (isset($item) && !is_null($item))
    	{
    		$fileobjects = $item->getFiles(TRUE, $narrowByName);
    		$count = 0;	
    		$filesrow = array();
    		foreach ($fileobjects as $file)
    		{
    			$count++;
    			
    			$thumbnail = $file->getPath();
    			$path = $file->getPath();
    			$ext = AcornFile::getFileExtension($file->getFileName());
    			if ($ext == 'jp2')
    			{
    				$thumbnail .= "?height=200";
    				$path .= "?buttons=y";
    				$filetype = "Image";
    			}
    			elseif ($ext == 'jpg' || $ext == 'gif')
    			{
    				$filetype = "Image";
    			}
    			elseif (AcornFile::parseFileType($file->getFileName()) == 'Image')
    			{
    				$thumbnail = $file->getFileName();
    				$filetype = "ImageNoView";
    			}
    			else
    			{
    				$thumbnail = substr($thumbnail, strrpos($thumbnail, "/") + 1);
    				$filetype = $file->getFileType();
    			}
    			
    			$zenddateentered = new Zend_Date($file->getDateEntered(), ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
    			$zendlastmodified = new Zend_Date($file->getLastModified(), ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
    			
    			$filesrow[FilesDAO::FILE_ID . $count] = $file->getPrimaryKey();
	    		$filesrow["Thumbnail" . $count] = $thumbnail;
	 			$filesrow[FilesDAO::DATE_ENTERED . $count] = $zenddateentered->toString(ACORNConstants::$ZEND_DATE_FORMAT);
	 			$filesrow[FilesDAO::LAST_MODIFIED . $count] = $zendlastmodified->toString(ACORNConstants::$ZEND_DATE_FORMAT);
	 			$filesrow[FilesDAO::PATH . $count] = $path;
	 			$filesrow[FilesDAO::FILE_NAME . $count] = $file->getFileName();
    			$filesrow[FilesDAO::DESCRIPTION . $count] = $file->getDescription();
	 			$filesrow[FilesDAO::FILE_TYPE . $count] = $filetype;
	 			
	 			//Every three thumbnails.
	 			if ($count == self::THUMBNAIL_COLUMN_COUNT) 
	 			{
	 				array_push($files, $filesrow);
	 				$filesrow = array();
	 				$count = 0;
	 			}		
	    	}
	    	if ($count > 0 && $count < self::THUMBNAIL_COLUMN_COUNT) 
	    	{
	    		array_push($files, $filesrow);
	    	}
    	}
		//Only use the results
    	$resultset = array('Result' => $files);
    	$retval = Zend_Json::encode($resultset);
		Logger::log($retval);
    	//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
    public function renamefileAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$recordtype = $this->getRequest()->getParam("recordtype", "item");
    	$fileid = $this->getRequest()->getParam("fileID");
    	$fileName = $this->getRequest()->getParam("fileName");
    	$pkid = $this->getRequest()->getParam("pkid");
    	
  		//Make sure that the file list that we are adding to is the one associated with the document in view
    	$this->verifyCurrentRecord($recordtype, $pkid);
    	
    	$item = NULL;
    	if ($recordtype == "item")
    	{
    		$item = RecordNamespace::getCurrentItem();
    	}
    	elseif ($recordtype == "osw")
    	{
    		$item = RecordNamespace::getCurrentOSW();
    	}
    	
    	//Default error message (will be changed if everything works out successfully).
    	$retval = Zend_Json::encode(array('ErrorMessage' => 'There was a problem renaming the file.  Please try again.'));
        
    	if (isset($item) && !is_null($item))
    	{
    		$files = $item->getFiles();
    		if (isset($files[$fileid]) && $files[$fileid]->getFileName() != $fileName)
    		{
    			//If the file type is not an image then update the path and rename in
    			//the file directory
    			if ($files[$fileid]->getFileType() != "Image")
    			{
    				//Get the files directory
        			$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
        			$filesdirectory = $config->getFilesDirectory();
        	
        			//Old path
        			$oldpath = $filesdirectory . "/" . $files[$fileid]->getFileName();
        			
        			//Find a filename that suits the new file so that no duplicates exist.
        			$fileName = $this->processFilename($fileName, $filesdirectory);
        			$files[$fileid]->setFileName($fileName);
        			$newpath = $filesdirectory . "/" . $fileName;
        			//Update the path
        			$files[$fileid]->setPath($config->getACORNUrl() . $config->getFilesDirectory(TRUE) . "/" . $fileName);
        			
        			//Rename the file in the actual server folder
        			$renamed = rename($oldpath, $newpath);
        			if (!$renamed)
        			{
        				//Send the error response before saving.
						$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
        			}
    			}
    			else 
    			{
    				$files[$fileid]->setFileName($fileName);
    			}
    			
    			$newfileid = FilesDAO::getFilesDAO()->saveAcornFile($files[$fileid]);
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
			    	
			    	//Send a success method
			    	$retval = Zend_Json::encode(array('Success' => 'Success'));
    			}
		    }
    	}
    	//Send the response.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
				->setBody($retval);
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
    
    public function addnewfileAction()
    {
    	//Don't display a new view
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    	
	   	$failedfiles = array();
	   	
	   	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$userid = $identity[PeopleDAO::PERSON_ID];
    	
	   	foreach ($_FILES as $fieldName => $file)
	   	{
	   		$tempfilepath = $_SERVER['DOCUMENT_ROOT'] . "/userfiles/temp" . $userid;
	   		//If the temp directory doesn't exist, create it
	   		if (!file_exists($tempfilepath))
	   		{
	   			mkdir($tempfilepath);
	   		}
	   		$fullpath = $tempfilepath . "/" . $file['name'];
	   		$fileuploaded = move_uploaded_file($file['tmp_name'], $fullpath);
	   		
	   		if (!$fileuploaded)
	   		{
	   			array_push($failedfiles, $file["name"]);
	   		}
	   	}
   	
	   	//If any files failed, then indicate them here.
	   	//otherwise send a success response.
	   	if (count($failedfiles) > 0)
	   	{
	   		$retval = "Fail";
	   		$response = array('FAILURE' => $failedfiles);
	   	}
	   	else
	   	{
	   		$retval = "Success";
	   		$response = array('SUCCESS' => 'SUCCESS');
	   	}
	   	$encoded = json_encode($response);
	   	//header('Content-type: application/json');
	   	echo $retval;
     }   
    
    private function displayFileErrors(array $formData, $message)
    {
    	$form = $this->getRecordForm();
		$form->populate($formData);
    	$this->view->form = $form;
    	
    	$this->view->placeholder("fileserrormessage")->set($message);
    	
    	$this->renderScript('record/index.phtml');
    }
    
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
     * Check to see if the filename already exists.  If so, then append a number (_1, _2, etc)
     * so that it has a unique filename.
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
    	
    	$filetype = AcornFile::parseFileType($filename);
    	
    	//Get the base name of the file
    	$strippedfilename = substr($filename, 0, strrpos($filename, "."));
    	$newfilename = $strippedfilename;
    	//Get the file suffix
	    $suffix = substr($filename, strrpos($filename, ".")+1);
	    	
	    //Files are being stored on the local server.
    	if ($filetype != "Image")
    	{
	    	$targetpath = $filesdirectory;
    		$fullpath = $targetpath . "/" . $filename;
    		$counter = 1;
    		//Loop through the files until the filename name doesn't exist.
	    	while (file_exists($fullpath))
	        {
	        	$newfilename = $strippedfilename . "_" . $counter . "." . $suffix;
	        	$fullpath = $targetpath . "/" . $newfilename;
	        	$counter++;
	        }
	        
    	}
    	else 
    	{
    		$counter = 1;
    		//Loop through the database filenames until the filename does not exist.
    		while (FilesDAO::getFilesDAO()->filenameExists($newfilename))
    		{
    			$newfilename = $strippedfilename . "_" . $counter;
    			$counter++;
    		}
    		$newfilename .= '.' . $suffix;
    	}
    	$filename = $newfilename;
    	return $filename;
    }
    
    /**
     * Remove a file from the Record.
     */
	public function removefileAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$recordtype = $this->getRequest()->getParam("recordtype", "item");
    	$fileid = $this->getRequest()->getParam("fileID");
    	
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
    	
    	if (isset($fileid) && isset($item) && !is_null($item))
    	{
    		$files = $item->getFiles();
    		if (isset($files[$fileid]))
    		{
    			if ($files[$fileid]->getFileType() != "Image")
	    		{
	    			$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
	    			
        			$filename = substr($files[$fileid]->getPath(), strrpos($files[$fileid]->getPath(), "/"));
    				$filenamepath = $config->getFilesDirectory() . $filename;
	    			//Remove from the file system.
	    			unlink($filenamepath);
	    		}
	    		
    			FilesDAO::getFilesDAO()->removeAcornFile($fileid, $linktype);
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
		    }
    	}
    }
        
    /**
     * Moves the files from the temp directory to the file upload destination 
     * @throws FileUploadException
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
    		//Get the list of files in the temp directory  #- ???
// #-    		$filelist = scandir($fulltemppath);

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

