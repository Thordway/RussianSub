<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>Engine Maintenance</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"/>
		<link href="engine.css" rel="stylesheet" type="text/css" />
	</head>
    <body onload='setupPage()'>
    <div align="left" id="countdown">TIME UNTIL ENGINE FAILURE 10:00</div>
    <div align="center" id="captain"></div>
    <?php
		$maxx = 12;
		$maxy = 10;
		$maxblank = intval(($maxx * $maxy) / 8);
		for ($x = 0; $x < $maxx; $x++)
		for ($y = 0; $y < $maxy; $y++)
			printf("<div align='center' name='tile%d-%d' id='tile' onclick='rotateTile(%d,%d,1)'></div>\n",$x,$y,$x,$y);
	?>
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

	var TILE_STATES = {
	  BLANK : {value: 0, name: "Tile is empty"}, 
	  LIGHT: {value: 1, name: "A light"}, 
	  WIRE : {value: 2, name: "A wire"},
	  POWER : {value: 3, name: "The power supply"},
	};
	/*
	var basex = 27;
	var basey = 46;
	var tilesize = -100; //needs to be negative
	var maxx = 6;
	var maxy = 5;
	var maxblank = 4;
	var pngext = ".png";
	*/
	
	var basex = 27;
	var basey = 46;
	var tilesize = -50; //needs to be negative
	var maxx = <?=$maxx?>;
	var maxy = <?=$maxy?>;
	var maxblank = <?=$maxblank?>;
	var pngext = "2.png";
	var numx = 0;
	var numy = 0;
		
	var countTimer=window.setInterval(function(){updateTimer()},100);
	var captainMode=0;
	var endTime = startTime + (60000 * lengthTime);
	var tiles = new Array(maxx+1);
	var batx = 0;
	var baty = 0;
	var lightcount = 0;
	var blankcount = 0;
	var curlight = 0;

	function updateTimer (image , id) {
		if (endTime == 0 || startTime == 0)
			return;
 		var countdown = document.getElementById('countdown');
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
			//+"-"+code[0]+code[1]+code[2]+code[3]+"-";
			if (mins < 5 || captainMode)
				calcTime = 15 * 1000; //15 seconds
			if (mins < 2)
				calcTime = 10 * 1000; //10 seconds
		} else {
			countdown.textContent = "ENGINE HAS FAILED!";
			//+ " - [" + endTime + "] - [" + mils +"]["+codeStr+"]";
			clearInterval(countTimer);
			window.location="dead.html";
		}
	}

    function setupPage(){
		if (endTime == 0 || startTime == 0) {
			var date = new Date();
			startTime = date.getTime();
			endTime = startTime + 600000; 
		}
		switch (stage)
		{
			case 0:
				numx = Math.floor(Math.random() * 2) + 5;
				numy = Math.floor(Math.random() * 2) + 5;
				maxblank = Math.floor((numx * numy) / 8);
				break;
			case 1:
				numx = Math.floor(Math.random() * 2) + 6;
				numy = Math.floor(Math.random() * 2) + 6;
				maxblank = Math.floor((numx * numy) / 8);
				break;
			case 2:
				numx = Math.floor(Math.random() * 2) + 8;
				numy = Math.floor(Math.random() * 2) + 8;
				maxblank = Math.floor((numx * numy) / 8);
				break;
			case 3:
				numx = Math.floor(Math.random() * 2) + 10;
				numy = Math.floor(Math.random() * 2) + 10;
				maxblank = Math.floor((numx * numy) / 6);
				break;
			default:
			case 4:
				numx = maxx;
				numy = maxy;
				maxblank = Math.floor((numx * numy) / 9);
				break;
		}
		if (numx > maxx)
			numx = maxx;
		if (numy > maxy)
			numy = maxy;
		console.debug("numx: "+numx+" numy: "+numy+" maxblank: "+maxblank);
		var mycount = 0;
		do {
			setupGrid();
			mycount++;
			console.debug("count: "+mycount+" blankcount: "+blankcount+" maxblank: "+maxblank);
		} while (blankcount > maxblank && mycount < 5);
		checkPower(batx,baty,-1);
    }
	
	function setupGrid() {
		var x,y;
		tiles = new Array(numx+1);
		for (x = 0; x < numx; x++)
		{
			tiles[x] = new Array(numy+1);
			xpos = basex + (x * (-tilesize));
			for (var y = 0; y < numy; y++) 
			{
				ypos = basey + (y * (-tilesize));
				tiles[x][y] = new Object();
				tiles[x][y].tile = document.getElementsByName("tile"+x+"-"+y)[0];
				tiles[x][y].tile.style.backgroundImage = "url(tiles/block"+pngext+")";
				tiles[x][y].tile.style.left = ""+xpos+"px";
				tiles[x][y].tile.style.top = ""+ypos+"px";
				tiles[x][y].tile.style.height = ""+(-tilesize)+"px";
				tiles[x][y].tile.style.width = ""+(-tilesize)+"px";
				tiles[x][y].state = TILE_STATES.BLANK;
				tiles[x][y].x = x;
				tiles[x][y].y = y;
				tiles[x][y].checked = 0;
				tiles[x][y].connections = [0,0,0,0];
				tiles[x][y].row = Math.floor(Math.random() * 3);
				tiles[x][y].col = 0;
				tiles[x][y].lit = 0;
				var str = ""+(tiles[x][y].col*tilesize)+"px "+(tiles[x][y].row*tilesize)+"px";
				tiles[x][y].tile.style.backgroundPosition = str;
			}			
		}
		
		setBattery();
		
		lightcount = 0;
		blankcount = 0;
		for (x = 0; x < numx; x++)
		{
			for (var y = 0; y < numy; y++) 
			{
				if (tiles[x][y].state != TILE_STATES.BLANK)
				{
					var num = Math.floor(Math.random() * 4);
					for (var i = 0; i < num; i++)
						rotateTile(x,y,0);
					if (tiles[x][y].state == TILE_STATES.LIGHT)
						lightcount++;
				}
				else
					blankcount++;
			}
		}
		
		return lightcount;
	}

	function setBattery()
	{
		var x = Math.floor(Math.random() * numx);
		var y = Math.floor(Math.random() * numy);
		tiles[x][y].tile.style.backgroundImage = "url(tiles/power"+pngext+")";
		tiles[x][y].state = TILE_STATES.POWER;
		tiles[x][y].lit = 1;
		 
		batx = x;
		baty = y;

		for (i = 0; i < 6; i++) 
		{
			var col = Math.floor(Math.random() * 4);
			
			var ret = 0;
			switch (col) {
				case 0:
					ret = placeLight(x,y-1,(col+2)%4);
					break;
				case 1:
					ret = placeLight(x+1,y,(col+2)%4);
					break;
				case 2:
					ret = placeLight(x,y+1,(col+2)%4);
					break;
				case 3:
					ret = placeLight(x-1,y,(col+2)%4);
					break;
			}
			
			if (ret)
				tiles[x][y].connections[col] = 1;
		}
		
		setTileImage(x,y);
	}

	function countConnections(x,y) {
		return tiles[x][y].connections[0] + tiles[x][y].connections[1] + 
			   tiles[x][y].connections[2] + tiles[x][y].connections[3];
	}
	
	function firstConnection(x,y) {
		for (var i = 0; i < 4; i++)
		if (tiles[x][y].connections[i])
			return i;
		return 0;
	}
	
	function secondConnection(x,y) {
		var first = -1;
		for (var i = 0; i < 4; i++)
		if (tiles[x][y].connections[i])
		if (first >= 0)
			return i;
		else
			first = i;
		return 0;
	}
	
	function firstEmptyConnection(x,y) {
		for (var i = 0; i < 4; i++)
		if (tiles[x][y].connections[i] == 0)
			return i;
		return 0;
	}
	
	function setTileImage(x,y) {
		var count = countConnections(x,y);
		switch (count) {
			case 0:
				tiles[x][y].row = 0;
				tiles[x][y].col = 0;
				break;
			case 1:
				tiles[x][y].row = 0;
				tiles[x][y].col = firstConnection(x,y);
				break;
			case 2:
				var pos1 = firstConnection(x,y);
				var pos2 = secondConnection(x,y);
				if (pos2 == pos1 + 2)
					tiles[x][y].row = 2;
				else
					tiles[x][y].row = 1;
				if (pos1 == 0 && pos2 == 3)
					tiles[x][y].col = pos2; 
				else
					tiles[x][y].col = pos1;
				break;
			case 3:
				tiles[x][y].row = 3;
				tiles[x][y].col = (firstEmptyConnection(x,y) + 1) % 4;
				break;
			case 4:
				tiles[x][y].row = 4;
				tiles[x][y].col = 0;
				break;
		}
		var str = ""+(tiles[x][y].col*tilesize)+"px "+(tiles[x][y].row*tilesize)+"px";
		tiles[x][y].tile.style.backgroundPosition = str;
		//console.debug(''+x+','+y+'-'+tiles[x][y].connections[0] + tiles[x][y].connections[1] + tiles[x][y].connections[2] + tiles[x][y].connections[3]+'-'+str);
	}

	function checkPower(x,y,from) {
		//console.debug('checkPower start');
		//console.debug(''+x+','+y);
		if (x < 0 || y < 0 || x >= numx || y >= numy)
			return;
		if (tiles[x][y].state == TILE_STATES.BLANK) 
			return;
		if (tiles[x][y].state != TILE_STATES.POWER && tiles[x][y].lit) 
			return;
		if (from >= 0 && tiles[x][y].connections[from] == 0) 
			return;
		if (tiles[x][y].state == TILE_STATES.WIRE) {
			tiles[x][y].tile.style.backgroundImage = "url(tiles/yellowwire"+pngext+")";
		}
		if (tiles[x][y].state == TILE_STATES.LIGHT) {
			tiles[x][y].row = 1;
			str = ""+(tiles[x][y].col*tilesize)+"px "+(tiles[x][y].row*tilesize)+"px";
			tiles[x][y].tile.style.backgroundPosition = str;
			curlight++;
		}
		tiles[x][y].lit = 1;
		if (tiles[x][y].connections[0] && from != 0)
			checkPower(x,y-1,2);
		if (tiles[x][y].connections[1] && from != 1)
			checkPower(x+1,y,3);
		if (tiles[x][y].connections[2] && from != 2)
			checkPower(x,y+1,0);
		if (tiles[x][y].connections[3] && from != 3)
			checkPower(x-1,y,1);
	}

	function darkenTile(x,y) {
		tiles[x][y].lit = 0;
		if (tiles[x][y].state == TILE_STATES.LIGHT) {
			tiles[x][y].row = 0;
			str = ""+(tiles[x][y].col*tilesize)+"px "+(tiles[x][y].row*tilesize)+"px";
			tiles[x][y].tile.style.backgroundPosition = str;
		} else if (tiles[x][y].state == TILE_STATES.WIRE) {
			tiles[x][y].tile.style.backgroundImage = "url(tiles/bluewire"+pngext+")";
		} 
	}

	function rotateTile(x,y,docheckpower) {
		//console.debug('rotate: '+x+','+y);
		if (tiles[x][y].state != TILE_STATES.BLANK) {
			tiles[x][y].col = (tiles[x][y].col + 1) % 4;
			var str = ""+(tiles[x][y].col*tilesize)+"px "+(tiles[x][y].row*tilesize)+"px";
			tiles[x][y].tile.style.backgroundPosition = str;
			var temp = tiles[x][y].connections[3];
			tiles[x][y].connections[3] = tiles[x][y].connections[2];
			tiles[x][y].connections[2] = tiles[x][y].connections[1];
			tiles[x][y].connections[1] = tiles[x][y].connections[0];
			tiles[x][y].connections[0] = temp;
			if (docheckpower) {
				curlight = 0;
				for (x = 0; x < numx; x++)
					for (var y = 0; y < numy; y++) 
						darkenTile(x,y);
				checkPower(batx,baty,-1);
				if (curlight == lightcount) {
					clearInterval(countTimer);
					countdown.textContent = "REPAIRED ENGINE!";
					window.location="process.php?stage="+(stage+1)+"&nextPage=index.html";
				}
			}
		}
	}
	
	function makeLight(x,y,from) {
		switch (Math.floor(Math.random() * 5)) {
			case 0:
				tiles[x][y].tile.style.backgroundImage = "url(tiles/greenlight"+pngext+")";
				break;
			case 1:
				tiles[x][y].tile.style.backgroundImage = "url(tiles/redlight"+pngext+")";
				break;
			case 2:
				tiles[x][y].tile.style.backgroundImage = "url(tiles/yellowlight"+pngext+")";
				break;
			case 3:
				tiles[x][y].tile.style.backgroundImage = "url(tiles/bluelight"+pngext+")";
				break;
			case 4:
				tiles[x][y].tile.style.backgroundImage = "url(tiles/whitelight"+pngext+")";
				break;
		}
		tiles[x][y].state = TILE_STATES.LIGHT;
		tiles[x][y].row = 0;
		tiles[x][y].col = from;
		str = ""+(tiles[x][y].col*tilesize)+"px "+(tiles[x][y].row*tilesize)+"px";
		tiles[x][y].tile.style.backgroundPosition = str;
	}
	
	function placeLight(x,y,from) {
		if (x < 0 || x >= numx || y < 0 || y >= numy)
			return 0;
			
		if (tiles[x][y].state != TILE_STATES.BLANK)
			return 0;
			
		tiles[x][y].tile.style.backgroundImage = "url(tiles/bluewire"+pngext+")";
		tiles[x][y].state = TILE_STATES.WIRE;
		tiles[x][y].connections[from] = 1;
		
		var doit = Math.floor(Math.random() * 4) + 2;
		for (var i = 0; i < doit; i++) 
		{	
			var col, count = 0;
			do {
				col = Math.floor(Math.random() * 4);
				count++;
				if (count > 5) {
					if (countConnections(x,y) == 1)
						makeLight(x,y,from);
					else
						setTileImage(x,y);				
					return 1;
				}
			} while (tiles[x][y].connections[col] == 1);
				
			var ret;
	
			switch (col) {
				case 0:
					ret = placeLight(x,y-1,(col+2)%4);
					break;
				case 1:
					ret = placeLight(x+1,y,(col+2)%4);
					break;
				case 2:
					ret = placeLight(x,y+1,(col+2)%4);
					break;
				case 3:
					ret = placeLight(x-1,y,(col+2)%4);
					break;
			}
			
			if (ret == 0) //light
			{
				if (countConnections(x,y) == 1)
					makeLight(x,y,from);
				else
					setTileImage(x,y);				
				return 1;
			}
	
			tiles[x][y].connections[col] = 1;
			setTileImage(x,y);	
		}
		return 1;
	}

    </script>

</html>
