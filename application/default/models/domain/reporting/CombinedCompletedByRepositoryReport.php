<?php
/**
 * Copyright 2016 The President and Fellows of Harvard College
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */
class CombinedCompletedByRepositoryReport extends AcornReport  
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
		parent::__construct('CombinedCompletedWorkFor' . $repository->getLocation());
		$this->setDefaultPageSize(Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE);
	}
	
	protected function drawReportBody()
	{
		$this->drawHeaders();
		$this->drawBody();
	}
	
	private function drawHeaders()
	{
		$callloc = self::$X_LEFT;
		$titleloc = self::$X_LEFT + 100;
		$authorloc = self::$X_LEFT + 225;
		$itemloc = self::$X_LEFT + 315;
		$acornnumloc = self::$X_LEFT + 365;
		$prophrsloc = self::$X_LEFT + 415;
		$comphrsloc = self::$X_LEFT + 465;
		$activityloc = self::$X_LEFT + 515;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$this->currentPage->drawText('Call Numbers', $callloc, $this->yloc);
		$this->currentPage->drawText('Title', $titleloc, $this->yloc);
		$this->currentPage->drawText('Author/Artist', $authorloc, $this->yloc);
		$this->currentPage->drawText('#Items', $itemloc, $this->yloc);
		$this->currentPage->drawText('ACORN#', $acornnumloc, $this->yloc);
		$this->currentPage->drawText('PropHrs', $prophrsloc, $this->yloc);
		$this->currentPage->drawText('CompHrs', $comphrsloc, $this->yloc);
		$this->currentPage->drawText('Activity', $activityloc, $this->yloc);
		$this->yloc -= 15;
	}
	
	private function drawBody()
	{
		$totalitems = 0;
		$totalhours = 0.0;
		
		//Get all items for this repository 
		//where the department is null
		$items = CombinedRecordsReportsDAO::getCombinedRecordsReportsDAO()->getCompletedByRepository($this->dateRange, $this->repository, NULL);
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
			$items = CombinedRecordsReportsDAO::getCombinedRecordsReportsDAO()->getCompletedByRepository($this->dateRange, $this->repository, $department);
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
	
	
	private function drawItems($departmentName, array $items)
	{
		//Check the ylocation
		if($this->checkYLoc(70, Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE, self::$Y_TOP))
		{
			$this->drawHeaders();
		}
		$this->currentPage->setFont(self::$BOLD_FONT, 8);
		$this->currentPage->drawText($departmentName, self::$X_LEFT-5, $this->yloc);
		$this->yloc -= 15;
		
		$callloc = self::$X_LEFT;
		$titleloc = self::$X_LEFT + 100;
		$authorloc = self::$X_LEFT + 225;
		$itemloc = self::$X_LEFT + 315;
		$acornnumloc = self::$X_LEFT + 365;
		$prophrsloc = self::$X_LEFT + 415;
		$comphrsloc = self::$X_LEFT + 465;
		$activityloc = self::$X_LEFT + 515;
		
		$itemcount = 0;
		$hourcount = 0.0;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$purpose = '';
		foreach ($items as $item)
		{
			//If the purpose changed, then print the new purpose on a line.
			if ($purpose != $item[PurposeDAO::PURPOSE])
			{
				$this->yloc -= 5;
				$this->currentPage->setFont(self::$BOLD_FONT, 8);
				$this->currentPage->drawText($item[PurposeDAO::PURPOSE], $callloc, $this->yloc);
				$this->yloc -= 15;
				$this->currentPage->setFont(self::$REGULAR_FONT, 8);
			}
			
			$purpose = $item[PurposeDAO::PURPOSE];
			
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
			$min = $item[ProposalDAO::MIN_HOURS];
			$max = $item[ProposalDAO::MAX_HOURS];
			$proposedhours = $min;
			if ($max > $min)
			{
				$proposedhours = $min . '-' . $max;
			}
			$this->currentPage->drawText($proposedhours, $prophrsloc, $this->yloc);
			$this->currentPage->drawText($item['Count'], $itemloc, $this->yloc);
			$this->currentPage->drawText($recordNumber, $acornnumloc, $this->yloc);
			$this->currentPage->drawText($item['TotalHours'], $comphrsloc, $this->yloc);
			$this->currentPage->drawText($activity, $activityloc, $this->yloc);
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
				$this->yloc = min(array($authyloc, $callwordwraparray['yloc'], $titleyloc, $this->yloc - 10));
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
		$title1 = 'Completed Work for ' . $this->repository->getLocation();
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
		$title1 = 'Completed Work for ' . $this->repository->getLocation();
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