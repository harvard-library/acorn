<?php

/*
 * @author Valdeva Crema
 * Class EmptyToNullFilter
 * Created on August 13, 2008
 * 
 * Returns empty values as null.
 */
class EmptyToNullFilter implements Zend_Filter_Interface
{
	/*
	 * Return the password as an encrypted value.
	 */
    public function filter($value)
    {
    	$value = trim($value);
    	$valueFiltered = $value;
    	if (!isset($value) || empty($value))
    	{
    		$valueFiltered = NULL;
    	}
        return $valueFiltered;
    }
}

?>