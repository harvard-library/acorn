A working LAMP stack is required to run the ACORN application.
Versions that ACORN has been used with thus far;

Linux
Red Hat Enterprise Linux Server release 5.11
Amazon Linux version 2015.09
Amazon Linux AMI 2016.09

Apache (with ssl)
2.2.31, 2.4.18, 2.4.23

MySQL 
5.0.95, 5.5.46, 5.5.52

PHP
5.3.4, 5.5.36, 5.6.28

The acorn_setup.sh script in this directory should be run from this directory
to set-up ACORN. Run the script with it's -h option to get a bit more 
information.

Once the ACORN application is up, login using the adminuser user account with
the password of changeme. This password can be changed using the Edit->Password
menu drop down within ACORN.

Before an ACORN record can be created (New->Record) users will need to be added 
(Edit->Person/User) and lists will need to be populated (Edit->Lists).
