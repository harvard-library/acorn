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
class CompletedHoursByTubReport extends AcornReport  
{
private $dateRange;
	
	/*
	 * @param DateRange - the date range
	 */
	public function __construct(DateRange $dateRange)
	{
		$this->dateRange = $dateRange;
		parent::__construct('CompletedHoursForTub');
	}
	
	protected function drawReportBody()
	{
		$this->drawHeaders();
		$this->drawBody();
	}
	
	private function drawHeaders()
	{
		$activityloc = self::$X_LEFT + 250;
		$comphrsloc = self::$X_LEFT + 320;
		$itemloc = self::$X_LEFT + 370;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$this->currentPage->drawText('Activity', $activityloc, $this->yloc);
		$this->currentPage->drawText('CompHrs', $comphrsloc, $this->yloc);
		$this->currentPage->drawText('#Items', $itemloc, $this->yloc);
		$this->yloc -= 15;
	}
	
	private function drawBody()
	{
		$subtotalitems = 0;
		$subtotalhours = 0.0;
		$totalitems = 0;
		$totalhours = 0.0;

		$tubloc = self::$X_LEFT + 100;
		$reploc = self::$X_LEFT + 125;
		$activityloc = self::$X_LEFT + 250;
		$comphrsloc = self::$X_LEFT + 325;
		$itemloc = self::$X_LEFT + 375;
		
		//Get all repositories
		$repositories = LocationDAO::getLocationDAO()->getAllLocations(TRUE, array(LocationDAO::TUB, LocationDAO::LOCATION));
		//For each repository, get the item count, item hours, osw count, osw hours
		//and print it to the page.
		$tub = '';
		foreach ($repositories as $repository)
		{
			$itemcounts = ItemReportsDAO::getItemReportsDAO()->getCompletedItemCount($this->dateRange, $repository);
			$itemhours = ItemReportsDAO::getItemReportsDAO()->getCompletedItemHours($this->dateRange, $repository);
			$oswcounts = OSWReportsDAO::getOSWReportsDAO()->getCompletedOSWCount($this->dateRange, $repository);
			$oswhours = OSWReportsDAO::getOSWReportsDAO()->getCompletedOSWHours($this->dateRange, $repository);
			
			if ($itemcounts > 0 || $itemhours > 0 || $oswcounts > 0 || $oswhours > 0)
			{
				//Check the ylocation
				if($this->checkYLoc(70, Zend_Pdf_Page::SIZE_LETTER, self::$Y_TOP))
				{
					$this->drawHeaders();
				}
		
				//If this is a new tub, draw it
				if ($repository->getTUB() != $tub)
				{
					if (!empty($tub))
					{
						$this->yloc -= 5;
						$this->drawSubtotals($subtotalitems, $subtotalhours);
				
						$subtotalitems = 0;
						$subtotalhours = 0.0;
					}
					$this->currentPage->setFont(self::$BOLD_FONT, 8);
					$this->currentPage->drawText($repository->getTUB(), $tubloc, $this->yloc);
					$this->yloc -= 10;
				}
				
				//Draw the repository
				$this->currentPage->setFont(self::$BOLD_FONT, 8);
				$this->currentPage->drawText($repository->getLocation(), $reploc, $this->yloc);
				$this->yloc -= 10;
				
				$this->currentPage->setFont(self::$REGULAR_FONT, 8);
				
				//Treatment
				if ($itemcounts > 0 || $itemhours > 0)
				{
					$this->currentPage->drawText('Treatment', $activityloc, $this->yloc);
					$formatteditemhours = number_format($itemhours, 2);
					$this->currentPage->drawText($formatteditemhours, $comphrsloc, $this->yloc);
					$formatteditemcounts = number_format($itemcounts, 0);
					$this->currentPage->drawText($formatteditemcounts, $itemloc, $this->yloc);
					$this->yloc -=10;
				}
				//OSW
				if ($oswcounts > 0 || $oswhours > 0)
				{
					$this->currentPage->drawText('On-site', $activityloc, $this->yloc);
					$formattedoswhours = number_format($oswhours, 2);
					$this->currentPage->drawText($formattedoswhours, $comphrsloc, $this->yloc);
					$formattedoswcounts = number_format($oswcounts, 0);
					$this->currentPage->drawText($formattedoswcounts, $itemloc, $this->yloc);
					$this->yloc -=10;
				}
				
				$tub = $repository->getTUB();
			}
			
			$totalitems += $itemcounts + $oswcounts;
			$totalhours += $itemhours + $oswhours;
			$subtotalitems += $itemcounts + $oswcounts;
			$subtotalhours += $itemhours + $oswhours;
		}
		
		if ($subtotalitems > 0 || $subtotalhours > 0)
		{
			$this->yloc -= 5;
			$this->drawSubtotals($subtotalitems, $subtotalhours);
		}
			
			
			//Totals
		$this->currentPage->setFont(self::$BOLD_FONT, 8);
		$xloc = $activityloc+10;
		$xloccount = $activityloc+110;
		$this->currentPage->drawText('Total, Number of Items:', $xloc, $this->yloc);
		$totalitems = number_format($totalitems);
		$this->currentPage->drawText($totalitems, $xloccount, $this->yloc);
		$this->yloc -= 10;
		$this->currentPage->drawText('Total, Number of Hours:', $xloc, $this->yloc);	
		$totalhours = number_format($totalhours, 2);
		$this->currentPage->drawText($totalhours, $xloccount, $this->yloc);	
		$this->yloc -= 15;
	}
	
	
	private function drawSubtotals($itemcount, $hourcount)
	{
		$xloc = self::$X_LEFT + 260;
		$xloccount = self::$X_LEFT + 360;
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
		$title1 = 'All Completed Hours by TUB';
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
		$title1 = 'All Completed Hours by TUB';
		$startdate = $this->dateRange->getStartDate();	
		$enddate = $this->dateRange->getEndDate();	
		$title2 = 'From ' . $startdate . ' To ' . $enddate;
		
		$title1 = strtoupper($title1);
		$xloc = 210;
		$this->currentPage->setFont(self::$BOLD_FONT, 12);
		$this->currentPage->drawText($title1, $xloc, $this->yloc);
		$this->yloc = $yloc - 20;
		
		$title2 = strtoupper($title2);
		$xloc = 215;
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