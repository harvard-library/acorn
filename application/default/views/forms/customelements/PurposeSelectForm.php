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
 * PurposeSelectForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
class PurposeSelectForm extends ACORNForm 
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
    	$purposeselect = new ACORNSelect('purposeselect');
    	$purposeselect->setLabel('Purpose');
		$purposeselect->setName('purposeselect');
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
		$purposeselect->setAttrib('onclick', 'loadPurposes(' . $inact . ',' . $blank . ')');
		$this->addElement($purposeselect);
		
		$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'customelements/purposeselect.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
	/*
     * Sets whether the purpose is required.
     * 
     * @param boolean 
     */
    public function setRequired($required)
    {
    	$this->getElement('purposeselect')->setRequired($required);
    	
    	if ($required)
    	{
    		$label = $this->getElement('purposeselect')->getLabel();
    		if (!strpos($label, '*'))
    		{
    			$this->getElement('purposeselect')->setLabel($label . '*');
    		}
    	}
    	else
    	{
    		$label = $this->getElement('purposeselect')->getLabel();
    		str_replace('*', '', $label);
    		$this->getElement('purposeselect')->setLabel($label);
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
    	if ($name == 'purposeselect')
    	{
    		$options = $this->getElement($name)->getMultiOptions();
    		//If the value isn't in the options, then add it
    		if (!array_key_exists($value, $options))
    		{
    			$purpose = PurposeDAO::getPurposeDAO()->getPurpose($value);
    			if (!is_null($purpose))
    			{
    				$this->getElement($name)->addMultiOption($value, $purpose->getPurpose());
    				//Remove the default blank option
    				$this->getElement($name)->removeMultiOption('');
    			}
    		}
    	}
    	parent::setDefault($name, $value);
    }
}
?>