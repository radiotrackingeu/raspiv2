<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/weblib/w3.css">
<link rel="stylesheet" href="/weblib/css/font-awesome.min.css">

<body>

<div class="w3-container w3-green">
<h1>radio-tracking.eu</h1>
  <img src="/images/logo_rteu.png" alt="radio-tracking.eu" style="width:25%"><br>
 <button class="w3-button w3-green w3-xlarge" onclick="w3_switch('sidebar')"><i class="fa fa-bars" aria-hidden="true"> Menu</i></button>
</div>
 

<div class="w3-bar w3-light-grey" style="display:none" id="sidebar">
	<!-- Home -->
	<a class="w3-bar-item w3-button w3-mobile" href="/index.html"><i class="fa fa-home"></i> Home</a>
	
	<!-- Radio -->
	<div class="w3-dropdown-hover w3-mobile">
		<button class="w3-button" onclick="dropd('radio')">
			<i class="fa fa-podcast"></i> Radio <i class="fa fa-caret-down"></i>
		</button>
		<div id="radio" class="w3-dropdown-content w3-card-4">
			<a href="/sdr/rtl_fm.php">WebRadio</a>
			<a href="/sdr/rtl_fftw.php">Recorder</a>
			<a href="/sdr/rtl_tcp.php">SDR#-Server</a>
			<a href="/sdr/websdr.php">WebRX</a>
		</div>
	</div>

	<!-- Camera -->
	<div class="w3-dropdown-hover w3-mobile">
		<button class="w3-button" onclick="dropd('camera')">
			<i class="fa fa-camera"></i> Camera <i class="fa fa-caret-down"></i>
		</button>
		<div id="camera" class="w3-dropdown-content w3-card-4">
			<a href="/picam/picam.php">Start</a>
			<a href="/picam/setup_picam.php">Setup</a>
		</div>
	</div>

	<!-- Microphone -->
	<div class="w3-dropdown-hover w3-mobile">
		<button class="w3-button" onclick="dropd('mic')">
			<i class="fa fa-microphone"></i> Microphone <i class="fa fa-caret-down"></i>
		</button>
		<div id="mic" class="w3-dropdown-content w3-card-4">
			<a href="/micro/micro.php">Start</a>
			<a href="/micro/micro_setup.php">Setup</a>
		</div>
	</div>
	
	<!-- GPS -->
	<div class="w3-dropdown-hover w3-mobile">
		<button class="w3-button" onclick="dropd('gps')">
			<i class="fa fa-compass"></i> GPS <i class="fa fa-caret-down"></i>
		</button>
		<div id="gps" class="w3-dropdown-content w3-card-4">
			<a href="/gps/gps.php">Start</a>
			<a href="/gps/gps_setup.php">Setup</a>
		</div>
	</div>
		
	
	<!-- Data storage -->
	<div class="w3-dropdown-hover w3-mobile">
		<button class="w3-button" onclick="dropd('data')">
			<i class="fa fa-database "></i> Data <i class="fa fa-caret-down"></i>
		</button>
		<div id="data" class="w3-dropdown-content w3-card-4">
			<a href="/data/data.php">Start</a>
			<a href="/data/data_setup.php">Setup</a>
		</div>
	</div>
	
	<!-- WiFi -->
	<div class="w3-dropdown-hover w3-mobile">
		<button class="w3-button" onclick="dropd('wifi')">
			<i class="fa fa-wifi"></i> WiFi <i class="fa fa-caret-down"></i>
		</button>
		<div id="wifi" class="w3-dropdown-content w3-card-4">
			<a href="/wifi/wifi.php">Start</a>
			<a href="/wifi/wifi_setup.php">Setup</a>
		</div>
	</div>
		
	<!-- Remote controll -->
	<div class="w3-dropdown-hover w3-mobile">
		<button class="w3-button" onclick="dropd('remote')">
			<i class="fa fa-exchange"></i> Remote <i class="fa fa-caret-down"></i>
		</button>
		<div id="remote" class="w3-dropdown-content w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">UMTS Setup</a>
			<a href="/connect/vpn_setup.php">VPN Setup</a>
		</div>
	</div>
	
	<!-- System settings -->
	<div class="w3-dropdown-hover w3-mobile">
		<button class="w3-button" onclick="dropd('system')">
			<i class="fa fa-wrench"></i> System <i class="fa fa-caret-down"></i>
		</button>
		<div id="system" class="w3-dropdown-content w3-card-4">
			<a href="/git/gitlab.php">Software</a>
			<a href="/git/system.php">System</a>
			<a href="/git/git_setup.php">Documentation</a>
		</div>
	</div>
	
	<!-- License -->
	<a class="w3-bar-item w3-button w3-mobile" href="/license.html"><i class="fa fa-registered"></i> License</a>
</div>

<div class="w3-bar w3-brown">
  <button class="w3-bar-item w3-button" onclick="openCity('activate')">Start</button>
  <button class="w3-bar-item w3-button" onclick="openCity('introduction')">Activate</button>
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