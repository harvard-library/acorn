<?php
/**
 * PersonSelectForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class PersonSelectForm extends ACORNForm 
{
	const INCLUDE_CONTRACTORS = "IncludeContractors";
	const PERSON_SELECT_NAME = "PersonSelectName";
	
	private $includeinactive = FALSE;
	private $includeblank = FALSE;
	private $includecontractors = FALSE;
	
	private $personselectname;
	
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
			
			if (isset($options[self::INCLUDE_CONTRACTORS]))
			{
				$this->includecontractors = $options[self::INCLUDE_CONTRACTORS];
				unset($options[self::INCLUDE_CONTRACTORS]);
			}
			
			if (isset($options[self::PERSON_SELECT_NAME]))
			{
				$this->personselectname = $options[self::PERSON_SELECT_NAME];
				unset($options[self::PERSON_SELECT_NAME]);
			}
		}
		elseif (!empty($options))
		{
			$this->personselectname = $options;
			$options = NULL;
		}
		parent::__construct($options);
	}

	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$personselect = new ACORNSelect($this->personselectname);
    	$personselect->setLabel('Person');
		$personselect->setName($this->personselectname);
		$inact = 0;
		$blank = 0;
		$contractors = 0;
		if ($this->includeinactive)
		{
			$inact = 1;
		}
    	if ($this->includeblank)
		{
			$blank = 1;
		}
    	if ($this->includecontractors)
		{
			$contractors = 1;
		}
		$personselect->setAttrib('onclick', 'loadStaff(\'' . $this->personselectname . '\',' . $inact . ',' . $blank . ',' . $contractors . ')');
		$this->addElement($personselect);
		
		$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'customelements/personselect.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
    /*
     * Sets the role types that will be used for this select.
     * 
     * @param <optional> array - an array of role types to populate the people from
     */
    public function setRoleTypes(array $roletypes = NULL)
    {
    	if (!is_null($roletypes) && in_array('Curator', $roletypes))
    	{
    		$personselect = $this->getElement($this->personselectname);
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
			$personselect->setAttrib('onclick', 'loadCurators(\'' . $this->personselectname . '\',' . $inact . ',' . $blank . ')');
    	}
    }
    
	public function setLabel($label)
    {
    	$this->getElement($this->personselectname)->setLabel($label);
    }
    
    /*
     * Sets whether the person is required.
     * 
     * @param boolean 
     */
    public function setRequired($required)
    {
    	$this->getElement($this->personselectname)->setRequired($required);
    	if ($required)
    	{
    		$label = $this->getElement($this->personselectname)->getLabel();
    		if (!strpos($label, '*'))
    		{
    			$this->getElement($this->personselectname)->setLabel($label . '*');
    		}
    	}
    	else
    	{
    		$label = $this->getElement($this->personselectname)->getLabel();
    		str_replace('*', '', $label);
    		$this->getElement($this->personselectname)->setLabel($label);
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
    	if ($name == $this->personselectname)
    	{
    		$options = $this->getElement($name)->getMultiOptions();
    		//If the value isn't in the options, then add it
    		if (!array_key_exists($value, $options))
    		{
    			$person = PeopleDAO::getPeopleDAO()->getPerson($value);
    			if (!is_null($person))
    			{
    				$this->getElement($name)->addMultiOption($value, $person->getDisplayName());
    				//Remove the default blank option
    				$this->getElement($name)->removeMultiOption('');
    			}
    		}
    	}
    	parent::setDefault($name, $value);
    }
    
    public function setAttrib($name, $value)
    {
    	$existingattrib = $this->getElement($this->personselectname)->getAttrib($name);
    	if (!empty($existingattrib))
    	{
    		$existingattrib .= "; " . $value;
    	}
    	else 
    	{
    		$existingattrib = $value;
    	}
    	$this->getElement($this->personselectname)->setAttrib($name, $existingattrib);
    	
    }
}
?>