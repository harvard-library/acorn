<?php
/*
 * @author Valdeva Crema
 * Class NumberValidator
 * Created On Sep 10, 2008
 * 
 * This Validator allows numbers.
 */
class NumberValidator extends Zend_Validate_Abstract
{
    const MESSAGE = 'notNumerical';

    protected $_messageTemplates = array(
        self::MESSAGE => "'%value%' is not a number"
    );

    public function isValid($value)
    {
        if (!is_null($value))
        {
	        $this->_setValue($value);

	        $isnum = is_numeric($value);
	        if (!$isnum)
	        {
        		$this->_error();
	        }
	        
			return $isnum;
        }

        return true;
    }
    
	public static function validateNumber($value)
	{
		$validator = new NumberValidator();
		return $validator->isValid($value);
	}
}
?>