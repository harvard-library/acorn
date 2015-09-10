<?php
class CompletedByPersonReport extends AcornReport  
{
	/**
	 * @var Person
	 */
	private $person;
	
	/**
	 * @var DateRange
	 */
	private $dateRange;
	
	/**
	 * @param Person - the person
	 * @param DateRange - the date range
	 */
	public function __construct(Person $person, DateRange $dateRange)
	{
		$this->person = $person;
		$this->dateRange = $dateRange;
		parent::__construct('CompletedWorkFor' . $person->getDisplayName());
		$this->setDefaultPageSize(Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE);
	}
	
	protected function drawReportBody()
	{
		$this->drawHeaders();
		$this->drawBody();
	}
	
	private function drawHeaders()
	{
		$activityloc = self::$X_LEFT+5;
		$acornnumloc = self::$X_LEFT + 75;
		$projectloc = self::$X_LEFT + 125;
		$callloc = self::$X_LEFT + 250;
		$titleloc = self::$X_LEFT + 375;
		$authorloc = self::$X_LEFT + 500;
		$itemloc = self::$X_LEFT + 600;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$this->currentPage->drawText('Activity', $activityloc, $this->yloc);
		$this->currentPage->drawText('ACORN#', $acornnumloc, $this->yloc);
		$this->currentPage->drawText('Project', $projectloc, $this->yloc);
		$this->currentPage->drawText('Call Numbers', $callloc, $this->yloc);
		$this->currentPage->drawText('Title', $titleloc, $this->yloc);
		$this->currentPage->drawText('Author/Artist', $authorloc, $this->yloc);
		$this->currentPage->drawText('#Items', $itemloc, $this->yloc);
		$this->yloc -= 15;
	}
	
	private function drawBody()
	{
		$totalitems = 0;
		
		//Get all items for this repository 
		//where the department is null
		$items = CombinedRecordsReportsDAO::getCombinedRecordsReportsDAO()->getCompletedByPerson($this->dateRange, $this->person);
		if (!empty($items))
		{
			$retval = $this->drawItems($items);
			$totalitems = $retval['SubtotalCount'];
		}

		//Totals
		$this->currentPage->setFont(self::$BOLD_FONT, 8);
		$xloc = self::$X_RIGHT - 125;
		$xloccount = self::$X_RIGHT - 25;
		$this->currentPage->drawText('Total, Number of Items:', $xloc, $this->yloc);
		$totalitems = number_format($totalitems);
		$this->currentPage->drawText($totalitems, $xloccount, $this->yloc);
		$this->yloc -= 15;
	}
	
	
	private function drawItems(array $items)
	{
		//Check the ylocation
		if($this->checkYLoc(70, Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE, self::$Y_TOP))
		{
			$this->drawHeaders();
		}
		
		$activityloc = self::$X_LEFT+5;
		$acornnumloc = self::$X_LEFT + 75;
		$projectloc = self::$X_LEFT + 125;
		$callloc = self::$X_LEFT + 250;
		$titleloc = self::$X_LEFT + 375;
		$authorloc = self::$X_LEFT + 500;
		$itemloc = self::$X_LEFT + 600;
		
		$totalitemcount = 0;
		
		$itemcount = 0;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$location = '';
		$department = '';
		foreach ($items as $item)
		{
			//If the location changed, then print the new location on a line.
			if ($location != $item[LocationDAO::LOCATION])
			{
				if (!empty($location))
				{
					$this->drawSubtotals($itemcount);
					$itemcount = 0;
					//Check the ylocation
					if($this->checkYLoc(70, Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE, self::$Y_TOP))
					{
						$this->drawHeaders();
					}
				}
				$this->yloc -= 5;
				$this->currentPage->setFont(self::$BOLD_FONT, 8);
				$this->currentPage->drawText($item[LocationDAO::LOCATION], self::$X_LEFT-5, $this->yloc);
				$this->yloc -= 15;
				$this->currentPage->setFont(self::$REGULAR_FONT, 8);
			}
			
			//If the department changed, then print the new department on a line.
			if (!is_null($item[DepartmentDAO::DEPARTMENT_NAME]) && $department != $item[DepartmentDAO::DEPARTMENT_NAME])
			{
				$this->yloc -= 5;
				$this->currentPage->setFont(self::$BOLD_FONT, 8);
				$this->currentPage->drawText($item[DepartmentDAO::DEPARTMENT_NAME], self::$X_LEFT, $this->yloc);
				$this->yloc -= 15;
				$this->currentPage->setFont(self::$REGULAR_FONT, 8);
			}
			
			$location = $item[LocationDAO::LOCATION];
			$department = $item[DepartmentDAO::DEPARTMENT_NAME];
			
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
			
			$this->currentPage->drawText($activity, $activityloc, $this->yloc);
			$this->currentPage->drawText($recordNumber, $acornnumloc, $this->yloc);
			$projectyloc = $this->createWordWrap($item[ProjectDAO::PROJECT_NAME], $projectloc, 25);
			$callwordwraparray = $this->createCallNumberWordWrap($item['CallNumbers'], $callloc, 20, 10, TRUE, FALSE);
			$currentpageindex = count($this->pages) - ($callwordwraparray['newpages']);
			$temppage = $this->currentPage;
			//If we added new pages, then go back to the original page.
			if ($callwordwraparray['newpages'] > 0)
			{
				$this->currentPage = $this->pages[$currentpageindex];
			}
			$titleyloc = $this->createWordWrap($item[ItemDAO::TITLE], $titleloc);
			$authyloc = $this->createWordWrap($item[ItemDAO::AUTHOR_ARTIST], $authorloc, 25);
			$this->currentPage->drawText($item['Count'], $itemloc, $this->yloc);
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
				$this->yloc = min(array($authyloc, $callwordwraparray['yloc'], $titleyloc, $projectyloc, $this->yloc - 10));
			}
			//Check the ylocation
			if($this->checkYLoc(70, Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE, self::$Y_TOP))
			{
				$this->drawHeaders();
			}
			
			$itemcount += $item['Count'];
			$totalitemcount += $item['Count'];
		}
		$this->yloc -=10;
		$this->drawSubtotals($itemcount);
		return array('SubtotalCount' => $totalitemcount);
	}
	
	private function drawSubtotals($itemcount)
	{
		$xloc = self::$X_RIGHT - 125;
		$xloccount = self::$X_RIGHT - 25;
		$this->currentPage->drawText('Subtotal, Number of Items:', $xloc, $this->yloc);
		$itemcount = number_format($itemcount);
		$this->currentPage->drawText($itemcount, $xloccount, $this->yloc);
		$this->yloc -= 15;
	}

	protected function getReportTitle()
	{
		$title1 = 'Completed Work for ' . $this->person->getDisplayName();
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
		$title1 = 'Completed Work for ' . $this->person->getDisplayName();
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