<?php

function FindVal($valName) {
	global $lines;
	foreach ($lines as $curline)
	{
		$words = explode(' ',$curline);
		if ($words[1] == $valName) {
			$str = trim(trim($words[3]),"';");
			return $str;
		}
	}
}

if (file_exists("include.html")) {
	$lines = file("include.html");
	//echo "{$lines[0]}<BR/>\n";
}
//else
	//echo "File is gone man!<BR>\n";

$startTime = FindVal('startTime');
$lengthTime = FindVal('lengthTime');
$soundType = FindVal('soundType');
$stage = FindVal('stage') + 0;

if (isset($_REQUEST['startTime']))
	$startTime = "" + $_REQUEST['startTime'];
if (isset($_REQUEST['lengthTime']))
	$lengthTime = $_REQUEST['lengthTime'];
if (isset($_REQUEST['soundType']))
	$soundType = $_REQUEST['soundType'];
if (isset($_REQUEST['stage']))
	$stage = $_REQUEST['stage'] + 0;

$nextPage = $_REQUEST['nextPage'];

/*echo "CodeStr: [{$codeStr}]<BR/>\n";
echo "StartTime [{$startTime}]<BR/>\n";
echo "captainStr [{$captainStr}]<BR/>\n";
echo "nextPage [{$nextPage}]<BR/>\n";*/

$filestr .= "var startTime = {$startTime};\n";
$filestr .= "var lengthTime = {$lengthTime};\n";
$filestr .= "var soundType = '{$soundType}';\n";
$filestr .= "var stage = {$stage};\n";
file_put_contents('include.html', $filestr);

header( "Location: {$nextPage}" ) ;
die();
?>
