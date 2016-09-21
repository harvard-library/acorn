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
 * GroupfilesController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
	
class GroupfilesController extends Zend_Controller_Action
{
	private $groupForm;
	
	const DB_ERROR_MESSAGE = "There was a problem saving the file information to the database. Please try again or contact an administrator if the problem continues.";
	const FILE_COPY_ERROR_MESSAGE = "There was a problem saving the file to the file server. Please try again or contact an administrator if the problem continues.";
   	
	const THUMBNAIL_COLUMN_COUNT = 3;
	
	private function getGroupForm()
	{
		if (is_null($this->groupForm))
		{
			$this->groupForm = new GroupForm();
		}
		return $this->groupForm;
	}

	public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
        {
            $this->_helper->redirector('index', 'index');
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
		
		$item = GroupNamespace::getCurrentGroup();
    	$narrowByName = $this->getRequest()->getParam("filesearch", NULL);
    	
    	$files = array();
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
		
		$pkid = $this->getRequest()->getParam("pkid");
    	$this->verifyCurrentGroup($pkid);
    	
		$fileid = $this->getRequest()->getParam("fileID");
    	$fileName = $this->getRequest()->getParam("fileName");
    	
    	$item = GroupNamespace::getCurrentGroup();
    	
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
		    		GroupNamespace::setCurrentGroup($item);
			    	
			    	//Send a success method
			    	$retval = Zend_Json::encode(array('Success' => 'Success'));
    			}
		    }
    	}
    	//Send the response.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
				->setBody($retval);
				
    }  
    
    /**
     * Adds a new file to the current user's temporary directory
     * @throws FileUploadException
     */
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
    	
    	//If the temp directory doesn't exist, create it
    	$tempfilepath = $_SERVER['DOCUMENT_ROOT'] . "/userfiles/temp" . $userid;
    	if (!file_exists($tempfilepath))
    	{
    		mkdir($tempfilepath);
    	}
    	 
    	foreach ($_FILES as $fieldName => $file)
    	{
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
    	 
    	$this->renderScript('group/index.phtml');
    }    
    
    private function formatGroupName($groupName)
    {
    	$groupName = str_replace(array(" ", ".", "(", ")"), "", $groupName);
    	return $groupName;
    }
        
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
    
    
	public function removefileAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$fileid = $this->getRequest()->getParam("fileID");
    	
		$item = GroupNamespace::getCurrentGroup();
    	
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
	    		
    			FilesDAO::getFilesDAO()->removeAcornFile($fileid, FilesDAO::LINK_TYPE_GROUP);
    			unset($files[$fileid]);
    			$item->setFiles($files);
		    	GroupNamespace::setCurrentGroup($item);
		    }
    	}
    }
    
    private function verifyCurrentGroup($pkid)
    {
    	$group = GroupNamespace::getCurrentGroup();
    	if (!isset($group) || $group->getPrimaryKey() != $pkid)
    	{
    		$group = GroupDAO::getGroupDAO()->getGroup($pkid);
    		if (!is_null($group))
    		{
    			GroupNamespace::setCurrentGroup($group);
    			GroupNamespace::setOriginalGroup(GroupDAO::getGroupDAO()->getGroup($pkid));
    		}
    	}
    	return $group->getPrimaryKey();
    }
    
    /**
     *
     */
    public function fileuploadAction()
    {
    	$groupid = $this->getRequest()->getParam("groupid");
    	 
    	//Make sure that the file list that we are adding to is the one associated with the document in view
    	$id = $this->verifyCurrentGroup($groupid);
    	 
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
    
    		if (!is_array($filelist))
    		{
    			$this->displayFileErrors(array('hiddenfilepkid'=>$pkid), self::FILE_COPY_ERROR_MESSAGE);
    		}
    	}
    	else
    	{
    		$this->displayFileErrors(array('hiddenfilepkid'=>$pkid), self::FILE_COPY_ERROR_MESSAGE);
    	}    	 

	    $filehandlername = ACORNConfig.get(self::FILE_HANDLER);
		$filehandler = new ReflectionClass($filehandlername);
    	$filehandler.processFiles($filelist); //You will have an array of files to pass it.  See the existing method
    }

}
?>