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
 * and more
 *   
 */
class CLIInitializer
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
        
        $this->initPhpConfig();
        $this->initDb();
        
        date_default_timezone_set('America/New_York');
        
        $locale = new Zend_Locale('en_US');
		Zend_Registry::set('Zend_Locale', $locale);
        
        // set the test environment parameters
        if ($env == 'test' || $env == 'dev') {
			// Enable all errors so we'll know when something goes wrong. 
			error_reporting(E_ALL | E_STRICT);  
			ini_set('display_startup_errors', 1);  
			ini_set('display_errors', 1); 

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

}
?>
