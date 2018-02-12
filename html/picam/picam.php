<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/resources/weblib/w3.css">
<link rel="stylesheet" href="/resources/weblib/css/font-awesome.min.css">

<body>

<?php
	//load config
	require_once '../cfg/baseConfig.php';
	//load top menu
	require_once RESOURCES_PATH.'/header.php';
 ?>
 
<div class="w3-bar w3-brown">
  <button class="w3-bar-item w3-button" onclick="openCity('output')">Links</button>
  <button class="w3-bar-item w3-button" onclick="openCity('start_cam')">Start</button>
  <button class="w3-bar-item w3-button" onclick="openCity('activate_light')">Light</button>
  <button class="w3-bar-item w3-button" onclick="openCity('activate_cam')">Activate</button>
</div>

<div id="start_cam" class="w3-container city" style="display:none">

<!-- Enter text here-->
<div class="w3-panel w3-green w3-round">
	<br>
	Start the software, if it isn't already running (just try by clicking on the link).
	<br><br>

	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="submit" class="w3-btn w3-brown" value="Start PiCam" name="run_motion_detection">
			<input type="submit" class="w3-btn w3-brown" value="Stop PiCam" name="stop_motion_detection">

			<br><br>
	</form>
	</div>
</div>

<div id="activate_cam" class="w3-container city" style="display:none">

	
	<div class="w3-panel w3-green w3-round">
	<br>
	A reboot is required after the activation. <br><br>
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="submit" class="w3-btn w3-brown" value="Activate PiCam" name="activate">
			<input type="submit" class="w3-btn w3-brown" value="Deactivate PiCam" name="deactivate">
			<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Activate I2C" name="activate_i2c">
			<input type="submit" class="w3-btn w3-brown" value="Deactivate I2C" name="deactivate_i2c">
			<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Reboot" name="reboot">
			
			<br><br>
	</form>	
	</div>	
</div>

<div id="activate_light" class="w3-container city" style="display:none">

	
	<div class="w3-panel w3-green w3-round">
	<br>
	A reboot is required after the activation. <br><br>
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="submit" class="w3-btn w3-brown" value="IR-Light on" name="activate_light">
			<input type="submit" class="w3-btn w3-brown" value="IR-Light off" name="deactivate_light">
			<br><br>
	</form>	
	</div>	
</div>




<!-- <iframe src="http://192.168.8.103:82/picture/1/frame/" height="300" width="432" style="border:none; ></iframe> -->


<div id="output" class="w3-container city" style="display:block">
	<div class="w3-panel w3-green w3-round">
	<br>
	<!--If you are using the raspberry pi cam, please first activate it. This will reserve more memory for the GPU. If the server has been already started, just click on the link: <br><br>
	<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+2)?>"> Link to Camera Server</a><br><br>-->
	The camera is configured to start recording if motion is detected and stop as soon as there is no more motion.<br>
	It also offers a simple configuration menu, where one can configure noise level and detection threshold among other things.<br>
	The required credentials are the same as for this site.<br><br>
	<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+4)?>"> Link to Camera Stream</a> <br><br>
	<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+3)?>"> Link to Camera Configuration</a> <br><br>
	To download the images of video files either use the software or the following directory:<br><br>
	<a target="_blank" href="/picam/record/"> Images/Videos</a><br>
	
	<br>
	
	</div>
	
	
	<p>
	<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			
			#system("grep 'gpu_mem=' /boot/config.txt ", $gpu);		
			#system("grep 'start_x=' /boot/config.txt ", $cam);
			
			if (isset($_POST["activate"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged -v /boot/:/tmp1/ -v /etc/:/tmp2/ -v /var/www/html/picam/:/tmp3/ i2c sh /tmp3/start_picam.sh 2>&1", $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["deactivate"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged -v /boot/:/tmp1/ -v /etc/:/tmp2/ -v /var/www/html/picam/:/tmp3/ i2c sh /tmp3/stop_picam.sh 2>&1", $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["activate_i2c"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged -v /boot/:/tmp1/ -v /etc/:/tmp2/ -v /var/www/html/picam/:/tmp3/ i2c sh /tmp3/start_i2c.sh 2>&1", $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["deactivate_i2c"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged -v /boot/:/tmp1/ -v /etc/:/tmp2/ -v /var/www/html/picam/:/tmp3/ i2c sh /tmp3/stop_i2c.sh 2>&1", $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["activate_light"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged -v /var/www/html/picam/:/tmp/ i2c sh /tmp/start_all_lights.sh 2>&1", $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["deactivate_light"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged i2c i2cset -y 1 0x70 0x00 0x00 2>&1", $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["reboot"])){
				echo '<pre>';
				$test = system('sudo reboot', $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["run_motion_detection"])){
				$test = system("sudo docker run --rm --privileged --name=motion_detection  -t -p ".($_SERVER['SERVER_PORT']+3).":8080 -p ".($_SERVER['SERVER_PORT']+4).":8081 -v /var/www/html/picam/record/:/var/lib/motion/ motion_detection:1.0", $ret);
			}
			if (isset($_POST["stop_motion_detection"])){
				echo '<pre>';
				$test = system('sudo docker stop motion_detection 2>&1', $ret);
				echo '</pre>';
			}			
			
			if (isset($_POST["run_motion"])){
				$test = system("sudo docker run --rm  --name camera -t -p ".($_SERVER['SERVER_PORT']+2).":8765 -v /var/www/html/picam/record/:/var/lib/motioneye/ -v /var/www/html/picam/config/:/etc/motioneye/ --privileged picam", $ret);
			}
			if (isset($_POST["stop_motion"])){
				echo '<pre>';
				$test = system('sudo docker stop camera 2>&1', $ret);
				echo '</pre>';
			}

	?>
	</p>
</div>
<!-- Enter text here-->


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