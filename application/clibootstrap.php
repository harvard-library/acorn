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
	. PATH_SEPARATOR . get_include_path());

require_once 'CLIInitializer.php';
require_once "Zend/Loader/Autoloader.php"; 

// Set up autoload.
$loader = Zend_Loader_Autoloader::getInstance();
$loader->setFallbackAutoloader(true);

$env = 'prod';
// Change $env variable to 'prod' parameter under production environment
$initializer = new CLIInitializer($env);

//Start session
Zend_Session::start();

//$assistant = new ACORNDRSCleanupAssistant();
//$assistant->runService();
?>
