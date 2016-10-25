<?php
/* 
 * Copyright 2016 The President and Fellows of Harvard College
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

chdir('../../../../../public'); 
require_once '../application/clibootstrap.php'; 

//set_include_path('../application/default/controllers' 
//                  . PATH_SEPARATOR . get_include_path());

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);
ini_set('memory_limit', '128M');
date_default_timezone_set('America/New_York');

$recordtype = 'item';

$options = getopt("u:r:");

if (isset($options['u']))
{
    $userid = $options['u'];
}
else {
    print "Please specify user ID with the '-u' the option\n";
    exit;    
}

if (isset($options['r']))
{
    $recordid = $options['r'];
}
else {
    print "Please specify a record ID with the '-r' the option\n";
    exit;    
}

// Set the item/record
$item = ItemDAO::getItemDAO()->getItem($recordid);
RecordNamespace::setCurrentItem($item);

$DRSBulkProcess = new DRSBulkCli();
$DRSBulkProcess->processFiles($userid, $recordid, $recordtype);

?>
