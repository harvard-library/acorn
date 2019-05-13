## Please Note: 
ACORN is currently under a reimplementation effort to migrate the existing solution from PHP to an Angular/Node.js solution.  Harvard is committed to release the new version once we have completed the migration.  The new version will come with working Docker images that should make startup easier and will support Passport authentication modules so you should be able to plug in better autentication.  We are no longer maintaining this PHP code base due to age of the system and the difficulty to migrate from a Zend platform to a newer platform like Laravel.  


## What is ACORN?

ACORN (A Conservation Records Network) is a workflow management system and database for preservation documentation to be used by conservators of artistic and historic materials. It has been in use by Harvard University conservators since 2009.

There are four major tasks that ACORN can perform. They are:

- **Registrarial tracking system of objects** -  entering and leaving the conservation lab or other preservation spaces.
- **Depository for treatment documentation** - written and visual, embedded and linked. Data can be shared and accessed by conservation, registrarial, and curatorial units.
- **Tracking system for preservation work and/or conservation activities off-site** - This may include activities such as environmental monitoring, emergency response, quick assessments, quick repair or other treatment related activities that do not have a full proposal.
- **Statistics queries** - for example, amount of treatment hours by person, project, repository, statistics by work type.

A built-in search engine makes information retrieval simple. As a web-based system, ACORN allows users to access the data at various locations and is a sustainable paperless system. All data is stored locally, either with the user or the user’s institution.

ACORN runs on a LAMP stack, using Linux as the operating system, Apache as the web server that allows the PHP to run, and PHP which communicates with MySQL to get its data. 

Versions that ACORN has been used with thus far;

Linux
Red Hat Enterprise Linux Server release 5.11, Amazon Linux version 2015.09, Amazon Linux AMI 2016.09

Apache (with ssl)
2.2.31, 2.4.18, 2.4.23

MySQL 
5.0.95, 5.5.46, 5.5.52

PHP
5.3.4, 5.5.36, 5.6.28

To set-up ACORN move into the acorn install directory and then run the acorn_setup.sh script. Running the script with it's -h option will give a bit more information.

Once the ACORN application is up, login using the adminuser user account with the password of changeme. This password can be changed using the Edit->Password menu drop down within ACORN.

Before an ACORN record can be created (New->Record) users will need to be added (Edit->Person/User) and lists will need to be populated (Edit->Lists).
