<?php
/**
 * CustomTextForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class CustomTextForm extends Zend_Form
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$lists = array(
    		'' => '',
    		'Preservation' => 'Preservation Recommendations (Level 1)',
    		'Dependency' => 'Preservation Recommendations (Level 2)',
    		'Condition' => 'Proposal Condition',
    		'Description' => 'Proposal Description',
    		'ProposedTreatment' => 'Proposal Treatment',
    		'Treatment' => 'Report Treatment'
    	);
    	$listtypeselect = new ACORNSelect('listtypeselect');
    	$listtypeselect->setLabel('Custom Text Type');
		$listtypeselect->setName('listtypeselect');
		$listtypeselect->setMultiOptions($lists);
		$listtypeselect->setAttrib('onchange', 'loadList(this.value)');
		$this->addElement($listtypeselect);
    }
    
    
}
?>