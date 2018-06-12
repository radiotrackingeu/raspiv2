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
    //load functions
	require_once RESOURCES_PATH.'/helpers.php';
    //load ConfigLite
	require_once CONFIGLITE_PATH.'/Lite.php';
	
    define ('confSection', 'network');
    define ('confKeys', array('ssid_hotspot', 'pw_hotspot'));
    $config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
 ?>
<!-- Enter text here-->

<div class="w3-bar w3-brown w3-mobile">
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'hotspot')">Hotspot</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'wifi_con')">WiFi Connection</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'lan')">Lan Connection</button>
</div>

<div id="hotspot" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		After you have modified anything - please reboot.
		<form method='POST' enctype="multipart/form-data" action="<?php update_Config($config);?>">
			<br>
			Hotspot-Name: <br>
			<input type="text" name="ssid_hotspot" value="<?php echo isset($config['network']['ssid_hotspot']) ? $config['network']['ssid_hotspot'] : "rteuv3" ?>">
			<br><br>
			Password: (8-63 characters)<br>
			<input type="password" name="pw_hotspot" value="<?php echo isset($config['network']['pw_hotspot']) ? $config['network']['pw_hotspot'] : "sdrtracking" ?>">
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
			<input type="password" name="pw_wifi" value="******">
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
	//load footer
	require_once RESOURCES_PATH.'/footer.php';
	//load javascripts
	require_once RESOURCES_PATH.'/javascript.php';
	//load php_scripts
	require_once RESOURCES_PATH.'/php_scripts.php';
 ?>
</body>

</html>