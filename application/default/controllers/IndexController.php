<?php

/**
 * IndexController - The default controller class
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class IndexController extends Zend_Controller_Action 
{
	private $userloginForm;
	
	private function getUserLoginForm()
	{
		if (is_null($this->userloginForm))
		{
			$this->userloginForm = new UserLoginForm();
		}
		return $this->userloginForm;
	}
	
	public function getAuthAdapter(array $params)
    {
        // Leaving this to the developer...
        // Makes the assumption that the constructor takes an array of
        // parameters which it then uses as credentials to verify identity.
        // Our form, of course, will just pass the parameters 'username'
        // and 'password'.
        
        return new UserLogin($params);
    }
    
	public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) 
        {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName()) {
            	$this->_helper->redirector('index', 'user');
            }
        } 
        else 
        {
            // If they aren't, they can't logout, so that action should
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index');
            }
        }
    }
	
	/**
	 * The default action - show the home page
	 */
    public function indexAction() 
    {
    	$this->view->form = $this->getUserLoginForm();
    }
    
	public function processAction()
    {
        $request = $this->getRequest();
   		
        // Check if we have a POST request
        if (!$request->isPost()) 
        {
            return $this->_helper->redirector('index');
        }
		
        // Get our form and validate it
        $form = $this->getUserLoginForm();
        
        if (!$form->isValid($request->getPost())) 
        {
        	Logger::log('Login not valid', Zend_Log::DEBUG);
            Logger::log($request->getPost(), Zend_Log::DEBUG);
            // Invalid entries
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        // Get our authentication adapter and check credentials
        $adapter = $this->getAuthAdapter($form->getValues());
        $auth    = Zend_Auth::getInstance();
        $result  = $auth->authenticate($adapter);
        if (!$result->isValid()) 
        {
        	Logger::log('Invalid credentials', Zend_Log::DEBUG);
            // Invalid credentials
            $form->setDescription('Invalid credentials provided');
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }
        
        Logger::log(Zend_Auth::getInstance()->getIdentity());
        
		// We're authenticated!
        $action = AcornNamespace::getRedirectAction();
        $controller = AcornNamespace::getRedirectController();
        
       //If a redirect controller-action exists, then redirect there.
        if (isset($action) && !empty($action) && isset($controller) && !empty($controller))
        {
        	$params = AcornNamespace::getRedirectParams();
        	if (!isset($params))
        	{
        		$params = NULL;
        	}
        	AcornNamespace::clearAll();
        	$this->_helper->redirector($action, $controller, NULL, $params);
        }
        //Otherwise, redirect to the home page
        else 
        {
      		$this->_helper->redirector('index', 'user');
        }
    }
    
	public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
    	$identity = $auth->getIdentity();
    	$personid = $identity[PeopleDAO::PERSON_ID];
    	ItemDAO::getItemDAO()->clearEdits($personid);
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        $this->_helper->redirector('index'); // back to login page
    }
    
    public function verifyadminAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$username = $this->getRequest()->getParam('username');
		$isadmin = array('isadmin' => FALSE);
		if (isset($username))
		{
			$peopledao = new PeopleDAO();
			$user = $peopledao->getUser($username);
			//$user = PeopleDAO::getPeopleDAO()->getUser($username);
			if (!is_null($user) && $user[PeopleDAO::ACCESS_LEVEL] == "Admin")
			{
				$isadmin = array('isadmin' => TRUE);
			}
		}
		
		$retval = Zend_Json::encode($isadmin);
		//Make sure JS knows it is in JSON format.
		$this->getResponse()->setHeader('Content-Type', 'application/json')
									->setBody($retval);
    }
}
