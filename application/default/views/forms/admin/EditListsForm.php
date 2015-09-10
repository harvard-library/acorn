<?php
/**
 * EditListsForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class EditListsForm extends ACORNForm 
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	if ($identity[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_ADMIN)
    	{
	    	$lists = array(
	    		'' => '',
	    		'Departments' => 'Departments',
	    		'Formats' => 'Formats',
	    		'Importances' => 'Importances',
	    		'Locations' => 'Locations',
	    		'People' => 'People',
	    		'Projects' => 'Projects',
	    		'Purposes' => 'Purposes',
	    		'WorkTypes' => 'Work Types'
	    	);
    	}
    	else 
    	{
    		$lists = array('Projects' => 'Projects');
    	}
    	$listtypeselect = new ACORNSelect('listtypeselect');
    	$listtypeselect->setLabel('List to Edit');
		$listtypeselect->setName('listtypeselect');
		$listtypeselect->setMultiOptions($lists);
		if ($identity[PeopleDAO::ACCESS_LEVEL] != PeopleDAO::ACCESS_LEVEL_ADMIN)
    	{
			$listtypeselect->setValue('Projects');
    	}
		$listtypeselect->setAttrib('onchange', 'loadLists(this.value)');
		$this->addElement($listtypeselect);
    }
    
    
}
?>