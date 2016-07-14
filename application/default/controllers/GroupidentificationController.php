<?php

/**
 * GroupidentificationController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class GroupidentificationController extends Zend_Controller_Action
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

    /*
     * Creates records based on the group data.
     */
    public function createnewrecordsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getGroupForm()->getSubForm("groupidentificationform");
			$formData = $this->_request->getPost();
			GroupNamespace::setRenderTab(GroupNamespace::IDENTIFICATION);
			
			if ($form->isValid($formData))
			{
				$values = $form->getValues();
				
				//If the group hasn't been created, create it.
				$group = GroupNamespace::getCurrentGroup();
				if (is_null($group->getPrimaryKey()))
				{
					$groupid = $this->createGroup($values);
					$group->setPrimaryKey($groupid);
					$group->setGroupName($values['groupinput']);
					GroupNamespace::setCurrentGroup($group);
				}
				
				//Save the data
				$numberitemsadded = $this->createRecords($values, $group);
				
				//Clear out the namespace lists so the group records get updated on the redirect
				if ($numberitemsadded > 0)
				{
					GroupNamespace::setSaveStatus(GroupNamespace::SAVE_STATE_SUCCESS);
					
					$group->clearRecords(TRUE);
					$group->clearCounts(TRUE);
					$group->clearWorkAssignedTo(TRUE);
					
					//Go back to the record screen.
					$this->_helper->redirector('index', 'group', NULL, array('groupnumber' => $group->getPrimaryKey()));
				}
				//TODO otherwise display an error message regarding the save
				else 
				{
						$this->displayGroupErrors($formData, ACORNConstants::MESSAGE_PROBLEM_SAVING);
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
    
    private function createGroup(array $formdata)
    {
    	$group = new Group();
    	$group->setGroupName($formdata['groupinput']);
  		$groupid = GroupDAO::getGroupDAO()->saveGroup($group);
  		return $groupid;
    }
    
	private function createRecords(array $formdata, Group $group)
    {
    	$itemscreated = 0;
    	if (!is_null($group->getPrimaryKey()))
    	{
    		$numbertocreate = $formdata['numbernewrecordsinput'];
    		$item = $this->buildItemIdentification($formdata, $group);
    		$itemscreated = ItemDAO::getItemDAO()->createMultipleItems($item, $numbertocreate);
    	}
    	return $itemscreated;
    }
    
	private function buildItemIdentification(array $formvalues, Group $group)
    {
    	//Create an item
    	$item = new Item();
    	$item->setAuthorArtist($formvalues['authorinput']);
    	$item->setCollectionName($formvalues['collectionnameinput']);
    	$item->setComments($formvalues['commentstextarea']);
    	$item->setCoordinatorID($formvalues['coordinatorselect']);
    	$item->setDateOfObject($formvalues['dateofobjectinput']);
    	$item->setDepartmentID($formvalues['departmentselect']);
    	$item->setFormatID($formvalues['formatselect']);
    	$item->setFundMemo($formvalues['repositorymemotextarea']);
    	$item->setGroupID($group->getPrimaryKey());
    	$item->setHomeLocationID($formvalues['repositoryselect']);
    	$item->setChargeToID($formvalues['chargetoselect']);
    	$item->setExpectedDateOfReturn($formvalues['expecteddateofreturninput']);
    	$item->setInsuranceValue($formvalues['insuranceinput']);
    	$item->setNonCollectionMaterial($formvalues['noncollectioncheckbox']);
    	$item->setProjectID($formvalues['projectselect']);
    	$item->setPurposeID($formvalues['purposeselect']);
    	$item->setStorage($formvalues['storageinput']);
    	$item->setTitle($formvalues['titleinput']);
    	$item->setCuratorID($formvalues['curatorselect']);
    	$item->setApprovingCuratorID($formvalues['approvingcuratorselect']);
    	
    	$item->setCallNumbers($this->createCallNumbers($formvalues['callnumberbaseinput'], $formvalues['callnumberfrominput'], $formvalues['callnumbertoinput']));
    	$item->setInitialCounts($group->getInitialCounts());
    	$item->setWorkAssignedTo($group->getWorkAssignedTo());
    	
    	return $item;
    }
    
    private function createCallNumbers($callnumberbase, $from = NULL, $to = NULL)
    {
    	$callnumbers = array();
    	if (!empty($from) && !empty($to) && $from <= $to)
    	{
    		for ($counter = $from; $counter <= $to; $counter++)
    		{
    			$callnumber = $callnumberbase . ' ' . $counter;
    			$callnumbers[$callnumber] = $callnumber;
    		}
    	}
    	else 
    	{
    		$callnumbers[$callnumberbase] = $callnumberbase;
    	}
    	return $callnumbers;
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
     * Updates all of the records in the group with the populated fields.
     */
    public function updaterecordsAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getGroupForm()->getSubForm("groupidentificationform");
			$formData = $this->_request->getPost();
			$formData = $form->getNonEmptyValues($formData);
			GroupNamespace::setRenderTab(GroupNamespace::IDENTIFICATION);
			
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
					foreach ($records as $record)
					{
						$updatedrecord = clone $record;
						$updatedrecord = $this->updateItemIdentification($updatedrecord, $values, $group);
						array_push($updatedrecords, $updatedrecord);
					}
					
					$numberitemsupdated = ItemDAO::getItemDAO()->updateMultipleItems($updatedrecords, $records);
					
					//Clear out the namespace lists so the group records get updated on the redirect
					if ($numberitemsupdated > 0)
					{
						GroupNamespace::setSaveStatus(GroupNamespace::SAVE_STATE_SUCCESS);
						
						$group->clearRecords(TRUE);
						$group->clearCounts(TRUE);
						$group->clearWorkAssignedTo(TRUE);
					
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
     * Only update those values that are populated
     */
	private function updateItemIdentification(Item $item, array $formvalues, Group $group)
    {
    	if (isset($formvalues['authorinput']))
    	{
    		$item->setAuthorArtist($formvalues['authorinput']);
    	}
    	if (isset($formvalues['collectionnameinput']))
    	{
    		$item->setCollectionName($formvalues['collectionnameinput']);
    	}
    	if (isset($formvalues['commentstextarea']))
    	{
    		$item->setComments($formvalues['commentstextarea']);
    	}
    	if (isset($formvalues['coordinatorselect']))
    	{
    		$item->setCoordinatorID($formvalues['coordinatorselect']);
    	}
    	if (isset($formvalues['curatorselect']))
    	{
    		$item->setCuratorID($formvalues['curatorselect']);
    	}
    	if (isset($formvalues['approvingcuratorselect']))
    	{
    		$item->setApprovingCuratorID($formvalues['approvingcuratorselect']);
    	}
    	if (isset($formvalues['dateofobjectinput']))
    	{
    		$item->setDateOfObject($formvalues['dateofobjectinput']);
    	}
    	if (isset($formvalues['departmentselect']))
    	{
    		$item->setDepartmentID($formvalues['departmentselect']);
    	}
    	if (isset($formvalues['formatselect']))
    	{
    		$item->setFormatID($formvalues['formatselect']);
    	}
    	if (isset($formvalues['repositorymemotextarea']))
    	{
    		$item->setFundMemo($formvalues['repositorymemotextarea']);
    	}
    	if (isset($formvalues['repositoryselect']))
    	{
    		$item->setHomeLocationID($formvalues['repositoryselect']);
    	}
    	if (isset($formvalues['chargetoselect']))
    	{
    		$item->setChargeToID($formvalues['chargetoselect']);
    	}
    	if (isset($formvalues['expecteddateofreturninput']))
    	{
    		$item->setExpectedDateOfReturn($formvalues['expecteddateofreturninput']);
    	}
    	if (isset($formvalues['insuranceinput']))
    	{
    		$item->setInsuranceValue($formvalues['insuranceinput']);
    	}
    	if (isset($formvalues['noncollectioncheckbox']))
    	{
    		$item->setNonCollectionMaterial($formvalues['noncollectioncheckbox']);
    	}
    	if (isset($formvalues['projectselect']))
    	{
    		$item->setProjectID($formvalues['projectselect']);
    	}
    	if (isset($formvalues['purposeselect']))
    	{
    		$item->setPurposeID($formvalues['purposeselect']);
    	}
    	if (isset($formvalues['storageinput']))
    	{
    		$item->setStorage($formvalues['storageinput']);
    	}
    	if (isset($formvalues['titleinput']))
    	{
    		$item->setTitle($formvalues['titleinput']);
    	}
    	if (isset($formvalues['callnumberbaseinput']))
    	{
    		$item->setCallNumbers($this->createCallNumbers($formvalues['callnumberbaseinput'], $formvalues['callnumberfrominput'], $formvalues['callnumbertoinput']));
    	}
    	
    	if (!$group->getInitialCounts()->isEmpty() && !$item->getInitialCounts()->equals($group->getInitialCounts()))
    	{
    		$item->setInitialCounts($group->getInitialCounts());
    	}
    	
    	$item->setWorkAssignedTo($group->getWorkAssignedTo());
    	return $item;
    }

    /*
     * Clears out the group identification counts and curators
     * since the form reset won't touch those.
     */
	public function resetidentificationAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$group = GroupNamespace::getCurrentGroup();
    	$group->clearCounts(TRUE);
    	$group->clearWorkAssignedTo(TRUE);
    }
}
?>

