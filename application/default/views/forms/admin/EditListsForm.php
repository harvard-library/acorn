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
 * EditListsForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
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