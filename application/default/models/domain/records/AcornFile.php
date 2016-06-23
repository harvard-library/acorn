<?php
class AcornFile extends AcornClass
{
	private $relatedPrimaryKeyID = NULL;
	private $path = NULL;
	private $description = NULL;
	private $linkType = FilesDAO::LINK_TYPE_ITEM;
	private $fileType = NULL;
	private $fileName = NULL;
	private $dateEntered;
	private $lastModified;
	private $uploadStatus = NULL;
	private $enteredBy = NULL;
	
	//These are the only images supported by Batch Builder
	private static $IMAGE_ARRAY = array("jp2", "gif", "tif", 
								"tiff", "jpg", "pcd");
	
    /**
     * @access public
     * @return string
     */
    public function getDescription()
    {
    	return $this->description;
    }

    /**
     * @access public
     * @return string
     */
    public function getLinkType()
    {
    	return $this->linkType;
    }

    /**
     * @access public
     * @return string
     */
    public function getPath()
    {
    	return $this->path;
    }
    
	/**
     * @access public
     * @return int
     */
    public function getRelatedPrimaryKeyID()
    {
    	return $this->relatedPrimaryKeyID;
    }

	/**
     * @access public
     * @return string
     */
    public function getFileType()
    {
    	if (is_null($this->fileType))
    	{
    		$this->fileType = $this->calculateFileType();
    	}
    	return $this->fileType;
    }
    
    private function calculateFileType()
    {
    	$suffix = self::getFileExtension($this->path);
    		
    	$type = "Unknown";
    	//As long as there was a suffix, get it, otherwise we assign it to 'Unknown'
    	if (!empty($suffix))
    	{
    		if (in_array($suffix, self::$IMAGE_ARRAY))
    		{
    			$type = "Image";
    		}
    		elseif ($suffix == 'doc')
    		{
    			$type = 'Word Document';
    		}
    		elseif ($suffix == 'txt')
    		{
    			$type = 'Text File';
    		}
    		elseif ($suffix == 'ppt')
    		{
    			$type = 'Power Point';
    		}
    		elseif ($suffix == 'xls')
    		{
    			$type = 'Excel File';
    		}
    		elseif ($suffix == 'pdf')
    		{
    			$type = 'PDF';
    		}
    		else 
    		{
    			$type = "Unknown";
    		}
    	}
    	return $type;
    }
    
    public static function getFileExtension($path)
    {
    	$patharray = explode(".", $path);
    	$suffix = "";
    	//As long as there was a suffix, get it, otherwise we assign it to 'Unknown'
    	if (count($patharray) > 1)
    	{
    		$suffix = end($patharray);
    		$suffix = strtolower($suffix);
    	}
    	return $suffix;
    }
    
	/**
	 * @return datetime
	 */
	public function getDateEntered()
	{
		return $this->dateEntered;
	}
	
	/**
	 * @return datetime
	 */
	public function getLastModified()
	{
		return $this->lastModified;
	}
	
	/**
	 * @return string
	 */
	public function getFileName()
	{
		return $this->fileName;
	}
	
	/**
	 * @param string $fileName
	 */
	public function setFileName($fileName)
	{
		$this->fileName = $fileName;
	}

	
	/**
	 * @param datetime $dateEntered
	 */
	public function setDateEntered($dateEntered)
	{
		$this->dateEntered = $dateEntered;
	}
	
	/**
	 * @param datetime $lastModified
	 */
	public function setLastModified($lastModified)
	{
		$this->lastModified = $lastModified;
	}

    /**
     * Sets the PK of the related item
     *
     * @access public
     * @param  Integer relatedPrimaryKeyID
     */
    public function setRelatedPrimaryKeyID($relatedPrimaryKeyID)
    {
    	$this->relatedPrimaryKeyID = $relatedPrimaryKeyID;
    }

    /**
     * @access public
     * @param  String description
     */
    public function setDescription($description)
    {
    	$this->description = $description;
    }

    /**
     * @access public
     * @param  string linkType
     */
    public function setLinkType($linkType)
    {
    	$this->linkType = $linkType;
    }

    /**
     * @access public
     * @param  String path
     */
    public function setPath($path)
    {
    	$this->path = $path;	
    }

    /**
     * @access public
     * @param  String fileType
     */
    public function setFileType($fileType)
    {
    	$this->fileType = $fileType;	
    }
    
