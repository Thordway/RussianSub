<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>Pressure Release Switches</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"/>
		<link href="pressure.css" rel="stylesheet" type="text/css" />
	</head>
	<body onload='setupPage()'>
    <div align="center" id="light0"></div>
    <div align="center" id="light1"></div>
    <div align="center" id="light2"></div>
    <div align="center" id="light3"></div>
    <div align="center" id="light4"></div>
    <div align="center" id="light5"></div>
    <div align="left" id="countdown">TIME REMAINING</div>
    <div align="center" id="switch0" onclick="toggleSwitch(0)"></div>
    <div align="center" id="switch1" onclick="toggleSwitch(1)"></div>
    <div align="center" id="switch2" onclick="toggleSwitch(2)"></div>
    <div align="center" id="switch3" onclick="toggleSwitch(3)"></div>
    <div align="center" id="switch4" onclick="toggleSwitch(4)"></div>
    <div align="center" id="switch5" onclick="toggleSwitch(5)"></div>
    <div align="center" id="arrows"></div>
    <div align="center" id="captain"></div>
    <div align="center" id="slider" onclick="moveSlider()"></div>
	<div align="center" id="clickhere" onMouseOver="changeClkHere ('click%20here%20over.png');" onMouseOut="changeClkHere ('click%20here.png');" onclick="startTest()"></div>
    <div align="center" id="audiopanel">
        <audio autoplay id="myaudio">
          <source src="klaxon.ogg" type="audio/ogg">
          <source src="klaxon.mp3" type="audio/mpeg">
          <source src="klaxon.wav" type="audio/wav">
         Your browser does not support the audio element.
        </audio>
    </div>
	</body>

	<script language="javascript">
	<?php include "include.html"; ?>

	var countTimer=0;
	var sequenceTimer=0;
	var switchTimer=0;
	var switches = new Array(0,0,0,0,0,0);
	var sequence = new Array(0);
	var captainMode=0;
	var captainCount=0;
	var endTime = startTime + (60000 * lengthTime);
	var arrowPos = 3;
	var arrowMax = 6;
	var sequencePos = 0;
	var sequenceTarget = 0;
	var sequenceSize = 6;
	var sequenceNumber = 0;
	var testPos = 0;
    var mouseY = 10;
	var curTestSwitch = 0;
	var sliderPercent = 50;
	var TEST_STATES = {
	  NOT_RUNNING : {value: 0, name: "Not Running"}, 
	  SEQUENCE_DARK: {value: 1, name: "Sequence, Light is Dark"}, 
	  SEQUENCE_LIGHT : {value: 2, name: "Sequence, Light is On"},
	  TESTING_DARK: {value: 3, name: "Testing, Light is Dark"}, 
	  TESTING_LIGHT : {value: 4, name: "Testing, Light is On"},
	};
	var runningTest = TEST_STATES.NOT_RUNNING; 

    window.onmousemove = handleMouseMove;
    function handleMouseMove(event) {
        event = event || window.event; // IE-ism
        mouseY = event.clientY;
    }
	
	function setupPage(){
		for (var i = 0; i < 6; i++) {
			switches[i] = Math.floor(Math.random()*2);
			if (switches[i])
  				document.getElementById('switch'+i).style.backgroundImage = 'url(switchdown.png)';
			setLight(i,0);
		}
		if (endTime == 0 || startTime == 0) {
			var date = new Date();
			endTime = date.getTime() + (60000 * 10); 
		}
 		//var countdown = document.getElementById('countdown');
		//countdown.textContent = "endTime " + endTime;
		
 		var slider = document.getElementById("slider");
		var num = Math.floor(Math.random()*162);
		sliderPercent = Math.round(num * 100 / 162);
		slider.style.backgroundPosition = "3px "+num+"px";
		
		arrowPos = 3;
		setArrow(arrowPos);
		sequencePos = 0;
		sequenceNumber = 0;
		runningTest = TEST_STATES.NOT_RUNNING;
		countTimer=window.setInterval(function(){updateTimer()},100);
   }
   
   function startTest() {
	   if (runningTest == TEST_STATES.NOT_RUNNING) {
		   changeClkHere('running.png');
		   
		   //create sequence
		   sequenceSize = 6 + Math.floor(Math.random()*(sequenceNumber * 3)); 
		   if (captainMode)
		   	sequenceSize = Math.round(1 * sequenceSize / 3);
		   sequencePos = 0;
		   sequenceTarget = 1;
		   sequence = new Array(0);
		   for (var i = 0; i < sequenceSize; i++)
			do {
				sequence[i] = Math.floor(Math.random()*switches.length);
			} while (sequenceNumber <= 1 && i > 0 && sequence[i]==sequence[i-1]);
		   
		   //start first test
		   runningTest = TEST_STATES.SEQUENCE_DARK;
		   sequenceTimer = window.setInterval(function(){updateSequence()},1000);
		   updateSequence();
	   }
   }
   
	function updateTimer () {
 		var countdown = document.getElementById('countdown');
		var date = new Date();
 		var mils = endTime - date.getTime();
		countdown.textContent = "MILS " + mils;
		if (mils > 0) {
			var milstr = '';
			var secstr = '';
			var mins = Math.floor(mils/60000);
			mils = mils - (mins * 60000);
			var secs = Math.floor(mils/1000);
			mils = mils - (secs * 1000);
			mils = Math.floor(6 * mils / 100);
			if (mils < 10)
				milstr = '0';
			if (secs < 10)
				secstr = '0';
			countdown.textContent = "TIME REMAINING: 0" + mins + ":" +secstr+secs+ ":" +milstr+mils; 
			//+"-"+code[0]+code[1]+code[2]+code[3]+"-";
			if (mins < 5 || captainMode)
				calcTime = 15 * 1000; //15 seconds
			if (mins < 2)
				calcTime = 10 * 1000; //10 seconds
			//setArrow(secs%7);
		} else {
			countdown.textContent = "PRESSURE HAS DESTROYED SUB!"; 
			//+ " - [" + endTime + "] - [" + mils +"]";
			clearInterval(countTimer);  
			window.location="dead.html";
		}
	}
	
	function updateSequence() {
		//lights
		if (sequencePos > 0)
			setLight(sequence[(sequencePos+sequence.length-1)% sequence.length], 0);		

		if (sequencePos >= sequenceTarget) {
			clearInterval(sequenceTimer);  
			runningTest = TEST_STATES.TESTING_DARK;
			testPos = 0;
			return;
		} else if (sequenceTimer == 0) {
	   		sequenceTimer=window.setInterval(function(){updateSequence()},2000);
		}

		setLight(sequence[sequencePos], sequence[sequencePos]+1);
		
		//sound
 		var myaudio = document.getElementById('myaudio');
		myaudio.src = "tones/tone"+sequence[sequencePos]+'.'+soundType;
		
		sequencePos = (sequencePos + 1);
	}

	function checkCaptainMode() {
 		var slider = document.getElementById("slider");
		//slider.textContent = "["+sliderPercent+"]";
		if (sliderPercent > 95 && captainCount == 0)
			captainCount = 1;
		else if (sliderPercent < 5 && captainCount == 1)
			captainCount = 2;
		else if (sliderPercent > 45 && sliderPercent < 55  && captainCount == 2) {
			captainCount = 3;
			return true;
		}
		return false;
	}

	function toggleSwitch (i) {
		switches[i] = 1 - switches[i];
		if (switches[i])
  			document.getElementById('switch'+i).style.backgroundImage = 'url(switchdown.png)';
		else
  			document.getElementById('switch'+i).style.backgroundImage = 'url(switchup.png)';

		if (!captainMode && checkCaptainMode()) {
			captainMode = 1;
			var cm=document.getElementById('captain');
			cm.style.backgroundImage = "url('captain.png')";
		}

		if (runningTest == TEST_STATES.TESTING_DARK) 
		{
			runningTest = TEST_STATES.TESTING_LIGHT;
			curTestSwitch = i;
			setLight(curTestSwitch, curTestSwitch+1);					
			var myaudio = document.getElementById('myaudio');
			myaudio.src = "tones/tone"+curTestSwitch+'.'+soundType;
			switchTimer=window.setInterval(function(){testSwitch()},1000);
		}
	}
		
	function testSwitch() {
		clearInterval(switchTimer);
		switchTimer = 0;
		setLight(curTestSwitch,0);	
		runningTest = TEST_STATES.TESTING_DARK;
		
		if (curTestSwitch == sequence[testPos]) //hooray, go to next switch
		{
			testPos++;
			if (testPos == sequenceSize) {
				runningTest = TEST_STATES.NOT_RUNNING;
				changeClkHere('click%20here.png');
				var myaudio = document.getElementById('myaudio');
				myaudio.src = "pressure"+'.'+soundType;
				arrowPos--;
				if (arrowPos >= 0) {
					setArrow(arrowPos);
					sequenceNumber++;
				} else {
					clearInterval(countTimer);  
					window.location="index.html";
				}
			} else if (testPos == sequenceTarget) {
				sequencePos = 0;
				sequenceTarget++;
				runningTest = TEST_STATES.SEQUENCE_DARK;
				sequenceTimer = window.setInterval(function(){updateSequence()},1000);
				updateSequence();
			}
		} else { //uh oh, boom!
			var myaudio = document.getElementById('myaudio');
			myaudio.src = "buzzer"+'.'+soundType;
			runningTest = TEST_STATES.NOT_RUNNING;
			changeClkHere('click%20here.png');
			arrowPos++;
			if (arrowPos <= arrowMax) {
				setArrow(arrowPos);
			} else {
				clearInterval(countTimer);  
				window.location="dead.html";
			}
		}
	}
	
	function setArrow(i) {
		if (i > 6) i = 6;
 		var arrows = document.getElementById('arrows');
		var num = i * -126;
		arrows.style.backgroundPosition = "0 "+num+"px";
	}
	
	function setLight(i,col) {
 		var light = document.getElementById("light"+i);
		var num = col * -44;
		light.style.backgroundPosition = "0 "+num+"px";
	}
	
	function turnOffLights() {
		for (var i = 0; i < 6; i++) {
			var light = document.getElementById("light"+i);
			light.style.backgroundPosition = "0 0px";
		}
	}
	
	function moveSlider() {
 		var slider = document.getElementById("slider");
		var num = mouseY - 112 - 18;
		if (num < 0) num = 0;
		if (num > 162) num = 162;
		sliderPercent = Math.round(num * 100 / 162);
		slider.style.backgroundPosition = "3px "+num+"px";
		//slider.textContent = "["+num+"]["+mouseY+"]";
	}
	
	function changeClkHere (image) {
		if (runningTest == TEST_STATES.NOT_RUNNING)
  			document.getElementById('clickhere').style.backgroundImage = 'url('+image+')';
		else
  			document.getElementById('clickhere').style.backgroundImage = 'url(running.png)';
	}

    </script>

</html>
