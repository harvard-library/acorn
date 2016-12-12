<?php

/**
 * RecordfilesController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class RecordfilesController extends Zend_Controller_Action
{
	private $oswForm;
	private $recordForm;
	private $acornRedirector;
	
	/**
	 * 
	 * @var BatchBuilderAssistant
	 */
	private $batchBuilderAssistant;
	
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
    
    /**
     * Get the instance of the BatchBuilderAssistant
     * @return BatchBuilderAssistant
     */
    private function getBatchBuilderAssistant($projectDirectory)
    {
    	if (is_null($this->batchBuilderAssistant))
    	{
	    	$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
			//BB will be used
			$this->batchBuilderAssistant = new BatchBuilderAssistant($projectDirectory, $config->getBbClientPath(), $config->getBbScriptName(), $config->getDRSVersion());
	    	//Get all of the emails that should receive ALL drs emails for ACORN
	    	$drsemails = PeopleDAO::getPeopleDAO()->getDRSEmails();
	    	$identity = Zend_Auth::getInstance()->getIdentity();
       	 	$userid = $identity[PeopleDAO::PERSON_ID];
        	$person = PeopleDAO::getPeopleDAO()->getPerson($userid);
	    	//If the person has a registered email address, then
	    	if (!is_null($person->getEmailAddress()))
	    	{
	    		array_push($drsemails, $person->getEmailAddress());
	    	}
	    	$drsemails = array_unique($drsemails);
	    	$this->batchBuilderAssistant->setSuccessEmails($drsemails);
	    	$this->batchBuilderAssistant->setFailEmails($drsemails);
    	}
    	return $this->batchBuilderAssistant;
    	
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

    
    private function updateItemWithNewFile($recordtype, $filetype, $filename, $batchnameforimages, DRSDropperConfig $config)
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
	    		$path = $config->getStagingFileDirectory(TRUE);
	    		//The path of the image in DRS 1 is <staging>/<batchname>/<batchtype>
	    		if ($config->getDRSVersion() == DRSDropperConfig::DRS)
	    		{
	    			$path .= "/" . $batchnameforimages . "/" . $config->getBatchType() . "/" . $filename;
	    		}
	    		//The path of the temporary image in DRS2 is <staging>/<project>/<batchname>/<object>/<batchtype>
	    		else
	    		{
	    			
        			$projectDirectory = "/user" . $identity[PeopleDAO::PERSON_ID] . "project";
        			$objectname = substr($filename, 0, strrpos($filename, "."));
	    			$path .= $projectDirectory . "/" . $batchnameforimages . "/" . $objectname . "/" . $config->getBatchType() . "/" . $filename;
	    		}
	    		$newfile->setPath($config->getACORNUrl() . $path);
	    		$newfile->setDrsStatus(FilesDAO::DRS_STATUS_PENDING);
	    		$newfile->setDrsBatchName($batchnameforimages);
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
            $counter = 1;
            $newfilename = $strippedfilename . "_" . $counter . "." . $suffix;
            $fullpath = $targetpath . "/" . $newfilename;

            //Loop through the files until the filename name doesn't exist.
	    while (file_exists($fullpath))
	    {
	       	$counter++;
	       	$newfilename = $strippedfilename . "_" . $counter . "." . $suffix;
	       	$fullpath = $targetpath . "/" . $newfilename;
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
     * Remove a file from the Record.  This does NOT remove it from the DRS.  That has to be done manually
     * via the DRS Web Admin.
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
     * Clears out the files that were saved in the user's temporary directory.
     * (DOCUMENT_ROOT/userfiles/temp<userid>)
     *
     */
    public function cleartemporaryfilesAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
        	
        $identity = Zend_Auth::getInstance()->getIdentity();
        $userid = $identity[PeopleDAO::PERSON_ID];
        
        $usertempdir = $config->getFilesDirectory() . "/temp" . $userid;
		$handle=opendir($usertempdir);

		while (($file = readdir($handle))!==false) 
		{
			@unlink($usertempdir.'/'.$file);
		}

		closedir($handle);
    }
    
    /**
     * Clears out the files that were saved in the user's temporary directory.
     * (DOCUMENT_ROOT/userfiles/temp<userid>)
     *
     */
    public function removefromtemporaryfilesAction()
    {
    	//Don't display a new view
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    
    	$filename = $this->getRequest()->getParam("filename");
    	
    	$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
    	 
    	$identity = Zend_Auth::getInstance()->getIdentity();
    	$userid = $identity[PeopleDAO::PERSON_ID];
    
    	$usertempdir = $config->getFilesDirectory() . "/temp" . $userid;
    	@unlink($usertempdir . '/' . $filename);
    	
    }
    
    /**
     * Prepare the batch and send the items to the DRS
     */
    public function sendtodrsAction()
    {
    	$recordtype = $this->getRequest()->getParam("recordtype", "item");
    	$pkid = $this->getRequest()->getParam("pkid");
    	
    	//Make sure that the file list that we are adding to is the one associated with the document in view
    	$id = $this->verifyCurrentRecord($recordtype, $pkid);
    	
    	//Get the config so the necessary variables and configs can be found
    	$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
    		
    	//Get the userid so the temp directory can be founds
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
                //Determine the batch name for the image
    		$batchnameforimages = date(ACORNConstants::$DATE_FILENAME_FORMAT_DRS) . "_" . $recordtype . "_" . $id;
    		//Variable to determine if any images exist
    		$imagesexist = false;
    		//The existing images
    		$imagearray = array();
    	
    		foreach ($filelist as $filename)
    		{
                    if (is_file($fulltemppath . "/" . $filename))
                    {
                        $filetype = AcornFile::parseFileType($filename);
    			$filesdirectory = $config->getFilesDirectory();
    			$newfilename = $this->processFilename($filename, $filesdirectory);
    					 
    			if ($filetype == "Image")
                        {
                            $imagearray[$filename] = $newfilename;
                            $imagesexist = true;
    			}
                        //Non-image files are not saved to the DRS so copy it to the proper directory
    			else
    			{
                            //Move the file to the proper destination.
                            $moved = rename($fulltemppath . "/" . $filename, $filesdirectory . "/" . $newfilename);
    			}
                        
    			$this->updateItemWithNewFile($recordtype, $filetype, $newfilename, $batchnameforimages, $config);			 
                    }
    		}

    		if ($recordtype == "item")
    		{
                    $item = RecordNamespace::getCurrentItem();
                    $action = "index";
                    $itemid = $item->getItemID();
    		}
    		elseif ($recordtype == "osw")
    		{
                    $item = RecordNamespace::getCurrentOSW();
                    $action = "osw";
                    $itemid = $item->getOSWID();
    		}
    		        
    		//If there are any image files, prepare them for BB
    		if (count($imagearray) > 0)
    		{
                    //The project name is based on the user
                    $projectName = "user" . $userid . "project";
                    $projectDirectory = $config->getStagingFileDirectory();
    	
                    try
                    {
                        if ($config->getDRSVersion() == DRSDropperConfig::DRS2)
    			{
                            $projectDirectory .= "/" . $projectName;
                            //BB2 requires the template directory to be set up.
                            BatchBuilderAssistant::prepareTemplateDirectory($fulltemppath, $imagearray, $projectName, $config);
    			}
    			else
    			{
                            BatchBuilderAssistant::createBB1DirectoryAndCopyFiles($fulltemppath, $imagearray, $batchnameforimages, $config);
    			}
                        
    			//Create the batch and descriptor files
    			$success = $this->getBatchBuilderAssistant($projectDirectory)->execute($batchnameforimages);
    			//If an error wasn't thrown but the batch.xml wasn't created, warn the user.
    			if (!$success)
    			{
                            $message = "There was a problem creating the batch file for the DRS.";
                            throw new DRSServiceException($message);
    			}
    					
    			RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);
			RecordNamespace::setStatusMessage("Your files have been sent to the DRS.");
    			$this->_helper->redirector($action, 'record', NULL, array('recordnumber' => $itemid, 'pkid' => $pkid));
                    }
                    catch (DRSServiceException $e)
                    {
                        //If an error was thrown, then the batch.xml wasn't created.
    			$message = "There was a problem processing the batch for the DRS.\n";
    			$message .= "The message generated was:\n" . $e->getMessage();
    			Logger::log($e->getMessage(), Zend_Log::ERR);
    	
    			//Delete the batch directory.
    			$cleanupAssistant = new DRSStagingDirectoryCleanupAssistant(array($batchnameforimages));
    			$cleanupAssistant->runService();
    			FilesDAO::getFilesDAO()->deleteFilesInBatch($batchnameforimages);
    			BatchBuilderAssistant::deleteSourceFiles($fulltemppath);
    			//Delete the template directory files
    			if ($config->getDRSVersion() == DRSDropperConfig::DRS2)
    			{
                            //BB2 requires the template directory to be set up.
                            BatchBuilderAssistant::deleteProjectTemplateFiles($projectDirectory . "/" . BatchBuilderAssistant::PROJECT_TEMPLATE_IMAGE_DIR);
    			}
    			if ($recordtype == "item")
    			{
                            $originalitem = RecordNamespace::getOriginalItem();
                            RecordNamespace::setCurrentItem($originalitem);
    			}
    			elseif ($recordtype == "osw")
    			{
                            $originalitem = RecordNamespace::getOriginalOSW();
                            RecordNamespace::setCurrentOSW($originalitem);
    			}
                        
    			$this->displayFileErrors(array('hiddenfilepkid'=>$pkid), $message);
                    }    				 
    		}
                else
                {
                    RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);
                    RecordNamespace::setStatusMessage("Your files have been saved.");
                    $this->_helper->redirector($action, 'record', NULL, array('recordnumber' => $itemid, 'pkid' => $pkid));
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

