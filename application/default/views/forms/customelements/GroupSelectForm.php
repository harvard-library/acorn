<?php
/**
 * GroupSelectForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class GroupSelectForm extends ACORNForm 
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
    	$groupselect = new ACORNSelect('groupselect');
    	$groupselect->setLabel('Group');
		$groupselect->setName('groupselect');
		$groupselect->setAttrib('onclick', 'loadGroups(' . $inact . ',' . $blank . ')');
		$this->addElement($groupselect);
		
		$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'customelements/groupselect.phtml'),
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
    	if ($name == 'groupselect')
    	{
    		$options = $this->getElement($name)->getMultiOptions();
    		//If the value isn't in the options, then add it
    		if (!array_key_exists($value, $options))
    		{
    			$group = GroupDAO::getGroupDAO()->getGroup($value);
    			if (!is_null($group))
    			{
    				$this->getElement($name)->addMultiOption($value, $group->getGroupName());
    				//Remove the default blank option
    				$this->getElement($name)->removeMultiOption('');
    			}
    		}
    	}
    	parent::setDefault($name, $value);
    }
}
?>