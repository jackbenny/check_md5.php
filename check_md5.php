#!/usr/bin/php5
<?php

//include class file
include ("Console/Getopt.php");

$VERSION="1.1";
$AUTHOR="(c) 2013 Jack-Benny Persson (jack-benny@cyberinfo.se)";

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

$options = new Console_Getopt();

$shortoptions = "hV";
$longoptions = array("warning", "file=", "md5=");

$args = $options->readPHPArgv();
$ret = $options->getopt($args, $shortoptions, $longoptions);

if (PEAR::isError($ret)) {
	fwrite(STDERR,$ret->getMessage() . "\n");
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
			print_help();
			exit ($STATE_OK);
			break;

			case 'V':
			print_version();
			break;

			case '--file':
			$filename = $o[1];
			echo $filename . "\n";
			break;

			case '--md5':
			$md5 = $o[1];
			break;

			case '--warning':
			echo "";
			break;
		}
	}
}

if (empty($filename))
{
	fwrite(STDERR,"A filename is requierd\n");
	exit($STATE_UNKNOWN);
}

if (empty($md5))
{
	fwrite(STDERR,"You need to enter an MD5 checksum\n");
	exit($STATE_UNKNOWN);
}
$file = md5_file($filename);

if ($file == $md5)
{
	echo "They match\n";
}
echo $file;
echo "\n\n";
?>


