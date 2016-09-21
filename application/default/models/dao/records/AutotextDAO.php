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
 * AutotextDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
class AutotextDAO extends Zend_Db_Table
{
	const AUTOTEXT_ID = 'AutotextID';
	const CAPTION = 'Caption';
	const AUTOTEXT = 'Autotext';
	const AUTOTEXT_TYPE = 'AutotextType';
	const DEPENDENT_AUTOTEXT_ID = 'DependentAutotextID';
	const IS_GLOBAL = 'IsGlobal';
	
	const AUTOTEXT_TYPE_CONDITION = 'Condition';
	const AUTOTEXT_TYPE_DEPENDENCY = 'Dependency';
	const AUTOTEXT_TYPE_DESCRIPTION = 'Description';
	const AUTOTEXT_TYPE_PRESERVATION = 'Preservation';
	const AUTOTEXT_TYPE_PROPOSED_TREATMENT = 'ProposedTreatment';
	const AUTOTEXT_TYPE_TREATMENT = 'Treatment';
	
    /* The name of the table */
	protected $_name = "Autotext";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "AutotextID";

    private static $autotextDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return AutotextDAO
     */
    public static function getAutotextDAO()
	{
		if (!isset(self::$autotextDAO))
		{
			self::$autotextDAO = new AutotextDAO();
		}
		return self::$autotextDAO;
	}

	/**
     * Returns a list of autotext values
     *
     * @access public
     * @param string autotext type in ('Dependency','Treatment','Description','Condition','Preservation','ProposedTreatment')
     * @param int personid  the person who owns the text
     * @param int dependentautotextid if autotext type is 'Dependency', this should have a value.
     * @return array, NULL if no rows found  
     */
    public function getAllAutotext($autotexttype, $personid, $dependentautotextid = NULL)
    {
    	$select = $this->select();
    	$autotexttype = $this->getAdapter()->quote($autotexttype);
        $personid = $this->getAdapter()->quote($personid, 'INTEGER');
        $where = 'AutotextType = ' . $autotexttype . ' AND (PersonID = ' . $personid . ' OR PersonID IS NULL)';
    	if (!is_null($dependentautotextid))
    	{
    		$dependentautotextid = $this->getAdapter()->quote($dependentautotextid, 'INTEGER');
    		$where .= ' AND DependentAutotextID = ' . $dependentautotextid;
        }
        $select->where($where);
    	$rows = $this->getAdapter()->fetchAssoc($select);
    	return $rows;
    }
    
	/**
     * Returns a list of autotext values
     *
     * @access public
     * @param string autotext type in ('Dependency','Treatment','Description','Condition','Preservation','ProposedTreatment')
     * @param int personid  the person who owns the text
     * @param int dependentautotextid if autotext type is 'Dependency', this should have a value.
     * @return array to be used with JSON, NULL if none found
     */
    public function getAllAutotextForSelect($autotexttype, $personid, $dependentautotextid = NULL, $includeall = FALSE, $includeglobal = TRUE)
    {
    	$select = $this->select();
    	$select->from($this, array('*'));
    	$select->setIntegrityCheck(FALSE);
    	$select->joinLeft(array('DependentAutotext' => 'Autotext'), 
    			'Autotext.DependentAutotextID = DependentAutotext.AutotextID',
    			array('DependentAutotext' => 'Caption'));
    	$select->joinLeft('People', 'Autotext.PersonID = People.PersonID', array('DisplayName'));
    	$autotexttype = $this->getAdapter()->quote($autotexttype);
        $where = 'Autotext.AutotextType = ' . $autotexttype;
        
        //If we don't want to include all, then get only the current person.
		if (!$includeall)
		{
			$personid = $this->getAdapter()->quote($personid, 'INTEGER');
			if ($includeglobal)
			{
        		$where .= ' AND (Autotext.PersonID = ' . $personid . ' OR Autotext.IsGlobal = 1)';
			}
			else 
			{
				$where .= ' AND Autotext.PersonID = ' . $personid;
			}
		}
		
		if (!is_null($dependentautotextid))
    	{
    		$dependentautotextid = $this->getAdapter()->quote($dependentautotextid, 'INTEGER');
    		$where .= ' AND Autotext.DependentAutotextID = ' . $dependentautotextid;
        }
        
        $select->where($where);
    	$select->order('Autotext.Caption');
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$rows = $this->fetchAll($select);
    	$retval = NULL;
    	if (!is_null($rows))
    	{
    		$retval = $rows->toArray();
    	}
    	return $retval;
    }


	/**
     * Returns a autotext for the selected caption
     *
     * @access public
     * @param int autotextid the autotextid for the selected caption
     * @return AutoText
     */
    public function getAutotextFromID($autotextid)
    {
    	$select = $this->select();
    	$autotextid = $this->getAdapter()->quote($autotextid, 'INTEGER');

        $select->where('AutotextID=' . $autotextid);
    	Logger::log($select->__toString(), Zend_Log::DEBUG);
    	$row = $this->fetchRow($select);
    	$retval = NULL;
    	if (!is_null($row))
    	{
    		$retval = $this->buildAutotext($row->toArray());
    	}
    	return $retval;
    }
    
    private function buildAutotext(array $data)
    {
    	$autotext = new AutoText($data[self::AUTOTEXT_ID]);
    	$autotext->setAutotext($data[self::AUTOTEXT]);
    	$autotext->setCaption($data[self::CAPTION]);
    	$autotext->setDependentAutoTextID($data[self::DEPENDENT_AUTOTEXT_ID]);
    	$autotext->setOwnerID($data[PeopleDAO::PERSON_ID]);
    	$autotext->setAutoTextType($data[self::AUTOTEXT_TYPE]);
    	$autotext->setGlobal($data[self::IS_GLOBAL]);
    	return $autotext;
    }

    /**
     * @access public
     * @param  AutoText autotext
     * @return mixed
     */
    public function saveAutotext(AutoText $autotext)
    {
    	$array = array(
        	self::AUTOTEXT => $autotext->getAutotext(),
        	self::AUTOTEXT_TYPE => $autotext->getAutoTextType(),
        	self::CAPTION => $autotext->getCaption(),
        	self::DEPENDENT_AUTOTEXT_ID => $autotext->getDependentAutoTextID(),
        	PeopleDAO::PERSON_ID => $autotext->getOwnerID(),
        	self::IS_GLOBAL => $autotext->isGlobal()
        );
        $db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try {
	   		$autotextid = $autotext->getPrimaryKey();
	        if (is_null($autotextid) || !$this->autotextExists($autotextid))
	        {
	        	$db->insert('Autotext', $array);
	        	$autotextid = $db->lastInsertId();
	        }
	        else 
	        {
	        	$db->update('Autotext', $array, 'AutotextID=' . $autotextid);
	        }
	        $db->commit();
	   	}
    	catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$autotextid = NULL;
   		}
        return $autotextid;
    }
    
	private function autotextExists($autotextID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(AutotextID)'));
    	$select->where('AutotextID=' . $autotextID);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }
    
	/**
     * @access public
     * @param  autotextID
     * @return mixed
     */
    public function deleteAutotext($autotextID)
    {
    	$db = $this->getAdapter();
	   	$db->beginTransaction();
	   	try {
	   		$rowsdeleted = $db->delete('Autotext', 'AutotextID=' . $autotextID);	
	        $db->commit();
	   	}
    	catch (Exception $e)
   		{
   			Logger::log($e->getMessage(), Zend_Log::ERR);
   			$db->rollBack();
   			$rowsdeleted = NULL;
   		}
        return $rowsdeleted;
    }
} 

?>