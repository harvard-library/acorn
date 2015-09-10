<?php
class CompletedWorkByTubReport extends AcornReport  
{
private $dateRange;
	
	/*
	 * @param DateRange - the date range
	 */
	public function __construct(DateRange $dateRange)
	{
		$this->dateRange = $dateRange;
		parent::__construct('CompletedWorkForTub');
		$this->setDefaultPageSize(Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE);
	}
	
	protected function drawReportBody()
	{
		$this->drawHeaders();
		$this->drawBody();
	}
	
	private function drawHeaders()
	{
		$acornnumloc = self::$X_LEFT + 5;
		$purposeloc = self::$X_LEFT + 75;
		$activityloc = self::$X_LEFT + 175;
		$callloc = self::$X_LEFT + 225;
		$titleloc = self::$X_LEFT + 350;
		$authorloc = self::$X_LEFT + 500;
		$itemloc = self::$X_LEFT + 600;
		$comphrsloc = self::$X_LEFT + 650;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$this->currentPage->drawText('ACORN#', $acornnumloc, $this->yloc);
		$this->currentPage->drawText('Purpose', $purposeloc, $this->yloc);
		$this->currentPage->drawText('Activity', $activityloc, $this->yloc);
		$this->currentPage->drawText('Call Numbers', $callloc, $this->yloc);
		$this->currentPage->drawText('Title', $titleloc, $this->yloc);
		$this->currentPage->drawText('Author/Artist', $authorloc, $this->yloc);
		$this->currentPage->drawText('#Items', $itemloc, $this->yloc);
		$this->currentPage->drawText('CompHrs', $comphrsloc, $this->yloc);
		$this->yloc -= 15;
	}
	
	private function drawBody()
	{
		$totalitems = 0;
		$totalhours = 0.0;
		
		//Get all items for this repository 
		//where the department is null
		$items = CombinedRecordsReportsDAO::getCombinedRecordsReportsDAO()->getCompletedByTub($this->dateRange);
		if (!empty($items))
		{
			$retval = $this->drawItems($items);
			$totalitems = $retval['SubtotalCount'];
			$totalhours = $retval['SubtotalHours'];
		}

		//Totals
		$this->currentPage->setFont(self::$BOLD_FONT, 8);
		$xloc = self::$X_RIGHT - 125;
		$xloccount = self::$X_RIGHT - 25;
		$this->currentPage->drawText('Total, Number of Items:', $xloc, $this->yloc);
		$totalitems = number_format($totalitems);
		$this->currentPage->drawText($totalitems, $xloccount, $this->yloc);
		$this->yloc -= 10;
		$this->currentPage->drawText('Total, Number of Hours:', $xloc, $this->yloc);	
		$totalhours = number_format($totalhours, 2);
		$this->currentPage->drawText($totalhours, $xloccount, $this->yloc);	
		$this->yloc -= 15;
	}
	
	
	private function drawItems(array $items)
	{
		//Check the ylocation
		if($this->checkYLoc(70, Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE, self::$Y_TOP))
		{
			$this->drawHeaders();
		}
		
		$acornnumloc = self::$X_LEFT + 5;
		$purposeloc = self::$X_LEFT + 75;
		$activityloc = self::$X_LEFT + 175;
		$callloc = self::$X_LEFT + 225;
		$titleloc = self::$X_LEFT + 350;
		$authorloc = self::$X_LEFT + 500;
		$itemloc = self::$X_LEFT + 600;
		$comphrsloc = self::$X_LEFT + 650;
		
		$totalitemcount = 0;
		$totalhourcount = 0.0;
		
		$itemcount = 0;
		$hourcount = 0.0;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$location = '';
		$tub = '';
		foreach ($items as $item)
		{
			//If the location changed, then print the new location on a line.
			if ($tub != $item[LocationDAO::TUB])
			{
				$this->yloc -= 5;
				$this->currentPage->setFont(self::$BOLD_FONT, 8);
				$this->currentPage->drawText($item[LocationDAO::TUB], self::$X_LEFT-5, $this->yloc);
				$this->yloc -= 15;
				$this->currentPage->setFont(self::$REGULAR_FONT, 8);
			}
			
			//If the department changed, then print the new department on a line.
			if ($location != $item[LocationDAO::LOCATION])
			{
				if (!empty($location))
				{
					$this->drawSubtotals($itemcount, $hourcount);
					$itemcount = 0;
					$hourcount = 0.0;
					//Check the ylocation
					if($this->checkYLoc(70, Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE, self::$Y_TOP))
					{
						$this->drawHeaders();
					}
				}
				$this->yloc -= 5;
				$this->currentPage->setFont(self::$BOLD_FONT, 8);
				$this->currentPage->drawText($item[LocationDAO::LOCATION], self::$X_LEFT, $this->yloc);
				$this->yloc -= 15;
				$this->currentPage->setFont(self::$REGULAR_FONT, 8);
			}
			
			$location = $item[LocationDAO::LOCATION];
			$tub = $item[LocationDAO::TUB];
			
			if ($item['RecordType'] == 'Item')
			{
				$activity = 'Treatment';
				$recordNumber = $item[CombinedRecordsDAO::RECORD_ID];
			}
			else 
			{
				$activity = 'On-site';
				$recordNumber = 'OSW-' . $item[CombinedRecordsDAO::RECORD_ID];
			}
			
			$this->currentPage->drawText($recordNumber, $acornnumloc, $this->yloc);
			$purpyloc = $this->createWordWrap($item[PurposeDAO::PURPOSE], $purposeloc);
			$this->currentPage->drawText($activity, $activityloc, $this->yloc);
			$callwordwraparray = $this->createCallNumberWordWrap($item['CallNumbers'], $callloc, 32, 10, TRUE, FALSE);
			$currentpageindex = count($this->pages) - ($callwordwraparray['newpages']);
			$temppage = $this->currentPage;
			//If we added new pages, then go back to the original page.
			if ($callwordwraparray['newpages'] > 0)
			{
				$this->currentPage = $this->pages[$currentpageindex];
			}
			$titleyloc = $this->createWordWrap($item[ItemDAO::TITLE], $titleloc, 40);
			$authyloc = $this->createWordWrap($item[ItemDAO::AUTHOR_ARTIST], $authorloc, 25);
			$this->currentPage->drawText($item['Count'], $itemloc, $this->yloc);
			$this->currentPage->drawText($item['TotalHours'], $comphrsloc, $this->yloc);
			//If new pages were created, put the current page back into the array
			//and assign the yloc to the new page yloc.
			if ($callwordwraparray['newpages'] > 0)
			{
				$this->pages[$currentpageindex] = $this->currentPage;
				$this->currentPage = $temppage;
				$this->yloc = self::$Y_TOP;
				$this->drawHeaders();
				$this->yloc = $callwordwraparray['yloc'];
			}
			else 
			{
				$this->yloc = min(array($authyloc, $callwordwraparray['yloc'], $titleyloc, $purpyloc, $this->yloc - 10));
			}
			//Check the ylocation
			if($this->checkYLoc(70, Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE, self::$Y_TOP))
			{
				$this->drawHeaders();
			}
			
			$itemcount += $item['Count'];
			$hourcount += $item['TotalHours'];
			$totalhourcount += $item['TotalHours'];
			$totalitemcount += $item['Count'];
		}
		$this->yloc -=10;
		$this->drawSubtotals($itemcount, $hourcount);
		return array('SubtotalCount' => $totalitemcount, 'SubtotalHours' => $totalhourcount);
	}
	
