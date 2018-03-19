<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/resources/weblib/w3.css">
<link rel="stylesheet" href="/resources/weblib/css/font-awesome.min.css">
<link rel="stylesheet" href="/resources/additional.css">

<body>

<?php
	//load config
	require_once '../cfg/baseConfig.php';
	//load top menu
	require_once RESOURCES_PATH.'/header.php';
 ?>
 
<!-- Enter text here-->
<div class="w3-container">
<div class="w3-panel w3-green w3-round w3-padding">
	If you haven't done so yet, install the app:
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="submit" class="w3-btn w3-brown" value="Install Player" name="install_playback"/>
	</form>

</div>
<div class="w3-panel w3-green w3-round w3-padding">
	Select file to play:<br>
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<select class="w3-mobile"  name="playback_file">
	<?php foreach(glob("files/*.wav") as $filename) {
		echo "<option value='".basename($filename)."'>".basename($filename)."</option>";
	}?>
	</select>
	<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Start Playback" name="start_playback">
			<input type="submit" class="w3-btn w3-brown" value="Stop Playback" name="stop_playback">
	</form>
</div>
</div>
	
	<p>
	<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			
			if (isset($_POST["start_playback"])){
				echo '<pre>';
				system("sudo docker run -td --restart=always --name=playback --privileged -v /var/www/html/playback/files/:/tmp/ -e \"file=/tmp/".$_POST['playback_file']."\" playback:1.0 2>&1 > /dev/null", $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["stop_playback"])){
				echo '<pre>';
				system("sudo docker stop \\$(sudo docker ps -a -q --filter name=playback) 2>&1 > /dev/null", $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["install_playback"])){
				echo '<pre>';
				$test = system('sudo docker build -t playback:1.0 /home/pi/gitrep/raspiv2/Docker/playback/. 2>&1', $ret);
				echo '</pre>';
			}

	?>
	</p>
<!-- Enter text here-->

<div id="container"></div>

<div class="w3-container w3-center w3-brown">
  Online-Website: <a href="https://radio-tracking.eu/">radio-tracking.eu</a>
  Email: <a href= "mailto:info@radio-tracking.eu">info@radio-tracking.eu</a>
</div>


<script>
function openCity(cityName) {
    var i;
    var x = document.getElementsByClassName("city");
    for (i = 0; i < x.length; i++) {
       x[i].style.display = "none";  
    }
    document.getElementById(cityName).style.display = "block";  
}
function w3_switch(name) {
	var x = document.getElementById(name);
    if (x.style.display == "none") {
        x.style.display = "block";
    } else { 
        x.style.display = "none";
    }
}
</script>


</body>

</html>