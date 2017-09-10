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
	define ('confSection', 'connect');
	define ('confKeys', array('Signle_Freq','Freq1','Freq2','Freq3','Freq4','Freq5','Freq6', 'Radio_Gain'));
	
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
	</form>
</div>
</div>

<div id="VPN" class="w3-container city" style="display:none">
<div class="w3-panel w3-green w3-round-xlarge">
	<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
		<p>Start a tunnel so the Raspberry pi can be oprated via www. A licence is needed.</p> 
		<input type="submit" class="w3-btn w3-brown" value="Start VPN" name="start_vpn">
		<input type="submit" class="w3-btn w3-brown" value="Stop VPN" name="stop_vpn">
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

		<?php
			if (isset($_POST["upload_cfg"])){
				$target_dir = "/connect/";
				$target_file = "/var/www/html/connect/client.conf";
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
					echo "The file has been uploaded.";
				} else {
					echo "Sorry, there was an error uploading your file.";
				}
			}				
			if (isset($_POST["rm_config"])){
				$cmd = "rm /var/www/html/connect/client.conf";
				$result = system($cmd);
				echo "Config has been removed";
			}
			if (isset($_POST["change_wvdial"])){ 
				$cmd1 = "cp /var/www/html/connect/edeka.conf /var/www/html/connect/wvdial.conf";
				$cmd2 = "sed -i 's/Init3 = AT+CGDCONT=1,\"IP\",.*$/Init3 = AT+CGDCONT=1,\"IP\",".$_POST["apn"]."/' /var/www/html/connect/wvdial.conf"; 
				$cmd3 = "sed -i 's/Phone = .*$/Phone = ".$_POST["dial"]."/' /var/www/html/connect/wvdial.conf"; 
				$result = system($cmd1);
				$result = system($cmd2);
				$result = system($cmd3);
			}
			if (isset($_POST["switch_mode"])){ 
				$cmd = "sudo docker run --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 1f01 -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";
				$result = system($cmd);
			}
			if (isset($_POST["start_umts_vpn"])){ 
				$cmd1 = "sudo docker run --rm --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 1f01 -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";
				$cmd2 = "sudo docker run --rm --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 15ca -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";
				$cmd3 = "sudo docker run --rm -v /var/www/html/connect/:/config/ --privileged --net=host -t umts sh /config/start_umts.sh 2>&1";
				$result1 = system($cmd1);
				$result2 = system($cmd2);
				sleep(2);
				$result3 = system($cmd3);
			}
			if (isset($_POST["stop_umts_vpn"])){
				$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=umts) 2>&1"; 
				$result = system($cmd);
			}
			if (isset($_POST["stop_vpn"])){
				$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=umts) 2>&1"; 
				$result = system($cmd);
			}
			if (isset($_POST["start_vpn"])){ 
				$cmd = "sudo docker run --rm -v /var/www/html/connect/:/config/ --privileged --net=host -t umts openvpn /config/client.conf 2>&1";
				$result = system($cmd);
			}
			if (isset($_POST["stop_umts"])){
				$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=umts) 2>&1"; 
				$result = system($cmd);
			}
			if (isset($_POST["start_umts"])){
				$cmd1 = "sudo docker run --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 1f01 -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";
				$cmd2 = "sudo docker run --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 15ca -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";				
				$cmd3 = "sudo docker run --privileged --net=host -t umts wvdial 2>&1";
				$result = system($cmd1);
				$result = system($cmd2);
				sleep(2);
				$result = system($cmd3);
			}
		?>




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