<?php
 
 /**********************************************************************
 * Copyright (c) 2011 by the President and Fellows of Harvard College
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA.
 *
 * Contact information
 *
 * Office for Information Systems
 * Harvard University Library
 * Harvard University
 * Cambridge, MA  02138
 * (617)495-3724
 * hulois@hulmail.harvard.edu
 **********************************************************************/
 
/**
 * Exports a Work Done By report into an Excel spreadsheet
 * 
 * The user picks date range from calendar. The date range refers to the Work Done By
 * Dates that users have added into Treatment Reports and OSW records.
 *
 * The report only pulls in the hours within the specific date range. 
 * For example, a Record Number may have a total of 20 hours but only 12 fall
 * within the date range specified. The 12 hours would show up on the report, not
 * the 20.  
 * 
 * The following columns are the in the result set in the given order:
 *
 * Charge To Repository >>    Repository >> Project >> Work Done By >> Hours >>
 * Activity >> Work Type >> Purpose >> Format >> Item Count >> Type >> Acorn # >>
 * Login Date >> Logout Date
 *
 * The data is sorted alphabetically by Charge To (Repository, Patron, and Project). 
 * Under each Charge To, Items are listed first, followed by OSW in numeric order.
 * There is a space after each Charge To location (ex. all the Baker items
 * together followed by a space followed by the next Charge To alphabetically, etc...)
 * 
 * @author Valdeva Crema
 *
 */
class WorkDoneByCSVReport
{
 	private $dateRange;
 	
	/**
 	* @param DateRange - the date range
 	*/
 	public function __construct(DateRange $dateRange)
 	{
 		$this->dateRange = $dateRange;
 	}
 	
 	/**
 	 * Generates the report 
 	 */
 	public function generateReport()
 	{
 		$fullfilename = "";
 		$auth = Zend_Auth::getInstance();
 		$identity = $auth->getIdentity();
 		$personid = $identity[PeopleDAO::PERSON_ID];
 		$date = date(ACORNConstants::$DATE_FILENAME_FORMAT);
 		
 		$filename = $personid . '_' . $date . '.csv';
 		$config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
 		
 		$fullfilename = $config->getReportsDirectory() . '/cvsreports/' . $filename;
 		$filehandler = fopen($fullfilename, 'w');
 		
 		$cols = array("ChargeTo",
 			"Repository",
 			ProjectDAO::PROJECT_NAME,
 			"WorkDoneBy",
 			ReportDAO::COMPLETED_HOURS,
 			"Activity",
 			"WorkType",
 			PurposeDAO::PURPOSE,
 			FormatDAO::FORMAT,
 			"Count",
 			CombinedRecordsDAO::RECORD_TYPE,
 			"Rec #",
 			LoginDAO::LOGIN_DATE,
 			LogoutDAO::LOGOUT_DATE);
 		
 		fputcsv($filehandler, $cols);
 		
 		$recordarray = CombinedRecordsReportsDAO::getCombinedRecordsReportsDAO()->getWorkDoneBy($this->dateRange);
 		
 		if (!empty($recordarray))
 		{
 			$currentchargeto = "";
 			foreach ($recordarray as $record)
 			{
 				if ($record["ChargeTo"] != $currentchargeto)
 				{
 					fputcsv($filehandler, array());
 					$currentchargeto = $record["ChargeTo"];
 				}
 				fputcsv($filehandler, $this->buildExportSearchResultArray($record));
 			}
 		}
 		fclose($filehandler);
 		
 		return $filename;
 	}
 	
