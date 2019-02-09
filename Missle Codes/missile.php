<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>Torpedo Codes</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"/>
		<link href="missile.css" rel="stylesheet" type="text/css" />
	</head>
	<body onload='setupPage()'>
	<div align="center" id="uparrow0" onclick="decrement(0)"></div>
	<div align="center" id="uparrow1" onclick="decrement(1)"></div>
	<div align="center" id="uparrow2" onclick="decrement(2)"></div>
	<div align="center" id="uparrow3" onclick="decrement(3)"></div>
	<div align="center" id="rednumber0"></div>
	<div align="center" id="rednumber1"></div>
	<div align="center" id="rednumber2"></div>
	<div align="center" id="rednumber3"></div>
	<div align="center" id="downarrow0" onclick="increment(0)"></div>
	<div align="center" id="downarrow1" onclick="increment(1)"></div>
	<div align="center" id="downarrow2" onclick="increment(2)"></div>
	<div align="center" id="downarrow3" onclick="increment(3)"></div>
	<div align="center" id="clickhere" onMouseOver="changeClkHere ('click%20here%20over.png');" onMouseOut="changeClkHere ('click%20here.png');" onclick="startTest()"></div>
    <div align="center" id="light0"></div>
    <div align="center" id="light1"></div>
    <div align="center" id="light2"></div>
    <div align="center" id="light3"></div>
    <div align="left" id="countdown">TIME TO LAUNCH 10:00</div>
    <div align="center" id="switch0" onclick="toggleSwitch(0)"></div>
    <div align="center" id="switch1" onclick="toggleSwitch(1)"></div>
    <div align="center" id="switch2" onclick="toggleSwitch(2)"></div>
    <div align="center" id="switch3" onclick="toggleSwitch(3)"></div>
    <div align="center" id="captain"></div>
    <div align="center" id="audiopanel">
        <audio autoplay>
          <source src="klaxon.ogg" type="audio/ogg">
          <source src="klaxon.mp3" type="audio/mpeg">
          <source src="klaxon.wav" type="audio/wav">
         Your browser does not support the audio element.
        </audio>
    </div>
	</body>

	<script language="javascript">
	<?php include "include.html"; ?>

	var guess = new Array(0,0,0,0);
	var switches = new Array(0,0,0,0);
	var calculating = 0;
	var calcTime = 15 * 1000; //15 seconds.
	var targetCalcTime = 0;
	var calcTimer=0;
	var captainMode=0;
	var endTime = startTime + (60000 * lengthTime);
	var countTimer=window.setInterval(function(){updateTimer()},100);

    function setupPage(){
		for (var i = 0; i < 4; i++) {
			var divsec = document.getElementById('rednumber'+i);
			divsec.style.backgroundImage = "url("+0+".jpg)";
			guess[i] = 0;
			switches[i] = Math.floor(Math.random()*2);
			if (switches[i])
  				document.getElementById('switch'+i).style.backgroundImage = 'url(switchdown.png)';
		}
		/*if (code[0] == code[1] == code[2] == code[3] == 0) {
			for (var i = 0; i < 4; i++)
				code[i] = Math.floor(Math.random()*10);
		}*/
		if (endTime == 0 || startTime == 0) {
			var date = new Date();
			endTime = date.getTime() + 600000; 
		}
    }
    function decrement(num){
		if (calculating)
			return;
		var divsec = document.getElementById('rednumber'+num);
		guess[num] = (guess[num] + maxnum - 1) % maxnum;
		divsec.style.backgroundImage = "url("+guess[num]+".jpg)";
    }
    function increment(num){
		if (calculating)
			return;
		var divsec = document.getElementById('rednumber'+num);
		guess[num] = (guess[num] + 1) % maxnum;
		divsec.style.backgroundImage = "url("+guess[num]+".jpg)";
    }
	function changeClkHere (image) {
		if (calculating)
  			document.getElementById('clickhere').style.backgroundImage = 'url(calculating.png)';
		else
  			document.getElementById('clickhere').style.backgroundImage = 'url('+image+')';
	}
	function updateTimer (image , id) {
 		var countdown = document.getElementById('countdown');
		var date = new Date();
 		var mils = endTime - date.getTime();
		if (mils > 0) {
			var milstr = '';
			var secstr = '';
			var minstr = '';
			var mins = Math.floor(mils/60000);
			mils = mils - (mins * 60000);
			var secs = Math.floor(mils/1000);
			mils = mils - (secs * 1000);
			mils = Math.floor(6 * mils / 100);
			if (mils < 10)
				milstr = '0';
			if (secs < 10)
				secstr = '0';
			if (mins < 10)
				minstr = '0';
			countdown.textContent = "TIME TO LAUNCH " +minstr+ mins + ":" +secstr+secs+ ":" +milstr+mils; 
			//+"-"+code[0]+code[1]+code[2]+code[3]+"-";
			if (mins < 5 || captainMode)
				calcTime = 10 * 1000; //10 seconds
			if (mins < 2)
				calcTime = 5 * 1000; //5 seconds
		} else {
			countdown.textContent = "DETONATING TORPEDOS!";
			//+ " - [" + endTime + "] - [" + mils +"]["+codeStr+"]";
			clearInterval(countTimer);
			window.location="dead.html";
		}
	}
	function startTest() {
		if (calculating)
			return;

		calculating = 1;
		changeClkHere ('click%20here.png');
		var date = new Date();
		targetCalcTime = date.getTime() + calcTime;
		calcTimer=window.setInterval(function(){calculate()},250);
	}

	function calculate() {
		var date = new Date();
		if (date.getTime() > targetCalcTime) {
			clearInterval(calcTimer);
			runTest();
			return;
		}

		for (var i = 0; i < 4; i++) {
			var light=document.getElementById('light'+i);
			var num = Math.floor(Math.random()*3);
			switch (num) {
				case 0:
					light.style.backgroundImage = "url('lightoff.png')";
					break;
				case 1:
					light.style.backgroundImage = "url('lightred.png')";
					break;
				case 2:
					light.style.backgroundImage = "url('lightgreen.png')";
					break;
			}
		}
	}

	function checkCaptainMode() {
		for (var i = 0; i < 4; i++) {
			if (captainCode[i] != switches[i])
				return false;
		}
		return true;
	}

	function runTest() {
		var greenCount = 0;
		var redCount = 0;
		var curLight = 0;
		var counted = new Array(0,0,0,0);
		var tested = new Array(0,0,0,0);

		calculating = 0;
		changeClkHere ('click%20here.png');

		if (!captainMode && checkCaptainMode()) {
			captainMode = 1;
			var cm=document.getElementById('captain');
			cm.style.backgroundImage = "url('captain.png')";
		}

		//count green
		for (var i = 0; i < 4; i++) {
			if (guess[i] == code[i]) {
				greenCount++;
				counted[i] = 1;
				tested[i] = 1;
			}
		}
		//count red
		for (var i = 0; i < 4; i++) {
			if (!tested[i]) {
				var keepgoing = 1;
				for (var j = 0; j < 4 && keepgoing; j++) {
					if (guess[i] == code[j] && !counted[j]) {
						redCount++;
						counted[j] = 1;
						keepgoing = 0;
					}
				}
			}
		}
		//show green
		for (var i = 0; i < greenCount; i++) {
			var light=document.getElementById('light'+curLight);
			light.style.backgroundImage = "url('lightgreen.png')";
			curLight++;
		}
		//show red
		for (var i = 0; i < redCount; i++) {
			var light=document.getElementById('light'+curLight);
			light.style.backgroundImage = "url('lightred.png')";
			curLight++;
		}
		//show black
		for (var i = curLight; i < 4; i++) {
			var light=document.getElementById('light'+i);
			light.style.backgroundImage = "url('lightoff.png')";
		}
		
		if (greenCount == 4)
		{
			countdown.textContent = "DETONATION AVERTED...";
			//+ " - [" + endTime + "] - [" + mils +"]["+codeStr+"]";
			clearInterval(countTimer);
			window.location="process.php?stage="+(stage+1)+"&nextPage=index.html";
		}
	}
	function toggleSwitch (i) {
		switches[i] = 1 - switches[i];
		if (switches[i])
  			document.getElementById('switch'+i).style.backgroundImage = 'url(switchdown.png)';
		else
  			document.getElementById('switch'+i).style.backgroundImage = 'url(switchup.png)';
	}
    </script>

</html>
