<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/weblib/w3.css">
<link rel="stylesheet" href="/weblib/css/font-awesome.min.css">

<body>

<div class="w3-container w3-green">
<h1>radio-tracking.eu</h1>
  <img src="/images/logo_rteu.png" alt="radio-tracking.eu" style="width:20%"><br>
  <button class="w3-button w3-green w3-round-xxlarge w3-hover-red w3-xlarge" onclick="w3_switch('sidebar')"><i class="fa fa-bars" aria-hidden="true"> Menu</i></button>
  <i class="fa fa-wifi"></i> WiFi
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

<div class="w3-bar w3-brown w3-mobile">
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('hotspot')">Hotspot</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('wifi_con')">WiFi Connection</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('lan')">Lan Connection</button>
</div>

<div id="hotspot" class="w3-container city">
	<div class="w3-panel w3-green w3-round">
		After you have modified anything - please reboot.
		<form method='POST' enctype="multipart/form-data">
			<br>
			Hotspot-Name: <br>
			<input type="text" name="ssid_hotspot" value="rteuv2">
			<br><br>
			Password: <br>
			<input type="text" name="pw_hotspot" value="sdrtracking">
			<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Connect to WiFi" name="start_hotspot" />
			<input type="submit" class="w3-btn w3-brown" value="Reboot" name="reboot" />
			<br><br>
		</form>
	</div>
</div>

<div id="wifi_con" class="w3-container city" style="display:none">

	<div class="w3-panel w3-green w3-round">
		After you have modified anything - please reboot.
		<form method='POST' enctype="multipart/form-data">
			<br>
			WiFi-Name: <br>
			<input type="text" name="ssid_wifi" value="<?php $out=shell_exec("iwgetid -r"); echo $out; ?>">
			<br><br>
			Password: <br>
			<input type="text" name="pw_wifi" value="******">
			<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Change" name="connect_wifi" />
			<input type="submit" class="w3-btn w3-brown" value="Reboot" name="reboot" />
			<br><br>
		</form>
	</div>

</div>

<div id="lan" class="w3-container city" style="display:none">

	<div class="w3-panel w3-green w3-round">
		After you have modified anything - please reboot.
		<form method='POST' enctype="multipart/form-data">
			<br>
			<input type="submit" class="w3-btn w3-brown" value="Automatic" name="change_auto" />
			<input type="submit" class="w3-btn w3-brown" value="Show Settings" name="ifconfig_all" />
			<br><br>
			IP4-Address: <br>
			<input type="text" name="lan_ip" value="<?php $out=shell_exec("/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'"); echo $out; ?>">
			<br><br>
			Gateway: <br>
			<input type="text" name="lan_gate" value="<?php echo $out=shell_exec("ip route show default | grep default | grep eth0 | awk {'print $3'}");?>">
			<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Change to static" name="change_lan" />
			<input type="submit" class="w3-btn w3-brown" value="Reboot" name="reboot" />
			<br><br>
		</form>
	</div>

</div>
<!-- Enter text here-->

<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);

			
			if (isset($_POST["change_lan"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/wifi/:/tmp1/ -v /etc/:/tmp2/ wifi sh /tmp1/static_lan.sh ".$_POST["lan_ip"]." ".$_POST["lan_gate"], $ret);
				echo '</pre>';
			}
			if (isset($_POST["change_auto"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/wifi/:/tmp1/ -v /etc/:/tmp2/ wifi sh /tmp1/dhcp_lan.sh ".$_POST["lan_ip"]." ".$_POST["lan_gate"], $ret);
				echo '</pre>';
			}
			if (isset($_POST["reboot"])){
				echo '<pre>';
				$test = system('sudo reboot', $ret);
				echo '</pre>';
			}
			if (isset($_POST["ifconfig_all"])){
				echo '<pre>';
				$test = system('ifconfig -a', $ret);
				echo '</pre>';
			}
			if (isset($_POST["connect_wifi"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/wifi/:/tmp1/ -v /etc/:/tmp/ wifi sh /tmp1/stop_hotspot_set_wifi_ssid.sh ".$_POST["ssid_wifi"]." ".$_POST["pw_wifi"], $ret);
				echo '</pre>';
			}
			if (isset($_POST["start_hotspot"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/wifi/:/tmp1/ -v /etc/:/tmp/ wifi sh /tmp1/start_hotspot_stop_wifi.sh");
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