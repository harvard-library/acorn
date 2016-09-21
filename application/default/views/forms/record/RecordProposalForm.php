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
 * RecordProposalForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class RecordProposalForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hiddenproposalid = new Zend_Form_Element_Hidden('hiddenproposalitemid');
    	$hiddenproposalid->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenproposalid);
    	
    	$coordinatorinput = new ACORNTextField('coordinatorinput');
    	$coordinatorinput->setLabel('Coordinator');
    	$coordinatorinput->setName('coordinatorinput');
    	$coordinatorinput->setAttrib('readonly', 'readonly');
    	$this->addElement($coordinatorinput);
    	
    	$proposalcommentstextarea = new ACORNTextArea('proposalcommentstextarea');
    	$proposalcommentstextarea->setLabel('Comments');
    	$proposalcommentstextarea->setName('proposalcommentstextarea');
    	$this->addElement($proposalcommentstextarea);
    	
    	$proposedhoursmin = new ACORNTextField('proposedhoursmin');
    	$proposedhoursmin->setLabel('Proposed Hours*');
    	$proposedhoursmin->setName('proposedhoursmin');
    	$proposedhoursmin->setAttrib('class', 'number');
    	$proposedhoursmin->removeFilter('EmptyToNullFilter');
    	//$proposedhoursmin->setRequired(TRUE);
    	$proposedhoursmin->setDecorators(Decorators::$REQUIRED_ELEMENT_DECORATORS);
    	$proposedhoursmin->addValidator(new NumberValidator());
    	$this->addElement($proposedhoursmin);
    	
    	$proposedhoursmax = new ACORNTextField('proposedhoursmax');
    	$proposedhoursmax->setName('proposedhoursmax');
    	$proposedhoursmax->setAttrib('class', 'number');
    	$proposedhoursmax->addValidator(new NumberValidator());
    	$proposedhoursmax->removeFilter('EmptyToNullFilter');
    	$this->addElement($proposedhoursmax);
    	
    	$this->initDateAndLocationElements();
    	$this->initDimensionElements();
    	$this->initTextareaElements();
    	$this->populateHiddenElements();
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'record/recordproposal.phtml'),
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
    	$proposalbyhidden = new Zend_Form_Element_Hidden('proposalbyhidden');
    	$proposalbyhidden->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($proposalbyhidden);
    }
    
    private function initDateAndLocationElements()
    {
    	$proposaldateinput = new DateTextField('proposaldateinput');
    	$proposaldateinput->setLabel('Proposal Date*');
		$proposaldateinput->setName('proposaldateinput');
		$proposaldateinput->setAttrib('onfocus', 'showCalendarChooser(\'proposaldateinput\')');
		$proposaldateinput->setAttrib('readonly', 'readonly');
		$proposaldateinput->setRequired(TRUE);
    	$this->addElement($proposaldateinput);
    	
    	$examdateinput = new DateTextField('examdateinput');
    	$examdateinput->setLabel('Exam Date');
		$examdateinput->setAttrib('onfocus', 'showExamCalendarChooser(\'examdateinput\')');
		$examdateinput->setAttrib('readonly', 'readonly');
		$examdateinput->setName('examdateinput');
		$this->addElement($examdateinput);
		
		$examlocationselectform = new LocationSelectForm(array(LocationSelectForm::LOCATION_SELECT_NAME => 'examlocationselect', ACORNSelect::INCLUDE_INACTIVE => FALSE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$examlocationselectform->setName('examlocationselectform');
    	$examlocationselectform->setLabel('Exam Location');
    	$this->addSubForm($examlocationselectform, 'examlocationselectform');
    }
    
    private function initDimensionElements()
    {
    	$multioptions = array(Dimensions::CENTIMETERS => Dimensions::CENTIMETERS, Dimensions::INCHES => Dimensions::INCHES);
    	$unitselect = new ACORNSelect('unitselect');
    	$unitselect->setName('unitselect');
    	$unitselect->setMultiOptions($multioptions);
		$this->addElement($unitselect);
		
    	$proposalheightinput = new ACORNTextField('proposalheightinput');
    	$proposalheightinput->setLabel('Height');
    	$proposalheightinput->setName('proposalheightinput');
    	$proposalheightinput->addValidator(new NumberValidator());
    	$this->addElement($proposalheightinput);
    	
    	$proposalwidthinput = new ACORNTextField('proposalwidthinput');
    	$proposalwidthinput->setLabel('Width');
    	$proposalwidthinput->setName('proposalwidthinput');
    	$proposalwidthinput->addValidator(new NumberValidator());
    	$this->addElement($proposalwidthinput);
    	
    	$proposalthicknessinput = new ACORNTextField('proposalthicknessinput');
    	$proposalthicknessinput->setLabel('Thickness');
    	$proposalthicknessinput->setName('proposalthicknessinput');
    	$proposalthicknessinput->addValidator(new NumberValidator());
    	$this->addElement($proposalthicknessinput);
    }
    
    private function initTextareaElements()
    {
    	$proposaldescriptionselect = new ACORNSelect('proposaldescriptionselect');
    	$proposaldescriptionselect->setLabel('Description');
    	$proposaldescriptionselect->setName('proposaldescriptionselect');
		$proposaldescriptionselect->setAttrib('onclick', 'loadAutotextCaptions(\'proposaldescriptionselect\',\'Description\', 1, -1)');
		$proposaldescriptionselect->setAttrib('onchange', 'loadAutotextFromSelection(\'proposaldescriptionselect\',\'proposaldescriptiontextarea\')');
		$this->addElement($proposaldescriptionselect);
		
		$proposaldescriptiontextarea = new ACORNTextArea('proposaldescriptiontextarea');
		$proposaldescriptiontextarea->setName('proposaldescriptiontextarea');
		$proposaldescriptiontextarea->setAttrib('class', 'fullproposaltextarea');
		$this->addElement($proposaldescriptiontextarea);
		
		$proposalconditionselect = new ACORNSelect('proposalconditionselect');
    	$proposalconditionselect->setLabel('Condition');
    	$proposalconditionselect->setName('proposalconditionselect');
		$proposalconditionselect->setAttrib('onclick', 'loadAutotextCaptions(\'proposalconditionselect\',\'Condition\', 1, -1)');
		$proposalconditionselect->setAttrib('onchange', 'loadAutotextFromSelection(\'proposalconditionselect\',\'proposalconditiontextarea\')');
		$this->addElement($proposalconditionselect);
		
		$proposalconditiontextarea = new ACORNTextArea('proposalconditiontextarea');
		$proposalconditiontextarea->setName('proposalconditiontextarea');
		$proposalconditiontextarea->setAttrib('class', 'fullproposaltextarea');
		$this->addElement($proposalconditiontextarea);
		
		$proposaltreatmentselect = new ACORNSelect('proposaltreatmentselect');
    	$proposaltreatmentselect->setLabel('Treatment');
    	$proposaltreatmentselect->setName('proposaltreatmentselect');
		$proposaltreatmentselect->setAttrib('onclick', 'loadAutotextCaptions(\'proposaltreatmentselect\',\'ProposedTreatment\', 1, -1)');
		$proposaltreatmentselect->setAttrib('onchange', 'loadAutotextFromSelection(\'proposaltreatmentselect\',\'proposaltreatmenttextarea\')');
		$this->addElement($proposaltreatmentselect);
		
		$proposaltreatmenttextarea = new ACORNTextArea('proposaltreatmenttextarea');
		$proposaltreatmenttextarea->setName('proposaltreatmenttextarea');
		$proposaltreatmenttextarea->setAttrib('class', 'fullproposaltextarea');
		$this->addElement($proposaltreatmenttextarea);
    }
    
    public function isValid($values)
    {
    	$isvalid = parent::isValid($values);
    	
    	if (!isset($values['proposedhoursmin']) || is_null($values['proposedhoursmin']))
    	{
	    	if (!isset($values['proposedhoursmax']) || is_null($values['proposedhoursmax']))
	    	{
	    		$this->getElement('proposedhoursmax')->addError('Please set the Proposed Hours.');
	    		$isvalid = FALSE;
	    	}
    	}
    	return $isvalid;
    }
}
?>