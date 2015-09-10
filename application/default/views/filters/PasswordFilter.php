<?php

/*
 * @author Valdeva Crema
 * Class PasswordFilter
 * Created on August 13, 2008
 * 
 * Uses md5 encryption on the password.
 */
class PasswordFilter implements Zend_Filter_Interface
{
	/*
	 * Return the password as an encrypted value.
	 */
    public function filter($value)
    {
    	if (!empty($value))
    	{
    		$valueFiltered = md5($value);
    	}
    	else 
    	{
    		$valueFiltered = '';
    	}
        return $valueFiltered;
    }
}

?>