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
class StatusFormReport extends AcornReport  
{
	private $item;
	
	/*
	 * Constructs the status report
	 * 
	 * @param Item item
	 */
	public function __construct(Item $item)
	{
		$this->item = $item;
		$itemID = $item->getItemID();
		parent::__construct('Acorn' . $itemID . 'Status');
	}
	
	protected function drawReportBody()
	{
		$this->drawCurrentStatus();
		$this->drawItemInformation();
		$this->drawStatusActivity();
	}
	
	private function drawCurrentStatus()
	{
		$xlocheaderleft = self::$X_LEFT;
		$xlocdataleft = self::$X_LEFT + 70;
		
		$currentstatus = $this->item->getItemStatus();
		//Add more detail to some stati
		switch ($currentstatus)
		{
			case ItemDAO::ITEM_STATUS_LOGGED_IN:
				$loc = LocationDAO::getLocationDAO()->getLocation($this->item->getFunctions()->getLogin()->getTransferTo())->getLocation();
				$storage = $this->item->getStorage();
				if (!empty($storage))
				{
					$loc .= ' (' . $storage . ') ';
				}
				$currentstatus = "Logged in, at " . $loc;
				break;
			case ItemDAO::ITEM_STATUS_TEMP_OUT:
				$loc = LocationDAO::getLocationDAO()->getLocation($this->item->getFunctions()->getTemporaryTransfer()->getTransferTo())->getLocation();
				$currentstatus = "Temporarily Transferred to " . $loc;
				break;
			case ItemDAO::ITEM_STATUS_DONE:
				$loc = LocationDAO::getLocationDAO()->getLocation($this->item->getFunctions()->getLogout()->getTransferTo())->getLocation();
				$currentstatus = "Logged out and transferred to " . $loc;
				break;
		}
		
		//Current STatus
		$this->currentPage->setFont(self::$BOLD_FONT, 12);
		$this->currentPage->drawText('STATUS:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText($currentstatus, $xlocdataleft, $this->yloc);
		
		$this->yloc -= 10;
		
		$this->currentPage->setLineWidth(.5);
		$this->currentPage->drawLine(self::$X_LEFT, $this->yloc, self::$X_RIGHT, $this->yloc);
		
		$this->yloc -= 15;
	}
	
	private function drawItemInformation()
	{
		$xlocheaderleft = self::$X_LEFT;
		$xlocheaderright = $this->currentPage->getWidth()/2;
		$xlocdataleft = self::$X_LEFT + 75;
		$xlocdataright = $this->currentPage->getWidth()/2 + 65;
		
		$this->currentPage->setFont(self::$BOLD_FONT, 12);
		$this->currentPage->drawText('RECORD INFORMATION', $xlocheaderleft, $this->yloc);
		
		$this->yloc -= 15;
		
		//Call Numbers
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Call Number(s):', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->createWordWrap($this->getCallNumbers(), $xlocdataleft, 40, 12);
		
		//Project
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Project:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getProject(), $xlocdataright, $this->yloc);
		
		$this->yloc = min(array($this->yloc-15, $wrapyloc));
		
		//Author/Artist
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Author/Artist:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->item->getAuthorArtist(), $xlocdataleft, $this->yloc);
		
