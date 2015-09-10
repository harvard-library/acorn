<?php

/**
 * GroupproposalController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class GroupproposalController extends Zend_Controller_Action
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
			$form = $this->getGroupForm()->getSubForm("groupproposalform");
			$formData = $this->_request->getPost();
			$formData = $form->getNonEmptyValues($formData);
			GroupNamespace::setRenderTab(GroupNamespace::PROPOSAL);
			
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
					$oldproposals = array();
					
					foreach ($records as $record)
					{
						$proposal = $record->getFunctions()->getProposal();
						
						$oldproposals[$record->getPrimaryKey()] = clone $proposal;
						
						$updatedrecord = clone $record;
						$updatedrecord = $this->updateItemProposal($updatedrecord, $values, $group);
						array_push($updatedrecords, $updatedrecord);
					}
					
					$numberitemsupdated = ProposalDAO::getProposalDAO()->updateMultipleItems($updatedrecords, $oldproposals);
					
					//Clear out the namespace lists so the group records get updated on the redirect
					if ($numberitemsupdated > 0)
					{
						GroupNamespace::setSaveStatus(GroupNamespace::SAVE_STATE_SUCCESS);
						
						$group->clearRecords(TRUE);
						$group->clearProposedBy(TRUE);
						
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
						$this->displayGroupErrors($formData, "This Group has not been created.  Please use the 'Create New Records' option on the Identification tab.");
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
		$this->view->placeholder("proposalerrormessage")->set($message);
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('groupnumber')->set($groupnumberlabel);
		
		$this->renderScript('group/index.phtml');
    }
    
	/*
     * Only update those values that are populated
     * 
     */
	private function updateItemProposal(Item $item, array $formvalues, Group $group)
    {
    	$proposal = $item->getFunctions()->getProposal();
    	$proposal->setIdentificationID($item->getPrimaryKey());
    	$min = 0;
    	if (isset($formvalues['proposedhoursmin']))
    	{
    		$proposal->setMinHours($formvalues['proposedhoursmin']);
    		$min = $formvalues['proposedhoursmin'];
    	}
    	//If max is set, then set the min to the max.
    	elseif (isset($formvalues['proposedhoursmax']))
    	{
    		$proposal->setMinHours($formvalues['proposedhoursmax']);
    	}
    	//If this is a new proposal, set the default to 0
    	elseif (is_null($proposal->getPrimaryKey()))
    	{
    		$proposal->setMinHours(0);
    		$min = 0;
    	}
    	
    	if (isset($formvalues['proposedhoursmax']))
    	{
    		$proposal->setMaxHours($formvalues['proposedhoursmax']);
    	}
    	//If the max isn't set, use the min.
    	else
    	{
    		$proposal->setMaxHours($min);
    	}
    	
    	if (isset($formvalues['proposalconditiontextarea']))
    	{
    		$proposal->setCondition($formvalues['proposalconditiontextarea']);
    	}
    	if (isset($formvalues['proposaldescriptiontextarea']))
    	{
    		$proposal->setDescription($formvalues['proposaldescriptiontextarea']);
    	}
    	
    	$dims = new Dimensions();
    	if (isset($formvalues['proposalheightinput']))
    	{
    		$dims->setHeight($formvalues['proposalheightinput']);
    	}
    	if (isset($formvalues['proposalthicknessinput']))
    	{
    		$dims->setThickness($formvalues['proposalthicknessinput']);
    	}
    	if (isset($formvalues['proposalwidthinput']))
    	{
    		$dims->setWidth($formvalues['proposalwidthinput']);
    	}
    	if (isset($formvalues['unitselect']))
    	{
    		$dims->setUnit($formvalues['unitselect']);
    	}
    	$proposal->setDimensions($dims);
    	
    	if (isset($formvalues['examdateinput']))
    	{
    		$proposal->setExamDate($formvalues['examdateinput']);
    	}
    	if (isset($formvalues['examlocationselect']))
    	{
    		$proposal->setExamLocation($formvalues['examlocationselect']);
    	}
    	
    	if (isset($formvalues['proposaldateinput']))
    	{
    		$proposal->setProposalDate($formvalues['proposaldateinput']);
    	}
    	//If this is a new proposal, set the date to the current date
    	elseif (is_null($proposal->getPrimaryKey()))
    	{
    		$zenddate = new Zend_Date();
			$proposal->setProposalDate($zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT));
    	}
    	if (isset($formvalues['proposaltreatmenttextarea']))
    	{
    		$proposal->setTreatment($formvalues['proposaltreatmenttextarea']);
    	}
    	if (isset($formvalues['proposalcommentstextarea']))
    	{
    		$item->setComments($formvalues['proposalcommentstextarea']);
    	}
    	
    	$groupproposedby = $group->getProposedBy();
    	if (!empty($groupproposedby))
    	{
    		$proposal->setProposedBy($groupproposedby);
    	}
    	
    	$item->getFunctions()->setProposal($proposal);
    	
    	return $item;
    }
    
    /*
     * Clears out the group proposal proposed by
    * since the form reset won't touch those.
    */
    public function resetproposalAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	$group = GroupNamespace::getCurrentGroup();
    	$group->clearProposedBy(TRUE);
    }
    
	/*private function saveItemProposal(array $formvalues)
    {
    	//Get the current item
    	$item = RecordNamespace::getCurrentItem();
    	
    	$proposal = $item->getFunctions()->getProposal();
    	if (is_null($proposal))
    	{
    		$proposal = new Proposal();
    	}
    	$min = $formvalues['proposedhoursmin'];
    	$max = $formvalues['proposedhoursmax'];
    	if (empty($min))
    	{
    		$min = $max;
    	}
    	elseif (empty($max))
    	{
    		$max = $min;
    	}
    	$proposal->setIdentificationID($item->getPrimaryKey());
    	$proposal->setCondition($formvalues['proposalconditiontextarea']);
    	$proposal->setDescription($formvalues['proposaldescriptiontextarea']);
    	$dims = new Dimensions();
    	$dims->setHeight($formvalues['proposalheightinput']);
    	$dims->setThickness($formvalues['proposalthicknessinput']);
    	$dims->setWidth($formvalues['proposalwidthinput']);
    	$dims->setUnit($formvalues['unitselect']);
    	$proposal->setDimensions($dims);
    	$proposal->setExamDate($formvalues['examdateinput']);
    	$proposal->setExamLocation($formvalues['examlocationselect']);
    	$proposal->setMaxHours($max);
    	$proposal->setMinHours($min);
    	$proposal->setProposalDate($formvalues['proposaldateinput']);
    	$proposal->setTreatment($formvalues['proposaltreatmenttextarea']);
    	$item->appendComments($formvalues['proposalcommentstextarea']);
    	
    	$proposalid = ProposalDAO::getProposalDAO()->saveItemProposal($proposal, $item);
    	return $proposalid;
    }    */
}
?>

