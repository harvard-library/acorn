<?php
class CompletedByProjectReport extends AcornReport  
{
	private $project;
	private $dateRange;
	
	/*
	 * @param Project - the project
	 * @param DateRange - the date range
	 */
	public function __construct(Project $project, DateRange $dateRange)
	{
		$this->project = $project;
		$this->dateRange = $dateRange;
		parent::__construct('CompletedWorkFor' . $project->getProjectName());
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
		$workloc = self::$X_LEFT + 75;
		$callloc = self::$X_LEFT + 150;
		$titleloc = self::$X_LEFT + 300;
		$authorloc = self::$X_LEFT + 500;
		$itemloc = self::$X_LEFT + 600;
		$comphrsloc = self::$X_LEFT + 650;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$this->currentPage->drawText('ACORN#', $acornnumloc, $this->yloc);
		$this->currentPage->drawText('WrkLoc', $workloc, $this->yloc);
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
		$items = CombinedRecordsReportsDAO::getCombinedRecordsReportsDAO()->getCompletedByProject($this->dateRange, $this->project);
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
		$totalhours = number_format($totalhours,2);
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
		$workloc = self::$X_LEFT + 75;
		$callloc = self::$X_LEFT + 150;
		$titleloc = self::$X_LEFT + 300;
		$authorloc = self::$X_LEFT + 500;
		$itemloc = self::$X_LEFT + 600;
		$comphrsloc = self::$X_LEFT + 650;
		$totalitemcount = 0;
		$totalhourcount = 0.0;
		
		$itemcount = 0;
		$hourcount = 0.0;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$location = '';
		foreach ($items as $item)
		{
			//If the location changed, then print the new location on a line.
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
				$this->currentPage->drawText($item[LocationDAO::LOCATION], self::$X_LEFT-5, $this->yloc);
				$this->yloc -= 15;
				$this->currentPage->setFont(self::$REGULAR_FONT, 8);
			}
			
			$location = $item[LocationDAO::LOCATION];
			
			if ($item['RecordType'] == 'Item')
			{
				$recordNumber = $item[CombinedRecordsDAO::RECORD_ID];
			}
			else 
			{
				$recordNumber = 'OSW-' . $item[CombinedRecordsDAO::RECORD_ID];
			}
			
			$worklocationname = $item[LocationDAO::ACRONYM];
			if (empty($workloc))
			{
				$worklocationname = $item[LocationDAO::LOCATION];
			}
			
			$this->currentPage->drawText($recordNumber, $acornnumloc, $this->yloc);
			$worklocyloc = $this->createWordWrap($worklocationname, $workloc);
			$callwordwraparray = $this->createCallNumberWordWrap($item['CallNumbers'], $callloc, 40, 10, TRUE, FALSE);
			$currentpageindex = count($this->pages) - ($callwordwraparray['newpages']);
			$temppage = $this->currentPage;
			//If we added new pages, then go back to the original page.
			if ($callwordwraparray['newpages'] > 0)
			{
				$this->currentPage = $this->pages[$currentpageindex];
			}
			$titleyloc = $this->createWordWrap($item[ItemDAO::TITLE], $titleloc, 50);
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
				$this->yloc = min(array($authyloc, $callwordwraparray['yloc'], $titleyloc, $worklocyloc, $this->yloc - 10));
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
		$hourcount = number_format($hourcount,2);
		$this->currentPage->drawText($hourcount, $xloccount, $this->yloc);	
		$this->yloc -= 15;
	}

	protected function getReportTitle()
	{
		$title1 = 'Completed Treatments for ' . $this->project->getProjectName();
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
		$title1 = 'Completed Treatments for ' . $this->project->getProjectName();
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