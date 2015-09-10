<?php
/**
 * @author Valdeva Crema
 * Class HOLLISNumberValidator
 * Created On Oct 1, 2012
 * 
 * This Validator verifies that the HOLLIS number does not already exist in another record.
 */
class HOLLISNumberValidator extends Zend_Validate_Abstract
{
    const MESSAGE = 'hollisNumberExists';

    protected $_messageTemplates = array(
        self::MESSAGE => "The HOLLIS number, '%value%' exists in Acorn record #%itemID%."
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
	        $itemid = ItemDAO::getItemDAO()->hollisNumberExists($value, $exclusionids);
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