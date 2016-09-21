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
 * RepositorySelectForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class RepositorySelectForm extends ACORNForm
{
	private $includeinactive = FALSE;
	private $includeblank = FALSE;
	
	public function __construct($options = NULL)
	{
		if (!is_null($options) && is_array($options))	
		{
			if (isset($options[ACORNSelect::INCLUDE_INACTIVE]))
			{
				$this->includeinactive = $options[ACORNSelect::INCLUDE_INACTIVE];
				unset($options[ACORNSelect::INCLUDE_INACTIVE]);
			}
			
			if (isset($options[ACORNSelect::INCLUDE_BLANK]))
			{
				$this->includeblank = $options[ACORNSelect::INCLUDE_BLANK];
				unset($options[ACORNSelect::INCLUDE_BLANK]);
			}
		}
		parent::__construct($options);
	}

	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$repositoryselect = new ACORNSelect('repositoryselect');
    	$repositoryselect->setLabel('Repository');
		$repositoryselect->setName('repositoryselect');
		$inact = 0;
		$blank = 0;
		if ($this->includeinactive)
		{
			$inact = 1;
		}
    	if ($this->includeblank)
		{
			$blank = 1;
		}
		$repositoryselect->setAttrib('onclick', 'loadLocations(\'repositoryselect\',' . $inact . ', 1,' . $blank . ')');
		$repositoryselect->setAttrib('onchange', 'clearList(\'departmentselect\'); loadDepartments(' . $inact . ',1)');
		$this->addElement($repositoryselect);
		
		$departmentselect = new ACORNSelect('departmentselect');
    	$departmentselect->setLabel('Department');
		$departmentselect->setName('departmentselect');
		$departmentselect->setAttrib('onclick', 'loadDepartments(' . $inact . ',1)');
		$this->addElement($departmentselect);
		
		$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'customelements/repositoryselect.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
    public function addRepositoryAttribute($attribname, $value)
    {
    	$repositoryselect = $this->getElement('repositoryselect');
    	if ($attribname == 'onclick' || $attribname == 'onchange')
    	{
    		$value = $repositoryselect->getAttrib($attribname) . '; ' . $value;
    	}
		$repositoryselect->setAttrib($attribname, $value);
    }
    
    /*
     * Sets whether the repository is required.
     * 
     * @param boolean 
     */
    public function setRequired($required)
    {
    	$this->getElement('repositoryselect')->setRequired($required);
    	if ($required)
    	{
    		$this->getElement('repositoryselect')->setLabel('Repository*');
    	}
    	else
    	{
    		$this->getElement('repositoryselect')->setLabel('Repository');
    	}
    }
    
	/*
     * Overrides the Zend_Form_Element method to add the multi option
     * if it doesn't exist in the list currently.
     * 
     * @param string name - the element name
     * @param value - the value to look for.
     */
    public function setDefault($name, $value)
    {
    	if ($name == 'repositoryselect')
    	{
    		$options = $this->getElement($name)->getMultiOptions();
    		//If the value isn't in the options, then add it
    		if (!array_key_exists($value, $options))
    		{
    			$location = LocationDAO::getLocationDAO()->getLocation($value);
    			if (!is_null($location))
    			{
    				$this->getElement($name)->addMultiOption($value, $location->getLocation());
    				//Remove the default blank option
    				$this->getElement($name)->removeMultiOption('');
    			}
    		}
    	}
    	elseif ($name == 'departmentselect')
    	{
    		$options = $this->getElement($name)->getMultiOptions();
    		//If the value isn't in the options, then add it
    		if (!array_key_exists($value, $options))
    		{
    			$department = DepartmentDAO::getDepartmentDAO()->getDepartment($value);
    			if (!is_null($department))
    			{
    				$this->getElement($name)->addMultiOption($value, $department->getDepartmentName());
    				//Remove the default blank option
    				$this->getElement($name)->removeMultiOption('');
    			}
    		}
    	}
    	
    	parent::setDefault($name, $value);
    }
}
?>