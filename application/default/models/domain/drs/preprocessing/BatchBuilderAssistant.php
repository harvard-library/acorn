<?php
/**********************************************************************
 * Copyright (c) 2010 by the President and Fellows of Harvard College
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA.
 *
 * Contact information
 *
 * Library Technology Services, Harvard University Information Technology
 * Harvard Library
 * Harvard University
 * Cambridge, MA  02138
 * (617)495-3724
 * hulois@hulmail.harvard.edu
 **********************************************************************/
 
/**
 * Generates the batch.xml file and places it accordingly.
 *
 * @access public
 * @author Valdeva Crema
 */
class BatchBuilderAssistant
{
	const OBJECT_XML = "object.xml";
	const OBJECT_DIRECTORY_NAME = "@DIRECTORY_NAME@";
	
	const PROJECT_CONF_TEMPLATE = "project.conf.template";
	const PROJECT_CONF = "project.conf";
	const PROJECT_NAME_VARIABLE = "@PROJECT_NAME@";
	const PROJECT_TEMPLATE_IMAGE_DIR = "template/imagedeliverable";
	
    /**
     * The path to the batch builder command line client
     *
     * @var String
     */
    private $bbClientPath;

    /**
     * The name of the BB script
     *
     * @var String
     */
    private $bbScriptName;
    
    /**
     * The directory of the project to be uploaded
     *
     * @var String
     */
    private $projectDirectory;

    
    /**
     * The array of emails to send
     *
     * @var array
     */
    private $successEmails = array();

    /**
     * The array of emails to send upon failure
     *
     * @access private
     * @var array
     */
    private $failEmails = array();

	public function __construct($projectDirectory, $bbClientPath, $bbScriptName)
    {
    	$this->projectDirectory = $projectDirectory;
    	$this->bbClientPath = $bbClientPath;
    	$this->bbScriptName = $bbScriptName;
    }
    
    /**
     * This runs BB2
     */
    public function execute($batchDirectory)
    {
    	$batchcreated = $this->createBatchFromTemplate($batchDirectory);
    	$batchprocessed = $this->processBatch($batchDirectory);
    	return $batchcreated && $batchprocessed;
    }
    
    
    /**
     * Creates the batch and objects from the template.
     * Image files should be in the <proj>/template directory
     * mapping.txt file should be in the <proj>/_aux/template directory
     * 
     * @param $batchDirectory - the directory to create
     * @return TRUE if the batch was created, FALSE otherwise.
     */
    private function createBatchFromTemplate($batchDirectory)
    {
    	$command = "cd " . $this->bbClientPath;
    	$command .= " && sh " . $this->bbScriptName . " -a buildtemplate -p " . $this->projectDirectory . " -b " . $batchDirectory;
    	Logger::log($command);
    	//Create the batch file.
    	$output = shell_exec($command);
    	 
    	//Make sure the batch directory was created.  We'll assume that the objects were created properly.
    	//Attempting to create the batch.xml and descriptor.xml files will catch the error if the objects
    	//were not created properly.
    	$batchdirectoryexists = file_exists($this->projectDirectory . "/" . $batchDirectory);
    	 
    	if (!$batchdirectoryexists)
    	{
    		throw new DRSServiceException($output);
    	}
    	
    		
    	//Check to see if the file was created properly
    	return $batchdirectoryexists;
    }
    
    /**
     * Creates the batch.xml and descriptors.xml files for the given batch
     * 
     * @param $batchDirectory - the directory to create
     * @return TRUE if the batch was created, FALSE otherwise.
     */
    private function processBatch($batchDirectory)
    {
    	$overrides = "";
    	$delimiter = "";
    	if (!empty($this->successEmails))
    	{
    		foreach($this->successEmails as $email)
    		{
    			$overrides .= $delimiter . "successEmail=" . $email;
    			$delimiter = ",";
    		}
    	}
    	if (!empty($this->failEmails))
    	{
    		foreach($this->failEmails as $email)
    		{
    			$overrides .= $delimiter . "failureEmail=" . $email;
    			$delimiter = ",";
    		}
    	}
    	$command = "cd " . $this->bbClientPath;
    	$command .= " && sh " . $this->bbScriptName . " -a build -p " . $this->projectDirectory . " -b " . $batchDirectory;
    	if (!empty($overrides))
    	{
    		$command .= " -batchprop " . $overrides;
    	}
    	Logger::log($command);
    	//Create the batch file.
    	$output = shell_exec($command);
    	 
    	
    	//Change the permissions so that the files can be deleted by either acornadm or apache
    	exec("chmod -R o=rwx " . $this->projectDirectory . "/" . $batchDirectory);
    	//Change the permissions so that the files can be deleted by either acornadm or apache
    	exec("chmod -R o=rwx " . $this->projectDirectory . "/_aux/" . $batchDirectory);
    	 
    	$fileexists = file_exists($this->projectDirectory . "/" . $batchDirectory . "/batch.xml");
    	
    	//Validate the descriptors were created
    	$handle = opendir($this->projectDirectory . "/" . $batchDirectory);
    	//Loop through all of the object files
    	while (false !== ($entry = readdir($handle))) {
    		if ($entry != "batch.xml" && $entry != "." && $entry != "..")
    		{
    			$fileexists = $fileexists && file_exists($this->projectDirectory . "/" . $batchDirectory . "/" . $entry . "/descriptor.xml");
    		}
    	}
    	closedir($handle);
    	if (!$fileexists)
    	{
    		throw new DRSServiceException($output);
    	}
    	 
    	//Check to see if the file was created properly
    	return $fileexists;
    }
    
