<?php
/*
 * @author Valdeva Crema
 * Class DateTextField
 * Created on October 28, 2008
 *
 * A Form Element for dates.
 */
class DateTextField extends ACORNTextField
{
	public function init()
	{
		parent::init();
		$this->addFilter(new DateFilter());
    	$this->addValidator(new DateValidator());
    	$this->setAttribs(array('class' => 'date'));
    }
}
?>