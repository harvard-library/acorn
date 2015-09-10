<?php
/*
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
    	Logger::log('Password value '  . $value);
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