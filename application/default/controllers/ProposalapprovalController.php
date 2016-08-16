<?php

/**
 * ProposalapprovalController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class ProposalapprovalController extends Zend_Controller_Action
{
	private $proposalApprovalForm;
	private $emailForm;
	private $acornRedirector;
	
	private function getEmailForm()
	{
		if (is_null($this->emailForm))
		{
			$this->emailForm = new EmailProposalForm();
		}
		return $this->emailForm;
	}
	
	private function getProposalApprovalForm()
	{
		if (is_null($this->proposalApprovalForm))
		{
			$this->proposalApprovalForm = new ProposalApprovalForm();
		}
		return $this->proposalApprovalForm;
	}
	
	private function getAcornRedirector()
	{
		if (is_null($this->acornRedirector))
		{
			$this->acornRedirector = new AcornRedirector();
		}
		return $this->acornRedirector;
	}

    /*
     * Determine if the given action is permitted for the given access level.
     * 
     * @param mixed - action name
     * @param mixed - access level.
     */
    private function actionPermitted($action, $accessLevel)
    {
    	if ($action == 'email' || $action == 'sendemail')
    	{
    		return $accessLevel != PeopleDAO::ACCESS_LEVEL_CURATOR;
    	}
    	return TRUE;
   	}
    
	public function indexAction()
    {
    	$recordnum = $this->getRequest()->getParam('recordnumber');
    	$readonly = $this->getRequest()->getParam('readonly', 0);
    	if (isset($recordnum))
    	{
    		$item = ItemDAO::getItemDAO()->getItem($recordnum);	
    		RecordNamespace::setCurrentItem($item);
    	}
    	else 
    	{
    		$item = RecordNamespace::getCurrentItem();	
    	}
    	
    	if (isset($item) && !is_null($item))
    	{
    		//Set the approval item specifically for 
    		//login purposes.
    		AcornNamespace::setRedirectAction('index');
    		AcornNamespace::setRedirectController('proposalapproval');
    		AcornNamespace::setRedirectParams(array('recordnumber' => $item->getItemID(), 'readonly' => $readonly));
    		$form = $this->getProposalApprovalForm();
    	
    		$data = $this->buildDataArray($readonly);
    		$form->populate($data);
    		
    		$this->view->form = $form;
    		$this->view->readonly = $readonly;
    		
    		$this->addSummaryInfo($item);
    		$this->addReportSummaryInfo($item);
		
    	}
    	else 
    	{
    		$this->_helper->redirector('index', 'index');
    	}
    }
    
    /**
     * Builds the data array for the forms
     * 
     * @return array
     */
    private function buildDataArray($readonly = 0)
    {
    	$currentItem = RecordNamespace::getCurrentItem();
    	//Submitted by
		if (Zend_Auth::getInstance()->hasIdentity())
    	{
			$identity = Zend_Auth::getInstance()->getIdentity();
			$submittedby = PeopleDAO::getPeopleDAO()->getPerson($identity[PeopleDAO::PERSON_ID]);
			$submittedbyemail = $submittedby->getEmailAddress();
		}
    	else 
    	{
    		$submittedby = '';
    		$submittedbyemail = '';
    	}
		//Approving Curator
		$approvingcurator = PeopleDAO::getPeopleDAO()->getPerson($currentItem->getApprovingCuratorID());
		$approvingcuratoremail = $approvingcurator->getEmailAddress();
		//Curator
		$curator = PeopleDAO::getPeopleDAO()->getPerson($currentItem->getCuratorID());
		$curatoremail = $curator->getEmailAddress();
		//Coordinator
		$coordinator = PeopleDAO::getPeopleDAO()->getPerson($currentItem->getCoordinatorID());
		$coordinatoremail = $coordinator->getEmailAddress();
		//Proposal by
		$propbyarray = $currentItem->getFunctions()->getProposal()->getProposedBy();
		$propbyemail = "";
		$delimiter = "; ";
		
		$ccs = array();
		foreach($propbyarray as $propbyperson)
		{
			$email = $propbyperson->getEmailAddress();
			if (!empty($email))
			{
				$ccs[$email] = $email;
				//$propbyemail .= $delimiter . $email;
				//$delimiter = ", ";
			}
		}
		
		$approvalemail = "";
		if (!empty($submittedbyemail) && !empty($approvingcuratoremail) && $submittedbyemail != $approvingcuratoremail)
		{
			$approvalemail = $approvingcuratoremail;
		}
		elseif (!empty($approvingcuratoremail))
		{
			$approvalemail = $approvingcuratoremail;
		}
		elseif (!empty($submittedbyemail))
		{
			$approvalemail = $submittedbyemail;
		}
		
		//Add the ccs (they will overwrite each other if they are the same person
		if (!empty($coordinatoremail))
		{
			$ccs[$coordinatoremail] = $coordinatoremail;
		}
		//if (!empty($propbyemail))
		//{
		//	$ccs[$propbyemail] = $propbyemail;
		//}
		if (!empty($approvingcuratoremail))
		{
			$ccs[$approvingcuratoremail] = $approvingcuratoremail;
		}
		if (!empty($curatoremail))
		{
			$ccs[$curatoremail] = $curatoremail;
		}
		if (!empty($submittedbyemail))
		{
			$ccs[$submittedbyemail] = $submittedbyemail;
		}
		
		unset($ccs[$approvingcuratoremail]);
		
		array_unique($ccs);
		
		$ccstring = implode(",", $ccs);
		
		$retval = array(
			'summarytoinput' => $approvalemail,
			'summaryccinput' => $ccstring,
			'historytoinput' => $approvalemail,
			'historyccinput' => $ccstring
		);
		
		if ($readonly == 1)
		{
			$retval['summaryauthorselect'] = $currentItem->getApprovingCuratorID();
			$retval['historyauthorselect'] = $currentItem->getApprovingCuratorID();
		}
		return $retval;
		
    }
    
    private function addSummaryInfo(Item $item)
    {
    	$proposal = $item->getFunctions()->getProposal();
   		$callnumberlist = $item->getCallNumbers();
		$callnumbers = implode(', ', $callnumberlist);
		$repository = LocationDAO::getLocationDAO()->getLocation($item->getHomeLocationID())->getLocation();
   		$curator = PeopleDAO::getPeopleDAO()->getPerson($item->getCuratorID())->getDisplayName();
   		$approvingcurator = PeopleDAO::getPeopleDAO()->getPerson($item->getApprovingCuratorID())->getDisplayName();
   		$author = $item->getAuthorArtist();
   		$project = "";
   		if (!is_null($item->getProjectID()))
		{
			$project = ProjectDAO::getProjectDAO()->getProject($item->getProjectID())->getProjectName();
		}
		$title = $item->getTitle();
		$group = "";
   		if (!is_null($item->getGroupID()))
		{
			$group = GroupDAO::getGroupDAO()->getGroup($item->getGroupID())->getGroupName();
		}
		$coordinator = PeopleDAO::getPeopleDAO()->getPerson($item->getCoordinatorID())->getDisplayName();
		$counts = $item->getInitialCounts()->toString(TRUE);
		$dateofobject = $item->getDateOfObject();
		$dimensions = $proposal->getDimensions()->getReportDimensions();
		$description = $proposal->getDescription();
		$description = str_replace("\n", "<br/>", $description);
		$condition = $proposal->getCondition();
		$condition = str_replace("\n", "<br/>", $condition);
		$treatment = $proposal->getTreatment();
		$treatment = str_replace("\n", "<br/>", $treatment);
		
		$this->view->placeholder('recordnumber')->set("Proposal Approval & Report Info for Record# " . $item->getItemID());
   		$this->view->placeholder('recordinfo')->set($item->getInfoForLabel());
   		$this->view->placeholder('callnumbers')->set($callnumbers);
   		$this->view->placeholder('repository')->set($repository);
   		$this->view->placeholder('curator')->set($curator);
   		$this->view->placeholder('approvingcurator')->set($approvingcurator);
   		$this->view->placeholder('author')->set($author);
   		$this->view->placeholder('project')->set($project);
   		$this->view->placeholder('itemtitle')->set($title);
   		$this->view->placeholder('group')->set($group);
   		$this->view->placeholder('coordinator')->set($coordinator);
   		$this->view->placeholder('counts')->set($counts);
   		$this->view->placeholder('dateofobject')->set($dateofobject);
   		$this->view->placeholder('size')->set($dimensions);
   		$this->view->placeholder('comments')->set($item->getComments());
   		$this->view->placeholder('description')->set($description);
   		$this->view->placeholder('condition')->set($condition);
   		$this->view->placeholder('treatment')->set($treatment);
   		$proposalinfo = "Proposed By: " . $proposal->getProposedByString() . "<br/>Proposed Hours: " . $proposal->getHoursAsString();
   		$this->view->placeholder('proposedhours')->set($proposalinfo);
    }
    
	private function addReportSummaryInfo(Item $item)
    {
    	$report = $item->getFunctions()->getReport();
    	if (!is_null($report->getPrimaryKey()))
    	{
	   		$callnumberlist = $item->getCallNumbers();
			$callnumbers = implode(', ', $callnumberlist);
			$repository = LocationDAO::getLocationDAO()->getLocation($item->getHomeLocationID())->getLocation();
	   		$curator = PeopleDAO::getPeopleDAO()->getPerson($item->getCuratorID())->getDisplayName();
	   		$author = $item->getAuthorArtist();
	   		$project = "";
	   		if (!is_null($item->getProjectID()))
			{
				$project = ProjectDAO::getProjectDAO()->getProject($item->getProjectID())->getProjectName();
			}
			$title = $item->getTitle();
			$group = "";
	   		if (!is_null($item->getGroupID()))
			{
				$group = GroupDAO::getGroupDAO()->getGroup($item->getGroupID())->getGroupName();
			}
			$coordinator = PeopleDAO::getPeopleDAO()->getPerson($item->getCoordinatorID())->getDisplayName();
			$counts = $report->getCounts()->toString(TRUE);
			$dateofobject = $item->getDateOfObject();
			$dimensions = $report->getDimensions()->getReportDimensions();
			$treatment = $report->getTreatment();
			$treatment = str_replace("\n", "<br/>", $treatment);
			
			$this->view->placeholder('reportcallnumbers')->set($callnumbers);
	   		$this->view->placeholder('reportrepository')->set($repository);
	   		$this->view->placeholder('reportcurator')->set($curator);
	   		$this->view->placeholder('reportauthor')->set($author);
	   		$this->view->placeholder('reportproject')->set($project);
	   		$this->view->placeholder('reportitemtitle')->set($title);
	   		$this->view->placeholder('reportgroup')->set($group);
	   		$this->view->placeholder('reportcoordinator')->set($coordinator);
	   		$this->view->placeholder('reportcounts')->set($counts);
	   		$this->view->placeholder('reportdateofobject')->set($dateofobject);
	   		$this->view->placeholder('reportsize')->set($dimensions);
	   		$this->view->placeholder('reportcomments')->set($item->getComments());
	   		$this->view->placeholder('preservationrecommendations')->set($item->getFunctions()->getReport()->getPreservationRecommendations());
	   		$this->view->placeholder('reporttreatment')->set($treatment);
	   		$zenddate = new Zend_Date($report->getReportDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    		$reportdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    		$reportinfo = "Report Date: " . $reportdate . "<br/>Report By: " . PeopleDAO::getPeopleDAO()->getPerson($report->getReportBy())->getDisplayName() . "<br/>Completed Hours: " . $report->getTotalHours();
	   		$this->view->placeholder('completedhours')->set($reportinfo);
    	}
    }
    
	/*
	 * Returns the total counts
	 */
	private function getCounts(Item $item)
	{
		$counts = $item->getInitialCounts();
		$vol = $counts->getVolumeCount();
		$sheet = $counts->getSheetCount();
		$photo = $counts->getPhotoCount();
		$other = $counts->getOtherCount();
		$box = $counts->getBoxCount();
		$countstring = $vol == 1 ? $vol . ' volume, ' : $vol . ' volumes, ';
		$countstring .= $sheet == 1 ? $sheet . ' sheet, ' : $sheet . ' sheets, ';
		$countstring .= $photo == 1 ? $photo . ' photo, ' : $photo . ' photos, ';
		$countstring .= $box == 1 ? $box . ' box, ' : $box . ' boxes, ';
		$countstring .= $other . ' other';
		return $countstring;
	}
    
	public function saveapprovalAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getProposalApprovalForm()->getSubForm("proposalsummaryform");
			$formData = $this->_request->getPost();
	
			if ($form->isValid($formData))
			{
				//Save the data
				$historyid = $this->saveProposalApproval($form->getValues());
				if (!is_null($historyid))
				{
					$historyitem = ProposalApprovalDAO::getProposalApprovalDAO()->getHistoryItem($historyid);
					$item = RecordNamespace::getCurrentItem();
					$item->getFunctions()->getProposal()->getProposalApproval()->addHistoryItem($historyitem);
					RecordNamespace::setCurrentItem($item);
					$this->emailChangeNotice($item, $form->getValue('hiddenapprovaltype'), $form->getValue('summarytoinput'), $form->getValue('summaryccinput'), $form->getValue('approvalcommentstextarea'));
					
					$origitem = ItemDAO::getItemDAO()->getItem($item->getItemID());
					RecordNamespace::setOriginalItem($origitem);
					
					RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);
					
					$redirectparams = AcornNamespace::getRedirectParams();
					if (isset($redirectparams))
					{
						$redirectparams['recordnumber'] = $item->getItemID();
					}
					$this->_helper->redirector('index', 'proposalapproval', NULL, $redirectparams);
				}
				//otherwise display an error message regarding the save
				else 
				{
					$this->displayErrors($this->getProposalApprovalForm(), 'summaryerrormessage', $formData, 'There was a problem saving your data.  Please try again.');
				}
			}
			else
			{
				$this->displayErrors($this->getProposalApprovalForm(), 'summaryerrormessage', $formData, 'Changes were not saved.  Please correct the errors in the form below.');
			}
		}
		else
		{
			//If the url was typed directly, go to the approval screen.
			$this->_helper->redirector('index');
		}
    }
    
	public function savecommentAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getProposalApprovalForm()->getSubForm("historyform");
			$formData = $this->_request->getPost();
	
			if ($form->isValid($formData))
			{
				//Save the data
				$historyid = $this->saveComment($form->getValues());
				if (!is_null($historyid))
				{
					$historyitem = ProposalApprovalDAO::getProposalApprovalDAO()->getHistoryItem($historyid);
					$item = RecordNamespace::getCurrentItem();
					$item->getFunctions()->getProposal()->getProposalApproval()->addHistoryItem($historyitem);
					RecordNamespace::setCurrentItem($item);
					$this->emailChangeNotice($item, 'Comment', $form->getValue('historytoinput'), $form->getValue('historyccinput'), $form->getValue('historycommentstextarea'));
					
					$origitem = ItemDAO::getItemDAO()->getItem($item->getItemID());
					RecordNamespace::setOriginalItem($origitem);
					
					RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);
					
					$redirectparams = AcornNamespace::getRedirectParams();
					if (isset($redirectparams))
					{
						$redirectparams['recordnumber'] = $item->getItemID();
						$lastparam = end($redirectparams);
						$key = array_search($lastparam, $redirectparams);
						$lastparam .= '#tabview=tab1';
						$redirectparams[$key] = $lastparam;
						
					}
					$this->getAcornRedirector()->setGoto('index', 'proposalapproval', NULL, $redirectparams);
				}
				//otherwise display an error message regarding the save
				else 
				{
					$this->displayErrors($this->getProposalApprovalForm(), 'historyerrormessage', $formData, 'There was a problem saving your data.  Please try again.');
				}
			}
			else
			{
				$this->displayErrors($this->getProposalApprovalForm(), 'historyerrormessage', $formData, 'Changes were not saved.  Please correct the errors in the form below.');
			}
		}
		else
		{
			//If the url was typed directly, go to the approval screen.
			$this->_helper->redirector('index');
		}
    }
    
    private function emailChangeNotice(Item $currentItem, $activitytype, $email, $ccstring, $comments = NULL)
    {
    	//Submitted by
    	if (Zend_Auth::getInstance()->hasIdentity())
    	{
			$identity = Zend_Auth::getInstance()->getIdentity();
			$submittedby = PeopleDAO::getPeopleDAO()->getPerson($identity[PeopleDAO::PERSON_ID]);
			$submittedbyemail = $submittedby->getEmailAddress();
		}
    	else 
    	{
    		$curatorID = $currentItem->getApprovingCuratorID();
    		if (!empty($curatorID))
    		{
    			$curator = PeopleDAO::getPeopleDAO()->getPerson($curatorID);
    			$submittedbyemail = $curator->getEmailAddress();
    		}
    		else 
    		{
    			$submittedbyemail = '';
    		}
    	}

    	$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
    	
        if ($activitytype == 'Approved')
        {
        	$messagebase = "The Record Proposal Approval Request for ACORN Record# ".$currentItem->getItemID() . " has been approved";
        	if (!empty($comments))
        	{
        		$messagebase .= " with the following message: \"" . $comments . "\"\r\n";
        	}
        	else 
        	{
        		$messagebase .= ".\r\n";
        	}
        }
    	elseif ($activitytype == 'Denied')
        {
        	$messagebase = "The Record Proposal Approval Request for ACORN Record# ".$currentItem->getItemID() . " has been denied";
        	$messagebase .= " with the following message: \"" . $comments . "\"\r\n";
        }
    	else 
        {
        	$messagebase = "The Record Proposal Approval Request for ACORN Record# ".$currentItem->getItemID() . " has a new comment: ";
        	$messagebase .= "\"" . $comments . "\"\r\n";
        }
		$url = $config->getACORNUrl() . "proposalapproval/index/recordnumber/" . $currentItem->getItemID();
		$readonlyurl = $config->getACORNUrl() . "proposalapproval/index/recordnumber/" . $currentItem->getItemID() . "/readonly/1";
		
		$subject = "ACORN Record# " . $currentItem->getItemID() . " Proposal Approval Request Update";
		$message = $messagebase . "\r\nFollow this link to view the proposal: " . $url;
		$message .= "\r\n\r\nPlease use the comments text box found on the above link for all correspondence.";
		
		$readonlymessage = $messagebase . "\r\nFollow this link to view the proposal: " . $readonlyurl;
		$readonlymessage .= "\r\n\r\nPlease use the comments text box found on the above link for all correspondence.";
		
		$erroremailsarray = $config->getErrorEmailAddresses();
		$erroremails = implode(";", $erroremailsarray);
		$headers = 'From: ' . $submittedbyemail . "\r\n" .
		    'Reply-To: ' . $submittedbyemail . "\r\n" .
		    'Errors-To: ' . $erroremails . "\r\n" .
			'Return-Path: ' . $erroremails . "\r\n" .
			'X-Mailer: PHP/' . phpversion() . "\r\n";
		
		$additionalparams = "-f" . $erroremails;
		 Logger::log($headers);
		$sent = mail($email, $subject, $message, $headers, $additionalparams);
		if (!empty($ccstring))
		{
			$sent = $sent && mail($ccstring, $subject, $readonlymessage, $headers, $additionalparams);
		}
		return $sent;
    }
    
    private function saveComment(array $formvalues)
    {
    	//Get the current item
    	$item = RecordNamespace::getCurrentItem();
    	
    	$proposalapproval = $item->getFunctions()->getProposal()->getProposalApproval();
    	if (is_null($proposalapproval))
    	{
    		$proposalapproval = new ProposalApproval();
    	}
    	$comments = $formvalues['historycommentstextarea'];
    	$authorid = $formvalues['historyauthorselect'];
    	
    	if (Zend_Auth::getInstance()->hasIdentity())
    	{
	    	$identity = Zend_Auth::getInstance()->getIdentity();
	    	$author = PeopleDAO::getPeopleDAO()->getPerson($identity[PeopleDAO::PERSON_ID]);
    	}
    	//If an author was chosen
    	elseif (isset($authorid) && !empty($authorid))
    	{
    		$author = PeopleDAO::getPeopleDAO()->getPerson($authorid);
    	}
    	//Assume it is the approving curator
    	else
    	{
    		$author = PeopleDAO::getPeopleDAO()->getPerson($item->getApprovingCuratorID());
    	}
    	$historyitem = new ProposalApprovalHistoryItem();
    	$historyitem->setActivityType('Comment Added');
    	$historyitem->setAuthor($author);
    	$historyitem->setDetails($comments);
    	$zenddate = new Zend_Date();		
    	$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    	$historyitem->setHistoryDate($date);
    	
    	$proposalapproval->addHistoryItem($historyitem);
    	
    	$historyid = ProposalApprovalDAO::getProposalApprovalDAO()->saveHistoryItem($item->getItemID(), $historyitem);
    	return $historyid;
    }  
    
	private function displayErrors($form, $placeholdername, array $formData, $message)
    {
    	//Populate the data with what the user
		//gave but also display the error messages.
		$form->populate($formData);
		$this->view->form = $form;

		$item = RecordNamespace::getCurrentItem();
		$recordnumberlabel = "Proposal Approval Record #" . $item->getItemID();
		$recordinfolabel = $item->getInfoForLabel();
		$this->view->placeholder($placeholdername)->set($message);
		$this->view->placeholder('recordnumber')->set($recordnumberlabel);
		$this->view->placeholder('recordinfo')->set($recordinfolabel);

		if (isset($formData['summaryauthorselect']) || isset($formData['historyauthorselect']))
		{
			$this->view->readonly = 1;
		}
		$this->addSummaryInfo($item);
		$this->addReportSummaryInfo($item);
		$this->renderScript('proposalapproval/index.phtml');
    }
    
	private function saveProposalApproval(array $formvalues)
    {
    	//Get the current item
    	$item = RecordNamespace::getCurrentItem();
    	
    	$proposalapproval = $item->getFunctions()->getProposal()->getProposalApproval();
    	if (is_null($proposalapproval))
    	{
    		$proposalapproval = new ProposalApproval();
    	}
    	$comments = $formvalues['approvalcommentstextarea'];
    	$approvaltype = $formvalues['hiddenapprovaltype'];
    	$authorid = $formvalues['summaryauthorselect'];
    	
    	//Login is not required to perform this function.
    	if (Zend_Auth::getInstance()->hasIdentity())
    	{
	    	$identity = Zend_Auth::getInstance()->getIdentity();
	    	$author = PeopleDAO::getPeopleDAO()->getPerson($identity[PeopleDAO::PERSON_ID]);
    	}
    	//If an author was chosen
    	elseif (isset($authorid) && !empty($authorid))
    	{
    		$author = PeopleDAO::getPeopleDAO()->getPerson($authorid);
    	}
    	//If someone is not logged in, assume it is the approving curator.
    	else 
    	{
    		$author = PeopleDAO::getPeopleDAO()->getPerson($item->getApprovingCuratorID());
    	}
    	$historyitem = new ProposalApprovalHistoryItem();
    	$historyitem->setActivityType($approvaltype);
    	$historyitem->setAuthor($author);
    	$historyitem->setDetails($comments);
    	$zenddate = new Zend_Date();		
    	$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    	$historyitem->setHistoryDate($date);
    	
    	$proposalapproval->addHistoryItem($historyitem);
    	
    	$historyid = ProposalApprovalDAO::getProposalApprovalDAO()->saveHistoryItem($item->getItemID(), $historyitem);
    	return $historyid;
    }   

    public function emailAction()
    {
    	$form = $this->getEmailForm();
		
		//Get the current record
		$currentItem = RecordNamespace::getCurrentItem();
		if(!isset($currentItem) || is_null($currentItem))
		{
			$this->displayEmailErrors($form, 'emailerrormessage', array(), 'There was a problem detecting the ACORN record number.');
		}
		//otherwise return the record number and email addresses to use.
		else 
		{
			//Submitted by
			if (Zend_Auth::getInstance()->hasIdentity())
	    	{
				$identity = Zend_Auth::getInstance()->getIdentity();
				$submittedby = PeopleDAO::getPeopleDAO()->getPerson($identity[PeopleDAO::PERSON_ID]);
				$submittedbyemail = $submittedby->getEmailAddress();
			}
	    	else 
	    	{
	    		$submittedby = '';
	    		$submittedbyemail = '';
	    	}
			//Approving Curator
			$approvingcurator = PeopleDAO::getPeopleDAO()->getPerson($currentItem->getApprovingCuratorID());
			$approvingcuratoremail = $approvingcurator->getEmailAddress();
			$approvingcuratorname = $approvingcurator->getDisplayName();
			//Curator
			$curator = PeopleDAO::getPeopleDAO()->getPerson($currentItem->getCuratorID());
			$curatoremail = $curator->getEmailAddress();
			//Coordinator
			$coordinator = PeopleDAO::getPeopleDAO()->getPerson($currentItem->getCoordinatorID());
			$coordinatoremail = $coordinator->getEmailAddress();
			//Proposal by
			$propby = $currentItem->getFunctions()->getProposal()->getProposedBy();
			$propbyemails = array();
			foreach ($propby as $prop)
			{
				$propbyemails[$prop->getEmailAddress()] = $prop->getEmailAddress();
			}
			$propbyemail = implode("; ", $propbyemails);
			
			$approvalemail = "";
			$ccs = array();
			if (!empty($submittedbyemail) && !empty($approvingcuratoremail) && $submittedbyemail != $approvingcuratoremail)
			{
				$approvalemail = $approvingcuratoremail;
			}
			elseif (!empty($approvingcuratoremail))
			{
				$approvalemail = $approvingcuratoremail;
			}
			elseif (!empty($submittedbyemail))
			{
				$approvalemail = $submittedbyemail;
			}
			
			//Add the ccs (they will overwrite each other if they are the same person
			if (!empty($coordinatoremail))
			{
				$ccs[$coordinatoremail] = $coordinatoremail;
			}
			if (!empty($propbyemails))
			{
				array_merge($ccs, $propbyemails);
			}
			if (!empty($approvingcuratoremail))
			{
				$ccs[$approvingcuratoremail] = $approvingcuratoremail;
			}
			if (!empty($curatoremail))
			{
				$ccs[$curatoremail] = $curatoremail;
			}
			if (!empty($submittedbyemail))
			{
				$ccs[$submittedbyemail] = $submittedbyemail;
			}
			
			unset($ccs[$approvingcuratoremail]);
			
			$ccstring = implode(",", $ccs);
			
			//$fc = Zend_Controller_Front::getInstance();
	        //$initializer = $fc->getPlugin('Initializer');
	        
			$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
			
	        $callnumbers = $currentItem->getCallNumbers();
	        $callstring = implode(',', $callnumbers);
	        
			$url = $config->getACORNUrl() . "proposalapproval/index/recordnumber/" . $currentItem->getItemID();
			$subject = "ACORN Record# " . $currentItem->getItemID() . " Proposal Approval Request";
			$message = "Dear " . $approvingcuratorname .",\r\nYour approval is needed for a Record Proposal for ACORN Record# ".$currentItem->getItemID() . ", " . $callstring . "\r\n";
			$message .= " Follow this link to view the proposal (Firefox is the preferred browser): " . $url;
			$message .= " . Please Approve or Deny by using the buttons on the lower right.  If you have a comment or question please use the Comments text box and then click the Add Comment button on the bottom right to send.";
			$message .= "\r\n\r\nPlease use the comments text box found on the above link for all correspondence.";
			$message .= "\r\n\r\nIf you would like to print out a copy of the Proposal please press the Print Proposal button on the right.  A PDF will be created in a separate tab on your browser. You can view the Approval History using the History tab on the upper left.";
			$message .= "\r\n\r\nSincerely,";
			$message .= "\r\n" . $submittedby->getDisplayName();
			
			$activity = "Proposal Submitted";
			$details = "Submitted to " . $approvingcurator->getDisplayName() . " for approval";

			$array = array(
				'toinput' => $approvalemail,
				'subjectinput' => $subject,
				'ccinput' => $ccstring,
				'messageinput' => $message,
				'hiddenactivity' => $activity,
				'hiddendetails' => $details,
				'hiddenemailtype' => 'Proposal'
			);
			
			$form->populate($array);
			$this->view->form = $form;
			
		}
    }
    
	public function sendemailAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$this->_helper->layout->disableLayout();
		$form = $this->getEmailForm();
		if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			if ($form->isValid($formData))
			{
				$to = $formData['toinput'];
				$cc = $formData['ccinput'];
				$subject = $formData['subjectinput'];
				$message = $formData['messageinput'];
				$activity = $formData['hiddenactivity'];
				$details = $formData['hiddendetails'];
				$emailtype = $formData['hiddenemailtype'];
				
				$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
				
				$currentItem = RecordNamespace::getCurrentItem();
				
				if ($emailtype == 'Proposal')
				{
					$url = $config->getACORNUrl() . "proposalapproval/index/recordnumber/" . $currentItem->getItemID();
					$readonlyurl = $config->getACORNUrl() . "proposalapproval/index/recordnumber/" . $currentItem->getItemID() . "/readonly/1";
				}
				else 
				{
					$url = $config->getACORNUrl() . "proposalapproval/index/recordnumber/" . $currentItem->getItemID() . '#tabview=tab2';
					$readonlyurl = $config->getACORNUrl() . "proposalapproval/index/recordnumber/" . $currentItem->getItemID() . '/readonly/1#tabview=tab2';
				}
				//Create a read only url inside the message
				$readonlymessage = str_replace($url, $readonlyurl, $message);
				
				$erroremailsarray = $config->getErrorEmailAddresses();
				$erroremails = implode(";", $erroremailsarray);
				if (Zend_Auth::getInstance()->hasIdentity())
		    	{
					$identity = Zend_Auth::getInstance()->getIdentity();
					$submittedby = PeopleDAO::getPeopleDAO()->getPerson($identity[PeopleDAO::PERSON_ID]);
					$submittedbyemail = $submittedby->getEmailAddress();
					$headers = 'From: ' . $submittedbyemail . "\r\n" .
						'Reply-To: ' . $submittedbyemail . "\r\n" .
					    'Errors-To: ' . $erroremails . "\r\n" .
						'Return-Path: ' . $erroremails . "\r\n" .
						'X-Mailer: PHP/' . phpversion() . "\r\n";
				}
		    	else 
		    	{
		    		$approvingCuratorID = $currentItem->getApprovingCuratorID();
		    		if (!empty($approvingCuratorID))
		    		{
		    			$submittedby = PeopleDAO::getPeopleDAO()->getPerson($approvingCuratorID);
		    			$submittedbyemail = $submittedby->getEmailAddress();
		    			$headers = "From: " . $submittedbyemail . "\r\n" .
		    					'Reply-To: ' . $submittedbyemail . "\r\n" .
		    					'Errors-To: ' . $erroremails . "\r\n" .
		    					'Return-Path: ' . $erroremails . "\r\n" .
		    					'X-Mailer: PHP/' . phpversion() . "\r\n";
		    		}
		    		else 
		    		{
			    		$submittedby = '';
			    		$submittedbyemail = '';
			    		$headers = "From: " . $erroremails . "\r\n" .
			    				'Reply-To: ' . $erroremails . "\r\n" .
			    				'Errors-To: ' . $erroremails . "\r\n" .
			    				'Return-Path: ' . $erroremails . "\r\n" .
			    				'X-Mailer: PHP/' . phpversion() . "\r\n";
		    		}
		    		
		    	}
		    	$additionalparams = "-f" . $erroremails;
		    	//ApprovingCurator email
				$sent = mail($to, $subject, $message, $headers, $additionalparams);
				if (!empty($cc))
				{
					//Curator and others email
					$sent = mail($cc, $subject, $readonlymessage, $headers, $additionalparams);
				}
				//If the message was sent propoerly, log it and go back to the previous screen.
				if ($sent)
				{
					//Mark this email as a history entry.
					$historyitem = new ProposalApprovalHistoryItem();
					$historyitem->setActivityType($activity);
					$historyitem->setAuthor($submittedby);
					$historyitem->setDetails($details);
					$zenddate = new Zend_Date();		
    				$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    				$historyitem->setHistoryDate($date);
					$historyid = ProposalApprovalDAO::getProposalApprovalDAO()->saveHistoryItem($currentItem->getItemID(), $historyitem);
				
					$tab = RecordNamespace::getRenderTab();
					if (!isset($tab))
					{
						$tab = "tab2";
					}
					$recnum = $currentItem->getItemID() . '#tabview=' . $tab;
					$this->getAcornRedirector()->setGoto('index', 'record', NULL, array('recordnumber' => $recnum));
				}
				else 
				{
					$this->displayEmailErrors($form, 'emailerrormessage', array(), 'There was a problem sending the email.');
				}
			}
			else 
			{
				
				$this->displayEmailErrors($form, 'summaryerrormessage', $formData, 'Changes were not saved.  Please make sure that all required data is entered.');
			}
		}
    	else 
		{
			$this->_helper->redirector('email');
		}
    }
    
	public function emailreportAction()
    {
    	$form = $this->getEmailForm();
		
		//Get the current record
		$currentItem = RecordNamespace::getCurrentItem();
		if(!isset($currentItem) || is_null($currentItem))
		{
			$this->displayEmailErrors($form, 'emailerrormessage', array(), 'There was a problem detecting the ACORN record number.');
		}
		//otherwise return the record number and email addresses to use.
		else 
		{
			//Submitted by
			if (Zend_Auth::getInstance()->hasIdentity())
	    	{
				$identity = Zend_Auth::getInstance()->getIdentity();
				$submittedby = PeopleDAO::getPeopleDAO()->getPerson($identity[PeopleDAO::PERSON_ID]);
				$submittedbyemail = $submittedby->getEmailAddress();
			}
	    	else 
	    	{
	    		$submittedby = '';
	    		$submittedbyemail = '';
	    	}
			//Approving Curator
			$approvingcurator = PeopleDAO::getPeopleDAO()->getPerson($currentItem->getApprovingCuratorID());
			$approvingcuratoremail = $approvingcurator->getEmailAddress();
			$approvingcuratorname = $approvingcurator->getDisplayName();
			//Curator
			$curator = PeopleDAO::getPeopleDAO()->getPerson($currentItem->getCuratorID());
			$curatoremail = $curator->getEmailAddress();
			//Coordinator
			$coordinator = PeopleDAO::getPeopleDAO()->getPerson($currentItem->getCoordinatorID());
			$coordinatoremail = $coordinator->getEmailAddress();
			//Report by
			$repby = PeopleDAO::getPeopleDAO()->getPerson($currentItem->getFunctions()->getReport()->getReportBy());
			$repbyemail = $repby->getEmailAddress();
			
			$email = "";
			$ccs = array();
                        
                        if (!empty($approvingcuratoremail)) 
                        {
                                $email = $approvingcuratoremail;
                        }
			elseif (!empty($curatoremail))
			{
				$email = $curatoremail;
			}
			
			//Add the ccs (they will overwrite each other if they are the same person
			if (!empty($coordinatoremail))
			{
				$ccs[$coordinatoremail] = $coordinatoremail;
			}
			if (!empty($repbyemail))
			{
				$ccs[$repbyemail] = $repbyemail;
			}
			if (!empty($curatoremail) && $email != $curatoremail)
			{
				$ccs[$curatoremail] = $curatoremail;
			}
			if (!empty($submittedbyemail))
			{
				$ccs[$submittedbyemail] = $submittedbyemail;
			}
			
			unset($ccs[$email]);
			
			$ccstring = implode(",", $ccs);
			
			//$fc = Zend_Controller_Front::getInstance();
	        //$initializer = $fc->getPlugin('Initializer');
	        
			$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
			
	        $callnumbers = $currentItem->getCallNumbers();
	        $callstring = implode(',', $callnumbers);
	        
			$url = $config->getACORNUrl() . "proposalapproval/index/recordnumber/" . $currentItem->getItemID() . '#tabview=tab2';
			$subject = "ACORN Record# " . $currentItem->getItemID() . " Final Report";
			$message = "Dear " . $approvingcuratorname .",\r\n\r\nYou may now view the Final Report for ACORN Record# ".$currentItem->getItemID() . ", " . $callstring . "\r\n";
			$message .= "Follow this link to view the report: " . $url;
			$message .= "\r\nYou do not need to approve the Final Report.";
			$message .= "\r\n\r\nSincerely,";
			$message .= "\r\n" . $submittedby->getDisplayName();
			
			$activity = "Report Submitted";
			$details = "Submitted to " . $approvingcurator->getDisplayName();
			
			$array = array(
				'toinput' => $email,
				'subjectinput' => $subject,
				'ccinput' => $ccstring,
				'messageinput' => $message,
				'hiddenactivity' => $activity,
				'hiddendetails' => $details,
				'hiddenemailtype' => 'Report'
			);
			
			$form->populate($array);
			$this->view->form = $form;
			$this->render('email');
		}
    }
    
	private function displayEmailErrors($form, $placeholdername, array $formData, $message)
    {
    	//Populate the data with what the user
		//gave but also display the error messages.
		$form->populate($formData);
		$this->view->form = $form;

		$item = RecordNamespace::getCurrentItem();
		$recordnumberlabel = "Proposal Approval Record #" . $item->getItemID();
		$recordinfolabel = $item->getInfoForLabel();
		$this->view->placeholder($placeholdername)->set($message);
		$this->view->placeholder('recordnumber')->set($recordnumberlabel);
		$this->view->placeholder('recordinfo')->set($recordinfolabel);
	}
    
	/**
	 * Finds the list of history for the current item.
	 */
    public function findhistoryAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$approval = RecordNamespace::getCurrentItem()->getFunctions()->getProposal()->getProposalApproval();
		$historyitems = $approval->getHistory();
		$history = array();
		foreach ($historyitems as $item)
		{
			array_push($history, array(
					ProposalApprovalDAO::DATE_ENTERED => $item->getHistoryDate(),
					ProposalApprovalDAO::ACTIVITY_TYPE => $item->getActivityType(),
					ProposalApprovalDAO::DETAILS => $item->getDetails(),
					"Author" => $item->getAuthor()->getDisplayName()
				)
			);
		}
		
    	//Only use the results
    	$resultset = array('Result' => $history);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
    function authorSort($a, $b)
    {
    	$cmp = strcmp($a["SortName"], $b["SortName"]);
    	if ($cmp == 0)
    	{
    		$cmp = strcmp($a["SortName"], $b["SortName"]);
    	}
    	return $cmp;
    }
    
	public function populatepermittedauthorsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$item = RecordNamespace::getCurrentItem();
		$authors = array();
		if (isset($item) && !is_null($item))
		{
			$approvingcuratorid = $item->getApprovingCuratorID();
			$curatorid = $item->getCuratorID();
			$coordinatorid = $item->getCoordinatorID();
			$proposalbyarray = $item->getFunctions()->getProposal()->getProposedBy();
			
			$listids = array();
			$listids[$approvingcuratorid] = $approvingcuratorid;
			$listids[$curatorid] = $curatorid;
			$listids[$coordinatorid] = $coordinatorid;
			foreach ($proposalbyarray as $proposalby)
			{
				$listids[$proposalby->getPrimaryKey()] = $proposalby->getPrimaryKey();
			}
			
			
			$repositoryadmin = PeopleDAO::getPeopleDAO()->getRepositoryAdmin($item->getHomeLocationID());
			if (!is_null($repositoryadmin))
			{
				$listids[$repositoryadmin->getPrimaryKey()] = $repositoryadmin->getPrimaryKey();
			}
			foreach ($listids as $listid)
			{
				$person = PeopleDAO::getPeopleDAO()->getPerson($listid);
				array_push($authors, array("DisplayName" => $person->getDisplayName(), 
										"PersonID" => $listid,
										"SortName" => $person->getSortName()));
			}
			
			function authorSort($a, $b){
				$cmp = strcmp($a["SortName"], $b["SortName"]);
				if ($cmp == 0)
				{
					$cmp = strcmp($a["SortName"], $b["SortName"]);
				}
				return $cmp;
			}
			usort($authors, "authorSort");
			
			/*$approvingcurator = PeopleDAO::getPeopleDAO()->getPerson($approvingcuratorid);
			array_push($authors, array("DisplayName" => $approvingcurator->getDisplayName(), "PersonID" => $approvingcuratorid));
			if ($curatorid != $approvingcuratorid)
			{
				$curator = PeopleDAO::getPeopleDAO()->getPerson($curatorid);
				array_push($authors, array("DisplayName" => $curator->getDisplayName(), "PersonID" => $curatorid));
			}*/
			
			
			
		}
		
		$retval = Zend_Json::encode(array('Result' => $authors));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
}
?>