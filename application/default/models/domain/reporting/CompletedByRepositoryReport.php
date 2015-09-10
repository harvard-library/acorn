<?php
class CompletedByRepositoryReport extends AcornReport  
{
	private $repository;
	private $dateRange;
	
	/*
	 * @param Location - the repository
	 * @param DateRange - the date range
	 */
	public function __construct(Location $repository, DateRange $dateRange)
	{
		$this->repository = $repository;
		$this->dateRange = $dateRange;
		parent::__construct('CompletedWorkFor' . $repository->getLocation() . 'byActivity');
		$this->setDefaultPageSize(Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE);
	}
	
	protected function drawReportBody()
	{
		$this->drawHeaders();
		$this->drawBody();
	}
	
	private function getOSWItems(Department $department = NULL)
	{
		return OSWReportsDAO::getOSWReportsDAO()->getCompletedByRepository($this->dateRange, $this->repository, $department);
	}
	
	private function getItems(Department $department = NULL)
	{
		return ItemReportsDAO::getItemReportsDAO()->getCompletedByRepository($this->dateRange, $this->repository, $department);
	}
	
	private function drawHeaders()
	{
		$callloc = self::$X_LEFT;
		$titleloc = self::$X_LEFT + 100;
		$authorloc = self::$X_LEFT + 225;
		$itemloc = self::$X_LEFT + 325;
		$acornnumloc = self::$X_LEFT + 375;
		$comphrsloc = self::$X_LEFT + 425;
		$purposeloc = self::$X_LEFT + 475;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$this->currentPage->drawText('Call Numbers', $callloc, $this->yloc);
		$this->currentPage->drawText('Title', $titleloc, $this->yloc);
		$this->currentPage->drawText('Author/Artist', $authorloc, $this->yloc);
		$this->currentPage->drawText('#Items', $itemloc, $this->yloc);
		$this->currentPage->drawText('ACORN#', $acornnumloc, $this->yloc);
		$this->currentPage->drawText('CompHrs', $comphrsloc, $this->yloc);
		$this->currentPage->drawText('Purpose', $purposeloc, $this->yloc);
		$this->yloc -= 15;
	}
	
