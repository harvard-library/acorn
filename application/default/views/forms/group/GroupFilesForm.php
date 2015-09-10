<?php
/**
 * GroupFilesForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class GroupFilesForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hiddenfilepkid = new Zend_Form_Element_Hidden('hiddenfilepkid');
    	$hiddenfilepkid->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenfilepkid);
    	 
    	$filesearchinput = new ACORNTextField('filesearchinput');
		$filesearchinput->setLabel('Find File');
		$filesearchinput->setName('filesearchinput');
		$filesearchinput->setAttribs(array('onkeypress' => 'updateFilesTable(event);'));
		$this->addElement($filesearchinput);
		
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'group/groupfiles.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
}
?>