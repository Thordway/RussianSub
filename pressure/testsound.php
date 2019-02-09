<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>testsound</title>
</head>

<body onload='setupPage()'>
	<?=$_REQUEST['soundType']?>
 <!--   <object type="audio/<?=$_REQUEST['soundType']?>" data="klaxon.<?=$_REQUEST['soundType']?>" height="0" width="0">
          <param name="autoplay" value="true">
    </object>-->
    <div id='mydiv'></div>
    <audio controls autoplay>
      <source src="klaxon.<?=$_REQUEST['soundType']?>" type="audio/<?=$_REQUEST['soundType']?>">
     Your browser does not support the audio element.
    </audio>
</body>

<script language="javascript">
function playHTML5(sound, soundcv){
                // sound = url to m4a audio file
                // soundcv = div in which the audioplayer should go

  var audio = document.createElement('audio');
  audio.src = sound;
  audio.controls = "controls";
  if (currentSound != null){
   soundcv.replaceChild(audio,currentSound);
  } else {
   soundcv.appendChild(audio);
  }
  currentSound = audio;
 }
 
 function setupPage() {
	var divsec = document.getElementById('mydiv');
	playHTML5('klaxon.ogg', divsec);
 }
 
 </script>
</html>