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
 * Created August 14, 2009
 *
 */
class CombinedRecordsReportsDAO extends CombinedRecordsDAO
{
    private static $combinedRecordsReportsDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return CombinedRecordsReportsDAO
     */
    public static function getCombinedRecordsReportsDAO()
	{
		if (!isset(self::$combinedRecordsReportsDAO))
		{
			self::$combinedRecordsReportsDAO = new CombinedRecordsReportsDAO();
		}
		return self::$combinedRecordsReportsDAO;
	}
	
	public function getCompletedByRepository(DateRange $dateRange, Location $repository = NULL, Department $department = NULL)
	{
		if (is_null($repository) && is_null($department))
		{
			throw new RuntimeException('Either a Repository or Department must be supplied.');
		}
		$select = $this->select();
		$select->from($this, array('*'));
		$select->setIntegrityCheck(FALSE);
		$select->joinLeft('ItemLogout', 'CombinedRecords.IdentificationID = ItemLogout.IdentificationID');
		$select->joinLeft('Items', 'CombinedRecords.IdentificationID = Items.IdentificationID', array());
		$select->joinLeft('OSW', 'CombinedRecords.IdentificationID = OSW.IdentificationID', array());
		$select->joinLeft('ItemProposal', 'CombinedRecords.IdentificationID = ItemProposal.IdentificationID', array(ProposalDAO::MIN_HOURS, ProposalDAO::MAX_HOURS));
		$select->joinInner('ItemReport', 'CombinedRecords.IdentificationID = ItemReport.IdentificationID', array('Count' => 'GetReportItemCount(ReportID)', 'TotalHours' => 'GetConservatorHours(ReportID, NULL)'));
		$select->joinLeft('CallNumbers', 'CombinedRecords.IdentificationID = CallNumbers.IdentificationID', array('CallNumbers' => 'GetCallNumberString(CombinedRecords.IdentificationID)'));
		$select->joinInner('Purposes', 'CombinedRecords.PurposeID = Purposes.PurposeID', array(PurposeDAO::PURPOSE));
		if (is_null($department))
		{
			$select->where('CombinedRecords.DepartmentID IS NULL');
			$select->where('CombinedRecords.HomeLocationID=' . $repository->getPrimaryKey());
		}
		else 
		{
			$select->where('CombinedRecords.DepartmentID=' . $department->getPrimaryKey());
		}
		$zendstartdate = new Zend_Date($dateRange->getStartDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$startdate = $zendstartdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$startdate = $this->getAdapter()->quote($startdate);
		$zendenddate = new Zend_Date($dateRange->getEndDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$enddate = $zendenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$enddate = $this->getAdapter()->quote($enddate);
		
		$where = '(ItemLogout.LogoutDate >= ' . $startdate . ' AND ItemLogout.LogoutDate <= ' . $enddate . ')';
		$where .= ' OR (OSW.WorkEndDate >= ' . $startdate . ' AND OSW.WorkEndDate <= ' . $enddate . ')';
		$select->where($where);
		$select->order(array('Purposes.Purpose', 'CallNumbers.CallNumber'));
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
    
	public function getCompletedByPerson(DateRange $dateRange, Person $person)
	{
		$select = $this->select();
		$select->from($this, array(self::RECORD_ID, self::RECORD_TYPE, ItemDAO::TITLE, ItemDAO::AUTHOR_ARTIST));
		$select->setIntegrityCheck(FALSE);
		$select->joinLeft('ItemLogout', 'CombinedRecords.IdentificationID = ItemLogout.IdentificationID');
		$select->joinLeft('Items', 'CombinedRecords.IdentificationID = Items.IdentificationID', array());
		$select->joinLeft('OSW', 'CombinedRecords.IdentificationID = OSW.IdentificationID', array());
		$select->joinInner('ItemReport', 'CombinedRecords.IdentificationID = ItemReport.IdentificationID', array('Count' => 'GetReportItemCount(ItemReport.ReportID)', 'TotalHours' => 'GetConservatorHours(ItemReport.ReportID, ' . $person->getPrimaryKey() . ')'));
		$select->joinInner('ItemConservators', 'ItemReport.ReportID = ItemConservators.ReportID', array());
		$select->joinLeft('CallNumbers', 'CombinedRecords.IdentificationID = CallNumbers.IdentificationID', array('CallNumbers' => 'GetCallNumberString(CombinedRecords.IdentificationID)'));
		$select->joinLeft('Projects', 'CombinedRecords.ProjectID = Projects.ProjectID', array(ProjectDAO::PROJECT_NAME));
		$select->joinInner('Locations', 'CombinedRecords.HomeLocationID = Locations.LocationID', array(LocationDAO::LOCATION));
		$select->joinLeft('Departments', 'CombinedRecords.DepartmentID = Departments.DepartmentID', array(DepartmentDAO::DEPARTMENT_NAME));
		$select->where('ItemConservators.PersonID=' . $person->getPrimaryKey());
		
		$startdate = $dateRange->getDatabaseStartDate();	
		$startdate = $this->getAdapter()->quote($startdate);
		$enddate = $dateRange->getDatabaseEndDate();
		$enddate = $this->getAdapter()->quote($enddate);
		
		$where = '(ItemLogout.LogoutDate >= ' . $startdate . ' AND ItemLogout.LogoutDate <= ' . $enddate . ')';
		$where .= ' OR (OSW.WorkEndDate >= ' . $startdate . ' AND OSW.WorkEndDate <= ' . $enddate . ')';
		$select->where($where);
		$select->order(array(LocationDAO::LOCATION, DepartmentDAO::DEPARTMENT_NAME, self::RECORD_TYPE, self::RECORD_ID));
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
	
	public function getCompletedByPeople(DateRange $dateRange, array $people)
	{
		$select = $this->select();
		$select->from($this, array(self::RECORD_ID, self::RECORD_TYPE, ItemDAO::TITLE, ItemDAO::AUTHOR_ARTIST));
		$select->setIntegrityCheck(FALSE);
		$select->joinLeft('ItemLogout', 'CombinedRecords.IdentificationID = ItemLogout.IdentificationID');
		$select->joinLeft('Items', 'CombinedRecords.IdentificationID = Items.IdentificationID', array());
		$select->joinLeft('OSW', 'CombinedRecords.IdentificationID = OSW.IdentificationID', array());
		$select->joinInner('ItemReport', 'CombinedRecords.IdentificationID = ItemReport.IdentificationID', array('ReportID', 'Count' => 'GetReportItemCount(ItemReport.ReportID)'));
		$select->joinInner('ItemConservators', 'ItemReport.ReportID = ItemConservators.ReportID', array());
		$select->joinLeft('CallNumbers', 'CombinedRecords.IdentificationID = CallNumbers.IdentificationID', array('CallNumbers' => 'GetCallNumberString(CombinedRecords.IdentificationID)'));
		$select->joinLeft('Projects', 'CombinedRecords.ProjectID = Projects.ProjectID', array(ProjectDAO::PROJECT_NAME));
		$select->joinInner('Locations', 'CombinedRecords.HomeLocationID = Locations.LocationID', array(LocationDAO::LOCATION));
		$select->joinLeft('Departments', 'CombinedRecords.DepartmentID = Departments.DepartmentID', array(DepartmentDAO::DEPARTMENT_NAME));
		
		$peopleids = array_keys($people);
		$peoplelist = implode(",", $peopleids);
		$select->where('ItemConservators.PersonID IN (' . $peoplelist . ")");
		
		$zendstartdate = new Zend_Date($dateRange->getStartDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$startdate = $zendstartdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$startdate = $this->getAdapter()->quote($startdate);
		$zendenddate = new Zend_Date($dateRange->getEndDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$enddate = $zendenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$enddate = $this->getAdapter()->quote($enddate);
		
		$where = '(ItemLogout.LogoutDate >= ' . $startdate . ' AND ItemLogout.LogoutDate <= ' . $enddate . ')';
		$where .= ' OR (OSW.WorkEndDate >= ' . $startdate . ' AND OSW.WorkEndDate <= ' . $enddate . ')';
		$select->where($where);
		$select->order(array(LocationDAO::LOCATION, DepartmentDAO::DEPARTMENT_NAME, self::RECORD_TYPE, self::RECORD_ID));
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
	
	public function getCompletedByProject(DateRange $dateRange, Project $project)
	{
		$select = $this->select();
		$select->from($this, array(self::RECORD_ID, self::RECORD_TYPE, ItemDAO::TITLE, ItemDAO::AUTHOR_ARTIST));
		$select->setIntegrityCheck(FALSE);
		$select->joinLeft('ItemLogout', 'CombinedRecords.IdentificationID = ItemLogout.IdentificationID');
		$select->joinLeft('Items', 'CombinedRecords.IdentificationID = Items.IdentificationID', array());
		$select->joinLeft('OSW', 'CombinedRecords.IdentificationID = OSW.IdentificationID', array());
		$select->joinInner('ItemReport', 'CombinedRecords.IdentificationID = ItemReport.IdentificationID', array('Count' => 'GetReportItemCount(ItemReport.ReportID)', 'TotalHours' => 'GetConservatorHours(ItemReport.ReportID, NULL)'));
		$select->joinLeft('CallNumbers', 'CombinedRecords.IdentificationID = CallNumbers.IdentificationID', array('CallNumbers' => 'GetCallNumberString(CombinedRecords.IdentificationID)'));
		$select->joinInner('Locations', 'CombinedRecords.HomeLocationID = Locations.LocationID', array(LocationDAO::LOCATION));
		$select->joinInner(array('WorkLocations' => 'Locations'), 'ItemReport.WorkLocationID = WorkLocations.LocationID', array(LocationDAO::ACRONYM));
		$where = 'CombinedRecords.ProjectID=' . $project->getPrimaryKey();
		
		$zendstartdate = new Zend_Date($dateRange->getStartDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$startdate = $zendstartdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$startdate = $this->getAdapter()->quote($startdate);
		$zendenddate = new Zend_Date($dateRange->getEndDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$enddate = $zendenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$enddate = $this->getAdapter()->quote($enddate);
		
		$where .= ' AND ((ItemLogout.LogoutDate >= ' . $startdate . ' AND ItemLogout.LogoutDate <= ' . $enddate . ')';
		$where .= ' OR (OSW.WorkEndDate >= ' . $startdate . ' AND OSW.WorkEndDate <= ' . $enddate . '))';
	
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		$accesslevel = $identity[PeopleDAO::ACCESS_LEVEL];
		$userrepository = $identity[LocationDAO::LOCATION_ID];
		//If the user is not an admin or regular WPC user, then they should be limited to their repository.
		if ($accesslevel != PeopleDAO::ACCESS_LEVEL_ADMIN && $accesslevel != PeopleDAO::ACCESS_LEVEL_REGULAR)
		{
			$where .= ' AND HomeLocationID = ' . $userrepository;
		}
		
		$select->where($where);
		$select->order(array(LocationDAO::LOCATION, self::RECORD_TYPE, self::RECORD_ID));
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
	
	public function getCompletedByTub(DateRange $dateRange)
	{
		$select = $this->select();
		$select->from($this, array(self::RECORD_ID, self::RECORD_TYPE, ItemDAO::TITLE, ItemDAO::AUTHOR_ARTIST));
		$select->setIntegrityCheck(FALSE);
		$select->joinLeft('ItemLogout', 'CombinedRecords.IdentificationID = ItemLogout.IdentificationID');
		$select->joinLeft('Items', 'CombinedRecords.IdentificationID = Items.IdentificationID', array());
		$select->joinLeft('OSW', 'CombinedRecords.IdentificationID = OSW.IdentificationID', array());
		$select->joinInner('ItemReport', 'CombinedRecords.IdentificationID = ItemReport.IdentificationID', array('Count' => 'GetReportItemCount(ItemReport.ReportID)', 'TotalHours' => 'GetConservatorHours(ItemReport.ReportID, NULL)'));
		$select->joinLeft('CallNumbers', 'CombinedRecords.IdentificationID = CallNumbers.IdentificationID', array('CallNumbers' => 'GetCallNumberString(CombinedRecords.IdentificationID)'));
		$select->joinInner('Locations', 'CombinedRecords.HomeLocationID = Locations.LocationID', array(LocationDAO::LOCATION, LocationDAO::TUB));
		$select->joinInner('Purposes', 'CombinedRecords.PurposeID = Purposes.PurposeID', array(PurposeDAO::PURPOSE));
		
		$zendstartdate = new Zend_Date($dateRange->getStartDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$startdate = $zendstartdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$startdate = $this->getAdapter()->quote($startdate);
		$zendenddate = new Zend_Date($dateRange->getEndDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$enddate = $zendenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$enddate = $this->getAdapter()->quote($enddate);
		
		$where = '(ItemLogout.LogoutDate >= ' . $startdate . ' AND ItemLogout.LogoutDate <= ' . $enddate . ')';
		$where .= ' OR (OSW.WorkEndDate >= ' . $startdate . ' AND OSW.WorkEndDate <= ' . $enddate . ')';
		$select->where($where);
		$select->order(array(LocationDAO::TUB, LocationDAO::LOCATION, self::RECORD_TYPE, self::RECORD_ID));
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

	/**
	 * The report only pulls in the hours within the specific date range. 
 	 * For example, a Record Number may have a total of 20 hours but only 12 fall
 	 * within the date range specified. The 12 hours would show up on the report, not
 	 * the 20. 
	 * @param DateRange $dateRange
	 * @return multitype:
	 */
	public function getWorkDoneBy(DateRange $dateRange)
	{
		$select = $this->select();
		$select->from($this, array(CombinedRecordsDAO::RECORD_ID, CombinedRecordsDAO::RECORD_TYPE, FormatDAO::FORMAT_ID, PurposeDAO::PURPOSE_ID, 'ChargeTo' => 'GetChargeToText(' . ItemDAO::CHARGE_TO_ID . ')'));
		$select->setIntegrityCheck(FALSE);
		$select->joinLeft('ItemLogin', 'CombinedRecords.IdentificationID = ItemLogin.IdentificationID', array(LoginDAO::LOGIN_DATE));
		$select->joinLeft('ItemLogout', 'CombinedRecords.IdentificationID = ItemLogout.IdentificationID', array(LogoutDAO::LOGOUT_DATE));
		$select->joinInner('ItemReport', 'CombinedRecords.IdentificationID = ItemReport.IdentificationID', array('Count' => 'GetReportItemCount(ItemReport.ReportID)', 'AdminOnly', 'ExamOnly', 'CustomHousingOnly'));
		$select->joinInner('ItemConservators', 'ItemReport.ReportID = ItemConservators.ReportID', array(ReportDAO::COMPLETED_HOURS, ReportDAO::DATE_COMPLETED));
		$select->joinInner('People', 'ItemConservators.PersonID = People.PersonID', array(PeopleDAO::DISPLAY_NAME));
		$select->joinLeft('Projects', 'CombinedRecords.ProjectID = Projects.ProjectID', array(ProjectDAO::PROJECT_NAME));
		$select->joinInner('Locations', 'CombinedRecords.HomeLocationID = Locations.LocationID', array(LocationDAO::LOCATION));
		
		$startdate = $dateRange->getDatabaseStartDate();	
		$startdate = $this->getAdapter()->quote($startdate);
		$enddate = $dateRange->getDatabaseEndDate();
		$enddate = $this->getAdapter()->quote($enddate);
		
		$where = '(ItemConservators.DateCompleted >= ' . $startdate . ' AND ItemConservators.DateCompleted <= ' . $enddate . ')';
		$select->where($where);
		$select->order(array('GetChargeToText(' . ItemDAO::CHARGE_TO_ID . ')', CombinedRecordsDAO::RECORD_TYPE, CombinedRecordsDAO::RECORD_ID));
		//$select->distinct();
		Logger::log($select->__toString(), Zend_Log::DEBUG);
		$searchresults = $this->fetchAll($select);
		$retval = array();
		if (!is_null($searchresults))
		{
			$retval = $searchresults->toArray();
		}
		return $retval;
	}
	
} 

?>