These scripts are used by the acornadm cronjobs.  They must be run via cron because
ACORN runs as apache and the dropbox keys have to be set up for the acornadm user.
If you are setting up an ACORN insatance outside of the designated dev/qa/prod instances,
you will have to have the two scripts findLoadReports<instancename>.sh and performDropboxTasks<instancename>.sh 
(see the two templates) and set up a cronjob to run the scripts.  Make sure these scripts are executable.
