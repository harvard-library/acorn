<?php
/**
 * My new Zend Framework project
 * 
 * @author  
 * @version 
 */

require_once 'Zend/Controller/Plugin/Abstract.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Abstract.php';
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * 
 * Initializes configuration depndeing on the type of environment 
 * (test, development, production, etc.)
 *  
 * This can be used to configure environment variables, databases, 
 * layouts, routers, helpers and more
 *   
 */
class Initializer extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var Zend_Config
     */
    protected static $_config;

    /**
     * @var string Current environment
     */
    protected $_env;

    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    /**
     * @var string Path to application root
     */
    protected $_root;
   
    /**
     * Constructor
     *
     * Initialize environment, root path, and configuration.
     * 
     * @param  string $env 
     * @param  string|null $root 
     * @return void
     */
    public function __construct($env, $root = null)
    {
        $this->_env = $env;
        if (null === $root) {
            $root = realpath(dirname(__FILE__) . '/../');
        }
        $this->_root = $root;
        
        $this->_front = Zend_Controller_Front::getInstance();
        $this->initPhpConfig();
        
        date_default_timezone_set('America/New_York');
        
        $locale = new Zend_Locale('en_US');
		Zend_Registry::set('Zend_Locale', $locale);
        
        // set the test environment parameters
        if ($env == 'test' || $env == 'dev') {
			// Enable all errors so we'll know when something goes wrong. 
			error_reporting(E_ALL | E_STRICT);  
			ini_set('display_startup_errors', 1);  
			ini_set('display_errors', 1); 

			$this->_front->throwExceptions(true);  
        }
    }

    /**
     * Initialize Data bases
     * 
     * @return void
     */
    public function initPhpConfig()
    {
    	$config = new ACORNConfig('../application/config.ini', $this->_env);
		$registry = Zend_Registry::getInstance();
		$registry->set('config', $config);
		
		$sessdir = $config->getACORNApplicationDirectory() . "/acorn_sessions";

		ini_set('session.save_path', $sessdir);
		ini_set('session.gc_maxlifetime', 28880);
	}
    
    /**
     * Route startup
     * 
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
       	$this->initDb();
        $this->initHelpers();
        $this->initView();
        $this->initPlugins();
        $this->initRoutes();
        $this->initControllers();
    }
    
    /**
     * Initialize data bases
     * 
     * @return void
     */
    public function initDb()
    {
    	// load configuration
		$config = new Zend_Config_Ini('../application/dbconfig.ini', $this->_env);
		$registry = Zend_Registry::getInstance();
		$registry->set('dbconfig', $config);
		
		// setup database
		$db = Zend_Db::factory($config->db);
		Zend_Db_Table::setDefaultAdapter($db);
    }

    /**
     * Initialize action helpers
     * 
     * @return void
     */
    public function initHelpers()
    {
    	// register the default action helpers
    	Zend_Controller_Action_HelperBroker::addPath('../application/default/helpers', 'Zend_Controller_Action_Helper');
    }
    
    /**
     * Initialize view 
     * 
     * @return void
     */
    public function initView()
    {
		// Bootstrap layouts
		Zend_Layout::startMvc(array(
		    'layoutPath' => $this->_root .  '/application/default/layouts',
		    'layout' => 'main'
		));
    	
    }
    
    /**
     * Initialize plugins 
     * 
     * @return void
     */
    public function initPlugins()
    {
    	$this->_front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
		    'module'     => 'mystuff',
		    'controller' => 'error',
		    'action'     => 'error'
		)));
    }
    
    /**
     * Initialize routes
     * 
     * @return void
     */
    public function initRoutes()
    {
    
    }

    /**
     * Initialize Controller paths 
     * 
     * @return void
     */
    public function initControllers()
    {
    	$this->_front->addControllerDirectory($this->_root . '/application/default/controllers', 'default');
    }
    
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	if (Zend_Auth::getInstance()->hasIdentity())
    	{
    		$identity = Zend_Auth::getInstance()->getIdentity();
    		$person = PeopleDAO::getPeopleDAO()->getPerson($identity[PeopleDAO::PERSON_ID]);
	    	if ($person->getAccessLevel() == PeopleDAO::ACCESS_LEVEL_CURATOR
	    		&& $request->getControllerName() != 'proposalapproval'
	    		&& $request->getControllerName() != 'index'
	    		&& $request->getControllerName() != 'user')
	    	{
	    		$request->setActionName('index');
	    		$request->setControllerName('user');
	    		$request->setDispatched(FALSE);
	    		$this->setRequest($request);
	    	}
	    	elseif ($person->getAccessLevel() == 'None')
	    	{
	    		$request->setActionName('index');
	    		$request->setControllerName('user');
	    		$request->setDispatched(FALSE);
	    		$this->setRequest($request);
	    	}
	    }
    }
}
?>
