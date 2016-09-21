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
 * LocationSelectForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class LocationSelectForm extends ACORNForm
{
	const LOCATION_SELECT_NAME = "LocationName";
	const IS_REPOSITORY_SEARCH = "RepositorySearch";
	
	private $includeinactive = FALSE;
	private $includeblank = FALSE;
	private $repositorysearch = FALSE;
	
	private $locationname;
	
	public function __construct($options)
	{
		if (is_array($options))	
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
			
			if (isset($options[self::LOCATION_SELECT_NAME]))
			{
				$this->locationname = $options[self::LOCATION_SELECT_NAME];
				unset($options[self::LOCATION_SELECT_NAME]);
			}
			
			if (isset($options[self::IS_REPOSITORY_SEARCH]))
			{
				$this->repositorysearch = $options[self::IS_REPOSITORY_SEARCH];
				unset($options[self::IS_REPOSITORY_SEARCH]);
			}
		}
		elseif (!empty($options))
		{
			$this->locationname = $options;
			$options = NULL;
		}
		parent::__construct($options);
	}
	
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$locationselect = new ACORNSelect($this->locationname);
    	$locationselect->setName($this->locationname);
    	$inact = 0;
		$blank = 0;
		$repository = 0;
		if ($this->includeinactive)
		{
			$inact = 1;
		}
    	if ($this->includeblank)
		{
			$blank = 1;
		}
		if ($this->repositorysearch)
		{
			$repository = 1;
		}
		$locationselect->setAttrib('onclick', 'loadLocations(\'' . $this->locationname . '\',' . $inact . ',' . $repository . ',' . $blank . ')');
		$this->addElement($locationselect);
		
		$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'customelements/locationselect.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
	}
    
    public function addSelectAttrib($eventname, $function)
    {
    	$this->getElement($this->locationname)->setAttrib($eventname, $function);
    }
    
    public function setLabel($label)
    {
    	$this->getElement($this->locationname)->setLabel($label);
    }
    
    /*
     * Sets whether the repository is required.
     * 
     * @param boolean 
     */
    public function setRequired($required)
    {
    	$this->getElement($this->locationname)->setRequired($required);
    	if ($required)
    	{
    		$label = $this->getElement($this->locationname)->getLabel();
    		if (!strpos($label, '*'))
    		{
    			$this->getElement($this->locationname)->setLabel($label . '*');
    		}
    	}
    	else
    	{
    		$label = $this->getElement($this->locationname)->getLabel();
    		str_replace('*', '', $label);
    		$this->getElement($this->locationname)->setLabel($label);
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
    	if ($name == $this->locationname)
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
    	parent::setDefault($name, $value);
    }
}
?>