<?php
class TransferFormReport extends AcornReport  
{
	const COURIER = 'Courier';
	const SECOND_COURIER = 'SecondCourier';
	const TRANSPORT_CONTAINER = 'TransportContainer';
	const TRANSFER_LIST = 'TransferList';
	
	//This should be one of the ReportNamespace constants.
	private $transferType;
	//The courier
	private $courier;
	//The second courier (can be blank)
	private $secondCourier;
	//The transport container
	private $transportContainer;
	private $transerObject;
	
	//The list of items to transfer
	private $transferList;
	
	/*
	 * Constructs the transfer report
	 * 
	 * @param string transfertype.  One of:
	 * 	'Login', 'Logout', 'TemporaryTransfer', 'TemporaryTransferReturn' (use the ReportNamespace constants)
	 * @param Object transferobject - one of Item or Group
	 * @param array
	 */
	public function __construct($transferType, array $transferOptions)
	{
		$this->transferType = $transferType;
		
		if (!isset($transferOptions[self::COURIER]) || !isset($transferOptions[self::TRANSPORT_CONTAINER]))
		{
			//Throw a runtime exception if the courier and transport aren't set
			//User entered error is caught in the ui
			throw new Zend_Exception('Courier and TransportContainer items must be set in the array.');
		}
		$this->courier = $transferOptions[self::COURIER];
		$this->transportContainer = $transferOptions[self::TRANSPORT_CONTAINER];
		if (isset($transferOptions[self::SECOND_COURIER]))
		{
			$this->secondCourier = $transferOptions[self::SECOND_COURIER];
		}
		else 
		{
			$this->secondCourier = '';
		}
		
		if (isset($transferOptions[self::TRANSFER_LIST]))
		{
			$this->transferList = $transferOptions[self::TRANSFER_LIST];
		}
		else 
		{
			$this->transferList = array();
		}
		
		$this->transferObject = $this->getItem();
		
		$name = '';
		switch ($transferType)
		{
			case RecordNamespace::LOGIN:
				$name = 'Login';
				break;
			case RecordNamespace::LOGOUT:
				$name = 'Logout';
				break;
			case RecordNamespace::TEMP_TRANSFER:
				$name = 'TempTransfer';
				break;
			case RecordNamespace::TEMP_TRANSFER_RETURN:
				$name = 'TempTransferReturn';
				break;
		}
		
		parent::__construct($name . 'TransferForm');
	}
	
	protected function drawReportBody()
	{
		$this->drawTransferInformation();
		
		$this->drawItemDetailHeaders();
		foreach ($this->transferList as $record)
		{
			$this->drawItemDetails($record);
			if ($this->checkYLoc())
			{
				$this->drawReportTitle($this->yloc);
				$this->yloc -= 20;
				$this->drawItemDetailHeaders();
			}
		}
		
		$this->drawApprovalArea();
	}
	
	private function drawTransferInformation()
	{
		$xlocheaderleft = self::$X_LEFT + 20;
		$xlocheaderright = $this->currentPage->getWidth()/2 + 25;
		$xlocdataleft = self::$X_LEFT + 110;
		$xlocdataright = $this->currentPage->getWidth()/2 + 100;
		
		$rectangleyloc1 = $this->yloc + 15;
		
		//Date
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Date of Transfer:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getTransferDate(), $xlocdataleft, $this->yloc);
		
