<?php

class WorkType extends AcornClass
{
    private $workType = NULL;

    /**
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function getWorkType()
    {
    	return $this->workType;
    }

    /**
     * @access public
     * @param  String workType
     */
    public function setWorkType($workType)
    {
    	$this->workType = $workType;
    }
	
	public function updateField($columnname, $newdata)
    {
    	switch ($columnname)
    	{
    		case WorkTypeDAO::WORK_TYPE:
    			$this->setWorkType($newdata);
    			break;
    		case WorkTypeDAO::INACTIVE:
    			$this->setInactive($newdata);
    			break;
    	}
    }
    
    public function __toString()
    {
    	return $this->getWorkType();
    }
} 

?>