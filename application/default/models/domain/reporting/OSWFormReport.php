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
class OSWFormReport extends AcornReport  
{
	//This must be an OSW item.
	private $oswObject;
	
	/*
	 * Constructs the transfer report
	 * 
	 * @param Object oswObject - one of Item or Group
	 */
	public function __construct(OnSiteWork $oswObject)
	{
		$this->oswObject = $oswObject;
		$oswID = $oswObject->getOSWID();
		parent::__construct('OSW' . $oswID);
	}
	
	protected function drawReportBody()
	{
		$this->drawReportInformation();
		$this->drawReportText();
		$this->drawSignatureLines();		
	}
	
	private function drawReportInformation()
	{
		$xlocheaderleft = self::$X_LEFT;
		$xlocheaderright = $this->currentPage->getWidth()/2;
		$xlocdataleft = self::$X_LEFT + 75;
		$xlocdataright = $this->currentPage->getWidth()/2 + 80;
		
		//Title
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Title:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->createWordWrap($this->oswObject->getTitle(), $xlocdataleft, 40, 12);
		$this->yloc = min(array($this->yloc-15, $wrapyloc-3));
		
		//Repository
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Repository:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->createWordWrap($this->getRepository(), $xlocdataleft, 40, 12);
		
		//Work Location
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Work Location:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getWorkLocation(), $xlocdataright, $this->yloc);
		$this->yloc = min(array($this->yloc-15, $wrapyloc-3));
		
