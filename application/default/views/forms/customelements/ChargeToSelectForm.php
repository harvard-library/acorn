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
 * ChargeToSelectForm
 *
 * @author vcrema
 * Created July 31, 2012
 *
 */
 
 class ChargeToSelectForm extends ACORNForm 
{
	
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$chargetoselect = new ACORNSelect('chargetoselect');
    	$chargetoselect->setLabel('Charge To');
		$chargetoselect->setName('chargetoselect');
		$chargetoselect->setAttrib('onclick', 'loadChargeTo()');
		$this->addElement($chargetoselect);
		
		$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'customelements/chargetoselect.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
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
    	if ($name == 'chargetoselect')
    	{
    		$options = $this->getElement($name)->getMultiOptions();
    		//If the value isn't in the options, then add it
    		if (!array_key_exists($value, $options))
    		{
	    		if (is_numeric($value) && $value > 0)
	    		{
		    		$chargeto = LocationDAO::getLocationDAO()->getLocation($value)->getLocation();
		    	}
		    	else
		    	{
		    		$chargeto = $value;
		    	}
    			if (!empty($chargeto))
    			{
    				$this->getElement($name)->addMultiOption($value, $chargeto);
    				//Remove the default blank option
    				$this->getElement($name)->removeMultiOption('');
    			}
    		}
    	}
    	parent::setDefault($name, $value);
    }
    
	/*
     * Sets whether the charge to item is required.
     * 
     * @param boolean 
     */
    public function setRequired($required)
    {
    	$this->getElement('chargetoselect')->setRequired($required);
    	if ($required)
    	{
    		$label = $this->getElement('chargetoselect')->getLabel();
    		if (!strpos($label, '*'))
    		{
    			$this->getElement('chargetoselect')->setLabel($label . '*');
    		}
    	}
    	else
    	{
    		$label = $this->getElement('chargetoselect')->getLabel();
    		str_replace('*', '', $label);
    		$this->getElement('chargetoselect')->setLabel($label);
    	}
    }
}
?>