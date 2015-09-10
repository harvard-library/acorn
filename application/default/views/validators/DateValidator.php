<?php
/*
 * @author Valdeva Crema
 * Class DateValidator
 * Created On Sep 5, 2008
 *
 * This Validator allows the date to be null or
 * in the format of m-d-yyyy or m/d/yyyy (where month and days can
 * be one or two digits).
 */
class DateValidator extends Zend_Validate_Abstract
{
	const MESSAGE = 'date';

	protected $_messageTemplates = array(
	self::MESSAGE => "'%value%' is not a correct date (mm-dd-yyyy or yyyy-mm-dd)"
	);

	public function isValid($value)
	{
		if (isset($value) && !empty($value))
		{
			$this->_setValue($value);
			$zenddate = new Zend_Date($value, ACORNConstants::$ZEND_DATE_FORMAT);		
    		$valueFiltered = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
			
			if (!$valueFiltered)
			{
				$this->_error();
				return FALSE;
			}
		}
		return TRUE;	 
	}
}
?>