<?php

/**
 * RecordController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
ini_set('memory_limit', '128M');


class RecordController extends Zend_Controller_Action
{
	private $recordForm;
	private $oswForm;
	private $recordStatusForm;
	private $acornRedirector;
	
	private function getRecordForm()
	{
		if (is_null($this->recordForm))
		{
			$this->recordForm = new RecordForm();
		}
		return $this->recordForm;
	}
	
	private function getOswForm()
	{
		if (is_null($this->oswForm))
		{
			$this->oswForm = new OswForm();
		}
		return $this->oswForm;
	}
	
	private function getRecordStatusForm()
	{
		if (is_null($this->recordStatusForm))
		{
			$this->recordStatusForm = new RecordStatusForm();
		}
		return $this->recordStatusForm;
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
	
    public function newitemAction()
    {
    	RecordNamespace::clearRenderTab();
    	RecordNamespace::clearCurrentItem();
    	RecordNamespace::clearOriginalItem();
    	RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_IDLE);
    	RecordNamespace::setCurrentItem(new Item());
    	$this->_helper->redirector('index');
    }
    
	public function newoswAction()
    {
    	RecordNamespace::clearRenderTab();
    	RecordNamespace::clearCurrentOSW();
    	RecordNamespace::clearOriginalOSW();
    	RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_IDLE);
    	RecordNamespace::setCurrentOSW(new OnSiteWork());
    	
    	$this->_helper->redirector('osw');
    }
    
	/**
	 * The default action - show the home page
	 */
    public function indexAction() 
    {
    	RecordNamespace::setRecordType(RecordNamespace::RECORD_TYPE_ITEM);
    	
       	$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		$accesslevel = $identity[PeopleDAO::ACCESS_LEVEL];
		$userrepository = $identity[LocationDAO::LOCATION_ID];
		
		$recordnumber = $this->getRequest()->getParam('recordnumber');
		
		//This gets the record created by info
		//Either the current user or the 
    	$createdby = NULL;
    	$createdate = NULL;
    	
    	$recordnumberlabel = "New Record";
    	$recordinfolabel = "";
    	
    	$identificationdisabledmessage = "";
    	$logindisabledmessage = "";
		$logoutdisabledmessage = "";
		$proposaldisabledmessage = "";
		$reportdisabledmessage = "";
		$filesdisabledmessage = "";
		$temptransferdisabledmessage = "";
		$temptransferreturndisabledmessage = "";
		
		$form = $this->getRecordForm();
			
		$item = RecordNamespace::getCurrentItem();
		if (isset($recordnumber))
		{
			if (!isset($item) || $item->getItemID() != $recordnumber)
			{
				$item = ItemDAO::getItemDAO()->getItem($recordnumber);
				RecordNamespace::setOriginalItem(ItemDAO::getItemDAO()->getItem($recordnumber));
				RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_IDLE);
				
				$userid = $identity[PeopleDAO::PERSON_ID];
				$tempfilepath = $_SERVER['DOCUMENT_ROOT'] . "/userfiles/temp" . $userid;
				$this->clearTemporaryFiles($tempfilepath);	
			}
			
			if (!is_null($item))
			{
				$recordnumberlabel = "Record #" . $item->getItemID();
				//If the access level is a repository access level and this record doesn't belong to their repostiroy,
				//redirect them to the error page.
				if (($accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY || $accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN) 
					&& $item->getHomeLocationID() != $userrepository)
					{
						$this->_helper->redirector('recordnotinrepository', 'record', NULL, array('recordnumberlabel' => $recordnumber));
					}
				
				$recordinfolabel = $item->getInfoForLabel();
				RecordNamespace::setCurrentItem($item);
				
				//Get the record creation information
				$audittrailitems = $item->getAuditTrail("Insert");
				if (!empty($audittrailitems))
				{
					$audittrailitem = current($audittrailitems);
					$createdate = $audittrailitem->getDate();
					$createdby = $audittrailitem->getPerson()->getDisplayName();
				}
				
				$identificationarray = $this->buildItemIdentificationFormArray($item);
				$loginarray = $this->buildItemLoginFormArray($item, $item->getFunctions()->getLogin());
				$proposalarray = $this->buildItemProposalFormArray($item->getFunctions()->getProposal(), $item);
				$reportarray = $this->buildItemReportFormArray($item->getFunctions()->getReport(), $item, $item->getFunctions()->getProposal());
				$logoutarray = $this->buildItemLogoutFormArray($item, $item->getFunctions()->getLogout(), $item->getFunctions()->getLogin());
				$temptransferarray = $this->buildItemTemporaryTransferFormArray($item->getFunctions()->getTemporaryTransfer(), $item);
				$temptransferreturnarray = $this->buildItemTemporaryTransferReturnFormArray($item->getFunctions()->getTemporaryTransferReturn(), $item);
				$filesarray = $this->buildFilesFormArray($item);
				
				//$filesarray = $this->buildFilesFormArray($item);
				$formarray = array_merge($identificationarray, $loginarray, $proposalarray, $reportarray, $logoutarray, $temptransferarray, $temptransferreturnarray, $filesarray);
				$form->populate($formarray);
			}
		}
		else
		{
			$formarray = $this->buildDefaultsArray();
			$form->populate($formarray);
		}

		//If the create date and created by haven't been populated,
		//use the current user.
		if (is_null($createdate) && is_null($createdby))
		{
			$createdby = $identity[PeopleDAO::DISPLAY_NAME];
			$zenddate = new Zend_Date();
			$createdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
		}
		
		//If this is a repository user then disable everything
		if ($accesslevel != PeopleDAO::ACCESS_LEVEL_ADMIN && $accesslevel != PeopleDAO::ACCESS_LEVEL_REGULAR)
		{
			//If the person is a repository admin and the item is not logged in, they are able to edit the identification still
			if ($accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN && is_null($item->getFunctions()->getLogin()->getPrimaryKey()))
			{
				$form->disable(array('recordidentificationform', 'expecteddateofreturninput', 'insuranceinput', 'repositorymemotextarea'));
			}
			else 
			{
				$form->disable(array('expecteddateofreturninput', 'insuranceinput', 'repositorymemotextarea'));
			}
		}
		elseif (isset($item) && !is_null($item) && !$item->isEditable())
		{
			$message = "This record is currently being edited by " . $item->getIsBeingEditedBy()->getDisplayName();
			
			$identificationdisabledmessage = $message;
			$logindisabledmessage = $message;
			$logoutdisabledmessage = $message;
			$proposaldisabledmessage = $message;
			$reportdisabledmessage = $message;
			$filesdisabledmessage = $message;
			$temptransferdisabledmessage = $message;
			$temptransferreturndisabledmessage = $message;
			
			$form->disable();
		}
		else
		{
			//If the item was set, see if there are any forms to disable.
			if (isset($item) && !is_null($item))
			{
				$itemid = $item->getItemID();
				//If this isn't a new item, then mark it as 'checked out'
				if (!is_null($itemid) && ItemDAO::getItemDAO()->itemExists($itemid))
				{
					RecordNamespace::getCurrentItem()->setIsBeingEdited(TRUE);
					RecordNamespace::getCurrentItem()->setIsBeingEditedByID($identity[PeopleDAO::PERSON_ID]);
				}
				
				$identificationdisabledmessage = $item->getDisableIdentificationMessage();
				$logindisabledmessage = $item->getDisableLoginMessage();
		    	$logoutdisabledmessage = $item->getDisableLogoutMessage();
		    	$proposaldisabledmessage = $item->getDisableProposalMessage();
				$reportdisabledmessage = $item->getDisableReportMessage();
				$filesdisabledmessage = $item->getDisableFilesMessage();
				$temptransferdisabledmessage = $item->getDisableTempTransferMessage();
				$temptransferreturndisabledmessage = $item->getDisableTempTransferReturnMessage();
			}
			
	    	if (!empty($identificationdisabledmessage))
			{
				$recordidentificationform = $form->getSubForm('recordidentificationform');
				$recordidentificationform->disable(array('unlockuntildateinput', 'gotohollisbutton'));
			}
			if (!empty($logindisabledmessage))
			{
				$recordloginform = $form->getSubForm('recordloginform');
				$recordloginform->disable();
			}
			if (!empty($proposaldisabledmessage))
			{
				$recordproposalform = $form->getSubForm('recordproposalform');
				$recordproposalform->disable();
			}
			if (!empty($reportdisabledmessage))
			{
				$recordreportform = $form->getSubForm('recordreportform');
				$recordreportform->disable();
			}
			if (!empty($logoutdisabledmessage))
			{
				$recordlogoutform = $form->getSubForm('recordlogoutform');
				$recordlogoutform->disable();
			}
			if (!empty($filesdisabledmessage))
			{
				$recordfileform = $form->getSubForm('recordfileform');
				$recordfileform->disable();
			}
			if (!empty($temptransferdisabledmessage))
			{
				$recordtemptransferform = $form->getSubForm('recordtemporarytransferform');
				$recordtemptransferform->disable();
			}
			if (!empty($temptransferreturndisabledmessage))
			{
				$recordtemptransferreturnform = $form->getSubForm('recordtemporarytransferreturnform');
				$recordtemptransferreturnform->disable();
			}
			
		}
				
		$this->view->form = $form;
		//Record info
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('recordnumber')->set($recordnumberlabel);
		$this->view->placeholder('recordinfo')->set($recordinfolabel);
		//Display the messages on the disabled forms (if any exist)
		//If the identification message is blank, then see if the record is currently unlocked.  
		//if (empty($identificationdisabledmessage) && isset($item) && !is_null($item))
		if (isset($item) && !is_null($item))
		{
			if ($item->isUnlocked())
			{
				$expdate = UnlockedItemsDAO::getUnlockedItemsDAO()->getExpirationDate($item->getItemID());
				$identificationdisabledmessage = "Item is Unlocked for Editing until " . $expdate;
			}
		}
		$this->view->placeholder('identificationdisabledmessage')->set($identificationdisabledmessage);
		$this->view->placeholder('logindisabledmessage')->set($logindisabledmessage);
		$this->view->placeholder('proposaldisabledmessage')->set($proposaldisabledmessage);
		$this->view->placeholder('reportdisabledmessage')->set($reportdisabledmessage);
		$this->view->placeholder('logoutdisabledmessage')->set($logoutdisabledmessage);
		$this->view->placeholder('filesdisabledmessage')->set($filesdisabledmessage);
		$this->view->placeholder('temptransferdisabledmessage')->set($temptransferdisabledmessage);
		$this->view->placeholder('temptransferreturndisabledmessage')->set($temptransferreturndisabledmessage);
	}
	
	/**
	 * Clears out the files that were saved in the user's temporary directory.
	 * (DOCUMENT_ROOT/userfiles/temp<userid>)
	 *
	 */
	private function clearTemporaryFiles($usertempdir)
	{
		if (file_exists($usertempdir))
		{
			$handle=opendir($usertempdir);
		
			while (($file = readdir($handle))!==false)
			{
				@unlink($usertempdir.'/'.$file);
			}
		
			closedir($handle);
		}
	}
	
	private function buildDefaultsArray()
	{
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		
		$zenddate = new Zend_Date();
		
		$formarray = array(
	    		'coordinatorselect' => $identity[PeopleDAO::PERSON_ID],
	    		'workassignedtoselect' => $identity[PeopleDAO::PERSON_ID],
				'loginbyselect' => $identity[PeopleDAO::PERSON_ID],
	    		'logindateinput' => $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT),
				'coordinatorinput' => $identity[PeopleDAO::DISPLAY_NAME],
	    		'proposaldateinput' => $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT),
	    		'proposalbyselect' => $identity[PeopleDAO::PERSON_ID],
				'reportdateinput' => $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT),
	    		'reportbyselect' => $identity[PeopleDAO::PERSON_ID],
    			'transferdateinput' => $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT),
				'transferreturndateinput' => $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT),
				'logoutbyselect' => $identity[PeopleDAO::PERSON_ID],
	    		'logoutdateinput' => $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT)
	    	);
		if ($identity[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN)
		{
			$formarray['repositoryselect'] = $identity[LocationDAO::LOCATION_ID];
			$formarray['chargetoselect'] = $identity[LocationDAO::LOCATION_ID];
		}
	    return $formarray;
	}

    /*
     * Builds an array to display the data on the record screen.
     * 
     * @param Item item 
     * @return an array prepared for the form
     */
    private function buildItemIdentificationFormArray(Item $item = NULL)
    {
    	if (!is_null($item))
    	{
    		$currentzenddate = new Zend_Date();
    		
    		$currentzenddate = $currentzenddate->addday(30);
    		$futuredate = $currentzenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
    		
	    	$formarray = array(
	    		'hiddenitemid' => $item->getItemID(),
	    		'projectselect' => $item->getProjectID(),
	    		'groupselect' => $item->getGroupID(),
	    		'coordinatorselect' => $item->getCoordinatorID(),
	    		'curatorselect' => $item->getCuratorID(),
	    		'approvingcuratorselect' => $item->getApprovingCuratorID(),
	    		'purposeselect' => $item->getPurposeID(),
	    		'storageinput' => $item->getStorage(),
	    		'repositoryselect' => $item->getHomeLocationID(),
	    		'departmentselect' => $item->getDepartmentID(),
	    		'chargetoselect' => $item->getChargeToID(),
	    		'collectionnameinput' => $item->getCollectionName(),
	    		'authorinput' => $item->getAuthorArtist(),
	    		'titleinput' => $item->getTitle(),
	    		'dateofobjectinput' => $item->getDateOfObject(),
	    		'formatselect' => $item->getFormatID(),
	    		'expecteddateofreturninput' => $item->getExpectedDateOfReturn(),
	    		'insuranceinput' => $item->getInsuranceValue(),
	    		'repositorymemotextarea' => $item->getFundMemo(),
	    		'noncollectioncheckbox' => $item->isNonCollectionMaterial(),
	    		'commentstextarea' => $item->getComments(),
	    		'unlockuntildateinput' => $futuredate
	    	);
    	}
    	else 
    	{
    		$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
		
			$formarray = array(
    			'coordinatorselect' => $identity[PeopleDAO::PERSON_ID],
	    		'workassignedtoselect' => $identity[PeopleDAO::PERSON_ID],
				);
				
			if ($identity[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN)
			{
				$formarray['repositoryselect'] = $identity[LocationDAO::LOCATION_ID];
				$formarray['chargetoselect'] = $identity[LocationDAO::LOCATION_ID];
			}
    	}
    	return $formarray;
    }
    
	/*
     * Builds an array to display the data on the record screen.
     * 
     * @param Login item 
     * @return an array prepared for the form
     */
    private function buildItemLoginFormArray(Item $item, Login $login)
    {
    	$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		
		if (!is_null($login->getPrimaryKey()))
    	{
		    $formarray = array(
		    	'hiddenloginitemid' => $item->getItemID(),
	    		'loginbyselect' => $login->getLoginBy(),
	    		'logindateinput' => $login->getLoginDate(),
	    		'loginfromselect' => $login->getTransferFrom(),
	    		'logintoselect' => $login->getTransferTo()
	    	);
	    }
    	else 
    	{
    		$zenddate = new Zend_Date();
			$formarray = array(
    			'hiddenloginitemid' => $item->getItemID(),
	    		'loginbyselect' => $identity[PeopleDAO::PERSON_ID],
    			'logindateinput' => $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT),
    			'loginfromselect' => $item->getHomeLocationID()
    		);
    	}
    	return $formarray;
    }
    
    
    
	/*
     * Builds an array to display the data on the record screen.
     * 
     * @param Proposal item 
     * @return an array prepared for the form
     */
    private function buildItemProposalFormArray(Proposal $proposal, Item $item)
    {
    	if (!is_null($proposal->getPrimaryKey()))
    	{
    		$coord = PeopleDAO::getPeopleDAO()->getPerson($item->getCoordinatorID());
		    $formarray = array(
	    		'hiddenproposalitemid' => $item->getItemID(),
	    		'coordinatorinput' => $coord->getDisplayName(),
	    		'proposalcommentstextarea' => $item->getComments(),
	    		'proposedhoursmin' => $proposal->getMinHours(),
	    		'proposedhoursmax' => $proposal->getMaxHours(),
	    		'proposaldateinput' => $proposal->getProposalDate(),
	    		'examdateinput' => $proposal->getExamDate(),
	    		'examlocationselect' => $proposal->getExamLocation(),
	    		'unitselect' => $proposal->getDimensions()->getUnit(),
	    		'proposalheightinput' => $proposal->getDimensions()->getHeight(),
	    		'proposalwidthinput' => $proposal->getDimensions()->getWidth(),
	    		'proposalthicknessinput' => $proposal->getDimensions()->getThickness(),
	    		'proposaldescriptiontextarea' => $proposal->getDescription(),
	    		'proposalconditiontextarea' => $proposal->getCondition(),
	    		'proposaltreatmenttextarea' => $proposal->getTreatment()
	    	);
    	}
    	else 
    	{
    		$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
		
    		$zenddate = new Zend_Date();
			$formarray = array(
    			'hiddenproposalitemid' => $item->getItemID(),
	    		'coordinatorinput' => $identity[PeopleDAO::DISPLAY_NAME],
	    		'proposaldateinput' => $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT),
	    		'proposalbyselect' => $identity[PeopleDAO::PERSON_ID],
    			'proposalcommentstextarea' => $item->getComments()
    		);
    	}
    	return $formarray;
    }
    
	/*
     * Builds an array to display the data on the record screen.
     * 
     * @param Report item 
     * @param Item item 
     * @return an array prepared for the form
     */
    private function buildItemReportFormArray(Report $report, Item $item, Proposal $proposal)
    {
    	if (!is_null($report->getPrimaryKey()))
    	{
		    $formarray = array(
	    		'hiddenreportitemid' => $item->getItemID(),
	    		'actualhours' => $report->getTotalHours(),
	    		'reportdateinput' => $report->getReportDate(),
	    		'reportbyselect' => $report->getReportBy(),
	    		'worklocationselect' => $report->getWorkLocation(),
	    		'reportformatselect' => $report->getFormat(),
	    		'reportunitselect' => $report->getDimensions()->getUnit(),
	    		'reportheightinput' => $report->getDimensions()->getHeight(),
	    		'reportwidthinput' => $report->getDimensions()->getWidth(),
	    		'reportthicknessinput' => $report->getDimensions()->getThickness(),
	    		'reportcommentstextarea' => $item->getComments(),
	    		'adminonlycheckbox' => $report->isAdminOnly(),
	    		'examonlycheckbox' => $report->isExamOnly(),
	    		'customhousingonlycheckbox' => $report->isCustomHousingOnly(),
	    		'reporttreatmenttextarea' => $report->getTreatment(),
	    		'presrectextarea' => $report->getPreservationRecommendations(),
	    		'reporttreatmentsummarytextarea' => $report->getSummary(),
		    	'additionalmaterialsonfilecheckbox' => $report->additionalMaterialsOnFile()
	    	);
    	}
    	else 
    	{
    		$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
		
			if (is_null($proposal))
			{
				$dimensions = new Dimensions();
			}
			else 
			{
				$dimensions = $proposal->getDimensions();
			}
    		$zenddate = new Zend_Date();
			$formarray = array(
    			'hiddenreportitemid' => $item->getItemID(),
	    		'reportdateinput' => $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT),
	    		'reportbyselect' => $identity[PeopleDAO::PERSON_ID],
    			'reportformatselect' => $item->getFormatID(),
	    		'reportunitselect' => $dimensions->getUnit(),
	    		'reportheightinput' => $dimensions->getHeight(),
	    		'reportwidthinput' => $dimensions->getWidth(),
	    		'reportthicknessinput' => $dimensions->getThickness(),
	    		'reportcommentstextarea' => $item->getComments(),
    			//Default to WPC
    			'worklocationselect' => 1
    		);
    	}
    	return $formarray;
    }
    
	/*
     * Builds an array to display the data on the record screen.
     * 
     * @param Logout item 
     * @return an array prepared for the form
     */
    private function buildItemLogoutFormArray(Item $item, Logout $logout, Login $login)
    {
    	if (!is_null($logout->getPrimaryKey()))
    	{
		    $formarray = array(
	    		'hiddenlogoutitemid' => $item->getItemID(),
	    		'logoutbyselect' => $logout->getLogoutBy(),
	    		'logoutdateinput' => $logout->getLogoutDate(),
	    		'logoutfromselect' => $logout->getTransferFrom(),
	    		'logouttoselect' => $logout->getTransferTo()
	    	);
    	}
    	else 
    	{
    		$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
    		
			$zenddate = new Zend_Date();
			$formarray = array(
				'hiddenlogoutitemid' => $item->getItemID(),
    			'logoutbyselect' => $identity[PeopleDAO::PERSON_ID],
    			'logoutdateinput' => $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT),
    			'logoutfromselect' => $login->getTransferTo()
    		);
    	}
    	return $formarray;
    }
   
	/*
     * Builds an array to display the data on the record screen.
     * 
     * @param TemporaryTransfer item 
     * @return an array prepared for the form
     */
    private function buildItemTemporaryTransferFormArray(TemporaryTransfer $transfer = NULL, Item $item)
    {
    	if (!is_null($transfer))
    	{
		    $formarray = array(
	    		'hiddentemptransferitemid' => $item->getItemID(),
	    		'transferdateinput' => $transfer->getTransferDate(),
	    		'transferfromselect' => $transfer->getTransferFrom(),
	    		'transfertoselect' => $transfer->getTransferTo(),
		    	'temptransfercommentstextarea' => $item->getComments()
	    	);
    	}
    	else 
    	{
    		$formarray = array('hiddentemptransferitemid' => $item->getItemID());
    	}
    	return $formarray;
    }
    
	/*
     * Builds an array to display the data on the record screen.
     * 
     * @param TemporaryTransfer item 
     * @return an array prepared for the form
     */
    private function buildItemTemporaryTransferReturnFormArray(TemporaryTransfer $transfer = NULL, Item $item)
    {
    	if (!is_null($transfer))
    	{
		    $formarray = array(
	    		'hiddentemptransferreturnitemid' => $item->getItemID(),
	    		'transferreturndateinput' => $transfer->getTransferDate(),
	    		'transferreturnfromselect' => $transfer->getTransferFrom(),
	    		'transferreturntoselect' => $transfer->getTransferTo(),
		    	'returncommentstextarea' => $item->getComments()
	    	);
    	}
    	else 
    	{
    		$formarray = array('hiddentemptransferreturnitemid' => $item->getItemID());
    	}
    	return $formarray;
    }
    
	/*
     * Builds an array to display the data on the record screen.
     * 
     * @param Item item 
     * @return an array prepared for the form
     */
    private function buildFilesFormArray(Record $item)
    {
    	$formarray = array('hiddenfilepkid' => $item->getPrimaryKey());
	    
    	return $formarray;
    }
    
	/**
	 * Show the osw page
	 */
    public function oswAction() 
    {
    	RecordNamespace::setRecordType(RecordNamespace::RECORD_TYPE_OSW);
    	RecordNamespace::clearRenderTab();
    	
      	$form = $this->getOswForm();
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		$accesslevel = $identity[PeopleDAO::ACCESS_LEVEL];
		$userrepository = $identity[LocationDAO::LOCATION_ID];
		
    	$recordnumber = $this->getRequest()->getParam('recordnumber');
    	
    	//This gets the record created by info
		//Either the current user or the 
    	$createdby = NULL;
    	$createdate = NULL;
    	
    	$recordnumberlabel = "New OSW Record";
    	
    	$oswdisabledmessage = "";
    	
		$osw = RecordNamespace::getCurrentOSW();
		if (isset($recordnumber))
		{
			if (!isset($osw) || $osw->getOSWID() != $recordnumber)
			{
				$osw = OnSiteWorkDAO::getOnSiteWorkDAO()->getOSW($recordnumber);
				RecordNamespace::setOriginalOSW(OnSiteWorkDAO::getOnSiteWorkDAO()->getOSW($recordnumber));
				RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_IDLE);
				
				$userid = $identity[PeopleDAO::PERSON_ID];
				$tempfilepath = $_SERVER['DOCUMENT_ROOT'] . "/userfiles/temp" . $userid;
				$this->clearTemporaryFiles($tempfilepath);
			}
			if (!is_null($osw))
			{
				$recordnumberlabel = "OSW Record #" . $osw->getOSWID();
				//If the access level is a repository access level and this record doesn't belong to their repostiroy,
				//redirect them to the error page.
				if (($accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY || $accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN) 
					&& $osw->getHomeLocationID() != $userrepository)
					{
						$this->_helper->redirector('recordnotinrepository', 'record', NULL, array('recordnumberlabel' => $recordnumberlabel));
					}
				RecordNamespace::setCurrentOSW($osw);
				
				//Get the record creation information
				$audittrailitems = $osw->getAuditTrail("Insert");
				if (!empty($audittrailitems))
				{
					$audittrailitem = current($audittrailitems);
					$createdate = $audittrailitem->getDate();
					$createdby = $audittrailitem->getPerson()->getDisplayName();
				}
				
				$identificationarray = $this->buildOSWFormArray($osw);
				$reportarray = $this->buildOSWReportFormArray($osw->getFunctions()->getReport());
				$filesarray = $this->buildFilesFormArray($osw);
				$formarray = array_merge($identificationarray, $reportarray, $filesarray);
				$form->populate($formarray);
			}
			
		}
    	//If the create date and created by haven't been populated,
		//use the current user.
		if (is_null($createdate) && is_null($createdby))
		{
			$createdby = $identity[PeopleDAO::DISPLAY_NAME];
			$zenddate = new Zend_Date();
			$createdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
		}
		if ($accesslevel != PeopleDAO::ACCESS_LEVEL_ADMIN && $accesslevel != PeopleDAO::ACCESS_LEVEL_REGULAR)
		{
			$form->disable();
		}
		//If the record is locked for editing, make the form read only.
    	elseif (isset($osw) && !is_null($osw) && !$osw->isEditable())
		{
			$oswdisabledmessage = "This record is currently being edited by " . $osw->getIsBeingEditedBy()->getDisplayName();
			
			$form->disable();
		}
		//Lock the record for editing if it isn't a new record.
    	elseif (isset($osw) && !is_null($osw))
		{
				$oswid = $osw->getOSWID();
				//If this isn't a new item, then mark it as 'checked out'
				if (!is_null($oswid) && OnSiteWorkDAO::getOnSiteWorkDAO()->oswExists($oswid))
				{
					RecordNamespace::getCurrentOSW()->setIsBeingEdited(TRUE);
					RecordNamespace::getCurrentOSW()->setIsBeingEditedByID($identity[PeopleDAO::PERSON_ID]);
				}

		}
		$this->view->form = $form;
		//OSW info
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('recordnumber')->set($recordnumberlabel);
		//Display the messages on the disabled forms (if any exist)
		$this->view->placeholder('oswdisabledmessage')->set($oswdisabledmessage);

    }
    
	/*
     * Builds an array to display the data on the record screen.
     * 
     * @param OnSiteWork item 
     * @return an array prepared for the form
     */
    private function buildOSWFormArray(OnSiteWork $item = NULL)
    {
    	if (!is_null($item))
    	{
		    $formarray = array(
	    		'hiddenoswid' => $item->getOSWID(),
	    		'projectselect' => $item->getProjectID(),
	    		'groupselect' => $item->getGroupID(),
	    		'purposeselect' => $item->getPurposeID(),
	    		'repositoryselect' => $item->getHomeLocationID(),
	    		'departmentselect' => $item->getDepartmentID(),
	    		'chargetoselect' => $item->getChargeToID(),
	    		'workstartdateinput' => $item->getDateRange()->getStartDate(),
	    		'workenddateinput' => $item->getDateRange()->getEndDate(),
	    		'formatselect' => $item->getFormatID(),
	    		'titleinput' => $item->getTitle(),
	    		'proposedhours' => $item->getProposedHours(),
	    		'commentstextarea' => $item->getComments(),
		    	'curatorselect' => $item->getCuratorID()
	    	);
    	}
    	else 
    	{
    		$formarray = array();
    	}
    	return $formarray;
    }
    
	/*
     * Builds an array to display the data on the record screen.
     * 
     * @param Report item 
     * @param Item item 
     * @return an array prepared for the form
     */
    private function buildOSWReportFormArray(Report $report = NULL)
    {
    	if (!is_null($report))
    	{
	    	$formarray = array(
	    		'actualhours' => $report->getTotalHours(),
	    		'worklocationselect' => $report->getWorkLocation(),
	    		'workdescriptiontextarea' => $report->getTreatment()
	    	);
    	}
    	else 
    	{
    		$formarray = array();
    	}
    	return $formarray;
    }
    
	/**
	 * Show the status page
	 */
    public function recordstatusAction() 
    {
      	$recordnumber = $this->getRequest()->getParam('recordnumber');
		
      	$form = $this->getRecordStatusForm();
      	
	    $item = RecordNamespace::getCurrentItem(); 
	    $currentstatus = "No Status"; 	
	    $proposalapproval = "Not Yet Submitted for Approval";
	    $title = 'Record Status';
	    if (isset($recordnumber))
      	{
      		$title = 'Record Status for Record #' . $recordnumber;
      		if (!isset($item) || $item->getItemID() != $recordnumber)
			{
				$item = ItemDAO::getItemDAO()->getItem($recordnumber);
				RecordNamespace::setOriginalItem(ItemDAO::getItemDAO()->getItem($recordnumber));
			}
			
			if (!is_null($item))
			{
				$auth = Zend_Auth::getInstance();
				$identity = $auth->getIdentity();
				$accesslevel = $identity[PeopleDAO::ACCESS_LEVEL];
				$userrepository = $identity[LocationDAO::LOCATION_ID];
		
				//If the access level is a repository access level and this record doesn't belong to their repostiroy,
				//redirect them to the error page.
				if (($accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY || $accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN) 
					&& $item->getHomeLocationID() != $userrepository)
					{
						$this->_helper->redirector('recordnotinrepository', 'record', NULL, array('recordnumberlabel' => $recordnumber));
					}
					
				RecordNamespace::setCurrentItem($item);
				$formarray = $this->buildRecordStatusFormArray($item);
				$form->populate($formarray);
				$currentstatus = $item->getItemStatus();
				//Add more detail to some stati
				switch ($currentstatus)
				{
					case ItemDAO::ITEM_STATUS_LOGGED_IN:
						$loc = LocationDAO::getLocationDAO()->getLocation($item->getFunctions()->getLogin()->getTransferTo())->getLocation();
						$storage = $item->getStorage();
						if (!empty($storage))
						{
							$loc .= ' (' . $storage . ') ';
						}
						$currentstatus = "Logged in, at " . $loc;
						break;
					case ItemDAO::ITEM_STATUS_TEMP_OUT:
						$loc = LocationDAO::getLocationDAO()->getLocation($item->getFunctions()->getTemporaryTransfer()->getTransferTo())->getLocation();
						$currentstatus = "Temporarily Transferred to " . $loc;
						break;
					case ItemDAO::ITEM_STATUS_DONE:
						if ($item->getManuallyClosed())
						{
							$zendcloseddate = new Zend_Date($item->getManuallyClosedDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
							$closeddate = $zendcloseddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
							$currentstatus = "Manually closed on " . $closeddate;
						}
						else
						{
							$loc = LocationDAO::getLocationDAO()->getLocation($item->getFunctions()->getLogout()->getTransferTo())->getLocation();
							$currentstatus = "Logged out and transferred to " . $loc;
						}
						break;
				}
				$proposalapproval = ProposalApprovalDAO::getProposalApprovalDAO()->getProposalApprovalStatus($item->getItemID());
				if (is_null($proposalapproval))
				{
					$proposalapproval = "Not Yet Submitted for Approval";
				}
				
			}
      	}
      	
      	$this->view->form = $form;
      	$this->view->placeholder('recordstatustitle')->set($title);
		$this->view->placeholder("currentstatus")->set($currentstatus);
    	$this->view->placeholder("proposalapproval")->set($proposalapproval);
    }
    
	
	
    public function recordactivityAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$item = RecordNamespace::getCurrentItem();
		
	    function activityCompare($a, $b)
		{
			$zend_date_a = new Zend_Date(substr($a["Date"], 0, 10), ACORNConstants::$ZEND_DATE_FORMAT);		
    		$zend_date_b = new Zend_Date(substr($b["Date"], 0, 10), ACORNConstants::$ZEND_DATE_FORMAT);		

			$compare = $zend_date_a->compare($zend_date_b, ACORNConstants::$ZEND_INTERNAL_DATETIME_FORMAT);
			if ($compare == 0)
			{
				$weight_a = $a['Weight'];
				$weight_b = $b['Weight'];
				if ($weight_a != $weight_b)
				{
					$compare = $weight_a < $weight_b ? -1 : 1;
				}
			}
			return $compare;
		}
		
    	if (isset($item))
    	{
    		$activity = $item->getActivity();
    		$approval = $item->getFunctions()->getProposal()->getProposalApproval();
    		 
    		if (!is_null($approval))
    		{
    			$historyitems = $approval->getHistoryAsActivity();
    			$activity = array_merge($activity, $historyitems);
			}
			usort($activity, "activityCompare");
    	}
    	else 
    	{
    		$activity = array();
    	}
    	$retval = Zend_Json::encode(array('Result' => $activity));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
/*
     * Builds an array to display the data on the record status screen.
     * 
     * @param Item item 
     * @return an array prepared for the form
     */
    private function buildRecordStatusFormArray(Item $item)
    {
    	$callnumbers = $item->getCallNumbers();
    	$stringcallnumbers = implode("; ", $callnumbers);
    	
    	$project = NULL;
		if (!is_null($item->getProjectID()))
		{
			$project = ProjectDAO::getProjectDAO()->getProject($item->getProjectID())->getProjectName();
		}
		
    	$group = NULL;
		if (!is_null($item->getGroupID()))
		{
			$group = GroupDAO::getGroupDAO()->getGroup($item->getGroupID())->getGroupName();
		}
		
		$coordinator = NULL;
		if (!is_null($item->getCoordinatorID()))
		{
			$coordinator = PeopleDAO::getPeopleDAO()->getPerson($item->getCoordinatorID())->getDisplayName();
		}
		
    	$curator = NULL;
		if (!is_null($item->getCuratorID()))
		{
			$curator = PeopleDAO::getPeopleDAO()->getPerson($item->getCuratorID())->getDisplayName();
		}

		$repository = NULL;
		if (!is_null($item->getHomeLocationID()))
		{
			$repository = LocationDAO::getLocationDAO()->getLocation($item->getHomeLocationID())->getLocation();
		}
		
		$department = NULL;
		if (!is_null($item->getDepartmentID()))
		{
			$department = DepartmentDAO::getDepartmentDAO()->getDepartment($item->getDepartmentID())->getDepartmentName();
		}
		
		$chargeto = $item->getChargeToName();
		
		//Use the final counts if they exist
    	if (!is_null($item->getFunctions()->getReport()->getPrimaryKey()))
    	{
    		$counts = $item->getFunctions()->getReport()->getCounts();
    	}
    	//Otherwise, use the initial counts
    	else 
    	{
    		$counts = $item->getInitialCounts();
    	}
    	$stringnumberofitems = $counts->toString();
    	
    	$formarray = array(
    		'callnumberinput' => $stringcallnumbers,
    		'titleinput' => $item->getTitle(),
    		'authorinput' => $item->getAuthorArtist(),
    		'dateofobjectinput' => $item->getDateOfObject(),
    		'numberofitemsinput' => $stringnumberofitems,
    		'repositoryinput' => $repository,
    		'departmentinput' => $department,
    		'chargetoinput' => $chargeto,
    		'curatorinput' => $curator,
    		'projectinput' => $project,
    		'groupinput' => $group,
    		'coordinatorinput' => $coordinator,
    		'commentstextarea' => $item->getComments(),
    		'expecteddateofreturninput' => $item->getExpectedDateOfReturn(),
	    	'insuranceinput' => $item->getInsuranceValue(),
    		'repositorymemotextarea' => $item->getFundMemo()
    	);
    	
    	return $formarray;
    }

    
	public function addtotransferlistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	if ($this->_request->isPost())
		{
			$listname = $this->getRequest()->getParam("transferlistname", TransferNamespace::LOGIN);
			if ($listname == TransferNamespace::LOGIN)
			{
				$this->_forward('addtotransferlist', 'recordlogin');
			}
			elseif ($listname == TransferNamespace::LOGOUT)
			{
				$this->_forward('addtotransferlist', 'recordlogout');
			}
			elseif ($listname == TransferNamespace::TEMP_TRANSFER)
			{
				$this->_forward('addtotemptransferlist', 'recordtemptransfer');
			}
			elseif ($listname == TransferNamespace::TEMP_TRANSFER_RETURN)
			{
				$this->_forward('addtotemptransferreturnlist', 'recordtemptransfer');
			}
    	}
		
    }
    
    public function removefromtransferlistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$listname = $this->getRequest()->getParam("listname");
    	$itemID = $this->getRequest()->getParam("itemID");
    	if (isset($itemID) && isset($listname))
    	{
    		TransferNamespace::removeFromTransferList($listname, $itemID);
    	}
    }
    
    public function deleterecordAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$item = RecordNamespace::getCurrentItem();
    	if (isset($item))
    	{
    		ItemDAO::getItemDAO()->deleteItem($item);
    	}
    	$this->_helper->redirector('index', 'user');
    }
    
    /**
     * Manually closes the record.  This is used if the item is not
     * going to come to WPC.  Otherwise, the logout dates give the item
     * a status of done.
     */
    public function markrecorddoneAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	 
    	$item = RecordNamespace::getCurrentItem();
    	$paramArray = NULL;
    	if (isset($item) && !is_null($item->getItemID()))
    	{
    		$success = ItemDAO::getItemDAO()->markAsManuallyClosed($item);
    		if ($success)
    		{
    			//Lock the item
    			$item->setUnlocked(FALSE);
    			RecordNamespace::setCurrentItem($item);
    		}
    		$paramArray = array('recordnumber' => $item->getItemID());
    	}
    	$this->_helper->redirector('index', 'record', NULL, $paramArray);
    }

    public function clearitemeditsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$itemid = $this->getRequest()->getParam('itemID');
    	if (isset($itemid))
    	{
	    	$item = ItemDAO::getItemDAO()->getItem($itemid);
			if (isset($item) && !is_null($item) && $item->isEditable())
			{
				ItemDAO::getItemDAO()->setEditStatus($item, FALSE);
			}
    	}
    }
    
	public function clearosweditsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$oswid = $this->getRequest()->getParam('oswID');
    	if (isset($oswid))
    	{
	    	$osw = OnSiteWorkDAO::getOnSiteWorkDAO()->getOSW($oswid);
			if (isset($osw) && !is_null($osw) && $osw->isEditable())
			{
				OnSiteWorkDAO::getOnSiteWorkDAO()->setEditStatus($osw, FALSE);
			}
    	}
    }
    
	/**
	 * Finds the status of the current record
	 */
    public function findcurrentrecordstatusAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$item = RecordNamespace::getCurrentItem();
		$status = ItemDAO::ITEM_STATUS_PENDING;
		if (isset($item))
		{
			$status = $item->getItemStatus();
		}
		
    	//Only use the results
    	$result = array('Status' => $status);
    	$retval = Zend_Json::encode($result);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	/**
	 * Finds the list of records for the current user
	 */
    public function findcurrentuserrecordsAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//$userrecords = ItemDAO::getItemDAO()->getCurrentUserItemIDs();
		$userrecords = CombinedRecordsDAO::getCombinedRecordsDAO()->getCurrentUserRecordIDs();
		
    	//Only use the results
    	$resultset = array('Result' => $userrecords);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	public function resetidentificationAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$olditem = RecordNamespace::getOriginalItem();
    	if (isset($olditem))
    	{
    		$functions = RecordNamespace::getCurrentItem()->getFunctions();
    		$currentitem = clone $olditem;
    		//Put back the functions since we are only changing the identification
    		$currentitem->setFunctions($functions);
    	}
    	else 
    	{
    		$currentitem = new Item();
    	}
    	RecordNamespace::setCurrentItem($currentitem);
    }

    public function resetproposalAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$item = RecordNamespace::getCurrentItem();
    	$olditem = RecordNamespace::getOriginalItem();
    	if (isset($olditem))
    	{
    		$oldproposal = $olditem->getFunctions()->getProposal();
    		//Only change the report
    		$item->getFunctions()->setProposal($oldproposal);
    	}
    	else
    	{
    		$item = new Item();
    	}
    	RecordNamespace::setCurrentItem($item);
    }

	public function resetreportAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$item = RecordNamespace::getCurrentItem();
    	$olditem = RecordNamespace::getOriginalItem();
    	if (isset($olditem))
    	{
    		$oldreport = $olditem->getFunctions()->getReport();
    		//Only change the report
    		$item->getFunctions()->setReport($oldreport);
    	}
    	else 
    	{
    		$item = new Item();
    	}
    	RecordNamespace::setCurrentItem($item);
    }

    public function recordnotinrepositoryAction()
    {
    	$recordnumber = $this->getRequest()->getParam('recordnumberlabel');
    	$this->view->placeholder('recordnumberlabel')->set($recordnumber);
    }
    
    public function gotocurrentrecordAction()
    {
    	$item = RecordNamespace::getCurrentItem();
    	if (isset($item))
    	{
    		$recordnumber = $item->getItemID();
    		$this->_helper->redirector('index', NULL, NULL, array('recordnumber' => $recordnumber));
    	}
    	else 
    	{
    		$this->_helper->redirector('newitem');
    	}
    }
    
    public function setrendertabAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		$rendertab = $this->getRequest()->getParam('renderTab', RecordNamespace::IDENTIFICATION);
		RecordNamespace::setRenderTab($rendertab);
    }
    
    /*
     * Returns the data needed to display in the record dialog.
     */
    public function getrecorddialogdataAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		$recordnumber = $this->getRequest()->getParam('recordID');
		$recordType = $this->getRequest()->getParam('recordType');
		
		$record = CombinedRecordsDAO::getCombinedRecordsDAO()->getRecord($recordnumber, $recordType);
		
		$calllist = $record->getCallNumbers();
		$callnumbers = implode(', ', $calllist);
		
		$approval = NULL;
		if ($record->getRecordType() == 'Item')
		{
			$approval = ProposalApprovalDAO::getProposalApprovalDAO()->getProposalApprovalStatus($record->getRecordID());
		}
		$data = array(
			"Author" => $record->getAuthorArtist(),
			"Title" => $record->getTitle(),
			"CallNumbers" => $callnumbers,
			"Status" => $record->getItemStatus(),
			"ProposalApproval" => $approval
		);
		
		$jsonstring = Zend_Json::encode($data);
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($jsonstring);
    }
    
    public function cantransferrecordAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		$selectedloc = $this->getRequest()->getParam('selectedloc');
    	$record = RecordNamespace::getCurrentItem();
		$tab = RecordNamespace::getRenderTab();
		
		$validmatch = FALSE;
    	if (isset($record) && $record->isEditable())
		{
			switch ($tab)
			{
			//Login
			case RecordNamespace::LOGIN:
				
				//Pending status or logged in status
				$status = $record->getItemStatus();
				$validmatch =  $status == ItemDAO::ITEM_STATUS_PENDING || $status == ItemDAO::ITEM_STATUS_LOGGED_IN;
				if ($validmatch)
				{
					$list = TransferNamespace::getTransferList(TransferNamespace::LOGIN);
					$listexists = isset($list) && count($list) > 0;
					$listdepartment = TransferNamespace::getTransferDepartmentID(TransferNamespace::LOGIN);
					$listcurator = TransferNamespace::getTransferCuratorID(TransferNamespace::LOGIN);
					
					if ($listexists)
					{
						$transferlistloc = TransferNamespace::getTransferFrom(TransferNamespace::LOGIN);
						$validmatch = $validmatch && $transferlistloc == $selectedloc;
					}
					
					//Home loc = selected loc
					$validmatch = $validmatch && $record->getHomeLocationID() == $selectedloc;
					//Department = login list department
					if ($listexists)
					{
						$validmatch = $validmatch && $record->getDepartmentID() == $listdepartment;
					}
					//Curator = login list curator
					if ($listexists)
					{
						$validmatch = $validmatch && $record->getCuratorID() == $listcurator;
					}
					$message = $record->getDisableLoginMessage();
					$validmatch = $validmatch && empty($message);
				}
				break;
			//Logout
			case RecordNamespace::LOGOUT:
				
				//Logged In status or Done status (can add them to a transfer list)
				$status = $record->getItemStatus();
				$validmatch = $status == ItemDAO::ITEM_STATUS_LOGGED_IN || $status == ItemDAO::ITEM_STATUS_DONE;
				if ($validmatch)
				{
					$listdepartment = TransferNamespace::getTransferDepartmentID(TransferNamespace::LOGOUT);
					$listcurator = TransferNamespace::getTransferCuratorID(TransferNamespace::LOGOUT);
					$list = TransferNamespace::getTransferList(TransferNamespace::LOGOUT);
					$listexists = isset($list) && count($list) > 0;

					if ($listexists)
					{
						$transferlistloc = TransferNamespace::getTransferFrom(TransferNamespace::LOGOUT);
						$validmatch = $validmatch && $transferlistloc == $selectedloc;
					}
					
					//Logged in transfer to = selected loc
					$validmatch = $validmatch && $record->getFunctions()->getLogin()->getTransferTo() == $selectedloc;
					//Department = login list department
					if ($listexists)
					{
						$validmatch = $validmatch && $record->getDepartmentID() == $listdepartment;
					}
					//Curator = login list curator
					if ($listexists)
					{
						$validmatch = $validmatch && $record->getCuratorID() == $listcurator;
					}
					$message = $record->getDisableLogoutMessage();
					$validmatch = $validmatch && empty($message);
				}
				break;
			//Temp Transfer
			case RecordNamespace::TEMP_TRANSFER:
				
				//Logged in status
				$validmatch = $record->getItemStatus() == ItemDAO::ITEM_STATUS_LOGGED_IN;
				if ($validmatch)
				{
					$listdepartment = TransferNamespace::getTransferDepartmentID(TransferNamespace::TEMP_TRANSFER);
					$listcurator = TransferNamespace::getTransferCuratorID(TransferNamespace::TEMP_TRANSFER);
					$list = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER);
					$listexists = isset($list) && count($list) > 0;
					
					if ($listexists)
					{
						$transferlistloc = TransferNamespace::getTransferFrom(TransferNamespace::TEMP_TRANSFER);
						$validmatch = $validmatch && $transferlistloc == $selectedloc;
					}
					
					//Logged in transfer to = selected loc
					$validmatch = $validmatch && $record->getFunctions()->getLogin()->getTransferTo() == $selectedloc;
					//Department = login list department
					if ($listexists)
					{
						$validmatch = $validmatch && $record->getDepartmentID() == $listdepartment;
					}
					//Curator = login list curator
					if ($listexists)
					{
						$validmatch = $validmatch && $record->getCuratorID() == $listcurator;
					}
					$message = $record->getDisableTempTransferMessage();
					$validmatch = $validmatch && empty($message);
				}
				break;
			//Temp Transfer Return
			case RecordNamespace::TEMP_TRANSFER_RETURN:
				//Temp out status
				$validmatch = $record->getItemStatus() == ItemDAO::ITEM_STATUS_TEMP_OUT;
				if ($validmatch)
				{
					$listdepartment = TransferNamespace::getTransferDepartmentID(TransferNamespace::TEMP_TRANSFER_RETURN);
					$listcurator = TransferNamespace::getTransferCuratorID(TransferNamespace::TEMP_TRANSFER_RETURN);
					$list = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER_RETURN);
					$listexists = isset($list) && count($list) > 0;
					
					if ($listexists)
					{
						$transferlistloc = TransferNamespace::getTransferFrom(TransferNamespace::TEMP_TRANSFER_RETURN);
						$validmatch = $validmatch && $transferlistloc == $selectedloc;
					}
					
					//temp transfer to = selected loc
					$validmatch = $validmatch && $record->getFunctions()->getTemporaryTransfer()->getTransferTo() == $selectedloc;
					//Department = login list department
					if ($listexists)
					{
						$validmatch = $validmatch && $record->getDepartmentID() == $listdepartment;
					}
					//Curator = login list curator
					if ($listexists)
					{
						$validmatch = $validmatch && $record->getCuratorID() == $listcurator;
					}
					$message = $record->getDisableTempTransferReturnMessage();
					$validmatch = $validmatch && empty($message);
				}
				break;
			}
		}
		$jsonstring = Zend_Json::encode(array('TransferAllowed' => $validmatch));
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($jsonstring);
    }
    
    public function unlockrecordAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$item = RecordNamespace::getCurrentItem();
		$date = $this->getRequest()->getParam('unlockuntildateinput');
		$zenddate = new Zend_Date($date, ACORNConstants::$ZEND_DATE_FORMAT);		
    	$date = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);

		$success = UnlockedItemsDAO::getUnlockedItemsDAO()->unlockItem($item->getItemID(), $date);
		
		if ($success)
		{
			$item = ItemDAO::getItemDAO()->getItem($item->getItemID());
			RecordNamespace::setCurrentItem($item);
			//If the item is part of a group, the group may have to be updated in the namespace.
			if (!is_null($item->getGroupID()))
			{
				$currentgroup = GroupNamespace::getCurrentGroup();
				if (isset($currentgroup) && !is_null($currentgroup) && $item->getGroupID() == $currentgroup->getPrimaryKey())
				{
					$group = GroupDAO::getGroupDAO()->getGroup($item->getGroupID());
					GroupNamespace::setCurrentGroup($group);					
				}
			}
		}
		
		$jsonstring = Zend_Json::encode(array('UnlockSuccessful' => $success));
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($jsonstring);
    }
    
}
?>

