<?php

class Functions
{
	private $identificationID = NULL;
    private $login = NULL;
	private $logout = NULL;
	private $proposal = NULL;
	private $report = NULL;
	private $temporarytransfer = NULL;
	private $temporarytransferreturn = NULL;

	public function __construct($identificationID = NULL)
	{
		$this->identificationID = $identificationID;
	}
	
    /**
     * @access public
     * @return Login
     */
    public function getLogin()
    {
    	if (is_null($this->login) && !is_null($this->identificationID))
    	{
    		$this->login = LoginDAO::getLoginDAO()->getLogin($this->identificationID);
    	}
    	
    	if (is_null($this->login))  
    	{
    		$this->login = new Login();
    	}
    	return $this->login;
    }

    /**
     * @access public
     * @return Logout
     */
    public function getLogout()
    {
    	if (is_null($this->logout) && !is_null($this->identificationID))
    	{
    		$this->logout = LogoutDAO::getLogoutDAO()->getLogout($this->identificationID);
    	}
    	
    	if (is_null($this->logout))  
    	{
    		$this->logout = new Logout();
    	}
    	return $this->logout;
    }

    /**
     * @access public
     * @return Proposal
     */
    public function getProposal()
    {
    	if (is_null($this->proposal) && !is_null($this->identificationID))
    	{
    		$this->proposal = ProposalDAO::getProposalDAO()->getProposal($this->identificationID);
    	}
    	
    	if (is_null($this->proposal))  
    	{
    		$this->proposal = new Proposal();
    	}
    	return $this->proposal;
    }

    /**
     * @access public
     * @return Report
     */
    public function getReport()
    {
    	if (is_null($this->report) && !is_null($this->identificationID))
    	{
    		$this->report = ReportDAO::getReportDAO()->getReport($this->identificationID);
    	}
    	
    	if (is_null($this->report))  
    	{
    		$this->report = new Report();
    	}
    	return $this->report;
    }

	/**
     * @access public
     * @return TemporaryTransfer
     */
    public function getTemporaryTransfer()
    {
    	if (is_null($this->temporarytransfer) && !is_null($this->identificationID))
    	{
    		$this->temporarytransfer = TemporaryTransfersDAO::getTemporaryTransfersDAO()->getTemporaryTransfer($this->identificationID);
    	}
    	
    	if (is_null($this->temporarytransfer))  
    	{
    		$this->temporarytransfer = new TemporaryTransfer();
    	}
    	return $this->temporarytransfer;
    }
    
	/**
     * @access public
     * @return TemporaryTransfer
     */
    public function getTemporaryTransferReturn()
    {
    	if (is_null($this->temporarytransferreturn) && !is_null($this->identificationID))
    	{
    		$this->temporarytransferreturn = TemporaryTransfersDAO::getTemporaryTransfersDAO()->getTemporaryTransferReturn($this->identificationID);
    	}
    	
    	if (is_null($this->temporarytransferreturn)) 
    	{
    		$this->temporarytransferreturn = new TemporaryTransfer();
    	}
    	return $this->temporarytransferreturn;
    }
    /**
     * @access public
     * @param  Login login
     */
    public function setLogin(Login $login)
    {
    	$this->login = $login;
    }

    /**
     * @access public
     * @param  Logout logout
     */
    public function setLogout(Logout $logout)
    {
    	$this->logout = $logout;
    }

    /**
     * @access public
     * @param  Proposal proposal
     */
    public function setProposal(Proposal $proposal)
    {
    	$this->proposal = $proposal;
    }

    /**
     * @access public
     * @param  Report report
     */
    public function setReport(Report $report)
    {
    	$this->report = $report;
    }

	/**
     * @access public
     * @param  TemporaryTransfer tempTransfer
     */
    public function setTemporaryTransfer(TemporaryTransfer $tempTransfer)
    {
    	$this->temporarytransfer = $tempTransfer;
    }
    
	/**
     * @access public
     * @param  TemporaryTransfer tempTransfer
     */
    public function setTemporaryTransferReturn(TemporaryTransfer $temporarytransferreturn)
    {
    	$this->temporarytransferreturn = $temporarytransferreturn;
    }
    
	/*
     * Determines if the instance passed in is the same as the current instance.
     * 
     * @param Functions functions
     */
    public function equals(Functions $functions)
    {
    	//Login
    	$equals = $this->getLogin()->equals($functions->getLogin());
    	//Proposal
    	if ($equals)
    	{
    		$equals = $this->getProposal()->equals($functions->getProposal());
    	}
 		//Report
    	if ($equals)
    	{
    		$equals = $this->getReport()->equals($functions->getReport());
    	}
 		//Logout
    	if ($equals)
    	{
    		$equals = $this->getLogout()->equals($functions->getLogout());
    	}
 		//Temp Transfer
    	if ($equals)
    	{
    		$equals = $this->getTemporaryTransfer()->equals($functions->getTemporaryTransfer());
    	}
 		//Temp Transfer Return
    	if ($equals)
    	{
    		$equals = $this->getTemporaryTransferReturn()->equals($functions->getTemporaryTransferReturn());
    	}
 		return $equals;
    }
}

?>