	private function drawBody()
	{
		$totalitems = 0;
		$totalhours = 0.0;
		
		//Get all OSW for this repository 
		//where the department is null
		$oswitems = $this->getOSWItems();
		if (!empty($oswitems))
		{
			$retval = $this->drawOSW('', $oswitems);
			$totalitems += $retval['SubtotalCount'];
			$totalhours += $retval['SubtotalHours'];
		}
		
		//Get all items for this repository 
		//where the department is null
		$items = $this->getItems();
		if (!empty($items))
		{
			$retval = $this->drawItems('', $items);
			$totalitems += $retval['SubtotalCount'];
			$totalhours += $retval['SubtotalHours'];
		}
		
		//Get the departments for the given repository.
		$departments = $this->repository->getDepartments();
		//For each department, get the corresponding osw and items
		foreach ($departments as $department)
		{
			$oswitems = $this->getOSWItems($department);
			if (!empty($oswitems))
			{
				$retval = $this->drawOSW($department->getDepartmentName(), $oswitems);
				$totalitems += $retval['SubtotalCount'];
				$totalhours += $retval['SubtotalHours'];
			}
			$items = $this->getItems($department);
			if (!empty($items))
			{
				$retval = $this->drawItems($department->getDepartmentName(), $items);
				$totalitems += $retval['SubtotalCount'];
				$totalhours += $retval['SubtotalHours'];
			}
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
		$totalhours = number_format($totalhours,2);
		$this->currentPage->drawText($totalhours, $xloccount, $this->yloc);	
		$this->yloc -= 15;
	}
	
	private function drawOSW($departmentName, array $oswitems)
	{
		//Check the ylocation
		if($this->checkYLoc(70, Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE, self::$Y_TOP))
		{
			$this->drawHeaders();
		}
		$this->currentPage->setFont(self::$BOLD_FONT, 8);
		$this->currentPage->drawText($departmentName, self::$X_LEFT, $this->yloc);
		$this->yloc -= 10;
		$this->currentPage->drawText('On-site', self::$X_LEFT, $this->yloc);
		$this->yloc -= 15;
		
		$callloc = self::$X_LEFT;
		$titleloc = self::$X_LEFT + 100;
		$authorloc = self::$X_LEFT + 225;
		$itemloc = self::$X_LEFT + 325;
		$acornnumloc = self::$X_LEFT + 375;
		$comphrsloc = self::$X_LEFT + 425;
		$purposeloc = self::$X_LEFT + 475;
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		
		$totalitemcount = 0;
		$totalhourcount = 0.0;
		
		$itemcount = 0;
		$hourcount = 0.0;
		foreach ($oswitems as $item)
		{
			$this->currentPage->drawText('~', $callloc, $this->yloc);
			$titleyloc = $this->createWordWrap($item[ItemDAO::TITLE], $titleloc);
			$this->currentPage->drawText('~', $authorloc, $this->yloc);
			$this->currentPage->drawText($item['Count'], $itemloc, $this->yloc);
			$this->currentPage->drawText('OSW-' . $item[OnSiteWorkDAO::OSW_ID], $acornnumloc, $this->yloc);
			$this->currentPage->drawText($item['TotalHours'], $comphrsloc, $this->yloc);
			$purposeyloc = $this->createWordWrap($item[PurposeDAO::PURPOSE], $purposeloc, 25);
			$this->yloc = min(array($titleyloc, $purposeyloc, $this->yloc - 10));
			//Check the ylocation
			if($this->checkYLoc(70, Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE, self::$Y_TOP))
			{
				$this->drawHeaders();
			}
			$itemcount += $item['Count'];
			$hourcount += $item['TotalHours'];
			$totalhourcount += $hourcount;
			$totalitemcount += $itemcount;
		}
		$this->yloc -=10;
		$this->drawSubtotals($itemcount, $hourcount);
		return array('SubtotalCount' => $itemcount, 'SubtotalHours' => $hourcount);
	}
	
	private function drawItems($departmentName, array $items)
	{
		//Check the ylocation
		if($this->checkYLoc(70, Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE, self::$Y_TOP))
		{
			$this->drawHeaders();
		}
		$this->currentPage->setFont(self::$BOLD_FONT, 8);
		$this->currentPage->drawText($departmentName, self::$X_LEFT, $this->yloc);
		$this->yloc -= 15;
		$this->currentPage->drawText('Treatment', self::$X_LEFT, $this->yloc);
		$this->yloc -= 15;
		
		$callloc = self::$X_LEFT;
		$titleloc = self::$X_LEFT + 100;
		$authorloc = self::$X_LEFT + 225;
		$itemloc = self::$X_LEFT + 325;
		$acornnumloc = self::$X_LEFT + 375;
		$comphrsloc = self::$X_LEFT + 425;
		$purposeloc = self::$X_LEFT + 475;
		
		$itemcount = 0;
		$hourcount = 0.0;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		foreach ($items as $item)
		{
			$callwordwraparray = $this->createCallNumberWordWrap($item['CallNumbers'], $callloc, 20, 10, TRUE, FALSE);
			$currentpageindex = count($this->pages) - ($callwordwraparray['newpages']);
			$temppage = $this->currentPage;
			//If we added new pages, then go back to the original page.
			if ($callwordwraparray['newpages'] > 0)
			{
				$this->currentPage = $this->pages[$currentpageindex];
			}
			//$callyloc = $this->createWordWrap($item['CallNumbers'], $callloc, 20, 10, TRUE);
			$titleyloc = $this->createWordWrap($item[ItemDAO::TITLE], $titleloc);
			$authyloc = $this->createWordWrap($item[ItemDAO::AUTHOR_ARTIST], $authorloc, 25);
			$this->currentPage->drawText($item['Count'], $itemloc, $this->yloc);
			$this->currentPage->drawText($item[ItemDAO::ITEM_ID], $acornnumloc, $this->yloc);
			$this->currentPage->drawText($item['TotalHours'], $comphrsloc, $this->yloc);
			$purposeyloc = $this->createWordWrap($item[PurposeDAO::PURPOSE], $purposeloc, 25);
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
				$this->yloc = min(array($authyloc, $callwordwraparray['yloc'], $titleyloc, $purposeyloc, $this->yloc - 10));
			}
			//Check the ylocation
			if($this->checkYLoc(70, Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE, self::$Y_TOP))
			{
				$this->drawHeaders();
			}
			
			$itemcount += $item['Count'];
			$hourcount += $item['TotalHours'];
		}
		$this->yloc -=10;
		$this->drawSubtotals($itemcount, $hourcount);
		return array('SubtotalCount' => $itemcount, 'SubtotalHours' => $hourcount);
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
		$hourcount = number_format($hourcount,2);
		$this->currentPage->drawText($hourcount, $xloccount, $this->yloc);	
		$this->yloc -= 15;
	}

	protected function getReportTitle()
	{
		$title1 = 'Completed Work for ' . $this->repository->getLocation() . ', by Activity';
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
		$title1 = 'Completed Work for ' . $this->repository->getLocation() . ', by Activity';
		$startdate = $this->dateRange->getStartDate();	
		$enddate = $this->dateRange->getEndDate();	
		$title2 = 'From ' . $startdate . ' To ' . $enddate;
		
		$title1 = strtoupper($title1);
		$length = strlen($title1);
		//Attempt to calculate the center of the page based on a font of 12
		//Some minor calculations estimates that the following will 
		//target the center of the page.
		$xloc = $length / .275;
		$xloc = $this->currentPage->getWidth()/2 - round($xloc, 0);
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