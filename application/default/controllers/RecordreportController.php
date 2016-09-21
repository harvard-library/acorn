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
 * RecordreportController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 */
	

class RecordreportController extends Zend_Controller_Action
{
	private $recordForm;
	private $acornRedirector;
	
	private function getRecordForm()
	{
		if (is_null($this->recordForm))
		{
			$this->recordForm = new RecordForm();
		}
		return $this->recordForm;
	}
	
	
	private function getAcornRedirector()
	{
		if (is_null($this->acornRedirector))
		{
			$this->acornRedirector = new AcornRedirector();
		}
		return $this->acornRedirector;
	}
	
	public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
        {
            $this->_helper->redirector('index', 'index');
        }
    }
	
    
	public function savereportAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getRecordForm()->getSubForm("recordreportform");
			$formData = $this->_request->getPost();
	
			$item = RecordNamespace::getCurrentItem();
			$report = $item->getFunctions()->getReport();
			//If the report being saved is not the item in the session, 
			//set the correct session item.
			if (!empty($formData['hiddenreportitemid']) 
				&& isset($report) 
				&& $item->getItemID() != $formData['hiddenreportitemid'])
			{
				$item = ItemDAO::getItemDAO()->getItem($formData['hiddenreportitemid']);
				if (is_null($item))
				{
					RecordNamespace::setCurrentItem(new Item());
					RecordNamespace::setOriginalItem(new Item());
				}
				else 
				{
					RecordNamespace::setCurrentItem($item);
					RecordNamespace::setOriginalItem(ItemDAO::getItemDAO()->getItem($item->getItemID()));
				}
			}
			
			RecordNamespace::setRenderTab(RecordNamespace::REPORT);
			if ($form->isValid($formData))
			{
				//Save the data
				$reportid = $this->saveItemReport($form->getValues());
				if (!is_null($reportid))
				{
					$item = RecordNamespace::getCurrentItem();
					$item->getFunctions()->getReport()->setPrimaryKey($reportid);
					RecordNamespace::setCurrentItem($item);
					
					//Clone the current item proposal and make it the 
					//original item
					$origitem = RecordNamespace::getOriginalItem();
					$clonedreport = clone $item->getFunctions()->getReport();
					//Since we only saved the report, we are only
					//updating that piece of information in the original item
					$origitem->getFunctions()->setReport($clonedreport);
					RecordNamespace::setOriginalItem($origitem);
                                        
					RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);

                                        //Ensure it goes to the proper tab
					$recnum = $item->getItemID() . '#tabview=tab3';

                                        // clear report conservators from session, 
                                        // we want to fetch an updated list from the database
                                        $report->setReportConservators(NULL);

                                        $this->getAcornRedirector()->setGoto('index', 'record', NULL, array('recordnumber' => $recnum));
 				}
				//TODO otherwise display an error message regarding the save
				else 
				{
					$this->displayRecordErrors($formData);
				}
			}
			else
			{
					$this->displayRecordErrors($formData);
			}
		}
		else
		{
			//If the url was typed directly, go to the DE screen.
			$this->_helper->redirector('newitem');
		}
    }
    
	private function displayRecordErrors(array $formData)
    {
    	$form = $this->getRecordForm();
		//Populate the data with what the user
		//gave but also display the error messages.
		$form->populate($formData);
		$this->view->form = $form;
		
		
		$item = RecordNamespace::getCurrentItem();
		$recordnumberlabel = "Record #" . $item->getItemID();
		$recordinfolabel = $item->getInfoForLabel();
		$createdate = "";
		$createdby = "";
		$audittrailitems = $item->getAuditTrail("Insert");
		if (!empty($audittrailitems))
		{
			$audittrailitem = current($audittrailitems);
			$createdate = $audittrailitem->getDate();
			$createdby = $audittrailitem->getPerson()->getDisplayName();
		}
		$this->view->placeholder("reporterrormessage")->set('Changes were not saved.  Please correct the errors in the form below.');
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('recordnumber')->set($recordnumberlabel);
		$this->view->placeholder('recordinfo')->set($recordinfolabel);

		$this->renderScript('record/index.phtml');
    }
    
	private function saveItemReport(array $formvalues)
    {
    	//Get the current item
    	$item = RecordNamespace::getCurrentItem();
    	$originalitem = RecordNamespace::getOriginalItem();
    	
    	$report = $item->getFunctions()->getReport();
    	if (is_null($report))
    	{
    		$report = new Report();
    	}
    	$report->setAdminOnly($formvalues['adminonlycheckbox']);
    	$report->setCustomHousingOnly($formvalues['customhousingonlycheckbox']);
    	$dims = new Dimensions();
    	$dims->setHeight($formvalues['reportheightinput']);
    	$dims->setThickness($formvalues['reportthicknessinput']);
    	$dims->setWidth($formvalues['reportwidthinput']);
    	$dims->setUnit($formvalues['reportunitselect']);
    	$report->setDimensions($dims);
    	$report->setExamOnly($formvalues['examonlycheckbox']);
    	$report->setFormat($formvalues['reportformatselect']);
    	$report->setPreservationRecommendations($formvalues['presrectextarea']);
    	$report->setReportBy($formvalues['reportbyselect']);
    	$report->setReportDate($formvalues['reportdateinput']);
    	$report->setSummary($formvalues['reporttreatmentsummarytextarea']);
    	$report->setTreatment($formvalues['reporttreatmenttextarea']);
    	$report->setWorkLocation($formvalues['worklocationselect']);
    	$item->setComments($formvalues['reportcommentstextarea']);
    	$report->setAdditionalMaterialsOnFile($formvalues['additionalmaterialsonfilecheckbox']);
    	
    	$reportid = ReportDAO::getReportDAO()->saveItemReport($report, $originalitem->getFunctions()->getReport(), $item);
    	return $reportid;
    }    
}
?>