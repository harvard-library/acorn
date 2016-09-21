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
class ProposalFormReport extends AcornReport  
{
	//This can be an Item or Group.
	private $proposalObject;
	
	/*
	 * Constructs the transfer report
	 * 
	 * @param Object proposalObject - one of Item or Group
	 */
	public function __construct($proposalObject)
	{
		$this->proposalObject = $proposalObject;
		if (!($proposalObject instanceof Item) && !($proposalObject instanceof Group))
		{
			//Throw a runtime exception if an item or group isn't passed in
			throw new Zend_Exception('proposalObject must be of type Item or Group');
		}
		$item = $this->getItem();
		$itemID = $item->getItemID();
		parent::__construct('Acorn' . $itemID . 'Proposal');
	}
	
	protected function drawReportBody()
	{
		$this->drawProposalInformation();
		$this->drawProposalText();
		$this->drawSignatureLines();		
	}
	
	private function drawProposalInformation()
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
		
		if ($this->proposalObject instanceof Item)
		{
			//Call Numbers
			$this->currentPage->setFont(self::$BOLD_FONT, 10);
			$this->currentPage->drawText('Call Number(s):', $xlocheaderright, $this->yloc);
			$this->currentPage->setFont(self::$REGULAR_FONT, 10);
			$wrapyloc = $this->createWordWrap($this->getCallNumbers($this->proposalObject), $xlocdataright, 40, 12);
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
		
		if ($this->proposalObject instanceof Item)
		{
			//Author/Artist
			$this->currentPage->setFont(self::$BOLD_FONT, 10);
			$this->currentPage->drawText('Author/Artist:', $xlocheaderright, $this->yloc);
			$this->currentPage->setFont(self::$REGULAR_FONT, 10);
			$wrapyloc = $this->createWordWrap($this->proposalObject->getAuthorArtist(), $xlocdataright, 45, 12);
			$this->yloc = min(array($this->yloc-15, $wrapyloc));
		}
		else 
		{
			$this->yloc -= 15;
		}
		//Project
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Project:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getProject($item), $xlocdataleft, $this->yloc);
		
		if ($this->proposalObject instanceof Item)
		{
			//Title
			$this->currentPage->setFont(self::$BOLD_FONT, 10);
			$this->currentPage->drawText('Title:', $xlocheaderright, $this->yloc);
			$this->currentPage->setFont(self::$REGULAR_FONT, 10);
			$wrapyloc = $this->createWordWrap($this->proposalObject->getTitle(), $xlocdataright, 45, 12);
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
		
		if ($this->proposalObject instanceof Item)
		{
			//Date of Object
			$this->currentPage->setFont(self::$BOLD_FONT, 10);
			$this->currentPage->drawText('Date of Object:', $xlocheaderright, $this->yloc);
			$this->currentPage->setFont(self::$REGULAR_FONT, 10);
			$this->currentPage->drawText($this->proposalObject->getDateOfObject(), $xlocdataright, $this->yloc);
		}
		
		$this->yloc -= 15;
			
		//Coordinator
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Coordinator:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getCoordinator($item), $xlocdataleft, $this->yloc);
		
