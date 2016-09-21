<?php
/*
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