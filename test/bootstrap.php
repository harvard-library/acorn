<?php
/**
 * ACORN Zend Framework project
 * 
 * @author  Valdeva Crema
 */
set_include_path('.' . PATH_SEPARATOR . '../library' 
	. PATH_SEPARATOR . '../application/default/models' 
	. PATH_SEPARATOR . '../application/default/models/dao/functions' 
	. PATH_SEPARATOR . '../application/default/models/dao/locations' 
	. PATH_SEPARATOR . '../application/default/models/dao/people' 
	. PATH_SEPARATOR . '../application/default/models/dao/proposalapproval' 
	. PATH_SEPARATOR . '../application/default/models/dao/records' 
	. PATH_SEPARATOR . '../application/default/models/dao/reporting' 
	. PATH_SEPARATOR . '../application/default/models/domain/custom'
	. PATH_SEPARATOR . '../application/default/models/domain/drs'
	. PATH_SEPARATOR . '../application/default/models/domain/drs/postprocessing'
	. PATH_SEPARATOR . '../application/default/models/domain/drs/preprocessing'
	. PATH_SEPARATOR . '../application/default/models/domain/functions'
	. PATH_SEPARATOR . '../application/default/models/domain/locations'
	. PATH_SEPARATOR . '../application/default/models/domain/people'
	. PATH_SEPARATOR . '../application/default/models/domain/proposalapproval'
	. PATH_SEPARATOR . '../application/default/models/domain/records'
	. PATH_SEPARATOR . '../application/default/models/domain/reporting'
	. PATH_SEPARATOR . '../application/default/models/domain/userlogin'
	. PATH_SEPARATOR . '../application/default/views/filters'
	. PATH_SEPARATOR . '../application/default/views/forms'
	. PATH_SEPARATOR . '../application/default/views/forms/admin'
	. PATH_SEPARATOR . '../application/default/views/forms/customelements'
	. PATH_SEPARATOR . '../application/default/views/forms/group'
	. PATH_SEPARATOR . '../application/default/views/forms/proposalapproval'
	. PATH_SEPARATOR . '../application/default/views/forms/record'
	. PATH_SEPARATOR . '../application/default/views/forms/reports'
	. PATH_SEPARATOR . '../application/default/views/forms/search'
	. PATH_SEPARATOR . '../application/default/views/forms/transfers'
	. PATH_SEPARATOR . '../application/default/views/forms/userlogin'
	. PATH_SEPARATOR . '../application/default/views/includes'
	. PATH_SEPARATOR . '../application/default/views/validators'
	. PATH_SEPARATOR . get_include_path());

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));
define('TESTS_PATH', realpath(dirname(__FILE__)));

require_once APPLICATION_PATH . '/CLIInitializer.php';
require_once LIBRARY_PATH . "/Zend/Loader/Autoloader.php";
require_once LIBRARY_PATH . "/Zend/Auth/Adapter/Interface.php";
require_once TESTS_PATH . "/TestUserLogin.php";

//require_once 'CleanupInitializer.php';
//require_once "Zend/Loader/Autoloader.php"; 

// Set up autoload.
$loader = Zend_Loader_Autoloader::getInstance();
$loader->setFallbackAutoloader(true);

$env = 'dev';
// Change $env variable to 'prod' parameter under production environemtn
$initializer = new CLIInitializer($env);

//Start session
Zend_Session::start();

$auth = Zend_Auth::getInstance();
$result = $auth->authenticate(new TestUserLogin());

?>

