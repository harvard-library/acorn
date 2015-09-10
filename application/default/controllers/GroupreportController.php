<?php

/**
 * GroupreportController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class GroupreportController extends Zend_Controller_Action
{
	private $groupForm;
	
	private function getGroupForm()
	{
		if (is_null($this->groupForm))
		{
			$this->groupForm = new GroupForm();
		}
		return $this->groupForm;
	}
	
	public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
        {
            $this->_helper->redirector('index', 'index');
        }
    }
	
    
	public function updaterecordsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getGroupForm()->getSubForm("groupreportform");
			$formData = $this->_request->getPost();
			$formData = $form->getNonEmptyValues($formData);
			GroupNamespace::setRenderTab(GroupNamespace::REPORT);
			
			if ($form->isValidPartial($formData))
			{
				$values = $form->getValues();
				$values = $form->getNonEmptyValues($values);
				
				//If the group hasn't been created, create it.
				$group = GroupNamespace::getCurrentGroup();
				if (isset($group) && count($group->getRecords()) > 0)
				{
					$records = $group->getRecords();
					$updatedrecords = array();
					$oldreports = array();
					
					$copyproposaltreatment = isset($values['reporttreatmenttextarea']) && $values['reporttreatmenttextarea'] == "(Proposed Treatment Will Be Copied For All Records)";
					if ($copyproposaltreatment)
				    {
				    	unset($values['reporttreatmenttextarea']);
				    }
					foreach ($records as $record)
					{
						//Update the comment value in the record if it is set.
						if (isset($values['reportcommentstextarea']))
				    	{
				    		$record->appendComments($values['reportcommentstextarea']);
				    	}

						//First make sure the record has the report
						$report = $record->getFunctions()->getReport();
						//If this is a new report, set the format to the initial format 
						//and the counts to the initial counts.
				    	if (is_null($report->getPrimaryKey()))
				    	{
				    		$report->setFormat($record->getFormatID());
				    		$report->setCounts($record->getInitialCounts());
				    	}
				    	
				    	//If it is a new report, set the counts to the initial counts
				    	
						if (is_null($report->getPrimaryKey()))
				    	{
				    		$report->setWorkLocation($record->getFunctions()->getLogin()->getTransferTo());
				    	}
				    	
				    	//If the user selected the propsed treatment, copy the record's proposal.
				    	if ($copyproposaltreatment)
				    	{
				    		$report->setTreatment($record->getFunctions()->getProposal()->getTreatment());
				    	}
				    	
						$oldreports[$record->getPrimaryKey()] = $report;
						$updatedreport = clone $report;
						$updatedreport = $this->updateItemReport($updatedreport, $values, $group);
						$record->getFunctions()->setReport($updatedreport);
						array_push($updatedrecords, $record);
					}
					
					$numberitemsupdated = ReportDAO::getReportDAO()->updateMultipleItems($updatedrecords, $oldreports);
					
					//Clear out the namespace lists so the group records get updated on the redirect
					if ($numberitemsupdated > 0)
					{
						GroupNamespace::setSaveStatus(GroupNamespace::SAVE_STATE_SUCCESS);
						
						$group->clearRecords(TRUE);
						$group->clearReportConservators(TRUE);
						$group->clearFinalCounts(TRUE);
						$group->clearImportances(TRUE);
						
						//Go back to the record screen.
						$this->_helper->redirector('index', 'group', NULL, array('groupnumber' => $group->getPrimaryKey()));
					}
					else 
					{
						$this->displayGroupErrors($formData, ACORNConstants::MESSAGE_PROBLEM_SAVING);
					}
				}
				//TODO otherwise display an error message regarding the save
				else 
				{
						$this->displayGroupErrors($formData, "This Group has not been created.  Please use the 'Create New Records' option.");
				}
			}
			else
			{
				$this->displayGroupErrors($formData, ACORNConstants::MESSAGE_INVALID_FORM);
			}
		}
		else
		{
			//If the url was typed directly, go to the DE screen.
			$this->_helper->redirector('newgroup', 'group');
		}
    }
    
	/*
     * Display the errors on the form
     */
	private function displayGroupErrors(array $formData, $message)
    {
    	$form = $this->getGroupForm();
		//Populate the data with what the user
		//gave but also display the error messages.
		$form->populate($formData);
		$this->view->form = $form;

		$group = GroupNamespace::getCurrentGroup();
		$groupnumberlabel = "Group " . $group->getPrimaryKey();
		$createdate = "";
		$createdby = "";
		$audittrailitems = $group->getAuditTrail("Insert");
		if (!empty($audittrailitems))
		{
			$audittrailitem = current($audittrailitems);
			$createdate = $audittrailitem->getDate();
			$createdby = $audittrailitem->getPerson()->getDisplayName();
		}
		$this->view->placeholder("reporterrormessage")->set($message);
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('groupnumber')->set($groupnumberlabel);
		
		$this->renderScript('group/index.phtml');
    }
    
	/*
     * Only update those values that are populated
     */
	private function updateItemReport(Report $report, array $formvalues, Group $group)
    {
    	if (isset($formvalues['adminonlycheckbox']))
    	{
    		$report->setAdminOnly($formvalues['adminonlycheckbox']);
    	}
    	if (isset($formvalues['customhousingonlycheckbox']))
    	{
    		$report->setCustomHousingOnly($formvalues['customhousingonlycheckbox']);
    	}
    	
    	$dims = new Dimensions();
    	if (isset($formvalues['reportheightinput']))
    	{
    		$dims->setHeight($formvalues['reportheightinput']);
    	}
    	if (isset($formvalues['reportthicknessinput']))
    	{
    		$dims->setThickness($formvalues['reportthicknessinput']);
    	}
    	if (isset($formvalues['reportwidthinput']))
    	{
    		$dims->setWidth($formvalues['reportwidthinput']);
    	}
    	if (isset($formvalues['reportunitselect']))
    	{
    		$dims->setUnit($formvalues['reportunitselect']);
    	}
    	$report->setDimensions($dims);
    	
    	
    	if (isset($formvalues['examonlycheckbox']))
    	{
    		$report->setExamOnly($formvalues['examonlycheckbox']);
    	}
    	if (isset($formvalues['reportformatselect']))
    	{
    		$report->setFormat($formvalues['reportformatselect']);
    	}
    	if (isset($formvalues['additionalmaterialsonfilecheckbox']))
    	{
    		$report->setAdditionalMaterialsOnFile($formvalues['additionalmaterialsonfilecheckbox']);
    	}
    	
    	if (isset($formvalues['presrectextarea']))
    	{
    		$report->setPreservationRecommendations($formvalues['presrectextarea']);
    	}
    	if (isset($formvalues['reportbyselect']))
    	{
    		$report->setReportBy($formvalues['reportbyselect']);
    	}
    	//If this is a new report, set the person to the current user.
    	elseif (is_null($report->getPrimaryKey()))
    	{
    		$auth = Zend_Auth::getInstance();
    		$identity = $auth->getIdentity();
    		$report->setReportBy($identity[PeopleDAO::PERSON_ID]);
    	}
    	if (isset($formvalues['reportdateinput']))
    	{
    		$report->setReportDate($formvalues['reportdateinput']);
    	}
    	//If this is a new report, set the date to the current date.
    	elseif (is_null($report->getPrimaryKey()))
    	{
    		$zenddate = new Zend_Date();
			$report->setReportDate($zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT));
    	}
    	if (isset($formvalues['reporttreatmentsummarytextarea']))
    	{
    		$report->setSummary($formvalues['reporttreatmentsummarytextarea']);
    	}
    	if (isset($formvalues['reporttreatmenttextarea']))
    	{
    		$report->setTreatment($formvalues['reporttreatmenttextarea']);
    	}
    	if (isset($formvalues['worklocationselect']))
    	{
    		$report->setWorkLocation($formvalues['worklocationselect']);
    	}
    	
    	$groupreportconservators = $group->getReportConservators();
    	if (!empty($groupreportconservators))
    	{
    		$report->setReportConservators($groupreportconservators);
    	}
    	
    	$groupimportances = $group->getImportances();
    	if (!empty($groupimportances))
    	{
    		$report->setImportances($groupimportances);
    	}
    	
    	if (!$group->getFinalCounts()->isEmpty() && !$report->getCounts()->equals($group->getFinalCounts()))
    	{
    		$report->setCounts($group->getFinalCounts());
    	}
    	return $report;
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

	/*
     * Clears out the group report counts and conservators
     * since the form reset won't touch those.
     */
	public function resetreportAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$group = GroupNamespace::getCurrentGroup();
    	$group->clearFinalCounts(TRUE);
    	$group->clearReportConservators(TRUE);
    }
}
?>

