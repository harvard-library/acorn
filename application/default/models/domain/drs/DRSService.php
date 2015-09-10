<?php
/**********************************************************************
 * Copyright (c) 2010 by the President and Fellows of Harvard College
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
 * Library Technology Services, Harvard University Information Technology
 * Harvard Library
 * Harvard University
 * Cambridge, MA  02138
 * (617)495-3724
 * hulois@hulmail.harvard.edu
 **********************************************************************/

/**
 * A general wrapper for running a DRS transfer
 *
 * @abstract
 * @access public
 * @author Valdeva Crema
 */
abstract class DRSService
{
    /**
     * Perform the cleanup (close any connections, etc)
     *
     * @access protected
     * @return mixed
     */
    protected function cleanup()
    {
    }

    /**
     * Run the program
     *
     * @abstract
     * @access protected
     * @return mixed
     */
    protected abstract function execute();

    /**
     * Get the configuraion for the program
     *
     * @abstract
     * @access protected
     * @return DRSServiceConfig
     */
    protected function getConfiguration()
    {
    	return Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
    }

    /**
     * Set up the service to run.
     *
     * @access protected
     * @return mixed
     */
    protected function prepareService()
    {
    }


    /**
     * Run the service.
     *
     * @access public
     * @return mixed
     */
    public function runService()
    {
    	try 
    	{
	    	//Prepare
	    	$this->prepareService();
	    	//Execute
	    	$this->execute();
	    }
    	catch (Exception $e)
    	{
    		Logger::log($e->getTraceAsString(), Zend_Log::ERR);
    		$this->sendErrorEmail($e->getTraceAsString());
    	}
    	//Cleanup
	    $this->cleanup();
    }

    /**
     * Send an email to support if the service fails.
     *
     * @access protected
     * @return mixed
     */
    protected function sendErrorEmail($trace)
    {
    	$subject = "Error running the DRSService";
    	$message = "There was an error running a DRSService.  The error message received was: \n";
    	$message .= $trace;
    	$erremails = $this->getConfiguration()->getErrorEmailAddresses();
    	if (empty($erremails))
    	{
    		Logger::log("No error emails supplied.  Therefore an error email was not sent.", Zend_Log::ERR);
    		return;
    	}
    	$emails = implode(",", $erremails);
    	$identity = Zend_Auth::getInstance()->getIdentity();
    	if (!is_null($identity))
    	{
			$submittedby = PeopleDAO::getPeopleDAO()->getPerson($identity[PeopleDAO::PERSON_ID]);
			$submittedbyemail = $submittedby->getEmailAddress();
    	}
    	else
    	{
    		//use the first error email if a user is not logged in.
    		$submittedbyemail = current($erremails);
    	}
		$headers = 'From: ' . $submittedbyemail . "\r\n" .
				'Reply-To: ' . $submittedbyemail . "\r\n" .
				'Errors-To: ' . $submittedbyemail . "\r\n" .
				'Return-Path: ' . $submittedbyemail . "\r\n" .
				'X-Mailer: PHP/' . phpversion() . "\r\n";
		$additionalparams = "-f" . $submittedbyemail;
		$success = mail($emails, $subject, $message, $headers, $additionalparams);
    	if (!$success)
    	{
    		Logger::log("Problem mailing error message to " . $emails, Zend_Log::ERR);
    	}
    }

} /* end of abstract class Service */

?>