	private function drawSubtotals($itemcount, $hourcount)
	{
		$xloc = self::$X_RIGHT - 125;
		$xloccount = self::$X_RIGHT - 25;
		$this->currentPage->drawText('Subtotal, Number of Items:', $xloc, $this->yloc);
		$itemcount = number_format($itemcount);
		$this->currentPage->drawText($itemcount, $xloccount, $this->yloc);
		$this->yloc -= 10;
		$this->currentPage->drawText('Subtotal, Number of Hours:', $xloc, $this->yloc);	
		$hourcount = number_format($hourcount, 2);
		$this->currentPage->drawText($hourcount, $xloccount, $this->yloc);	
		$this->yloc -= 15;
	}

	protected function getReportTitle()
	{
		$title1 = 'Completed Work by TUB';
		$startdate = $this->dateRange->getStartDate();	
		$enddate = $this->dateRange->getEndDate();	
		$title2 = 'From ' . $startdate . ' To ' . $enddate;
		
		return array($title1, $title2);
	}
	
	/*
	 * Draws the report title using upper case letters.
	 */
	protected function drawReportTitle($yloc)
	{
		$title1 = 'Completed Work by TUB';
		$startdate = $this->dateRange->getStartDate();	
		$enddate = $this->dateRange->getEndDate();	
		$title2 = 'From ' . $startdate . ' To ' . $enddate;
		
		$title1 = strtoupper($title1);
		$xloc = 320;
		$this->currentPage->setFont(self::$BOLD_FONT, 12);
		$this->currentPage->drawText($title1, $xloc, $this->yloc);
		$this->yloc = $yloc - 20;
		
		$title2 = strtoupper($title2);
		$xloc = 310;
		$this->currentPage->setFont(self::$BOLD_FONT, 12);
		$this->currentPage->drawText($title2, $xloc, $this->yloc);
		$yloc -= 15;

		$this->yloc = $yloc - 35;
	}	
	
	protected function getCustomFooter()
	{
		return '';
	}
}
?>