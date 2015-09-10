<?php

class Report extends AcornClass
{
	private $identificationID = NULL;
	private $format = NULL;
	private $reportBy = NULL;
	private $reportDate = NULL;
	private $treatment = NULL;
	private $summary = NULL;
	private $dimensions = NULL;
	private $counts = NULL;
	private $workLocation = NULL;
	private $examOnly = FALSE;
	private $customHousingOnly = FALSE;
	private $adminOnly = FALSE;
	private $additionalMaterialsOnFile = FALSE;
	private $preservationRecommendations = NULL;
	private $importances = NULL;
	private $reportConservators = NULL;

	/**
     * @access public
     * @return mixed
     */
    public function getIdentificationID()
    {
    	return $this->identificationID;
    }
    
	/**
     * @access public
     * @param  int identificationID
     */
    public function setIdentificationID($identificationID)
    {
    	$this->identificationID = $identificationID;
    }
	
    /**
     * @access public
     * @return mixed
     */
    public function isAdminOnly()
    {
    	return $this->adminOnly;
    }

    /**
     * @access public
     * @return Counts
     */
    public function getCounts()
    {
    	if (is_null($this->counts) && !is_null($this->getPrimaryKey()))
    	{
    		$this->counts = ReportDAO::getReportDAO()->getCounts($this->getPrimaryKey());
    	}
    	elseif (is_null($this->counts))
    	{
    		$this->counts = new Counts();
    	}
    	return $this->counts;
   	}

    /**
     * @access public
     * @return mixed
     */
    public function isCustomHousingOnly()
    {
    	return $this->customHousingOnly;
    }
    
    /**
     * @access public
     * @return mixed
     */
    public function additionalMaterialsOnFile()
    {
    	return $this->additionalMaterialsOnFile;
    }

    /**
     * @access public
     * @return Dimensions
     */
    public function getDimensions()
    {
    	if (is_null($this->dimensions))
    	{
    		$this->dimensions = new Dimensions();
    	}
    	return $this->dimensions;
    }

    /**
     * @access public
     * @return mixed
     */
    public function isExamOnly()
    {
    	return $this->examOnly;
    }

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
     * @return mixed
     */
    public function getActivity()
    {
    	$activity = "Treatment";
    	
    	$checkedactivity = '';
    	$delimiter = '';
    	if ($this->isAdminOnly())
    	{
    		$checkedactivity = "Admin Only";
    		$delimiter = '; ';
    	}
    	if ($this->isExamOnly())
    	{
    		$checkedactivity .= $delimiter . "Exam Only";
    		$delimiter = '; ';
    	}
    	if ($this->isCustomHousingOnly())
    	{
    		$checkedactivity .= $delimiter . "Custom Housing";
    	}
    	if (!empty($checkedactivity))
    	{
    		$activity = $checkedactivity;
    	}
    	return $activity;
    }
    
