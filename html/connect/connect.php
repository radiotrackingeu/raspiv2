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
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('Both')">Connect</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('UMTS')">Mobile</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('VPN')">Tunnel</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('VPN_Setup')">Tunnel Setup</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('UMTS_Setup')">Mobile Setup</button>
</div>


<div id="UMTS" class="w3-container city" style="display:none">
<div class="w3-panel w3-green w3-round-xlarge">
	<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
		<p>UMTS is used when no other Internet Connection is avaible. The proper stick needs to be pluged in and installed.</p>
		<input type="submit" class="w3-btn w3-brown" value="Start UMTS" name="start_umts">
		<input type="submit" class="w3-btn w3-brown" value="Stop UMTS" name="stop_umts">
		<br><br>
	</form>
</div>
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
			<br><br>
		</form>
	</div>
</div>

<div id="Both" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round-xlarge">
		<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<p>First UMTS will be connected then the VPN</p>
			<input type="submit" class="w3-btn w3-brown" value="Start Both" name="start_umts_vpn">
			<input type="submit" class="w3-btn w3-brown" value="Stop Both" name="stop_umts_vpn">
			<br><br>
		</form>
	</div>
</div>

<div id="UMTS_Setup" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round-xlarge">
		<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<p>APN needs to be changed according to the provider's needs. The Swtich Mode must be executed if the UMTS Dongle has been removed while the system was running. </p>
			<input type="submit" class="w3-btn" value="Swtich Mode" name="switch_mode">

			Standard Settings: Edeka Mobil
			<br><br>
			<label class="w3-label">First Name</label>
			<input type="text" class="w3-input" value="&quot;data.access.de&quot;" name="apn">
			
			<label class="w3-label">First Name</label>
			<input type="text" class="w3-input" value="*99***1#" name="dial">
			<input type="submit" class="w3-btn" value="Change Settings" name="change_wvdial">
			<br><br>
		</form>
	</div>
</div>

<div id="VPN_Setup" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round-xlarge">
		<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<p>VPN Certificat.</p>
			<input type="file" name="fileToUpload" id="fileToUpload">
			<input type="submit" value="Upload Certificate" name="upload_cfg">
			<br><br><br>
			<input type="submit" class="w3-btn" value="Remove Certificate" name="rm_config">
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