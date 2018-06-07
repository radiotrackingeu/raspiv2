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
	define ('confSection', 'gitlab_updates');
	define ('confKeys', array('online_repository','lokal_branch','git_keepcfg'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');	
	
	//Image versions
	$image_version_git="1.0";
	$image_version_umts="1.0";
	$image_version_wifi="1.0";
	$image_version_microphone="1.0";
	$image_version_pwchange="1.0";
	
 ?>
<!-- Enter text here-->

<div class="w3-bar w3-brown w3-mobile">
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'GIT')">System Update</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'install')">Install/Update Applications</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'running_docker')">Application Status</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'config_file')">Configuration File</button>
</div>

<div id="GIT" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		Update the User Interface - if a single Application has been updated - please go afterwards to Applications. Please also choose to keep your old config file or update it with standard settings.<br><br>
		<form method="POST" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF'];?>">
				<select name="git_checkout">
					<option value="master" <?php echo isset($config['gitlab_updates']['lokal_branch']) && $config['gitlab_updates']['lokal_branch'] == "master" ?  "selected" : ""; ?>>Stable Version</option>
					<option value="live" <?php echo isset($config['gitlab_updates']['lokal_branch']) && $config['gitlab_updates']['lokal_branch'] == "live" ? "selected" : ""; ?>>Development Version</option>
				</select>
				<select name="git_keepcfg">
					<option value="updatecfg" <?php echo isset($config['gitlab_updates']['git_keepcfg']) && $config['gitlab_updates']['git_keepcfg'] == "updatecfg" ? "selected" : ""; ?>>Update config file</option>
					<option value="keepcfg" <?php echo isset($config['gitlab_updates']['git_keepcfg']) && $config['gitlab_updates']['git_keepcfg'] == "keepcfg" ?  "selected" : ""; ?>>Keep old config file</option>
				</select>
				<select name="git_switch_system">
					<option value="build_none" SELECTED>No change in system</option>
					<option value="build_raspi3">Update for Raspberry Pi 3</option>
					<option value="build_raspi_zerow">Update for Raspberry Pi Zero W</option>
				</select>
			<input class="w3-btn w3-brown" type="submit" value="Update User Interface" name="update_rep" onclick="openCity('GIT')"/>
		</form>
		<br>
	</div>
</div>
	
<div id="install" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="POST" onsubmit="document.getElementById('install').style.display = 'block');">
			<br>
			<input type="submit" class="w3-btn w3-brown" value="Update" name="update_docker_git"/> <br> <br>
			Available: <?php echo $image_version_git;?>
			Equivalent with Installed version: <?php $test=system("if sudo docker images --filter reference=git | grep -q 1.0; then echo yes; else echo no; fi 2>&1", $ret); ?>
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="Remote" name="update_docker_umts"/> <br> <br>
			Available: <?php echo $image_version_umts;?>
			Equivalent with Installed version: <?php $test=system("if sudo docker images --filter reference=umts | grep -q 1.0; then echo yes; else echo no; fi 2>&1", $ret); ?>
			
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="WiFi" name="install_wifi"/> <br> <br> 
			Available: <?php echo $image_version_wifi;?>
			Equivalent with Installed version: <?php $test=system("if sudo docker images --filter reference=wifi | grep -q 1.0; then echo yes; else echo no; fi 2>&1", $ret); ?>
			
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="WebRadio" name="install_rtlsdr"/> <br> <br> 
			
			<hr>
			OpenWebRX<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Spectrogram" name="install_webrx"/> <br> <br>
			
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="MotionEye" name="install_motioneye"/> <br> <br>
			
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="Motion Detection" name="install_motion_detection"/> <br> <br>
			
			<hr>
			I2C<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Light Controls" name="install_i2c"/> <br> <br>
			
			
			<hr>
			SoX<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Microphone" name="install_sox"/> <br> <br>
			Available: <?php echo $image_version_microphone;?>
			Equivalent with Installed version: <?php system("if sudo docker images --filter reference=microphone | grep -q 1.0; then echo yes; else echo no; fi") ?>
			<hr>
			RTL_433<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Sensor Readings" name="install_rtl_433"/> <br> <br>
			
			<hr>
			LiquidSDR<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Logger" name="install_liquidsdr"/> <br> <br>
			
			<hr>
			MySQL<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Database" name="install_mysql"/> <br> <br>
					
			<hr>
			PhpMyAdmin<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Database-Management" name="install_phpmyadmin"/> <br> <br>
						
			<hr>
			USB Power On/Off<br><br>
			<input type="submit" class="w3-btn w3-brown" value="USB Power On/Off" name="install_hubctrl"/> <br> <br>
						
			<hr>
			Password changer<br><br>			
			<input type="submit" class="w3-btn w3-brown" value="Password changer" name="install_pwchange"/> <br> <br>

		</form>
	</div>
</div>

<div id="running_docker" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="POST">
			<br>
			
			<input type="submit" class="w3-btn w3-brown" value="List running Applications" name="running_containers">
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="List installed Applications" name="installed_images">
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="Stop all running Applications" name="stop_all">
			Careful, this will also disable the Hotspot!
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="Delete all stopped containers" name="rm_all">
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="Delete all unsed Images" name="rmi_unused">
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="Delete all Images" name="rmi_all">
			<br><br>
		</form>
	</div>
</div>
		
