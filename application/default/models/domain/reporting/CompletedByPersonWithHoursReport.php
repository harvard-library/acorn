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
class CompletedByPersonWithHoursReport extends AcornReport  
{
	private $people;
	private $dateRange;
	
	/*
	 * @param Person - the people
	 * @param DateRange - the date range
	 */
	public function __construct(array $people, DateRange $dateRange)
	{
		$this->people = $people;
		$this->dateRange = $dateRange;
		parent::__construct('CompletedWithHoursWorkForMultiple');
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
		$comphrsloc = self::$X_LEFT + 650;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$this->currentPage->drawText('Activity', $activityloc, $this->yloc);
		$this->currentPage->drawText('ACORN#', $acornnumloc, $this->yloc);
		$this->currentPage->drawText('Project', $projectloc, $this->yloc);
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
		$items = CombinedRecordsReportsDAO::getCombinedRecordsReportsDAO()->getCompletedByPeople($this->dateRange, $this->people);
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
		
		$activityloc = self::$X_LEFT+5;
		$acornnumloc = self::$X_LEFT + 75;
		$projectloc = self::$X_LEFT + 125;
		$callloc = self::$X_LEFT + 250;
		$titleloc = self::$X_LEFT + 375;
		$authorloc = self::$X_LEFT + 500;
		$itemloc = self::$X_LEFT + 600;
		$comphrsloc = self::$X_LEFT + 650;
		
		$totalitemcount = 0;
		$totalhourcount = 0.0;
		
		$itemcount = 0;
		$hourcount = 0.0;
		
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
			$hours = 0;
			foreach ($this->people as $person)
			{
				$hours += ReportDAO::getReportDAO()->getConservatorHours($item['ReportID'], $person);
			}
			$this->currentPage->drawText($hours, $comphrsloc, $this->yloc);
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
			$hourcount += $hours;
			$totalhourcount += $hours;
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
		$names = "Completed Work for ";
		$delimiter = "";
		$titles = array();
		$counter = 0;
		foreach ($this->people as $person)
		{
			$names .= $delimiter . $person->getDisplayName();
			$counter++;
			//Stop after three names so the string doesn't get too long.
			if ($counter == 3)
			{
				array_push($titles, $names);
				$names = "";
				$delimiter = "";
				$counter = 0;
			}
			else 
			{
				$delimiter = ", ";
			}
			
		}
		if ($counter != 3 && !empty($names))
		{
			array_push($titles, $names);
		}
		$startdate = $this->dateRange->getStartDate();	
		$enddate = $this->dateRange->getEndDate();	
		array_push($titles, 'From ' . $startdate . ' To ' . $enddate);
		
		return $titles;
	}
	
	protected function getCustomFooter()
	{
		return '';
	}
}
?>