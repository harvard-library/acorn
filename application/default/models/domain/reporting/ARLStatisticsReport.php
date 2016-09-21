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
class ARLStatisticsReport extends AcornReport  
{
	private $dateRange;
	private $location;
	
	/*
	 * @param DateRange - the date range
	 */
	public function __construct(DateRange $dateRange, Location $location = NULL)
	{
		$this->dateRange = $dateRange;
		$this->location = $location;
		parent::__construct('ARLForWPC');
	}
	
	protected function drawReportBody()
	{
		$this->drawHeaders();
		$this->drawBody();
	}
	
	private function drawHeaders()
	{
		$statloc = self::$X_LEFT + 100;
		$valueloc = self::$X_LEFT + 375;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		$this->currentPage->drawText('ARL Statistic', $statloc, $this->yloc);
		$this->currentPage->drawText('Value', $valueloc, $this->yloc);
		$this->yloc -= 15;
	}
	
	private function drawBody()
	{
		$statloc = self::$X_LEFT + 100;
		$valueloc = self::$X_LEFT + 375;
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		
		$level1 = ItemReportsDAO::getItemReportsDAO()->getTreatmentLevelCounts(1, $this->dateRange, $this->location);
		$level2 = ItemReportsDAO::getItemReportsDAO()->getTreatmentLevelCounts(2, $this->dateRange, $this->location);
		$level3 = ItemReportsDAO::getItemReportsDAO()->getTreatmentLevelCounts(3, $this->dateRange, $this->location);
		$sheets = ItemReportsDAO::getItemReportsDAO()->getCountsByType(array('Sheets'), $this->dateRange, $this->location);
		$photos = ItemReportsDAO::getItemReportsDAO()->getCountsByType(array('Photos', 'Other'), $this->dateRange, $this->location);
		$housings = ItemReportsDAO::getItemReportsDAO()->getCountsByType(array('Housings'), $this->dateRange, $this->location);
		
		$this->currentPage->drawText('Number of volumes/pamphlets given level 1 conservation treatment', $statloc, $this->yloc);
		$this->currentPage->drawText($level1, $valueloc, $this->yloc);
		$this->yloc -= 10;
		
		$this->currentPage->drawText('Number of volumes/pamphlets given level 2 conservation treatment', $statloc, $this->yloc);
		$this->currentPage->drawText($level2, $valueloc, $this->yloc);
		$this->yloc -= 10;
		
		$this->currentPage->drawText('Number of volumes/pamphlets given level 3 conservation treatment', $statloc, $this->yloc);
		$this->currentPage->drawText($level3, $valueloc, $this->yloc);
		$this->yloc -= 10;
		
		$this->currentPage->drawText('Number of unbound sheets given conservation treatment', $statloc, $this->yloc);
		$this->currentPage->drawText($sheets, $valueloc, $this->yloc);
		$this->yloc -= 10;
		
		$this->currentPage->drawText('Number of photographs and non-paper items given conservation treatment', $statloc, $this->yloc);
		$this->currentPage->drawText($photos, $valueloc, $this->yloc);
		$this->yloc -= 10;
		
		$this->currentPage->drawText('Number of custom-fitted protective enclosures constructed', $statloc, $this->yloc);
		$this->currentPage->drawText($housings, $valueloc, $this->yloc);
		$this->yloc -= 10;
		
		//$this->currentPage->drawText('Number of other items given conservation treatment', $statloc, $this->yloc);
		//$this->currentPage->drawText($other, $valueloc, $this->yloc);
		//$this->yloc -= 10;
	}


	protected function getReportTitle()
	{
		if (is_null($this->location))
		{
			$title1 = 'ARL For WPC';
		}
		else 
		{
			$title1 = 'ARL Statistics For ' . $this->location->getLocation();
		}
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
		if (is_null($this->location))
		{
			$title1 = 'ARL For WPC';
			$title1 = strtoupper($title1);
			$xloc = 270;
		}
		else 
		{
			$title1 = 'ARL Statistics For ' . $this->location->getLocation();
			$length = strlen($title1);
			//Attempt to calculate the center of the page based on a font of 12
			//Some minor calculations estimates that the following will 
			//target the center of the page.
			$xloc = $length / .375;
			$xloc = $this->currentPage->getWidth()/2 - round($xloc, 0);
		
		}
		$this->currentPage->setFont(self::$BOLD_FONT, 12);
		$this->currentPage->drawText($title1, $xloc, $this->yloc);
		$this->yloc = $yloc - 20;
		
		$startdate = $this->dateRange->getStartDate();	
		$enddate = $this->dateRange->getEndDate();	
		$title2 = 'From ' . $startdate . ' To ' . $enddate;

		$title2 = strtoupper($title2);
		$xloc = 225;
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