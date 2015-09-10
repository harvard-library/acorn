<?php
/**
 * FormatSelectForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
class FormatSelectForm extends ACORNForm 
{
	const FORMAT_SELECT_NAME = "FormatName";
	private $includeinactive = FALSE;
	private $includeblank = FALSE;
	private $name = "formatselect";
	
	public function __construct($options)
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
			if (isset($options[self::FORMAT_SELECT_NAME]))
			{
				$this->name = $options[self::FORMAT_SELECT_NAME];
				unset($options[self::FORMAT_SELECT_NAME]);
			}
		}
		elseif (!empty($options))
		{
			$this->name = $options;
			$options = NULL;
		}
		parent::__construct($options);
	}
	
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$formatselect = new ACORNSelect($this->name);
    	$formatselect->setLabel('Format');
		$formatselect->setName($this->name);
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
		$formatselect->setAttrib('onclick', 'loadFormats(\'' . $this->name . '\',' . $inact . ',' . $blank . ')');
		$this->addElement($formatselect);
		
		$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'customelements/formatselect.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }

    /*
     * Sets whether the repository is required.
     * 
     * @param boolean 
     */
    public function setRequired($required)
    {
    	$this->getElement($this->name)->setRequired($required);
    	if ($required)
    	{
    		$this->getElement($this->name)->setLabel('Format*');
    	}
    	else
    	{
    		$this->getElement($this->name)->setLabel('Format');
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
    	if ($name == $this->name)
    	{
    		$options = $this->getElement($name)->getMultiOptions();
    		//If the value isn't in the options, then add it
    		if (!array_key_exists($value, $options))
    		{
    			$format = FormatDAO::getFormatDAO()->getFormat($value);
    			if (!is_null($format))
    			{
    				$this->getElement($name)->addMultiOption($value, $format->getFormat());
    				//Remove the default blank option
    				$this->getElement($name)->removeMultiOption('');
    			}
    		}
    	}
    	parent::setDefault($name, $value);
    }
}
?>