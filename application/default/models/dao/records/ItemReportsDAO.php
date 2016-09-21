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
 * ItemReportsDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
class ItemReportsDAO extends ItemDAO
{
    private static $itemReportsDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return ItemReportsDAO
     */
    public static function getItemReportsDAO()
	{
		if (!isset(self::$itemReportsDAO))
		{
			self::$itemReportsDAO = new ItemReportsDAO();
		}
		return self::$itemReportsDAO;
	}
	
	public function getCompletedByRepository(DateRange $dateRange, Location $repository = NULL, Department $department = NULL)
	{
		if (is_null($repository) && is_null($department))
		{
			throw new RuntimeException('Either a Repository or Department must be supplied.');
		}
		$select = $this->buildBaseSelect();
		$select->setIntegrityCheck(FALSE);
		$select->joinInner('ItemLogout', 'ItemIdentification.IdentificationID = ItemLogout.IdentificationID', array());
		$select->joinInner('ItemReport', 'ItemIdentification.IdentificationID = ItemReport.IdentificationID', array('Count' => 'GetReportItemCount(ReportID)', 'TotalHours' => 'GetConservatorHours(ReportID, NULL)'));
		$select->joinLeft('CallNumbers', 'ItemIdentification.IdentificationID = CallNumbers.IdentificationID', array('CallNumbers' => 'GetCallNumberString(ItemIdentification.IdentificationID)'));
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
		
		$select->where('ItemLogout.LogoutDate >= ' . $startdate);
		$select->where('ItemLogout.LogoutDate <= ' . $enddate);
		$select->order('CallNumbers.CallNumber');
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
    
	public function getCompletedItemCount(DateRange $dateRange, Location $location)
	{
		$select = $this->select();
		$select->from($this, array());
		$select->setIntegrityCheck(FALSE);
		$select->joinInner('ItemIdentification', 'Items.IdentificationID = ItemIdentification.IdentificationID', array());
		$select->joinInner('ItemLogout', 'Items.IdentificationID = ItemLogout.IdentificationID', array());
		$select->joinInner('ItemReport', 'Items.IdentificationID = ItemReport.IdentificationID', array());
		$select->joinInner('ReportCounts', 'ItemReport.ReportID = ReportCounts.ReportID AND ReportCounts.CountType <> \'Housings\'', array('Count' => 'SUM(TotalCount)'));
		
		$zendstartdate = new Zend_Date($dateRange->getStartDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$startdate = $zendstartdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$startdate = $this->getAdapter()->quote($startdate);
		$zendenddate = new Zend_Date($dateRange->getEndDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$enddate = $zendenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$enddate = $this->getAdapter()->quote($enddate);
		
		$select->where('HomeLocationID=' . $location->getPrimaryKey());
		$select->where('ItemLogout.LogoutDate >= ' . $startdate . ' AND ItemLogout.LogoutDate <= ' . $enddate);
		Logger::log($select->__toString(), Zend_Log::DEBUG);
		$row = $this->fetchRow($select);
		return $row->Count;
	}
	
	public function getCompletedItemHours(DateRange $dateRange, Location $location)
	{
		$select = $this->select();
		$select->from($this, array());
		$select->setIntegrityCheck(FALSE);
		$select->joinInner('ItemIdentification', 'Items.IdentificationID = ItemIdentification.IdentificationID', array());
		$select->joinInner('ItemLogout', 'Items.IdentificationID = ItemLogout.IdentificationID', array());
		$select->joinInner('ItemReport', 'Items.IdentificationID = ItemReport.IdentificationID', array());
		$select->joinInner('ItemConservators', 'ItemReport.ReportID = ItemConservators.ReportID', array('Count' => 'SUM(CompletedHours)'));
		
		$zendstartdate = new Zend_Date($dateRange->getStartDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$startdate = $zendstartdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$startdate = $this->getAdapter()->quote($startdate);
		$zendenddate = new Zend_Date($dateRange->getEndDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$enddate = $zendenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$enddate = $this->getAdapter()->quote($enddate);
		
		$select->where('HomeLocationID=' . $location->getPrimaryKey());
		$select->where('ItemLogout.LogoutDate >= ' . $startdate . ' AND ItemLogout.LogoutDate <= ' . $enddate);
		Logger::log($select->__toString(), Zend_Log::DEBUG);
		$row = $this->fetchRow($select);
		return $row->Count;
	}
	
	/*
	 * Returns the count of items 
	 */
	public function getTreatmentLevelCounts($treatmentLevel, DateRange $dateRange, Location $location = NULL)
	{
		$select = $this->select();
		$select->from($this, array());
		$select->setIntegrityCheck(FALSE);
		$select->joinLeft('ItemLogout', 'Items.IdentificationID = ItemLogout.IdentificationID', array());
		$select->joinInner('ItemReport', 'Items.IdentificationID = ItemReport.IdentificationID', array());
		$select->joinInner('ReportCounts', 'ItemReport.ReportID = ReportCounts.ReportID', array('Count' => 'SUM(TotalCount)'));
		$select->where('ReportCounts.CountType=\'Volumes\'');
		$select->where(ReportDAO::ADMIN_ONLY . '=0');
		$select->where(ReportDAO::CUSTOM_HOUSING_ONLY . '=0');
		$select->where(ReportDAO::EXAM_ONLY . '=0');
		$select->where('GetTreatmentLevel(ItemReport.ReportID)=' . $treatmentLevel);
		if (!is_null($location))
		{
			$select->joinInner('ItemIdentification', 'Items.IdentificationID = ItemIdentification.IdentificationID', array());
			$select->where(ItemDAO::HOME_LOCATION_ID . '=' . $location->getPrimaryKey());
		}
		$zendstartdate = new Zend_Date($dateRange->getStartDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$startdate = $zendstartdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$startdate = $this->getAdapter()->quote($startdate);
		$zendenddate = new Zend_Date($dateRange->getEndDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$enddate = $zendenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$enddate = $this->getAdapter()->quote($enddate);
		
		$select->where('ItemLogout.LogoutDate >= ' . $startdate . ' AND ItemLogout.LogoutDate <= ' . $enddate);
		Logger::log($select->__toString());
		$row = $this->fetchRow($select);
		$count = 0;
		if (!is_null($row->Count))
		{
			$count = $row->Count;
		}
		return $count;
	}
	
	/*
	 * Returns the count of the given item types
	 */
	public function getCountsByType(array $types, DateRange $dateRange, Location $location = NULL)
	{
		$select = $this->select();
		$select->from($this, array());
		$select->setIntegrityCheck(FALSE);
		$select->joinLeft('ItemLogout', 'Items.IdentificationID = ItemLogout.IdentificationID', array());
		$select->joinInner('ItemReport', 'Items.IdentificationID = ItemReport.IdentificationID', array());
		$select->joinInner('ReportCounts', 'ItemReport.ReportID = ReportCounts.ReportID', array('Count' => 'SUM(TotalCount)'));
		$select->where(ReportDAO::ADMIN_ONLY . '=0');
		$select->where(ReportDAO::CUSTOM_HOUSING_ONLY . '=0');
		$select->where(ReportDAO::EXAM_ONLY . '=0');
		$where = "(";
		$delimiter = "";
		foreach ($types as $type)
		{
			$where .= $delimiter . "ReportCounts.CountType='" . $type . "'";
			$delimiter = " OR ";
		}
		$where .= ")";
		$select->where($where);
		if (!is_null($location))
		{
			$select->joinInner('ItemIdentification', 'Items.IdentificationID = ItemIdentification.IdentificationID', array());
			$select->where(ItemDAO::HOME_LOCATION_ID . '=' . $location->getPrimaryKey());
		}
		$zendstartdate = new Zend_Date($dateRange->getStartDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$startdate = $zendstartdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$startdate = $this->getAdapter()->quote($startdate);
		$zendenddate = new Zend_Date($dateRange->getEndDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$enddate = $zendenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$enddate = $this->getAdapter()->quote($enddate);
		
		$select->where('ItemLogout.LogoutDate >= ' . $startdate . ' AND ItemLogout.LogoutDate <= ' . $enddate);
		Logger::log($select->__toString());
		$row = $this->fetchRow($select);
		$count = 0;
		if (!is_null($row->Count))
		{
			$count = $row->Count;
		}
		return $count;
	}
} 

?>