<?php

/**
 * RecordproposalController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class RecordproposalController extends Zend_Controller_Action
{
	private $recordForm;
	private $proposalApprovalForm;
	private $acornRedirector;
	
	private function getRecordForm()
	{
		if (is_null($this->recordForm))
		{
			$this->recordForm = new RecordForm();
		}
		return $this->recordForm;
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
	
	public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
        {
        	if ($this->getRequest()->getActionName() == 'proposalapproval')
        	{
        		AcornNamespace::getAcornNamespace()->setRedirectAction('proposalapproval');
        		AcornNamespace::getAcornNamespace()->setRedirectController('recordproposal');
        	}
            $this->_helper->redirector('index', 'index');
        }
    }
    
	public function saveproposalAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getRecordForm()->getSubForm("recordproposalform");
			$formData = $this->_request->getPost();
	
			$item = RecordNamespace::getCurrentItem();
			$proposal = $item->getFunctions()->getProposal();
			//If the proposal being saved is not the item in the session, 
			//set the correct session item.
			if (!empty($formData['hiddenproposalitemid']) 
				&& isset($proposal) 
				&& $item->getItemID() != $formData['hiddenproposalitemid'])
			{
				$item = ItemDAO::getItemDAO()->getItem($formData['hiddenproposalitemid']);
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
			
			RecordNamespace::setRenderTab(RecordNamespace::PROPOSAL);
			if ($form->isValid($formData))
			{
				
				//Save the data
				$proposalid = $this->saveItemProposal($form->getValues());
				if (!is_null($proposalid))
				{
					$item = RecordNamespace::getCurrentItem();
					$item->getFunctions()->getProposal()->setPrimaryKey($proposalid);
					RecordNamespace::setCurrentItem($item);
					
					//Clone the current item proposal and make it the 
					//original item
					$origitem = RecordNamespace::getOriginalItem();
					$clonedproposal = clone $item->getFunctions()->getProposal();
					//Since we only saved the proposal, we are only
					//updating that piece of information in the original item
					$origitem->getFunctions()->setProposal($clonedproposal);
					RecordNamespace::setOriginalItem($origitem);
					
					RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);
					
					//Ensure it goes to the proper tab
					$recnum = $item->getItemID() . '#tabview=tab2';
					$this->getAcornRedirector()->setGoto('index', 'record', NULL, array('recordnumber' => $recnum));
				}
				//otherwise display an error message regarding the save
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
		$this->view->placeholder("proposalerrormessage")->set('Changes were not saved.  Please correct the errors in the form below.');
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('recordnumber')->set($recordnumberlabel);
		$this->view->placeholder('recordinfo')->set($recordinfolabel);

		$this->renderScript('record/index.phtml');
    }
    
	private function saveItemProposal(array $formvalues)
    {
    	//Get the current item
    	$item = RecordNamespace::getCurrentItem();
    	$originalitem = RecordNamespace::getOriginalItem();
    	 
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
    	$item->setComments($formvalues['proposalcommentstextarea']);
    	
    	$proposalid = ProposalDAO::getProposalDAO()->saveItemProposal($proposal, $originalitem->getFunctions()->getProposal(), $item);
    	return $proposalid;
    }   

    public function proposalapprovalAction()
    {
    	$item = RecordNamespace::getRecordNamespace()->getCurrentItem();
    	if (isset($item) && !is_null($item))
    	{
    		$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
			RecordNamespace::getCurrentItem()->getFunctions()->getProposal()->getProposalApproval()->setIsBeingEdited(TRUE);
			RecordNamespace::getCurrentItem()->getFunctions()->getProposal()->getProposalApproval()->setIsBeingEditedByID($identity[PeopleDAO::PERSON_ID]);	
    	}
    	
    	$form = $this->getProposalApprovalForm();
    	
    	$this->view->form = $form;
    }
}
?>

