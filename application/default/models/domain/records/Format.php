<?php

class Format extends AcornClass
{
    private $format = NULL;

    /**
     * @access public
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @access public
     * @param  String format
     */
    public function setFormat($format)
    {
    	$this->format = $format;
    }

	public function updateField($columnname, $newdata)
    {
    	switch ($columnname)
    	{
    		case FormatDAO::FORMAT:
    			$this->setFormat($newdata);
    			break;
    		case FormatDAO::INACTIVE:
    			$this->setInactive($newdata);
    			break;
    	}
    }
    
    /**
     * Overrides the parent __toString() method.
     * Prints out the format object as a string.
     */
    public function __toString()
    {
    	$string = "";
    	if (!is_null($this->getPrimaryKey()) && $this->getPrimaryKey() > 0)
    	{
    		$string = $this->getPrimaryKey() . "_";
    	}
    	$string .= $this->getFormat();
    	return $string;
    }
    	 
} 

?>