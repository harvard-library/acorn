<?php
/*
 * @author Valdeva Crema
 * Class ACORNTextArea
 * Created on March 6, 2009
 *
 * A Form Element for ACORN text areas that adds the filters and decorators.
 */
class ACORNTextArea extends Zend_Form_Element_Textarea
{
	public function init()
	{
		$this->addFilter(new Zend_Filter_StringTrim());
    	$this->addFilter(new Zend_Filter_StripTags());
    	$this->addFilter(new EmptyToNullFilter());
    	$this->setDecorators(Decorators::$ELEMENT_DECORATORS);
    }
}
?>