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
	//define config section and items.
	define ('confSection', 'remote');
	define ('confKeys', array('time_start_vpn'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');	
 ?>
<!-- Enter text here-->


<div class="w3-bar w3-brown">
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'VPN')">Tunnel</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'VPN_Setup')">Tunnel Setup</button>
</div>

<div id="VPN" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round-xlarge">
		<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<p>Start a tunnel so the Raspberry pi can be oprated via www. A licence is needed.</p> 
			<input type="submit" class="w3-btn w3-brown" value="Start VPN" name="start_vpn">
			<input type="submit" class="w3-btn w3-brown" value="Stop VPN" name="stop_vpn"><br><br>
			
			Choose whether VPN Tunnel shall be started upon boot. 
			<br>
			<input type="radio" name="time_start_vpn" value="start_no" <?php echo isset($config['remote']['time_start_vpn']) && $config['remote']['time_start_vpn'] == "start_no" ? "checked" : "" ?>> No start<br>
			<input type="radio" name="time_start_vpn" value="reboot" <?php echo isset($config['remote']['time_start_vpn']) && $config['remote']['time_start_vpn'] == "reboot" ? "checked" : "" ?>> Start at Boot<br>			
			<input type="submit" class="w3-btn w3-brown" value="Change Settings" name="change_VPN_cron"/>
			<br><br>
		</form>
	</div>
</div>

<div id="VPN_Setup" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round-xlarge">
		<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<p>VPN Certificate.</p>
			<input type="file" name="fileToUpload" id="fileToUpload">
			<input type="submit" class="w3-btn w3-brown" value="Upload Certificate" name="upload_cert">
			<br><br><br>
			<input type="submit" class="w3-btn w3-brown" value="Remove Certificate" name="rm_cert">
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