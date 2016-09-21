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
 * Class TitleValidator
 * Created On Oct 1, 2012
 * 
 * This Validator verifies that the Title does not already exist in another record.
 */
class TitleValidator extends Zend_Validate_Abstract
{
    const MESSAGE = 'titleExists';

    protected $_messageTemplates = array(
        self::MESSAGE => "The Title, '%value%' already exists in Acorn record #%itemID%."
    );
    
   	protected $currentItemID = NULL;
    protected $itemID = NULL;
    protected $_messageVariables = array(
    		'itemID' => 'itemID'
    );
    
    /**
	 * A constructor for allowing the current item to be passed in.
	 * @param itemID - the itemID being edited currently
	 */
	public function __construct($itemID = NULL)
	{
		$this->currentItemID = $itemID;
	}
    
    public function isValid($value)
    {
        if (!is_null($value))
        {
	        $this->_setValue($value);
	        
	        $exclusionids = !empty($this->currentItemID) ? array($this->currentItemID) : NULL;
	        $itemid = ItemDAO::getItemDAO()->titleExists($value, $exclusionids);
	        if (!is_null($itemid))
	        {
        		$this->itemID = $itemid;
	        	$this->_error(self::MESSAGE);
	        	return FALSE;
	        }
	    }

        return TRUE;
    }
    
}
?>