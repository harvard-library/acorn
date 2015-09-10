<?php

class ReportConservator
{
    private $conservator = NULL;
	private $hoursWorked = NULL;
	private $dateCompleted = NULL;
	
    /**
     * @access public
     * @return Person
     */
    public function getConservator()
    {
    	return $this->conservator;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getHoursWorked()
    {
    	return $this->hoursWorked;
    }

    /**
     * @access public
     * @param  Person conservator
     */
    public function setConservator($conservator)
    {
    	$this->conservator = $conservator;
    }

    /**
     * @access public
     * @param  Decimal hoursWorked
     */
    public function setHoursWorked($hoursWorked)
    {
    	$this->hoursWorked = $hoursWorked;
    }
    
    

    /**
	 * @return the $dateCompleted
	 */
	public function getDateCompleted()
	{
		return $this->dateCompleted;
	}

	/**
	 * @param NULL $dateCompleted
	 */
	public function setDateCompleted($dateCompleted)
	{
		$this->dateCompleted = $dateCompleted;
	}

	public function __toString()
    {
    	$string = $this->getConservator()->__toString();
    	if (!is_null($this->dateCompleted))
    	{
    		$string .= $this->getDateCompleted();
    	}
    	if (!is_null($this->hoursWorked))
    	{
    		$string .= $this->getHoursWorked();
    	}
    	return $string;
    }
} 

?>