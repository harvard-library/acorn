<?php
/*
 * @author Valdeva Crema
 * Class Decorators
 * Created on August 11, 2008
 *
 * Holds the default decorators for the elements,
 * buttons, checkboxes, and textareas.
 */
class Decorators
{
	//Decorator for the elements.
	public static $ELEMENT_DECORATORS = array(
		//Zend's View Helper
        'ViewHelper',
		//Error to be displayed if the element's
		//validator
        'Errors',
		//Put the labels for the elements before
		//the elements themselves.
    	array('Label', array('placement' => 'prepend')),
    );
    
    //Decorator for the elements.
	public static $REQUIRED_ELEMENT_DECORATORS = array(
		//Zend's View Helper
        'ViewHelper',
		//Error to be displayed if the element's
		//validator
        'Errors',
		//Put the labels for the elements before
		//the elements themselves.
    	array('Label', array('placement' => 'prepend', 'class' => 'required')),
    );

    //Decorator for buttons - just use Zend's View Helper.
    public static $BUTTON_DECORATORS = array(
        'ViewHelper',
    );

 	//Decorator for checkboxes
    public static $CHECKBOX_DECORATORS = array(
        'ViewHelper',
    	//Put the labels for the elements after
		//the elements themselves.
    	array('Label', array('placement' => 'append', 'class' => 'checkboxlabel')),
    );

    //Decorator for textareas
    public static $TEXTAREA_DECORATORS = array(
        'ViewHelper',
    	//Error to be displayed if the element's
		//validator
        'Errors',
		//Put the labels for the elements before
		//the elements themselves.
    	array('Label', array('placement' => 'prepend')),
    );
    
    //Decorator for textareas
    public static $NOTES_DECORATORS = array(
        'ViewHelper',
    	//Error to be displayed if the element's
		//validator
        'Errors',
		//Put the labels for the elements before
		//the elements themselves.
    	array('Label', array('placement' => 'prepend', 'class' => 'notes')),
    );
}
?>