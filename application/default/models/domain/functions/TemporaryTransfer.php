<?php

class TemporaryTransfer extends AcornClass
{
    private $itemID = NULL;
	private $transferDate = NULL;
	private $transferFromID = NULL;
	private $transferToID = NULL;
	private $transferType = NULL;
	
	/**
     * @access public
     * @return mixed
     */
    public function getItemID()
    {
    	return $this->itemID;
    }
    
	/**
     * @access public
     * @param  int itemID
     */
    public function setItemID($itemID)
    {
    	$this->itemID = $itemID;
    }
    
    /**
     * @access public
     * @param  loginDate
     */
    public function setTransferDate($transferDate)
    {
    	$this->transferDate = $transferDate;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getTransferDate()
    {
    	return $this->transferDate;
    }
    
	/**
     * @access public
     * @param  transferType
     */
    public function setTransferType($transferType)
    {
    	$this->transferType = $transferType;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getTransferType()
    {
    	return $this->transferType;
    }


    /**
     * @access public
     * @param  transferFromID
     */
    public function setTransferFrom($transferFromID)
    {
    	$this->transferFromID = $transferFromID;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getTransferFrom()
    {
    	return $this->transferFromID;
    }
    

    /**
     * @access public
     * @param  transferToID
     */
    public function setTransferTo($transferToID)
    {
    	$this->transferToID = $transferToID;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getTransferTo()
    {
    	return $this->transferToID;
    }
    
	/*
     * Determines if the instance passed in is the same as the current instance.
     * 
     * @param TemporaryTransfer transfer
     */
    public function equals(TemporaryTransfer $transfer)
    {
    	//First compare basic fields to determine if they are equal.
    	$equal = $this->getTransferDate() == $transfer->getTransferDate();
    	$equal = $equal && $this->getTransferFrom() == $transfer->getTransferFrom();
    	$equal = $equal && $this->getTransferTo() == $transfer->getTransferTo();
    	return $equal;
    }
} 

?>