		//Group
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Group:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getGroup(), $xlocdataright, $this->yloc);
		$this->yloc -= 15;
		
		//Title
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Title:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->createWordWrap($this->item->getTitle(), $xlocdataleft, 40, 12);
		
		//Coordinator
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Coordinator:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getCoordinator(), $xlocdataright, $this->yloc);
		
		$this->yloc = min(array($this->yloc-15, $wrapyloc));
			
		//Date of Object
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Date of Object:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->item->getDateOfObject(), $xlocdataleft, $this->yloc);

		//Comments
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Comments:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->createWordWrap($this->getComments(), $xlocdataright, 40, 12);
		
		$this->yloc -= 15;
		
		//ItemCounts
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Item Counts:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		//$this->currentPage->drawText($this->getCounts(), $xlocdataleft, $this->yloc);
		$countyloc = $this->createWordWrap($this->getCounts(), $xlocdataleft, 38, 12);
		
		$this->yloc = min(array($this->yloc-15, $wrapyloc, $countyloc));
		
		//Repository
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Repository:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getRepository(), $xlocdataleft, $this->yloc);
		
		//Fund
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Ret. Date:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->createWordWrap($this->item->getExpectedDateOfReturn(), $xlocdataright, 22, 12);
		
		//Insurance Value
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Ins. Val:', self::$X_RIGHT - 100, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$insuranceval = $this->item->getInsuranceValue();
		if (!is_null($insuranceval) && $insuranceval > 0)
		{
			$insuranceval = '$' . number_format($insuranceval, 2);
		}
		else 
		{
			$insuranceval = '';
		}
		$this->currentPage->drawText($insuranceval, self::$X_RIGHT - 60, $this->yloc);

		$this->yloc = min(array($this->yloc-15, $wrapyloc));
		
		//Curator
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Curator:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getCurator(), $xlocdataleft, $this->yloc);
		
		//Repository Memo
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Notes:', $xlocheaderright, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->createWordWrap($this->item->getFundMemo(), $xlocdataright, 45, 12);
		
		$this->yloc = min(array($this->yloc-10, $wrapyloc));
				
		$this->currentPage->setLineWidth(.5);
		$this->currentPage->drawLine(self::$X_LEFT, $this->yloc, self::$X_RIGHT, $this->yloc);
		
		$this->yloc -= 15;
	}
	
	private function drawStatusActivity()
	{
		$xlocdate = self::$X_LEFT;
		$xlocactivity = self::$X_LEFT + 75;
		
		$this->currentPage->setFont(self::$BOLD_FONT, 12);
		$this->currentPage->drawText('RECORDED ACTIVITY', $xlocdate, $this->yloc);
		
		$this->yloc -= 15;
		
		$activities = $this->item->getActivity();
		
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		foreach ($activities as $activityitem)
		{
			$date = $activityitem['Date'];
			$activity = $activityitem['Activity'];
			$this->currentPage->drawText($date, $xlocdate, $this->yloc);
			$wrapyloc = $this->createWordWrap($activity, $xlocactivity, 90, 12);
			$this->yloc = min(array($this->yloc-15, $wrapyloc-3));
		}
	}
	
	
	protected function getReportTitle()
	{
		return 'Status for Record #' . $this->item->getItemID();
	}
	
	protected function getCustomFooter()
	{
		return '';
	}
	
	private function getCurator()
	{
		$curator = '';
		if (!is_null($this->item->getCuratorID()))
		{
			$curator = PeopleDAO::getPeopleDAO()->getPerson($this->item->getCuratorID())->getDisplayName();
		}
		return $curator;
	}
	
	private function getCallNumbers()
	{
		$callnumberlist = $this->item->getCallNumbers();
		$callnumbers = implode(', ', $callnumberlist);
		return $callnumbers;
	}
	
	/*
	 * Returns the total counts
	 */
	private function getCounts()
	{
		$countstring = '';
		$counts = $this->item->getInitialCounts();
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
		if (!is_null($this->item->getHomeLocationID()))
		{
			$repository = LocationDAO::getLocationDAO()->getLocation($this->item->getHomeLocationID())->getLocation();
		}
		return $repository;
	}
	
	private function getProject()
	{
		$project = '';
		if (!is_null($this->item->getProjectID()))
		{
			$project = ProjectDAO::getProjectDAO()->getProject($this->item->getProjectID())->getProjectName();
		}
		return $project;
	}
	
	private function getCoordinator()
	{
		$coordinator = '';
		if (!is_null($this->item->getCoordinatorID()))
		{
			$coordinator = PeopleDAO::getPeopleDAO()->getPerson($this->item->getCoordinatorID())->getDisplayName();
		}
		return $coordinator;
	}
	
	
	private function getComments()
	{
		$comments = '';
		if (!is_null($this->item->getComments()))
		{
			$comments = $this->item->getComments();
		}
		return $comments;
	}
	
	private function getGroup()
	{
		$group = '';
		if (!is_null($this->item->getGroupID()))
		{
			$group = GroupDAO::getGroupDAO()->getGroup($this->item->getGroupID())->getGroupName();
		}
		return $group;
	}

}
?>