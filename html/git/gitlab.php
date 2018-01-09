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
	define ('confKeys', array('online_repository','lokal_branch'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');	
 ?>
<!-- Enter text here-->

<div class="w3-bar w3-brown w3-mobile">
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('GIT')">User Interface</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('install')">Applications</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('running_docker')">Applications Status</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('git_setup')">Setup Update</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('create_id')">Create Key</button>
</div>

<div id="GIT" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br>First update the User Interface - if a single Application has been updated - please go afterwards to Applications<br><br>
		<form method="POST" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF'];?>">
				<select name="git_checkout">
					<option value="master" <?php echo isset($config['gitlab_updates']['lokal_branch']) && $config['gitlab_updates']['lokal_branch'] == "master" ?  "selected" : ""; ?>>Stable Version</option>
					<option value="live" <?php echo isset($config['gitlab_updates']['lokal_branch']) && $config['gitlab_updates']['lokal_branch'] == "live" ? "selected" : ""; ?>>Development Version</option>
				</select> 
			<input class="w3-btn w3-brown" type="submit" value="Update User Interface" name="update_rep" onclick="openCity('GIT')"/>
		</form>
		<br>
	</div>
</div>
	
<div id="install" class="w3-container city" style="display:none">
	<form method="POST" onsubmit="document.getElementById('install').style.display = 'block');">
		<br>
		<input type="submit" class="w3-btn" value="Downloader" name="update_docker_git"/> <br> <br>
		
		<hr>
		<input type="submit" class="w3-btn" value="Remote" name="update_docker_umts"/> <br> <br>
		
		<hr>
		<input type="submit" class="w3-btn" value="WiFi" name="install_wifi"/> <br> <br> 
		
		<hr>
		<input type="submit" class="w3-btn" value="Radio" name="install_rtlsdr"/> <br> <br> 
		
		<hr>
		<input type="submit" class="w3-btn" value="WebRX" name="install_webrx"/> <br> <br>
		
		<hr>
		<input type="submit" class="w3-btn" value="Picam" name="install_picam"/> <br> <br>
		
		<hr>
		<input type="submit" class="w3-btn" value="I2C" name="install_i2c"/> <br> <br>
		
		<hr>
		<input type="submit" class="w3-btn" value="SoX" name="install_sox"/> <br> <br>
		
		<hr>
		<input type="submit" class="w3-btn" value="RTL_433" name="install_rtl_433"/> <br> <br>
		<hr>
		<input type="submit" class="w3-btn" value="Logger" name="install_rtl_433_mod"/> <br> <br>
		
		<hr>	
		<input type="submit" class="w3-btn" value="GammaRF" name="install_gammrf"/> <br> <br>
		
		<hr>
		<input type="submit" class="w3-btn" value="LiquidSDR" name="install_liquidsdr"/> <br> <br>
		
		<hr>
		<input type="submit" class="w3-btn" value="MySQL" name="install_mysql"/> <br> <br>
				
		<hr>
		<input type="submit" class="w3-btn" value="phpMyAdmin" name="install_phpmyadmin"/> <br> <br>
		
		<hr>

	</form>
</div>

<div id="git_setup" class="w3-container city" style="display:none">
	<form method="post" enctype="multipart/form-data">
		<br>
		Select private key to upload (the one without an ending): 
		<br><br>
		<input type="file" name="fileToUpload_id" id="fileToUpload_id">
		<br><br><br>
		Select public key to upload (the one with a .pub ending):
		<br><br>
		<input type="file" name="fileToUpload_pub" id="fileToUpload_pub">
		<br><br><br><br>
		<input type="submit" class="w3-btn" value="Upload keys" name="upload_files">
		<input type="submit" class="w3-btn" value="Remove Files" name="rm_files">
	</form>
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
		
		<div id="create_id" class="w3-container city" style="display:none">
		<br>
		<form method="POST">
			<input type="submit" class="w3-btn" value="Create new Keys" name="create_keys">
			<input type="submit" class="w3-btn" value="Show installed key" name="show_keys"> <br>
			<label class="w3-label w3-validate">Email</label>
			<input class="w3-input" type="email">
			
		</form>
		<a target="_blank" href="/git/id_rsa">Open Key in new tab</a>
			
			<?php

			?>
		</div>
		<?php
			if (isset($_POST["reboot"])){
				echo '<pre>';
				$test = system('sudo reboot', $ret);
				echo '</pre>';
			}
			if (isset($_POST["update_docker_git"])){
				echo '<pre>';
				$test = system('sudo docker build  -t git /home/pi/gitrep/raspiv2/Docker/gitlab/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["update_docker_umts"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t umts /home/pi/gitrep/raspiv2/Docker/umts/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_wifi"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t wifi /home/pi/gitrep/raspiv2/Docker/wifi/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_rtlsdr"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t rtlsdr /home/pi/gitrep/raspiv2/Docker/rtlsdr/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_webrx"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t webrx /home/pi/gitrep/raspiv2/Docker/webrx/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_picam"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t picam /home/pi/gitrep/raspiv2/Docker/picam/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_rtl_433"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t rtl_433 /home/pi/gitrep/raspiv2/Docker/rtl_433/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_rtl_433_mod"])){
				echo '<pre>';
				$test = system('sudo docker build --no-cache -t rtl_433_mod /home/pi/gitrep/raspiv2/Docker/rtl_433_mod/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_i2c"])){
				echo '<pre>';
				$test = system('sudo docker build -t i2c /home/pi/gitrep/raspiv2/Docker/i2c/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_sox"])){
				echo '<pre>';
				$test = system('sudo docker build -t mircophone /home/pi/gitrep/raspiv2/Docker/microphone/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_gammrf"])){
				echo '<pre>';
				$test = system('sudo docker build -t gammrf /home/pi/gitrep/raspiv2/Docker/gammrf/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_liquidsdr"])){
				echo '<pre>';
				$test = system('sudo docker build -t liquidsdr /home/pi/gitrep/raspiv2/Docker/liquidsdr/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_mysql"])){
				echo '<pre>';
				$test = system('sudo docker build -t mysql /home/pi/gitrep/raspiv2/Docker/mysql/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_phpmyadmin"])){
				echo '<pre>';
				$test = system('sudo docker build -t phpmyadmin /home/pi/gitrep/raspiv2/Docker/phpmyadmin/. 2>&1', $ret);
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
