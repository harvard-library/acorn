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


class Counts
{
    private $volumeCount = 0;
	private $sheetCount = 0;
	private $photoCount = 0;
	private $otherCount = 0;
	private $housingCount = 0;
	private $boxCount = 0;
	private $volumeCountDescription = NULL;
	private $sheetCountDescription = NULL;
	private $photoCountDescription = NULL;
	private $otherCountDescription = NULL;
	private $housingCountDescription = NULL;
	private $boxCountDescription = NULL;
	
    /**
     * @access public
     * @return mixed
     */
    public function getVolumeCount()
    {
    	return $this->volumeCount;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getSheetCount()
    {
    	return $this->sheetCount;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getOtherCount()
    {
    	return $this->otherCount;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getPhotoCount()
    {
    	return $this->photoCount;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getHousingCount()
    {
    	return $this->housingCount;
    }
    
    /**
     * @access public
     * @return mixed
     */
    public function getBoxCount()
    {
    	return $this->boxCount;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getOtherDescription()
    {
    	if (empty($this->otherCountDescription))
    	{
    		$this->otherCountDescription = NULL;
    	}
    	return $this->otherCountDescription;
    }
    
	/**
     * @access public
     * @return mixed
     */
    public function getVolumeDescription()
    {
    	if (empty($this->volumeCountDescription))
    	{
    		$this->volumeCountDescription = NULL;
    	}
    	return $this->volumeCountDescription;
    }
    
	/**
     * @access public
     * @return mixed
     */
    public function getSheetDescription()
    {
    	if (empty($this->sheetCountDescription))
    	{
    		$this->sheetCountDescription = NULL;
    	}
    	return $this->sheetCountDescription;
    }
    
	/**
     * @access public
     * @return mixed
     */
    public function getPhotoDescription()
    {
    	if (empty($this->photoCountDescription))
    	{
    		$this->photoCountDescription = NULL;
    	}
    	return $this->photoCountDescription;
    }
    
	/**
     * @access public
     * @return mixed
     */
    public function getHousingDescription()
    {
    	if (empty($this->housingCountDescription))
    	{
    		$this->housingCountDescription = NULL;
    	}
    	return $this->housingCountDescription;
    }
    
    /**
     * @access public
     * @return mixed
     */
    public function getBoxDescription()
    {
    	if (empty($this->boxCountDescription))
    	{
    		$this->boxCountDescription = NULL;
    	}
    	return $this->boxCountDescription;
    }

    /**
     * @access public
     * @param  volumeCount
     */
    public function setVolumeCount($volumeCount)
    {
    	$this->volumeCount = $volumeCount;
    }

    /**
     * @access public
     * @param  sheetCount
     */
    public function setSheetCount($sheetCount)
    {
    	$this->sheetCount = $sheetCount;
    }

    /**
     * @access public
     * @param  photoCount
     */
    public function setPhotoCount($photoCount)
    {
    	$this->photoCount = $photoCount;
    }

    /**
     * @access public
     * @param  housingCount
     */
    public function setHousingCount($housingCount)
    {
    	$this->housingCount = $housingCount;
    }

    /**
     * @access public
     * @param  otherCount
     */
    public function setOtherCount($otherCount)
    {
    	$this->otherCount = $otherCount;
    }
    
    /**
     * @access public
     * @param  otherCount
     */
    public function setBoxCount($boxCount)
    {
    	$this->boxCount = $boxCount;
    }

    /**
     * @access public
     * @param  String otherDescription
     */
    public function setOtherDescription($otherDescription)
    {
    	$this->otherCountDescription = $otherDescription;
    }
    
	/**
     * @access public
     * @param  String photoCountDescription
     */
    public function setPhotoDescription($photoCountDescription)
    {
    	$this->photoCountDescription = $photoCountDescription;
    }
    
	/**
     * @access public
     * @param  String volumeCountDescription
     */
    public function setVolumeDescription($volumeCountDescription)
    {
    	$this->volumeCountDescription = $volumeCountDescription;
    }
    
	/**
     * @access public
     * @param  String sheetCountDescription
     */
    public function setSheetDescription($sheetCountDescription)
    {
    	$this->sheetCountDescription = $sheetCountDescription;
    }
    
	/**
     * @access public
     * @param  String housingDescription
     */
    public function setHousingDescription($housingDescription)
    {
    	$this->housingCountDescription = $housingDescription;
    }
    
    /**
     * @access public
     * @param  String boxDescription
     */
    public function setBoxDescription($boxDescription)
    {
    	$this->boxCountDescription = $boxDescription;
    }
    
    /*
     * Determines if the instance passed in is the same as the current instance.
     * 
     * @param Counts comparerecords
     */
    public function equals(Counts $comparecounts)
    {
    	$equals = $this->getHousingCount() == $comparecounts->getHousingCount();
    	$equals = $equals && $this->getHousingDescription() == $comparecounts->getHousingDescription();
    	$equals = $equals && $this->getOtherCount() == $comparecounts->getOtherCount();
    	$equals = $equals && $this->getOtherDescription() == $comparecounts->getOtherDescription();
    	$equals = $equals && $this->getPhotoCount() == $comparecounts->getPhotoCount();
    	$equals = $equals && $this->getPhotoDescription() == $comparecounts->getPhotoDescription();
    	$equals = $equals && $this->getSheetCount() == $comparecounts->getSheetCount();
    	$equals = $equals && $this->getSheetDescription() == $comparecounts->getSheetDescription();
    	$equals = $equals && $this->getVolumeCount() == $comparecounts->getVolumeCount();
    	$equals = $equals && $this->getVolumeDescription() == $comparecounts->getVolumeDescription();
 		$equals = $equals && $this->getBoxCount() == $comparecounts->getBoxCount();
    	$equals = $equals && $this->getBoxDescription() == $comparecounts->getBoxDescription();
 		return $equals;
    }
    
	/*
     * Determines if any data exists in this count object.
     * 
     */
    public function isEmpty()
    {
    	$empty = $this->getHousingCount() == 0;
    	$empty = $empty && is_null($this->getHousingDescription());
    	$empty = $empty && $this->getOtherCount() == 0;
    	$empty = $empty && is_null($this->getOtherDescription());
    	$empty = $empty && $this->getPhotoCount() == 0;
    	$empty = $empty && is_null($this->getPhotoDescription());
    	$empty = $empty && $this->getSheetCount() == 0;
    	$empty = $empty && is_null($this->getSheetDescription());
    	$empty = $empty && $this->getVolumeCount() == 0;
    	$empty = $empty && is_null($this->getVolumeDescription());
 		$empty = $empty && $this->getBoxCount() == 0;
    	$empty = $empty && is_null($this->getBoxDescription());
 		return $empty;
    }
    
    public function toString($includezeros = FALSE)
    {
    	$values = array();
    	if ($this->getVolumeCount() > 0 || $includezeros)
    	{
    		array_push($values, $this->getStringValue($this->getVolumeCount(), 'volume'));
    	}
    	if ($this->getSheetCount() > 0 || $includezeros)
    	{
    		array_push($values, $this->getStringValue($this->getSheetCount(), 'sheet'));
    	}
    	if ($this->getPhotoCount() > 0 || $includezeros)
    	{
    		array_push($values, $this->getStringValue($this->getPhotoCount(), 'photo'));
    	}
    	if ($this->getBoxCount() > 0 || $includezeros)
    	{
    		array_push($values, $this->getStringValue($this->getBoxCount(), 'box'));
    	}
    	if ($this->getOtherCount() > 0 || $includezeros)
    	{
    		array_push($values, $this->getStringValue($this->getOtherCount(), 'other'));
    	}
    	if ($this->getHousingCount() > 0 || $includezeros)
    	{
    		array_push($values, $this->getStringValue($this->getHousingCount(), 'housing'));
    	}
    	
    	$stringvalue = implode("; ", $values);
    	return $stringvalue;
    }
    
    private function getStringValue($count, $basename)
    {
    	if ($count != 1)
    	{
    		$basename .= 's';
    	}
    	return $count . ' ' . $basename;
    }

    public function getTotalItemCount()
    {
    	return $this->getVolumeCount() + $this->getSheetCount() + $this->getPhotoCount() + $this->getBoxCount() + $this->getOtherCount();
    }
} 

?>