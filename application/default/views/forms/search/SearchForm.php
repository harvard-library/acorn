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
 *
 * SearchForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
class SearchForm extends Zend_Form
{
	const RECORD_SEARCH = "recordsearch";
	const OSW_SEARCH = "oswsearch";
	
	private $searchType;
	
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$this->searchType = self::RECORD_SEARCH;
    	
    	$advancedsearchparameters = new Zend_Form_Element_Hidden('advancedsearchparameters');
    	$advancedsearchparameters->setName('advancedsearchparameters');
		$advancedsearchparameters->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->addElement($advancedsearchparameters);
		
    	$recordnumber = new ACORNTextField('recordnumberinput');
    	$recordnumber->setLabel('Record/OSW Number*');
    	$recordnumber->setName('recordnumberinput');
    	$recordnumber->setRequired(TRUE);
    	$recordnumber->setAttrib('class', 'number');
    	$recordnumber->setAttrib('onkeyup', 'findRecordFromEnter(event)');
    	$this->addElement($recordnumber);
    	
    	$loadsavedsearchselect = new ACORNSelect('loadsavedsearchselect');
    	$loadsavedsearchselect->setLabel('Load Saved Search');
    	$loadsavedsearchselect->setName('loadsavedsearchselect');
    	$loadsavedsearchselect->setAttrib('onchange', 'populateSearch(this.value)');
    	$loadsavedsearchselect->setAttrib('onclick', 'loadSearches()');
    	$this->addElement($loadsavedsearchselect);
    	
    	$savesearchinput = new ACORNTextField('savesearchinput');
    	$savesearchinput->setLabel('Save This Search');
    	$savesearchinput->setName('savesearchinput');
    	$savesearchinput->setRequired(TRUE);
    	$savesearchinput->setValue('(Enter Name Here)');
    	$this->addElement($savesearchinput);
    	
    	$hiddenerrormessagesinput = new Zend_Form_Element_Hidden('hiddenerrormessagesinput');
    	$hiddenerrormessagesinput->setName('hiddenerrormessagesinput');
    	$hiddenerrormessagesinput->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenerrormessagesinput);
    	
    	$hiddensavemessagesinput = new Zend_Form_Element_Hidden('hiddensavemessagesinput');
    	$hiddensavemessagesinput->setName('hiddensavemessagesinput');
    	$hiddensavemessagesinput->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddensavemessagesinput);
    	
    	$hiddenoverridesearchnameinput = new Zend_Form_Element_Hidden('hiddenoverridesearchnameinput');
    	$hiddenoverridesearchnameinput->setName('hiddenoverridesearchnameinput');
    	$hiddenoverridesearchnameinput->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenoverridesearchnameinput);
    }
    
    public function setSearchType($searchtype)
    {
    	$this->searchType = $searchtype;
    }
    
    public function isValid($data)
    {
    	$isvalid = parent::isValid($data);
    	
    	if ($isvalid && isset($data['recordnumberinput']))
    	{
	    	if ($this->searchType == self::RECORD_SEARCH)
	    	{
	    		$itemexists = ItemDAO::getItemDAO()->itemExists($data['recordnumberinput']);
	    		$isvalid = $isvalid && $itemexists;
	    		if (!$itemexists)
	    		{
	    			$this->getElement('hiddenerrormessagesinput')->addError($data['recordnumberinput'] . ' is not an existing Acorn Record.');
	    		}
	    	}
	    	else
	    	{
	    		$itemexists = OnSiteWorkDAO::getOnSiteWorkDAO()->oswExists($data['recordnumberinput']);
	    		$isvalid = $isvalid && $itemexists;
	    		if (!$itemexists)
	    		{
	    			$this->getElement('hiddenerrormessagesinput')->addError($data['recordnumberinput'] . ' is not an existing OSW Record.');
	    		}
	    	}
    	}
    	return $isvalid;
    }
    
	public function isValidPartial(array $data)
    {
    	$isvalid = parent::isValidPartial($data);
    	if ($isvalid && isset($data['recordnumberinput']))
    	{
	    	if ($this->searchType == self::RECORD_SEARCH)
	    	{
	    		$itemexists = ItemDAO::getItemDAO()->itemExists($data['recordnumberinput']);
	    		$isvalid = $isvalid && $itemexists;
	    		if (!$itemexists)
	    		{
	    			$this->getElement('hiddenerrormessagesinput')->addError($data['recordnumberinput'] . ' is not an existing Acorn Record.');
	    		}
	    	}
	    	else
	    	{
	    		$itemexists = OnSiteWorkDAO::getOnSiteWorkDAO()->oswExists($data['recordnumberinput']);
	    		$isvalid = $isvalid && $itemexists;
	    		if (!$itemexists)
	    		{
	    			$this->getElement('hiddenerrormessagesinput')->addError($data['recordnumberinput'] . ' is not an existing OSW Record.');
	    		}
	    	}
    	}
    	
    	if ($isvalid && isset($data['savesearchinput']))
    	{
    		$auth = Zend_Auth::getInstance();
    		$identity = $auth->getIdentity();
    		
	    	if ($data['savesearchinput'] == '(Enter Name Here)')
	    	{
	    		$isvalid = FALSE;
	    		$this->getElement('hiddensavemessagesinput')->addError('Please enter a search name.');
	    	}
	    	elseif(SearchDAO::getSearchDAO()->searchnameExists($data['savesearchinput'], $identity[PeopleDAO::PERSON_ID]))
	    	{
	    		$isvalid = FALSE;
	    		$this->getElement('hiddensavemessagesinput')->addError('Press "Overwrite" to update the existing file "' . $data['savesearchinput'] . '"?');
	    	}
    		elseif(SearchDAO::getSearchDAO()->searchnameExists($data['savesearchinput']))
	    	{
	    		$isvalid = FALSE;
	    		$this->getElement('hiddensavemessagesinput')->addError('This search name is already in use.  Please enter another name and save.');
	    	}
    	}
    	return $isvalid;
    }	
}
?>