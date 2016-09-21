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

class UserLogin implements Zend_Auth_Adapter_Interface
{
    private $username;
	private $password;
	private $parameters;
	
    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
    	$loginname = $this->parameters['usernameinput'];
    	$pw = $this->parameters['passwordinput'];

    	$user = PeopleDAO::getPeopleDAO()->getUserWithPassword($loginname, $pw);
    	
        if (!empty($user))
        {
        	//Remove the password from the array
        	unset($user[PeopleDAO::USER_PASSWORD]);
        	
        	$user = $this->updateUserAccessLevel($user);
        	Logger::log($user, Zend_Log::DEBUG);
        	Logger::log($this->parameters, Zend_Log::DEBUG);
        	return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
        }
        return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $this->parameters);
    }
    
    /*
     * Returns the access level for the user.
     * This function is only necessary for an administrator logging in
     * as a different level.
     * 
     * @param array the user array
     * @return the updated array
     */
    private function updateUserAccessLevel(array $user)
    {
    	//If the user is an administrator, then determine what access level s/he is logging in as.
    	if ($user[PeopleDAO::ACCESS_LEVEL] == PeopleDAO::ACCESS_LEVEL_ADMIN)
        {
        	//As long as the usertype is set (it always should be), then continue
        	if (isset($this->parameters['usertypeselect']))
			{
        		$accesslevel = $this->parameters['usertypeselect'];	
        		$user[PeopleDAO::ACCESS_LEVEL] = $accesslevel;
        		//If s/he is logging in as a repository access level, set the repository that he is logging in to.
        		if ($accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY || $accesslevel == PeopleDAO::ACCESS_LEVEL_REPOSITORY_ADMIN)
        		{
        			$user[LocationDAO::LOCATION_ID] = $this->parameters['repositoryselect'];
        		}
			}
        }
        return $user;	
    }

} 

?>