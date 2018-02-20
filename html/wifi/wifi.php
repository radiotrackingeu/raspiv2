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

<div class="w3-bar w3-brown w3-mobile">
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'hotspot')">Hotspot</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'wifi_con')">WiFi Connection</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'lan')">Lan Connection</button>
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
			<input type="submit" class="w3-btn w3-brown" value="Create Hotspot" name="start_hotspot" />
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
				$test = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/wifi/:/tmp1/ -v /etc/:/tmp2/ wifi:1.0 sh /tmp1/static_lan.sh ".$_POST["lan_ip"]." ".$_POST["lan_gate"], $ret);
				echo '</pre>';
			}
			if (isset($_POST["change_auto"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/wifi/:/tmp1/ -v /etc/:/tmp2/ wifi:1.0 sh /tmp1/dhcp_lan.sh ".$_POST["lan_ip"]." ".$_POST["lan_gate"], $ret);
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
			// be aware of the wifi version in the shell script!!!
			if (isset($_POST["connect_wifi"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/wifi/:/tmp1/ -v /etc/:/tmp/ wifi:1.0 sh /tmp1/stop_hotspot_set_wifi_ssid.sh ".$_POST["ssid_wifi"]." ".$_POST["pw_wifi"], $ret);
				echo '</pre>';
			}
			if (isset($_POST["start_hotspot"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/wifi/:/tmp1/ -v /etc/:/tmp/ wifi:1.0 sh /tmp1/start_hotspot_stop_wifi.sh");
				echo '</pre>';
			}
	?>

<?php
	//load footer
	require_once RESOURCES_PATH.'/footer.php';
	//load javascripts
	require_once RESOURCES_PATH.'/javascript.php';
	//load php_scripts
	require_once RESOURCES_PATH.'/php_scripts.php';
 ?>
</body>

</html>