<?php 
/**
 * FilesDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class FilesDAO extends Zend_Db_Table
{
	const FILE_ID = 'FileID';
	const PKID = 'PKID';
	const FILE_NAME = 'FileName';
	const PATH = 'Path';
	const DESCRIPTION = 'Description';
	const FILE_TYPE = 'FileType';
	const LINK_TYPE = 'LinkType';
	const DATE_ENTERED = 'DateEntered';
	const LAST_MODIFIED = 'LastModified';
	const DRS_STATUS = 'DRSStatus';
	const ENTERED_BY_ID = 'EnteredByID';
	const DRS_BATCH_NAME = 'DRSBatchName';
	
	const LINK_TYPE_OSW = 'OSW';
	const LINK_TYPE_ITEM = 'Item';
	const LINK_TYPE_GROUP = 'Group';
	
	const DRS_STATUS_PENDING = 'Pending';
	const DRS_STATUS_COMPLETE = 'Complete';
	
    /* The name of the table */
	protected $_name = "Files";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "FileID";

    private static $filesDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return FilesDAO
     */
    public static function getFilesDAO()
	{
		if (!isset(self::$filesDAO))
		{
			self::$filesDAO = new FilesDAO();
		}
		return self::$filesDAO;
	}

	/**
     * Returns an array of files
     *
     * @access public
     * @param int pkid = the primary key (IdentificationID or GroupID)
     * @param string -the link type
     * @return array of AcornFile objects
     */
    public function getFiles($pkid, $linkType = self::LINK_TYPE_ITEM, $narrowByName = NULL)
    {
    	$select = $this->select();
    	$pkid = $this->getAdapter()->quote($pkid, 'INTEGER');
    	$linkTypeCompare = $linkType;
    	$linkType = $this->getAdapter()->quote($linkType);
    	$where = '((PKID=' . $pkid . ' AND LinkType=' . $linkType . ')';
    	if ($linkTypeCompare == self::LINK_TYPE_ITEM)
    	{
    		$itemid = ItemDAO::getItemDAO()->getItemID($pkid);
    		$item = ItemDAO::getItemDAO()->getItem($itemid);
    		$groupid = $item->getGroupID();
    		if (!empty($groupid))
    		{
    			$where .= ' OR (PKID=' . $groupid . ' AND LinkType=\'' . self::LINK_TYPE_GROUP . '\')';
    		}
    	}
    	elseif ($linkTypeCompare == self::LINK_TYPE_OSW)
    	{
    		$oswid = OnSiteWorkDAO::getOnSiteWorkDAO()->getOSWID($pkid);
    		$osw = OnSiteWorkDAO::getOnSiteWorkDAO()->getOSW($oswid);
    		$groupid = $osw->getGroupID();
    		if (!empty($groupid))
    		{
    			$where .= ' OR (PKID=' . $groupid . ' AND LinkType=\'' . self::LINK_TYPE_GROUP . '\')';
    		}
    	}
    	$where .= ")";
    	if (!is_null($narrowByName))
    	{
    		$narrowByName = $this->getAdapter()->quote("%" . $narrowByName . "%");
    		$where .= "AND " . self::FILE_NAME . " LIKE " . $narrowByName;
    	}
    	$select->where($where);
    	Logger::log($select->__toString());
    	$rows = $this->getAdapter()->fetchAssoc($select);
    	$retval = array();
    	if (!is_null($rows))
    	{
    		foreach($rows as $row)
    		{
    			$file = $this->buildAcornFile($row);
    			$retval[$row[self::FILE_ID]] = $file;
    		}
    	}
    	return $retval;
    }
    

    /*
     * @access private
     * @param  array values from the database
     * @return AcornFile
     */
    private function buildAcornFile(array $values)
    {
    	$file = new AcornFile($values[self::FILE_ID]);
    	$file->setDescription($values[self::DESCRIPTION]);
    	$file->setLinkType($values[self::LINK_TYPE]);
    	$file->setPath($values[self::PATH]);
    	$file->setFileName($values[self::FILE_NAME]);
    	$file->setRelatedPrimaryKeyID($values[self::PKID]);
    	$file->setFileType($values[self::FILE_TYPE]);
    	$file->setDateEntered($values[self::DATE_ENTERED]);
    	$file->setLastModified($values[self::LAST_MODIFIED]);
    	$file->setDrsStatus($values[self::DRS_STATUS]);
    	$file->setDrsBatchName($values[self::DRS_BATCH_NAME]);
    	$enteredBy = NULL;
    	if (!is_null($values[self::ENTERED_BY_ID]))
    	{
    		$enteredBy = PeopleDAO::getPeopleDAO()->getPerson($values[self::ENTERED_BY_ID]);
    	}
    	$file->setEnteredBy($enteredBy);
    	return $file;
    }
    
	/**
     * Returns an array of files for the supplied batch
     *
     * @access public
     * @param string batchName 
     * @return array of AcornFile objects
     */
    public function getFilesInBatch($batchName)
    {
    	$select = $this->select();
    	$batchName = $this->getAdapter()->quote($batchName);
    	$select->where(self::DRS_BATCH_NAME . "=" . $batchName);
    	Logger::log($select->__toString());
    	$rows = $this->getAdapter()->fetchAssoc($select);
    	$retval = array();
    	if (!is_null($rows))
    	{
    		foreach($rows as $row)
    		{
    			$file = $this->buildAcornFile($row);
    			$retval[$row[self::FILE_ID]] = $file;
    		}
    	}
    	return $retval;
    }
   
    
	public function removeAcornFile($fileid, $linktype)
    {
    	$db = $this->getAdapter();
    	$this->removeFiles($db, array($fileid), $linktype);
    }
    
	/*
     * Remove related files
     */
    private function removeFiles($db, array $fileids, $linktype)
    {
    	Logger::log($fileids, Zend_Log::DEBUG);
    	$linktype = $db->quote($linktype);
    	foreach ($fileids as $fileid)
       	{Logger::log('FileID=' . $fileid . ' AND LinkType=' . $linktype);
       		$db->delete('Files', 'FileID=' . $fileid . ' AND LinkType=' . $linktype);
       	}
    }
    
    public function saveAcornFile(AcornFile $file)
    {
    	$db = $this->getAdapter();
    	return $this->saveFile($file, $db);
    }
    
    /*
     * Save the file
     */
    private function saveFile(AcornFile $file, $db)
    {
    	$fileid = $file->getPrimaryKey();
    	$zenddate = new Zend_Date();
		$filearray = array(
    		self::DESCRIPTION => $file->getDescription(),
    		self::FILE_TYPE => $file->getFileType(),
    		self::PATH => $file->getPath(),
    		self::FILE_NAME => $file->getFileName(),
    		self::PKID => $file->getRelatedPrimaryKeyID(),
    		self::LINK_TYPE => $file->getLinkType(),
    		self::LAST_MODIFIED => $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT),
    		self::DRS_STATUS => $file->getDrsStatus(),
    		self::DRS_BATCH_NAME => $file->getDrsBatchName()
    	);
    	
    	Logger::log($filearray);
    	
    	if (!is_null($file->getEnteredBy()))
    	{
    		$filearray[self::ENTERED_BY_ID] = $file->getEnteredBy()->getPrimaryKey();
    	}
    	
    	if (is_null($fileid) || !$this->fileExists($fileid))
    	{
    		$filearray[self::DATE_ENTERED] = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
    		$db->insert($this->_name, $filearray);
    		$fileid = $db->lastInsertId();
    	}
    	else 
    	{
    		Logger::log('updating: ' . $fileid, Zend_Log::DEBUG);
    		$db->update($this->_name, $filearray, 'FileID=' . $fileid);
    	}
    	return $fileid;
    }
    
	public function fileExists($fileid)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(FileID)'));
    	$select->where('FileID=' . $fileid);
    	Logger::log($select->__toString());
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }
	
	public function filenameExists($basefilename)
    {
    	$select = $this->select();
    	$basefilename = $this->getAdapter()->quote($basefilename . '.%');
    	$select->from($this, array('Count' => 'Count(FileName)'));
    	$select->where('FileName LIKE ' . $basefilename);
    	Logger::log($select->__toString());
    	$row = $this->fetchRow($select);
    	return $row->Count > 0;
    }
    
	/**
     * Updates the drs info for the given DRSLoadReportFiles.
     * 
     * @param array - an array of DRSLoadReportFiles.
     * @param status - the status to update the batches to.
     * @return  boolean - true if the update was successful.
     */
    public function updateDRSInfo(array $loadReportFiles)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		$success = TRUE;
   		try {
   			foreach ($loadReportFiles as $loadReportFile)
   			{
   				$this->updateDRSFile($loadReportFile, $db);
   			}
	        $db->commit();
	    }
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$success = FALSE;
   		}
   		return $success;
    }
    
    private function updateDRSFile(DRSLoadReportFile $file, $db)
    {
    	$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
    	
        $filename = substr($file->getFilePath(), strrpos($file->getFilePath(), "/") + 1);
      	$extension = AcornFile::getFileExtension($filename);
      	//if (in_array($extension, array('jpg', 'jp2', 'gif')))
      	//{
      		$path = $config->getNRSPath() . $file->getUrn();
      	/*}
      	else 
      	{
      		$path = $file->getFilePath();
      	}*/
      	$updatearray = array(self::PATH => $path, self::DRS_STATUS => self::DRS_STATUS_COMPLETE);
      	Logger::log("Updating DRS file to complete: " . $filename . " with " . $path);
    	$db->update('Files', $updatearray, self::FILE_NAME . " = '" . $filename . "'");
    }
    
    public function deleteFilesInBatch($batchname)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		$batchname = $db->quote($batchname);
   		$success = TRUE;
   		try {
   			$db->delete('Files', self::DRS_BATCH_NAME . " = " . $batchname);
	        $db->commit();
	    }
   		catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$success = FALSE;
   		}
   		return $success;
    }
    
	/**
     * Returns an array of files that are 'Pending' in the DB
     *
     * @access public
     * @return array of AcornFile objects
     */
    public function getPendingDRSFiles()
    {
    	$select = $this->select();
    	$select->where("DRSStatus='Pending'");
    	Logger::log($select->__toString());
    	$rows = $this->getAdapter()->fetchAssoc($select);
    	$retval = array();
    	if (!is_null($rows))
    	{
    		foreach($rows as $row)
    		{
    			$file = $this->buildAcornFile($row);
    			$retval[$row[self::FILE_ID]] = $file;
    		}
    	}
    	return $retval;
    }
} 

?>