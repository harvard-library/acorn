<?php

/**
 * PeopleController
 *
 * @author vcrema
 * Created April 13, 2009
 *
 * Copyright 2009 The President and Fellows of Harvard College
 */

class PeopleController extends Zend_Controller_Action 
{
	private $personForm;
	
	private function getPersonForm()
	{
		if (is_null($this->personForm))
		{
			$this->personForm = new PersonForm();
		}
		return $this->personForm;
	}
	
	public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
        {
            $this->_helper->redirector('index', 'index');
        }
        
        //TODO Make update password actions the only actions accessible to all users
        /*if ($this->getRequest()->getActionName() == 'newuser')
        {
	        $auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
			//Only Admin have access to this action
			if ($identity[PeopleDAO::ACCESS_LEVEL] != PeopleDAO::ACCESS_LEVEL_ADMIN) 
	        {
	            $this->_helper->redirector('index', 'index');
	        }
        }*/
    }
	
	public function newuserAction()
    {
    	PeopleNamespace::clearAll();
    	PeopleNamespace::setSaveStatus(PeopleNamespace::SAVE_STATE_IDLE);
    	PeopleNamespace::setCurrentPerson(new Person());
    	$this->_helper->redirector('index');
    }
    
	public function indexAction()
    {
    	$form = $this->getPersonForm();	
    	$personid = $this->getRequest()->getParam('personid');
    	$personname = 'New Person';
    	
    	$person = PeopleNamespace::getCurrentPerson();
		if (isset($personid))
		{
			if (!isset($person) || $person->getPrimaryKey() != $personid)
			{
				$person = PeopleDAO::getPeopleDAO()->getPerson($personid);
				PeopleNamespace::setOriginalPerson(PeopleDAO::getPeopleDAO()->getPerson($personid));
				PeopleNamespace::setSaveStatus(PeopleNamespace::SAVE_STATE_IDLE);
			}
			
			if (!is_null($person))
			{
				PeopleNamespace::setCurrentPerson($person);
				$dataarray = $this->buildPersonFormArray($person);
			
				$form->populate($dataarray);
				$personname = $person->getDisplayName();
			}	
		}
		//New form will default the user to 'regular'
		else 
		{
			$formarray = array(
    			'usertypeselect' => PeopleDAO::ACCESS_LEVEL_REGULAR
    		);
    		$form->populate($formarray);
		}
    	
    	$this->view->form = $form;	
    	$this->view->placeholder('personname')->set($personname);
    }
    
	/*
     * Builds an array to display the data on the person screen.
     * 
     * @param Person person 
     * @return array prepared for the form
     */
    private function buildPersonFormArray(Person $person)
    {
    	$formarray = array(
    		'personinput' => $person->getDisplayName(),
    		'firstnameinput' => $person->getFirstName(),
    		'middlenameinput' => $person->getMiddleName(),
    		'lastnameinput' => $person->getLastName(),
    		'logininput' => $person->getUsername(),
    		'usertypeselect' => $person->getAccessLevel(),
    		'repositoryselect' => $person->getLocation(),
    		'personinput' => $person->getDisplayName(),
    		'personidinput' => $person->getPrimaryKey(),
    		'initialsinput' => $person->getInitials(),
    		'inactivecheckbox' => $person->isInactive(),
    		'emailinput' => $person->getEmailAddress()
    	);
    	return $formarray;
    }
    
	/**
	 * Finds a list of people that begin with the given data
	 */
    public function findpeopleAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the entered data
		$name = $this->getRequest()->getParam("query");
    	
		$peoplearray = PeopleDAO::getPeopleDAO()->getPeople($name);
		$resultset = array('Result' => $peoplearray);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	/**
	 * Finds a list of people that begin with the given data
	 */
    public function findstaffAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
		//Determine the entered data
		$name = $this->getRequest()->getParam("query");
    	
		$peoplearray = PeopleDAO::getPeopleDAO()->getPeople($name, array('Staff'));
		$resultset = array('Result' => $peoplearray);
    	$retval = Zend_Json::encode($resultset);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
								->setBody($retval);
    }
    
	public function populateuserrolesAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$person = PeopleNamespace::getCurrentPerson();
		$roles = array();
    	if (isset($person))
		{
			$rolelist = $person->getRoles();
			foreach($rolelist as $role)
			{
				array_push($roles, array('RoleType' => $role));
			}
		}
		
		array_push($roles, array(PeopleDAO::ROLE_TYPE => '(Double click to add)'));
		$retval = Zend_Json::encode(array('Result' => $roles));
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
    
	public function addroleAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
    	$person = PeopleNamespace::getCurrentPerson();
    	if (isset($person))
    	{
    		//Determine the role type
			$roletype = $this->getRequest()->getParam("roletype");
    		$person->addRole($roletype);
    	}
    }
    
	public function removeroleAction()
    {
    	//Don't display a new view
		$this->_helper->viewRenderer->setNoRender();
		//Don't use the default layout since this isn't a view call
		$this->_helper->layout->disableLayout();
		
    	$person = PeopleNamespace::getCurrentPerson();
    	if (isset($person))
    	{
    		//Determine the role type
			$roletype = $this->getRequest()->getParam("roletype");
    		$person->removeRole($roletype);
    	}	
    }
    
    public function savepersonAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getPersonForm();
			$formData = $this->_request->getPost();
			Logger::log($formData);
			if ($form->isValidPartial($formData))
			{
				//Save the data
				$personid = $this->savePerson($form->getValues());
				
				if (!is_null($personid))
				{
					PeopleNamespace::setCurrentPerson(PeopleDAO::getPeopleDAO()->getPerson($personid));
					PeopleNamespace::setOriginalPerson(PeopleDAO::getPeopleDAO()->getPerson($personid));
					
					PeopleNamespace::setSaveStatus(PeopleNamespace::SAVE_STATE_SUCCESS);
					
					//Go back to the record screen.
					$this->_helper->redirector('index', 'people', NULL, array('personid' => $personid));
				}
				//otherwise display an error message regarding the save
				else 
				{
						$this->displayPersonErrors($formData, ACORNConstants::MESSAGE_PROBLEM_SAVING);
				}
			}
			else
			{
				$this->displayPersonErrors($formData, ACORNConstants::MESSAGE_INVALID_FORM);
			}
		}
		else
		{
			//If the url was typed directly, go to the DE screen.
			$this->_helper->redirector('newuser');
		}
    }
    
    private function savePerson(array $data)
    {
    	$person = PeopleNamespace::getCurrentPerson();
    	$person->setAccessLevel($data['usertypeselect']);
    	$person->setFirstName($data['firstnameinput']);
    	$person->setLastName($data['lastnameinput']);
    	$person->setMiddleName($data['middlenameinput']);
    	$person->setDisplayName($person->getDisplayName(TRUE));
    	$person->setUsername($data['logininput']);
    	$person->setInactive($data['inactivecheckbox']);
    	$person->setEmailAddress($data['emailinput']);
    	
    	if (isset($data['repositoryselect']))
    	{
    		$person->setLocation($data['repositoryselect']);
    	}
    	else 
    	{
    		$person->setLocation(NULL);
    	}
    	$person->setInitials($data['initialsinput']);
    	
    	$oldperson = PeopleNamespace::getOriginalPerson();
    	if (!isset($oldperson))
    	{
    		$oldperson = NULL;
    	}
    	$personid = PeopleDAO::getPeopleDAO()->savePerson($person, $oldperson);
    	return $personid;
    }
    
	public function resetpasswordAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	//Make sure that this was submitted.
		if ($this->_request->isPost())
		{
			$form = $this->getPersonForm();
			$formData = $this->_request->getPost();
			Logger::log($formData);
			if ($form->isValidPartial($formData))
			{
				$person = PeopleNamespace::getCurrentPerson();
				$personid = NULL;
				if (isset($person))
				{
					$personformdata = $this->buildPersonFormArray($person);
					//Save the data
					$personid = PeopleDAO::getPeopleDAO()->savePassword($person->getPrimaryKey(), $form->getValue('newpassword'));
				}
				else 
				{
					$personformdata = array();
				}
				
				if (!is_null($personid))
				{
					PeopleNamespace::setSaveStatus(PeopleNamespace::SAVE_STATE_SUCCESS);
					
					//Go back to the record screen.
					$this->_helper->redirector('index', 'people', NULL, array('personid' => $personid));
				}
				//otherwise display an error message regarding the save
				else 
				{
						$this->displayPersonErrors($personformdata, 'There was a problem saving the password.  Please try again.');
				}
			}
			else
			{
				$person = PeopleNamespace::getCurrentPerson();
				if (isset($person))
				{
					$personformdata = $this->buildPersonFormArray($person);
				}
				else 
				{
					$personformdata = array();
				}
				$this->displayPersonErrors($personformdata, ACORNConstants::MESSAGE_INVALID_FORM);
			}
		}
		else
		{
			//If the url was typed directly, go to the DE screen.
			$this->_helper->redirector('newuser');
		}
    }
    
	private function displayPersonErrors(array $formData, $message)
    {
    	$form = $this->getPersonForm();
		//Populate the data with what the user
		//gave but also display the error messages.
		$form->populate($formData);
		$this->view->form = $form;

		$person = PeopleNamespace::getCurrentPerson();
		$personname = $person->getDisplayName();
		$this->view->placeholder('personerrormessage')->set($message);
    	$this->view->placeholder('personname')->set($personname);
    	
		$this->render('index');
    }
    
	public function resetpersonAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
    	
    	$oldperson = PeopleNamespace::getOriginalPerson();
    	$currentperson = clone $oldperson;
    	PeopleNamespace::setCurrentPerson($currentperson);
    }
}
