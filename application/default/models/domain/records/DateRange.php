<?php
/*
 * @author Valdeva Crema
 * Class DateRange
 * 
 * A DateRange object
 * 
 * Copyright 2010 The President and Fellows of Harvard College
 */

class DateRange
{
	private $startDate = NULL;
	private $endDate = NULL;
	private $databaseStartDate = NULL;
	private $databaseEndDate = NULL;
	private $databaseStartDatetime = NULL;
	private $databaseEndDatetime = NULL;
	
    /**
     * @access public
     * @return mixed
     */
    public function getStartDate()
    {
    	return $this->startDate;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getEndDate()
    {
    	return $this->endDate;
    }
    
	/**
     * @access public
     * @return mixed
     */
    public function getDatabaseStartDate()
    {
    	return $this->databaseStartDate;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getDatabaseEndDate()
    {
    	return $this->databaseEndDate;
    }
    
/**
     * @access public
     * @return mixed
     */
    public function getDatabaseStartDatetime()
    {
    	return $this->databaseStartDatetime;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getDatabaseEndDatetime()
    {
    	return $this->databaseEndDatetime;
    }

    /**
     * @access public
     * @param  mixed
     */
    public function setStartDate($startDate, $format = 'MM-dd-yyyy')
    {
    	if (!empty($startDate))
    	{
	    	$zenddate = new Zend_Date($startDate, $format);
			$this->startDate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
			$this->databaseStartDate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
			$this->databaseStartDatetime = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
    	}
    	else
    	{
    		$this->startDate = NULL;
    		$this->databaseStartDate = NULL;
    		$this->databaseStartDatetime = NULL;
    	}
	}

    /**
     * @access public
     * @param  mixed
     */
    public function setEndDate($endDate = NULL, $format = 'MM-dd-yyyy')
    {
    	if (!empty($endDate))
    	{
	    	$zenddate = new Zend_Date($endDate, $format);
			$this->endDate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
			$this->databaseEndDate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
			$this->databaseEndDatetime = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
    	}
    	else
    	{
    		$this->endDate = NULL;
    		$this->databaseEndDate = NULL;
    		$this->databaseEndDatetime = NULL;
    	}
	}
    
    /*
     * Creates a DateRange object based on the date and supplied interval.
     * 
     * @param string date
     * @param int dayinterval 
     * 
     * If the dayinterval is positive, the supplied date will be the 
     * begin date.  The day interval will be added to the begin date to 
     * calculate the end date.
     * 
     * If the dayinterval is negative, the supplied date will be the end date.  
     * The day interval will be subtracted from the end date to calculate the
     * begin date.
     */
    public static function createDateRange($date, $dayinterval)
    {
    	//First make sure the date is valid:
    	$zenddate = new Zend_Date($date, ACORNConstants::ZEND_DATE_FORMAT);
		$date = $zenddate->toString(ACORNConstants::ZEND_INTERNAL_DATETIME_FORMAT);
		if (!$date)
    	{
    		return NULL;
    	}
    	
    	$zenddate = new Zend_Date($date, ACORNConstants::ZEND_INTERNAL_DATE_FORMAT);
    	
    	$daterange = new DateRange();
    	//If the day interval is > 0, the begin date is the supplied date.
    	if ($dayinterval > 0)
    	{
    		$daterange->setStartDate($zenddate->toString(ACORNConstants::ZEND_DATE_FORMAT));
    		$zendenddate = $zenddate->addDay($dayinterval);
    		$daterange->setEndDate($zendenddate->toString(ACORNConstants::ZEND_DATE_FORMAT));
    	}
    	//Otherwise the end date is the supplied date.
    	else 
    	{
    		$daterange->setEndDate($zenddate->toString(ACORNConstants::ZEND_DATE_FORMAT));
    		$zendenddate = $zenddate->addDay($dayinterval);
    		$daterange->setStartDate($zendenddate->toString(ACORNConstants::ZEND_DATE_FORMAT));
    	}
    	return $daterange;
    }
} 

?>