	/**
	 * @return array
	 */
	public function getFailEmails()
	{
		return $this->failEmails;
	}
	
	/**
	 * @return array
	 */
	public function getSuccessEmails()
	{
		return $this->successEmails;
	}
	
	/**
	 * @param array $failEmails
	 */
	public function setFailEmails(array $failEmails)
	{
		$this->failEmails = $failEmails;
	}
	
	/**
	 * @param array $successEmails
	 */
	public function setSuccessEmails(array $successEmails)
	{
		$this->successEmails = $successEmails;
	}

	public function addSuccessEmail($email)
	{
		array_push($this->successEmails, $email);
	}
	
	public function addFailEmail($email)
	{
		array_push($this->failEmails, $email);
	}
	
	public function removeSuccessEmail($email)
	{
		$key = array_search($email, $this->successEmails);
		if ($key !== FALSE)
		{
			unset($this->successEmails[$key]);
		}
	}
	
	public function removeFailEmail($email)
	{
		$key = array_search($email, $this->failEmails);
		if ($key !== FALSE)
		{
			unset($this->failEmails[$key]);
		}
	}
	
	/**
	 * 
	 * @param String $sourcePath - The path to the temporary directory containing the files.
	 * @param array $sourcefiles - The array of files (key = temp directory name, value = name for destination)
	 * @param String $projectName - The name of the project
	 * @param String $batchnameforimages - The name for the batch
	 * @param DRSDropperConfig $config
	 * @param boolean $tryCopyAgain - If the copy function fails, this gives it a second chance.
	 * @throws DRSServicException
	 */
	public static function prepareTemplateDirectory($sourcePath, array $sourcefiles, $projectName, DRSDropperConfig $config, $tryCopyAgain = TRUE)
	{
		$projectDirectoryPath = self::createProjectDirectory($config, $projectName);
		
		//Copy the source files to the project/template/imagedeliverable directory
		$projectDirectoryTemplatePath = $projectDirectoryPath . "/" . self::PROJECT_TEMPLATE_IMAGE_DIR;
		$mappingFile = $projectDirectoryPath . "/" . $config->getAuxillaryDirectoryName() . "/template/mapping.txt";
		//Open with 'w' so it clears out old data.
		$mappingFilePointer = fopen($mappingFile, 'w');
		foreach ($sourcefiles as $sourcefile => $destinationfilename)
		{
			copy($sourcePath . "/" . $sourcefile, $projectDirectoryTemplatePath . "/" . $destinationfilename);
			//Add the info to the mapping file
			$objectname = substr($destinationfilename, 0, strrpos($destinationfilename, "."));
			//The mapping string is: relative_file_path,file_OwnerSuppliedName,PDS_sequence number(optional),object_OwnerSuppliedName(optional)
			$mappingString = "imagedeliverable/" . $destinationfilename . "," . $destinationfilename . ",," . $objectname . "\n";
			fwrite($mappingFilePointer, $mappingString);
		}
		fclose($mappingFilePointer);
		
		//Make sure the files copied properly
		$projectDirectoryFiles = scandir($projectDirectoryTemplatePath);
		//Take two away from the projectDirectoryFiles since they will include '.' and '..'
		//If the counts are not the same and this is the second try, throw an exception
		if (count($sourcefiles) != count($projectDirectoryFiles) - 2)
		{
			//Try a second time?
			if ($tryCopyAgain)
			{
				self::prepareTemplateDirectory($sourcePath, $sourcefiles, $projectDirectoryPath, $config, FALSE);
			}
			//After the second attempt, delete the files and warn the user.
			else
			{
				//Delete the source files and the project/template files.
				self::deleteSourceFiles($sourcePath);
				self::deleteProjectTemplateFiles($projectDirectoryTemplatePath);
				throw new DRSServicException("Files were not copied from the temporary directory to the DRS staging area properly.  Please upload again.");
			}
		}
		
		//Delete the sourcefiles from the source path
		$deleted = self::deleteSourceFiles($sourcePath);
		if (!$deleted)
		{
			throw new DRSServicException("Could not delete the source files.");
		}
	}
	
	
	/**
	 * Delete the files in the temporary directory
	 * @param String $sourcePath
	 * @return boolean - true if the files were deleted, false otherwise
	 */
	public static function deleteSourceFiles($sourcePath)
	{
		$command = "rm " . $sourcePath . "/*";
		$output = exec($command);
		$sourceFiles = scandir($sourcePath);
		//Should be 2 because of '.' and '..'
		return count($sourceFiles) == 2; 
	}
	
