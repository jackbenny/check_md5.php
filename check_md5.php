#!/usr/bin/php5
<?php
$VERSION="1.1";
$AUTHOR="(c) 2012 Jack-Benny Persson (jack-benny@cyberinfo.se)";

// Exit codes 
$STATE_OK=0;
$STATE_WARNING=1;
$STATE_CRITICAL=2;
$STATE_UNKNOWN=3;

// Functions 

function print_version()
{
	global $argv, $argc;
	global $VERSION;
	echo "$argv[0] $VERSION\n";
}	

function print_help()
{
	global $AUTHOR;
$HELP_TEXT = <<<'EOD'
Monitor the MD5 checksum of a single file

Options:
-h
   Print detailed help screen
-V
   Print version information

--warning
   Issue a warning state instead of a critical state in case of a MD5 failure
   Default is critical

--file /path/to/file
   Set which file to monitor
 
--md5 md5checksum
   Set the MD5 checksum for the file set by --file
EOD;
// Print the help
	print_version();
	echo "$AUTHOR\n";
	echo "\n$HELP_TEXT\n";
}

print_help();

?>


