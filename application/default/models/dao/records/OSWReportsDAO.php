<?php
/**
 * OSWReportsDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class OSWReportsDAO extends OnSiteWorkDAO 
{
    private static $oswReportsDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return OSWReportsDAO
     */
    public static function getOSWReportsDAO()
	{
		if (!isset(self::$oswReportsDAO))
		{
			self::$oswReportsDAO = new OSWReportsDAO();
		}
		return self::$oswReportsDAO;
	}
	
	public function getCompletedByRepository(DateRange $dateRange, Location $repository = NULL, Department $department = NULL)
	{
		if (is_null($repository) && is_null($department))
		{
			throw new RuntimeException('Either a Repository or Department must be supplied.');
		}
		$select = $this->buildBaseSelect();
		$select->setIntegrityCheck(FALSE);
		$select->joinInner('ItemReport', 'ItemIdentification.IdentificationID = ItemReport.IdentificationID', array('Count' => 'GetReportItemCount(ReportID)', 'TotalHours' => 'GetConservatorHours(ReportID, NULL)'));
		$select->joinInner('Purposes', 'ItemIdentification.PurposeID = Purposes.PurposeID', array(PurposeDAO::PURPOSE));
		if (is_null($department))
		{
			$select->where('ItemIdentification.DepartmentID IS NULL');
			$select->where('ItemIdentification.HomeLocationID=' . $repository->getPrimaryKey());
		}
		else 
		{
			$select->where('ItemIdentification.DepartmentID=' . $department->getPrimaryKey());
		}
		$zendstartdate = new Zend_Date($dateRange->getStartDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$startdate = $zendstartdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$startdate = $this->getAdapter()->quote($startdate);
		$zendenddate = new Zend_Date($dateRange->getEndDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$enddate = $zendenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$enddate = $this->getAdapter()->quote($enddate);
		
		$select->where('OSW.WorkEndDate >= ' . $startdate);
		$select->where('OSW.WorkEndDate <= ' . $enddate);
		$select->order('Title');
		$select->distinct();
		Logger::log($select->__toString(), Zend_Log::DEBUG);
		$searchresults = $this->fetchAll($select);
		$retval = array();
		if (!is_null($searchresults))
		{
			$retval = $searchresults->toArray();
		}
		return $retval;
	}
    
	public function getCompletedOSWCount(DateRange $dateRange, Location $location)
	{
		$select = $this->select();
		$select->from($this, array());
		$select->setIntegrityCheck(FALSE);
		$select->joinInner('ItemIdentification', 'OSW.IdentificationID = ItemIdentification.IdentificationID', array());
		$select->joinInner('ItemReport', 'OSW.IdentificationID = ItemReport.IdentificationID', array());
		$select->joinInner('ReportCounts', 'ItemReport.ReportID = ReportCounts.ReportID AND CountType <> \'Housings\'', array('Count' => 'SUM(TotalCount)'));
		
		$zendstartdate = new Zend_Date($dateRange->getStartDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$startdate = $zendstartdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$startdate = $this->getAdapter()->quote($startdate);
		$zendenddate = new Zend_Date($dateRange->getEndDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$enddate = $zendenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$enddate = $this->getAdapter()->quote($enddate);
		
		$select->where('HomeLocationID=' . $location->getPrimaryKey());
		$select->where('OSW.WorkEndDate >= ' . $startdate . ' AND OSW.WorkEndDate <= ' . $enddate);
		Logger::log($select->__toString(), Zend_Log::DEBUG);
		$row = $this->fetchRow($select);
		return $row->Count;
	}
	
	public function getCompletedOSWHours(DateRange $dateRange, Location $location)
	{
		$select = $this->select();
		$select->from($this, array());
		$select->setIntegrityCheck(FALSE);
		$select->joinInner('ItemIdentification', 'OSW.IdentificationID = ItemIdentification.IdentificationID', array());
		$select->joinInner('ItemReport', 'OSW.IdentificationID = ItemReport.IdentificationID', array());
		$select->joinInner('ItemConservators', 'ItemReport.ReportID = ItemConservators.ReportID', array('Count' => 'SUM(CompletedHours)'));
		
		$zendstartdate = new Zend_Date($dateRange->getStartDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$startdate = $zendstartdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$startdate = $this->getAdapter()->quote($startdate);
		$zendenddate = new Zend_Date($dateRange->getEndDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$enddate = $zendenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$enddate = $this->getAdapter()->quote($enddate);
		
		$select->where('HomeLocationID=' . $location->getPrimaryKey());
		$select->where('OSW.WorkEndDate >= ' . $startdate . ' AND OSW.WorkEndDate <= ' . $enddate);
		Logger::log($select->__toString(), Zend_Log::DEBUG);
		$row = $this->fetchRow($select);
		return $row->Count;
	}
} 

?>