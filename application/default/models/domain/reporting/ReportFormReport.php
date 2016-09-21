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
class ReportFormReport extends AcornReport  
{
	//This can be an Item or Group.
	private $reportObject;
	
	/*
	 * Constructs the transfer report
	 * 
	 * @param Object reportObject - one of Item or Group
	 */
	public function __construct($reportObject)
	{
		$this->reportObject = $reportObject;
		if (!($reportObject instanceof Item) && !($reportObject instanceof Group))
		{
			//Throw a runtime exception if an item or group isn't passed in
			throw new Zend_Exception('reportObject must be of type Item or Group');
		}
		$item = $this->getItem();
		$itemID = $item->getItemID();
		parent::__construct('Acorn' . $itemID . 'Report');
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
		$xlocdataleft = self::$X_LEFT + 70;
		$xlocdataright = $this->currentPage->getWidth()/2 + 80;
		
		$item = $this->getItem();
		
		//Repository
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Repository:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$repwrapyloc = $this->createWordWrap($this->getRepository($item), $xlocdataleft, 40, 12);
		
		if ($this->reportObject instanceof Item)
		{
			//Call Numbers
			$this->currentPage->setFont(self::$BOLD_FONT, 10);
			$this->currentPage->drawText('Call Number(s):', $xlocheaderright, $this->yloc);
			$this->currentPage->setFont(self::$REGULAR_FONT, 10);
			$wrapyloc = $this->createWordWrap($this->getCallNumbers($this->reportObject), $xlocdataright, 40, 12);
			$this->yloc = min(array($this->yloc-15, $wrapyloc, $repwrapyloc));
		}
		else 
		{
			$this->yloc = min(array($this->yloc-15, $repwrapyloc));
		}
		
		//Curator
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Curator:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getCurator($item), $xlocdataleft, $this->yloc);
		
		if ($this->reportObject instanceof Item)
		{
			//Author/Artist
			$this->currentPage->setFont(self::$BOLD_FONT, 10);
			$this->currentPage->drawText('Author/Artist:', $xlocheaderright, $this->yloc);
			$this->currentPage->setFont(self::$REGULAR_FONT, 10);
			$this->currentPage->drawText($this->reportObject->getAuthorArtist(), $xlocdataright, $this->yloc);
		}
		$this->yloc -= 15;
		