<div id="config_file" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round w3-padding">
	Here you can down- and upload configuration files.<br>
	When you upload a new file, a copy of the old one will be made. This copy of the last configuration file can be restored by clicking the corresponding button.<br>
	Restoring the default configuration will load the configuration file that came with the last software update.
	<hr>
		<div class="w3-bar w3-padding">
		<a class="w3-btn w3-brown" href="../cfg/globalconfig" download>Download current config</a>
		</div>
		<br>
		<form method="POST" enctype="multipart/form-data">
		<div class="w3-bar w3-padding">
			<input type="submit" class="w3-btn w3-brown" value="Upload new config" name="ul_config">
			<input type="file" class="w3-btn w3-green" name="file_config" style="hover:none">
		</div>
		<br>
		<div class="w3-bar w3-padding">
			<input type="submit" class="w3-btn w3-brown" value="Restore last config" name="restore_config">
			<input type="submit" class="w3-btn w3-brown" value="Restore default config" name="restore_default">
		</div>
		<br>
		</form>
	</div>
</div>		
		
		
		
		<?php
			if (isset($_POST["update_docker_git"])){
				echo '<pre>';
				$test = system('sudo docker build  -t git:1.0 /home/pi/gitrep/raspiv2/Docker/gitlab/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["update_docker_umts"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t umts:1.0 /home/pi/gitrep/raspiv2/Docker/umts/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_wifi"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t wifi:1.0 /home/pi/gitrep/raspiv2/Docker/wifi/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_rtlsdr"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t rtlsdr:1.0 /home/pi/gitrep/raspiv2/Docker/rtlsdr/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_webrx"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t webrx:1.0 /home/pi/gitrep/raspiv2/Docker/webrx/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_motioneye"])){
				echo '<pre>';
				$test = system('sudo docker pull ccrisan/motioneye:dev-armhf 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_motion_detection"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t motion_detection:1.0 /home/pi/gitrep/raspiv2/Docker/motion_detection/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_rtl_433"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t rtl_433:1.0 /home/pi/gitrep/raspiv2/Docker/rtl_433/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_i2c"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t i2c:1.0 /home/pi/gitrep/raspiv2/Docker/i2c/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_sox"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t microphone:1.0 /home/pi/gitrep/raspiv2/Docker/microphone/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_liquidsdr"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t liquidsdr:1.0 /home/pi/gitrep/raspiv2/Docker/liquidsdr/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_mysql"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t mysql:1.0 /home/pi/gitrep/raspiv2/Docker/mysql/. 2>&1 && sudo docker create -t --restart=unless-stopped --name=mysql -e MYSQL_ROOT_PASSWORD=rteuv2! -p 3306:3306 -v /var/www/html/data/mysql:/var/lib/mysql  mysql:1.0 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_phpmyadmin"])){
				echo '<pre>';
				$test = system('sudo docker build -t phpmyadmin:1.0 /home/pi/gitrep/raspiv2/Docker/phpmyadmin/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_hubctrl"])){
				echo '<pre>';
				$test = system('sudo docker build -t hubctrl:1.0 /home/pi/gitrep/raspiv2/Docker/hubctrl/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_pwchange"])) {
				echo '<pre>';
				$test = system('sudo docker build -t pwchange:1.1 /home/pi/gitrep/raspiv2/Docker/pwchange/. 2>&1', $ret);
				echo '</pre>';
			}
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			if (isset($_POST["installed_images"])){
				echo '<pre>';
				$content = system('sudo docker images', $ret);
				echo '</pre>';
			}
			if (isset($_POST["stop_all"])){
				echo '<pre>';
				$test = system('sudo docker stop $(sudo docker ps -a -q) 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["rm_all"])){
				echo '<pre>';
				$test = system('sudo docker rm $(sudo docker ps -a -q) 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["rmi_unused"])){
				echo '<pre>';
				$test = system('sudo docker rmi $(sudo docker images --filter "dangling=true" -q --no-trunc)', $ret);
				echo '</pre>';
			}
			if (isset($_POST["rmi_all"])){
				echo '<pre>';
				$test = system('sudo docker rmi -f $(sudo docker images -q)', $ret);
				echo '</pre>';
			}
			
            //upload config
			if (isset($_POST["ul_config"]))
				if ($_FILES["file_config"]["size"]!=0 ) {
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mtype = finfo_file($finfo,$_FILES["file_config"]["tmp_name"]);
					if ($mtype == "text/plain")
					{
						rename(CONFIGFILES_PATH."/globalconfig", CONFIGFILES_PATH."/globalconfig.bak");
						if (move_uploaded_file($_FILES["file_config"]["tmp_name"], CONFIGFILES_PATH."/globalconfig")){
							echo "Successfully uploaded new config file.";
							$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
						} else {
							rename(CONFIGFILES_PATH."/globalconfig.bak", CONFIGFILES_PATH."/globalconfig");
							echo "An error occured. Restored old config file.";
						}
					}
					else 
						echo "That was not a valid config file.";
				}
				else
					echo "No file given.";
			
            //restore last config
			if (isset($_POST["restore_config"])) {
				if (!file_exists(CONFIGFILES_PATH."/globalconfig.bak"))
					echo "No backup found.";
				else {
					if (copy(CONFIGFILES_PATH."/globalconfig.bak", CONFIGFILES_PATH."/globalconfig")) {
						echo "Configuration file restored.";
						$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
					} else
						echo "Error: Could not copy config file.";
					
				}
			}
			
            //restore from git
			if (isset($_POST["restore_default"])) {
				$git_file = GITREPO_PATH."/html/cfg/globalconfig";
				if(!file_exists($git_file))
					echo "No backup found.";
				else {
					if (copy($git_file, CONFIGFILES_PATH."/globalconfig")) {
						echo "Configuration file restored.";
						$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
					} else
						echo "Error: Could not copy config file.";
					
				}
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
