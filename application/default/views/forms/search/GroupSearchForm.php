<?php
/**
 * GroupSearchForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
class GroupSearchForm extends ACORNForm 
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$groupselectform = new GroupSelectForm(array(ACORNSelect::INCLUDE_INACTIVE => TRUE, ACORNSelect::INCLUDE_BLANK => FALSE));
    	$groupselectform->setName('groupselectform');
    	$this->addSubForm($groupselectform, 'groupselectform');
    }
}
?>