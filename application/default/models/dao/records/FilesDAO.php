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
 * FilesDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
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
	const UPLOAD_STATUS = 'UploadStatus';
	const ENTERED_BY_ID = 'EnteredByID';
	
	const LINK_TYPE_OSW = 'OSW';
	const LINK_TYPE_ITEM = 'Item';
	const LINK_TYPE_GROUP = 'Group';
	
	const UPLOAD_STATUS_PENDING = 'Pending';
	const UPLOAD_STATUS_COMPLETE = 'Complete';
	
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
    	$file->setUploadStatus($values[self::UPLOAD_STATUS]);
    	$enteredBy = NULL;
    	if (!is_null($values[self::ENTERED_BY_ID]))
    	{
    		$enteredBy = PeopleDAO::getPeopleDAO()->getPerson($values[self::ENTERED_BY_ID]);
    	}
    	$file->setEnteredBy($enteredBy);
    	return $file;
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
    		self::UPLOAD_STATUS => $file->getUploadStatus(),
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
} 

?>