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
	require_once './cfg/baseConfig.php';
	//load top menu
	require_once RESOURCES_PATH.'/header.php';
 ?>
<!-- Enter text here-->

<div class="w3-bar w3-brown w3-mobile">
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'features')">Features</button>
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'pdf_manuals')">PDF Manuals</button>
</div>

<div id="features" class="w3-container city">
	<div class="w3-panel w3-green w3-round">
		<h2>Version 3.0 aka Nyctalus</h2>
		<p>
		<h4>Radio signal logging</h4>
		->   on a 250 kHz/1 MHz frequency range or on single frequency (higher detection range)<br>
		->   Output is saved as .csv file or in a MySQL Database<br>
		->   Up to two receivers are supported<br>
		<h4>Browser-Spectrum-Viewer</h4>
		->   Visual feedback of the spectrum<br>
		->   Demodulation<br>
		->   To check for noise sources<br>
		<h4>SDR# support</h4>
		->   Monitor a Frequency Range of up to 2MHz<br>
		->   Handy
		<h4>Remote Access</h4>
		->   Using 2G/3G/4G Hotspots<br>
		->   VPN-Certificate is required (paid feature)<br>
		<h4>WiFi</h4>
		->   Can create own hotspot for access<br>
		->   Login to an external Hotspot<br>
		<h4>Camera</h4>
		->   With motion detection and IR-Lights switch<br>
		<h4>Software Handling</h4>
		->   Global Setup-File to easily duplicate settings<br>
		->   Update via WiFi or LAN - stable and development version<br>
		<br><br>
		List of features still in development:<br><br>
		- Automated Microphone Recordings<br>
		- GPS Logging<br>
		-> get GPS from Cell Phone<br>
		</p>
	</div>
</div>

<div id="pdf_manuals" class="w3-container city">
	<div class="w3-panel w3-green w3-round">
	
	<br><a target="_blank" href="/instructions/radiotrackingeu_basic_setup.pdf"><h4>Basic Setup Instructions</h4></a><br>
	
	to be continued...  <br><br>
	</div>
</div>
<!-- Enter text here-->

<?php
	//load footer
	require_once RESOURCES_PATH.'/footer.php';
	//load javascripts
	require_once RESOURCES_PATH.'/javascript.php';
 ?>
 
</body>
</html>