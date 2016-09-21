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
 * EmailProposalForm
 *
 * @author vcrema
 * Created September 30, 2009
 *
 */
 
 class EmailProposalForm extends ACORNForm
{
	/**
	 * Initialize the form
	 */
    public function init()
    {
    	$hiddenactivity = new Zend_Form_Element_Hidden('hiddenactivity');
    	$hiddenactivity->setName('hiddenactivity');
    	$hiddenactivity->setValue('Proposal Submitted');
    	$hiddenactivity->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenactivity);
    	
    	$hiddendetails = new Zend_Form_Element_Hidden('hiddendetails');
    	$hiddendetails->setName('hiddendetails');
    	$hiddendetails->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddendetails);
    	
    	$hiddenemailtype = new Zend_Form_Element_Hidden('hiddenemailtype');
    	$hiddenemailtype->setName('hiddenemailtype');
    	$hiddenemailtype->setValue('Proposal');
    	$hiddenemailtype->setDecorators(Decorators::$ELEMENT_DECORATORS);
    	$this->addElement($hiddenemailtype);
    	
    	$toinput = new ACORNTextField('toinput');
    	$toinput->setLabel('To*');
    	$toinput->setName('toinput');
    	$toinput->setRequired(TRUE);
    	$this->addElement($toinput);
    	
    	$ccinput = new ACORNTextField('ccinput');
    	$ccinput->setLabel('Cc');
    	$ccinput->setName('ccinput');
    	$this->addElement($ccinput);
    	
    	$subjectinput = new ACORNTextField('subjectinput');
    	$subjectinput->setLabel('Subject*');
    	$subjectinput->setName('subjectinput');
    	$subjectinput->setRequired(TRUE);
    	$this->addElement($subjectinput);
    	
    	$messageinput = new ACORNTextArea('messageinput');
    	$messageinput->setLabel('Message*');
    	$messageinput->setName('messageinput');
    	$messageinput->setRequired(TRUE);
    	$this->addElement($messageinput);
    }
}
?>