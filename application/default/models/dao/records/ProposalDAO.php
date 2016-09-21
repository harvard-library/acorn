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
 * ProposalDAO
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
class ProposalDAO extends Zend_Db_Table
{
	const IDENTIFICATION_ID = 'IdentificationID';
	const PROPOSAL_ID = 'ProposalID';
	const PROPOSAL_DATE = 'ProposalDate';
	const CONDITION = 'Condition';
	const DESCRIPTION = 'Description';
	const EXAM_DATE = 'ExamDate';
	const EXAM_LOCATION = 'ExamLocationID';
	const MIN_HOURS = 'MinimumProposedHours';
	const MAX_HOURS = 'MaximumProposedHours';
	const TREATMENT = 'Treatment';
	const HEIGHT = 'Height';
	const WIDTH = 'Width';
	const THICKNESS = 'Thickness';
	const DIMENSION_UNIT = 'DimensionUnit';
	
	/* The name of the table */
	protected $_name = "ItemProposal";
	/* The table's primary key which can be represented as an array for > 1 PK */
    protected $_primary = "ProposalID";

    private static $proposalDAO;
    
	/**
     * Returns the static instance of this dao
     *
     * @access public
     * @return ProposalDAO
     */
    public static function getProposalDAO()
	{
		if (!isset(self::$proposalDAO))
		{
			self::$proposalDAO = new ProposalDAO();
		}
		return self::$proposalDAO;
	}
	
	
    /**
     * Returns the item proposal with the given id.
     *
     * @access public
     * @param  Integer identificationID
     * @return Proposal
     */
    public function getProposal($identificationID)
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
	    		$item = $this->buildItemProposal($row->toArray());
	    	}
    	}
    	return $item;
    }
    
    /*
     * @access private
     * @param  array values from the database
     * @return Proposal
     */
    private function buildItemProposal(array $values)
    {
    	$proposal = new Proposal($values[self::PROPOSAL_ID]);
    	$proposal->setIdentificationID($values[self::IDENTIFICATION_ID]);
    	$proposal->setProposalDate($values[self::PROPOSAL_DATE]);
    	$proposal->setCondition($values[self::CONDITION]);
    	$proposal->setDescription($values[self::DESCRIPTION]);
    	$proposal->setExamDate($values[self::EXAM_DATE]);
    	$proposal->setExamLocation($values[self::EXAM_LOCATION]);
    	$proposal->setMinHours($values[self::MIN_HOURS]);
    	$proposal->setMaxHours($values[self::MAX_HOURS]);
    	$proposal->setTreatment($values[self::TREATMENT]);
    	$proposal->setDimensions($this->buildDimensions($values));
    	return $proposal;
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
     * Returns the people that wrote the proposal.
     *
     * @access public
     * @param  Integer the proposalID
     * @return array of Person objects
     */
    public function getProposedBy($proposalID)
    {
    	$select = $this->select();
    	$select->setIntegrityCheck(FALSE);
    	$select->from('ProposedBy', array());
    	$select->joinInner('People', 'ProposedBy.PersonID = People.PersonID', array('*'));
    	$proposalID = $this->getAdapter()->quote($proposalID, 'INTEGER');
    	$select->where('ProposalID=' . $proposalID);
    	Logger::log($select->__toString());
    	$rows = $this->fetchAll($select);
    	$personarray = array();
    	if (!is_null($rows))
    	{
    		$rowarray = $rows->toArray();
    		foreach ($rowarray as $row)
    		{
    			$person = PeopleDAO::buildPerson($row);
    			$personarray[$row[PeopleDAO::PERSON_ID]] = $person;
    		}
    	}
    	return $personarray;
    }
    
	/**
     * Saves the Item Proposal information
     * @access public
     * @param  Item item
     */
    public function saveItemProposal(Proposal $proposal, Proposal $oldProposal = NULL, Item $item, $db = NULL)
    {
    	$proposalid = $proposal->getPrimaryKey();
        //Build the array to save
        $proposalarray = $this->buildProposalArray($proposal);
        
        if (is_null($db))
        {
	        $db = $this->getAdapter();
	   		$db->beginTransaction();
	   		try {
		   		//If the item isn't in the database,
		        //insert it
		        if (is_null($proposalid) || !$this->proposalExists($proposalid))
		        {
		        	$proposalarray[self::IDENTIFICATION_ID] = $proposal->getIdentificationID();
		        	$db->insert('ItemProposal', $proposalarray);
		        	$proposedby = $proposal->getProposedBy();
		        	$proposalid = $db->lastInsertId();
		        }
		        //otherwise, update it
		        else 
		        {
		        	$db->update('ItemProposal', $proposalarray, 'ProposalID=' . $proposalid);

		        	$proposedby = $proposal->getProposedByDifference($oldProposal->getProposedBy());
		        	$removalproposedby = $oldProposal->getProposedByDifference($proposal->getProposedBy());
		        	$this->removeProposedBy($db, $proposalid, $removalproposedby);
		        	 
		        }    
	
		        //Also update the comments
		        ItemDAO::getItemDAO()->updateComments($item, $db);
		        
		        $this->insertProposedBy($db, $proposalid, $proposedby);
		        
		        $db->commit();
	   		}
	   		catch (Exception $e)
	   		{
	   			Logger::log($e->getMessage(), Zend_Log::ERR);
	   			$db->rollBack();
	   			$proposalid = NULL;
	   		}
        }
        else 
        {
        	//If the item isn't in the database,
	        //insert it
	        if (is_null($proposalid) || !$this->proposalExists($proposalid))
	        {
	        	$proposalarray[self::IDENTIFICATION_ID] = $proposal->getIdentificationID();
	        	$db->insert('ItemProposal', $proposalarray);
	        	$proposedby = $proposal->getProposedBy();
		        $proposalid = $db->lastInsertId();
	        }
	        //otherwise, update it
	        else 
	        {
	        	$db->update('ItemProposal', $proposalarray, 'ProposalID=' . $proposalid);
	        	$proposedby = $proposal->getProposedByDifference($oldProposal->getProposedBy());
	        	$removalproposedby = $oldProposal->getProposedByDifference($proposal->getProposedBy());
	        	$this->removeProposedBy($db, $proposalid, $removalproposedby);
	        	Logger::log("ADD:");
	        	Logger::log($proposedby);
	        	Logger::log("REMOVE:");
	        	Logger::log($removalproposedby);
	        	 
	        	 
	        }    
	         
	        //Also update the comments
	        ItemDAO::getItemDAO()->updateComments($item, $db);
	        
	        $this->insertProposedBy($db, $proposalid, $proposedby);
	        
        }
   		return $proposalid; 
    }

    /*
     * Inserts the ids into the ProposedBy table
    */
    private function insertProposedBy($db, $proposalid, array $proposedby)
    {
    	foreach ($proposedby as $person)
    	{
    		$proposedbyarray = array(
    				self::PROPOSAL_ID => $proposalid,
    				PeopleDAO::PERSON_ID => $person->getPrimaryKey()
    		);
    		$db->insert('ProposedBy', $proposedbyarray);
    	}
    }
    
    /*
     * Removes the ids from the ProposedBy table
    */
    private function removeProposedBy($db, $proposalid, array $proposedby)
    {
    	foreach ($proposedby as $person)
    	{
    		$db->delete('ProposedBy', 'ProposalID=' . $proposalid . ' AND PersonID=' . $person->getPrimaryKey());
    	}
    }
    
	/*
     * Data to be loaded into the Item Proposal table
     */
    private function buildProposalArray(Proposal $proposal)
    {
    	$zendproposaldate = new Zend_Date($proposal->getProposalDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
    	$proposaldate = $zendproposaldate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		$examdate = $proposal->getExamDate();
		if (!empty($examdate))
		{
	    	$zendexamdate = new Zend_Date($proposal->getExamDate(), ACORNConstants::$ZEND_DATE_FORMAT);		
	    	$examdate = $zendexamdate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);	
		}
		$array = array(
    		self::PROPOSAL_DATE => $proposaldate,
    		self::CONDITION => $proposal->getCondition(),
    		self::DESCRIPTION => $proposal->getDescription(),
    		self::DIMENSION_UNIT => $proposal->getDimensions()->getUnit(),
    		self::EXAM_DATE => $examdate,
    		self::EXAM_LOCATION => $proposal->getExamLocation(),
    		self::HEIGHT => $proposal->getDimensions()->getHeight(),
    		self::MAX_HOURS => $proposal->getMaxHours(),
    		self::MIN_HOURS => $proposal->getMinHours(),
    		self::THICKNESS => $proposal->getDimensions()->getThickness(),
    		self::TREATMENT => $proposal->getTreatment(),
    		self::WIDTH => $proposal->getDimensions()->getWidth()
    	);
    	return $array;
    }
    
	private function proposalExists($proposalID)
    {
    	$select = $this->select();
    	$select->from($this, array('Count' => 'Count(ProposalID)'));
    	$select->where('ProposalID=' . $proposalID);
    	$row = $this->fetchRow($select);
    	return $row->Count == 1;
    }
    
	/*
     * Creates multiple proposals based on the model item
     * 
     */
    public function updateMultipleItems(array $items, array $oldproposals = NULL)
    {
    	$db = $this->getAdapter();
   		$db->beginTransaction();
   		$itemsupdated = 0;
   		try {
   			foreach ($items as $item)
    		{
    			$oldproposal = $oldproposals[$item->getPrimaryKey()];
    			Logger::log("NEW:");
    			Logger::log($item->getFunctions()->getProposal()->getProposedBy());
    			Logger::log("OLD:");
    			Logger::log($oldproposal->getProposedBy());
    			$proposalid = $this->saveItemProposal($item->getFunctions()->getProposal(), $oldproposal, $item, $db);
    			if (!is_null($proposalid))
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