		if ($this->proposalObject instanceof Item)
		{
			//Size
			$this->currentPage->setFont(self::$BOLD_FONT, 10);
			$this->currentPage->drawText('Size:', $xlocheaderright, $this->yloc);
			$this->currentPage->setFont(self::$REGULAR_FONT, 10);
			$this->currentPage->drawText($this->proposalObject->getFunctions()->getProposal()->getDimensions()->getReportDimensions(), $xlocdataright, $this->yloc);
		}
		$this->yloc -= 15;
		
		
		//Proposal Authors
		$authors = $this->getProposedBy($item);
		$authorliststring = implode(", ", $authors);
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Proposal', $xlocheaderleft, $this->yloc);
		$this->currentPage->drawText('Author(s):', $xlocheaderleft+8, $this->yloc-12);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->createWordWrap($authorliststring, $xlocdataleft, 85, 12);
		$this->yloc = min(array($this->yloc-30, $wrapyloc));
		
		
		//ItemCounts
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Item Counts:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getCounts(), $xlocdataleft, $this->yloc);
		$this->yloc -= 15;
		
		//Comments
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Comments:', $xlocheaderleft, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->parseAndDrawNewlineTextBlock($this->getComments($item), $xlocdataleft, TRUE, 90, 12);
		
		$this->yloc = $wrapyloc - 13;
	}
	
	private function drawProposalText()
	{
		$item = $this->getItem();
		
		if ($this->checkYLoc(70))
		{
			$this->drawReportTitle(self::$Y_TOP);
		}
		//Description
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Description:', self::$X_LEFT, $this->yloc);
		$this->yloc -= 15;
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->parseAndDrawNewlineTextBlock($this->getDescription($item), self::$X_LEFT, TRUE, 120, 12, TRUE);
		
		$this->yloc = $wrapyloc - 13;
		
		if ($this->checkYLoc(70))
		{
			$this->drawReportTitle(self::$Y_TOP);
		}
		
		//Condition
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Condition:', self::$X_LEFT, $this->yloc);
		$this->yloc -= 15;
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->parseAndDrawNewlineTextBlock($this->getCondition($item), self::$X_LEFT, TRUE, 120, 12, TRUE);

		$this->yloc = $wrapyloc - 13;
		
		if ($this->checkYLoc(70))
		{
			$this->drawReportTitle(self::$Y_TOP);
		}
		
		//ProposedTreatment
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Proposed Treatment:', self::$X_LEFT, $this->yloc);
		$this->yloc -= 15;
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$wrapyloc = $this->parseAndDrawNewlineTextBlock($this->getTreatment($item), self::$X_LEFT, TRUE, 120, 12, TRUE);
		$this->yloc = $wrapyloc - 13;
		
		if ($this->checkYLoc())
		{
			$this->drawReportTitle(self::$Y_TOP);
		}
		
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$prophours = 'Proposed Hours: ';
		if ($this->proposalObject instanceof Group)
		{
			$prophours = 'Proposed Hours Per Record: ';
		}
		$prophours .= $this->getProposedHours($this->getItem());
		$this->currentPage->drawText($prophours, self::$X_LEFT, $this->yloc);
		
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
		
		//Proposed By
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->yloc += 2;
		
		$proposedbyarray = $this->getProposedBy($item);
		$proposedbystring = '';
		$counter = 1;
		foreach ($proposedbyarray as $proposedby)
		{
			if ($counter == 1)
			{
				$proposedbystring = $proposedby;
			}
			elseif ($counter > 1 && $counter % 2 == 1)
			{
				$this->currentPage->drawText($proposedbystring . ',', self::$X_LEFT + 80, $this->yloc);
				$this->yloc -= 10;
				$proposedbystring = $proposedby;
			}
			else
			{
				$proposedbystring .= ', ' . $proposedby;
			}
			$counter++;
		}
		$this->currentPage->drawText($proposedbystring, self::$X_LEFT + 80, $this->yloc);
		$this->yloc -= 10;
		
		$this->yloc += 5;
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Proposed By:', self::$X_LEFT, $this->yloc);
		$this->currentPage->drawLine(self::$X_LEFT + 75, $this->yloc, self::$X_LEFT + 300, $this->yloc);
		
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Date:', self::$X_LEFT + 325, $this->yloc);
		$this->currentPage->drawLine(self::$X_LEFT + 355, $this->yloc, self::$X_RIGHT, $this->yloc);
		$this->currentPage->setFont(self::$REGULAR_FONT, 10);
		$this->currentPage->drawText($this->getProposalDate($item), self::$X_LEFT + 360, $this->yloc + 2);
		
		$this->yloc -= 20;
		
		
		//Approved By
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Approved By:', self::$X_LEFT, $this->yloc);
		$this->currentPage->drawLine(self::$X_LEFT + 70, $this->yloc, self::$X_LEFT + 300, $this->yloc);
		$status = ProposalApprovalDAO::getProposalApprovalDAO()->getProposalApprovalStatus($this->getItem()->getItemID());
		//If the status is approved, print the curator and approval
		if ($status == ProposalApprovalDAO::APPROVAL_TYPE_APPROVE)
		{
			$curator = $this->getApprovingCurator($this->getItem());
			$this->currentPage->setFont(self::$REGULAR_FONT, 10);
			$this->currentPage->drawText('Electronically Approved by ' . $curator, self::$X_LEFT + 75, $this->yloc + 2);
		}
		
		$this->currentPage->setFont(self::$BOLD_FONT, 10);
		$this->currentPage->drawText('Date:', self::$X_LEFT + 325, $this->yloc);
		$this->currentPage->drawLine(self::$X_LEFT + 355, $this->yloc, self::$X_RIGHT, $this->yloc);
		//If the status is approved, print the approval date
		if ($status == ProposalApprovalDAO::APPROVAL_TYPE_APPROVE)
		{
			$approvaldate = ProposalApprovalDAO::getProposalApprovalDAO()->getProposalApprovalDate($this->getItem()->getItemID());
			$this->currentPage->setFont(self::$REGULAR_FONT, 10);
			$this->currentPage->drawText($approvaldate, self::$X_LEFT + 360, $this->yloc + 2);
		}
		
	}
	
	protected function getReportTitle()
	{
		if ($this->proposalObject instanceof Item)
		{
			return 'PROPOSAL RECORD #' . $this->proposalObject->getItemID();
		}
		else 
		{
			$records = $this->proposalObject->getRecords();
			$recordrange = ' Rec#: ';
			if (count($records) == 1)
			{
				$item = current($records);
				$recordrange .= $item->getItemID();
			}
			elseif (count($records) > 1)
			{
				$item = current($records);
				$lastitem = end($records);
				$recordrange .= $item->getItemID() . '-' . $lastitem->getItemID();
			}
			return 'GROUP PROPOSAL: ' . $this->proposalObject->getGroupName() . $recordrange;
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
		if ($this->proposalObject instanceof Item)
		{
			$item = $this->proposalObject;
		}
		else
		{
			$records = $this->proposalObject->getRecords();
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
	
	private function getApprovingCurator(Item $item = NULL)
	{
		$curator = '';
		if (!is_null($item) && !is_null($item->getApprovingCuratorID()))
		{
			$curator = PeopleDAO::getPeopleDAO()->getPerson($item->getApprovingCuratorID())->getDisplayName();
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
		if ($this->proposalObject instanceof Item)
		{
			$counts = $this->proposalObject->getInitialCounts();
			$vol = $counts->getVolumeCount();
			$sheet = $counts->getSheetCount();
			$photo = $counts->getPhotoCount();
			$box = $counts->getBoxCount();
			$other = $counts->getOtherCount();
		}
		else
		{
			$records = $this->proposalObject->getRecords();
			$vol = 0;
			$sheet = 0;
			$photo = 0;
			$box = 0;
			$other = 0;
			foreach ($records as $record)
			{
				$counts = $record->getInitialCounts();
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
	
	private function getProposedBy(Item $item = NULL)
	{
		$namelist = array();
		if (!is_null($item))
		{
			$proposedbylist = $item->getFunctions()->getProposal()->getProposedBy();
			foreach ($proposedbylist as $person)
			{
				array_push($namelist, $person->getDisplayName());
			}
		}
		return $namelist;
	}
	
	private function getProposalDate(Item $item = NULL)
	{
		$proposaldate = '';
		if (!is_null($item))
		{
			$zenddate = new Zend_Date($item->getFunctions()->getProposal()->getProposalDate(), ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);		
    		$proposaldate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);	
		}
		return $proposaldate;
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
	
	private function getDescription(Item $item = NULL)
	{
		$desc = '';
		if (!is_null($item))
		{
			$desc = $item->getFunctions()->getProposal()->getDescription();
		}
		return $desc;
	}
	
	private function getCondition(Item $item = NULL)
	{
		$condition = '';
		if (!is_null($item))
		{
			$condition = $item->getFunctions()->getProposal()->getCondition();
		}
		return $condition;
	}
	
	private function getTreatment(Item $item = NULL)
	{
		$treat = '';
		if (!is_null($item))
		{
			$treat = $item->getFunctions()->getProposal()->getTreatment();
		}
		return $treat;
	}
	
	private function getGroup()
	{
		$group = '';
		if ($this->proposalObject instanceof Item)
		{
			if (!is_null($this->proposalObject->getGroupID()))
			{
				$group = GroupDAO::getGroupDAO()->getGroup($this->proposalObject->getGroupID())->getGroupName();
			}
		}
		else 
		{
			$group = $this->proposalObject->getGroupName();
		}
		return $group;
	}
	
	private function getProposedHours(Item $item = NULL)
	{
		$hours = '';
		if (!is_null($item))
		{
			$hours = $item->getFunctions()->getProposal()->getHoursAsString();
		}
		return $hours;
	}
}
?>