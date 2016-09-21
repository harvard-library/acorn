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
 * AccessLevelSelectForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class AccessLevelSelectForm extends ACORNForm
{
	private $label = 'Login As';
	
	public function __construct($options = NULL)
	{
		if (!is_null($options) && is_array($options))	
		{
			if (isset($options['AccessLevelLabel']))
			{
				$this->label = $options['AccessLevelLabel'];
				unset($options['AccessLevelLabel']);
			}
		}
		parent::__construct($options);
	}
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$usertypeselect = new ACORNSelect('usertypeselect');
    	$usertypeselect->setLabel($this->label . '*');
		$usertypeselect->setName('usertypeselect');
		$usertypeselect->setRequired(TRUE);
		$usertypeselect->setMultiOptions(
			array(PeopleDAO::ACCESS_LEVEL_ADMIN => PeopleDAO::ACCESS_LEVEL_ADMIN,
				PeopleDAO::ACCESS_LEVEL_CURATOR => PeopleDAO::ACCESS_LEVEL_CURATOR,
				PeopleDAO::ACCESS_LEVEL_REGULAR => PeopleDAO::ACCESS_LEVEL_REGULAR,
				PeopleDAO::ACCESS_LEVEL_REPOSITORY => PeopleDAO::ACCESS_LEVEL_REPOSITORY,
				PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN => PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN
		));
		$usertypeselect->setAttrib('onchange', 'userTypeChanged(this.value)');
		$this->addElement($usertypeselect);
		
		$repositoryselect = new ACORNSelect('repositoryselect');
    	$repositoryselect->setLabel('Repository*');
		$repositoryselect->setName('repositoryselect');
		$repositoryselect->setRequired(TRUE);
		$repositoryselect->setAttrib('onclick', 'loadLocations(\'repositoryselect\',0, 1, 0)');
		$this->addElement($repositoryselect);
		
		$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'customelements/accesslevelselect.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
    /*
     * Sets whether the repository and access level are required.
     * 
     * @param boolean 
     */
    public function setRequired($required)
    {
    	$this->getElement('repositoryselect')->setRequired($required);
    	$this->getElement('usertypeselect')->setRequired($required);
    	if ($required)
    	{
    		$this->getElement('repositoryselect')->setLabel('Repository*');
    		$this->getElement('usertypeselect')->setLabel($this->label . '*');
    	}
    	else
    	{
    		$this->getElement('repositoryselect')->setLabel('Repository');
    		$this->getElement('usertypeselect')->setLabel($this->label);
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
    			$repository = LocationDAO::getLocationDAO()->getLocation($value);
    			if (!is_null($repository))
    			{
    				$this->getElement($name)->addMultiOption($value, $repository->getLocation());
    				//Remove the default blank option
    				$this->getElement($name)->removeMultiOption('');
    			}
    		}
    	}
    	parent::setDefault($name, $value);
    }
}
?>