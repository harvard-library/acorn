<?php
/**
 * OswIdentificationForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class OswIdentificationForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hiddenoswid = new Zend_Form_Element_Hidden('hiddenoswid');
    	$hiddenoswid->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenoswid);
    	
    	$this->populateIdentifyingElements();
    	$this->populateWorkInfoElements();
    	$this->populateHiddenElements();
    	
    	$commentstextarea = new ACORNTextArea('commentstextarea');
    	$commentstextarea->setLabel('Comments');
		$commentstextarea->setName('commentstextarea');
		$this->addElement($commentstextarea);
		
		$workdescriptiontextarea = new ACORNTextArea('workdescriptiontextarea');
		$workdescriptiontextarea->setName('workdescriptiontextarea');
		$workdescriptiontextarea->setLabel('Work Description');
		$workdescriptiontextarea->setDecorators(array(
	        'ViewHelper',
	    	//Error to be displayed if the element's
			//validator
	        'Errors',
			//Put the labels for the elements before
			//the elements themselves.
	    	array('Label', array('placement' => 'prepend', 'class' => 'oswworkdescriptionlabel')))
	    );
		$this->addElement($workdescriptiontextarea);
    	
		$proposedhours = new ACORNTextField('proposedhours');
    	$proposedhours->setLabel('Proposed Hours');
    	$proposedhours->setName('proposedhours');
    	$proposedhours->setAttrib('class', 'number');
    	$proposedhours->addValidator(new NumberValidator());
    	
    	$proposedhours->setDecorators(array(
	        'ViewHelper',
	    	//Error to be displayed if the element's
			//validator
	        'Errors',
			//Put the labels for the elements before
			//the elements themselves.
	    	array('Label', array('placement' => 'prepend', 'class' => 'oswhourslabel')))
	    );
	    $this->addElement($proposedhours);
    	
    	$actualhours = new ACORNTextField('actualhours');
    	$actualhours->setLabel('Actual Hours');
    	$actualhours->setName('actualhours');
    	$actualhours->setAttrib('class', 'number');
    	$actualhours->setAttrib('readonly', 'readonly');
    	$actualhours->addValidator(new NumberValidator());
    	$actualhours->setDecorators(array(
	        'ViewHelper',
	    	//Error to be displayed if the element's
			//validator
	        'Errors',
			//Put the labels for the elements before
			//the elements themselves.
	    	array('Label', array('placement' => 'prepend', 'class' => 'oswhourslabel')))
	    );
	    $this->addElement($actualhours);
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'record/oswidentification.phtml'),
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
    	$workdonebyhidden = new Zend_Form_Element_Hidden('workdonebyhidden');
        $workdonebyhidden->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($workdonebyhidden);
    	
    	$worktypehidden = new Zend_Form_Element_Hidden('worktypehidden');
        $worktypehidden->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($worktypehidden);
    	
    	$callnumbererrorhidden = new Zend_Form_Element_Hidden('callnumbererrorhidden');
    	$callnumbererrorhidden->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($callnumbererrorhidden);

    	$callnumberhidden = new Zend_Form_Element_Hidden('callnumberhidden');
    	$callnumberhidden->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($callnumberhidden);
    	 
    }
    
    /*
     * Identifying information
     */
    private function populateIdentifyingElements()
    {
    	$titleinput = new ACORNTextField('titleinput');
		$titleinput->setLabel('Title*');
		$titleinput->setName('titleinput');
		$titleinput->setRequired(TRUE);
    	$this->addElement($titleinput);
		
		$projectselectform = new ProjectSelectForm(array(ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$projectselectform->setName('projectselectform');
    	$this->addSubForm($projectselectform, 'projectselectform');
    	
    	$groupselectform = new GroupSelectForm(array(ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$groupselectform->setName('groupselectform');
    	$this->addSubForm($groupselectform, 'groupselectform');
    	
    	$purposeselectform = new PurposeSelectForm();
    	$purposeselectform->setName('purposeselectform');
    	$purposeselectform->setRequired(TRUE);
    	$this->addSubForm($purposeselectform, 'purposeselectform');
    	
    	$curatorselectform = new PersonSelectForm(array(PersonSelectForm::PERSON_SELECT_NAME => 'curatorselect', ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$curatorselectform->setRoleTypes(array('Curator'));
    	$curatorselectform->setLabel('Curator*');
    	$curatorselectform->setName('curatorselectform');
    	$curatorselectform->setRequired(TRUE);
    	$this->addSubForm($curatorselectform, 'curatorselectform');
    	
    	$repositoryselectform = new RepositorySelectForm();
    	$repositoryselectform->setName('repositoryselectform');
    	$repositoryselectform->addRepositoryAttribute('onchange', 'setChargeTo();');
    	$repositoryselectform->setRequired(TRUE);
    	$this->addSubForm($repositoryselectform, 'repositoryselectform');
    
    	$chargetoselectform = new ChargeToSelectForm();
    	$chargetoselectform->setName('chargetoselectform');
    	$chargetoselectform->setRequired(TRUE);
    	$this->addSubForm($chargetoselectform, 'chargetoselectform');
    }
    
	/*
     * Work Info
     */
    private function populateWorkInfoElements()
    {	
    	$worklocationselect = new LocationSelectForm('worklocationselect');
    	$worklocationselect->setLabel('Work Location*');
		$worklocationselect->setName('worklocationselectform');
    	$worklocationselect->setRequired(TRUE);
    	$this->addSubForm($worklocationselect, 'worklocationselectform');
    	
		$workstartdateinput = new DateTextField('workstartdateinput');
		$workstartdateinput->setLabel('Work Start Date*');
		$workstartdateinput->setName('workstartdateinput');
		$workstartdateinput->setRequired(TRUE);
		$workstartdateinput->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$workstartdateinput->setAttrib('onfocus', 'showWorkStartChooser()');
		$workstartdateinput->setAttrib('readonly', 'readonly');
		$this->addElement($workstartdateinput);
		
		$workenddateinput = new DateTextField('workenddateinput');
		$workenddateinput->setName('workenddateinput');
		$workenddateinput->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	        $workenddateinput->setAttrib('onfocus', 'showWorkEndChooser()');
    	        $workenddateinput->setAttrib('onChange', 'checkWorkEndDate(this)');
		$this->addElement($workenddateinput);
		
		$formatselectform = new FormatSelectForm(array(ACORNSelect::INCLUDE_INACTIVE => TRUE, ACORNSelect::INCLUDE_BLANK => TRUE));
		$formatselectform->setName('formatselectform');
		$this->addSubForm($formatselectform, 'formatselectform');
    }
    
	public function isValid($values)
 	{
 		$isvalid = parent::isValid($values);
 		
 		$item = RecordNamespace::getCurrentOSW();
 		$olditem = RecordNamespace::getOriginalOSW();
 		if (count($item->getFunctions()->getReport()->getReportConservators()) == 0)
 		{
 			$isvalid = FALSE;
 			$this->getElement('workdonebyhidden')->addError('Please enter at least one Work Done By.');
 		}
 		// Don't allow more than 1 work type to be added (bug 4210)
 		if (count($item->getWorkTypes()) == 0)
 		{
 			$isvalid = FALSE;
 			$this->getElement('worktypehidden')->addError('Please enter a Work Type.');
 		}
 		
 		$newcallnumbers = $item->getCallNumbers();
 		if (isset($olditem))
 		{
 			//Get the call numbers that were added.
 			$newcallnumbers = $item->getCallNumberDifference($olditem->getCallNumbers());
 		}
 		$callnumbervalidator = new CallNumberValidator($item->getPrimaryKey());
 		$existingduplicatecallnumbers = $item->getDuplicateCallNumbers();
 		foreach ($newcallnumbers as $callnumber)
 		{
 			//Check to see if the call number is in the array of call numbers that
 			//the user overrode.  If it isn't and it exists in another item, an error will be displayed.
 			//If the call number was overridden (in the $existingduplicatecallnumbers array) by the user
 			//after a previous save attempt, it will be saved without further complaints from the validator.
 			if (!in_array($callnumber, $existingduplicatecallnumbers) && !$callnumbervalidator->isValid($callnumber))
 			{
 				$isvalid = FALSE;
 				$messages = $callnumbervalidator->getMessages();
 				$this->getElement('callnumberhidden')->addError(current($messages));
 			}
 		}
 		return $isvalid;
 	}
 	
 	public function isValidPartial(array $values)
 	{
 		$isvalid = parent::isValidPartial($values);
 			
 		$item = RecordNamespace::getCurrentOSW();
 		$olditem = RecordNamespace::getOriginalOSW();
 		if (count($item->getFunctions()->getReport()->getReportConservators()) == 0)
 		{
 			$isvalid = FALSE;
 			$this->getElement('workdonebyhidden')->addError('Please enter at least one Work Done By.');
 		}
 		if (count($item->getWorkTypes()) == 0)
 		{
 			$isvalid = FALSE;
 			$this->getElement('worktypehidden')->addError('Please enter a Work Type.');
 		}
 			
 		$newcallnumbers = $item->getCallNumbers();
 		if (isset($olditem))
 		{
 			//Get the call numbers that were added.
 			$newcallnumbers = $item->getCallNumberDifference($olditem->getCallNumbers());
 		}
 		$callnumbervalidator = new CallNumberValidator($item->getPrimaryKey());
 		$existingduplicatecallnumbers = $item->getDuplicateCallNumbers();
 		foreach ($newcallnumbers as $callnumber)
 		{
 			//Check to see if the call number is in the array of call numbers that
 			//the user overrode.  If it isn't and it exists in another item, an error will be displayed.
 			//If the call number was overridden (in the $existingduplicatecallnumbers array) by the user
 			//after a previous save attempt, it will be saved without further complaints from the validator.
 			if (!in_array($callnumber, $existingduplicatecallnumbers) && !$callnumbervalidator->isValid($callnumber))
 			{
 				$isvalid = FALSE;
 				$messages = $callnumbervalidator->getMessages();
 				$this->getElement('callnumberhidden')->addError(current($messages));
 			}
 		}
 		return $isvalid;
 	}
}
?>