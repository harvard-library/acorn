<?php
/*
 * @author Valdeva Crema
 * Class ACORNTextField
 * Created on March 6, 2009
 *
 * A Form Element for ACORN text elements that adds the filters and decorators.
 */
class ACORNTextField extends Zend_Form_Element_Text
{
	public function init()
	{
		parent::init();
		$this->addFilter(new Zend_Filter_StringTrim());
    	$this->addFilter(new Zend_Filter_StripTags());
    	$this->addFilter(new EmptyToNullFilter());
    	$this->setDecorators(Decorators::$ELEMENT_DECORATORS);
    }
}
?>