		//Project
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Project:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getProject($item), $xlocdataleft, $this->yloc);
		
		if ($this->reportObject instanceof Item)
		{
			//Title
			$this->currentPage->setFont(self::$BOLD_FONT, 10);
			$this->currentPage->drawText('Title:', $xlocheaderright, $this->yloc);
			$this->currentPage->setFont(self::$REGULAR_FONT, 10);
			$wrapyloc = $this->createWordWrap($this->reportObject->getTitle(), $xlocdataright, 45, 12);
			$this->yloc = min(array($this->yloc-15, $wrapyloc));
		}
		else 
		{
			$this->yloc -= 15;
		}
		
		//Group
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Group:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getGroup(), $xlocdataleft, $this->yloc);
		$this->yloc -= 15;
		
		//Coordinator
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Coordinator:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getCoordinator($item), $xlocdataleft, $this->yloc);
		
		if ($this->reportObject instanceof Item)
		{
			//Date of Object
			$this->currentPage->setFont(self::$BOLD_FONT, 10);
			$this->currentPage->drawText('Date of Object:', $xlocheaderright, $this->yloc);
			$this->currentPage->setFont(self::$REGULAR_FONT, 10);
			$this->currentPage->drawText($this->reportObject->getDateOfObject(), $xlocdataright, $this->yloc);
		}
		
		$this->yloc -= 15;
		
		//ItemCounts
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Item Counts:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		if ($this->reportObject instanceof Item)
		{
			$countyloc = $this->createWordWrap($this->getCounts(), $xlocdataleft, 38, 12);
		}
		else
		{
			$this->currentPage->drawText($this->getCounts(), $xlocdataleft, $this->yloc);
			$countyloc = $this->yloc-15;
		}
		
		if ($this->reportObject instanceof Item)
		{
			//Size
			$this->currentPage->setFont(self::$BOLD_FONT, 10);
			$this->currentPage->drawText('Size:', $xlocheaderright, $this->yloc);
			$this->currentPage->setFont(self::$REGULAR_FONT, 10);
			$this->currentPage->drawText($this->reportObject->getFunctions()->getProposal()->getDimensions()->getReportDimensions(), $xlocdataright-15, $this->yloc);
		}
		
		$this->yloc = min(array($this->yloc-15, $countyloc-3));
		
		
		//Comments
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Comments:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->parseAndDrawNewlineTextBlock($this->getComments($item), $xlocdataleft, TRUE, 90, 12);
		
		$this->yloc = min($this->yloc - 25, $wrapyloc - 13);
		
		//Pres Recs
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Preservation Recommendations:', $xlocheaderleft, $this->yloc);
		$this->yloc -= 15;
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->parseAndDrawNewlineTextBlock($this->getPreservationRecommendations($item), $xlocheaderleft, TRUE, 90, 12);
		
		$this->yloc = min($this->yloc - 25, $wrapyloc - 13);
	}
	
	private function drawReportText()
	{
		$item = $this->getItem();
		
		if ($this->checkYLoc(70))
		{
			$this->drawReportTitle(self::$Y_TOP);
		}
		//Treatment
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Treatment Performed:', self::$X_LEFT, $this->yloc);
		$this->yloc -= 15;
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->parseAndDrawNewlineTextBlock($this->getTreatment($item), self::$X_LEFT, TRUE, 120, 12, TRUE);
		$this->yloc = min($this->yloc - 25, $wrapyloc - 13);
		
		if ($this->checkYLoc())
		{
			$this->drawReportTitle(self::$Y_TOP);
		}
		
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$completedhours = 'Completed Hours: ' . $this->getReportHours();
		$this->currentPage->drawText($completedhours, self::$X_LEFT, $this->yloc);
		
		$this->yloc -= 25;
	}
	
	private function drawSignatureLines()
	{
		if ($this->checkYLoc())
		{
			$this->drawReportTitle(self::$Y_TOP);
		}
		
		$item = $this->getItem();

		$this->currentPage->setLineWidth(.5);
		
		//Work Done By By
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		//$this->currentPage->drawText($this->getWorkDoneBy($item), self::$X_LEFT + 80, $this->yloc + 2);
		$this->yloc += 2;
              
                $workdoneby = $this->getWorkDoneBy($item);
                $workdonebydeduped = array_unique($workdoneby);
                $workbystring = '';
                $counter = 1;
                foreach ($workdonebydeduped as $workby)
                {
                        if ($counter == 1)
                        {
                                $workbystring = $workby;
                        }
                        elseif ($counter > 1 && $counter % 2 == 1)
                        {                              
                                $this->currentPage->drawText($workbystring . ',', self::$X_LEFT + 80, $this->yloc);
                                $this->yloc -= 10;
                                $workbystring = $workby;
                        }
                        else
                        {
                                $workbystring .= ', ' . $workby;
                        }
                        $counter++;
                }
                $this->currentPage->drawText($workbystring, self::$X_LEFT + 80, $this->yloc);
        
 		$this->yloc -= 10;	

		$this->yloc += 5;
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Work Done By:', self::$X_LEFT, $this->yloc);
		$this->currentPage->drawLine(self::$X_LEFT + 75, $this->yloc, self::$X_LEFT + 300, $this->yloc);
			
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Date:', self::$X_LEFT + 325, $this->yloc);
		$this->currentPage->drawLine(self::$X_LEFT + 355, $this->yloc - 2, self::$X_RIGHT, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getReportDate($item), self::$X_LEFT + 360, $this->yloc + 2);
		
		$this->yloc -= 20;
	}
	
	protected function getReportTitle()
	{
		if ($this->reportObject instanceof Item)
		{
			return 'REPORT RECORD #' . $this->reportObject->getItemID();
		}
		else 
		{
			$records = $this->reportObject->getRecords();
			$recordrange = ' Rec#: ';
			if (count($records) == 1)
			{
				$item = current($records);
				$recordrange .= $item->getItemID();
			}
			elseif (count($records) > 1)
			{
				$startid = NULL;
				$endid = NULL;
				$previd = NULL;
				$delimiter = "";
				foreach ($records as $record)
				{
					Logger::log('Start: ' .$startid);
					Logger::log('End: ' .$endid);
					//If this isn't the next in the sequence, 
					//then append the previous records to the range
					if (!is_null($previd) && $record->getItemID() - 1 != $previd)
					{
						//If there is only one item, then append it
						if (is_null($endid))
						{
							$recordrange .= $delimiter . $startid;
						}
						//Append the range
						else
						{
							$recordrange .= $delimiter . $startid . '-' . $endid;
						}
						//$temprange = array();
						$startid = $record->getItemID();
						$endid = NULL;
						$delimiter = ",";
					}
					//If it is the first loop, then this is the startid.
					elseif (is_null($previd))
					{
						$startid = $record->getItemID();
					}
					//This is part of a range.
					else 
					{
						$endid = $record->getItemID();
					}
						//array_push($temprange, $record->getItemID());
					$previd = $record->getItemID();
				}
				//If the last item is not part of the sequence, then append it to the range.
				if (is_null($endid))
				{
					$recordrange .= $delimiter . $startid;
				}
				//Otherwise, append it to the range.
				else
				{
					$recordrange .= $delimiter . $startid . '-' . $endid;
				}
			}
			return array('GROUP REPORT: ' . $this->reportObject->getGroupName(), $recordrange);
		}
	}
	
	protected function getCustomFooter()
	{
		return 'WPC phone 617.495.8596; fax 617.496.8344';
	}
	
	/*
	 * Returns the transfer object or the first item in the group.
	 * 
	 * @return Item
	 */
	private function getItem()
	{
		if ($this->reportObject instanceof Item)
		{
			$item = $this->reportObject;
		}
		else
		{
			$records = $this->reportObject->getRecords();
			if (count($records) > 0)
			{
				$item = current($records);
			}
			else 
			{
				$item = NULL;	
			}
		}
		
		return $item;
	}
	
	private function getCurator(Item $item = NULL)
	{
		$curator = '';
		if (!is_null($item) && !is_null($item->getCuratorID()))
		{
			$curator = PeopleDAO::getPeopleDAO()->getPerson($item->getCuratorID())->getDisplayName();
		}
		return $curator;
	}
	
	private function getCallNumbers(Item $item)
	{
		$callnumberlist = $item->getCallNumbers();
		$callnumbers = implode(', ', $callnumberlist);
		return $callnumbers;
	}
	
	/*
	 * Returns the total counts
	 */
	private function getCounts()
	{
		$countstring = '';
		if ($this->reportObject instanceof Item)
		{
			$counts = $this->reportObject->getFunctions()->getReport()->getCounts();
			$vol = $counts->getVolumeCount();
			$sheet = $counts->getSheetCount();
			$photo = $counts->getPhotoCount();
			$box = $counts->getBoxCount();
			$other = $counts->getOtherCount();
		}
		else
		{
			$records = $this->reportObject->getRecords();
			$vol = 0;
			$sheet = 0;
			$photo = 0;
			$box = 0;
			$other = 0;
			foreach ($records as $record)
			{
				$counts = $record->getFunctions()->getReport()->getCounts();
				$vol += $counts->getVolumeCount();
				$sheet += $counts->getSheetCount();
				$photo += $counts->getPhotoCount();
				$box += $counts->getBoxCount();
				$other += $counts->getOtherCount();
			}
		}
		$countstring = $vol == 1 ? $vol . ' volume, ' : $vol . ' volumes, ';
		$countstring .= $sheet == 1 ? $sheet . ' sheet, ' : $sheet . ' sheets, ';
		$countstring .= $photo == 1 ? $photo . ' photo, ' : $photo . ' photos, ';
		$countstring .= $box == 1 ? $box . ' box, ' : $box . ' boxes, ';
		$countstring .= $other . ' other';
		return $countstring;
	}

	
	private function getRepository(Item $item = NULL)
	{
		$repository = '';
		if (!is_null($item) && !is_null($item->getHomeLocationID()))
		{
			$repository = LocationDAO::getLocationDAO()->getLocation($item->getHomeLocationID())->getLocation();
		}
		return $repository;
	}
	
	private function getProject(Item $item = NULL)
	{
		$project = '';
		if (!is_null($item) && !is_null($item->getProjectID()))
		{
			$project = ProjectDAO::getProjectDAO()->getProject($item->getProjectID())->getProjectName();
		}
		return $project;
	}
	
	private function getCoordinator(Item $item = NULL)
	{
		$coordinator = '';
		if (!is_null($item) && !is_null($item->getCoordinatorID()))
		{
			$coordinator = PeopleDAO::getPeopleDAO()->getPerson($item->getCoordinatorID())->getDisplayName();
		}
		return $coordinator;
	}
	
	private function getWorkDoneBy(Item $item = NULL)
	{
		$namelist = array();
		if (!is_null($item))
		{
			$workdonebylist = $item->getFunctions()->getReport()->getReportConservators();
			foreach ($workdonebylist as $conservatorobject)
			{
				array_push($namelist, $conservatorobject->getConservator()->getDisplayName());
			}
		}
		return $namelist;
	}
	
	private function getReportDate(Item $item = NULL)
	{
		$reportdate = '';
		if (!is_null($item))
		{
			$zenddate = new Zend_Date($item->getFunctions()->getReport()->getReportDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    		$reportdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
		}
		return $reportdate;
	}
	
	private function getComments(Item $item = NULL)
	{
		$comments = '';
		if (!is_null($item))
		{
			$comments = $item->getComments();
		}
		return $comments;
	}
	
	private function getPreservationRecommendations(Item $item = NULL)
	{
		$presrecs = '';
		if (!is_null($item))
		{
			$presrecs = $item->getFunctions()->getReport()->getPreservationRecommendations();
		}
		return $presrecs;
	}
	
	private function getTreatment(Item $item = NULL)
	{
		$treat = '';
		if (!is_null($item))
		{
			$treat = $item->getFunctions()->getReport()->getTreatment();
		}
		return $treat;
	}
	
	private function getGroup()
	{
		$group = '';
		if ($this->reportObject instanceof Item)
		{
			if (!is_null($this->reportObject->getGroupID()))
			{
				$group = GroupDAO::getGroupDAO()->getGroup($this->reportObject->getGroupID())->getGroupName();
			}
		}
		else 
		{
			$group = $this->reportObject->getGroupName();
		}
		return $group;
	}
	
	private function getReportHours()
	{
		$hourstring = '';
		if ($this->reportObject instanceof Item)
		{
			$hourstring = $this->reportObject->getFunctions()->getReport()->getTotalHours();
		}
		else
		{
			$records = $this->reportObject->getRecords();
			$hours = 0;
			foreach ($records as $record)
			{
				$hours += $record->getFunctions()->getReport()->getTotalHours();
			}
			if ($hours > 0)
			{
				$hourstring = $hours;
			}
		}
		return $hourstring;
	}
}
?>