	/**
	 * Delete the files in the project template directory
	 * @param String $projectTemplateDirectory
	 * @return boolean - true if the files were deleted, false otherwise
	 */
	public static function deleteProjectTemplateFiles($projectTemplateDirectory)
	{
		$command = "rm " . $projectTemplateDirectory . "/*";
		$output = exec($command);
		$files = scandir($projectTemplateDirectory);
		//Should be 2 because of '.' and '..'
		return count($files) == 2;
	}
	
	/**
	 * This creates the a project directory under the staging file directory.
	 * This is necessary for DRS2 since multiple users may be running the buildtemplate
	 * command at the same time.  
	 * @param DRSDropperConfig $config
	 * @param String $projectName
	 * @return boolean - TRUE if the project directory exists after the method AND the configuration file 
	 * created properly.
	 * @throws DRSServiceException
	 */
	private static function createProjectDirectory(DRSDropperConfig $config, $projectName)
	{
		$projectDir = $config->getStagingFileDirectory() . "/" . $projectName;
		$projectTemplateDir = $projectDir . "/" . self::PROJECT_TEMPLATE_IMAGE_DIR;
		$projectdirexists = file_exists($projectTemplateDir);
		//If the project does not yet exist, create the directory and the configuration file.
		//Otherwise, just return the directory path.
		if (!$projectdirexists)
		{
			mkdir($projectTemplateDir, 0777, TRUE);

			//Change the permissions so that the files can be deleted by either acornadm or apache
			exec("chmod -R o=rwx " . $projectDir);
			
			$projectdirexists = file_exists($projectTemplateDir);
			//If the file was created properly, create the project.conf file from the project.conf.template
			if ($projectdirexists)
			{
				self::createProjectConfigurationFile($config, $projectName);
				self::createProjectAuxDirectories($config, $projectDir);
			}
			else
			{
				throw new DRSServiceException("The project directory could not be created " . $projectDir);
			}
		}
			
		return $projectDir;
	}

	
	/**
	 * 
	 * @param DRSDropperConfig $config
	 * @param String $projectName
	 * @return boolean - TRUE if the project _aux directory exists after the method
	 */
	private static function createProjectAuxDirectories(DRSDropperConfig $config, $projectDirectoryPath)
	{
		$projectAuxDirectory = $projectDirectoryPath . "/" . $config->getAuxillaryDirectoryName() . "/template";
		if (!file_exists($projectAuxDirectory))
		{
			mkdir($projectAuxDirectory, 0777, TRUE);
			//Change the permissions so that the files can be deleted by either acornadm or apache
			exec("chmod -R o=rwx " . $projectDirectoryPath . "/" . $config->getAuxillaryDirectoryName());
				
		}
		return file_exists($projectAuxDirectory);
	}
	
	/**
	 * Copy the project.conf.template to the project directory and modify the projectName variable.
	 * @param DRSDropperConfig $config
	 * @param String $projectName
	 * @throws DRSServiceException
	 */
	private static function createProjectConfigurationFile(DRSDropperConfig $config, $projectName)
	{
		$projectConfTemplate = $config->getStagingFileDirectory() . "/" . self::PROJECT_CONF_TEMPLATE;
		$projectDir = $config->getStagingFileDirectory() . "/" . $projectName;
		$projectConf = $projectDir . "/" . self::PROJECT_CONF;
		
		//Copy the project.conf.template file to the project
		$copy = copy($projectConfTemplate, $projectConf);
		
		//If it didn't copy properly, then  throw an exception
		if (!$copy || !file_exists($projectConf))
		{
			throw new DRSServiceException("Could not copy project.conf.template to " . $projectName);
		}
		
		//Replace the project name key
		$projectconffile = file_get_contents($projectConf);
		$projectconffile = str_replace(self::PROJECT_NAME_VARIABLE, $projectName, $projectconffile);
		file_put_contents($projectConf, $projectconffile);
	}


} /* end of class BatchBuilderAssistant */

?>