    /**
     * @access public
     * @return mixed
     */
    public function getImportances()
    {
    	if (is_null($this->importances) && !is_null($this->getPrimaryKey()))
    	{
    		$this->importances = ImportanceDAO::getImportanceDAO()->getImportances($this->getPrimaryKey());
    	}
    	elseif (is_null($this->importances))
    	{
    		$this->importances = array();
    	}
    	return $this->importances;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getPreservationRecommendations()
    {
    	return $this->preservationRecommendations;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getReportBy()
    {
    	return $this->reportBy;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getReportConservators()
    {
    	if (is_null($this->reportConservators) && !is_null($this->getPrimaryKey()))
    	{
    		$this->reportConservators = ReportDAO::getReportDAO()->getReportConservators($this->getPrimaryKey());
    	}
    	elseif (is_null($this->reportConservators))
    	{
    		$this->reportConservators = array();
    	}
    	return $this->reportConservators;
    }
    
	/**
     * @access public
     * @param  array reportConservators
     */
    public function setReportConservators($reportConservators)
    {
    	$this->reportConservators = $reportConservators;
    }
    
	/**
     * @access public
     * @param  ReportConservator conservator
     * @return Int the key of the array that the conservator was added.
     */
    public function addConservator(ReportConservator $conservator, $conservatorID)
    {
    	$conservators = $this->getReportConservators();
    	if (array_key_exists($conservatorID, $conservators))
    	{
    		$conservators[$conservatorID] = $conservator;
    	}
    	else
    	{
	    	array_push($conservators, $conservator);
	    	end($conservators);	
	    	$conservatorID = key($conservators);
	    }
    	$this->setReportConservators($conservators);
    	return $conservatorID;
    }

    /**
     * @access public
     * @param  Integer personID
     * @param String - The date in YYYY-MM-DD format
     */
    public function removeConservator($conservatorID)
    {
    	$conservators = $this->getReportConservators();
    	unset($conservators[$conservatorID]); 
    	$this->setReportConservators($conservators);
    }

    /**
     * @access public
     * @return mixed
     */
    public function getReportDate()
    {
    	return $this->reportDate;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getSummary()
    {
    	return $this->summary;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getTreatment()
    {
    	return $this->treatment;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getWorkLocation()
    {
    	return $this->workLocation;
    }

    /**
     * @access public
     * @param  Counts counts
     */
    public function setCounts(Counts $counts)
    {
    	$this->counts = $counts;
    }

    /**
     * @access public
     * @param  Dimensions dimensions
     */
    public function setDimensions(Dimensions $dimensions)
    {
    	$this->dimensions = $dimensions;
    }

    /**
     * @access public
     * @param  Format format
     */
    public function setFormat($format)
    {
    	$this->format = $format;
    }

    /**
     * @access public
     * @param  array importances
     */
    public function setImportances($importances)
    {
    	$this->importances = $importances;
    }

    /**
     * @access public
     * @param  Importance importance
     */
    public function addImportance(Importance $importance)
    {
    	$importances = $this->getImportances();
    	$importances[$importance->getPrimaryKey()] = $importance;
    	$this->setImportances($importances);
    }

    /**
     * @access public
     * @param  Integer importanceID
     */
    public function removeImportance($importanceID)
    {
    	$importances = $this->getImportances();
    	unset($importances[$importanceID]);
    	$this->setImportances($importances);
    }

    /**
     * @access public
     * @param  array preservationRecommendations
     */
    public function setPreservationRecommendations($preservationRecommendations)
    {
    	$this->preservationRecommendations = $preservationRecommendations;
    }


    /**
     * @access public
     * @param  Person reportBy
     */
    public function setReportBy($reportBy)
    {
    	$this->reportBy = $reportBy;
    }

    /**
     * @access public
     * @param  String reportDate
     */
    public function setReportDate($reportDate)
    {
    	$this->reportDate = $reportDate;
    }

    /**
     * @access public
     * @param  String summary
     */
    public function setSummary($summary)
    {
    	$this->summary = $summary;
    }

    /**
     * @access public
     * @param  String treatment
     */
    public function setTreatment($treatment)
    {
    	$this->treatment = $treatment;
    }

    /**
     * @access public
     * @param  Location workLocation
     */
    public function setWorkLocation($workLocation)
    {
    	$this->workLocation = $workLocation;
    }

    /**
     * @access public
     * @param  Boolean adminOnly
     */
    public function setAdminOnly($adminOnly)
    {
    	$this->adminOnly = $adminOnly;
    }

    /**
     * @access public
     * @param  Boolean customHousingOnly
     */
    public function setCustomHousingOnly($customHousingOnly)
    {
    	$this->customHousingOnly = $customHousingOnly;
    }

    /**
     * @access public
     * @param  Boolean examOnly
     */
    public function setExamOnly($examOnly)
    {
    	$this->examOnly = $examOnly;
    }
	
	/**
	 * @param Boolean $additionalMaterialsOnFile
	 */
	public function setAdditionalMaterialsOnFile($additionalMaterialsOnFile)
	{
		$this->additionalMaterialsOnFile = $additionalMaterialsOnFile;
	}

    /*
     * Returns the sum of the conservator hours.
     */
    public function getTotalHours()
    {
    	$conservators = $this->getReportConservators();
    	$total = 0.0;
    	foreach ($conservators as $conservator)
    	{
    		$total += $conservator->getHoursWorked();
    	}
    	return $total;
    }
    
	/*
     * Returns the array of work done by that are in this reports work done by
     * and are not in the array of compare work done by
     * @param array - work done by to compare
     * @return array - work done by not in given array
     */
    public function getWorkDoneByDifference(array $compareworkdoneby)
    {
    	return array_diff($this->getReportConservators(), $compareworkdoneby);
    }
    
/*
     * Returns the array of importances that are in this reports importances
     * and are not in the array of compare importances
     * @param array - importances to compare
     * @return array - importances not in given array
     */
    public function getImportancesDifference(array $compareimportances)
    {
    	return array_diff($this->getImportances(), $compareimportances);
    }	
    
	/*
     * Determines if the instance passed in is the same as the current instance.
     * 
     * @param Report report
     */
    public function equals(Report $report)
    {
    	//First compare basic fields to determine if they are equal.
    	$equal = $this->getFormat() == $report->getFormat();
    	$equal = $equal && $this->getPreservationRecommendations() == $report->getPreservationRecommendations();
    	$equal = $equal && $this->getReportBy() == $report->getReportBy();
    	$equal = $equal && $this->getReportDate() == $report->getReportDate();
    	$equal = $equal && $this->getSummary() == $report->getSummary();
    	$equal = $equal && $this->getTreatment() == $report->getTreatment();
    	$equal = $equal && $this->getWorkLocation() == $report->getWorkLocation();
    	
    	//If these fields are equal, determine whether the report
    	//lists have changed
    	//Importances
    	if ($equal)
    	{
    		$equal = $equal && $this->arraysDifferent($this->getImportances(), $report->getImportances());
    	}
    	//Curators
    	if ($equal)
    	{
    		$equal = $equal && $this->arraysDifferent($this->getReportConservators(), $report->getReportConservators());
    	}
    	//Counts
    	if ($equal)
    	{
    		$counts = $this->getCounts();
    		$equal = $equal && $counts->equals($report->getCounts());
    	}
    	return $equal;
    }

} 

?>