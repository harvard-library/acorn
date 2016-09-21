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
 * ACORNReportParametersForm
 *
 * @author vcrema
 * Created August 10, 2009
 *
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