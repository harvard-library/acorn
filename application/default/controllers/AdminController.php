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
 * AdminController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
	

class AdminController extends Zend_Controller_Action
{
	private $editListsForm;
	private $customTextForm;

	private function getEditListsForm()
	{
		if (is_null($this->editListsForm))
		{
			$this->editListsForm = new EditListsForm();
		}
		return $this->editListsForm;
	}
	
	private function getCustomTextForm()
	{
		if (is_null($this->customTextForm))
		{
			$this->customTextForm = new CustomTextForm();
		}
		return $this->customTextForm;
	}
	
	public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
        {
            $this->_helper->redirector('index', 'index');
        }
    }
	
	/**
	 * The default action - show the home page
	 */
    public function indexAction() 
    {
        // TODO Auto-generated {0}::indexAction() default action
    }
    
    public function customtextAction()
    {
    	$this->view->form = $this->getCustomTextForm();
    }
    
    public function editlistsAction()
    {
    	$this->view->form = $this->getEditListsForm();
    }
    
    public function exporttoexcelAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$this->_helper->layout->disableLayout();
    
    	$listname = $this->getRequest()->getParam('listname', '');
    	$data = $this->getExportSearchResults($listname);
    	$fullfilename = "";
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$personid = $identity[PeopleDAO::PERSON_ID];
    	$date = date(ACORNConstants::$DATE_FILENAME_FORMAT);
    
    	$filename = $personid . '_' . $date . '.csv';
    	$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
    
    	$fullfilename = $config->getReportsDirectory() . '/cvsreports/' . $filename;
    	$filehandler = fopen($fullfilename, 'w');

    	if (!empty($data))
    	{
    		$firstelement = current($data);
    		$headers = array_keys($firstelement);
    		fputcsv($filehandler, $headers);
    		foreach ($data as $result)
    		{
    			fputcsv($filehandler, $result);
    		}
    	}
    	fclose($filehandler);
    
    	echo $filename;
    
    }
    
    /**
     * Finds the search results
    
     * @return array
     */
    private function getExportSearchResults($listname)
    {
    	$values = array();
    	if (!empty($listname))
    	{
    		switch ($listname)
    		{
    			case "Departments":
    				$values = DepartmentDAO::getDepartmentDAO()->getDepartmentsForSelect(TRUE);		
    				break;
    			case "Formats":
    				$values = FormatDAO::getFormatDAO()->getAllFormatsForSelect(TRUE);
    				break;
    			case "Importances":
    				$values = ImportanceDAO::getImportanceDAO()->getAllImportancesForSelect(TRUE);
    				break;
    			case "Locations":
    				$values = LocationDAO::getLocationDAO()->getAllLocationsForSelect(TRUE);
    				break;	
    			case "People":
    				$values = PeopleDAO::getPeopleDAO()->getAllPeopleForSelect(NULL, TRUE);
					break;
    			case "Projects":
    				$values = ProjectDAO::getProjectDAO()->getAllProjectsForSelect(TRUE);
    				break;
    			case "Purposes":
    				$values = PurposeDAO::getPurposeDAO()->getAllPurposesForSelect(TRUE);
    				break;
    			case "WorkTypes":
    				$values = WorkTypeDAO::getWorkTypeDAO()->getAllWorkTypesForSelect(TRUE);
    				break;
    		}
    	}
    	return $values;
    }
    
    public function promptforsaveAction()
    {
    	//Don't display a new view, only the search area is being updated.
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout for the search area (the
    	//default layout is only needed for the entire form).
    	$this->_helper->layout->disableLayout();
    
    	$filename = $this->getRequest()->getParam('filename');
    	if (isset($filename))
    	{
    		//$fc = Zend_Controller_Front::getInstance();
    		//$initializer = $fc->getPlugin('Initializer');
    		$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
    		$path = $config->getReportsDirectory() . '/cvsreports/' . $filename;
    		 
    		if (!is_file($path)) { die("<b>404 File not found!</b> " . $path); }
    
    		//Gather relevent info about file
    		$len = filesize($path);
    		$filename = basename($path);
    			
    		//Begin writing headers
    		header("Pragma: public");
    		header("Expires: 0");
    		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    		header("Cache-Control: public");
    		header("Content-Description: File Transfer");
    
    		//Use the switch-generated Content-Type
    		header("Content-Type: application/force-download");
    
    		//Force the download
    		$header="Content-Disposition: attachment; filename=".$filename.";";
    		header($header );
    		header("Content-Transfer-Encoding: binary");
    		header("Content-Length: ".$len);
    		readfile($path);
    	}
    }
}
?>