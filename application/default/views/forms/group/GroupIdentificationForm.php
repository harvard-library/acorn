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
 * GroupIdentificationForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class GroupIdentificationForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$this->populateIdentifyingElements();
    	$this->populatePhysicalDescriptionElements();
    	$this->populateRepositoryElements();
    	$this->populateHiddenElements();
    	
    	$noncollectioncheckbox = new Zend_Form_Element_Checkbox('noncollectioncheckbox');
        $noncollectioncheckbox->setLabel('Non-collection material');
        $noncollectioncheckbox->setAttribs(array('class' => 'checkboxinput'));
        $noncollectioncheckbox->setDecorators(Decorators::$CHECKBOX_DECORATORS);
    	$this->addElement($noncollectioncheckbox);
    	
    	$commentstextarea = new ACORNTextArea('commentstextarea');
		$commentstextarea->setName('commentstextarea');
		$this->addElement($commentstextarea);
		
		$unlockuntildateinput = new ACORNTextField('unlockuntildateinput');
		$unlockuntildateinput->setLabel('Unlock Until');
		$unlockuntildateinput->setAttrib('onfocus', 'showCalendarChooser(\'unlockuntildateinput\')');
		$unlockuntildateinput->setAttrib('readonly', 'readonly');
		$unlockuntildateinput->setName('unlockuntildateinput');
		$this->addElement($unlockuntildateinput);
		
		$numbernewrecordsinput = new NumberTextField('numbernewrecordsinput');
		$numbernewrecordsinput->setName('numbernewrecordsinput');
		$numbernewrecordsinput->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$numbernewrecordsinput->addValidator(new Zend_Validate_GreaterThan(0));
		$numbernewrecordsinput->setLabel('Number New Records*');
        $numbernewrecordsinput->setRequired(TRUE);
    	$this->addElement($numbernewrecordsinput);
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'group/groupidentification.phtml'),
   			//Renders the form.
   			'Form'
   			)
		));
    }
    
 	/*
     * Hidden elements to attach error messages to if they are necessary.
     */
    private function populateHiddenElements()
    {
    	$callnumberhidden = new Zend_Form_Element_Hidden('callnumberhidden');
        $callnumberhidden->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($callnumberhidden);
    }
    
    /*
     * Identifying information
     */
    private function populateIdentifyingElements()
    {
    	$projectselectform = new ProjectSelectForm(array(ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$projectselectform->setName('projectselectform');
    	$this->addSubForm($projectselectform, 'projectselectform');
    	
    	$groupinput = new ACORNTextField('groupinput');
    	$groupinput->setName('groupinput');
    	$groupinput->setLabel('Group*');
    	$groupinput->setRequired(TRUE);
    	$this->addElement($groupinput);
    	
    	$coordinatorselectform = new PersonSelectForm(array(PersonSelectForm::PERSON_SELECT_NAME => 'coordinatorselect', ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$coordinatorselectform->setRoleTypes(array('Staff'));
    	$coordinatorselectform->setLabel('Coordinator*');
    	$coordinatorselectform->setName('coordinatorselectform');
    	$coordinatorselectform->setRequired(TRUE);
    	$this->addSubForm($coordinatorselectform, 'coordinatorselectform');
    	
    	$curatorselectform = new PersonSelectForm(array(PersonSelectForm::PERSON_SELECT_NAME => 'curatorselect', ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$curatorselectform->setRoleTypes(array('Curator'));
    	$curatorselectform->setLabel('Curator*');
    	$curatorselectform->setName('curatorselectform');
    	$curatorselectform->setRequired(TRUE);
    	$this->addSubForm($curatorselectform, 'curatorselectform');
    	
    	$approvingcuratorselectform = new PersonSelectForm(array(PersonSelectForm::PERSON_SELECT_NAME => 'approvingcuratorselect', ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$approvingcuratorselectform->setRoleTypes(array('Curator'));
    	$approvingcuratorselectform->setLabel('Approving Curator*');
    	$approvingcuratorselectform->setName('approvingcuratorselectform');
    	$approvingcuratorselectform->setRequired(TRUE);
    	$this->addSubForm($approvingcuratorselectform, 'approvingcuratorselectform');
    	
    	$purposeselectform = new PurposeSelectForm(array(ACORNSelect::INCLUDE_BLANK => TRUE));
    	$purposeselectform->setName('purposeselectform');
    	$purposeselectform->setRequired(TRUE);
    	$this->addSubForm($purposeselectform, 'purposeselectform');
    	
    	$storageinput = new ACORNTextField('storageinput');
		$storageinput->setLabel('Storage');
		$storageinput->setName('storageinput');
		$this->addElement($storageinput);
    	
    	$repositoryselectform = new RepositorySelectForm(array(ACORNSelect::INCLUDE_BLANK => TRUE));
    	$repositoryselectform->setName('repositoryselectform');
    	$repositoryselectform->setRequired(TRUE);
    	$this->addSubForm($repositoryselectform, 'repositoryselectform');
    	
    	$chargetoselectform = new ChargeToSelectForm();
    	$chargetoselectform->setName('chargetoselectform');
    	$chargetoselectform->setRequired(TRUE);
    	$this->addSubForm($chargetoselectform, 'chargetoselectform');
    }
    
	/*
     * Physical content information
     */
    private function populatePhysicalDescriptionElements()
    {
    	$callnumberbaseinput = new ACORNTextField('callnumberbaseinput');
		$callnumberbaseinput->setLabel('Call Number Base*');
		$callnumberbaseinput->setName('callnumberbaseinput');
		$callnumberbaseinput->setRequired(TRUE);
		$this->addElement($callnumberbaseinput);
		
		$callnumberfrominput = new NumberTextField('callnumberfrominput');
		$callnumberfrominput->setLabel('Call Number Range');
		$callnumberfrominput->setName('callnumberfrominput');
		$callnumberfrominput->addValidator(new Zend_Validate_GreaterThan(0));
		$callnumberfrominput->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$this->addElement($callnumberfrominput);
		
		$callnumbertoinput = new NumberTextField('callnumbertoinput');
		$callnumbertoinput->setName('callnumbertoinput');
		$callnumbertoinput->setDecorators(Decorators::$ELEMENT_DECORATORS);
		$callnumbertoinput->addValidator(new Zend_Validate_GreaterThan(0));
		$this->addElement($callnumbertoinput);
		
    	$collectionnameinput = new ACORNTextField('collectionnameinput');
		$collectionnameinput->setLabel('Coll Name/Other ID');
		$collectionnameinput->setName('collectionnameinput');
		$this->addElement($collectionnameinput);
		
		$authorinput = new ACORNTextField('authorinput');
		$authorinput->setLabel('Author/Artist*');
		$authorinput->setName('authorinput');
		$authorinput->setRequired(TRUE);
    	$this->addElement($authorinput);
		
		$titleinput = new ACORNTextField('titleinput');
		$titleinput->setLabel('Title*');
		$titleinput->setName('titleinput');
		$titleinput->setRequired(TRUE);
    	$this->addElement($titleinput);
		
		$dateofobjectinput = new ACORNTextField('dateofobjectinput');
		$dateofobjectinput->setLabel('Date of Object*');
		$dateofobjectinput->setName('dateofobjectinput');
		$dateofobjectinput->setRequired(TRUE);
    	$this->addElement($dateofobjectinput);
		
		$formatselectform = new FormatSelectForm(array(ACORNSelect::INCLUDE_INACTIVE => TRUE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$formatselectform->setName('formatselectform');
    	$formatselectform->setRequired(TRUE);
    	$this->addSubForm($formatselectform, 'formatselectform');
    }
    
	/*
     * Repository information
     */
    private function populateRepositoryElements()
    {
    	$expecteddateofreturninput = new DateTextField('expecteddateofreturninput');
    	$expecteddateofreturninput->setLabel('Expected Date of Return');
		$expecteddateofreturninput->setName('expecteddateofreturninput');
		$expecteddateofreturninput->setAttrib('onfocus', 'showExpectedReturnDateCalendarChooser(\'expecteddateofreturninput\')');
		$expecteddateofreturninput->setAttrib('readonly', 'readonly');
		$this->addElement($expecteddateofreturninput);
    	
    	$insuranceinput = new ACORNTextField('insuranceinput');
    	$insuranceinput->setLabel('Insurance Value');
		$insuranceinput->setName('insuranceinput');
		$insuranceinput->setAttrib('class', 'number');
		$insuranceinput->addFilter(new CurrencyFilter());
		$insuranceinput->addValidator(new CurrencyValidator());
		$this->addElement($insuranceinput);
		
		$repositorymemotextarea = new ACORNTextArea('repositorymemotextarea');
		$repositorymemotextarea->setLabel('Notes');
		$repositorymemotextarea->setName('repositorymemotextarea');
		$this->addElement($repositorymemotextarea);
    }
    
	public function isValid($values)
 	{
 		$isvalid = parent::isValid($values);
 		
 		$item = GroupNamespace::getCurrentGroup();
 		return $isvalid;
 	}
}
?>