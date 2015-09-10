<?php

class Dimensions
{
	const CENTIMETERS = 'cm';
	const INCHES = 'in';
	
	private $height = NULL;
	private $width = NULL;
	private $thickness = NULL;
	private $unit = NULL;

    /**
     * @access public
     * @return mixed
     */
    public function getHeight()
    {
    	return $this->height;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getWidth()
    {
    	return $this->width;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getThickness()
    {
    	return $this->thickness;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getUnit()
    {
    	if (empty($this->unit))
    	{
    		$this->unit = self::CENTIMETERS;
    	}
    	return $this->unit;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getReportDimensions()
    {
    	$dims = '';
    	if ($this->getHeight() > 0 || $this->getWidth() > 0 || $this->getThickness() > 0)
    	{
	    	$dims = 'H: ' . $this->getHeight() . ' ' . $this->getUnit();
	    	$dims .= '  W: ' . $this->getWidth() . ' ' . $this->getUnit();
	    	$dims .= '  T: ' . $this->getThickness() . ' ' . $this->getUnit();
	    }
    	
    	return $dims;
    }

    /**
     * @access public
     * @param  Integer height
     */
    public function setHeight($height)
    {
    	$this->height = $height;
    }

    /**
     * @access public
     * @param  Integer width
     */
    public function setWidth($width)
    {
    	$this->width = $width;
    }

    /**
     * @access public
     * @param  Integer thickness
     */
    public function setThickness($thickness)
    {
    	$this->thickness = $thickness;
    }

    /**
     * @access public
     * @param  String unit
     */
    public function setUnit($unit)
    {
    	$this->unit = $unit;
    }
    
	/*
     * Determines if the instance passed in is the same as the current instance.
     * 
     * @param Dimensions dimensions
     */
    public function equals(Dimensions $dimensions)
    {
    	$equals = $this->getHeight() == $dimensions->getHeight();
    	$equals = $equals && $this->getThickness() == $dimensions->getThickness();
    	$equals = $equals && $this->getUnit() == $dimensions->getUnit();
    	$equals = $equals && $this->getWidth() == $dimensions->getWidth();
    	return $equals;
    }

} 

?>