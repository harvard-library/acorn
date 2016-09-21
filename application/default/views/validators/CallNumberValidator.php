<?php
/**
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
 * Class CallNumberValidator
 * Created On Oct 1, 2012
 * 
 * This Validator verifies that the call number does not already exist in another record.
 */
class CallNumberValidator extends Zend_Validate_Abstract
{
    const MESSAGE = 'callNumberExists';

    protected $_messageTemplates = array(
        self::MESSAGE => "The call number, '%value%' exists in another Acorn or OSW record."
    );
    
   protected $currentIdentificationID = NULL;
    
    /**
	 * A constructor for allowing the current item to be passed in.
	 * @param itemID - the identificationID being edited currently
	 */
	public function __construct($identificationID = NULL)
	{
		$this->currentIdentificationID = $identificationID;
	}
    
    public function isValid($value)
    {
        if (!is_null($value))
        {
	        $this->_setValue($value);
	        
	        $exclusionids = !empty($this->currentIdentificationID) ? array($this->currentIdentificationID) : NULL;
	        $dup = CombinedRecordsDAO::getCombinedRecordsDAO()->callNumberExists($value, $exclusionids);
	        if (!is_null($dup))
	        {
        		$this->_error(self::MESSAGE);
	        	return FALSE;
	        }
	    }

        return TRUE;
    }
    
}
?>