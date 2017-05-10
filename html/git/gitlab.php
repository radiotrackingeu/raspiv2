<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/weblib/w3.css">
<link rel="stylesheet" href="/weblib/css/font-awesome.min.css">

<body>

<div class="w3-container w3-green">
<h1>radio-tracking.eu</h1>
  <img src="/images/logo_rteu.png" alt="radio-tracking.eu" style="width:25%"><br>
 <button class="w3-button w3-green w3-xlarge" onclick="w3_switch('sidebar')"><i class="fa fa-bars" aria-hidden="true"> Menu</i></button>
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
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('GIT')">Update</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('install')">Install</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('running_docker')">Status</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('git_setup')">Setup Update</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('create_id')">Create Key</button>
  
</div>
	
	<div id="GIT" class="w3-container city" style="display:none">
		<br>First download then install the feature - installing requires also an internet connection and requires some time. <br><br>
		<form method="POST" onsubmit="return openCity('GIT');">
		<input class="w3-btn" type="submit" value="Download Recepies and HTML Files" name="update_rep" onclick="openCity('GIT')"/>

		</form>
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
		<form method="POST">
			<br>
			<input type="submit" class="w3-btn" value="Runnning" name="running_containers">
			<input type="submit" class="w3-btn" value="Installed" name="installed_images">
			<input type="submit" class="w3-btn" value="Stop all" name="stop_all">
			<input type="submit" class="w3-btn" value="Remove all stopped containers" name="rm_all">
			<input type="submit" class="w3-btn" value="Remove all unsed Images" name="rmi_unused">
			<input type="submit" class="w3-btn" value="Remove all Images" name="rmi_all">
			<br>
			

		</form>
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
		<div id="output" class="w3-container city" style="display:block">
		<br> Please choose one of the option shown above - the result will be displayed here:
		<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			if (isset($_POST["update_rep"])){
				echo '<pre>';
				$test = system('sudo docker run -t -v /home/pi/gitrep/:/home/pi/gitrep/ -v /var/www/html/:/var/www/html/ --net="host" git sh /home/pi/gitrep/raspiv2/Docker/gitlab/update_html.sh 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["update_docker_git"])){
				echo '<pre>';
				$test = system('sudo docker build -t git /home/pi/gitrep/raspiv2/Docker/gitlab/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["update_docker_umts"])){
				echo '<pre>';
				$test = system('sudo docker build -t umts /home/pi/gitrep/raspiv2/Docker/umts/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_wifi"])){
				echo '<pre>';
				$test = system('sudo docker build -t wifi /home/pi/gitrep/raspiv2/Docker/wifi/. 2>&1', $ret);
				echo '</pre>';
			}
			if (isset($_POST["install_rtlsdr"])){
				echo '<pre>';
				$test = system('sudo docker build -t rtlsdr /home/pi/gitrep/raspiv2/Docker/rtlsdr/. 2>&1', $ret);
			echo '</pre>';
			}
			if (isset($_POST["install_webrx"])){
				echo '<pre>';
				$test = system('sudo docker build -t webrx /home/pi/gitrep/raspiv2/Docker/webrx/. 2>&1', $ret);
			echo '</pre>';
			}
			if (isset($_POST["install_picam"])){
				echo '<pre>';
				$test = system('sudo docker build -t picam /home/pi/gitrep/raspiv2/Docker/picam/. 2>&1', $ret);
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
				$result = liveExecuteCommand($cmd1);
				$result = liveExecuteCommand($cmd2);
				echo "Config has been removed";
			}
			if (isset($_POST["running_containers"])){
				echo '<pre>';
				$content = system('sudo docker ps', $ret);
				echo '</pre>';
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
	</div>



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

<!-- Enter text here-->

</body>

</html>

<?php 
function liveExecuteCommand($cmd)
{

    while (@ ob_end_flush()); // end all output buffers if any

    $proc = popen("$cmd 2>&1 ; echo Exit status : $?", 'r');

    $live_output     = "";
    $complete_output = "";

    while (!feof($proc))
    {
        $live_output     = fread($proc, 4096);
        $complete_output = $complete_output . $live_output;
        echo "$live_output";
        @ flush();
    }

    pclose($proc);

    // get exit status
    preg_match('/[0-9]+$/', $complete_output, $matches);

    // return exit status and intended output
    return array (
                    'exit_status'  => intval($matches[0]),
                    'output'       => str_replace("Exit status : " . $matches[0], '', $complete_output)
                 );
}
function unliveExecuteCommand($cmd)
{
    while (@ ob_end_flush()); // end all output buffers if any
    $proc = popen("$cmd 2>&1 ; echo Exit status : $?", 'r');
    pclose($proc);
}
?>
