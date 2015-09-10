<?php
/**
 * OnSiteWorkDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class OnSiteWorkDAO extends Zend_Db_Table
{
	const OSW_ID = 'OSWID';
	const CURATOR_ID = 'CuratorID';
	const APPROVING_CURATOR_ID = 'ApprovingCuratorID';
	const IDENTIFICATION_ID = 'IdentificationID';
	const PROPOSED_HOURS = 'ProposedHours';
	const WORK_START_DATE = 'WorkStartDate';
	const WORK_END_DATE = 'WorkEndDate';
	const COMMENTS = 'Comments';
	const HOME_LOCATION_ID = 'HomeLocationID';
	const TITLE = 'Title';
	const EDIT_COUNTER = 'EditCounter';
	const IS_BEING_EDITED = 'IsBeingEdited';
	const EDITED_BY_ID = 'EditedByID';
	const CHARGE_TO_ID = 'ChargeToID';
	
	//Counts
	const TOTAL_COUNT = 'TotalCount';
	const COUNT_DESCRIPTION = 'Description';
	const COUNT_TYPE = 'CountType';
	
	
	/* The name of the table */
	protected $_name = "OSW";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "OSWID";

    private static $onsiteworkDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return OnSiteWorkDAO
     */
    public static function getOnSiteWorkDAO()
	{
		if (!isset(self::$onsiteworkDAO))
		{
			self::$onsiteworkDAO = new OnSiteWorkDAO();
		}
		return self::$onsiteworkDAO;
	}
	
	
    /**
     * Returns the item with the given item id.
     *
     * @access public
     * @param  Integer oswID
     * @return OnSiteWork
     */
    public function getOSW($oswID)
    {
    	$osw = NULL;
	    if (!empty($oswID))
    	{
	    	$oswID = $this->getAdapter()->quote($oswID, 'INTEGER');
	    	$select = $this->buildBaseSelect();
	    	$select->where('OSWID=' . $oswID);
	    	Logger::log($select->__toString());
	    	$row = $this->fetchRow($select);
	    	if (!is_null($row))
	    	{
	    		$osw = $this->buildOSW($row->toArray());
	    	}
    	}
    	return $osw;
    }
    
    /*
     * @access private
     * @param  array values from the database
     * @return OnSiteWork
     */
    private function buildOSW(array $values)
    {
    	$osw = new OnSiteWork($values[self::IDENTIFICATION_ID]);
    	$osw->setComments($values[self::COMMENTS]);
    	$osw->setDateRange($this->buildDateRange($values[self::WORK_START_DATE], $values[self::WORK_END_DATE]));
    	$osw->setProposedHours($values[self::PROPOSED_HOURS]);
    	$osw->setDepartmentID($values[DepartmentDAO::DEPARTMENT_ID]);
    	$osw->setGroupID($values[GroupDAO::GROUP_ID]);
    	$osw->setHomeLocationID($values[self::HOME_LOCATION_ID]);
    	$osw->setChargeToID($values[self::CHARGE_TO_ID]);
    	$osw->setOSWID($values[self::OSW_ID]);
    	$osw->setProjectID($values[ProjectDAO::PROJECT_ID]);
    	$osw->setPurposeID($values[PurposeDAO::PURPOSE_ID]);
    	$osw->setTitle($values[self::TITLE]);
    	$osw->setCuratorID($values[self::CURATOR_ID]);
    	$osw->setFormatID($values[FormatDAO::FORMAT_ID]);
    	return $osw;
    }

    private function buildDateRange($start, $end)
    {
    	$daterange = new DateRange();
    	$daterange->setStartDate($start, ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	$daterange->setEndDate($end, ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	return $daterange;
    }
    
	public function deleteOSW(OnSiteWork $osw)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		try {
	   		$rowsdeleted = $db->delete('ItemIdentification', 'IdentificationID=' . $osw->getPrimaryKey());	
	        $db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$rowsdeleted = 0;
	   	}
	   	return $rowsdeleted;
    }

	public function oswExists($oswID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(OSWID)'));
    	$select->where('OSWID=' . $oswID);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }
    
	public function setEditStatus(OnSiteWork $osw, $status, $editedByID = NULL)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		try 
   		{
   			$updatearray = array(self::EDITED_BY_ID => $editedByID, self::IS_BEING_EDITED => $status);
   			$rowsupdated = $db->update('ItemIdentification', $updatearray, 'IdentificationID=' . $osw->getPrimaryKey());	
	        $db->commit();
	        if ($rowsupdated == 0)
	        {
	        	$rowsupdated = 1;
	        }
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$rowsupdated = 0;
	   	}
	   	return $rowsupdated;
    }
    
	/**
     * Saves the OSW information
     * @access public
     * @param  OnSiteWork item
     * @param  OnSiteWork oldItem - the item currently in the database
     */
    public function saveOSW(OnSiteWork $osw, OnSiteWork $oldOsw = NULL)
    {
        //Build the array to save
        $oswid = $osw->getOSWID();
        $identificationid = $osw->getPrimaryKey();
        
        $report = $osw->getFunctions()->getReport();
        $reportid = $report->getPrimaryKey();
        
        $identificationarray = $this->buildIdentificationArray($osw);
        $oswarray = $this->buildOSWArray($osw);
        $reportarray = $this->buildReportArray($osw->getFunctions()->getReport());
        
        $db = $this->getAdapter();
   		$db->beginTransaction();
   		try {
   			$auth = Zend_Auth::getInstance();
   			$identity = $auth->getIdentity();
	   		$audittrail = new AuditTrail();
	        $zenddate = new Zend_Date();
			$audittrail->setDate($zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT));
	        $audittrail->setTableName('ItemIdentification');
	        $audittrail->setPersonID($identity[PeopleDAO::PERSON_ID]);
	        
	        //If the item isn't in the database,
	        //insert it
	        if (is_null($oswid) || !$this->oswExists($oswid))
	        {
	        	$db->insert('ItemIdentification', $identificationarray);
	        	$identificationid = $db->lastInsertId();
	        	$oswarray[self::IDENTIFICATION_ID] = $identificationid;
	        	$db->insert($this->_name, $oswarray);
	        	$oswid = $db->lastInsertId();
	        	$reportarray[self::IDENTIFICATION_ID] = $identificationid;
	        	$db->insert('ItemReport', $reportarray);
	        	$reportid = $db->lastInsertId();
	        	
	        	//Related arrays
	        	$insertcounts = $this->buildInsertCounts($report->getCounts());
	        	$workdoneby = $report->getReportConservators();	
	        	$worktypes = $osw->getWorkTypes();	
	        
	        	//Related arrays
	        	$callnumbers = $osw->getCallNumbers();

	        	$audittrail->setObjectPKID($identificationid);
	        	$audittrail->setActionType(AuditTrailDAO::ACTION_INSERT);
	        }
	        //otherwise, update it
	        else 
	        {
	        	$db->update('ItemIdentification', $identificationarray, 'IdentificationID=' . $identificationid);	
	        	$db->update($this->_name, $oswarray, 'OSWID=' . $oswid);	
	        	$db->update('ItemReport', $reportarray, 'IdentificationID=' . $identificationid);	
	        	
	        	$insertcounts = $this->buildInsertCounts($report->getCounts());
	        	$removalcounts = $this->buildRemovalCounts($report->getCounts());
	        	$this->removeCounts($db, $report->getPrimaryKey(), $removalcounts);
	        	
	        	$workdoneby = $report->getWorkDoneByDifference($oldOsw->getFunctions()->getReport()->getReportConservators());
	        	$removalworkdoneby = $oldOsw->getFunctions()->getReport()->getWorkDoneByDifference($osw->getFunctions()->getReport()->getReportConservators());
	        	$this->removeWorkDoneBy($db, $report->getPrimaryKey(), $removalworkdoneby);
	        	
	        	$worktypes = $osw->getWorkTypeDifference($oldOsw->getWorkTypes());
	        	$removalworktypes = $oldOsw->getWorkTypeDifference($osw->getWorkTypes());
	        	$this->removeWorkTypes($db, $osw->getOSWID(), $removalworktypes);
	        	
	        	$callnumbers = $osw->getCallNumberDifference($oldOsw->getCallNumbers());
	        	$removalcallnumbers = $oldOsw->getCallNumberDifference($osw->getCallNumbers());
	        	$this->removeCallNumbers($db, $identificationid, $removalcallnumbers);
	        	
	        	$audittrail->setActionType(AuditTrailDAO::ACTION_UPDATE);
	        }
	        
	        $this->insertCounts($db, $reportid, $insertcounts);
	        $this->insertWorkDoneBy($db, $reportid, $workdoneby);
	        $this->insertWorkTypes($db, $oswid, $worktypes);
	        $this->insertCallNumbers($db, $identificationid, $callnumbers);
	        
   			$audittrail->setPrimaryKey($identificationid);
	        AuditTrailDAO::getAuditTrailDAO()->insertAuditTrailItem($audittrail, $db);
   			
   			$db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$oswid = NULL;
   		}
   		return $oswid; 
    }
    
    /*
     * Insert related call numbers
    */
    private function insertCallNumbers($db, $identificationid, array $callnumbers)
    {
    	foreach ($callnumbers as $callnumber)
    	{
    		$callnumberarray = array(
    				CombinedRecordsDAO::CALL_NUMBER => trim($callnumber),
    				self::IDENTIFICATION_ID => $identificationid
    		);
    		Logger::log($callnumberarray, Zend_Log::DEBUG);
	    	$db->insert('CallNumbers', $callnumberarray);
    	}
    }
    
    /*
     * Remove related call numbers
    */
    private function removeCallNumbers($db, $identificationid, array $callnumbers)
    {
    	foreach ($callnumbers as $callnumber)
    	{
    		$callnumber = $this->getAdapter()->quote($callnumber);
    		$db->delete('CallNumbers', 'IdentificationID=' . $identificationid . ' AND CallNumber=' . $callnumber);
    	}
    }
    
	/*
     * Data to be loaded into the ItemIdentification table
     */
	private function buildIdentificationArray(OnSiteWork $item)
    {
    	$idarray = array(
    		self::COMMENTS => $item->getComments(),
    		DepartmentDAO::DEPARTMENT_ID => $item->getDepartmentID(),
    		GroupDAO::GROUP_ID => $item->getGroupID(),
    		ProjectDAO::PROJECT_ID => $item->getProjectID(),
    		self::HOME_LOCATION_ID => $item->getHomeLocationID(),
    		self::CHARGE_TO_ID => $item->getChargeToID(),
    		PurposeDAO::PURPOSE_ID => $item->getPurposeID(),
    		self::TITLE => $item->getTitle(),
    		self::CURATOR_ID => $item->getCuratorID(),
    		self::APPROVING_CURATOR_ID => $item->getCuratorID()
    	);
    	return $idarray;
    }
    
	/*
     * Data to be loaded into the OSW table
     */
	private function buildOSWArray(OnSiteWork $item)
    {
    	$idarray = array(
    		self::PROPOSED_HOURS => $item->getProposedHours(),
    		self::WORK_START_DATE => $item->getDateRange()->getDatabaseStartDate(),
    		self::WORK_END_DATE => $item->getDateRange()->getDatabaseEndDate(),
    		FormatDAO::FORMAT_ID => $item->getFormatID(),
    	);
    	return $idarray;
    }
    
	/*
     * Data to be loaded into the Item Report table
     */
    private function buildReportArray(Report $report)
    {
    	$zenddate = new Zend_Date($report->getReportDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$date = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$array = array(
    		ReportDAO::REPORT_BY_ID => $report->getReportBy(),
    		ReportDAO::REPORT_DATE => $date,
    		ReportDAO::TREATMENT => $report->getTreatment(),
    		ReportDAO::WORK_LOCATION_ID => $report->getWorkLocation()
    	);
    	return $array;
    }
    
	private function buildInsertCounts(Counts $counts)
    {
    	$countsarray = array();
    	if (($counts->getOtherCount() != 0 || !is_null($counts->getOtherDescription())))
    	{
    		$countsarray["Other"] = array(
    			self::COUNT_TYPE => "Other",
    			self::COUNT_DESCRIPTION => $counts->getOtherDescription(),
    			self::TOTAL_COUNT => $counts->getOtherCount()
    		);
    	}
    	if ($counts->getVolumeCount() != 0 || !is_null($counts->getVolumeDescription()))
    	{
    		$countsarray["Volumes"] = array(
    			self::COUNT_TYPE => "Volumes",
    			self::COUNT_DESCRIPTION => $counts->getVolumeDescription(),
    			self::TOTAL_COUNT => $counts->getVolumeCount()
    		);
    	}
    	if ($counts->getSheetCount() != 0 || !is_null($counts->getSheetDescription()))
    	{
    		$countsarray["Sheets"] = array(
    			self::COUNT_TYPE => "Sheets",
    			self::COUNT_DESCRIPTION => $counts->getSheetDescription(),
    			self::TOTAL_COUNT => $counts->getSheetCount()
    		);
    	}
    	if ($counts->getPhotoCount() != 0 || !is_null($counts->getPhotoDescription()))
    	{
    		$countsarray["Photos"] = array(
    			self::COUNT_TYPE => "Photos",
    			self::COUNT_DESCRIPTION => $counts->getPhotoDescription(),
    			self::TOTAL_COUNT => $counts->getPhotoCount()
    			);
    	}
    	if ($counts->getBoxCount() != 0 || !is_null($counts->getBoxDescription()))
    	{
    		$countsarray["Boxes"] = array(
    			self::COUNT_TYPE => "Boxes",
    			self::COUNT_DESCRIPTION => $counts->getBoxDescription(),
    			self::TOTAL_COUNT => $counts->getBoxCount()
    			);
    	}
    	if ($counts->getHousingCount() != 0 || !is_null($counts->getHousingDescription()))
    	{
    		$countsarray["Housings"] = array(
    			self::COUNT_TYPE => "Housings",
    			self::COUNT_DESCRIPTION => $counts->getHousingDescription(),
    			self::TOTAL_COUNT => $counts->getHousingCount()
    			);
    	}
		return $countsarray;
    }
    
	private function buildRemovalCounts(Counts $counts)
    {
    	$countsarray = array();
    	if ($counts->getOtherCount() == 0 && is_null($counts->getOtherDescription()))
    	{
    		array_push($countsarray, "Other");
    	}
    	if ($counts->getVolumeCount() == 0 && is_null($counts->getVolumeDescription()))
    	{
    		array_push($countsarray, "Volumes");
    	}
    	if ($counts->getSheetCount() == 0 && is_null($counts->getSheetDescription()))
    	{
    		array_push($countsarray, "Sheets");
    	}
    	if ($counts->getPhotoCount() == 0 && is_null($counts->getPhotoDescription()))
    	{
    		array_push($countsarray, "Photos");
    	}
    	if ($counts->getBoxCount() == 0 && is_null($counts->getBoxDescription()))
    	{
    		array_push($countsarray, "Boxes");
    	}
    	if ($counts->getHousingCount() == 0 && is_null($counts->getHousingDescription()))
    	{
    		array_push($countsarray, "Housings");
    	}
    	return $countsarray;
    }
    
	/*
     * Insert related counts
     */
    private function insertCounts($db, $reportid, array $counts)
    {
    	foreach ($counts as $count)
       	{
       		if ($this->countTypeExists($count[self::COUNT_TYPE], $reportid))
       		{
       			$counttype = $this->getAdapter()->quote($count[self::COUNT_TYPE]);
       			$db->update('ReportCounts', $count, 'ReportID=' . $reportid . ' AND CountType=' . $counttype);
       		}
       		else 
       		{
	       		$count[ReportDAO::REPORT_ID] = $reportid;
	       		$db->insert('ReportCounts', $count);
       		}
       	}
    }
    
    private function countTypeExists($counttype, $reportid)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from('ReportCounts', array('Count' => 'Count(*)'));
    	$counttype = $this->getAdapter()->quote($counttype);
    	$select->where('CountType=' . $counttype . ' AND ReportID=' . $reportid);
    	Logger::log($select->__toString());
    	$row = $this->fetchRow($select);
    	return $row->Count > 0;
    }
    
	/*
     * Remove related counts
     */
    private function removeCounts($db, $reportid, array $counts)
    {
    	foreach ($counts as $count)
       	{
       		$db->delete('ReportCounts', 'ReportID=' . $reportid . ' AND CountType="' . $count . '"');
       	}
    }
    
	/*
     * Insert work done by
     */
    private function insertWorkDoneBy($db, $reportid, array $workdoneby)
    {
    	foreach ($workdoneby as $conservator)
       	{
       		$date = $conservator->getDateCompleted();
       		$date = DateFormat::getInternalDate($date);
       		
       		$workdonebyarray = array(
       			ReportDAO::REPORT_ID => $reportid,
       			PeopleDAO::PERSON_ID => $conservator->getConservator()->getPrimaryKey(),
       			ReportDAO::DATE_COMPLETED => $date,
       			ReportDAO::COMPLETED_HOURS => $conservator->getHoursWorked()
       		);
       		$db->insert('ItemConservators', $workdonebyarray);
       	}
    }
    
	/*
     * Remove related work done by
     */
    private function removeWorkDoneBy($db, $reportid, array $workdoneby)
    {
    	foreach ($workdoneby as $conservator)
       	{
       		$date = $conservator->getDateCompleted();
       		if (!empty($date))
       		{
	       		$date = DateFormat::getInternalDate($date);
	    		$where = 'ReportID=' . $reportid . ' AND PersonID=' . $conservator->getConservator()->getPrimaryKey() . ' AND DateCompleted=\'' . $date . '\''; 
       		}
       		else 
       		{
       			$where = 'ReportID=' . $reportid . ' AND PersonID=' . $conservator->getConservator()->getPrimaryKey() . ' AND DateCompleted IS NULL';
       		}
       		Logger::log("Removing work done by with query . " . $where);
       		$db->delete('ItemConservators', $where);
       	}
    }
    
	/*
     * Insert curators
     */
    private function insertCurators($db, $identificationid, array $curators)
    {
    	foreach ($curators as $curator)
       	{
       		$curatorarray = array(
       			self::IDENTIFICATION_ID => $identificationid,
       			PeopleDAO::PERSON_ID => $curator->getPrimaryKey()
       		);
       		$db->insert('Curators', $curatorarray);
       	}
    }
    
	/*
     * Remove related curators
     */
    private function removeCurators($db, $identificationid, array $curators)
    {
    	foreach ($curators as $curator)
       	{
       		$db->delete('Curators', 'IdentificationID=' . $identificationid . ' AND PersonID=' . $curator->getPrimaryKey());
       	}
    }
    
	/*
     * Insert work types
     */
    private function insertWorkTypes($db, $oswid, array $worktypes)
    {
    	foreach ($worktypes as $worktype)
       	{
       		$worktypearray = array(
       			self::OSW_ID => $oswid,
       			WorkTypeDAO::WORK_TYPE_ID => $worktype->getPrimaryKey()
       		);
       		$db->insert('OSWWorkTypes', $worktypearray);
       	}
    }
    
	/*
     * Remove related work types
     */
    private function removeWorkTypes($db, $oswid, array $worktypes)
    {
    	foreach ($worktypes as $worktype)
       	{
       		$db->delete('OSWWorkTypes', 'OSWID=' . $oswid . ' AND WorkTypeID=' . $worktype->getPrimaryKey());
       	}
    }
    
	protected function buildBaseSelect()
	{
		$select = $this->select();
	    $select->setIntegrityCheck(FALSE);
	    $select->from($this, array('*'));
	    $select->joinInner('ItemIdentification', 
	    		'OSW.IdentificationID = ItemIdentification.IdentificationID', 
	    		array('CuratorID', 'PurposeID', 'HomeLocationID', 'Title', 'DepartmentID', 'GroupID', 'ProjectID',
	    			'Comments', 'Inactive', 'NonDigitalImagesExist', 'ChargeToID'));
	    return $select;
	}
	/**
     * Returns the items based on the search criteria
     *
     * @access public
     * @param  array searchparameters (SearchParameter objects)
     * @return JSON ready array
     */
    public function getOSWItems(array $searchparameters, $startIndex = 0, $count = 50, $additionalwhere = NULL, $order = 'OSWID')
    {
    	$retval = array();
	    if (!empty($searchparameters) || !empty($additionalwhere))
    	{
	        $select = $this->buildBaseSelect();
		    $select = $this->appendSearchClause($select, $searchparameters);
		    //If it is null, then the search is not an osw search.
		    if (!is_null($select))
		    {
		    	if (!empty($additionalwhere))
		    	{
		    		$select->where($additionalwhere);
		   	 	}
			    
		    	$select = $this->populateOrderBy($select, $order);
		    	
		    	$select->limit($count, $startIndex);
		     	Logger::log($select->__toString(), Zend_Log::DEBUG);
		   
		   		$searchresults = $this->fetchAll($select);
		    	if (!is_null($searchresults))
		    	{
		    		$rowarray = $searchresults->toArray();
		    		foreach ($rowarray as $row)
		    		{
		    			$osw = $this->buildOSW($row);
		    			$retval[$osw->getOSWID()] = $osw;
		    		}
		    	}
		    }
		}
	    return $retval;
    }
    

    private function populateOrderBy($select, $order)
    {
    	$updatedorder = array();
	    $coordinatororder = FALSE;
	    $curatororder = FALSE;
	    $departmentsorder = FALSE;
	    $tuborder = FALSE;
	    $projectorder = FALSE;
	    $repositoryorder = FALSE;
	    
	    $identtable = $this->getAdapter()->describeTable('ItemIdentification');
	    $oswtable = $this->getAdapter()->describeTable('OSW');
	    $reporttable = $this->getAdapter()->describeTable('ItemReport');
	    if (is_array($order))
	    {
	    	foreach ($order as $o)
	    	{
	    		$orderarray = explode(' ', $o);
	    		$o = $orderarray[0];
	    		$sort = 'asc';
	    		if (isset($orderarray[1]))
	    		{
	    			$sort = $orderarray[1];
	    		}
	    		
	    		if ($o == 'Coordinator')
	    		{
	    			$coordinatororder = TRUE;
	    			array_push($updatedorder, 'Coordinator.DisplayName' . ' ' . $sort);
	    		}
	    		elseif ($o == 'Curator')
	    		{
	    			$curatororder = TRUE;
	    			array_push($updatedorder, 'Curator.DisplayName' . ' ' . $sort);
	    		}
    			elseif ($o == 'DepartmentName')
	    		{
	    			$departmentsorder = TRUE;
	    			array_push($updatedorder, 'LocationsOrder.Location' . ' ' . $sort);
    				array_push($updatedorder, 'DepartmentsOrder.DepartmentName' . ' ' . $sort);
	    		}
	    		elseif ($o == 'ProjectName')
	    		{
	    			$projectorder = TRUE;
	    			array_push($updatedorder, 'ProjectsOrder.ProjectName' . ' ' . $sort);
    			}
	    		elseif ($o == 'Repository')
	    		{
	    			$repositoryorder = TRUE;
	    			array_push($updatedorder, 'LocationsOrder.Location' . ' ' . $sort);
    			}
	    		elseif ($o == 'TUB')
	    		{
	    			$tuborder = TRUE;
	    			array_push($updatedorder, 'LocationsOrder.TUB' . ' ' . $sort);
    			}
	    		else 
	    		{
	    			if ($o == 'RecordID' 
	    				|| array_key_exists($o, $oswtable) 
	    				|| array_key_exists($o, $reporttable)
	    				|| array_key_exists($o, $identtable))
			    	{
			    		array_push($updatedorder, $this->determineOrder($o) . ' ' . $sort);
			    	}
	    		}
	    	}
	    }
	    else 
	    {
	    	$orderarray = explode(' ', $order);
	    	$order = $orderarray[0];
	    	$sort = 'asc';
	    	if (isset($orderarray[1]))
	    	{
	    		$sort = $orderarray[1];
	    	}
	    	
	    	if ($order == 'Curator')
    		{
    			$curatororder = TRUE;
    			array_push($updatedorder, 'Curator.DisplayName' . ' ' . $sort);
    		}
    		elseif ($order == 'DepartmentName')
    		{
    			$departmentsorder = TRUE;
    			array_push($updatedorder, 'LocationsOrder.Location' . ' ' . $sort);
    			array_push($updatedorder, 'DepartmentsOrder.DepartmentName' . ' ' . $sort);
    		}
    		elseif ($order == 'ProjectName')
	    	{
	    		$projectorder = TRUE;
	    		array_push($updatedorder, 'ProjectsOrder.ProjectName' . ' ' . $sort);
    		}
	    	elseif ($order == 'Repository')
	    	{
	    		$repositoryorder = TRUE;
	    		array_push($updatedorder, 'LocationsOrder.Location' . ' ' . $sort);
    		}
	    	elseif ($order == 'TUB')
    		{
    			$tuborder = TRUE;
    			array_push($updatedorder, 'LocationsOrder.TUB' . ' ' . $sort);
    		}
    		else 
    		{
    			if ($order == 'RecordID' 
    				|| array_key_exists($order, $oswtable) 
    				|| array_key_exists($order, $reporttable)
    				|| array_key_exists($order, $identtable))
			    {
			    	array_push($updatedorder, $this->determineOrder($order) . ' ' . $sort);
			    }
    		}
	    }
	    
	    if ($coordinatororder)
	    {
	    	$select->joinInner(array('Coordinator' => 'People'),
	    		'Coordinator.PersonID = Items.CoordinatorID', array());
	    }
	    if ($curatororder)
	    {
	    	$select->joinInner(array('Curator' => 'People'),
	    		'Curator.PersonID = ItemIdentification.CuratorID', array());
	    }
	    if ($projectorder)
	    {
	    	$select->joinLeft(array('ProjectsOrder' => 'Projects'),
	    		'ItemIdentification.ProjectID = ProjectsOrder.ProjectID', array());
	    }
	    if ($departmentsorder)
	    {
	    	$select->joinLeft(array('DepartmentsOrder' => 'Departments'),
	    		'ItemIdentification.DepartmentID = DepartmentsOrder.DepartmentID', array());
	    	$select->joinLeft(array('LocationsOrder' => 'Locations'),
	    		'DepartmentsOrder.LocationID = LocationsOrder.LocationID', array());
	    }
	    if ($tuborder || $repositoryorder)
	    {
	    	if (!$departmentsorder)
	    	{
	    		$select->joinInner(array('LocationsOrder' => 'Locations'),
	    		'ItemIdentification.HomeLocationID = LocationsOrder.LocationID', array());
	    	}
	    }
	    $select->order($updatedorder);
	    return $select;
    }
    
	private function determineOrder($order)
    {
    	if ($order == 'RecordID')
    	{
    		$order = 'OSWID';
    	}
    	return $order;
    }
    
	/**
     * Returns the items based on the search criteria
     *
     * @access public
     * @param  array searchparameters (SearchParameter objects)
     * @return JSON ready array
     */
    public function getOSWCount(array $searchparameters, $additionalwhere = NULL)
    {
    	$count = 0;
    	if (!empty($searchparameters) || !empty($additionalwhere))
    	{
	        $select = $this->select();
			$select->setIntegrityCheck(FALSE);
			$select->from($this, array('Count' => 'COUNT(*)'));
			$select->joinInner('ItemIdentification', 
		    		'OSW.IdentificationID = ItemIdentification.IdentificationID', 
		    		array());
		
		    $select = $this->appendSearchClause($select, $searchparameters, TRUE);
		    //If it is null, then the search is not an osw search.
		    if (!is_null($select))
		    {
		    	if (!empty($additionalwhere))
		    	{
		    		$select->where($additionalwhere);
		    	}
		    	Logger::log($select->__toString(), Zend_Log::DEBUG);
		    	$searchresult = $this->fetchRow($select);
		    	$count = $searchresult->Count;
		    }
		    
    	}
    	return $count;
    }
    
    /*
     * TODO: Have to include functionality for other search types
     * and boolean comparisons.
     */
    private function appendSearchClause($select, array $searchparameters, $countonly = FALSE)
    {
    	$reportexists = FALSE;
    	$conservatorsexists = FALSE;
    	$worktypesexist = FALSE;
    	
    	$reportappended = FALSE;
    	$conservatorsappended = FALSE;
    	$worktypesappended = FALSE;
    	
    	$where = "";
    	$delimiter = "";
    	$countopen = 0;
    	foreach ($searchparameters as $param)
    	{
    		$value = $param->getValue();
    		$column = $param->getColumnName();
    		//Get only the column name
    		if (strpos($column, ".") > 0)
    		{
	    		$columncompare = substr($column, strpos($column, ".") + 1);
	    		$tablename = substr($column, 0, strpos($column, "."));
    		}
    		else 
    		{
    			$columncompare = $column;
    			$tablename = NULL;
    		}
    		if ($column == 'RecordID')
    		{
    			$column = self::OSW_ID;
    		}
    		
    		$open = $param->getOpenParenthesis();
    		//Increment the number of open parens when one is opened.
    		if (!empty($open))
    		{
    			$countopen++;
    		}
    		
    		$delimiter = $param->getBooleanValue();
    		if (!empty($where) && !empty($delimiter))
	    	{
	    		$delimiter = ' ' . $delimiter . ' ';
	    	}
	    	else
	    	{
	    		$delimiter = '';
	    	}
	    	
    		$closed = $param->getClosedParenthesis();
    		//Decrement the number of open parens when one is closed.
    		if (!empty($closed))
    		{
    			$countopen--;
    		}
    		
    		//If the column is the item status, then
    		//this is for the Item search, not the OSW search.
    		if ($column == 'ItemStatus' || $tablename == 'ItemLogin' || $tablename == 'ItemProposal'
    			|| $tablename == 'ItemLogout' || $tablename == 'ItemImportances'
    			|| $tablename == 'Items' || ($columncompare == 'Activity' && $value != 'On-site'))
    		{
    			return NULL;
    		}
    		
    		if ($column == 'OSWStatus')
    		{
    			$statusclause = "";
    			switch ($value)
    			{
    				//Not logged in
    				case 'Open':
    					$statusclause = "OSW.WorkEndDate IS NULL";
    					break;
    				//Logged in but not logged out
    				case 'Closed':
    					$statusclause = "OSW.WorkEndDate IS NOT NULL";
    					break;
    			}
    			if (!empty($statusclause))
    			{
	    			$delimiter = ' ' . $delimiter . ' ';
		    		$where .= $delimiter . $open . $statusclause . $closed;
    			}
    		}
    		else
    		{
	    		$datatype = $this->getDataType($columncompare, $tablename);
	    		if ($tablename == 'ItemReport')
		    	{
		    		$reportexists = TRUE;
		    	}
		    	if ($tablename == 'ItemConservators')
		    	{
		    		$reportexists = TRUE;
		    		$conservatorsexists = TRUE;
		    	}
		    	if ($tablename == 'OSWWorkTypes')
		    	{
		    		$worktypesexist = TRUE;
		    	}
    		}
	    		
	    	$searchtype = $param->getSearchType();							

	    	if ($searchtype == SearchParameters::SEARCH_TYPE_BEGINS_WITH)
	    	{
	    		if ($datatype == 'int' || $datatype == 'decimal')
	    		{
	    			$searchtype = SearchParameters::SEARCH_TYPE_GREATER_THAN;
	    		}
	    		else 
	    		{
	    			$searchtype = "LIKE";
	    			$value = $value . '%';	
	    		}
	    	}	
	    	elseif ($searchtype == SearchParameters::SEARCH_TYPE_CONTAINS)
	    	{
	    		if ($datatype != 'int' && $datatype != 'decimal')
	    		{
	    			$searchtype = "LIKE";
	    			$value = '%' . $value . '%';
	    		}
	    		else 
	    		{
	    			$searchtype = '=';
	    		}
	    	}	
	    	elseif ($searchtype == SearchParameters::SEARCH_TYPE_EQUALS)
	    	{
	    		$searchtype = '=';
	    		if (!$param->isZeroOrBlankSearch() && ($value == '' || $value == 0))
	    		{
	    			$value = NULL;
	    		}
	    	}
	    		
	    	$searchtype = ' ' . $searchtype . ' ';
	    		
	    	if ($column != 'Activity' && $column != 'OSWStatus') 
	    	{
		    	if (!is_null($value))
		    	{
		    		$value = $this->getAdapter()->quote($value, $datatype);
		    		$where .= $delimiter . $open . $column . $searchtype . $value . $closed;
		    	}
		    	else 
		    	{
		    		$where .= $delimiter . $open . $column . ' IS NULL ' . $closed;
		    	}
	    	}
	   }
    	
    	//Close all remaining open parens.
    	while ($countopen > 0)
    	{
    		$where .= ')';
    		$countopen--;
    	}
    	
    	
    	//If the report exists, then add the join
    	if ($reportexists && !$reportappended)
    	{
    		$columnarray = array();
    		if (!$countonly)
    		{
    			$columnarray = array('ReportByID', 'ReportDate');
    		}
    		$select->joinInner('ItemReport', 
		    	'OSW.IdentificationID = ItemReport.IdentificationID', 
		    	$columnarray);
		    $reportappended = TRUE;
    	}
    	
    	//If the conservators item exists, then add the join
    	if ($conservatorsexists && !$conservatorsappended)
    	{
    		$columnarray = array();
    		$select->joinInner('ItemConservators', 
		    	'ItemReport.ReportID = ItemConservators.ReportID', 
		    	$columnarray);
		    $conservatorsappended = TRUE;
    	}
    	
    	//If the worktypes item exists, then add the join
    	if ($worktypesexist && !$worktypesappended)
    	{
    		$columnarray = array();
    		$select->joinInner('OSWWorkTypes', 
		    	'OSW.OSWID = OSWWorkTypes.OSWID', 
		    	$columnarray);
		    $worktypesappended = TRUE;
    	}
    	
    	if (!empty($where))
    	{
    		$select->where($where);
    		$select->distinct();
    	}
    	return $select;
    }
    
    private function getDataType($column, $tablename = NULL)
    {
    	$datatype = 'VARCHAR';
    	if (!is_null($tablename))
    	{
	    	$table = $this->getAdapter()->describeTable($tablename);
	    	
	    	if (array_key_exists($column, $table))
	    	{
		    	$datatype = $table[$column]['DATA_TYPE'];
	    	}
    	}
    	elseif ($column == 'RecordID')
    	{
    		$datatype = 'int';
    	}
   		return $datatype;		
    }
    
    /*
     * Returns whether the osw record is open or closed.
     * 
     * @param mixed - the oswid
     * @return mixed - 'None' if the oswid doesn't exist, 'Open' if no work end date is found, 'Closed' otherwise.
     */
    public function getOSWStatus($oswid)
    {
    	$select = $this->select();
    	$select->from($this, array('WorkEndDate'));
    	$select->where('OSWID=' . $oswid);
    	$row = $this->fetchRow($select);
    	$status = 'None';
    	if (!is_null($row))
    	{
    		$wed = $row->WorkEndDate;
    		if (empty($wed))
    		{
    			$status = 'Open';
    		}
    		else 
    		{
    			$status = 'Closed';
    		}
    	}
    	return $status;
    }
    
	public function getOSWID($identificationid)
    {
    	$select = $this->select();
    	$select->from($this, array('OSWID'));
    	$select->where('IdentificationID=' . $identificationid);
    	$row = $this->fetchRow($select);
    	$itemid = NULL;
    	if (!is_null($row))
    	{
    		$itemid = $row->OSWID;
    	}
    	return $itemid;
    }
} 

?>