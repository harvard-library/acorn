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
 * ChangePasswordForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class ChangePasswordForm extends ACORNForm 
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$oldpassword = new Zend_Form_Element_Password('oldpassword');
		$oldpassword->setLabel('Old Password*');
		$oldpassword->setName('oldpassword');
		$oldpassword->setRequired(TRUE);
		$oldpassword->addFilter(new Zend_Filter_StringTrim());
    	$oldpassword->addFilter(new Zend_Filter_StripTags());
    	$oldpassword->addFilter(new EmptyToNullFilter());
    	$oldpassword->addFilter(new PasswordFilter());
    	$oldpassword->addValidator(new CurrentUserPasswordValidator());
		$oldpassword->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->addElement($oldpassword);
		
    	$newpassword = new Zend_Form_Element_Password('newpassword');
		$newpassword->setLabel('New Password*');
		$newpassword->setName('newpassword');
		$newpassword->setRequired(TRUE);
		$newpassword->addFilter(new Zend_Filter_StringTrim());
    	$newpassword->addFilter(new Zend_Filter_StripTags());
    	$newpassword->addFilter(new EmptyToNullFilter());
    	$newpassword->addFilter(new PasswordFilter());
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
    	$newpasswordconfirm->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->addElement($newpasswordconfirm);
    }
    
	public function isValid($values)
    {
    	$isvalid = parent::isValid($values);
    	
    	if ($isvalid)
    	{
    		//Makes sure that the two passwords match
	    	if (isset($values['newpassword']))
	    	{
	    		if ($values['newpassword'] != $values['newpasswordconfirm'])
	    		{
	    			$isvalid = FALSE;
	    			$this->getElement('newpasswordconfirm')->addError('Your new and confirm password do not match.  Please try again.');
	    		}
	    	}
    	}
    	return $isvalid;
    }
}
?>