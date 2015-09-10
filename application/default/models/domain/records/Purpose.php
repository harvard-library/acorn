<?php

class Purpose extends AcornClass
{
    private $purpose = NULL;

    /**
     * @access public
     * @return mixed
     */
    public function getPurpose()
    {
    	return $this->purpose;
    }

    /**
     * @access public
     * @param  String purpose
     */
    public function setPurpose($purpose)
    {
    	$this->purpose = $purpose;
    }
    
	public function updateField($columnname, $newdata)
    {
    	switch ($columnname)
    	{
    		case PurposeDAO::PURPOSE:
    			$this->setPurpose($newdata);
    			break;
    		case PurposeDAO::INACTIVE:
    			$this->setInactive($newdata);
    			break;
    	}
    }

} 

?>