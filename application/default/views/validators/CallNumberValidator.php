<?php
/**
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