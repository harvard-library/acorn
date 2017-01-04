#!/bin/bash
#
# Copyright 2016 The President and Fellows of Harvard College
#
#   Licensed under the Apache License, Version 2.0 (the "License");
#   you may not use this file except in compliance with the License.
#   You may obtain a copy of the License at
#
#       http://www.apache.org/licenses/LICENSE-2.0
#
#   Unless required by applicable law or agreed to in writing, software
#   distributed under the License is distributed on an "AS IS" BASIS,
#   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#   See the License for the specific language governing permissions and
#   limitations under the License.
#
# TME  01/04/17  Initial version

#
# Define functions and variables, grab any arguments and check usage
#

# Usage/description
function usage {

cat <<END

Use this script to set-up a new ACORN instance. It will set the proper 
directory permissions and, if specified, set-up a MySQL user and database
for ACORN. The user will be prompted for needed information. The $mysqlAdminUser
MySQL password will be required if databases changes will be made.

END

    exit
}

appHomeDir=`pwd`
mysqlAdminUser='root'
mysqlCmd="mysql --user=$mysqlAdminUser "
mysqlAcornUser="acorn"

if [ $# -gt 0 ]
then
    usage
fi

#
# Main program
#

# Prompt for and collect needed information
echo "Please enter an instance of prod, test or dev"
read instance

if [ $instance != 'prod' -a $instance != 'test' -a $instance != 'dev' ]
then
    echo "Instance of $instance is not supported"
    exit
fi

echo "Please enter the base url for your instance of ACORN (do not include https:// or public/index.php)"
read acornUrl

echo "Please enter an email address to use a recipient for any ACORN errors messages"
read mailTo

echo "Please enter the name of the MySQL database to use for ACORN"
read databaseName

echo "Would you like the MySQL database $databaseName created for ACORN? (y or n)"
read makeDatabase

echo "Would you like the ACORN tables loaded into the MySQL $databaseName database? (any data in database will be lost)(y or n)"
read loadTables

echo "Would you like the MySQL user account $mysqlAcornUser created for ACORN? (y or n)"
read makeUser

echo "Please enter the password that will be or is used for the $mysqlAcornUser MySQL user account"
read mysqlAcornPw

cd $appHomeDir

# Set directory permissions
echo "Setting directory permissions"
chmod 777 logs
chmod 777 acorn_sessions
chmod -R 777 public/userfiles
chmod -R 777 public/userreports

# Setting up the config files. Use sed, our variables and the config templates
echo "Setting up the config files"
cd application
sed "s/<instance>/$instance/" bootstrap.php_template > bootstrap.php
echo "application/bootstrap.php written"
sed "s/<instance>/$instance/" clibootstrap.php_template > clibootstrap.php
echo "application/clibootstrap.php written"
sed -e "s/<instance>/$instance/" -e "s/<username>/$mysqlAcornUser/" -e "s/<password>/$mysqlAcornPw/" -e "s/<databasename>/$databaseName/" dbconfig.ini_template > dbconfig.ini
echo "application/dbconfig.ini written"
sed -e "s/<instance>/$instance/" -e "s/<username>/$mysqlAcornUser/" -e "s|<app_home_dir>|$appHomeDir|g" -e "s|<acorn_url>|$acornUrl|" -e "s/<mail_to>/$mailTo/" config.ini_template > config.ini
echo "application/config.ini written"
cd ../public/scripts
sed "s|<acorn_url>|$acornUrl|" acorn.js_template > acorn.js
echo "public/scripts/acorn.js written"
cd ../..

# Create acorn user and databases is specified
  if [ $makeUser = 'y' -o $makeDatabase = 'y' -o $loadTables = 'y' ]
then

    echo "Please enter the password for your MySQL $mysqlAdminUser user account"
    read mysqlAdminPw
    mysqlCmd+="--password=$mysqlAdminPw"

    mysqlAcornCmds=""

    if [ $makeUser = y ]
    then
        echo "Creating mysqlAcorn MySQL user account"
        mysqlAcornCmds="create user '"
        mysqlAcornCmds+="$mysqlAcornUser'@'localhost' identified by '"
        mysqlAcornCmds+="$mysqlAcornPw';"
    fi

    if [ $makeDatabase = y ]
    then
        echo "Creating database $databaseName"
        mysqlAcornCmds+="create database $databaseName;"
        mysqlAcornCmds+="grant all on $databaseName.* to '"
        mysqlAcornCmds+="$mysqlAcornUser'@'localhost';"
   fi

    if [ -n "$mysqlAcornCmds" ]
    then
$mysqlCmd <<COMMANDS
$mysqlAcornCmds
COMMANDS
    fi

    if [ $loadTables = y ]
    then
        echo "Loading database tables for $databaseName"
        $mysqlCmd $databaseName < acorn_tables.sql
    fi
fi