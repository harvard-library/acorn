<?php

/**
 * ReportsController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class ReportsController extends Zend_Controller_Action
{
	private $reportParameterForm;
	
	private function getReportParameterForm()
	{
		if (is_null($this->reportParameterForm))
		{
			$this->reportParameterForm = new ACORNReportParametersForm();
		}
		return $this->reportParameterForm;
	}
	
	/**
	 * Transfers the current item.  The type of transfer is based on the current tab.
	 */
    public function transferreportAction() 
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		switch(RecordNamespace::getRenderTab())
		{
			case RecordNamespace::LOGIN:
				$transferlist = TransferNamespace::getTransferList(TransferNamespace::LOGIN);
				break;
			case RecordNamespace::LOGOUT:
				$transferlist = TransferNamespace::getTransferList(TransferNamespace::LOGOUT);
				break;
			case RecordNamespace::TEMP_TRANSFER:
				$transferlist = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER);
				break;
			case RecordNamespace::TEMP_TRANSFER_RETURN:
				$transferlist = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER_RETURN);
				break;
			default:
				$transferlist = array();
				break;
		}
		
		$this->transfer($transferlist, RecordNamespace::getRenderTab());
    }
    
	/**
	 * Transfers the current items.  The type of transfer is based on the current tab.
	 */
    public function grouptransferreportAction() 
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		switch(GroupNamespace::getRenderTab())
		{
			case GroupNamespace::LOGIN:
				$transferlist = TransferNamespace::getTransferList(TransferNamespace::LOGIN);
				break;
			case GroupNamespace::LOGOUT:
				$transferlist = TransferNamespace::getTransferList(TransferNamespace::LOGOUT);
				break;
			case GroupNamespace::TEMP_TRANSFER:
				$transferlist = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER);
				break;
			case GroupNamespace::TEMP_TRANSFER_RETURN:
				$transferlist = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER_RETURN);
				break;
			default:
				$transferlist = array();
				break;
		}
		
		$this->transfer($transferlist, GroupNamespace::getRenderTab());
    }
    
	/**
	 * Transfers the current items from the bulk login screen
	 */
    public function bulklogintransferreportAction() 
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$transferlist = TransferNamespace::getTransferList(TransferNamespace::LOGIN);

		$this->transfer($transferlist, RecordNamespace::LOGIN);
    }
    
	/**
	 * Transfers the current items from the bulk logout screen
	 */
    public function bulklogouttransferreportAction() 
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$transferlist = TransferNamespace::getTransferList(TransferNamespace::LOGOUT);

		$this->transfer($transferlist, RecordNamespace::LOGOUT);
    }
    
    private function transfer($transferlist, $transferType)
    {
    	$courier = $this->getRequest()->getParam('courierinput');
		$courier2 = $this->getRequest()->getParam('courier2input', '');
		
		$transferOptions = array(
			TransferFormReport::COURIER => $courier,
			TransferFormReport::SECOND_COURIER => $courier2,
			TransferFormReport::TRANSPORT_CONTAINER => $this->getRequest()->getParam('transportcontainertextarea'),
			TransferFormReport::TRANSFER_LIST => $transferlist
		);
		
		$report = new TransferFormReport($transferType, $transferOptions);
		echo $report->drawReport();	
    }
    
	/**
	 * Generate a copy of the item proposal
	 */
    public function itemproposalformAction() 
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$item = RecordNamespace::getCurrentItem();
		$report = new ProposalFormReport($item);
		echo $report->drawReport();
		
    }
    
	/**
	 * Generate a copy of the item report
	 */
    public function itemreportformAction() 
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$item = RecordNamespace::getCurrentItem();
		$report = new ReportFormReport($item);
		echo $report->drawReport();
		
    }
    
	/**
	 * Generate a copy of the group proposal
	 */
    public function groupproposalformAction() 
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$group = GroupNamespace::getCurrentGroup();
		$report = new ProposalFormReport($group);
		echo $report->drawReport();
		
    }
    
	/**
	 * Generate a copy of the group report
	 */
    public function groupreportformAction() 
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$group = GroupNamespace::getCurrentGroup();
		$report = new ReportFormReport($group);
		echo $report->drawReport();
		
    }
    
	/**
	 * Generate a copy of the OSW report
	 */
    public function oswformAction() 
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$osw = RecordNamespace::getCurrentOSW();
		$report = new OSWFormReport($osw);
		echo $report->drawReport();
		
    }
    
	/**
	 * Generate a copy of the item status
	 */
    public function statusreportformAction() 
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$item = RecordNamespace::getCurrentItem();
		$report = new StatusFormReport($item);
		echo $report->drawReport();
		
    }
    
    /*
     * Data Entry form for this report.
     */
    public function completedrepositoryAction()
    {
    	$form = $this->getReportParameterForm();
    	$this->view->form = $form;
    }
    
	/*
     * Generate the report.
     */
    public function completedrepositoryreportAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		
		$form = $this->getReportParameterForm();
		$formData = $this->getRequest()->getParams();
		
		if (!isset($formData['enddateinput']))
		{
			$formData['enddateinput'] = NULL;
		}
	
    	if ($form->isValidPartial($formData))
    	{
    		//Don't use the default layout since this isn't a view call
			$this->_helper->layout->disableLayout();
    		$values = $form->getValues();
    		$repository = LocationDAO::getLocationDAO()->getLocation($values['locationselect']);
    		$dateRange = new DateRange();
    		$dateRange->setStartDate($values['begindateinput']);
    		$dateRange->setEndDate($values['enddateinput']);
    		
    		$report = new CompletedByRepositoryReport($repository, $dateRange);
    		$filename = $report->drawReport();
    		$retval = Zend_Json::encode(array('Filename' => $filename));
			
			//Make sure JS knows it is in JSON format.
			$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    	}
    	else
		{
			$this->displayReportErrors($form, $formData, ACORNConstants::MESSAGE_INVALID_FORM, 'completedrepository');
		}	
    }
    
	/*
     * Data Entry form for this report.
     */
    public function combinedcompletedrepositoryAction()
    {
    	$form = $this->getReportParameterForm();
    	$this->view->form = $form;
    }
    
	/*
     * Generate the report.
     */
    public function combinedcompletedrepositoryreportAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		
		$form = $this->getReportParameterForm();
		$formData = $this->getRequest()->getParams();
		
		if (!isset($formData['enddateinput']))
		{
			$formData['enddateinput'] = NULL;
		}
	
    	if ($form->isValidPartial($formData))
    	{
    		//Don't use the default layout since this isn't a view call
			$this->_helper->layout->disableLayout();
    		$values = $form->getValues();
    		$repository = LocationDAO::getLocationDAO()->getLocation($values['locationselect']);
    		$dateRange = new DateRange();
    		$dateRange->setStartDate($values['begindateinput']);
    		$dateRange->setEndDate($values['enddateinput']);
    		
    		$report = new CombinedCompletedByRepositoryReport($repository, $dateRange);
    		$filename = $report->drawReport();
    		$retval = Zend_Json::encode(array('Filename' => $filename));
			
			//Make sure JS knows it is in JSON format.
			$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    	}
    	else
		{
			$this->displayReportErrors($form, $formData, ACORNConstants::MESSAGE_INVALID_FORM, 'combinedcompletedrepository');
		}	
    }
    
	/*
     * Data Entry form for this report.
     */
    public function completedpersonAction()
    {
    	$form = $this->getReportParameterForm();
    	$this->view->form = $form;
    }
    
	/*
     * Generate the report.
     */
    public function completedpersonreportAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		
		$form = $this->getReportParameterForm();
		$formData = $this->getRequest()->getParams();
		
		if (!isset($formData['enddateinput']))
		{
			$formData['enddateinput'] = NULL;
		}
	
    	if ($form->isValidPartial($formData))
    	{
    		//Don't use the default layout since this isn't a view call
			$this->_helper->layout->disableLayout();
    		$values = $form->getValues();
    		Logger::log($values);
    		$person = PeopleDAO::getPeopleDAO()->getPerson($values['personselect']);
    		$dateRange = new DateRange();
    		$dateRange->setStartDate($values['begindateinput']);
    		$dateRange->setEndDate($values['enddateinput']);
    		
    		$report = new CompletedByPersonReport($person, $dateRange);
    		$filename = $report->drawReport();
    		$retval = Zend_Json::encode(array('Filename' => $filename));
			
			//Make sure JS knows it is in JSON format.
			$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    	}
    	else
		{
			$this->displayReportErrors($form, $formData, ACORNConstants::MESSAGE_INVALID_FORM, 'completedperson');
		}	
    }
    
	/*
     * Data Entry form for this report.
     */
    public function completedpersonwithhoursAction()
    {
    	$form = $this->getReportParameterForm();
    	$this->view->form = $form;
    }
    
	/*
     * Generate the report.
     */
    public function completedpersonwithhoursreportAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		
		$form = $this->getReportParameterForm();
		$formData = $this->getRequest()->getParams();
		
		if (!isset($formData['enddateinput']))
		{
			$formData['enddateinput'] = NULL;
		}
	
    	if ($form->isValidPartial($formData))
    	{
    		//Don't use the default layout since this isn't a view call
			$this->_helper->layout->disableLayout();
    		$values = $form->getValues();
    		$jsonpeoplelist = $formData['peoplelist'];
    		$peoplelist = Zend_Json::decode($jsonpeoplelist);
    		$people = array();
    		foreach ($peoplelist as $personid)
    		{
    			$person = PeopleDAO::getPeopleDAO()->getPerson($personid);
    			$people[$personid] = $person;
    		}
    		//$person = PeopleDAO::getPeopleDAO()->getPerson($values['personselect']);
    		$dateRange = new DateRange();
    		$dateRange->setStartDate($values['begindateinput']);
    		$dateRange->setEndDate($values['enddateinput']);
    		
    		$report = new CompletedByPersonWithHoursReport($people, $dateRange);
    		$filename = $report->drawReport();
    		$retval = Zend_Json::encode(array('Filename' => $filename));
			
			//Make sure JS knows it is in JSON format.
			$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    	}
    	else
		{
			$this->displayReportErrors($form, $formData, ACORNConstants::MESSAGE_INVALID_FORM, 'completedpersonwithhours');
		}	
    }
    
	/*
     * Data Entry form for this report.
     */
    public function completedprojectAction()
    {
    	$form = $this->getReportParameterForm();
    	$this->view->form = $form;
    }
    
	/*
     * Generate the report.
     */
    public function completedprojectreportAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		
		$form = $this->getReportParameterForm();
		$formData = $this->getRequest()->getParams();
		
		if (!isset($formData['enddateinput']))
		{
			$formData['enddateinput'] = NULL;
		}
	
    	if ($form->isValidPartial($formData))
    	{
    		//Don't use the default layout since this isn't a view call
			$this->_helper->layout->disableLayout();
    		$values = $form->getValues();
    		$project = ProjectDAO::getProjectDAO()->getProject($values['projectselect']);
    		$dateRange = new DateRange();
    		$dateRange->setStartDate($values['begindateinput']);
    		$dateRange->setEndDate($values['enddateinput']);
    		
    		$report = new CompletedByProjectReport($project, $dateRange);
    		$filename = $report->drawReport();
    		$retval = Zend_Json::encode(array('Filename' => $filename));
			
			//Make sure JS knows it is in JSON format.
			$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    	}
    	else
		{
			$this->displayReportErrors($form, $formData, ACORNConstants::MESSAGE_INVALID_FORM, 'completedproject');
		}	
    }
    
	/*
     * Data Entry form for this report.
     */
    public function completedtubworkAction()
    {
    	$form = $this->getReportParameterForm();
    	$this->view->form = $form;
    }
    
	/*
     * Generate the report.
     */
    public function completedtubworkreportAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		
		$form = $this->getReportParameterForm();
		$formData = $this->getRequest()->getParams();
		
		if (!isset($formData['enddateinput']))
		{
			$formData['enddateinput'] = NULL;
		}
	
    	if ($form->isValidPartial($formData))
    	{
    		//Don't use the default layout since this isn't a view call
			$this->_helper->layout->disableLayout();
    		$values = $form->getValues();
    		$dateRange = new DateRange();
    		$dateRange->setStartDate($values['begindateinput']);
    		$dateRange->setEndDate($values['enddateinput']);
    		
    		$report = new CompletedWorkByTubReport($dateRange);
    		$filename = $report->drawReport();
    		$retval = Zend_Json::encode(array('Filename' => $filename));
			
			//Make sure JS knows it is in JSON format.
			$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    	}
    	else
		{
			$this->displayReportErrors($form, $formData, ACORNConstants::MESSAGE_INVALID_FORM, 'completedtubwork');
		}	
    }
    
	/*
     * Data Entry form for this report.
     */
    public function completedtubhoursAction()
    {
    	$form = $this->getReportParameterForm();
    	$this->view->form = $form;
    }
    
	/*
     * Generate the report.
     */
    public function completedtubhoursreportAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		
		$form = $this->getReportParameterForm();
		$formData = $this->getRequest()->getParams();
		
		if (!isset($formData['enddateinput']))
		{
			$formData['enddateinput'] = NULL;
		}
	
    	if ($form->isValidPartial($formData))
    	{
    		//Don't use the default layout since this isn't a view call
			$this->_helper->layout->disableLayout();
    		$values = $form->getValues();
    		$dateRange = new DateRange();
    		$dateRange->setStartDate($values['begindateinput']);
    		$dateRange->setEndDate($values['enddateinput']);
    		
    		$report = new CompletedHoursByTubReport($dateRange);
    		$filename = $report->drawReport();
    		$retval = Zend_Json::encode(array('Filename' => $filename));
			
			//Make sure JS knows it is in JSON format.
			$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    	}
    	else
		{
			$this->displayReportErrors($form, $formData, ACORNConstants::MESSAGE_INVALID_FORM, 'completedtubhours');
		}	
    }
    
	/*
     * Data Entry form for this report.
     */
    public function arlforwpcAction()
    {
    	$form = $this->getReportParameterForm();
    	$this->view->form = $form;
    }
    
	/*
     * Generate the report.
     */
    public function arlforwpcreportAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		
		$form = $this->getReportParameterForm();
		$formData = $this->getRequest()->getParams();
		
		if (!isset($formData['enddateinput']))
		{
			$formData['enddateinput'] = NULL;
		}
	
    	if ($form->isValidPartial($formData))
    	{
    		//Don't use the default layout since this isn't a view call
			$this->_helper->layout->disableLayout();
    		$values = $form->getValues();
    		$dateRange = new DateRange();
    		$dateRange->setStartDate($values['begindateinput']);
    		$dateRange->setEndDate($values['enddateinput']);
    		
    		$report = new ARLStatisticsReport($dateRange);
    		$filename = $report->drawReport();
    		$retval = Zend_Json::encode(array('Filename' => $filename));
			
			//Make sure JS knows it is in JSON format.
			$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    	}
    	else
		{
			$this->displayReportErrors($form, $formData, ACORNConstants::MESSAGE_INVALID_FORM, 'arlforwpc');
		}	
    }
    
	/*
     * Data Entry form for this report.
     */
    public function arlforrepositoryAction()
    {
    	$form = $this->getReportParameterForm();
    	$this->view->form = $form;
    }
    
	/*
     * Generate the report.
     */
    public function arlforrepositoryreportAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		
		$form = $this->getReportParameterForm();
		$formData = $this->getRequest()->getParams();
		
			
		if (!isset($formData['enddateinput']))
		{
			$formData['enddateinput'] = NULL;
		}
	
    	if ($form->isValidPartial($formData))
    	{
    		//Don't use the default layout since this isn't a view call
			$this->_helper->layout->disableLayout();
    		$values = $form->getValues();
    		$repository = LocationDAO::getLocationDAO()->getLocation($values['locationselect']);
    		$dateRange = new DateRange();
    		$dateRange->setStartDate($values['begindateinput']);
    		$dateRange->setEndDate($values['enddateinput']);
    		
    		$report = new ARLStatisticsReport($dateRange, $repository);
    		$filename = $report->drawReport();
    		$retval = Zend_Json::encode(array('Filename' => $filename));
			
			//Make sure JS knows it is in JSON format.
			$this->getResponse()->setHeader('Content-Type', 'application/json')
							->setBody($retval);
    	}
    	else
		{
			$this->displayReportErrors($form, $formData, ACORNConstants::MESSAGE_INVALID_FORM, 'arlforwpc');
		}	
    }
    
    /*
     * Data Entry form for this report.
    */
    public function workdonebyAction()
    {
    	$form = $this->getReportParameterForm();
    	$this->view->form = $form;
    }
    
    /*
     * Generate the report.
    */
    public function workdonebyreportAction()
    {
    	//Don't display a new view
    	$this->_helper->viewRenderer->setNoRender();
    
    	$form = $this->getReportParameterForm();
    	$formData = $this->getRequest()->getParams();
 
    	if (!isset($formData['enddateinput']))
    	{
    		$formData['enddateinput'] = NULL;
    	}
    
    	if ($form->isValidPartial($formData))
    	{
    		//Don't use the default layout since this isn't a view call
    		$this->_helper->layout->disableLayout();
    		$values = $form->getValues();
    		$dateRange = new DateRange();
    		$dateRange->setStartDate($values['begindateinput']);
    		$dateRange->setEndDate($values['enddateinput']);
    
    		$report = new WorkDoneByCSVReport($dateRange);
    		$filename = $report->generateReport();
    		$retval = Zend_Json::encode(array('Filename' => $filename));
    			
    		//Make sure JS knows it is in JSON format.
    		$this->getResponse()->setHeader('Content-Type', 'application/json')
    		->setBody($retval);
    	}
    	else
    	{
    		$this->displayReportErrors($form, $formData, ACORNConstants::MESSAGE_INVALID_FORM, 'completedperson');
    	}
    }
    
    private function displayReportErrors(ACORNForm $form, array $formData, $message, $viewtorender)
    {
    	//Populate the data with what the user
		//gave but also display the error messages.
		$form->populate($formData);
		$this->view->form = $form;

		$this->view->placeholder('reporterrormessage')->set($message);
    	
		$this->render($viewtorender);
    }
}
?>