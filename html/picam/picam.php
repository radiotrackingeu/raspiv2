<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/weblib/w3.css">
<link rel="stylesheet" href="/weblib/css/font-awesome.min.css">

<body>

<div class="w3-container w3-green">
<h1>radio-tracking.eu</h1>

  <img src="/images/logo_rteu.png" alt="Nice car" style="width:20%">
 <br><br>
 
</div>


<nav class="w3-sidenav w3-bar-block w3-light-grey w3-card-2" style="width:25%">
	<h4> <b> Options to choose:</b></h4>
	<a class="w3-green w3-bar-item w3-button" href="/index.html"><i class="fa fa-home"></i> Home</a>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('radio')">
		<i class="fa fa-podcast"></i> Radio <i class="fa fa-caret-down"></i></div>
		<div id="radio" class="w3-hide w3-white w3-card-4">
			<a href="/sdr/rtl_fm.php">WebRadio</a>
			<a href="/sdr/rtl_fftw.php">Recorder</a>
			<a href="/sdr/rtl_tcp.php">SDR#-Server</a>
			<a href="/sdr/websdr.php">WebRX</a>
		</div>
	</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('camera')">
		<i class="fa fa-camera"></i> Camera <i class="fa fa-caret-down"></i></div>
		<div id="camera" class="w3-hide w3-white w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">Setup</a>
		</div>
	</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('mic')">
		<i class="fa fa-microphone"></i> Micro <i class="fa fa-caret-down"></i></div>
		<div id="mic" class="w3-hide w3-white w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">Setup</a>
		</div>
	</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('gps')">
		<i class="fa fa-compass"></i> GPS <i class="fa fa-caret-down"></i></div>
		<div id="gps" class="w3-hide w3-white w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">Setup</a>
		</div>
	</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('wifi')">
		<i class="fa fa-wifi"></i> WiFi <i class="fa fa-caret-down"></i></div>
		<div id="wifi" class="w3-hide w3-white w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">Setup</a>
		</div>
	</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('remote')">
		<i class="fa fa-exchange"></i> Remote <i class="fa fa-caret-down"></i></div>
		<div id="remote" class="w3-hide w3-white w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">UMTS Setup</a>
			<a href="/connect/vpn_setup.php">VPN Setup</a>
		</div>
	</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('system')">
		<i class="fa fa-wrench"></i> System <i class="fa fa-caret-down"></i></div>
		<div id="system" class="w3-hide w3-white w3-card-4">
			<a href="/git/gitlab.php">Software</a>
			<a href="/git/system.php">System</a>
			<a href="/connect/umts_setup.php">Documentation</a>
		</div>
	</div>
	<a href="/licence.php"><i class="fa fa-registered"></i> License</a>
</nav>

<div style="margin-left:25%">

<div class="w3-bar w3-black">
  <button class="w3-bar-item w3-button" onclick="openCity('activate')">Activate</button>
  <button class="w3-bar-item w3-button" onclick="openCity('introduction')">Introduction</button>
</div>

<div id="activate" class="w3-container city" style="display:none">

<!-- Enter text here-->
<br>
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="submit" class="w3-btn" value="Activate PiCam" name="activate">
			<input type="submit" class="w3-btn" value="Deactivate PiCam" name="deactivate">
			<input type="submit" class="w3-btn" value="Start PiCam" name="run_motion">
			<input type="submit" class="w3-btn" value="Stop PiCam" name="stop_motion">

			<br><br>
			<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+2)?>"> Link to Camera Server</a>
	</form>				
</div>
<br>

<!-- <iframe src="http://192.168.8.103:82/picture/1/frame/" height="300" width="432" style="border:none; ></iframe> -->


<div id="ouput" class="w3-container" style="display:block">
	<p>
	<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			
			system("grep 'gpu_mem=' /boot/config.txt ", $gpu);		
			system("grep 'start_x=' /boot/config.txt ", $cam);
			
			if (isset($_POST["activate"])){
				echo '<pre>';
				$test = system("sudo docker run --rm -t --privileged -v /boot/:/boot/ git sed -i 's/start_x=0/start_x=1/g' /boot/config.txt 2>&1", $ret);
				$test = system("sudo docker run --rm -t --privileged -v /boot/:/boot/ git sed -i 's/gpu_mem=16/gpu_mem=256/g' /boot/config.txt 2>&1", $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["deactivate"])){
				echo '<pre>';
				$test = system("sudo docker run --rm -t --privileged -v /boot/:/boot/ git sed -i 's/start_x=1/start_x=0/g' /boot/config.txt 2>&1", $ret);
				$test = system("sudo docker run --rm -t --privileged -v /boot/:/boot/ git sed -i 's/gpu_mem=256/gpu_mem=16/g' /boot/config.txt 2>&1", $ret);
				echo '</pre>';
			}
			
			
			if (isset($_POST["run_motion"])){
				$test = system("sudo docker run --rm -t -p ".($_SERVER['SERVER_PORT']+2).":8765 -v /var/www/html/picam/record/:/var/lib/motioneye/ -v /var/www/html/picam/config/:/etc/motioneye/ --device=/dev/video0 vividboarder/rpi-motioneye", $ret);
			}
			if (isset($_POST["stop_motion"])){
				echo '<pre>';
				$test = system('sudo docker stop $(sudo docker ps -a -q --filter ancestor=picam) 2>&1', $ret);
				echo '</pre>';
			}

	?>
	</p>
</div>
<!-- Enter text here-->


</div>

<script>
function myAccFunc(element_id) {
    var x = document.getElementById(element_id);
    if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
        x.previousElementSibling.className += " w3-green";
    } else { 
        x.className = x.className.replace(" w3-show", "");
        x.previousElementSibling.className = 
        x.previousElementSibling.className.replace(" w3-green", "");
    }
}

function myDropFunc(element_id) {
    var x = document.getElementById(element_id);
    if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
        x.previousElementSibling.className += " w3-green";
    } else { 
        x.className = x.className.replace(" w3-show", "");
        x.previousElementSibling.className = 
        x.previousElementSibling.className.replace(" w3-green", "");
    }
}
function openCity(cityName) {
    var i;
    var x = document.getElementsByClassName("city");
    for (i = 0; i < x.length; i++) {
       x[i].style.display = "none";  
    }
    document.getElementById(cityName).style.display = "block";  
}
</script>


</body>

</html>