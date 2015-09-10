<?php

/*
 * @author Valdeva Crema
 * Class DateFilter
 * Created on March 6, 2009
 * 
 * Returns in the form of MM-DD-YYYY
 */
class DateFilter implements Zend_Filter_Interface
{
	public function filter($value)
    {
    	if (empty($value))
    	{
    		return $value;
    	}
    	$valuearray = explode(" ", $value);
    	if (count($valuearray) > 0)
    	{
    		$value = current($valuearray);
    		$format = DateFormat::determineDateFormat($value);
    		if (!is_null($format))
    		{
    			$zenddate = new Zend_Date($value, $format);		
    			$value = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    		}	
			if (!$value)
    		{
    			$value = '';
    		}
    	}
    	else 
    	{
    		$value = '';
    	}
    	return $value;
    }
}

?>