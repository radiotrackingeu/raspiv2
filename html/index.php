<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/resources/weblib/w3.css">
<link rel="stylesheet" href="/resources/weblib/css/font-awesome.min.css">

<body>
<?php
	//load config
	require_once './cfg/baseConfig.php';
	//load top menu
	require_once RESOURCES_PATH.'/header.php';
 ?>
<!-- Enter text here-->

<div class="w3-bar w3-brown w3-mobile">
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('welcome')">Welcome</button>
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('introduction')">Introduction</button>
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('instructions')">Setup Instructions</button>
</div>

<div id="welcome" class="w3-container city">

<div class="w3-panel w3-green w3-round">

<h4>HTML Version 2.4 aka plecotus</h4>

<p>
List of features:
<br><br>
- Signal logging within 250 kHz Frequency Range (detection range is still limited)<br>
- Single Frequency Radio for VHF tags using the browser and local audio output<br>
- wav or mp3 recording on several frequencies<br>
- SDR# support to monitor a frequency range of 2MHz<br>
- WebRX to monitor live a specturm using the browser <br>
- Remote Access using 2G/3G/4G (certificate required)<br>
- Camera with motion detection fully supported<br>
- Automated Microphone Recordings<br>
- GPS Logging<br>
- Data Management<br>
- WiFi Hotspot or login to specific WiFi<br>
- Time triggered job management<br>
- Global Setup-File to easily duplicate settings<br>

</p>
</div>
</div>
<div id="introduction" class="w3-container city" style="display:none">

<div class="w3-panel w3-green w3-round">
<p>
If you managed to get this page running on a Rasberry-Pi: congratulations!
<br><br>
The aim of the whole project is to locate the transmitters and show them on a map. The software is still in development.
In the following boxes you will find an overview of the supported featuers. For more details please visit the pages.<br>
</p>
</div>

<div class="w3-panel w3-green w3-round">

<h3>Radio</h3>
To receive the signal of the transmitters, you got the following options.<br>

<b>WebRadio:</b> Listen to a single, preset frequency<br>
<b>Recorder:</b> Records the frequency-time-signal spectrum<br>
<b>SDR#-Server:</b> Sets up a server which can be used with SDR# to monitor live the frequency-time-signal spectrum<br>
<b>WebRX:</b> To monitor live the frequency-time-signal spectrum within a browser application<br>
</div>

<div class="w3-panel w3-green w3-round">
<h3>Camera</h3>
Use it to setup and run a camera.<br>

<b>Start:</b> Activate and start the camera<br>
<b>Setup:</b> No setup options yet<br>
</div>

<div class="w3-panel w3-green w3-round">
<h3>Microphone</h3>
Will be supported soon.<br>
</div>

<div class="w3-panel w3-green w3-round">
<h3>GPS</h3>
Will be supported soon.<br>
</div>

<div class="w3-panel w3-green w3-round">
<h3>Data</h3>
Use it to manage the storage of the data.<br>
<b>Start:</b> Just a bunch of links right now<br>
<b>Setup:</b> No setup options yet<br>
</div>

<div class="w3-panel w3-green w3-round">
<h3>Wifi</h3>
Will be supported soon.<br>
</div>

<div class="w3-panel w3-green w3-round">
<h3>Remote</h3>
Use it to connect the system to a mobile network, so you can access it via the world wide web.<br>

<b>Start:</b> Read the introdution on the page<br>
<b>UMTS-Setup:</b> Mobile Network Modifications <br>
<b>VPN-Setup:</b> VPN-Tunnel Modifications<br>
</div>

<div class="w3-panel w3-green w3-round">
<h3>System</h3>
<b>Software:</b> Update and Debug options<br>
<b>System:</b> Nothing here yet<br>
<b>Documentation:</b> Nope<br>
</div>
</div>
<!-- Enter text here-->

<?php
	//load footer
	require_once RESOURCES_PATH.'/footer.php';
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