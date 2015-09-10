<?php

class ProposalApproval extends AcornClass
{
	private $history = array();
	/**
	 * Array of information for the Record Status history.
	 */
	private $historyAsActivity = array();
	
	/**
     * @access public
     * @return array
     */
    public function getHistory()
    {
    	return $this->history;
    }
    
	/**
     * @access public
     * @param  array history
     */
    public function setHistory(array $history)
    {
    	$this->history = $history;
    	
    }
	
	/**
	 * @return array
	 */
	public function getHistoryAsActivity()
	{
		return $this->historyAsActivity;
	}
	
	/**
	 * @param array $historyAsActivity
	 */
	public function setHistoryAsActivity(array $historyAsActivity)
	{
		$this->historyAsActivity = $historyAsActivity;
	}

    
	/**
     * @access public
     * @param  ProposalApprovalHistoryItem conservator
     */
    public function addHistoryItem(ProposalApprovalHistoryItem $item)
    {
    	$history = $this->getHistory();
    	$history[$item->getPrimaryKey()] = $item;
    	$this->setHistory($history);
    	
    	if ($item->getActivityType() == ProposalApprovalDAO::APPROVAL_TYPE_APPROVE
    		|| $item->getActivityType() == ProposalApprovalDAO::APPROVAL_TYPE_DENY)
    	{
	    	$historyAsActivity = $this->getHistoryAsActivity();
	    	$weight = $item->getPrimaryKey() + 20;
	    	$historyAsActivity[$item->getPrimaryKey()] = array('Date' => $item->getHistoryDate(),
	    														'Activity' => $item->getActivityDetails(),
	    														'Weight' => $weight);
	    	$this->setHistoryAsActivity($historyAsActivity);
    	}
    }

    /**
     * @access public
     * @param  Integer historyItemID
     */
    public function removeHistoryItem($historyItemID)
    {
    	$history = $this->getHistory();
    	unset($history[$historyItemID]);
    	$this->setHistory($history);
    	
    	$historyAsActivity = $this->getHistoryAsActivity();
    	unset($historyAsActivity[$historyItemID]);
    	$this->setHistoryAsActivity($historyAsActivity);
    }
}

?>