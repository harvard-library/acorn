<?php
/**
 * ACORNForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
abstract class ACORNForm extends Zend_Form
{   
    /**
     * Sets all values to read only
     * 
     */
    public function disable(array $excludeitems = NULL)
    {
    	$subforms = $this->getSubForms();
    	foreach ($subforms as $subform)
    	{
    		if (is_null($excludeitems) || !in_array($subform->getName(), $excludeitems))
    		{
    			$subform->disable($excludeitems);
    		}
    	}
    	
    	$elements = $this->getElements();
    	foreach ($elements as $element)
    	{
    		if ($element->getType() != 'Zend_Form_Element_Hidden'
    			&& (is_null($excludeitems) || !in_array($element->getName(), $excludeitems)))
    		{
    			$element->setAttrib('disabled', 'disabled');
    		}
    	}
    }
    
	/**
     * Removes the disabled status
     * 
     */
    public function enable()
    {
    	$subforms = $this->getSubForms();
    	foreach ($subforms as $subform)
    	{
    		$subform->enable();
    	}
    	
    	$elements = $this->getElements();
    	foreach ($elements as $element)
    	{
    		$attribs = $element->getAttribs();
    		unset($attribs['disabled']);
    		$element->setAttribs($attribs);
    	}
    }
    
    public function getValues($suppressarraynotation = FALSE)
 	{
 		$values = parent::getValues($suppressarraynotation);
 		
 		$subforms = $this->getSubForms();
 		foreach ($subforms as $subform)
 		{
 			$values = array_merge($values, $subform->getValues());
 		}
 		return $values;
 	}
    
	public function isValid($values)
    {
    	$isvalid = parent::isValid($values);
    	
    	if (!$isvalid)
    	{
    		$this->addErrorMessage('Changes were not saved.  Please correct the errors in the form below.');
    	}
    	return $isvalid;
    }
    
    /*
     * Returns only those values that have data.
     * 
     * @return array
     */
	public function getNonEmptyValues($values)
    {
    	$returnvalues = array();
    	foreach ($values as $key => $value)
    	{
    		if (!empty($value))
    		{
    			$returnvalues[$key] = $value;
    		}
    	}
    	return $returnvalues;
    }
}
?>