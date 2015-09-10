<?php
/**
 * ACORNSelect
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class ACORNSelect extends Zend_Form_Element_Select  
{
	const INCLUDE_INACTIVE = 'IncludeInactive';
	const INCLUDE_BLANK = 'IncludeBlank';
	
	public function init()
    {
    	$this->addFilter(new SelectBlankToNullFilter());
		$this->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->setRegisterInArrayValidator(FALSE);
		$options = $this->getMultiOptions();
		if (empty($options))
		{
			$this->setMultiOptions(array(''=>''));
		}
		parent::init();
	}
}
?>