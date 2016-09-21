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
 * UserLoginForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class UserLoginForm extends Zend_Form
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$usernameinput = new ACORNTextField('usernameinput');
		$usernameinput->setLabel('Username*');
		$usernameinput->setName('usernameinput');
		//$usernameinput->setAttrib('onkeyup', 'checkDisplayLoginAs(event)');
		$usernameinput->setAttrib('onblur', 'checkDisplayLoginAs()');
		$usernameinput->setRequired(TRUE);
		$this->addElement($usernameinput);
		
		$passwordinput = new Zend_Form_Element_Password('passwordinput');
		$passwordinput->setLabel('Password*');
		$passwordinput->setName('passwordinput');
		$passwordinput->setRequired(TRUE);
		$passwordinput->addFilter(new PasswordFilter());
		$this->addElement($passwordinput);
		
		$accesslevelselectform = new AccessLevelSelectForm();
    	$accesslevelselectform->setName('accesslevelselectform');
    	$this->addSubForm($accesslevelselectform, 'accesslevelselectform');
		
		$userloginbutton = new Zend_Form_Element_Submit('userloginbutton');
		$userloginbutton->setName('userloginbutton');
		$userloginbutton->setLabel('Login');
		$this->addElement($userloginbutton);
		
		$this->setDecorators(array(
			array('Description', array('placement' => 'prepend')),
		));
    }
    
	public function getValues($suppressarraynotation = FALSE)
 	{
 		$values = parent::getValues($suppressarraynotation);
 		
 		$subforms = $this->getSubForms();
 		foreach ($subforms as $subform)
 		{
 			$values = array_merge($values, $subform->getValues());
 		}
 		return $values;
 	}
 	
 	public function isValid($values)
 	{
 		$accesslevel = $values['usertypeselect'];
 		if ($accesslevel == PeopleDAO::ACCESS_LEVEL_ADMIN 
 			|| $accesslevel == PeopleDAO::ACCESS_LEVEL_REGULAR
 			|| $accesslevel == PeopleDAO::ACCESS_LEVEL_CURATOR)
 		{
 			//Don't need a repository for this
 			unset($values['repositoryselect']);
 		}
 		$isvalid = parent::isValidPartial($values);
 		return $isvalid;
 	}
}
?>