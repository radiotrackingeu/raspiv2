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
	<button class="w3-button w3-green w3-round-xxlarge w3-hover-red w3-xlarge" onclick="w3_switch('sidebar')"><i class="fa fa-bars" aria-hidden="true"> Menu</i></button>
	<i class="fa fa-wrench"></i> System
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
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('schedule')">Schedule</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('date')">Time/Date</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('date')">Hostname</button>
</div>

<div id="schedule" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="POST">
			<br>
			You can leave out the weekday <br><br>
			<input type="text" name="new_date" value="* * * * *"> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Light on" name="cron_light_on"><br>
			<input type="text" name="new_date" value="* * * * *"> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Light off" name="cron_light_off"><br>
			<br>
		</form>
	</div>
</div>	


<div id="date" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="POST">
			<br>
			You can leave out the weekday <br><br>
			<input type="text" name="new_date" value="<?php echo shell_exec("date"))?>"> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Update date and time" name="update_date"><br>
			<br>
		</form>
	</div>
</div>	

<div id="hostname" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="POST">
			<br>
			Please choose a new hostname - it needs to start with characters. <br><br>
			<input type="text" name="new_hostname" value="<?php echo  system("cat /etc/hostname")?>"> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Update Hostname" name="change_hostname"><br>
			<br>
		</form>
	</div>
</div>	

<div id="output" class="w3-container city" style="display:block">
	<br> Please choose one of the option shown above - the result will be displayed here:
		<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			if (isset($_POST["update_date"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged git date --set \"".$_POST["new_date"]."\" 2>&1", $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["change_hostname"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm -v /var/www/html/git/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/change_hostname.sh ".$_POST["new_hostname"], $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["cron_light_on"])){
				echo '<pre>';
				$cmd = $_POST["cron_lights"]."root       sudo docker run -t --rm --privileged -v /var/www/html/picam/:/tmp/ i2c sh /tmp/start_all_lights.sh 2>&1";
				$file = "/etc/crontab";
				$test = system("sudo docker run -t --rm --privileged -v /var/www/html/git/:/tmp/ git sh /tmp/add_cronjob.sh ".$cmd." ".$file , $ret);
				echo '</pre>';
			}
			
			if (isset($_POST["reboot"])){
				echo '<pre>';
				$test = system('sudo reboot', $ret);
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