    /**
     * Determine the filetype based on the extension.
     */
    public static function parseFileType($filename)
    {
    	$extension = self::getFileExtension($filename);
    	$filetype = "Other";
    	if (!empty($extension))
    	{
    		if (in_array($extension, self::$IMAGE_ARRAY))
    		{
    			$filetype = "Image";
    		}
    		elseif ($extension == "txt")
    		{
    			$filetype = "Text";
    		}
    		elseif ($extension == "doc" || $extension == "docx")
    		{
    			$filetype = "Microsoft Word";
    		}
    		elseif ($extension == "xls" || $extension == "xlsx"  || $extension == "xlsm"  || $extension == "xlsb")
    		{
    			$filetype = "Microsoft Excel";
    		}
    		elseif ($extension == "ppt" || $extension == "pptx"  || $extension == "pps"  || $extension == "ppsx")
    		{
    			$filetype = "Microsoft Power Point";
    		}
    		elseif ($extension == "mda" || $extension == "mdb"  || $extension == "mdf"  || $extension == "mde")
    		{
    			$filetype = "Microsoft Access";
    		}
    		elseif ($extension == "xml")
    		{
    			$filetype = "XML";
    		}
    		elseif ($extension == "pdf")
    		{
    			$filetype = "PDF";
    		}
    		else
    		{
    			$filetype = "Other";
    		}
    	}
    	return $filetype;
    }
	
	/**
	 * @return string
	 */
	public function getUploadStatus()
	{
		return $this->uploadStatus;
	}
	
	/**
	 * @param string $uploadStatus
	 */
	public function setUploadStatus($uploadStatus)
	{
		$this->uploadStatus = $uploadStatus;
	}
	
	/**
	 * @return Person
	 */
	public function getEnteredBy()
	{
		return $this->enteredBy;
	}
	
	/**
	 * @param Person $enteredBy
	 */
	public function setEnteredBy($enteredBy)
	{
		$this->enteredBy = $enteredBy;
	}

	static function acornFilenameCompare($file1, $file2)
	{
		$fn1 = $file1->getFileName();
	    $fn2 = $file2->getFileName();
	    //Find the before treatment locs
	    $beforeTreatment1 = strpos($fn1, "-BT-");
	    $beforeTreatment2 = strpos($fn2, "-BT-");
	    if ($beforeTreatment1 !== FALSE && $beforeTreatment2 !== FALSE)
	    {
	    	return strcmp($fn1, $fn2);
	    }
	    elseif ($beforeTreatment1 !== FALSE)
	    {
	    	return -1;
	    }
	    elseif ($beforeTreatment2 !== FALSE) 
	    {
	    	return 1;
	    }
	    
	    //If one wasn't a BT, then do DT comparisons
		//Find the during treatment locs
	    $duringTreatment1 = strpos($fn1, "-DT-");
	    $duringTreatment2 = strpos($fn2, "-DT-");
	    if ($duringTreatment1 !== FALSE && $duringTreatment2 !== FALSE)
	    {
	    	return strcmp($fn1, $fn2);
	    }
	    elseif ($duringTreatment1 !== FALSE)
	    {
	    	return -1;
	    }
	    elseif ($duringTreatment2 !== FALSE) 
	    {
	    	return 1;
	    }
	    
	    //If one wasn't a DT, then do AT comparisons
		//Find the during treatment locs
	    $afterTreatment1 = strpos($fn1, "-AT-");
	    $afterTreatment2 = strpos($fn2, "-AT-");
	    if ($afterTreatment1 !== FALSE && $afterTreatment2 !== FALSE)
	    {
	    	return strcmp($fn1, $fn2);
	    }
	    elseif ($afterTreatment1 !== FALSE)
	    {
	    	return -1;
	    }
	    elseif ($afterTreatment2 !== FALSE) 
	    {
	    	return 1;
	    }
	    
	    //This pushes all of the non BT,DT,AT files to the bottom.
	    return strcmp($fn1, $fn2);
	}
		
	public static function getSortedFileNames(array $filenamearray)
	{
		uasort($filenamearray, array("AcornFile","acornFilenameCompare"));
		return $filenamearray;
	}
	
	public function __toString()
    {
    	return $this->getPath();
    }
} 

?>