 	/**
 	 * Builds an array in the correct display order for the requested data.
 	 * @param array $record - the data returned as an array from the query
 	 * @return array - the data to be placed into the Excel spreadsheet.
 	 */
 	private function buildExportSearchResultArray(array $record)
 	{
 		//$cr = CombinedRecordsDAO::getCombinedRecordsDAO()->getRecord($record[CombinedRecordsDAO::RECORD_ID], $record[CombinedRecordsDAO::RECORD_TYPE]);
 		$worktypestring = NULL;
 		if ($record[CombinedRecordsDAO::RECORD_TYPE] == 'OSW')
 		{
 			$worktypes = WorkTypeDAO::getWorkTypeDAO()->getWorkTypes($record[CombinedRecordsDAO::RECORD_ID]);
 			$worktypestringarray = array();
 			foreach ($worktypes as $wt)
 			{
 				array_push($worktypestringarray, $wt->getWorkType());
 			}
 			$worktypestring = implode(',', $worktypestringarray);
 		}
 		
 		$purpose = NULL;
 		if (!is_null($record[PurposeDAO::PURPOSE_ID]))
 		{
 			$purpose = PurposeDAO::getPurposeDAO()->getPurpose($record[PurposeDAO::PURPOSE_ID])->getPurpose();
 		}
 		
 		$format = NULL;
 		if (!is_null($record[FormatDAO::FORMAT_ID]))
 		{
 			$format = FormatDAO::getFormatDAO()->getFormat($record[FormatDAO::FORMAT_ID])->getFormat();
 		}
 		
 		$logindate = NULL;
 		if (!is_null($record[LoginDAO::LOGIN_DATE]))
 		{
 			$zenddate = new Zend_Date($record[LoginDAO::LOGIN_DATE], ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
 			$logindate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
 		}
 		
 		$logoutdate = NULL;
 		if (!is_null($record[LogoutDAO::LOGOUT_DATE]))
 		{
 			$zenddate = new Zend_Date($record[LogoutDAO::LOGOUT_DATE], ACORNConstants::$ZEND_INTERNAL_DATE_FORMAT);
 			$logoutdate = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
 		}
 			
 		if ($record[CombinedRecordsDAO::RECORD_TYPE] == "OSW")
 		{
 			$activitystatus = "On-Site";
 		}
 		else
 		{
	 		$activitystatus = "Treatment";
	 		 
	 		$checkedactivity = '';
	 		$delimiter = '';
	 		if ($record[ReportDAO::ADMIN_ONLY])
	 		{
	 			$checkedactivity = "Admin Only";
	 			$delimiter = '; ';
	 		}
	 		if ($record[ReportDAO::EXAM_ONLY])
	 		{
	 			$checkedactivity .= $delimiter . "Exam Only";
	 			$delimiter = '; ';
	 		}
	 		if ($record[ReportDAO::CUSTOM_HOUSING_ONLY])
	 		{
	 			$checkedactivity .= $delimiter . "Custom Housing";
	 		}
	 		if (!empty($checkedactivity))
	 		{
	 			$activitystatus = $checkedactivity;
	 		}
 		}
 		
 		$results = array(
 			"ChargeTo" => $record['ChargeTo'],
 			LocationDAO::LOCATION => $record[LocationDAO::LOCATION],
 			ProjectDAO::PROJECT_NAME => $record[ProjectDAO::PROJECT_NAME],
 			PeopleDAO::DISPLAY_NAME => $record[PeopleDAO::DISPLAY_NAME],
 			ReportDAO::COMPLETED_HOURS => $record[ReportDAO::COMPLETED_HOURS],
 			"Activity" => $activitystatus,
 			"WorkType" => $worktypestring,
 			PurposeDAO::PURPOSE_ID => $purpose,
 			FormatDAO::FORMAT_ID => $format,
 			"Count" => $record["Count"],
 			CombinedRecordsDAO::RECORD_TYPE => $record[CombinedRecordsDAO::RECORD_TYPE],
 			CombinedRecordsDAO::RECORD_ID => $record[CombinedRecordsDAO::RECORD_ID],
 			LoginDAO::LOGIN_DATE => $logindate,
 			LogoutDAO::LOGOUT_DATE => $logoutdate
 			);
 		Logger::log($results);
 	
 		return $results;
 	}
}
?>