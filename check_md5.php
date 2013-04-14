#!/usr/bin/php
<?php

/*

################################################################################
#                                                                              #
#  Copyright (C) 2013 Jack-Benny Persson <jack-benny@cyberinfo.se>             #
#                                                                              #
#   This program is free software; you can redistribute it and/or modify       #
#   it under the terms of the GNU General Public License as published by       #
#   the Free Software Foundation; either version 2 of the License, or          #
#   (at your option) any later version.                                        #
#                                                                              #
#   This program is distributed in the hope that it will be useful,            #
#   but WITHOUT ANY WARRANTY; without even the implied warranty of             #
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the              #
#   GNU General Public License for more details.                               #
#                                                                              #
#   You should have received a copy of the GNU General Public License          #
#   along with this program; if not, write to the Free Software                #
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  #
#                                                                              #
################################################################################

###############################################################################
#                                                                             # 
# Nagios plugin to monitor a single files MD5 sum. In case of mismatch        #
# the plugin exit with a CRICITAL error code. This behavior can be changed    #
# with the --warning argument.                                                # 
# Rewritten in PHP (depending on PEAR ConsoleGetopt.                          #
#                                                                             #
###############################################################################

*/

//include PEAR ConsoleGetopt
include ("Console/Getopt.php");


$VERSION="1.0";
$AUTHOR="(c) 2013 Jack-Benny Persson (jack-benny@cyberinfo.se)";

// Exit codes 
$STATE_OK=0;
$STATE_WARNING=1;
$STATE_CRITICAL=2;
$STATE_UNKNOWN=3;


// Default to critical
$warning = "no";

// Functions 
function print_version()
{
	global $argv, $argc;
	global $VERSION;
	fwrite(STDOUT, "$argv[0] $VERSION\n");
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
// Print the help text
	print_version();
	fwrite(STDOUT, "\n");
	fwrite(STDOUT, "$AUTHOR\n");
	fwrite(STDOUT, "\n$HELP_TEXT\n");
}


// Arguments and options (depending on PEAR ConsoleGetopt)
$options = new Console_Getopt();

$shortoptions = "hV?";
$longoptions = array("warning", "help", "version", "file=", "md5=");

$args = $options->readPHPArgv();
$ret = $options->getopt($args, $shortoptions, $longoptions);

if (PEAR::isError($ret)) 
{
	fwrite(STDERR,$ret->getMessage() . "\n\n");
	print_help();
   	exit ($STATE_UNKNOWN);
}

$opts = $ret[0];

if(sizeof($opts) > 0)
{
	foreach($opts as $o)
	{
		switch($o[0])
		{
			case 'h':
			case '--help':
			case '?':
			print_help();
			exit ($STATE_OK);
			break;

			case 'V':
			case '--version':
			print_version();
			exit ($STATE_OK);
			break;

			case '--file':
			$filename = $o[1];
			break;

			case '--md5':
			$md5 = $o[1];
			break;

			case '--warning':
			$warning = "yes";
			break;
		}
	}
}


// Sanity checks
if (empty($filename))
{
	fwrite(STDERR,"No file specified\n\n");
	print_help();
	exit($STATE_UNKNOWN);
}

if (file_exists($filename) == FALSE)
{
	fwrite(STDERR, "File $filename does not exsist!\n");
	exit($STATE_UNKNOWN);
}

if (empty($md5))
{
	fwrite(STDERR,"No MD5 sum specified\n");
	exit($STATE_UNKNOWN);
}


// MAIN
// Compare the file against the MD5 checksum
$file = md5_file($filename);

if ($file == $md5) // Checksum is ok
{
	fwrite(STDOUT, "$filename - MD5 OK\n");
	exit($STATE_OK);
}

elseif ($file != $md5) // Checksum is not ok
{
	if ($warning == "yes") // Fail as warning
	{
		fwrite(STDERR, "$filename - MD5 WARNING\n");
		exit($STATE_WARNING);
	}
	elseif ($warning == "no") // Fail as critical
	{
		fwrite(STDERR, "$filename - MD5 CRITICAL\n");
		exit($STATE_CRITICAL);
	}
}

else // Fail as unknown, something went haywire
{
	fwrite(STDERR, "$filename - MD5 UNKNOWN\n");
	exit($STATE_UNKNOWN);
}


// Catch all and fail with unknown state
exit($STATE_UNKNOWN);


?>


