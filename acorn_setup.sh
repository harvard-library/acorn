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

function promptuser {
  # Prompt for and collect needed information
  echo "Please enter an instance of prod, test or dev"
  read ACORNINSTANCE

  if [ $ACORNINSTANCE != 'prod' -a $ACORNINSTANCE != 'test' -a $ACORNINSTANCE != 'dev' ]
  then
      echo "Instance of $ACORNINSTANCE is not supported"
      exit
  fi

  echo "Please enter the base url for your instance of ACORN (do not include https:// or public/index.php)"
  read ACORNURL

  echo "Please enter an email address to use a recipient for any ACORN errors messages"
  read ACORNMAILTO

  echo "Please enter the name of the MySQL database to use for ACORN"
  read ACORNDATABASENAME

  echo "Would you like the MySQL database $databaseName created for ACORN? (y or n)"
  read ACORNMAKEDB

  echo "Would you like the ACORN tables loaded into the MySQL $databaseName database? (any data in database will be lost)(y or n)"
  read ACORNLOADTABLES

  echo "Would you like the MySQL user account $mysqlAcornUser created for ACORN? (y or n)"
  read ACORNMAKEUSER

  echo "Please enter the password that will be or is used for the $mysqlAcornUser MySQL user account"
  read ACORNMYSQLPASS

  if [ $ACORNMAKEUSER = 'y' -o $ACORNMAKEDB = 'y' -o $ACORNLOADTABLES = 'y' ]
then

    echo "Please enter the password for your MySQL $mysqlAdminUser user account"
    read MYSQLADMINPW
  fi

  export ACORNINSTANCE ACORNURL ACORNMAILTO ACORNDATABASENAME ACORNMAKEDB ACORNLOADTABLES ACORNMAKEUSER ACORNMYSQLPASS MYSQLADMINPW
}

APPHOMEDIR=`pwd`
MYSQLADMINUSER='root'
MYSQLCMD="mysql --user=$MYSQLADMINUSER"
MYSQLACORNUSER="acorn"

echo $1

if [ $# -eq 0 ]
then
    promptuser
elif [[ "$1" != "-s" ]]
then
  usage
fi
#
# Main program
#

if [ -z $ACORNINSTANCE ]
then
  echo "INSTANCE NOT SET!"
  usage
fi

cd $APPHOMEDIR

# Set directory permissions
echo "Setting directory permissions"
chmod 777 logs
chmod 777 acorn_sessions
chmod -R 777 public/userfiles
chmod -R 777 public/userreports

# Setting up the config files. Use sed, our variables and the config templates
echo "Setting up the config files"
cd application
sed "s/<instance>/$ACORNINSTANCE/" bootstrap.php_template > bootstrap.php
echo "application/bootstrap.php written"
sed "s/<instance>/$ACORNINSTANCE/" clibootstrap.php_template > clibootstrap.php
echo "application/clibootstrap.php written"
sed -e "s/<instance>/$ACORNINSTANCE/" -e "s/<username>/$MYSQLACORNUSER/" -e "s/<password>/$ACORNMYSQLPASS/" -e "s/<databasename>/$ACORNDATABASENAME/" dbconfig.ini_template > dbconfig.ini
echo "application/dbconfig.ini written"
sed -e "s/<instance>/$ACORNINSTANCE/" -e "s/<username>/$MYSQLACORNUSER/" -e "s|<app_home_dir>|$APPHOMEDIR|g" -e "s|<acorn_url>|$ACORNURL|" -e "s/<mail_to>/$ACORNMAILTO/" config.ini_template > config.ini
echo "application/config.ini written"
cd ../public/scripts
sed "s|<acorn_url>|$ACORNURL|" acorn.js_template > acorn.js
echo "public/scripts/acorn.js written"
cd ../..

# Create acorn user and databases is specified
if [ $ACORNMAKEUSER = 'y' -o $ACORNMAKEDB = 'y' -o $ACORNLOADTABLES = 'y' ]
then

    MYSQLCMD+=" --password=$MYSQLADMINPW"

    mysqlAcornCmds=""

    if [ $ACORNMAKEUSER = y ]
    then
        echo "Creating mysqlAcorn MySQL user account: $MYSQLACORNUSER"
        mysqlAcornCmds="create user '"
        mysqlAcornCmds+="$MYSQLACORNUSER'@'localhost' identified by '"
        mysqlAcornCmds+="$ACORNMYSQLPASS';"
    fi

    if [ $ACORNMAKEDB = y ]
    then
        echo "Creating database $ACORNDATABASENAME"
        mysqlAcornCmds+="create database $ACORNDATABASENAME;"
        mysqlAcornCmds+="grant all on $ACORNDATABASENAME.* to '"
        mysqlAcornCmds+="$MYSQLACORNUSER'@'localhost';"
    fi

    if [ -n "$mysqlAcornCmds" ]
    then
        $MYSQLCMD <<COMMANDS
$mysqlAcornCmds
COMMANDS
    fi
    echo $MYSQLCMD
    if [ $ACORNLOADTABLES = y ]
    then
        echo "Loading database tables for $ACORNDATABASENAME"
        $MYSQLCMD $ACORNDATABASENAME < acorn_tables.sql
    fi
fi
