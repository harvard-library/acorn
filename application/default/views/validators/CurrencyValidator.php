<?php
/*
 * @author Valdeva Crema
 * Class CurrencyValidator
 * Created On Mar 5, 2009
 *
 * 
 */
class CurrencyValidator extends Zend_Validate_Abstract
{
	/*const MESSAGE = "date";
*/
	/*protected $_messageTemplates = array(
	"not a a form of currency ($2 or $2.2 or 2.2 or 2,000 are examples)"
	);*/

	public function isValid($value)
	{
		return true;	 
	}
}
?>