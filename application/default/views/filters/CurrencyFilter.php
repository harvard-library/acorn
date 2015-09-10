<?php

/*
 * @author Valdeva Crema
 * Class CurrencyFilter
 * Created on March 6, 2009
 * 
 * Returns in the form of $2000.00
 */
class CurrencyFilter implements Zend_Filter_Interface
{
	public function filter($value)
    {
    	return $value;
    }
}

?>