		//Curator
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Curator:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getCurator(), $xlocdataleft, $this->yloc);
		
		//Work Type
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Work Type(s):', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getWorkTypes(), $xlocdataright, $this->yloc);
		
		$this->yloc -= 15;
		
		//Project/Group
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Project/Group:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$projectgroup = $this->getProject() . '/' . $this->getGroup();
		$wrapyloc = $this->createWordWrap($projectgroup, $xlocdataleft, 40, 12);
		
		//ItemCounts
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Item Counts:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$countyloc = $this->createWordWrap($this->getCounts(), $xlocdataright, 30, 12);
		
		$this->yloc = min(array($this->yloc-15, $wrapyloc-3, $countyloc-3));

                /* No longer used
		//Work Done By Initials
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Work Done By:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$workdoneby = implode(', ', $this->getWorkDoneBy());
		$wrapyloc = $this->createWordWrap('*' . $workdoneby, $xlocdataleft, 40, 12);
                */
                
		//Format
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Format:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		if (!is_null($this->oswObject->getFormatID()))
		{
			$format = FormatDAO::getFormatDAO()->getFormat($this->oswObject->getFormatID());
			$this->currentPage->drawText($format->getFormat(), $xlocdataright, $this->yloc);
		}
		
		$this->yloc = min(array($this->yloc-15, $wrapyloc-3));
		
		$workdates = $this->oswObject->getDateRange();
		//Start Date
		$zendstartdate = new Zend_Date($workdates->getStartDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    	$startdate = $zendstartdate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Start Date:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($startdate, $xlocdataleft, $this->yloc);
		
		//Housings
		$housings = $this->oswObject->getFunctions()->getReport()->getCounts()->getHousingCount();
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Housing:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($housings, $xlocdataright, $this->yloc);
		
		$this->yloc -= 15;
		
		//End Date
		$zendenddate = new Zend_Date($workdates->getEndDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    	$enddate = $zendenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('End Date:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($enddate, $xlocdataleft, $this->yloc);
		
		$this->yloc -= 15;
		
		//Comments
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Comments:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->parseAndDrawNewlineTextBlock($this->oswObject->getComments(), $xlocdataleft, TRUE, 90, 12);
		
		$this->yloc = min($this->yloc - 25, $wrapyloc - 13);
	}
	
	private function drawReportText()
	{
		if ($this->checkYLoc(70))
		{
			$this->drawReportTitle(self::$Y_TOP);
		}
		//Work Description (which is stored in the Report Treatment
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Work Description:', self::$X_LEFT, $this->yloc);
		$this->yloc -= 15;
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$treatment = $this->oswObject->getFunctions()->getReport()->getTreatment();
		if (is_null($treatment))
		{
			$treatment = '';
		}
		$wrapyloc = $this->parseAndDrawNewlineTextBlock($treatment, self::$X_LEFT, TRUE, 120, 12, TRUE);
		$this->yloc = min($this->yloc - 25, $wrapyloc - 13);
		
		if ($this->checkYLoc())
		{
			$this->drawReportTitle(self::$Y_TOP);
		}
		
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Proposed On-Site Hours: ', self::$X_LEFT, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->oswObject->getProposedHours(), self::$X_LEFT+130, $this->yloc);
		
		$this->yloc -= 15;
		
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Completed On-Site Hours: ', self::$X_LEFT, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->oswObject->getFunctions()->getReport()->getTotalHours(), self::$X_LEFT+130, $this->yloc);
		
		$this->yloc -= 15;
		
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('AssociatedFiles: ', self::$X_LEFT, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->createWordWrap($this->getAssociatedFiles(), self::$X_LEFT+90, 80, 12);
		
		$this->yloc = min($this->yloc - 25, $wrapyloc - 13);
	}
	
	protected function drawFooter()
	{
		$footer = $this->getCustomFooter();
		$length = strlen($footer);
		//Attempt to calculate the center of the page based on a font of 8
		//Some minor calculations estimates that the following will 
		//target the center of the page.
		$customxloc = $length * 2;
		$customxloc = $this->currentPage->getWidth()/2 - round($customxloc, 0);
		
		$count = count($this->pages);
		$counter = 1;
		foreach ($this->pages as $page)
		{
			$zenddate = new Zend_Date();
			$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
			$page->setFont(self::$REGULAR_FONT, 8);
// No longer used			$page->drawText('*' . $this->getWorkDoneByFooter(), self::$XLOC_PAGE_FOOTER, self::$YLOC_FOOTER + 10);
			$page->drawText('Page ' . $counter . ' of ' . $count, self::$XLOC_PAGE_FOOTER, self::$YLOC_FOOTER);
			$page->drawText($footer, $customxloc, self::$YLOC_FOOTER);
			$page->drawText($date, self::$XLOC_DATE_FOOTER, self::$YLOC_FOOTER);
			$counter++;
		}
	}
	
	private function drawSignatureLines()
	{
		if ($this->checkYLoc())
		{
			$this->drawReportTitle(self::$Y_TOP);
		}
		
		$this->currentPage->setLineWidth(.5);
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$workdoneby = $this->getWorkDoneBy();
		$workbystring = '*';
		$counter = 1;
		foreach ($workdoneby as $workby)
		{
			if ($counter > 1 && $counter % 2 == 1)
			{
				$this->currentPage->drawText($workbystring, self::$X_LEFT + 80, $this->yloc);
				$this->yloc -= 10;	
				$workbystring = $workby;
			}
			elseif ($counter > 1)
			{
				$workbystring .= ', ' . $workby;
			}
			else
			{
				$workbystring = $workby;
			}
			$counter++;
		}
		$this->currentPage->drawText($workbystring, self::$X_LEFT + 80, $this->yloc);
		$this->yloc -= 5;	

                //Work By
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Work By:', self::$X_LEFT, $this->yloc);
		$this->currentPage->drawLine(self::$X_LEFT + 75, $this->yloc, self::$X_LEFT + 300, $this->yloc);
		
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Date:', self::$X_LEFT + 325, $this->yloc);
		$this->currentPage->drawLine(self::$X_LEFT + 355, $this->yloc - 2, self::$X_RIGHT, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText(date(ACORNConstants::$DATE_FORMAT), self::$X_LEFT + 360, $this->yloc + 2);
		
		$this->yloc -= 20;
	}
	
	protected function getReportTitle()
	{
		return 'ON-SITE WORK REPORT: OSW-' . $this->oswObject->getOSWID();
	}
	
	protected function getCustomFooter()
	{
		return '';
	}
	
	private function getCurator()
	{
		$curator = '';
		if (!is_null($this->oswObject->getCuratorID()))
		{
			$curator = PeopleDAO::getPeopleDAO()->getPerson($this->oswObject->getCuratorID())->getDisplayName();
		}
		return $curator;
	}
	
	private function getWorkTypes()
	{
		$worktypelist = $this->oswObject->getWorkTypes();
		$namelist = array();
		foreach ($worktypelist as $worktype)
		{
			array_push($namelist, $worktype->getWorkType());
		}
		$worktypes = implode(', ', $namelist);
		return $worktypes;
	}
	
	/*
	 * Returns the total counts
	 */
	private function getCounts()
	{
		$countstring = '';
		$counts = $this->oswObject->getFunctions()->getReport()->getCounts();
		$vol = $counts->getVolumeCount();
		$sheet = $counts->getSheetCount();
		$photo = $counts->getPhotoCount();
		$box = $counts->getBoxCount();
		$other = $counts->getOtherCount();
		
		$countstring = $vol == 1 ? $vol . ' volume, ' : $vol . ' volumes, ';
		$countstring .= $sheet == 1 ? $sheet . ' sheet, ' : $sheet . ' sheets, ';
		$countstring .= $photo == 1 ? $photo . ' photo, ' : $photo . ' photos, ';
		$countstring .= $box == 1 ? $box . ' box, ' : $box . ' boxes, ';
		$countstring .= $other . ' other';
		return $countstring;
	}

	private function getRepository()
	{
		$repository = '';
		if (!is_null($this->oswObject->getHomeLocationID()))
		{
			$repository = LocationDAO::getLocationDAO()->getLocation($this->oswObject->getHomeLocationID())->getLocation();
		}
		$department = '';
		if (!is_null($this->oswObject->getDepartmentID()))
		{
			$department = DepartmentDAO::getDepartmentDAO()->getDepartment($this->oswObject->getDepartmentID())->getDepartmentName();
		}
		if (!empty($repository) && !empty($department))
		{
			$repository = $repository . '/' . $department;
		}
		elseif (!empty($department))
		{
			$repository = $department;
		}
		return $repository;
	}
	
	private function getWorkLocation()
	{
		$worklocation = '';
		if (!is_null($this->oswObject->getFunctions()->getReport()->getWorkLocation()))
		{
			$worklocation = LocationDAO::getLocationDAO()->getLocation($this->oswObject->getFunctions()->getReport()->getWorkLocation())->getLocation();
		}
		return $worklocation;
	}
	
	private function getProject()
	{
		$project = '';
		if (!is_null($this->oswObject->getProjectID()))
		{
			$project = ProjectDAO::getProjectDAO()->getProject($this->oswObject->getProjectID())->getProjectName();
		}
		return $project;
	}
	
	private function getWorkDoneBy()
	{
		$workdonebylist = $this->oswObject->getFunctions()->getReport()->getReportConservators();
		$namelist = array();
		foreach ($workdonebylist as $conservatorobject)
		{
			$name = $conservatorobject->getConservator()->getDisplayName();
                        array_push($namelist, $name);
		}
               
		return array_unique($namelist);
	}
	
	private function getWorkDoneByFooter()
	{
		$workdoneby = '';
		$workdonebylist = $this->oswObject->getFunctions()->getReport()->getReportConservators();
		$namelist = array();
		foreach ($workdonebylist as $conservatorobject)
		{
			array_push($namelist, $conservatorobject->getConservator()->getDisplayName());
		}
		$workdoneby = implode(', ', $namelist);

		return $workdoneby;
	}
	
	private function getGroup()
	{
		$group = '';
		if (!is_null($this->oswObject->getGroupID()))
		{
			$group = GroupDAO::getGroupDAO()->getGroup($this->oswObject->getGroupID())->getGroupName();
		}
		
		return $group;
	}
	
	private function getAssociatedFiles()
	{
		$associatedfilelist = array();
		$files = $this->oswObject->getFiles();
		foreach ($files as $file)
		{
			$filesarray = explode('\\', $file->getPath());
			$filename = end($filesarray);
			array_push($associatedfilelist, $filename);
		}
		return implode(',', $associatedfilelist);
	}
}
?>