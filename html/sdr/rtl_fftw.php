<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/weblib/w3.css">
<link rel="stylesheet" href="/weblib/css/font-awesome.min.css">

<body>

<div class="w3-container w3-green">
<h1>radio-tracking.eu</h1>

  <img src="/images/logo_rteu.png" alt="radio-tracking.eu" style="width:20%">
 <br><br>
 
</div>
 

<nav class="w3-sidenav w3-bar-block w3-light-grey w3-card-2" style="width:15%">
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

	<div class="w3-bar-item w3-button" onclick="myAccFunc('camera')">
		<i class="fa fa-camera"></i> Camera <i class="fa fa-caret-down"></i></div>
		<div id="camera" class="w3-hide w3-white w3-card-4">
			<a href="/picam/picam.php">Start</a>
			<a href="/connect/umts_setup.php">Setup</a>
		</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('mic')">
		<i class="fa fa-microphone"></i> Micro <i class="fa fa-caret-down"></i></div>
		<div id="mic" class="w3-hide w3-white w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">Setup</a>
		</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('gps')">
		<i class="fa fa-compass"></i> GPS <i class="fa fa-caret-down"></i></div>
		<div id="gps" class="w3-hide w3-white w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">Setup</a>
		</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('wifi')">
		<i class="fa fa-database "></i> Data <i class="fa fa-caret-down"></i></div>
		<div id="wifi" class="w3-hide w3-white w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">Setup</a>
		</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('wifi')">
		<i class="fa fa-wifi"></i> WiFi <i class="fa fa-caret-down"></i></div>
		<div id="wifi" class="w3-hide w3-white w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">Setup</a>
		</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('remote')">
		<i class="fa fa-exchange"></i> Remote <i class="fa fa-caret-down"></i></div>
		<div id="remote" class="w3-hide w3-white w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">UMTS Setup</a>
			<a href="/connect/vpn_setup.php">VPN Setup</a>
		</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('system')">
		<i class="fa fa-wrench"></i> System <i class="fa fa-caret-down"></i></div>
		<div id="system" class="w3-hide w3-white w3-card-4">
			<a href="/git/gitlab.php">Software</a>
			<a href="/git/system.php">System</a>
			<a href="/connect/umts_setup.php">Documentation</a>
		</div>
	<a href="/license.html"><i class="fa fa-registered"></i> License</a>
</nav>

<div style="margin-left:15%">

<!-- Enter text here-->



<br>
<br>To record a Frequency Spektrum for a given time, just modify the entries below and press Start.

<h3>Record properties</h3><br>

<form method='POST' enctype="multipart/form-data"> 
<table style="width:90%">

	<tr>
		<td>Center Frequency:</td>
		<td><input type="text" name="cfreq" value="150190k"></td>
		<td>Frequency in the middle of the frequency range of 250 kHz</td>
	</tr>
	</tr>
	<tr>
		<td>Gain in mDB:</td>
		<td><input type="text" name="gain" value="400"></td>
		<td>Gain of the recording device. Higher gain results in more noise.</td
	</tr>
	<tr>
		<td>Record Time:</td>
		<td><input type="text" name="rtime" value="1m"></td>
		<td>The is the actual overall recording time. You can use units like m for minutes and h for hours.</td>
	</tr>
	<tr>
		<td>Record Name:</td>
		<td><input type="text" name="rname" value="d0"></td>
		<td>Each record will be given a file name, be careful, the same name will overwrite existing files. You can find the results here: <a href="/barbastella/sdr/record/">Record Folder</a></td>
	</tr>
</table>

<br>
<br>
Start or stop recording/s: 
<br>
<br>


	<input type="submit" value="Start" name="fftw_start" />
	<input type="submit" value="Stop" name="fftw_stopt" />
</form> 
<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	if (isset($_POST["fftw_start"])){
		$cmd = "rtl_power_fftw -r 250000 -f " . $_POST["cfreq"]. " -b 300 -t 0.1 -g " . $_POST["gain"]. " -q -d 0 -e " . $_POST["rtime"]. " -m /home/" . $_POST["rname"];
		echo '<pre>';
		$test = system("sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtlsdr ".$cmd." 2>&1", $ret);
		echo '</pre>';
		echo "sudo docker run -t -v /var/www/html/sdr/record/:/home/ rtlsdr ".$cmd;
	}
	if (isset($_POST["fftw_stopt"])){
		echo '<pre>';
		$result = system("sudo docker stop $(sudo docker ps -a -q --filter ancestor=rtlsdr) 2>&1", $ret);
		echo '</pre>';
	}
?>

<!-- Enter text here-->


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