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
 * RecordReportForm
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
 
 class RecordReportForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hiddenreportid = new Zend_Form_Element_Hidden('hiddenreportitemid');
    	$hiddenreportid->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenreportid);
    	
    	$this->initReportBasicInformation();
    	$this->initDimensionElements();
    	$this->initCommentsAndChecksElements();
    	$this->initTreatmentAndRecommendationElements();
    	$this->populateHiddenElements();
    	
    	$actualhours = new ACORNTextField('actualhours');
    	$actualhours->setLabel('Actual Hours');
    	$actualhours->setName('actualhours');
    	$actualhours->setAttrib('class', 'number');
    	$actualhours->setAttrib('readonly', 'readonly');
    	$actualhours->addValidator(new NumberValidator());
    	$this->addElement($actualhours);
    	
    	$this->setDecorators(array(
   			array(
   			//Use a view script for the form layout
   			//The view script will call all of the elements and place
   			//them in the desired format.
   			'ViewScript', array('viewScript' => 'record/recordreport.phtml'),
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
    }
    
    private function initReportBasicInformation()
    {
    	$reportdateinput = new DateTextField('reportdateinput');
    	$reportdateinput->setLabel('Report Date*');
		$reportdateinput->setName('reportdateinput');
		$reportdateinput->setAttrib('onfocus', 'showCalendarChooser(\'reportdateinput\')');
		$reportdateinput->setAttrib('readonly', 'readonly');
		$reportdateinput->setRequired(TRUE);
    	$this->addElement($reportdateinput);
    	
    	$reportbyselectform = new PersonSelectForm('reportbyselect');
    	$reportbyselectform->setRoleTypes(array('Staff'));
    	$reportbyselectform->setName('reportbyselectform');
    	$reportbyselectform->setRequired(TRUE);
    	$reportbyselectform->setLabel('Report By*');
    	$this->addSubForm($reportbyselectform, 'reportbyselectform');
    	
    	$worklocationselectform = new LocationSelectForm('worklocationselect');
    	$worklocationselectform->setName('worklocationselectform');
    	$worklocationselectform->setLabel('Work Location*');
    	$worklocationselectform->setRequired(TRUE);
    	$this->addSubForm($worklocationselectform, 'worklocationselectform');
    	
    	$formatselectform = new FormatSelectForm(array(FormatSelectForm::FORMAT_SELECT_NAME => 'reportformatselect', ACORNSelect::INCLUDE_INACTIVE => TRUE, ACORNSelect::INCLUDE_BLANK => TRUE));
    	$formatselectform->setName('formatselectform');
    	$formatselectform->setRequired(TRUE);
    	$this->addSubForm($formatselectform, 'formatselectform');
    }
    
	private function initDimensionElements()
    {
    	$multioptions = array(Dimensions::CENTIMETERS => Dimensions::CENTIMETERS, Dimensions::INCHES => Dimensions::INCHES);
    	$reportunitselect = new ACORNSelect('reportunitselect');
    	$reportunitselect->setName('reportunitselect');
    	$reportunitselect->setMultiOptions($multioptions);
		$this->addElement($reportunitselect);
		
		$reportheightinput = new ACORNTextField('reportheightinput');
    	$reportheightinput->setName('reportheightinput');
    	$reportheightinput->addValidator(new NumberValidator());
    	$this->addElement($reportheightinput);
    	
    	$reportwidthinput = new ACORNTextField('reportwidthinput');
    	$reportwidthinput->setName('reportwidthinput');
    	$reportwidthinput->addValidator(new NumberValidator());
    	$this->addElement($reportwidthinput);
    	
    	$reportthicknessinput = new ACORNTextField('reportthicknessinput');
    	$reportthicknessinput->setName('reportthicknessinput');
    	$reportthicknessinput->addValidator(new NumberValidator());
    	$this->addElement($reportthicknessinput);
    }
    
	private function initCommentsAndChecksElements()
    {
    	$reportcommentstextarea = new ACORNTextArea('reportcommentstextarea');
		$reportcommentstextarea->setName('reportcommentstextarea');
		$reportcommentstextarea->setAttrib('class', 'fulltextarea');
		$this->addElement($reportcommentstextarea);
		
		$adminonlycheckbox = new Zend_Form_Element_Checkbox('adminonlycheckbox');
        $adminonlycheckbox->setLabel('Admin Only');
        $adminonlycheckbox->setAttribs(array('class' => 'checkboxinput'));
        $adminonlycheckbox->setDecorators(Decorators::$CHECKBOX_DECORATORS);
    	$this->addElement($adminonlycheckbox);
    	
    	$examonlycheckbox = new Zend_Form_Element_Checkbox('examonlycheckbox');
        $examonlycheckbox->setLabel('Exam Only');
        $examonlycheckbox->setAttribs(array('class' => 'checkboxinput'));
        $examonlycheckbox->setDecorators(Decorators::$CHECKBOX_DECORATORS);
    	$this->addElement($examonlycheckbox);
    	
    	$customhousingonlycheckbox = new Zend_Form_Element_Checkbox('customhousingonlycheckbox');
        $customhousingonlycheckbox->setLabel('Custom Housing Only');
        $customhousingonlycheckbox->setAttribs(array('class' => 'checkboxinput'));
        $customhousingonlycheckbox->setDecorators(Decorators::$CHECKBOX_DECORATORS);
    	$this->addElement($customhousingonlycheckbox);
    	
    	$additionalmaterialsonfilecheckbox = new Zend_Form_Element_Checkbox('additionalmaterialsonfilecheckbox');
        $additionalmaterialsonfilecheckbox->setLabel('Additional Materials on File');
        $additionalmaterialsonfilecheckbox->setAttribs(array('class' => 'checkboxinput'));
        $additionalmaterialsonfilecheckbox->setDecorators(Decorators::$CHECKBOX_DECORATORS);
    	$this->addElement($additionalmaterialsonfilecheckbox);
    }
    
	private function initTreatmentAndRecommendationElements()
    {
    	$reporttreatmentselect = new ACORNSelect('reporttreatmentselect');
    	$reporttreatmentselect->setLabel('Treatment');
    	$reporttreatmentselect->setName('reporttreatmentselect');
		$reporttreatmentselect->setAttrib('onclick', 'loadAutotextCaptions(\'reporttreatmentselect\',\'Treatment\', 1, -1)');
		$reporttreatmentselect->setAttrib('onchange', 'loadAutotextFromSelection(\'reporttreatmentselect\',\'reporttreatmenttextarea\')');
		$this->addElement($reporttreatmentselect);
		
		$reporttreatmenttextarea = new ACORNTextArea('reporttreatmenttextarea');
		$reporttreatmenttextarea->setName('reporttreatmenttextarea');
		$reporttreatmenttextarea->setAttrib('class', 'fulltextarea');
		$this->addElement($reporttreatmenttextarea);
		
		$presrec1select = new ACORNSelect('presrec1select');
    	$presrec1select->setName('presrec1select');
		$presrec1select->setAttrib('onclick', 'loadAutotextCaptions(\'presrec1select\',\'Preservation\', 1, -1)');
		$presrec1select->setAttrib('onchange', 'clearList(\'presrec2select\')');
		$this->addElement($presrec1select);
		
		$presrec2select = new ACORNSelect('presrec2select');
    	$presrec2select->setName('presrec2select');
		$presrec2select->setAttrib('onclick', 'loadAutotextCaptions(\'presrec2select\',\'Dependency\', 1, document.getElementById(\'presrec1select\').value)');
		$presrec2select->setAttrib('onchange', 'loadAutotextFromSelection(\'presrec2select\',\'presrectextarea\')');
		$this->addElement($presrec2select);
		
		$presrectextarea = new ACORNTextArea('presrectextarea');
		$presrectextarea->setName('presrectextarea');
		$presrectextarea->setAttrib('class', 'fulltextarea');
		$this->addElement($presrectextarea);
		
		$reporttreatmentsummarytextarea = new ACORNTextArea('reporttreatmentsummarytextarea');
		$reporttreatmentsummarytextarea->setName('reporttreatmentsummarytextarea');
		$reporttreatmentsummarytextarea->setAttrib('class', 'fulltextarea');
		$this->addElement($reporttreatmentsummarytextarea);
    }
    
	public function isValid($values)
 	{
 		$isvalid = parent::isValid($values);
 		
 		$item = RecordNamespace::getCurrentItem();
 		$report = $item->getFunctions()->getReport();
                $conservators = $report->getReportConservators();
                
                if (count($conservators) == 0)
                {
                        $isvalid = FALSE;
                        $this->getElement('workdonebyhidden')->addError('Please enter at least one Work Done By.');
                }
                else
                {
                        foreach ($conservators as $conservator)
                        {
                                $dateCompleted = $conservator->getDateCompleted();
                    
                                if (is_null($dateCompleted))
                                {
                                        $isvalid = FALSE;
                                        $this->getElement('workdonebyhidden')->addError('Please enter the date that work was done on.');
                                        break;
                                }
                        }
                }

                return $isvalid;
 	}
}
?>
