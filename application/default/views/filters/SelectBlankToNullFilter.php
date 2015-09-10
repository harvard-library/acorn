<?php

/*
 * @author Valdeva Crema
 * Class SelectBlankToNullFilter
 * Created on September 10, 2008
 * 
 * Returns empty values as null.
 */
class SelectBlankToNullFilter implements Zend_Filter_Interface
{
	/*
	 * Return the value or null if the selected field is blank.
	 */
    public function filter($value)
    {
    	$valueFiltered = $value;
    	if (empty($value) || $value == "-1")
    	{
    		$valueFiltered = NULL;
    	}
    	return $valueFiltered;
    }
}

?>