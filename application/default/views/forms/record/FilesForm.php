<?php
/**
 * FilesForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class FilesForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hiddenfilepkid = new Zend_Form_Element_Hidden('hiddenfilepkid');
    	$hiddenfilepkid->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenfilepkid);
    	
    	$nondigitalcheck = new Zend_Form_Element_Checkbox('nondigitalcheck');
        $nondigitalcheck->setLabel('Non-digital images exist');
        $nondigitalcheck->setAttribs(array('class' => 'checkboxinput'));
        $nondigitalcheck->setDecorators(Decorators::$CHECKBOX_DECORATORS);
    	$this->addElement($nondigitalcheck);
    	
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
   			'ViewScript', array('viewScript' => 'record/recordfiles.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
    
}
?>