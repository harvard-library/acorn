<?php
/**
 * RecordStatusForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
 class RecordStatusForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$this->populateIdentifyingElements();
    	$this->populatePhysicalDescriptionElements();
    	$this->populateRepositoryElements();
    	
    	$donebutton = new Zend_Form_Element_Button('donebutton');
    	$donebutton->setName('donebutton');
    	$donebutton->setLabel("Done");
    	$donebutton->setDecorators(Decorators::$BUTTON_DECORATORS);
    	$this->addElement($donebutton);
    	
    	$gotorecordbutton = new Zend_Form_Element_Button('gotorecordbutton');
    	$gotorecordbutton->setName('gotorecordbutton');
    	$gotorecordbutton->setLabel("Go To Record");
    	$gotorecordbutton->setDecorators(Decorators::$BUTTON_DECORATORS);
    	$this->addElement($gotorecordbutton);
    	
    	$elements = $this->getElements();
    	foreach ($elements as $element)
    	{
    		if ($element->getType() != 'Zend_Form_Element_Hidden' && $element->getType() != 'Zend_Form_Element_Button')
    		{
    			$element->setAttrib('readonly', 'readonly');
    		}
    	}
    	
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
     * Identifying information
     */
    private function populateIdentifyingElements()
    {
    	$callnumberinput = new ACORNTextField('callnumberinput');
    	$callnumberinput->setLabel('Call Number(s)');
		$callnumberinput->setName('callnumberinput');
		$this->addElement($callnumberinput);
    	
		$curatorinput = new ACORNTextField('curatorinput');
    	$curatorinput->setLabel('Curator/Librarian');
		$curatorinput->setName('curatorinput');
		$this->addElement($curatorinput);
    	
		$projectinput = new ACORNTextField('projectinput');
    	$projectinput->setLabel('Project');
		$projectinput->setName('projectinput');
		$this->addElement($projectinput);
    	
		$groupinput = new ACORNTextField('groupinput');
    	$groupinput->setLabel('Group');
		$groupinput->setName('groupinput');
		$this->addElement($groupinput);
    	
    	$coordinatorinput = new ACORNTextField('coordinatorinput');
    	$coordinatorinput->setLabel('Coordinator');
		$coordinatorinput->setName('coordinatorinput');
		$this->addElement($coordinatorinput);
    	
    	$repositoryinput = new ACORNTextField('repositoryinput');
    	$repositoryinput->setLabel('Repository');
		$repositoryinput->setName('repositoryinput');
		$this->addElement($repositoryinput);
		
		$departmentinput = new ACORNTextField('departmentinput');
    	$departmentinput->setLabel('Department');
		$departmentinput->setName('departmentinput');
		$this->addElement($departmentinput);
		
		$chargetoinput = new ACORNTextField('chargetoinput');
		$chargetoinput->setLabel('Charge To');
		$chargetoinput->setName('chargetoinput');
		$this->addElement($chargetoinput);
		
		$commentstextarea = new ACORNTextArea('commentstextarea');
		$commentstextarea->setLabel('Comments');
		$commentstextarea->setName('commentstextarea');
		$this->addElement($commentstextarea);
    }
    
	/*
     * Physical content information
     */
    private function populatePhysicalDescriptionElements()
    {
    	$authorinput = new ACORNTextField('authorinput');
		$authorinput->setLabel('Author/Artist');
		$authorinput->setName('authorinput');
		$this->addElement($authorinput);
		
		$titleinput = new ACORNTextField('titleinput');
		$titleinput->setLabel('Title');
		$titleinput->setName('titleinput');
		$this->addElement($titleinput);
		
		$dateofobjectinput = new ACORNTextField('dateofobjectinput');
		$dateofobjectinput->setLabel('Date of Object');
		$dateofobjectinput->setName('dateofobjectinput');
		$this->addElement($dateofobjectinput);	
		
		$numberofitemsinput = new ACORNTextField('numberofitemsinput');
		$numberofitemsinput->setLabel('Number of Items');
		$numberofitemsinput->setName('numberofitemsinput');
		$this->addElement($numberofitemsinput);
	}
    
	/*
     * Repository information
     */
    private function populateRepositoryElements()
    {
    	$expecteddateofreturninput = new DateTextField('expecteddateofreturninput');
    	$expecteddateofreturninput->setLabel('Expected Date of Return');
		$expecteddateofreturninput->setName('expecteddateofreturninput');
		$this->addElement($expecteddateofreturninput);
    	
    	$insuranceinput = new ACORNTextField('insuranceinput');
    	//$insuranceinput->setLabel('Insurance Value');
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
    
    
}
?>