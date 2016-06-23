<?php
/**
 * RecordIdentificationForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class RecordIdentificationForm extends ACORNForm
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
    	
    	$hiddenitemid = new Zend_Form_Element_Hidden('hiddenitemid');
    	$hiddenitemid->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenitemid);
    	
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
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'record/recordidentification.phtml'),
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
    	$callnumbererrorhidden = new Zend_Form_Element_Hidden('callnumbererrorhidden');
    	$callnumbererrorhidden->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($callnumbererrorhidden);

    	$titleerrorhidden = new Zend_Form_Element_Hidden('titleerrorhidden');
    	$titleerrorhidden->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($titleerrorhidden);
    	 
    	$callnumberhidden = new Zend_Form_Element_Hidden('callnumberhidden');
        $callnumberhidden->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($callnumberhidden);
    	
    	$workassignedtohidden = new Zend_Form_Element_Hidden('workassignedtohidden');
        $workassignedtohidden->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($workassignedtohidden);
    }
    
    /*
     * Identifying information
     */
    private function populateIdentifyingElements()
    {
    	$projectselectform = new ProjectSelectForm(array(ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$projectselectform->setName('projectselectform');
    	$this->addSubForm($projectselectform, 'projectselectform');
    	
    	$groupselectform = new GroupSelectForm(array(ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$groupselectform->setName('groupselectform');
    	$this->addSubForm($groupselectform, 'groupselectform');
    	
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
    	$curatorselectform->setAttrib('onchange', 'setApprovingCurator()');
    	$this->addSubForm($curatorselectform, 'curatorselectform');
    	
    	$approvingcuratorselectform = new PersonSelectForm(array(PersonSelectForm::PERSON_SELECT_NAME => 'approvingcuratorselect', ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$approvingcuratorselectform->setRoleTypes(array('Curator'));
    	$approvingcuratorselectform->setLabel('Approving Curator*');
    	$approvingcuratorselectform->setName('approvingcuratorselectform');
    	$approvingcuratorselectform->setRequired(TRUE);
    	$this->addSubForm($approvingcuratorselectform, 'approvingcuratorselectform');
    	
    	$purposeselectform = new PurposeSelectForm(array(ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$purposeselectform->setName('purposeselectform');
    	$purposeselectform->setRequired(TRUE);
    	$this->addSubForm($purposeselectform, 'purposeselectform');
    	
    	$storageinput = new ACORNTextField('storageinput');
		$storageinput->setLabel('Storage');
		$storageinput->setName('storageinput');
		$this->addElement($storageinput);
    	
    	$repositoryselectform = new RepositorySelectForm(array(ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$repositoryselectform->addRepositoryAttribute('onchange', 'setChargeTo(); loadCurators(\'curatorselect\',0,1); clearList(\'curatorselect\'); loadCurators(\'approvingcuratorselect\',0,1); clearList(\'approvingcuratorselect\');');
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
	
 		$item = RecordNamespace::getCurrentItem();
 		$olditem = RecordNamespace::getOriginalItem();
 		
 		//The initial save checks whether the Call Numbers or Title,
 		//have been used by another item in the database.  If this is the second save,
 		//it is considered the override save so do not check the duplicate validators
 		//for those fields.
 		
 		if (count($item->getCallNumbers()) == 0)
 		{
 			$isvalid = FALSE;
 			$this->getElement('callnumberhidden')->addError('Please enter at least one Call Number.');
 		}
 		
 		if (count($item->getWorkAssignedTo()) == 0)
 		{
 			$isvalid = FALSE;
 			$this->getElement('workassignedtohidden')->addError('Please enter at least one Work Assigned To.');
 		}
 		 		
 		$oldtitle = NULL;
 		if (isset($olditem))
 		{
 			$oldtitle = $olditem->getTitle();
 		}
 		/*
 		 * This checks towee that the title changed AND if it has not been overridden.
 		 * If it changed and was not overridden, it then verifies
 		 * that the title is not being used in another item before saving.
 		 */
 		if (!empty($values['titleinput']) 
 				&& $oldtitle != $values['titleinput']
 				&& $item->getDuplicateTitle() != $values['titleinput'])
 		{
 			$titlevalidator = new TitleValidator($item->getItemID());
 		
 			if ($item->getDuplicateTitle() != $values['titleinput']
 					&& !$titlevalidator->isValid($values['titleinput']))
 			{
 				$isvalid = FALSE;
 				$messages = $titlevalidator->getMessages();
 				$this->getElement('titleinput')->addError(current($messages));
 			}
 				
 		}
 		return $isvalid;
 	}
 	
	public function isValidPartial(array $values)
 	{
 		$isvalid = parent::isValidPartial($values);
 		
 		$item = RecordNamespace::getCurrentItem();
 		$olditem = RecordNamespace::getOriginalItem();
 		
 		//The initial save checks whether the Call Numbers or Title
 		//have been used by another item in the database.  If this is the second save,
 		//it is considered the override save so do not check the duplicate validators
 		//for those fields.
 		$saveduplicates = $values['duplicatemessagehidden'];
 			
 		if (count($item->getCallNumbers()) == 0)
 		{
 			$isvalid = FALSE;
 			$this->getElement('callnumberhidden')->addError('Please enter at least one Call Number.');
 		}
 		else
 		{
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
 		}
 		if (count($item->getWorkAssignedTo()) == 0)
 		{
 			$isvalid = FALSE;
 			$this->getElement('workassignedtohidden')->addError('Please enter at least one Work Assigned To.');
 		}
 		 		
 		$oldtitle = NULL;
 		if (isset($olditem))
 		{
 			$oldtitle = $olditem->getTitle();
 		}
 		/*
 		 * This checks towee that the title changed AND if it has not been overridden.
 		 * If it changed and was not overridden, it then verifies
 		 * that the title is not being used in another item before saving.
 		 */
 		if (!empty($values['titleinput']) 
 				&& $oldtitle != $values['titleinput']
 				&& $item->getDuplicateTitle() != $values['titleinput'])
 		{
 			$titlevalidator = new TitleValidator($item->getItemID());
 		
 			if ($item->getDuplicateTitle() != $values['titleinput']
 					&& !$titlevalidator->isValid($values['titleinput']))
 			{
 				$isvalid = FALSE;
 				$messages = $titlevalidator->getMessages();
 				$this->getElement('titleinput')->addError(current($messages));
 			}
 				
 		}
 		return $isvalid;
 	}
}
?>