<?php
/* 
 * Copyright 2016 The President and Fellows of Harvard College
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class DRSBulkCli
{

    private $batchBuilderAssistant;

    public function processFiles($userid, $recordid, $recordtype)
    {
        
        // Get the config so the necessary variables and configs can be found
        $config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
        $filesdirectory = $config->getFilesDirectory();
        $fulltemppath   = $filesdirectory . "/temp" . $userid;
        
        // Does the file exist?
        if (file_exists($fulltemppath))
        {
            //Get the list of files in the temp directory
            $filelist = array_diff(scandir($fulltemppath), array('..', '.'));

            if ($filelist)
            {
                //Determine the batch name for the image
                $batchnameforimages = date(ACORNConstants::$DATE_FILENAME_FORMAT_DRS) . "_" . $recordtype . "_" . $recordid;
                //Variable to determine if any images exist
                $imagesexist = false;
                //The existing images
                $imagearray = array();
    	
                foreach ($filelist as $filename)
                {

                    if (is_file($fulltemppath . "/" . $filename))
                    {
                        $filetype = AcornFile::parseFileType($filename);
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
                    
                        $message = $this->updateItemWithNewFile($recordtype, $filetype, $newfilename, $batchnameforimages, $config);
                        if ($message == "Success")
                        {
                            print "Record $recordid was updated with file $newfilename\n";
                        }
                        else
                        {
                            print "$message\n";
                        }
                    }
                }

                //If there are any image files, prepare them for BB
                if (count($imagearray) > 0)
    		{
                    //The project name is based on the user
                    $projectName = "user" . $userid . "project";
                    $projectDirectory = $config->getStagingFileDirectory();
    	
                    try
                    {
                        $projectDirectory .= "/" . $projectName;
    			//BB2 requires the template directory to be set up.
                	BatchBuilderAssistant::prepareTemplateDirectory($fulltemppath, $imagearray, $projectName, $config, FALSE);

    			//Create the batch and descriptor files
    			$success = $this->getBatchBuilderAssistant($projectDirectory, $userid)->execute($batchnameforimages);

                        //If an error wasn't thrown but the batch.xml wasn't created, warn the user.
    			if (!$success)
                        {
                            $message = "There was a problem creating the batch file for the DRS.";
                            throw new DRSServiceException($message);
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
   					
    			RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);
			RecordNamespace::setStatusMessage("Your files have been sent to the DRS.");
                    }
                    catch (DRSServiceException $e)
                    {
                        //If an error was thrown, then the batch.xml wasn't created.
    			$message = "There was a problem processing the batch for the DRS.\n";
    			$message .= "The message generated was:\n" . $e->getMessage();
// #- 			
    			//Delete the batch directory.
    			$cleanupAssistant = new DRSStagingDirectoryCleanupAssistant(array($batchnameforimages));
    			$cleanupAssistant->runService();
    			FilesDAO::getFilesDAO()->deleteFilesInBatch($batchnameforimages);
    			BatchBuilderAssistant::deleteSourceFiles($fulltemppath);
    			//Delete the template directory files
    			BatchBuilderAssistant::deleteProjectTemplateFiles($projectDirectory . "/" . BatchBuilderAssistant::PROJECT_TEMPLATE_IMAGE_DIR);

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
// #-
    			print "$message\n";
                        }			 
                }  	
            }
            else
            {
                $message = "No files found in $fulltemppath\n";
            }
        }
        else
        {
            $message = "$fulltemppath not found\n";
        }

        print "$message\n";
}
    
    /**
     * Check to see if the filename already exists.  If so, then append a number (_1, _2, etc)
     * so that it has a unique filename.
     * @param string $filename
     * @param string $filesdirectory
     * @return string
     */
    function processFilename($filename, $filesdirectory)
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
	    	//The path of the temporary image in DRS2 is <staging>/<project>/<batchname>/<object>/<batchtype>
	    	$projectDirectory = "/user" . $identity[PeopleDAO::PERSON_ID] . "project";
        	$objectname = substr($filename, 0, strrpos($filename, "."));
	    	$path .= $projectDirectory . "/" . $batchnameforimages . "/" . $objectname . "/" . $config->getBatchType() . "/" . $filename;

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
        else {
            $message = "Current Item is not set";
        }
    	return $message;
    }

    /**
     * Get the instance of the BatchBuilderAssistant
     * @return BatchBuilderAssistant
     */
    private function getBatchBuilderAssistant($projectDirectory, $userid)
    {
    	if (is_null($this->batchBuilderAssistant))
    	{
	    $config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
            //BB will be used
            $this->batchBuilderAssistant = new BatchBuilderAssistant($projectDirectory, $config->getBbClientPath(), $config->getBbScriptName());
	    //Get all of the emails that should receive ALL drs emails for ACORN
	    $drsemails = PeopleDAO::getPeopleDAO()->getDRSEmails();
	    $identity = Zend_Auth::getInstance()->getIdentity();
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
}      
?>
