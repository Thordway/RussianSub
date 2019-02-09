<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>Submarine Pressure</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"/>
		<link href="pressure.css" rel="stylesheet" type="text/css" />
	</head>
	<body onload='setupPage()'>
	<div align="left" id="countdown">TIME REMAINING</div>
	<div align="left" id="movebox">MOVES REMAINING</div>
    <div align="center" id="arrows"></div>
	<?php
		$maxx = 11;
		$maxy = 10;
		$maxblank = intval(($maxx * $maxy) / 8);
		for ($x = 0; $x < $maxx; $x++)
		for ($y = 0; $y < $maxy; $y++)
			printf("<div align='center' name='tile%d-%d' id='tile'></div>\n",$x,$y);
			
		$maxpin = 7;
		for ($i = 0; $i < $maxpin; $i++)
			printf("<div align='center' name='pin-%d' id='tile' onclick='updateLights(%d)'></div>\n",$i,$i);
	?>
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

	var COLORS = {
	  RED : {value: 0, name: "Red", img: "redlight.png", offsetx: 1, offsety: 0}, 
	  YELLOW: {value: 1, name: "Yellow", img: "yellowlight.png", offsetx: 1, offsety: 1}, 
	  GREEN : {value: 2, name: "Green", img: "greenlight.png", offsetx: 0, offsety: 1},
	  BLUE : {value: 3, name: "Blue", img: "bluelight.png", offsetx: 0, offsety: 0},
	  ORANGE : {value: 4, name: "Orange", img: "orangelight.png", offsetx: 2, offsety: 1},
	  PURPLE : {value: 5, name: "Purple", img: "purplelight.png", offsetx: 2, offsety: 0},
	  WHITE : {value: 6, name: "White", img: "whitelight.png", offsetx: 3, offsety: 0},
	};

	var countTimer=0;
	var endTime = startTime + (60000 * lengthTime);
	var maxx = <?=$maxx?>;
	var maxy = <?=$maxy?>;
	var numx = 0;
	var numy = 0;
	var numcolors = 4;
	var colors;
	var basex = 27;
	var basey = 46;
	var tilesize = -50; //needs to be negative
	var tiles = 0;
	var pins = 0;
	var moves = 12;
	var arrowPos = 3;
	var arrowMax = 6;
	var delayEnd = 0;
	var nextIncrement = 0;
	
	function setupPage(){
		if (endTime == 0 || startTime == 0) {
			var date = new Date();
			startTime = date.getTime();
			endTime = startTime + (60000 * lengthTime); 
		}
		switch (stage)
		{
			case 0:
				numx = Math.floor(Math.random() * 2) + 5;
				numy = Math.floor(Math.random() * 2) + 5;
				numcolors = 4;
				break;
			case 1:
				numx = Math.floor(Math.random() * 2) + 6;
				numy = Math.floor(Math.random() * 2) + 6;
				numcolors = 5;
				break;
			case 2:
				numx = Math.floor(Math.random() * 2) + 8;
				numy = Math.floor(Math.random() * 2) + 8;
				numcolors = 5;
				break;
			case 3:
				numx = Math.floor(Math.random() * 2) + 10;
				numy = Math.floor(Math.random() * 2) + 10;
				numcolors = 6;
				break;
			default:
			case 4:
				numx = maxx;
				numy = maxy;
				numcolors = 7;
				break;
		}
		if (numx > maxx)
			numx = maxx;
		if (numy > maxy)
			numy = maxy;
		
		colors = new Array(7);
		colors[0] = COLORS.RED;
		colors[1] = COLORS.YELLOW;
		colors[2] = COLORS.GREEN;
		colors[3] = COLORS.BLUE;
		colors[4] = COLORS.ORANGE;
		colors[5] = COLORS.PURPLE;
		colors[6] = COLORS.WHITE;

		pins = new Array(numcolors);
		var xpos = 650;
		var i = 0;
		for (i = 0; i < numcolors; i++) 
		{
			ypos = basey + (i * (-tilesize));
			if (i == 6)
			{
				xpos = 650 - tilesize;
				ypos = basey;
			}
			pins[i] = new Object();
			pins[i].tile = document.getElementsByName("pin-"+i)[0];
			pins[i].tile.style.backgroundImage = "url(tiles/pins.png)";
			pins[i].tile.style.left = ""+xpos+"px";
			pins[i].tile.style.top = ""+ypos+"px";
			pins[i].tile.style.height = ""+(-tilesize)+"px";
			pins[i].tile.style.width = ""+(-tilesize)+"px";
			pins[i].color = colors[i];
			pins[i].coloridx = i;				
			var str = ""+(tilesize*colors[i].offsetx)+"px "+(tilesize*colors[i].offsety)+"px";
			pins[i].tile.style.backgroundPosition = str;
		}			

		setupGrid();

 		//var countdown = document.getElementById('countdown');
		//countdown.textContent = "endTime " + endTime;
		countTimer=window.setInterval(function(){updateTimer()},100);
		delayEnd=0;
   }
    
	function initGrid() {
		for (var x = 0; x < maxx; x++)
		for (var y = 0; y < maxy; y++) 
		{
			var tile = document.getElementsByName("tile"+x+"-"+y)[0];
			tile.style.backgroundImage = "";
		}
	}
	 
	function setupGrid() {
		var x,y;
		
		moves = Math.floor(numx * numy / 4);
		moves += arrowPos; 
		//moves += numcolors - 1;
		if (moves < 5)
			moves = 5;
		
		initGrid();
		
		tiles = new Array(numx+1);
		for (x = 0; x < numx; x++)
		{
			tiles[x] = new Array(numy+1);
			xpos = basex + (x * (-tilesize));
			for (var y = 0; y < numy; y++) 
			{
				ypos = basey + (y * (-tilesize));
				i = Math.floor(Math.random() * numcolors);
				tiles[x][y] = new Object();
				tiles[x][y].tile = document.getElementsByName("tile"+x+"-"+y)[0];
				tiles[x][y].tile.style.backgroundImage = "url(tiles/"+colors[i].img+")";
				tiles[x][y].tile.style.left = ""+xpos+"px";
				tiles[x][y].tile.style.top = ""+ypos+"px";
				tiles[x][y].tile.style.height = ""+(-tilesize)+"px";
				tiles[x][y].tile.style.width = ""+(-tilesize)+"px";
				tiles[x][y].x = x;
				tiles[x][y].y = y;
				tiles[x][y].lit = 0;
				tiles[x][y].color = colors[i];
				tiles[x][y].coloridx = i;				
				var str = "0px "+(tilesize)+"px";
				tiles[x][y].tile.style.backgroundPosition = str;
			}			
		}
		
		updateLights((tiles[0][0].coloridx+1) % numcolors);
	}
	
	function updateTimer () {
		if (endTime == 0 || startTime == 0)
			return;
 		var countdown = document.getElementById('countdown');
 		var movebox = document.getElementById('movebox');
		var date = new Date();
 		var mils = endTime - date.getTime();
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
			countdown.textContent = "TIME UNTIL ENGINE FAILURE 0" + mins + ":" +secstr+secs+ ":" +milstr+mils; 
		} else {
			countdown.textContent = "PRESSURE HAS DESTROYED SUB!"; 
			//+ " - [" + endTime + "] - [" + mils +"]";
			/*console.debug("startTime: ["+startTime+"]\n");
			console.debug("endTime: ["+endTime+"]\n");
			console.debug("difference: ["+(endTime-startTime)+"]\n");
			console.debug("mills: ["+(mils)+"]\n");*/
			clearInterval(countTimer);  
			window.location="dead.html";
		}
		if (delayEnd > 0) {
			if (nextIncrement < 0)
				movebox.textContent = "CORRECT SEQUENCE: RESETTING GRID";
			else
				movebox.textContent = "FAILURE: RESETTING GRID";
			if (date.getTime() > delayEnd) {
				nextLevel(nextIncrement);
			}
		} else {
			movebox.textContent = "MOVES REMAINING: "+moves;
		}
	}
	
	function nextLevelDelay(increment)
	{
		var date = new Date();
		delayEnd = date.getTime() + (10 * 1000); //10 seconds
		nextIncrement = increment;
		arrowPos += increment;
		if (arrowPos > arrowMax) {
			window.location="dead.html";
		} else {
			setArrow(arrowPos);
		}
		var myaudio = document.getElementById('myaudio');
		if (increment < 0)
		{
			myaudio.src = "pressure"+'.'+soundType;
		} 
		else
		{
			myaudio.src = "buzzer"+'.'+soundType;
		}	
	}
	
	function nextLevel(increment)
	{
		delayEnd = 0;
		console.debug("nextLevel: ["+arrowPos+"]\n");
		if (arrowPos > arrowMax) {
			window.location="dead.html";
		} else if (arrowPos <= 0) {
			window.location="process.php?stage="+(stage+1)+"&nextPage=index.html";
		} else {
			numx -= increment;
			numy -= increment;
			if (numx > maxx)
				numx = maxx;
			if (numy > maxy)
				numy = maxy;
			setupGrid();
		}
	}
	
	function checkLights() {
		for (var x = numx - 1; x >= 0; x--)
		for (var y = numy - 1; y >= 0; y--) 
			if (tiles[x][y].lit == 0)
				return false;
		return true;
	}
	
	function updateLights(coloridx) {
		if (delayEnd > 0)
			return;
		var oldidx = tiles[0][0].coloridx;
		if (oldidx != coloridx)
		{
			moves = moves - 1;
			updateColorsInner(0,0,coloridx,oldidx);
			for (var x = 0; x < numx; x++)
			for (var y = 0; y < numy; y++) 
				tiles[x][y].lit = 0;
			updateLightsInner(0,0,coloridx);
			if (checkLights())
				nextLevelDelay(-1);
			else if (moves == 0)
				nextLevelDelay(1);
		}
	}
	
	function updateColorsInner(x,y,coloridx,oldidx) {
		if (x < 0 || x >= numx)
			return;
		if (y < 0 || y >= numy)
			return;
		if (tiles[x][y].coloridx != oldidx)
			return;
		
		tiles[x][y].color = colors[coloridx];
		tiles[x][y].coloridx = coloridx;				
		tiles[x][y].tile.style.backgroundImage = "url(tiles/"+colors[coloridx].img+")";
		
		updateColorsInner(x-1,y,coloridx,oldidx);
		updateColorsInner(x+1,y,coloridx,oldidx);
		updateColorsInner(x,y-1,coloridx,oldidx);
		updateColorsInner(x,y+1,coloridx,oldidx);
	}
	
	
	function updateLightsInner(x,y,coloridx) {
		if (x < 0 || x >= numx)
			return;
		if (y < 0 || y >= numy)
			return;
		if (tiles[x][y].coloridx != coloridx)
			return;
		if (tiles[x][y].lit == 1)
			return;
			
		tiles[x][y].lit = 1;
		var str = "0px 0px";
		tiles[x][y].tile.style.backgroundPosition = str;
		
		updateLightsInner(x-1,y,coloridx);
		updateLightsInner(x+1,y,coloridx);
		updateLightsInner(x,y-1,coloridx);
		updateLightsInner(x,y+1,coloridx);
	}
	
	function setArrow(i) {
		if (i > arrowMax) 
			i = arrowMax;
		if (i < 0) 
			i = 0;
 		var arrows = document.getElementById('arrows');
		var num = i * -126;
		arrows.style.backgroundPosition = "0 "+num+"px";
	}

	
    </script>

</html>
