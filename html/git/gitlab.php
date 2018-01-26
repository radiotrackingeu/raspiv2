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
	define ('confSection', 'gitlab_updates');
	define ('confKeys', array('online_repository','lokal_branch','git_keepcfg'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');	
 ?>
<!-- Enter text here-->

<div class="w3-bar w3-brown w3-mobile">
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('GIT')">System Update</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('install')">Install/Update Applications</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('running_docker')">Application Status</button>
</div>

<div id="GIT" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br>Update the User Interface - if a single Application has been updated - please go afterwards to Applications. Please also choose to keep your old config file or update it with standard settings.<br><br>
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
			Avaible: <?php $image_version_git="1.0"; echo $image_version_git;?>
			Equivalent with Installed version: <?php system("if sudo docker images --filter reference=git | grep -q 1.0; yes; else echo no; fi") ?>
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="Remote" name="update_docker_umts"/> <br> <br>
			<?php $image_version_umts="1.0"?>
			
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="WiFi" name="install_wifi"/> <br> <br> 
			<?php $image_version_umts="1.0"?>
			
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="WebRadio" name="install_rtlsdr"/> <br> <br> 
			
			<hr>
			OpenWebRX<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Spectrogram" name="install_webrx"/> <br> <br>
			
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="Camera" name="install_picam"/> <br> <br>
			
			
			<hr>
			I2C<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Light Controls" name="install_i2c"/> <br> <br>
			
			
			<hr>
			SoX<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Microphone" name="install_sox"/> <br> <br>
			Avaible: <?php $image_version_microphone="1.0"; echo $image_version_microphone;?>
			Equivalent with Installed version: <?php system("if sudo docker images --filter reference=mircophone | grep -q 1.0; then echo yes; else echo no; fi") ?>
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
			This means, that also the Hotspot won't work any more.
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="Delete all stopped containers" name="rm_all">
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="Delte all unsed Images" name="rmi_unused">
			<hr>
			<input type="submit" class="w3-btn w3-brown" value="Delte all Images" name="rmi_all">
			<br><br>
		</form>
	</div>
</div>
		<?php
			if (isset($_POST["reboot"])){
				echo '<pre>';
				$test = system('sudo reboot', $ret);
				echo '</pre>';
			}
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
			if (isset($_POST["install_picam"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t picam:1.0 /home/pi/gitrep/raspiv2/Docker/picam/. 2>&1', $ret);
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
				$test = system('sudo docker build --no-cache -t mysql:1.0 /home/pi/gitrep/raspiv2/Docker/mysql/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_phpmyadmin"])){
				echo '<pre>';
				$test = system('sudo docker build -t phpmyadmin:1.0 /home/pi/gitrep/raspiv2/Docker/phpmyadmin/. 2>&1', $ret);
				echo '</pre>';
			}
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			if (isset($_POST["upload_files"])){
				$target_file1 = "/var/www/html/git/id_rsa";
				if (move_uploaded_file($_FILES["fileToUpload_id"]["tmp_name"], $target_file1)) {
					echo "The file has been uploaded.";
				} else {
					echo "Sorry, there was an error uploading your private key.";
				}
				$target_file2 = "/var/www/html/git/id_rsa.pub";
				if (move_uploaded_file($_FILES["fileToUpload_pub"]["tmp_name"], $target_file2)) {
					echo "The file has been uploaded.";
				} else {
					echo "Sorry, there was an error uploading your public key.";
				}
			}				
			if (isset($_POST["rm_files"])){
				$cmd1 = "rm /var/www/html/git/id_rsa";
				$cmd2 = "rm /var/www/html/git/id_rsa.pub";
				$result = system($cmd1);
				$result = system($cmd2);
				echo "Config has been removed";
			}
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
			if (isset($_POST["create_keys"])){
				echo '<pre>';
				$content = system('ssh-keygen -f /var/www/html/git/id_rsa -t rsa -P "" -b 4096 -C '. $email, $ret);
				echo '</pre>';
			}
			if (isset($_POST["show_keys"])){
				echo '<pre>';
				$content = system('cat /var/www/html/git/id_rsa', $ret);
				echo '</pre>';
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
