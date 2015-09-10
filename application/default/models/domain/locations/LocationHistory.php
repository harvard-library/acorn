<?php

class LocationHistory extends AcornClass
{
    private $itemID = NULL;
	private $dateIn = NULL;
	private $dateOut = NULL;
	private $locationID = NULL;
	private $isTemporary = NULL;
	
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
     * @param  dateIn
     */
    public function setDateIn($dateIn)
    {
    	$this->dateIn = $dateIn;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getDateIn()
    {
    	return $this->dateIn;
    }


    /**
     * @access public
     * @param  dateOut
     */
    public function setDateOut($dateOut)
    {
    	$this->dateOut = $dateOut;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getDateOut()
    {
    	return $this->dateOut;
    }
    

    /**
     * @access public
     * @param  isTemporary
     */
    public function setTemporary($isTemporary)
    {
    	$this->isTemporary = $isTemporary;
    }

    /**
     * @access public
     * @return mixed
     */
    public function isTemporary()
    {
    	return $this->isTemporary;
    }
    
	/**
     * @access public
     * @param  locationID
     */
    public function setLocationID($locationid)
    {
    	$this->locationID = $locationid;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getLocationID()
    {
    	return $this->locationID;
    }
} 

?>