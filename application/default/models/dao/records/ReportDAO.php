<?php
/**
 * ReportDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class ReportDAO extends Zend_Db_Table
{
	const REPORT_ID = 'ReportID';
	const IDENTIFICATION_ID = 'IdentificationID';
	const REPORT_BY_ID = 'ReportByID';
	const REPORT_DATE = 'ReportDate';
	const FORMAT_ID = 'FormatID';
	const SUMMARY = 'Summary';
	const WORK_LOCATION_ID = 'WorkLocationID';
	const EXAM_ONLY = 'ExamOnly';
	const CUSTOM_HOUSING_ONLY = 'CustomHousingOnly';
	const ADMIN_ONLY = 'AdminOnly';
	const ADDITIONAL_MATERIALS_ON_FILE = 'AdditionalMaterialsOnFile';
	const TREATMENT = 'Treatment';
	const PRESERVATION_RECOMMENDATIONS = 'PreservationRecommendations';
	const HEIGHT = 'Height';
	const WIDTH = 'Width';
	const THICKNESS = 'Thickness';
	const DIMENSION_UNIT = 'DimensionUnit';
	
	//From ItemConservators table
	const COMPLETED_HOURS = 'CompletedHours';
	const DATE_COMPLETED = 'DateCompleted';
	
	//Counts
	const TOTAL_COUNT = 'TotalCount';
	const COUNT_DESCRIPTION = 'Description';
	const COUNT_TYPE = 'CountType';
	
	
	
	/* The name of the table */
	protected $_name = "ItemReport";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "ReportID";

    private static $reportDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return ReportDAO
     */
    public static function getReportDAO()
	{
		if (!isset(self::$reportDAO))
		{
			self::$reportDAO = new ReportDAO();
		}
		return self::$reportDAO;
	}
	
	
    /**
     * Returns the item report with the given id.
     *
     * @access public
     * @param  Integer identificationID
     * @return Report
     */
    public function getReport($identificationID)
    {
    	$item = NULL;
    	if (!empty($identificationID))
    	{
	    	$identificationID = $this->getAdapter()->quote($identificationID, 'INTEGER');
	    	$select = $this->select();
	    	$select->where('IdentificationID=' . $identificationID);
	    	$row = $this->fetchRow($select);
	    	if (!is_null($row))
	    	{
	    		$item = $this->buildItemReport($row->toArray());
	    	}
    	}
    	return $item;
    }
    
    /*
     * @access private
     * @param  array values from the database
     * @return Report
     */
    private function buildItemReport(array $values)
    {
    	$report = new Report($values[self::REPORT_ID]);
    	$report->setIdentificationID($values[self::IDENTIFICATION_ID]);
    	$report->setAdminOnly($values[self::ADMIN_ONLY]);
    	$report->setCustomHousingOnly($values[self::CUSTOM_HOUSING_ONLY]);
    	$report->setExamOnly($values[self::EXAM_ONLY]);
    	$report->setFormat($values[self::FORMAT_ID]);
    	$report->setPreservationRecommendations($values[self::PRESERVATION_RECOMMENDATIONS]);
    	$report->setReportBy($values[self::REPORT_BY_ID]);
    	$report->setReportDate($values[self::REPORT_DATE]);
    	$report->setSummary($values[self::SUMMARY]);
    	$report->setTreatment($values[self::TREATMENT]);
    	$report->setWorkLocation($values[self::WORK_LOCATION_ID]);
    	$report->setAdditionalMaterialsOnFile($values[self::ADDITIONAL_MATERIALS_ON_FILE]);
    	$report->setDimensions($this->buildDimensions($values));
    	return $report;
    }
    
	/*
     * @access private
     * @param  array values from the database
     * @return Dimensions
     */
    private function buildDimensions(array $values)
    {
    	$dims = new Dimensions();
    	$dims->setHeight($values[self::HEIGHT]);
    	$dims->setWidth($values[self::WIDTH]);
    	$dims->setThickness($values[self::THICKNESS]);
    	$dims->setUnit($values[self::DIMENSION_UNIT]);
    	return $dims;
    }

    /**
     * Returns the conservators with the given id.
     *
     * @access public
     * @param  Integer reportID
     * @return array of ReportConservator objects
     */
    public function getReportConservators($reportID)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from('ItemConservators', array('PersonID', 'CompletedHours', 'DateCompleted'));
        $select->order('DateCompleted ASC');
    	$select->joinInner('People', 'ItemConservators.PersonID = People.PersonID', array('DisplayName', 'Initials', 'Inactive'));
    	$reportID = $this->getAdapter()->quote($reportID, 'INTEGER');
	    $select->where('ReportID=' . $reportID);
	    $rows = $this->fetchAll($select);
    	$conservatorarray = array();
	    if (!is_null($rows))
	    {
	    	$rowarray = $rows->toArray();
	    	foreach ($rowarray as $row)
	    	{
	    		$conservator = new ReportConservator();
	    		$conservatorperson = new Person($row[PeopleDAO::PERSON_ID], $row[PeopleDAO::INACTIVE]);
	    		$conservatorperson->setDisplayName($row[PeopleDAO::DISPLAY_NAME]);
	    		$conservatorperson->setInitials($row[PeopleDAO::INITIALS]);
	    		$conservator->setConservator($conservatorperson);
	    		$conservator->setHoursWorked($row[self::COMPLETED_HOURS]);
	    		$conservator->setDateCompleted($row[self::DATE_COMPLETED]);
	    		array_push($conservatorarray, $conservator);
	    	}
	    }
	    return $conservatorarray;
    }
    
	/**
     * Returns the final counts with the given id.
     *
     * @access public
     * @param  Integer reportID
     * @return Counts object
     */
    public function getCounts($reportID)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from('ReportCounts', array('CountType', 'TotalCount', 'Description'));
    	$reportID = $this->getAdapter()->quote($reportID, 'INTEGER');
	    $select->where('ReportID=' . $reportID);
	    Logger::log($select->__toString());
    	$rows = $this->fetchAll($select);
    	$count = new Counts();
	    if (!is_null($rows))
	    {
	    	$rowarray = $rows->toArray();
	    	foreach ($rowarray as $value)
	    	{
	    		switch ($value[self::COUNT_TYPE])
	    		{
	    			case "Volumes":
	    				$count->setVolumeCount($value[self::TOTAL_COUNT]);
	    				$count->setVolumeDescription($value[self::COUNT_DESCRIPTION]);
	    				break;
	    			case "Sheets":
	    				$count->setSheetCount($value[self::TOTAL_COUNT]);
	    				$count->setSheetDescription($value[self::COUNT_DESCRIPTION]);
	    				break;
	    			case "Photos":
	    				$count->setPhotoCount($value[self::TOTAL_COUNT]);
	    				$count->setPhotoDescription($value[self::COUNT_DESCRIPTION]);
	    				break;
	    			case "Boxes":
	    				$count->setBoxCount($value[self::TOTAL_COUNT]);
	    				$count->setBoxDescription($value[self::COUNT_DESCRIPTION]);
	    				break;
	    			case "Other":
	    				$count->setOtherCount($value[self::TOTAL_COUNT]);
	    				$count->setOtherDescription($value[self::COUNT_DESCRIPTION]);
	    				break;
	    			case "Housings":
	    				$count->setHousingCount($value[self::TOTAL_COUNT]);
	    				$count->setHousingDescription($value[self::COUNT_DESCRIPTION]);
	    				break;
	    		}
	    	}
	    }
	    return $count;
    }
    
	/**
     * Returns the final counts with the given id.
     *
     * @access public
     * @param  Integer reportID
     * @return Counts object
     */
    public function getConservatorHours($reportID, Person $conservator)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from('ItemReport', array('TotalHours' => 'GetConservatorHours(ItemReport.ReportID, ' . $conservator->getPrimaryKey() . ')'));
    	$reportID = $this->getAdapter()->quote($reportID, 'INTEGER');
	    $select->where('ReportID=' . $reportID);
	    Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$row = $this->fetchRow($select);
    	return $row->TotalHours;
    }
    
	/**
     * Saves the Item Report information
     * @access public
     * @param  Item item
     */
    public function saveItemReport(Report $report, Report $oldReport = NULL, Item $item, $db = NULL)
    {
    	$reportid = $report->getPrimaryKey();
        //Build the array to save
        $reportarray = $this->buildReportArray($report);
        
        if (is_null($db))
        {
	        $db = $this->getAdapter();
	   		$db->beginTransaction();
	   		try {
		   		//If the item isn't in the database,
		        //insert it
		        if (is_null($reportid) || !$this->reportExists($reportid))
		        {
		        	$reportarray[self::IDENTIFICATION_ID] = $item->getPrimaryKey();
		        	$db->insert('ItemReport', $reportarray);
		        	$reportid = $db->lastInsertId();
		        	$insertcounts = $this->buildInsertCounts($report->getCounts());
		        	$workdoneby = $report->getReportConservators();
		        	$importances = $report->getImportances();	
		        }
		        //otherwise, update it
		        else 
		        {
		        	$db->update('ItemReport', $reportarray, 'ReportID=' . $reportid);
	
		        	$insertcounts = $this->buildInsertCounts($report->getCounts());
		        	$removalcounts = $this->buildRemovalCounts($report->getCounts());
		        	$this->removeCounts($db, $reportid, $removalcounts);
		        	
		        	$workdoneby = $report->getWorkDoneByDifference($oldReport->getReportConservators());
		        	$removalworkdoneby = $oldReport->getWorkDoneByDifference($report->getReportConservators());
		        	$this->removeWorkDoneBy($db, $reportid, $removalworkdoneby);
		        	
		        	$importances = $report->getImportancesDifference($oldReport->getImportances());
		        	$removalimportances = $oldReport->getImportancesDifference($report->getImportances());
		        	$this->removeImportances($db, $reportid, $removalimportances);	
		        }    
	
		        //Also update the comments
		        ItemDAO::getItemDAO()->updateComments($item, $db);
		        
		        $this->insertCounts($db, $reportid, $insertcounts);
		        $this->insertWorkDoneBy($db, $reportid, $workdoneby);
		        if (!empty($importances))
		        {
	   				$this->insertImportances($db, $reportid, $importances);
		        }
	   			
	   			$db->commit();
	   		}
	   		catch (Exception $e)
	   		{
	   			Logger::log($e->getMessage(), Zend_Log::ERR);
	   			$db->rollBack();
	   			$reportid = NULL;
	   		}
        }
        else 
        {
        	//If the item isn't in the database,
	        //insert it
	        if (is_null($reportid) || !$this->reportExists($reportid))
	        {
	        	$reportarray[self::IDENTIFICATION_ID] = $item->getPrimaryKey();
	        	$db->insert('ItemReport', $reportarray);
	        	$reportid = $db->lastInsertId();
	        	$insertcounts = $this->buildInsertCounts($report->getCounts());
	        	$workdoneby = $report->getReportConservators();	
	        	$importances = $report->getImportances();
	        }
	        //otherwise, update it
	        else 
	        {
	        	$db->update('ItemReport', $reportarray, 'ReportID=' . $reportid);

	        	$insertcounts = $this->buildInsertCounts($report->getCounts());
	        	$removalcounts = $this->buildRemovalCounts($report->getCounts());
	        	$this->removeCounts($db, $reportid, $removalcounts);
	        	
	        	$workdoneby = $report->getWorkDoneByDifference($oldReport->getReportConservators());
	        	$removalworkdoneby = $oldReport->getWorkDoneByDifference($report->getReportConservators());
	        	$this->removeWorkDoneBy($db, $reportid, $removalworkdoneby);
	        		
	        	$importances = $report->getImportancesDifference($oldReport->getImportances());
	        	$removalimportances = $oldReport->getImportancesDifference($report->getImportances());
	        	$this->removeImportances($db, $reportid, $removalimportances);	
	        }    

	        //Also update the comments
	        ItemDAO::getItemDAO()->updateComments($item, $db);
	        
	        $this->insertCounts($db, $reportid, $insertcounts);
	        $this->insertWorkDoneBy($db, $reportid, $workdoneby);
	        if (!empty($importances))
	        {
   				$this->insertImportances($db, $reportid, $importances);
	        }
        }
   		return $reportid; 
    }

	/*
     * Data to be loaded into the Item Report table
     */
    private function buildReportArray(Report $report)
    {
    	$zenddate = new Zend_Date($report->getReportDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$date = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$array = array(
    		self::REPORT_BY_ID => $report->getReportBy(),
    		self::REPORT_DATE => $date,
    		self::ADMIN_ONLY => $report->isAdminOnly(),
    		self::CUSTOM_HOUSING_ONLY => $report->isCustomHousingOnly(),
    		self::DIMENSION_UNIT => $report->getDimensions()->getUnit(),
    		self::EXAM_ONLY => $report->isExamOnly(),
    		self::FORMAT_ID => $report->getFormat(),
    		self::HEIGHT => $report->getDimensions()->getHeight(),
    		self::PRESERVATION_RECOMMENDATIONS => $report->getPreservationRecommendations(),
    		self::SUMMARY => $report->getSummary(),
    		self::THICKNESS => $report->getDimensions()->getThickness(),
    		self::TREATMENT => $report->getTreatment(),
    		self::WIDTH => $report->getDimensions()->getWidth(),
    		self::WORK_LOCATION_ID => $report->getWorkLocation(),
    		self::ADDITIONAL_MATERIALS_ON_FILE => $report->additionalMaterialsOnFile()
    	);
    	return $array;
    }
    
	private function reportExists($reportID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(ReportID)'));
    	$select->where('ReportID=' . $reportID);
    	$row = $this->fetchRow($select);
    	return $row->Count = 1;
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
	       		$count[self::REPORT_ID] = $reportid;
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
       			self::REPORT_ID => $reportid,
       			PeopleDAO::PERSON_ID => $conservator->getConservator()->getPrimaryKey(),
       			self::DATE_COMPLETED => $date,
       			self::COMPLETED_HOURS => $conservator->getHoursWorked()
       		);
       		Logger::log("Inserting work done by");
       		Logger::log($workdonebyarray);
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
     * Insert importances
     */
    private function insertImportances($db, $reportid, array $importances)
    {
    	foreach ($importances as $importance)
       	{
       		$importancearray = array(
       			self::REPORT_ID => $reportid,
       			ImportanceDAO::IMPORTANCE_ID => $importance->getPrimaryKey()
       		);
       		$db->insert('ItemImportances', $importancearray);
       	}
    }
    
	/*
     * Remove related importances
     */
    private function removeImportances($db, $reportid, array $importances)
    {
    	foreach ($importances as $importance)
       	{
       		$db->delete('ItemImportances', 'ReportID=' . $reportid . ' AND ImportanceID=' . $importance->getPrimaryKey());
       	}
    }
    
	/*
     * Creates multiple reports based
     * 
     * @param array items - an array of items to insert/update
     * @param array oldreports - key = identificationID, value = Report object
     */
    public function updateMultipleItems(array $items, array $oldreports)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		$itemsupdated = 0;
   		try {
   			foreach ($items as $item)
    		{
    			$oldreport = $oldreports[$item->getPrimaryKey()];
    			$reportid = $this->saveItemReport($item->getFunctions()->getReport(), $oldreport, $item, $db);
    			if (!is_null($reportid))
    			{
    				$itemsupdated++;
    			}
    		}
	        
   			$db->commit();
   		}
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$itemsupdated = 0;
	   	}
	   	return $itemsupdated;
    }
} 

?>