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
 */

class Proposal extends AcornClass
{
    private $identificationID = NULL;
	private $proposalBy = NULL;
	private $proposalDate = NULL;
	private $description = NULL;
	private $condition = NULL;
	private $treatment = NULL;
	private $minHours = NULL;
	private $maxHours = NULL;
	private $examDate = NULL;
	private $examLocation = NULL;
	private $dimensions = NULL;
	private $proposalApproval = NULL;

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
    public function getCondition()
    {
    	return $this->condition;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getDescription()
    {
    	return $this->description;
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
    public function setDimensions($dimensions)
    {
    	$this->dimensions = $dimensions;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getExamDate()
    {
    	return $this->examDate;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getExamLocation()
    {
    	return $this->examLocation;
    }

    /**
     * @access public
     * @param examLocation
     */
    public function setExamLocation($examLocation)
    {
    	$this->examLocation = $examLocation;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getMaxHours()
    {
    	return $this->maxHours;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getMinHours()
    {
    	return $this->minHours;
    }
    
    public function getHoursAsString()
    {
    	if ($this->getMinHours() != $this->getMaxHours())
    	{
    		return $this->getMinHours() . '-' . $this->getMaxHours();
    	}
    	return $this->getMinHours();
    }

   
    /**
     * @access public
     * @return mixed
     */
    public function getProposalDate()
    {
    	return $this->proposalDate;
    }

    /**
     * @access public
     * @param  Integer maxHours
     */
    public function setMaxHours($maxHours)
    {
    	$this->maxHours = $maxHours;
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
     * @param  Integer minHours
     */
    public function setMinHours($minHours)
    {
    	$this->minHours = $minHours;
    }

    /**
     * @access public
     * @param  String condition
     */
    public function setCondition($condition)
    {
    	$this->condition = $condition;
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
     * @param  String examDate
     */
    public function setExamDate($examDate)
    {
    	$this->examDate = $examDate;
   	}

    
    /**
     * @access public
     * @param  proposalDate
     */
    public function setProposalDate($proposalDate)
    {
    	$this->proposalDate = $proposalDate;
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
	 * @return ProposalApproval
	 */
	public function getProposalApproval()
	{
		if (is_null($this->proposalApproval) && !empty($this->identificationID))
		{
			$itemID = ItemDAO::getItemDAO()->getItemID($this->identificationID);
			if (!is_null($itemID))
			{
				$this->proposalApproval = ProposalApprovalDAO::getProposalApprovalDAO()->getProposalApproval($itemID);
			}
		}
		if (is_null($this->proposalApproval))
		{
			$this->proposalApproval = new ProposalApproval();
		}
		return $this->proposalApproval;
	}
	
	/**
	 * @param ProposalApproval $proposalApproval
	 */
	public function setProposalApproval(ProposalApproval $proposalApproval)
	{
		$this->proposalApproval = $proposalApproval;
	}
	
	/**
	 * @access public
	 * @return array
	 */
	public function getProposedBy()
	{
		if (is_null($this->proposalBy) && !is_null($this->getPrimaryKey()))
		{
			$this->proposalBy = ProposalDAO::getProposalDAO()->getProposedBy($this->getPrimaryKey());
		}
		elseif (is_null($this->proposalBy))
		{
			$this->proposalBy = array();
		}
		return $this->proposalBy;
	}
	
	/**
	 * Returns the proposed by array as a string delimited by commas.
	 */
	public function getProposedByString()
	{
		$proposedbyarray = $this->getProposedBy();
		return implode(', ',$proposedbyarray);
	}
	
	/**
	 * @access public
	 * @param  array - list of people writing the proposal
	 */
	public function setProposedBy($proposalBy)
	{
		$this->proposalBy = $proposalBy;
	}
	
	/**
	 * @access public
	 * @param  Person
	 */
	public function addProposedBy(Person $proposalBy)
	{
		$proposalByList = $this->getProposedBy();
		$proposalByList[$proposalBy->getPrimaryKey()] = $proposalBy;
		$this->setProposedBy($proposalByList);
	}
	
	/**
	 * @access public
	 * @param  Integer personID
	 */
	public function removeProposedBy($personID)
	{
		$proposalByList = $this->getProposedBy();
		unset($proposalByList[$personID]);
		$this->setProposedBy($proposalByList);
	}
	
	/*
	 * Returns the array of proposed by that are in this proposal's proposed by
	* and are not in the array of compare proposed by
	* @param array - proposed by to compare
	* @return array - proposed by not in given array
	*/
	public function getProposedByDifference(array $compareproposedby)
	{
		return array_diff($this->getProposedBy(), $compareproposedby);
	}

    
	/*
     * Determines if the instance passed in is the same as the current instance.
     * 
     * @param Proposal proposal
     */
    public function equals(Proposal $proposal)
    {
    	//First compare basic fields to determine if they are equal.
    	$equal = $this->getCondition() == $proposal->getCondition();
    	$equal = $equal && $this->getDescription() == $proposal->getDescription();
    	$equal = $equal && $this->getExamDate() == $proposal->getExamDate();
    	$equal = $equal && $this->getExamLocation() == $proposal->getExamLocation();
    	$equal = $equal && $this->getMaxHours() == $proposal->getMaxHours();
    	$equal = $equal && $this->getMinHours() == $proposal->getMinHours();
    	$equal = $equal && $this->getProposalDate() == $proposal->getProposalDate();
    	$equal = $equal && $this->getTreatment() == $proposal->getTreatment();
    	//Proposed By
    	if ($equal)
    	{
    		$equal = $equal && $this->arraysDifferent($this->getProposedBy(), $proposal->getProposedBy());
    	}
    	//Dimensions
    	if ($equal)
    	{
    		$dimensions = $this->getDimensions();
    		$equal = $equal && $dimensions->equals($proposal->getDimensions());
    	}
    	return $equal;
    }
}

?>