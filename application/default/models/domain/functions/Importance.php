<?php

class Importance extends AcornClass
{
    private $importance = NULL;

    public function getImportance()
    {
    	return $this->importance;
    }

    /**
     * @access public
     * @param  String importance
     */
    public function setImportance($importance)
    {
    	$this->importance = $importance;
    }

	public function updateField($columnname, $newdata)
    {
    	switch ($columnname)
    	{
    		case ImportanceDAO::IMPORTANCE:
    			$this->setImportance($newdata);
    			break;
    		case ImportanceDAO::INACTIVE:
    			$this->setInactive($newdata);
    			break;
    	}
    }
    
    public function __toString()
    {
    	return $this->getImportance();
    }
} 

?>