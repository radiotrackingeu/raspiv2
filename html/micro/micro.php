<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/weblib/w3.css">
<link rel="stylesheet" href="/weblib/css/font-awesome.min.css">

<body>

<div class="w3-container w3-green w3-mobile">
<h1>radio-tracking.eu</h1>
  <img src="/images/logo_rteu.png" alt="radio-tracking.eu" style="width:40vmin"><br>
 <button class="w3-button w3-green w3-round-xxlarge w3-hover-red w3-xlarge" onclick="w3_switch('sidebar')"><i class="fa fa-bars" aria-hidden="true"> Menu</i></button>
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

<!-- Enter text here-->
<div class="w3-panel w3-green w3-round">
	<br>
	Start the software, if it isn't already running (just try by clicking on the link).
	<br><br>

	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="submit" class="w3-btn w3-brown" value="Start Audio Record" name="start_record_mic">

			<br><br>
	</form>
</div>
	
	<p>
	<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			
			if (isset($_POST["start_record_mic"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged -v /var/www/html/micro/:/tmp/ microphone AUDIODEV=hw:1 rec -c1 -r 22000 /tmp/record.wav sinc 400 silence 1 0.1 1% trim 0 5 2>&1", $ret);
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