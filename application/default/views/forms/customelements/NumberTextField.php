<?php
/*
 * Copyright 2016 The President and Fellows of Harvard College
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 *
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