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

<div class="w3-bar w3-brown w3-mobile">
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('camera1')">Camera</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('radio1')">Radio</button>
</div>

<div id="camera1" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br>

		<h3>Zip Camera's record folder</h3><br>
		<form method='POST' enctype="multipart/form-data">
			<input type="text" name="zip_name" value="<?php echo date('Y_m_d_H_i')?>">
			<input type="submit" class="w3-btn w3-brown" value="Zip All Camera Recordings" name="zip_camera" /> <br><br>
			You can find the zipped files here: <a href="/picam/zipped/">Record Folder</a> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Delete all recordings" name="rm_record_folder" />
			<input type="submit" class="w3-btn w3-brown" value="Delete all zipped files" name="rm_zip_folder" /><br><br>

		</form>
		<br>
	</div>
</div>
<div id="radio1" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br>
		To record a Frequency Spektrum for a given time, just modify the entries below and press Start.
		<h3>Record properties</h3><br>
		<form method='POST' enctype="multipart/form-data">
			<table style="width:90%">
				<tr>
					<td>Gain in DB:</td>
					<td><input type="text" name="log_gain" value="20"></td>
					<td>Gain of the recording device. Higher gain results in more noise.</td>
				</tr>
				<tr>
					<td>Zip Name:</td>
					<td><input type="text" name="zipasd_name" value="<?php echo date('Y_m_d_H_i')?>"></td>
					<td>You can find the results here: <a href="/picam/zipped/">Record Folder</a></td>
				</tr>
			</table>
			<input type="submit" class="w3-btn w3-brown" value="Start" name="log_start" />
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="log_stop" />
		</form>
		<br>
	</div>
</div>


<!-- Enter text here-->

	<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			
			
			if (isset($_POST["zip_camera"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged -v /var/www/html/picam/:/tmp/ git zip -r /tmp/zipped/".$_POST["zip_name"]." /tmp/record/ 2>&1", $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["rm_zip_folder"])){
				echo '<pre>';
				$test = system("rm -rf /var/www/html/picam/zipped/* 2>&1", $ret);
				echo '</pre>';
			}
			if (isset($_POST["rm_record_folder"])){
				echo '<pre>';
				$test = system("rm -rf /var/www/html/picam/record/* 2>&1", $ret);
				echo '</pre>';
			}

	?>


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