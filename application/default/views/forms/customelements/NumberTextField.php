<?php
/*
 * @author Valdeva Crema
 * Class NumberTextField
 * Created on October 28, 2008
 *
 * A Form Element for numbers.
 */
class NumberTextField extends ACORNTextField
{
	public function init()
	{
		$this->addValidator(new Zend_Validate_Digits());
    	$this->setAttribs(array('class' => 'number')); 	
    }
}
?>