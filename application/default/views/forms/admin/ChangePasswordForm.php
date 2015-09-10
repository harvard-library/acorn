<?php
/**
 * ChangePasswordForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
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