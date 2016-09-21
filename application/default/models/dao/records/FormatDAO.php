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
 * FormatDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
class FormatDAO extends Zend_Db_Table
{
	const FORMAT_ID = 'FormatID';
	const FORMAT = 'Format';
	const INACTIVE = 'Inactive';
	
    /* The name of the table */
	protected $_name = "Formats";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "FormatID";

    private static $formatDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return FormatDAO
     */
    public static function getFormatDAO()
	{
		if (!isset(self::$formatDAO))
		{
			self::$formatDAO = new FormatDAO();
		}
		return self::$formatDAO;
	}

	/**
     * Returns a list of formats
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return array, NULL if no rows found  (key = FormatID, array of all information)
     */
    public function getAllFormats($includeinactive = FALSE)
    {
    	$select = $this->select();
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	$rows = $this->getAdapter()->fetchAssoc($select);
    	return $rows;
    }
    
	/**
     * Returns a list of format formatted for a select list
     *
     * @access public
     * @param boolean includeinactive if true, return all values, false, return only active
     * @return array to be used with JSON, NULL if none found
     */
    public function getAllFormatsForSelect($includeinactive = FALSE)
    {
    	$select = $this->select();
    	if (!$includeinactive)
    	{
    		$select->where('Inactive = 0');
    	}
    	$select->order(array('Rank', 'Format'));
    	$rows = $this->fetchAll($select);
    	$retval = NULL;
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }

    /**
     * @access public
     * @param  Integer formatID
     * @return Format format
     */
    public function getFormat($formatID)
    {
    	$select = $this->select();
    	$formatID = $this->getAdapter()->quote($formatID, 'INTEGER');
    	$select->where('FormatID=' . $formatID);
    	$row = $this->fetchRow($select);
    	$retval = NULL;
    	if (!is_null($row))
    	{
    		$retval = $this->buildFormat($row->toArray());
    	}
    	return $retval;
    }

	private function buildFormat(array $data)
    {
    	$format = new Format($data[self::FORMAT_ID], $data[self::INACTIVE]);
    	$format->setFormat($data[self::FORMAT]);
    	return $format;
    }

    /**
     * @access public
     * @param  Format format
     * @return mixed
     */
    public function saveFormat(Format $format)
    {
        $array = array(
        	self::FORMAT => $format->getFormat(),
        	self::INACTIVE => $format->isInactive()
        );
        $db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try {
	   		$formatid = $format->getPrimaryKey();
	        if (is_null($formatid) || !$this->formatExists($formatid))
	        {
	        	$db->insert('Formats', $array);
	        	$formatid = $db->lastInsertId();
	        }
	        else 
	        {
	        	$db->update('Formats', $array, 'FormatID=' . $formatid);
	        }
	        $db->commit();
	   	}
    	catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$formatid = NULL;
   		}
        return $formatid;
    }
    
	private function formatExists($formatID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(FormatID)'));
    	$select->where('FormatID=' . $formatID);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }
}

?>