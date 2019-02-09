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


$codeStr = FindVal('codeStr');
$startTime = FindVal('startTime');
$captainStr = FindVal('captainStr');
$lengthTime = FindVal('lengthTime');
$stage = FindVal('stage') + 0;

if (isset($_REQUEST['codeStr']))
	$codeStr = $_REQUEST['codeStr'];
if (isset($_REQUEST['startTime']))
	$startTime = "" + $_REQUEST['startTime'];
if (isset($_REQUEST['captainStr']))
	$captainStr = $_REQUEST['captainStr'];
if (isset($_REQUEST['lengthTime']))
	$lengthTime = $_REQUEST['lengthTime'];
if (isset($_REQUEST['stage']))
	$stage = $_REQUEST['stage'] + 0;

$nextPage = $_REQUEST['nextPage'];

$maxnum = 9;

switch ($stage) {
	case 0:
		$maxnum = 4;
		break;
	case 1:
		$maxnum = 6;
		break;
	case 2:
		$maxnum = 8;
		break;
	default:
		$maxnum = 10;
		break;
}

$codeNum = 0;
for ($i = 0; $i < 4; $i++) {
	$codeNum = ($codeNum * 10) + rand( 0 , $maxnum-1);
}
$codeStr = substr("0000{$codeNum}", -4);


/*echo "CodeStr: [{$codeStr}]<BR/>\n";
echo "StartTime [{$startTime}]<BR/>\n";
echo "captainStr [{$captainStr}]<BR/>\n";
echo "nextPage [{$nextPage}]<BR/>\n";*/

$filestr = "var captainCode= new Array({$captainStr[0]},{$captainStr[1]},{$captainStr[2]},{$captainStr[3]});\n";
$filestr .= "var code = new Array({$codeStr[0]},{$codeStr[1]},{$codeStr[2]},{$codeStr[3]});\n";
$filestr .= "var startTime = {$startTime};\n";
$filestr .= "var codeStr = '{$codeStr}';\n";
$filestr .= "var captainStr = '{$captainStr}';\n";
$filestr .= "var lengthTime = '{$lengthTime}';\n";
$filestr .= "var stage = {$stage};\n";
$filestr .= "var maxnum = {$maxnum};\n";
file_put_contents('include.html', $filestr);

header( "Location: {$nextPage}" ) ;
die();
?>
