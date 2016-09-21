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

class AutoText extends AcornClass 
{
    private $caption = NULL;
	private $autoText = NULL;
	private $ownerID = NULL;
	private $subAutoText = array();
	private $autoTextType = NULL;
	private $dependentAutoTextID = NULL;
	private $isGlobal = FALSE;
	
    /**
     * @access public
     * @return mixed
     */
    public function getCaption()
    {
    	return $this->caption;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getAutoText()
    {
    	return $this->autoText;
    }

    /**
     * @access public
     * @return mixed
     */
    public function getOwnerID()
    {
    	return $this->ownerID;
    }

    /**
     * @access public
     * @return array
     */
    public function getSubText()
    {
    	return $this->subAutoText;
    }

    /**
     * @access public
     * @param  String caption
     */
    public function setCaption($caption)
    {
    	$this->caption = $caption;
    }

    /**
     * @access public
     * @param  String autoText
     */
    public function setAutoText($autoText)
    {
    	$this->autoText = $autoText;
    }

    /**
     * @access public
     * @param  int ownerID
     */
    public function setOwnerID($ownerID)
    {
    	$this->ownerID = $ownerID;
    }
    
	/**
     * @access public
     * @return  boolean global
     */
    public function isGlobal()
    {
    	return $this->isGlobal;
    }
    
	/**
     * @access public
     * @param  boolean global
     */
    public function setGlobal($isGlobal)
    {
    	$this->isGlobal = $isGlobal;
    }

    /**
     * @access public
     * @param  array subText
     */
    public function setSubText(array $subText)
    {
    	$this->subAutoText = $subText;
    }

    /**
     * @access public
     * @param  AutoText subText
     */
    public function addSubText(AutoText $subText)
    {
    	$this->subAutoText[$subText->getPrimaryKey()] = $subText;
    }

    /**
     * @access public
     * @param  Integer subTextID
     */
    public function removeSubText($subTextID)
    {
    	unset($this->subAutoText[$subTextID]);
    }

    /**
     * @access public
     * @return mixed
     */
    public function getAutoTextType()
    {
    	return $this->autoTextType;
    }

    /**
     * @access public
     * @param  String autoTextType
     */
    public function setAutoTextType($autoTextType)
    {
    	$this->autoTextType = $autoTextType;
    }

	/**
     * @access public
     * @return mixed
     */
    public function getDependentAutoTextID()
    {
    	return $this->dependentAutoTextID;
    }

    /**
     * @access public
     * @param  Int dependentAutoTextID
     */
    public function setDependentAutoTextID($dependentAutoTextID)
    {
    	$this->dependentAutoTextID = $dependentAutoTextID;
    }
    
	public function updateField($columnname, $newdata)
    {
    	switch ($columnname)
    	{
    		case AutotextDAO::AUTOTEXT:
    			$this->setAutoText($newdata);
    			break;
    		case AutotextDAO::AUTOTEXT_TYPE:
    			$this->setAutoTextType($newdata);
    			break;
    		case AutotextDAO::CAPTION:
    			$this->setCaption($newdata);
    			break;
    		case AutotextDAO::DEPENDENT_AUTOTEXT_ID:
    			$this->setDependentAutoTextID($newdata);
    			break;
    		case PeopleDAO::PERSON_ID:
    			$this->setOwnerID($newdata);
    			break;
    		case AutotextDAO::IS_GLOBAL:
    			$this->setGlobal($newdata);
    			break;
    	}
    }
} 

?>