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

class ProposalApprovalHistoryItem extends AcornClass
{
    private $historyDate = NULL;
	private $activityType = NULL;
	private $details = NULL;
	private $author = NULL;

	/**
	 * @return mixed
	 */
	public function getHistoryDate()
	{
		return $this->historyDate;
	}
	
	/**
	 * @return mixed
	 */
	public function getActivityType()
	{
		return $this->activityType;
	}
	
	/**
	 * @return mixed
	 */
	public function getDetails()
	{
		return $this->details;
	}
	
	/**
	 * @return Person
	 */
	public function getAuthor()
	{
		return $this->author;
	}
	
	/**
	 * @param mixed $activityType
	 */
	public function setActivityType($activityType)
	{
		$this->activityType = $activityType;
	}
	
	/**
	 * @param Person $author
	 */
	public function setAuthor(Person $author)
	{
		$this->author = $author;
	}
	
	/**
	 * @param mixed $details
	 */
	public function setDetails($details)
	{
		$this->details = $details;
	}
	
	/**
	 * @param mixed $historyDate
	 */
	public function setHistoryDate($historyDate)
	{
		$this->historyDate = $historyDate;
	}

	/**
	 * Returns a sentance for the Record Status page.
	 */
	public function getActivityDetails()
	{
		if ($this->getActivityType() == "Comment Added")
		{
			$activity = "Comment by " . $this->getAuthor()->getDisplayName() . ": " . $this->getDetails();
		}
		elseif ($this->getActivityType() == "Approved" || $this->getActivityType() == "Denied")
		{
			$activity = $this->getActivityType() . " by " . $this->getAuthor()->getDisplayName();
			if (!empty($this->details))
			{ 
				$activity .= ": " . $this->getDetails();
			}
		}
		else 
		{
			$activity = $this->getDetails() . " by " . $this->getAuthor()->getDisplayName();
		}
		return $activity;
	}
}

?>