<?php

/**
 * GroupController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class GroupController extends Zend_Controller_Action
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
        $auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		//Repository and repository admin don't have access to this.
		if (!Zend_Auth::getInstance()->hasIdentity() || $identity[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_REPOSITORY || $identity[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN) 
        {
            $this->_helper->redirector('index', 'index');
        }
    }
    
	public function newgroupAction()
    {
    	GroupNamespace::clearRenderTab();
    	GroupNamespace::clearCurrentGroup();
    	GroupNamespace::clearOriginalGroup();
    	GroupNamespace::setSaveStatus(GroupNamespace::SAVE_STATE_IDLE);
    	GroupNamespace::setCurrentGroup(new Group());
    	$this->_helper->redirector('index');
    }
	
	/**
	 * The default action - show the home page
	 */
    public function indexAction() 
    {
		RecordNamespace::clearAll();
    	$form = $this->getGroupForm();
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		$groupnumber = $this->getRequest()->getParam('groupnumber');
		
		//This gets the record created by info
		//Either the current user or the 
    	$createdby = NULL;
    	$createdate = NULL;
    	
    	$groupnumberlabel = "New Group";
    	
    	$identificationdisabledmessage = "";
    	$logindisabledmessage = "";
    	$logoutdisabledmessage = "";
    	$proposaldisabledmessage = "";
    	$reportdisabledmessage = "";
    	$filesdisabledmessage = "";
    	$temptransferdisabledmessage = "";
    	$temptransferreturndisabledmessage = "";
    	
    	$group = GroupNamespace::getCurrentGroup();
    	$formdata = $this->buildTransferDefaultsArray();
		if (isset($groupnumber))
		{
			if (!isset($group) || $group->getPrimaryKey() != $groupnumber)
			{
				$group = GroupDAO::getGroupDAO()->getGroup($groupnumber);
				GroupNamespace::setCurrentGroup($group);
				GroupNamespace::setOriginalGroup(GroupDAO::getGroupDAO()->getGroup($groupnumber));
				GroupNamespace::setSaveStatus(GroupNamespace::SAVE_STATE_IDLE);
				
				$userid = $identity[PeopleDAO::PERSON_ID];
				$tempfilepath = $_SERVER['DOCUMENT_ROOT'] . "/userfiles/temp" . $userid;
				$this->clearTemporaryFiles($tempfilepath);
			}
			
			if (!is_null($group))
			{
				$groupnumberlabel = "Group " . $group->getGroupName();
				GroupNamespace::setCurrentGroup($group);
				
				//Get the record creation information
				$audittrailitems = $group->getAuditTrail("Insert");
				if (!empty($audittrailitems))
				{
					$audittrailitem = current($audittrailitems);
					$createdate = $audittrailitem->getDate();
					$createdby = $audittrailitem->getPerson()->getDisplayName();
				}
				$formdata['groupinput'] = $group->getGroupName();
				$formdata['hiddenfilepkid'] = $group->getPrimaryKey();
			}
		}
		else 
		{
			$formdata = $this->buildDefaultsArray();
		}
		
		//If the create date and created by haven't been populated,
		//use the current user.
		if (is_null($createdate) && is_null($createdby))
		{
			$createdby = $identity[PeopleDAO::DISPLAY_NAME];
			$zenddate = new Zend_Date();
			$createdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);		
		}
		
		
		/*if (isset($group) && !is_null($group) && !$group->isEditable())
		{
			$message = "This record is currently being edited by " . $group->getIsBeingEditedBy()->getDisplayName();
				
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
		{*/
			//If the group was set, see if there are any forms to disable.
			if (isset($group) && !is_null($group))
			{
				$groupid = $group->getPrimaryKey();
				//If this isn't a new group, then mark it as 'checked out'
				if (!is_null($groupid) && GroupDAO::getGroupDAO()->groupExists($groupid))
				{
					GroupNamespace::getCurrentGroup()->setIsBeingEdited(TRUE);
				}
		
				$identificationdisabledmessage = $group->getDisableIdentificationMessage();
				$logindisabledmessage = $group->getDisableLoginMessage();
				$logoutdisabledmessage = $group->getDisableLogoutMessage();
				$proposaldisabledmessage = $group->getDisableProposalMessage();
				$reportdisabledmessage = $group->getDisableReportMessage();
				$filesdisabledmessage = $group->getDisableFilesMessage();
				$temptransferdisabledmessage = $group->getDisableTempTransferMessage();
				$temptransferreturndisabledmessage = $group->getDisableTempTransferReturnMessage();
			}
				
			if (!empty($identificationdisabledmessage))
			{
				$groupidentificationform = $form->getSubForm('groupidentificationform');
				$groupidentificationform->disable(array('unlockuntildateinput'));
			}
			if (!empty($logindisabledmessage))
			{
				$grouploginform = $form->getSubForm('grouploginform');
				$grouploginform->disable();
			}
			if (!empty($proposaldisabledmessage))
			{
				$groupproposalform = $form->getSubForm('groupproposalform');
				$groupproposalform->disable();
			}
			if (!empty($reportdisabledmessage))
			{
				$groupreportform = $form->getSubForm('groupreportform');
				$groupreportform->disable();
			}
			if (!empty($logoutdisabledmessage))
			{
				$grouplogoutform = $form->getSubForm('grouplogoutform');
				$grouplogoutform->disable();
			}
			if (!empty($filesdisabledmessage))
			{
				$groupfileform = $form->getSubForm('groupfileform');
				$groupfileform->disable();
			}
			if (!empty($temptransferdisabledmessage))
			{
				$grouptemptransferform = $form->getSubForm('grouptemporarytransferform');
				$grouptemptransferform->disable();
			}
			if (!empty($temptransferreturndisabledmessage))
			{
				$grouptemptransferreturnform = $form->getSubForm('grouptemporarytransferreturnform');
				$grouptemptransferreturnform->disable();
			}
				
	//	}

		$unlockeduntildate = '';
		//Display the messages on the disabled forms (if any exist)
		//If the identification message is blank, then see if the group is currently unlocked.
		if (empty($identificationdisabledmessage) && isset($group) && !is_null($group))
		{
			if ($group->isUnlocked())
			{
				$items = $group->getRecords();
				$expirationdate = NULL;
				//Get the earliest expiration date.
				foreach ($items as $item)
				{
					$expdate = UnlockedItemsDAO::getUnlockedItemsDAO()->getExpirationDate($item->getItemID());
					$zenddate = new Zend_Date($expdate, ACORNConstants::$ZEND_DATE_FORMAT);
					if ($item->isUnlocked() && (is_null($expirationdate) || $zenddate->isEarlier($expirationdate)))
					{
						$expirationdate = new Zend_Date($expdate, ACORNConstants::$ZEND_DATE_FORMAT);
					}
				}
				if (!is_null($expirationdate))
				{
					$unlockeduntildate = $expirationdate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
					$identificationdisabledmessage = "Group is Unlocked for Editing until " . $expirationdate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
				}
			}
		}
		
		if (empty($unlockeduntildate))
		{
			$currentzenddate = new Zend_Date();
    		$currentzenddate = $currentzenddate->addDay(30);
			$unlockeduntildate = $currentzenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
			
		}
		$formdata['unlockuntildateinput'] = $unlockeduntildate;
		$form->populate($formdata);
		$this->view->form = $form;
		
		//Group info
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('groupnumber')->set($groupnumberlabel);
		
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
		
		$currentzenddate = new Zend_Date();
		
		$currentzenddate = $currentzenddate->addday(30);
		$futuredate = $currentzenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
		
		$zenddate = new Zend_Date();
		$proposaldate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
		$reportdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
		$groupdate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
		$formarray = array(
	    		'coordinatorselect' => $identity[PeopleDAO::PERSON_ID],
	    		'workassignedtoselect' => $identity[PeopleDAO::PERSON_ID],
				'coordinatorinput' => $identity[PeopleDAO::DISPLAY_NAME],
	    		'proposaldateinput' => $proposaldate,
	    		'proposalbyselect' => $identity[PeopleDAO::PERSON_ID],
				'reportdateinput' => $reportdate,
	    		'reportbyselect' => $identity[PeopleDAO::PERSON_ID],
				'groupinput' => $groupdate,
				'unlockuntildateinput' => $futuredate
	    	);
	    $transferarray = $this->buildTransferDefaultsArray();
	    $formarray = array_merge($formarray, $transferarray);
	    return $formarray;
	}
	
	private function buildTransferDefaultsArray()
	{
		$loginarray = $this->buildLoginDefaultsArray();
		$logoutarray = $this->buildLogoutDefaultsArray();
		$temptransferarray = $this->buildTempTransferDefaultsArray();
		$tempreturnarray = $this->buildTempTransferReturnDefaultsArray();
		
		$formarray = array_merge($loginarray, $logoutarray, $temptransferarray, $tempreturnarray);
		
	    return $formarray;
	}
	
	private function buildLoginDefaultsArray()
	{
		$transferfrom = NULL;
		$transferto = NULL;

		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		$zenddate = new Zend_Date();
		$transferdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
		$transferby = $identity[PeopleDAO::PERSON_ID];
		
		$formarray = array(
	    	'loginbyselect' => $transferby,
	    	'logindateinput' => $transferdate,
			'loginfromselect' => $transferfrom,
			'logintoselect' => $transferto
		);
		return $formarray;
	}
	
	private function buildLogoutDefaultsArray()
	{
		$transferfrom = NULL;
		$transferto = NULL;
		$auth = Zend_Auth::getInstance();
		$identity = $auth->getIdentity();
		$zenddate = new Zend_Date();
		$transferdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
		$transferby = $identity[PeopleDAO::PERSON_ID];
		
		$formarray = array(
	    	'logoutbyselect' => $transferby,
	    	'logoutdateinput' => $transferdate,
			'logoutfromselect' => $transferfrom,
			'logouttoselect' => $transferto
		);
		return $formarray;
	}
	
	private function buildTempTransferDefaultsArray()
	{
		$transferfrom = NULL;
		$transferto = NULL;
		$zenddate = new Zend_Date();
		$transferdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
		
		$formarray = array(
	    	'transferdateinput' => $transferdate,
			'transferfromselect' => $transferfrom,
			'transfertoselect' => $transferto
		);
		return $formarray;
	}
	
	private function buildTempTransferReturnDefaultsArray()
	{
		$transferfrom = NULL;
		$transferto = NULL;
		$zenddate = new Zend_Date();
		$transferdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
		
		$formarray = array(
	    	'transferreturndateinput' => $transferdate,
			'transferreturnfromselect' => $transferfrom,
			'transferreturntoselect' => $transferto
		);
		return $formarray;
	}
    
	/**
	 * Finds the list of records for the current group
	 */
    public function findgrouprecordsAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$group = GroupNamespace::getCurrentGroup();
		if (isset($group) && !is_null($group))
		{
			$records = $group->getRecords(TRUE);
		}
		else 
		{
			$records = array();
		}
		
		$grouprecords = array();
		foreach ($records as $record)
		{
			$callnumbers = $record->getCallNumbers();
			$stringcallnumbers = implode("; ", $callnumbers);
			$counts = $record->getInitialCounts();
			$vol = $counts->getVolumeCount();
			$sheet = $counts->getSheetCount();
			$photo = $counts->getPhotoCount();
			$other = $counts->getOtherCount();
			$boxes = $counts->getBoxCount();
				
			$finalcounts = $record->getFunctions()->getReport()->getCounts();
			$finalvol = $finalcounts->getVolumeCount();
			$finalsheet = $finalcounts->getSheetCount();
			$finalphoto = $finalcounts->getPhotoCount();
			$finalother = $finalcounts->getOtherCount();
			$finalhousing = $finalcounts->getHousingCount();
			$finalbox = $finalcounts->getBoxCount();
				
			array_push($grouprecords, array(
				ItemDAO::ITEM_ID => $record->getItemID(),
				"CallNumbers" => $stringcallnumbers,
				ItemDAO::TITLE => $record->getTitle(),
				ItemDAO::AUTHOR_ARTIST => $record->getAuthorArtist(),
				ItemDAO::DATE_OF_OBJECT => $record->getDateOfObject(),
				"VolumeCount" => $vol,
				"SheetCount" => $sheet,
				"PhotoCount" => $photo,
				"OtherCount" => $other,
				"BoxCount" => $boxes,
				ItemDAO::COMMENTS => $record->getComments(),
				ProposalDAO::EXAM_DATE => $record->getFunctions()->getProposal()->getExamDate(),
				ProposalDAO::DIMENSION_UNIT => $record->getFunctions()->getProposal()->getDimensions()->getUnit(),
				ProposalDAO::HEIGHT => $record->getFunctions()->getProposal()->getDimensions()->getHeight(),
				ProposalDAO::WIDTH => $record->getFunctions()->getProposal()->getDimensions()->getWidth(),
				ProposalDAO::THICKNESS => $record->getFunctions()->getProposal()->getDimensions()->getThickness(),
				ReportDAO::REPORT_DATE => $record->getFunctions()->getReport()->getReportDate(),
				"TotalHours" => $record->getFunctions()->getReport()->getTotalHours(),
				"ReportUnit" => $record->getFunctions()->getReport()->getDimensions()->getUnit(),
				"ReportHeight" => $record->getFunctions()->getReport()->getDimensions()->getHeight(),
				"ReportWidth" => $record->getFunctions()->getReport()->getDimensions()->getWidth(),
				"ReportThickness" => $record->getFunctions()->getReport()->getDimensions()->getThickness(),
				"FinalVolumeCount" => $finalvol,
				"FinalSheetCount" => $finalsheet,
				"FinalPhotoCount" => $finalphoto,
				"FinalOtherCount" => $finalother,
				"FinalHousingCount" => $finalhousing,
				"FinalBoxCount" => $finalbox
				));
		}
		
		//Only use the results
    	$resultset = array('Result' => $grouprecords);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
    /*
     * Removes an item from the group
     */
    public function removefromgroupAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		
		$itemid = $this->getRequest()->getParam('itemID');
		
		$group = GroupNamespace::getCurrentGroup();
		if (isset($itemid) && isset($group))
		{
			$item = $group->removeRecord($itemid);
			$item->setGroupID(NULL);
			ItemDAO::getItemDAO()->saveItemIdentification($item, $item);
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
		$this->view->placeholder("identificationerrormessage")->set($message);
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('groupnumber')->set($groupnumberlabel);
		
		$this->renderScript('group/index.phtml');
    }
    
    /*
     * Updates the record data that has been updated in the group record list.
     */
    public function updaterecorddataAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$itemid = $this->getRequest()->getParam('itemID');
    	$columnname = $this->getRequest()->getParam('column');
    	$newvalue = $this->getRequest()->getParam('newdata');
    	$group = GroupNamespace::getCurrentGroup();
    	
    	if (isset($itemid) && isset($group))
    	{
    		$records = $group->getRecords();
    		if (isset($records[$itemid]))
    		{
    			$record = $records[$itemid];
    			$updatedrecord = $this->updateRecord($record, $columnname, $newvalue);
    			if ($this->isIdentification($columnname))
    			{
    				$itemid = ItemDAO::getItemDAO()->saveItemIdentification($updatedrecord, $record);
    			}
    			elseif ($this->isProposal($columnname))
    			{
    				$proposalid = ProposalDAO::getProposalDAO()->saveItemProposal($updatedrecord->getFunctions()->getProposal(), $updatedrecord);
    				//Set the itemid to null if the proposal update failed.
    				if (is_null($proposalid))
    				{
    					$itemid = NULL;
    				}
    			}
    			elseif ($this->isReport($columnname))
    			{
    				$reportid = ReportDAO::getReportDAO()->saveItemReport($updatedrecord->getFunctions()->getReport(), $record->getFunctions()->getReport(), $updatedrecord);
    				//Set the itemid to null if the report update failed.
    				if (is_null($reportid))
    				{
    					$itemid = NULL;
    				}
    			}
    			//If the save was successful, then update the namespace
    			if (!is_null($itemid))
    			{
    				$records[$itemid] = $updatedrecord;
    				$group->setRecords($records);
    				GroupNamespace::setCurrentGroup($group);
    			}
    			//TODO: Otherwise, throw an error via json
    			else
    			{
    				Logger::log('Update record did not save', Zend_Log::ERR);
    			}
    		}
    	}
    }
    
    private function isIdentification($columnname)
    {
    	return in_array($columnname, array(
    		"CallNumbers",
    		ItemDAO::TITLE,
    		ItemDAO::AUTHOR_ARTIST,
    		ItemDAO::DATE_OF_OBJECT,
    		"VolumeCount",
    		"SheetCount",
    		"PhotoCount",
    		"BoxCount",
    		"OtherCount",
    		ItemDAO::COMMENTS,
    		ItemDAO::IDENTIFICATION_ID,
    	));
    }
    
	private function isProposal($columnname)
    {
    	return in_array($columnname, array(
    		ProposalDAO::EXAM_DATE,
    		ProposalDAO::DIMENSION_UNIT,
    		ProposalDAO::HEIGHT,
    		ProposalDAO::WIDTH,
    		ProposalDAO::THICKNESS
    	));
    }
    
	private function isReport($columnname)
    {
    	return in_array($columnname, array(
    		ReportDAO::REPORT_DATE,
    		"TotalHours",
    		"ReportUnit",
    		"ReportHeight",
    		"ReportWidth",
    		"ReportThickness",
    		"FinalVolumeCount",
    		"FinalSheetCount",
    		"FinalPhotoCount",
    		"FinalBoxCount",
    		"FinalOtherCount",
    		"FinalHousingCount"
    	));
    }
    
    /*
     * Sets the record information that changed
     * 
     * @return Item the updated item
     */
    private function updateRecord(Item $record, $columnname, $newvalue)
    {
    	$clonedrecord = clone $record;
    	switch ($columnname)
    	{
    		case "CallNumbers":
    			$callnumbers = explode(";", $newvalue);
    			$callnumbers = array_combine($callnumbers, $callnumbers);
    			$clonedrecord->setCallNumbers($callnumbers);
    			break;
    		case ItemDAO::TITLE:
    			$clonedrecord->setTitle($newvalue);
    			break;
    		case ItemDAO::AUTHOR_ARTIST:
    			$clonedrecord->setAuthorArtist($newvalue);
    			break;
    		case ItemDAO::DATE_OF_OBJECT:
    			$clonedrecord->setDateOfObject($newvalue);
    			break;
    		case "VolumeCount":
    			$clonedrecord->getInitialCounts()->setVolumeCount($newvalue);
    			break;
    		case "SheetCount":
    			$clonedrecord->getInitialCounts()->setSheetCount($newvalue);
    			break;
    		case "PhotoCount":
    			$clonedrecord->getInitialCounts()->setPhotoCount($newvalue);
    			break;
    		case "BoxCount":
    			$clonedrecord->getInitialCounts()->setBoxCount($newvalue);
    			break;
    		case "OtherCount":
    			$clonedrecord->getInitialCounts()->setOtherCount($newvalue);
    			break;
    		case ItemDAO::COMMENTS:
    			$clonedrecord->setComments($newvalue);
    			break;
    		case ProposalDAO::EXAM_DATE:
    			$clonedrecord->getFunctions()->getProposal()->setExamDate($newvalue);
    			break;
    		case ProposalDAO::DIMENSION_UNIT:
    			$clonedrecord->getFunctions()->getProposal()->getDimensions()->setUnit($newvalue);
    			break;
    		case ProposalDAO::HEIGHT:
    			$clonedrecord->getFunctions()->getProposal()->getDimensions()->setHeight($newvalue);
    			break;
    		case ProposalDAO::WIDTH:
    			$clonedrecord->getFunctions()->getProposal()->getDimensions()->setWidth($newvalue);
    			break;
    		case ProposalDAO::THICKNESS:
    			$clonedrecord->getFunctions()->getProposal()->getDimensions()->setThickness($newvalue);
    			break;
    		case ReportDAO::REPORT_DATE:
    			$clonedrecord->getFunctions()->getReport()->setReportDate($newvalue);
    			break;
    		case "ReportUnit":
    			$clonedrecord->getFunctions()->getReport()->getDimensions()->setUnit($newvalue);
    			break;
    		case "ReportHeight":
    			$clonedrecord->getFunctions()->getReport()->getDimensions()->setHeight($newvalue);
    			break;
    		case "ReportWidth":
    			$clonedrecord->getFunctions()->getReport()->getDimensions()->setWidth($newvalue);
    			break;
    		case "ReportThickness":
    			$clonedrecord->getFunctions()->getReport()->getDimensions()->setThickness($newvalue);
    			break;
    		case "FinalVolumeCount":
    			$clonedrecord->getFunctions()->getReport()->getCounts()->setVolumeCount($newvalue);
    			break;
    		case "FinalSheetCount":
    			$clonedrecord->getFunctions()->getReport()->getCounts()->setSheetCount($newvalue);
    			break;
    		case "FinalPhotoCount":
    			$clonedrecord->getFunctions()->getReport()->getCounts()->setPhotoCount($newvalue);
    			break;
    		case "FinalBoxCount":
    			$clonedrecord->getFunctions()->getReport()->getCounts()->setBoxCount($newvalue);
    			break;
    		case "FinalOtherCount":
    			$clonedrecord->getFunctions()->getReport()->getCounts()->setOtherCount($newvalue);
    			break;
    		case "FinalHousingCount":
    			$clonedrecord->getFunctions()->getReport()->getCounts()->setHousingCount($newvalue);
    			break;
    	}
		return $clonedrecord;
    }
    
    /*
     * Deletes the group and then redirects to the homepage.
     */
	public function deletegroupAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$group = GroupNamespace::getCurrentGroup();
    	if (isset($group))
    	{
    		GroupDAO::getGroupDAO()->deleteGroup($group);
    	}
    	GroupNamespace::clearCurrentGroup();
    	$this->_helper->redirector('index', 'user');
    }
    
	/**
	 * Finds the list of matching records for the current selected location
	 */
    public function findmatchinggrouprecordsAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		$tab = $this->getRequest()->getParam('tab', -1);
		$selectedloc = $this->getRequest()->getParam('selectedlocation');
		
		$group = GroupNamespace::getCurrentGroup();
		if (isset($group) && !is_null($group))
		{
			$records = $group->getRecords(TRUE);
		}
		else 
		{
			$records = array();
		}
		
		$listdepartment = TransferNamespace::getTransferDepartmentID(GroupNamespace::getRenderTab());
		$listcurator = TransferNamespace::getTransferCuratorID(GroupNamespace::getRenderTab());
		$list = TransferNamespace::getTransferList(GroupNamespace::getRenderTab());
		$listexists = isset($list) && count($list) > 0;
		if ($tab == -1)
		{
			switch(GroupNamespace::getRenderTab())
			{
				case TransferNamespace::LOGIN:
					$tab = 1;
					break;
				case TransferNamespace::LOGOUT:
					$tab = 4;
					break;
				case TransferNamespace::TEMP_TRANSFER:
					$tab = 6;
					break;
				case TransferNamespace::TEMP_TRANSFER_RETURN:
					$tab = 7;
					break;
					
			}
		}
		
		$matchingrecords = array();
		foreach ($records as $record)
		{
			$validmatch = FALSE;
			$message = "";
			if ($record->isEditable())
			{
				switch ($tab)
				{
					//Login
					case 1:
						//Pending status
						$validmatch = $record->getItemStatus() == ItemDAO::ITEM_STATUS_PENDING;
						if ($validmatch)
						{
							//Home loc = selected loc
							$validmatch = $validmatch && $record->getHomeLocationID() == $selectedloc;
							//Department = login list department
							if ($listexists)
							{
								$validmatch = $validmatch && $record->getDepartmentID == $listdepartment;
							}
							//Curator = login list curator
							if ($listexists)
							{
								$validmatch = $validmatch && $record->getCuratorID == $listcurator;
							}
							$message = $record->getDisableLoginMessage();
							$validmatch = $validmatch && empty($message);
						}
						break;
					//Logout
					case 4:
						//Logged In status
						$validmatch = $record->getItemStatus() == ItemDAO::ITEM_STATUS_LOGGED_IN;
						if ($validmatch)
						{
							//Logged in transfer to = selected loc
							$validmatch = $validmatch && $record->getFunctions()->getLogin()->getTransferTo() == $selectedloc;
							//Department = login list department
							if ($listexists)
							{
								$validmatch = $validmatch && $record->getDepartmentID == $listdepartment;
							}
							//Curator = login list curator
							if ($listexists)
							{
								$validmatch = $validmatch && $record->getCuratorID == $listcurator;
							}
							$message = $record->getDisableLogoutMessage();
							$validmatch = $validmatch && empty($message);
						}
						break;
					//Temp Transfer
					case 6:
						//Logged in status
						$validmatch = $record->getItemStatus() == ItemDAO::ITEM_STATUS_LOGGED_IN;
						if ($validmatch)
						{
							//Logged in transfer to = selected loc
							$validmatch = $validmatch && $record->getFunctions()->getLogin()->getTransferTo() == $selectedloc;
							//Department = login list department
							if ($listexists)
							{
								$validmatch = $validmatch && $record->getDepartmentID == $listdepartment;
							}
							//Curator = login list curator
							if ($listexists)
							{
								$validmatch = $validmatch && $record->getCuratorID == $listcurator;
							}
							$message = $record->getDisableTempTransferMessage();
							$validmatch = $validmatch && empty($message);
						}
						break;
					//Temp Transfer Return
					case 7:
						//Temp out status
						$validmatch = $record->getItemStatus() == ItemDAO::ITEM_STATUS_TEMP_OUT;
						if ($validmatch)
						{
							//temp transfer to = selected loc
							$validmatch = $validmatch && $record->getFunctions()->getTemporaryTransfer()->getTransferTo() == $selectedloc;
							//Department = login list department
							if ($listexists)
							{
								$validmatch = $validmatch && $record->getDepartmentID == $listdepartment;
							}
							//Curator = login list curator
							if ($listexists)
							{
								$validmatch = $validmatch && $record->getCuratorID == $listcurator;
							}
							$message = $record->getDisableTempTransferReturnMessage();
							$validmatch = $validmatch && empty($message);
						}
						break;
				}
			}

			if ($validmatch)
			{
				$callnumbers = $record->getCallNumbers();
				$stringcallnumbers = implode("; ", $callnumbers);
				$counts = $record->getInitialCounts();
				$vol = $counts->getVolumeCount();
				$sheet = $counts->getSheetCount();
				$photo = $counts->getPhotoCount();
				$box = $counts->getBoxCount();
				$other = $counts->getOtherCount();
			
				array_push($matchingrecords, array(
					ItemDAO::ITEM_ID => $record->getItemID(),
					"CallNumbers" => $stringcallnumbers,
					ItemDAO::TITLE => $record->getTitle(),
					ItemDAO::AUTHOR_ARTIST => $record->getAuthorArtist(),
					ItemDAO::DATE_OF_OBJECT => $record->getDateOfObject(),
					"VolumeCount" => $vol,
					"SheetCount" => $sheet,
					"PhotoCount" => $photo,
					"BoxCount" => $box,
					"OtherCount" => $other,
					ItemDAO::COMMENTS => $record->getComments()
				));
			}
		}
		
		//Only use the results
    	$resultset = array('Result' => $matchingrecords);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
    public function savegroupAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getGroupForm()->getSubForm("groupidentificationform");
			$formData = $this->_request->getPost();
			GroupNamespace::setRenderTab(GroupNamespace::IDENTIFICATION);
			
			if ($form->isValidPartial(array('groupinput' => $formData['groupinput'])))
			{
				$values = $form->getValues();
				$groupname = $values['groupinput'];
		    	$group = GroupNamespace::getCurrentGroup();
		    	if (!isset($group) || is_null($group))
		    	{
		    		$group = new Group();
		    	}
		    	$group->setGroupName($groupname);
		  		$groupid = GroupDAO::getGroupDAO()->saveGroup($group);
		  		if (is_null($groupid))
		  		{
		  			$this->displayGroupErrors($formData, ACORNConstants::MESSAGE_PROBLEM_SAVING);
		  		}
		  		else 
		  		{
		  			$this->_helper->redirector('index', 'group', NULL, array('groupnumber' => $groupid));
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
    
    /**
     * Unlocks the entire group of items for editing.
     */
    public function unlockgroupAction()
    {
    	//Don't display a new view
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    
    	$group = GroupNamespace::getCurrentGroup();
    	$date = $this->getRequest()->getParam('unlockuntildateinput');
    	$zenddate = new Zend_Date($date, ACORNConstants::$ZEND_DATE_FORMAT);
    	$date = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    
    	$items = $group->getRecords();
    	$success = UnlockedItemsDAO::getUnlockedItemsDAO()->unlockItems($items, $date);
    	
    	if ($success)
    	{
    		$group = GroupDAO::getGroupDAO()->getGroup($group->getPrimaryKey());
    		GroupNamespace::setCurrentGroup($group);
    	}
    
    	$jsonstring = Zend_Json::encode(array('UnlockSuccessful' => $success));
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
    	->setBody($jsonstring);
    }
    
    public function setrendertabAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    	$rendertab = $this->getRequest()->getParam('renderTab', GroupNamespace::IDENTIFICATION);
    	GroupNamespace::setRenderTab($rendertab);
    }
    
    public function istransferfunctiondisabledAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	//Don't use the default layout since this isn't a view call
    	$this->_helper->layout->disableLayout();
    	$transferfunction = $this->getRequest()->getParam('transferfunction', '');
    	$group = GroupNamespace::getCurrentGroup();
    	$disabledmessage = "";
    	if (isset($group) && !is_null($group))
    	{
	    	//Find the disable message for the tab
	    	switch($transferfunction)
	    	{
	    		case GroupNamespace::LOGIN:
	    			$disabledmessage = $group->getDisableLoginMessage();
	    			break;
	    		case GroupNamespace::LOGOUT:
	    			$disabledmessage = $group->getDisableLogoutMessage();
	    			break;
	    		case GroupNamespace::TEMP_TRANSFER:
	    			$disabledmessage = $group->getDisableTempTransferMessage();
	    			break;
	    		case GroupNamespace::TEMP_TRANSFER_RETURN:
	    			$disabledmessage = $group->getDisableTempTransferReturnMessage();
	    			break;
	    		default:
	    			$disabledmessage = "";
	    	}
    	}
    	
    	$isdisabled = !empty($disabledmessage);
    	
    	$jsonstring = Zend_Json::encode(array('IsDisabled' => $isdisabled));
    	$this->getResponse()->setHeader('Content-Type', 'application/json')
    		->setBody($jsonstring);
    }
}
?>