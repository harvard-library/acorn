#!/usr/bin/perl
#
# monitor-drs-services-log.pl
#   Run the script with it's -h option to see it's description and usage or 
#   scroll to the subroutines section near the botton.
#
# TME  05/20/15  Initial version

#
# Load modules, define variables, grab arguments & check usage
#
use Getopt::Std;
use Sys::Hostname;
use File::Basename;
use Time::localtime;

$sendmailCmd   = '/usr/sbin/sendmail -t';     # command used when sending mail
$checkInterval = 1;                       # minutes to wait between log parsing
$psCmd         = '/bin/ps auxw';

# Set defaults for log directory and mailto
$logDir = '/home/acorn/prod/logs';
$mailTo = 'tim_elliott@harvard.edu';

# Limit our email messages to about 9MB (sendmail's limit is 10MB)
$msgSizeLimit = 9437184;

# Used with our emailed messages, log file name will be added to header later
$host      = hostname();
$msgHeader = "Errors found in $host:$logDir/";
$logErrors = '';

# Script parameters: usage/help, send email, path to log and instance
getopts('hm:d:i:a:', \%opts) or usage();

# Usage and grab arguments
usage() if $opts{'h'};

if ($opts{'m'}) {
    $mailTo = $opts{'m'};
}

if ($opts{'d'}) {
    $logDir = $opts{'d'};
}

if ($opts{'i'}) {                            # instance is a required parameter
    $instance = $opts{'i'};
}
else {
    usage();
}

if ($opts{'a'}) {                            # action start or stop
    $action = $opts{'a'};                    # required parameter
}
else {
    usage();
}

$subject = "Acorn $instance error log monitor";
$checkInterval *= 60;                           # seconds to minutes conversion

#
# Main program
#

# Only one instance of the script is allowed to run for an app's instance
$scriptName = fileparse($0);
$scriptInstances = 0;

unless(open PSLIST, "$psCmd |") {
        $msg = "FAILED to check system processes with \"$psCmd\". Script will exit.\n";
        send_mail($subject, $msg);
        print $msg;
        exit 0;
}

while (<PSLIST>) {
    if (/^\w+\s+(\d+).+$scriptName.*start.*$instance/) {
        $scriptPid = $1;
    
        if ($action eq 'stop') {
            unless (kill 'TERM', $scriptPid) {
                $msg = "FAILED to stop script with kill \"$scriptPid\". Script will exit.\n";
                send_mail($subject, $msg);
                print $msg;
                exit 0;
            }
        }
        else {
            $scriptInstances++;    
        }
    }
}
close PSLIST;

# No need to go any further if we're only stopped the script
if ($action eq 'stop') {
    print "Log monitor $instance instance has been stopped\n";
    exit 1;
}

if ($scriptInstances > 1) {
    print "Script is already running for $instance\n";
    exit 0;
}

# Drop into an infinite loop to parse log until script is stopped
while (1) {

    $logFile = setLogName('errorlog');   # set log name with current date stamp

    # Log might not exist if app has not thrown errors on a given day
    unless (-s "$logDir/$logFile") {
        sleep $checkInterval;
        next;
    }
    
    # Open log and look for errors
    unless (open LOG, "$logDir/$logFile") {
        $msg = "FAILED to open $logDir/$logFile. Script will exit.\n";
        send_mail($subject, $msg);
        print $msg;
        exit 0;
    }

    while (<LOG>) {

        # Keep track of the which log entries we've checked so far
        $entry = $_;

        # Figure out where to resume parsing (upon subsequent parsing)
        if (defined $resumeParsing and defined $priorEntry) {          
            unless ($resumeParsing) {
                if ($priorEntry eq $entry) {
                    $resumeParsing = 1;
                    $priorEntry = $entry;
                }
                next;
            }
        }

        # Collect any error messages
        if (/ERROR/i) {
            $logErrors .= $_ . "\n";
            
            # Send mail now and then reset $logErrors if we hit our message 
            # size limit. Otherwise, we'll wait until we're done parsing.
            if ((length $logErrors) >= $msgSizeLimit) {
                send_mail($subject, "${msgHeader}$logFile\n\n$logErrors");            
                $logErrors = '';
            }
        }

        $priorEntry = $entry;
    }
    
    # Done parsing the log for now
    close LOG;
    
    if (defined $resumeParsing) {
    
        # This would mean that we did not find where we need to pick up parsing 
        # the log. It happens after a log is rolled over. Undefine the 
        # variables below so that we can start parsing the new log at the begin. 
        if ($resumeParsing == 0) {
            undef $resumeParsing;
            undef $priorEntry;
            next;
        }
        else {
            $resumeParsing = 0;
        }
    }
    else {
        $resumeParsing = 0;
    }
    
    # Email any error messages found
    if ($logErrors) {
        send_mail($subject, "${msgHeader}$logFile\n\n$logErrors");
        $logErrors = '';
        $waitBetweenMail = 15 * $checkInterval;
        sleep $waitBetweenMail;
    }
    
    sleep $checkInterval;
}

print "Done\n";

#
# Subroutines
#

# Set log name with current date stamp and return name
sub setLogName {
    my $logName = $_[0];
    
    $day   = sprintf("%02d", localtime->mday());
    $month = sprintf("%02d", localtime->mon() +1);
    $year  = sprintf("%04d", localtime->year() + 1900);

    return "${logName}.${year}-${month}-${day}";
}

# Send email if that option was specified
sub send_mail {
    my ($subject, $message) = @_;

    open MAIL, "| $sendmailCmd";
    print MAIL<<EOF;
To: $mailTo
Subject: $subject
$message

EOF
    close MAIL;
    return;
}

# Usage/decription
sub usage {
    print<<END;

log-monitor.pl

Monitor $logFile in the directory specified. Script will check the
log every $checkInterval minutes and email any errors found. On subsequent checks,
the script will pick up where it left off (as long as it's kept running).

usage: $0 -a action -i instance [-d directory -m mailTo]
       $0 -h

arguments

  -a action       Where action is start or stop
  
  -i instance     Instance, such as DEV, QA or PROD
  
  -d directory    Directory holding the drs services log to monitor.
                  Optional parameter, defaults to
                  $logDir
  
  -m mailTo       eMail address to send any error messages to.
                  Optional parameter, defaults to
                  $mailTo
                  
  -h              Help, display this message

END

    exit 1;
}

# Clean up
sub END {
    if (defined $runFlag and -e $runFlag) {
        unlink $runFlag;
    }
}
