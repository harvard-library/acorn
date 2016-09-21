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
 * GroupFilesForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
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