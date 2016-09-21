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
 * PersonForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class PersonForm extends ACORNForm 
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$personinput = new ACORNTextField('personinput');
		$personinput->setLabel('Find Person');
		$personinput->setName('personinput');
		$this->addElement($personinput);
		
		$personidinput = new Zend_Form_Element_Hidden('personidinput');
		$personidinput->setName('personidinput');
		$personidinput->setRequired(TRUE);
		$personidinput->setValue(-1);
		$personidinput->addFilter(new Zend_Filter_StringTrim());
    	$personidinput->addFilter(new Zend_Filter_StripTags());
    	$personidinput->addFilter(new EmptyToNullFilter());
    	$personidinput->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->addElement($personidinput);
		
		$inactivecheckbox = new Zend_Form_Element_Checkbox('inactivecheckbox');
        $inactivecheckbox->setLabel('Inactive');
        $inactivecheckbox->setAttribs(array('class' => 'checkboxinput'));
        $inactivecheckbox->setDecorators(Decorators::$CHECKBOX_DECORATORS);
    	$this->addElement($inactivecheckbox);
    	
		$firstnameinput = new ACORNTextField('firstnameinput');
		$firstnameinput->setLabel('First Name*');
		$firstnameinput->setName('firstnameinput');
		$firstnameinput->setRequired(TRUE);
		$this->addElement($firstnameinput);
		
		$middlenameinput = new ACORNTextField('middlenameinput');
		$middlenameinput->setLabel('Middle Name');
		$middlenameinput->setName('middlenameinput');
		$this->addElement($middlenameinput);
		
		$lastnameinput = new ACORNTextField('lastnameinput');
		$lastnameinput->setLabel('Last Name*');
		$lastnameinput->setName('lastnameinput');
		$lastnameinput->setRequired(TRUE);
		$this->addElement($lastnameinput);
		
		$emailinput = new ACORNTextField('emailinput');
		$emailinput->setLabel('Email');
		$emailinput->setName('emailinput');
		$this->addElement($emailinput);
		
		$initialsinput = new ACORNTextField('initialsinput');
		$initialsinput->setLabel('Initials');
		$initialsinput->setName('initialsinput');
		$this->addElement($initialsinput);
		
		$logininput = new ACORNTextField('logininput');
		$logininput->setLabel('Login');
		$logininput->setName('logininput');
		$this->addElement($logininput);
		
		$accesslevelselectform = new AccessLevelSelectForm(array('AccessLevelLabel' => 'Access Level'));
    	$accesslevelselectform->setName('accesslevelselectform');
    	$accesslevelselectform->setRequired(TRUE);
    	$this->addSubForm($accesslevelselectform, 'accesslevelselectform');
    	
    	$newpassword = new Zend_Form_Element_Password('newpassword');
		$newpassword->setLabel('New Password*');
		$newpassword->setName('newpassword');
		$newpassword->setRequired(TRUE);
		$newpassword->addFilter(new Zend_Filter_StringTrim());
    	$newpassword->addFilter(new Zend_Filter_StripTags());
    	$newpassword->addFilter(new EmptyToNullFilter());
    	$newpassword->addFilter(new PasswordFilter());
		$newpassword->addValidator(new Zend_Validate_StringLength(3));
		$newpassword->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->addElement($newpassword);
		
		$newpasswordconfirm = new Zend_Form_Element_Password('newpasswordconfirm');
		$newpasswordconfirm->setLabel('New Password Confirm*');
		$newpasswordconfirm->setName('newpasswordconfirm');
		$newpasswordconfirm->setRequired(TRUE);
		$newpasswordconfirm->addFilter(new Zend_Filter_StringTrim());
    	$newpasswordconfirm->addFilter(new Zend_Filter_StripTags());
    	$newpasswordconfirm->addFilter(new EmptyToNullFilter());
    	$newpasswordconfirm->addFilter(new PasswordFilter());
		$newpasswordconfirm->addValidator(new Zend_Validate_StringLength(3));
		$newpasswordconfirm->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->addElement($newpasswordconfirm);
    }
    
    public function isValidPartial(array $values)
    {
    	$accesslevel = $values['usertypeselect'];
 		if ($accesslevel == PeopleDAO::ACCESS_LEVEL_ADMIN 
 			|| $accesslevel == PeopleDAO::ACCESS_LEVEL_REGULAR)
 		{
 			//Don't need a repository for this
 			unset($values['repositoryselect']);
 		}
 		$isvalid = parent::isValidPartial($values);
 		
    	if ($isvalid)
    	{
    		//Makes sure that the two passwords match
	    	if (isset($values['newpassword']))
	    	{
	    		if ($values['newpassword'] != $values['newpasswordconfirm'])
	    		{
	    			$isvalid = FALSE;
	    			$this->getElement('newpasswordconfirm')->addError('Passwords do not match.  Please try again.');
	    		}
	    	}
	    	
    		//Makes sure that the username does not already exist
	    	if (isset($values['logininput']) && !empty($values['logininput']))
	    	{
	    		$personid = NULL;
	    		if (!empty($values['personidinput']) && $values['personidinput'] != -1)
	    		{
	    			$personid = $values['personidinput'];
	    		}
	    		$loginexists = PeopleDAO::getPeopleDAO()->loginExists($values['logininput'], $personid);
	    		if ($loginexists)
	    		{
	    			$isvalid = FALSE;
	    			$this->getElement('logininput')->addError('This login is alread in use.  Please choose a different login.');
	    		}
	    	}
    	}
    	return $isvalid;
    }
}
?>