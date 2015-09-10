<?php
/**
 * HOLLISInputForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class HOLLISInputForm extends ACORNForm 
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hollisnumberinput = new NumberTextField('hollisnumberinput');
		$hollisnumberinput->setName('hollisnumberinput');
		$hollisnumberinput->setLabel('HOLLIS Number');
		$hollisnumberinput->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->addElement($hollisnumberinput);
		
		$gotohollisbutton = new Zend_Form_Element_Button('gotohollisbutton');
		$gotohollisbutton->setName('gotohollisbutton');
		$gotohollisbutton->setLabel('Go To HOLLIS');
		$gotohollisbutton->setAttribs(array('onclick' => 'gotoHOLLIS()'));
		$gotohollisbutton->setDecorators(Decorators::$BUTTON_DECORATORS);
		$this->addElement($gotohollisbutton);
		
		$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'customelements/hollisinput.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
    
    
}
?>