<?php

/**
 * RecordtemptransferController
 *
 * @author vcrema
 * Created Mar 5, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
	

class RecordtemptransferController extends Zend_Controller_Action
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

    
    private function displayRecordErrors(array $formData, $errordivname)
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
		$this->view->placeholder($errordivname)->set('Changes were not saved.  Please correct the errors in the form below.');
		$this->view->placeholder('createdate')->set($createdate);
		$this->view->placeholder('createdby')->set($createdby);
		$this->view->placeholder('recordnumber')->set($recordnumberlabel);
		$this->view->placeholder('recordinfo')->set($recordinfolabel);

		$this->renderScript('record/index.phtml');
    }
    
	private function validateTempTransferFormAndSave($formData)
    {
    	$form = $this->getRecordForm()->getSubForm("recordtemporarytransferform");
		$temptransfer = NULL;
		
    	RecordNamespace::setRenderTab(RecordNamespace::TEMP_TRANSFER);
		if ($form->isValid($formData))
		{
			//Save the data
			$temptransfer = $this->saveItemTempTransfer($form->getValues());
			if (!is_null($temptransfer))
			{
				$item = RecordNamespace::getCurrentItem();
				$item->getFunctions()->setTemporaryTransfer($temptransfer);
				RecordNamespace::setCurrentItem($item);
				
				//Clone the current item login and make it the 
				//original item
				$origitem = RecordNamespace::getOriginalItem();
				$clonedtemptransfer = clone $item->getFunctions()->getTemporaryTransfer();
				//Since we only saved the login, we are only
				//updating that piece of information in the original item
				$origitem->getFunctions()->setTemporaryTransfer($clonedtemptransfer);
				RecordNamespace::setOriginalItem($origitem);
				
			}
			//TODO otherwise display an error message regarding the save
			else 
			{
				$this->displayRecordErrors($formData, 'temptransfererrormessage');
			}
		}
		else
		{
			$this->displayRecordErrors($formData, 'temptransfererrormessage');
		}
		return $temptransfer;
    }
    
    private function saveItemTempTransfer(array $formvalues)
    {
    	//Get the current item
    	$item = RecordNamespace::getCurrentItem();
    	
    	$temptransfer = $item->getFunctions()->getTemporaryTransfer();
    	if (is_null($temptransfer))
    	{
            $temptransfer = new TemporaryTransfer();
    	}
    	$zenddate = new Zend_Date($formvalues['transferdateinput'], ACORNConstants::$ZEND_DATE_FORMAT);		
    	$transferdate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	$temptransfer->setTransferDate($transferdate);
    	$temptransfer->setTransferFrom($formvalues['transferfromselect']);
    	$temptransfer->setTransferTo($formvalues['transfertoselect']);
    	$temptransfer->setTransferType(TemporaryTransfersDAO::TRANSFER_TYPE_TRANSFER);
        $itemID = $item->getItemID();
        $temptransfer->setItemID($itemID);
    	$item->getFunctions()->setTemporaryTransfer($temptransfer);
    	$item->setComments($formvalues['temptransfercommentstextarea']);     
    	                               
        // Email from here, TME
	$item->emailCurator($itemID, 'transferOut');

    	$temptransferid = TemporaryTransfersDAO::getTemporaryTransfersDAO()->saveTemporaryTransfer($temptransfer);
    	if (is_null($temptransferid))
    	{
    		$temptransfer = NULL;
    	}
    	if (!is_null($temptransfer))
    	{
    		ItemDAO::getItemDAO()->updateComments($item);
    	}
    	return $temptransfer;
    }
    
 	private function validateTempTransferReturnFormAndSave($formData)
    {
    	$form = $this->getRecordForm()->getSubForm("recordtemporarytransferreturnform");
		$temptransfer = NULL;
		
    	RecordNamespace::setRenderTab(RecordNamespace::TEMP_TRANSFER_RETURN);
		if ($form->isValid($formData))
		{
			//Save the data
			$temptransfer = $this->saveItemTempTransferReturn($form->getValues());
			if (!is_null($temptransfer))
			{
				$item = RecordNamespace::getCurrentItem();
				$item->getFunctions()->setTemporaryTransferReturn($temptransfer);
				RecordNamespace::setCurrentItem($item);
				
				//Clone the current item login and make it the 
				//original item
				$origitem = RecordNamespace::getOriginalItem();
				$clonedtemptransfer = clone $item->getFunctions()->getTemporaryTransferReturn();
				//Since we only saved the login, we are only
				//updating that piece of information in the original item
				$origitem->getFunctions()->setTemporaryTransferReturn($clonedtemptransfer);
				RecordNamespace::setOriginalItem($origitem);
				
			}
			//TODO otherwise display an error message regarding the save
			else 
			{
				$this->displayRecordErrors($formData, 'temptransferreturnerrormessage');
			}
		}
		else
		{
			$this->displayRecordErrors($formData, 'temptransferreturnerrormessage');
		}
		return $temptransfer;
    }
    
	private function saveItemTempTransferReturn(array $formvalues)
    {
    	//Get the current item
    	$item = RecordNamespace::getCurrentItem();
    	
    	$temptransfer = $item->getFunctions()->getTemporaryTransferReturn();
    	if (is_null($temptransfer))
    	{
    		$temptransfer = new TemporaryTransfer();
    	}
    	$zenddate = new Zend_Date($formvalues['transferreturndateinput'], ACORNConstants::$ZEND_DATE_FORMAT);		
    	$transferdate = $zenddate->toString(ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
    	$temptransfer->setTransferDate($transferdate);
    	$temptransfer->setTransferFrom($formvalues['transferreturnfromselect']);
    	$temptransfer->setTransferTo($formvalues['transferreturntoselect']);
    	$temptransfer->setTransferType(TemporaryTransfersDAO::TRANSFER_TYPE_RETURN);       
        $itemID = $item->getItemID();
        $temptransfer->setItemID($itemID);
    	$item->getFunctions()->setTemporaryTransfer($temptransfer);
    	$item->setComments($formvalues['returncommentstextarea']);

        // Email from here, TME
	$item->emailCurator($itemID, 'transferReturn');
    	
    	$temptransferid = TemporaryTransfersDAO::getTemporaryTransfersDAO()->saveTemporaryTransfer($temptransfer);
    	if (is_null($temptransferid))
    	{
    		$temptransfer = NULL;
    	}
    	if (!is_null($temptransfer))
    	{
    		ItemDAO::getItemDAO()->updateComments($item);
    	}
    	return $temptransfer;
    }
    
	public function addtotemptransferlistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			
			$item = RecordNamespace::getCurrentItem();
			//If the temp transfer being saved is not the item in the session, 
			//set the correct session item.
			if (!empty($formData['hiddentemptransferitemid']) 
				&& $item->getItemID() != $formData['hiddentemptransferitemid'])
			{
				$item = ItemDAO::getItemDAO()->getItem($formData['hiddentemptransferitemid']);
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
			
			$resultid = $this->validateTempTransferFormAndSave($formData);
	
			//As long as it saved properly, then continue
			if (!is_null($resultid))
			{
		    	$item = RecordNamespace::getCurrentItem();
		    	if (isset($item) && $this->isTempTransferrable($item))
				{
		    		TransferNamespace::addToTransferList(TransferNamespace::TEMP_TRANSFER, $item);
		    		RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);
					
		    		//Ensure it goes to the proper tab
					$recnum = $item->getItemID() . '#tabview=tab6';
			    	$this->getAcornRedirector()->setGoto('index', 'record', NULL, array('recordnumber' => $recnum));
		    	}
		    	else 
		    	{
		    		$this->displayRecordErrors($formData, 'The transfer data, curator, and department must match the temp transfer list data.');
				}
		    }
    	}
		
    }
    

	private function isTempTransferrable(Item $item)
    {
    	$transferlist = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER);
    	//If it exists, get the temp transfer information from one item in the transfer list
    	if (isset($transferlist) && count($transferlist) > 0)
    	{
    		$compareitem = current($transferlist);
    		$itemtemptransfer = $item->getFunctions()->getTemporaryTransfer();
    		$equal = $itemtemptransfer->equals($compareitem->getFunctions()->getTemporaryTransfer());
    		//Also compare the curator and department
    		if ($equal)
    		{
    			$equal = $item->getCuratorID() == $compareitem->getCuratorID();
    			$equal = $equal && $item->getDepartmentID() == $compareitem->getDepartmentID();
    		}
    		return $equal;
    	}
    	return TRUE;
    }
    
	public function addtotemptransferreturnlistAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	if ($this->_request->isPost())
		{
			$formData = $this->_request->getPost();
			
			$item = RecordNamespace::getCurrentItem();
			//If the temp transfer being saved is not the item in the session, 
			//set the correct session item.
			if (!empty($formData['hiddentemptransferreturnitemid']) 
				&& $item->getItemID() != $formData['hiddentemptransferreturnitemid'])
			{
				$item = ItemDAO::getItemDAO()->getItem($formData['hiddentemptransferreturnitemid']);
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
			
			$resultid = $this->validateTempTransferReturnFormAndSave($formData);
	
			//As long as it saved properly, then continue
			if (!is_null($resultid))
			{
		    	$item = RecordNamespace::getCurrentItem();
		    	if (isset($item) && $this->isReturnTransferrable($item))
				{
		    		TransferNamespace::addToTransferList(TransferNamespace::TEMP_TRANSFER_RETURN, $item);
		    		RecordNamespace::setSaveStatus(RecordNamespace::SAVE_STATE_SUCCESS);
					
		    		//Ensure it goes to the proper tab
					$recnum = $item->getItemID() . '#tabview=tab7';
			    	$this->getAcornRedirector()->setGoto('index', 'record', NULL, array('recordnumber' => $recnum));
		    	}
				else 
		    	{
		    		$this->displayRecordErrors($formData, 'The return data, curator, and department must match the return transfer list data.');
				}
		    }
    	}
		
    }
    
	private function isReturnTransferrable(Item $item)
    {
    	$transferlist = TransferNamespace::getTransferList(TransferNamespace::TEMP_TRANSFER_RETURN);
    	//If it exists, get the return information from one item in the transfer list
    	if (isset($transferlist) && count($transferlist) > 0)
    	{
    		$compareitem = current($transferlist);
    		$itemreturn = $item->getFunctions()->getTemporaryTransferReturn();
    		$equal = $itemreturn->equals($compareitem->getFunctions()->getTemporaryTransferReturn());
    		//Also compare the curator and department
    		if ($equal)
    		{
    			$equal = $item->getCuratorID() == $compareitem->getCuratorID();
    			$equal = $equal && $item->getDepartmentID() == $compareitem->getDepartmentID();
    		}
    		return $equal;
    	}
    	return TRUE;
    }
    
	public function deletetemptransferAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$item = RecordNamespace::getCurrentItem();
    	if (isset($item) && !is_null($item->getFunctions()->getTemporaryTransfer()->getPrimaryKey()))
    	{
    		TemporaryTransfersDAO::getTemporaryTransfersDAO()->deleteTemporaryTransfer($item->getFunctions()->getTemporaryTransfer()->getPrimaryKey());
    	}
    	$this->_helper->redirector('index', 'record');
    }
    

    
	public function deletetemptransferreturnAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$item = RecordNamespace::getCurrentItem();
    	if (isset($item) && !is_null($item->getFunctions()->getTemporaryTransferReturn()->getPrimaryKey()))
    	{
    		TemporaryTransfersDAO::getTemporaryTransfersDAO()->deleteTemporaryTransfer($item->getFunctions()->getTemporaryTransferReturn()->getPrimaryKey());
    	}
    	$this->_helper->redirector('index', 'record');
    }
    
}
?>