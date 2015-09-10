<?php
/*
 * @author Valdeva Crema
 * Class DateOrYearValidator
 * Created On Sep 5, 2008
 *
 * This Validator allows the date to be null, to be
 * in the format of m-d-yyyy or m/d/yyyy (where month and days can
 * be one or two digits), or to be a four digit year.
 */
class DateOrYearValidator extends Zend_Validate_Abstract
{
	const MESSAGE = 'date';

	protected $_messageTemplates = array(
	self::MESSAGE => "'%value%' is not a correct date or year (mm-dd-yyyy or yyyy-mm-dd or yyyy)"
	);

	public function isValid($value)
	{
		if (!empty($value) && strlen($value) != 4)
		{
			$this->_setValue($value);
			$zenddate = new Zend_Date($value, ACORNConstants::$ZEND_DATE_FORMAT);		
    		$valueFiltered = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
			
			if (!$valueFiltered)
			{
				$this->_error();
				return false;
			}
		}
		return true;	 
	}
}
?>