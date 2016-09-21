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
 * Class CurrentUserPasswordValidator
 * Created On May 13, 2009
 * 
 * This Validator checks the password against the current user.
 */
class CurrentUserPasswordValidator extends Zend_Validate_Abstract
{
    const MESSAGE = 'incorrectPassword';

    protected $_messageTemplates = array(
        self::MESSAGE => "You have entered an incorrect password.  Please try again."
    );

    public function isValid($value)
    {
    	$isvalid = TRUE;
        if (empty($value))
        {
        	$this->_error();
        	$isvalid = FALSE;
        }
        else
        {
	        $auth = Zend_Auth::getInstance();
	        $identity = $auth->getIdentity();
	        $user = PeopleDAO::getPeopleDAO()->getUserWithPassword($identity[PeopleDAO::USERNAME], $value);
	        if (is_null($user))
	        {
        		$this->_error();
        		$isvalid = FALSE;
	        }
	    }

        return $isvalid;
    }
    
	public static function validateNumber($value)
	{
		$validator = new NumberValidator();
		return $validator->isValid($value);
	}
}
?>