		//Courier
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Courier:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->courier, $xlocdataright, $this->yloc);
		$this->yloc -= 15;
		
		//Repository
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Repository:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->createWordWrap($this->getRepository(), $xlocdataleft, 35, 12);
		
		//Courier 2
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('2nd Courier:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->secondCourier, $xlocdataright, $this->yloc);
		
		$this->yloc = min(array($this->yloc-15, $wrapyloc));
		
		//Curators
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Curator/Librarian:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getCurator(), $xlocdataleft, $this->yloc);
		
		//Number of Items
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('No. of Items:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getNumberOfItems(), $xlocdataright, $this->yloc);
		
		$this->yloc -= 15;
		
		//From
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Transfer From:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getTransferFrom(), $xlocdataleft, $this->yloc);
		//To
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Transfer To:', $xlocheaderleft, $this->yloc - 15);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getTransferTo(), $xlocdataleft, $this->yloc - 15);
		
		//Transport Containers
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Transport', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->createWordWrap($this->transportContainer, $xlocdataright, 35, 12);
		$this->yloc -= 15;
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Containers:', $xlocheaderright, $this->yloc);
		
		$this->yloc = min(array($this->yloc-15, $wrapyloc));
		
		$this->currentPage->setLineWidth(.5);
		//Rectangle
		$this->currentPage->drawRectangle(self::$X_LEFT, $rectangleyloc1, self::$X_RIGHT, $this->yloc, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
		
		$this->yloc -= 15;
	}
	
	private function drawItemDetailHeaders()
	{
		$xlocrec = self::$X_LEFT;
		$xloccall = self::$X_LEFT + 40;
		$xloctitle = self::$X_LEFT + 170;
		$xloccounts = self::$X_LEFT + 300;
		$xlocreturndate = self::$X_LEFT + 400;
		$xlocinsurance = self::$X_LEFT + 470;
		$this->currentPage->setFont(self::$BOLD_FONT, 9);
		$this->currentPage->drawText('Rec #', $xlocrec, $this->yloc);
		$this->currentPage->drawText('Call Number(s)', $xloccall, $this->yloc);
		$this->currentPage->drawText('Title/Description', $xloctitle, $this->yloc);
		$this->currentPage->drawText('Volume/Sheet/Photo/', $xloccounts, $this->yloc);
		$this->currentPage->drawText('Box/Other Count', $xloccounts+15, $this->yloc-12);
		$this->currentPage->drawText('Expected Date', $xlocreturndate, $this->yloc);
		$this->currentPage->drawText('of Return', $xlocreturndate+8, $this->yloc-12);
		$this->currentPage->drawText('Insurance', $xlocinsurance, $this->yloc);
		$this->currentPage->drawText('Value', $xlocinsurance+8, $this->yloc-12);
		
		$this->yloc -= 25;
	}
	
	private function drawItemDetails(Item $item)
	{
		$xlocrec = self::$X_LEFT;
		$xloccall = self::$X_LEFT + 40;
		$xloctitle = self::$X_LEFT + 170;
		$xloccounts = self::$X_LEFT + 300;
		$xlocreturndate = self::$X_LEFT + 400;
		$xlocinsurance = self::$X_LEFT + 470;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 9);
		$this->currentPage->drawText($item->getItemID(), $xlocrec, $this->yloc);
		$calls = implode(', ', $item->getCallNumbers());
		$callyloc = $this->createWordWrap($calls, $xloccall, 25);
		$titleyloc = $this->createWordWrap($item->getTitle(), $xloctitle, 30);
		
		$counts = $this->getCounts($item);
		$countstring = $counts->getVolumeCount() . '/' . $counts->getSheetCount() . '/' . $counts->getPhotoCount() . '/' . $counts->getBoxCount() . '/' . $counts->getOtherCount();
		$this->currentPage->drawText($countstring, $xloccounts+25, $this->yloc);
		
		$expdatereturn = $item->getExpectedDateOfReturn();
		if (!empty($expdatereturn))
		{
			$zenddate = new Zend_Date($expdatereturn, ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
			$expdatereturn = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
		}
		
		$this->currentPage->drawText($expdatereturn, $xlocreturndate+8, $this->yloc);
		
		$insurance = '';
		if ($item->getInsuranceValue() > 0)
		{
			$insurance = '$' . number_format($item->getInsuranceValue(), 2);
		}
		$this->currentPage->drawText($insurance, $xlocinsurance+8, $this->yloc);
		$this->yloc = min(array($this->yloc-15, $callyloc, $titleyloc));
	}
	
	private function drawApprovalArea()
	{
		$ylocboxtop = self::$YLOC_FOOTER + 150;
		//If the item list is too long to fit the approval boxes,
		//move the approval boxes to the next page.
		if ($this->checkYLoc($ylocboxtop))
		{
			$this->drawReportTitle($this->yloc);
			$this->yloc -= 20;
			$ylocboxtop = $this->yloc;
		}
		
		$approvaltextxloc = self::$X_LEFT + 5;
		$signaturexloc = self::$X_LEFT + 330;
		
		$this->currentPage->setLineWidth(.5);
		//Rectangle
		$this->currentPage->drawRectangle(self::$X_LEFT, $ylocboxtop, self::$X_RIGHT, self::$YLOC_FOOTER + 20, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
		$this->yloc = $ylocboxtop - 25;
		$textyloc = $this->yloc + 7;
		
		$this->currentPage->setFont(self::$BOLD_FONT, 9);
		$this->currentPage->drawText('Transfer Approval', $approvaltextxloc, $textyloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$this->currentPage->drawText('(Signature)', $signaturexloc, $textyloc);
		$this->currentPage->setFont(self::$BOLD_FONT, 9);
		$this->currentPage->drawText('Date', $signaturexloc + 55, $textyloc);
		
		$this->currentPage->drawLine(self::$X_LEFT, $this->yloc, self::$X_RIGHT, $this->yloc);
		$this->yloc -= 25;
		$textyloc = $this->yloc + 7;
		
		$this->currentPage->setFont(self::$BOLD_FONT, 9);
		$this->currentPage->drawText('Secondary Transfer Approval', $approvaltextxloc, $textyloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$this->currentPage->drawText('(if required)', $approvaltextxloc+130, $textyloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$this->currentPage->drawText('(Signature)', $signaturexloc, $textyloc);
		$this->currentPage->setFont(self::$BOLD_FONT, 9);
		$this->currentPage->drawText('Date', $signaturexloc + 55, $textyloc);
		
		$this->currentPage->drawLine(self::$X_LEFT, $this->yloc, self::$X_RIGHT, $this->yloc);
		$this->yloc -= 25;
		$textyloc = $this->yloc + 7;
		
		$this->currentPage->setFont(self::$BOLD_FONT, 9);
		$this->currentPage->drawText('Received By', $approvaltextxloc, $textyloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$this->currentPage->drawText('(Signature)', $signaturexloc, $textyloc);
		$this->currentPage->setFont(self::$BOLD_FONT, 9);
		$this->currentPage->drawText('Date', $signaturexloc + 55, $textyloc);
		
		$this->currentPage->drawLine(self::$X_LEFT, $this->yloc, self::$X_RIGHT, $this->yloc);
		$this->currentPage->drawLine($signaturexloc + 50, $ylocboxtop, $signaturexloc + 50, $this->yloc);
		
		$this->yloc -= 18;
		
		$this->currentPage->setFont(self::$BOLD_FONT, 9);
		$this->currentPage->drawText('Comments', $approvaltextxloc, $this->yloc);
		
		
	}
	
	protected function getReportTitle()
	{
		return 'TRANSFER OF COLLECTION MATERIAL';
	}
	
	protected function getCustomFooter()
	{
		return 'COPY -- Librarian/Curator, Registrar, WPC';
	}
	
	/*
	 * Returns the transfer object or the first item in the group.
	 * 
	 * @return Item
	 */
	private function getItem()
	{
		//If at least one item is being transferred,
		//use the first item as the item to extract data from.
		if (count($this->transferList) > 0)
		{
			$item = current($this->transferList);
		}
		else 
		{
			$item = NULL;	
		}
		
		return $item;
	}
	
	/*
	 * Determine the transfer date
	 */
	private function getTransferDate()
	{
		$date = '';
		if (!is_null($this->transferObject))
		{
			switch ($this->transferType)
			{
				case RecordNamespace::LOGIN:
					$zenddate = new Zend_Date($this->transferObject->getFunctions()->getLogin()->getLoginDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    				$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
					break;
				case RecordNamespace::LOGOUT:
					$zenddate = new Zend_Date($this->transferObject->getFunctions()->getLogout()->getLogoutDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    				$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
					break;
				case RecordNamespace::TEMP_TRANSFER:
					$zenddate = new Zend_Date($this->transferObject->getFunctions()->getTemporaryTransfer()->getTransferDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    				$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
					break;
				case RecordNamespace::TEMP_TRANSFER_RETURN:
					$zenddate = new Zend_Date($this->transferObject->getFunctions()->getTemporaryTransferReturn()->getTransferDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    				$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
					break;
			}
		}
		return $date;
	}
	
	/*
	 * Determine the transfer from
	 */
	private function getTransferFrom()
	{
		$location = '';
		if (!is_null($this->transferObject))
		{
			switch ($this->transferType)
			{
				case RecordNamespace::LOGIN:
					if (!is_null($this->transferObject->getFunctions()->getLogin()->getTransferFrom()))
					{
						$location = LocationDAO::getLocationDAO()->getLocation($this->transferObject->getFunctions()->getLogin()->getTransferFrom())->getLocation();
					}
					break;
				case RecordNamespace::LOGOUT:
					if (!is_null($this->transferObject->getFunctions()->getLogout()->getTransferFrom()))
					{
						$location = LocationDAO::getLocationDAO()->getLocation($this->transferObject->getFunctions()->getLogout()->getTransferFrom())->getLocation();
					}
					break;
				case RecordNamespace::TEMP_TRANSFER:
					if (!is_null($this->transferObject->getFunctions()->getTemporaryTransfer()->getTransferFrom()))
					{
						$location = LocationDAO::getLocationDAO()->getLocation($this->transferObject->getFunctions()->getTemporaryTransfer()->getTransferFrom())->getLocation();
					}
					break;
				case RecordNamespace::TEMP_TRANSFER_RETURN:
					if (!is_null($this->transferObject->getFunctions()->getTemporaryTransferReturn()->getTransferFrom()))
					{
						$location = LocationDAO::getLocationDAO()->getLocation($this->transferObject->getFunctions()->getTemporaryTransferReturn()->getTransferFrom())->getLocation();
					}
					break;
			}
		}
		return $location;
	}
	
	private function getTransferTo()
	{
		$location = '';
		if (!is_null($this->transferObject))
		{
			switch ($this->transferType)
			{
				case RecordNamespace::LOGIN:
					if (!is_null($this->transferObject->getFunctions()->getLogin()->getTransferTo()))
					{
						$location = LocationDAO::getLocationDAO()->getLocation($this->transferObject->getFunctions()->getLogin()->getTransferTo())->getLocation();
					}
					break;
				case RecordNamespace::LOGOUT:
					if (!is_null($this->transferObject->getFunctions()->getLogout()->getTransferTo()))
					{
						$location = LocationDAO::getLocationDAO()->getLocation($this->transferObject->getFunctions()->getLogout()->getTransferTo())->getLocation();
					}
					break;
				case RecordNamespace::TEMP_TRANSFER:
					if (!is_null($this->transferObject->getFunctions()->getTemporaryTransfer()->getTransferTo()))
					{
						$location = LocationDAO::getLocationDAO()->getLocation($this->transferObject->getFunctions()->getTemporaryTransfer()->getTransferTo())->getLocation();
					}
					break;
				case RecordNamespace::TEMP_TRANSFER_RETURN:
					if (!is_null($this->transferObject->getFunctions()->getTemporaryTransferReturn()->getTransferTo()))
					{
						$location = LocationDAO::getLocationDAO()->getLocation($this->transferObject->getFunctions()->getTemporaryTransferReturn()->getTransferTo())->getLocation();
					}
					break;
			}
		}
		return $location;
	}
	
	private function getCurator()
	{
		$curator = '';
		if (!is_null($this->transferObject) && !is_null($this->transferObject->getCuratorID()))
		{
			$curator = PeopleDAO::getPeopleDAO()->getPerson($this->transferObject->getCuratorID())->getDisplayName();
		}
		return $curator;
	}
	
	private function getNumberOfItems()
	{
		$counts = 0;
		foreach($this->transferList as $item)
		{
			$counts += $this->getCounts($item)->getTotalItemCount();
		}
		return $counts;
	}
	
	/*
	 * Returns the initial or final counts depending on where in the process this item is.
	 * 
	 * @return Counts
	 */
	private function getCounts(Item $item = NULL)
	{
		$counts = NULL;
		if (!is_null($item))
		{
			switch ($this->transferType)
			{
				case RecordNamespace::LOGIN:
					$counts = $item->getInitialCounts();
					break;
				case RecordNamespace::LOGOUT:
					$counts = $item->getFunctions()->getReport()->getCounts();
					break;
				default:
					if (is_null($item->getFunctions()->getReport()->getPrimaryKey()))
					{
						$counts = $item->getInitialCounts();
					}
					else 
					{
						$counts = $item->getFunctions()->getReport()->getCounts();
					}
					break;
			}
		}
		else
		{
			$counts = new Counts();
		}
		return $counts;
	}
	
	private function getRepository()
	{
		$repository = '';
		if (!is_null($this->transferObject) && !is_null($this->transferObject->getHomeLocationID()))
		{
			$repository = LocationDAO::getLocationDAO()->getLocation($this->transferObject->getHomeLocationID())->getLocation();
		}
		return $repository;
	}
}
?>