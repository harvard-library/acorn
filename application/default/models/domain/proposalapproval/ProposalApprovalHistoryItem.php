<?php

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