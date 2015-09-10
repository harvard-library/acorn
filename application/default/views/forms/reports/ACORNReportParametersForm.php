<?php
/**
 * ACORNReportParametersForm
 *
 * @author vcrema
 * Created August 10, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
 
class ACORNReportParametersForm extends ACORNForm 
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$locationselectform = new LocationSelectForm(array(
    		LocationSelectForm::LOCATION_SELECT_NAME => 'locationselect',
    		LocationSelectForm::IS_REPOSITORY_SEARCH => TRUE));
    	$locationselectform->setName('locationselectform');
    	$locationselectform->setLabel('Repository*');
    	$locationselectform->setRequired(TRUE);
    	$this->addSubForm($locationselectform, 'locationselectform');
    	
    	$personselectform = new PersonSelectForm(array(PersonSelectForm::PERSON_SELECT_NAME => 'personselect'));
    	$personselectform->setName('personselectform');
    	$personselectform->setLabel('User*');
    	$personselectform->setRequired(TRUE);
    	$this->addSubForm($personselectform, 'personselectform');
    	
    	$projectselectform = new ProjectSelectForm();
    	$projectselectform->setName('projectselectform');
    	$projectselectform->setRequired(TRUE);
    	$this->addSubForm($projectselectform, 'projectselectform');
    	
    	$begindateinput = new DateTextField('begindateinput');
    	$begindateinput->setLabel('Start Date*');
		$begindateinput->setName('begindateinput');
		$begindateinput->setAttrib('onfocus', 'showCalendarChooser(\'begindateinput\')');
		$begindateinput->setAttrib('readonly', 'readonly');
		$begindateinput->setRequired(TRUE);
		$zenddate = new Zend_Date();
		$begindateinput->setValue($zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT));
    	$this->addElement($begindateinput);
    	
    	$enddateinput = new DateTextField('enddateinput');
    	$enddateinput->setLabel('End Date*');
		$enddateinput->setName('enddateinput');
		$enddateinput->setAttrib('onfocus', 'showCalendarChooser(\'enddateinput\')');
		$enddateinput->setAttrib('readonly', 'readonly');
		$enddateinput->setRequired(TRUE);
    	$enddateinput->setValue($zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT));
    	$this->addElement($enddateinput);
    }
}
?>