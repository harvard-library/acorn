<?php
/**
 * TestUserLogin
 *
 * @author ValdevaCrema
 * Created Nov 2, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */
class TestUserLogin implements Zend_Auth_Adapter_Interface
{
    private $username;
	private $password;
	
    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
    	//Authenticate the Administrative user for this.
    	$user = PeopleDAO::getPeopleDAO()->getUser('deedee');  
    	//Remove the password from the array
    	unset($user[PeopleDAO::USER_PASSWORD]);
    	 
    	return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
    }
} 

?>