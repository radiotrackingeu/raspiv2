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
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('logger')">Logger</button>
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('logger_timer')">Logger Timer</button>
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('spectrum')">Spectrum</button>
</div>

<div id="spectrum" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br>
		To record a Frequency Spektrum for a given time, just modify the entries below and press Start.
		<h3>Record properties</h3><br>
		<form method='POST' enctype="multipart/form-data"> 
			<table style="width:90%">
				<tr>
					<td>Center Frequency:</td>
					<td><input type="text" name="cfreq" value="150190k"></td>
					<td>Frequency in the middle of the frequency range of 250 kHz</td>
				</tr>
				<tr>
					<td>Gain in mDB:</td>
					<td><input type="text" name="gain" value="400"></td>
					<td>Gain of the recording device. Higher gain results in more noise.</td>
				</tr>
				<tr>
					<td>Record Time:</td>
					<td><input type="text" name="rtime" value="1m"></td>
					<td>The is the actual overall recording time. You can use units like m for minutes and h for hours.</td>
				</tr>
				<tr>
					<td>Record Name:</td>
					<td><input type="text" name="rname" value="d0"></td>
					<td>Each record will be given a file name, be careful, the same name will overwrite existing files. You can find the results here: <a href="/sdr/record/">Record Folder</a></td>
				</tr>
			</table>
			<br>
			<br>
			Start or stop recording/s: 
			<br>
			<br>
			<input type="submit" class="w3-btn w3-brown" value="Start" name="fftw_start" />
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="fftw_stop" />
		</form> 
		<br>
	</div>
</div>

<div id="logger" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br>
		<h3>Logger settings</h3><br>
		<form method='POST' enctype="multipart/form-data">

				Gain in DB:<br>
				<input type="number" name="log_gain" value="20"><br>
				Gain of the recording device. Higher gain results in more noise. max 49DB
				<br><br>
				Center Frequency in Hz:<br>
				<input type="number" name="center_freq" value="150100000"><br>
				Frequency Range to monitor: <br>
				<select name="freq_range">
					<option value="250000">250kHz</option>
					<option value="1024000">1024kHz</option>
				</select> 
				<br>
				Log Detection Level:<br>
				<input type="text" name="log_level" value="0"><br>
				0 means automatic - level up to 16384
				<br><br>
				Record Name:<br>
				<input type="text" name="log_name" value="<?php echo date('Y_m_d_H_i')?>"><br>
				Each record will be given a file name, be careful, the same name will overwrite existing files. You can find the results here: <a href="/sdr/record/">Record Folder</a><br><br>
				
				
			<input type="submit" class="w3-btn w3-brown" value="Start" name="log_start" />
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="log_stop" />
		</form>
		<br>
	</div>
</div>

<div id="logger_timer" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br>
		<h3>Logger settings</h3><br>
		<form method='POST' enctype="multipart/form-data">
		
			Gain in DB:<br>
			<input type="number" name="log_gain" value="20"><br>
			Gain of the recording device. Higher gain results in more noise. max 49DB
			<br><br>
			Center Frequency in Hz:<br>
			<input type="number" name="center_freq" value="150100000"><br>
			Frequency Range to monitor: <br>
			<select name="freq_range">
				<option value="250000">250kHz</option>
				<option value="1024000">1024kHz</option>
			</select> 
			<br>
			Log Detection Level:<br>
			<input type="text" name="log_level" value="0"><br>
			0 means automatic - level up to 16384
			<br><br>
			Record Name:<br>
			<input type="text" name="log_name" value="<?php echo date('Y_m_d_H_i')?>"><br>
			Each record will be given a file name, be careful, the same name will overwrite existing files. You can find the results here: <a href="/sdr/record/">Record Folder</a><br><br>

			<input type="radio" name="start_timer" value="start_no" checked> No start<br>
			<input type="radio" name="start_timer" value="reboot"> Start at Boot<br>
			<input type="radio" name="start_timer" value="start_on_time"> Start at times stated below<br>
				Minute
				<select name="start_min">
					<option value="0">0</option>
					<option value="10">10</option>
					<option value="20">20</option>
					<option value="30">30</option>
					<option value="40">40</option>
					<option value="50">50</option>
				</select> 
				Hour
				<select name="start_hour">
					<option value="0">0</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
				</select>
			<br><br>
			<input type="radio" name="stop_timer" value="stop_no" checked> No stop<br>
			<input type="radio" name="stop_timer" value="stop_on_tim"> Stop at times stated below<br>
				Minute
				<select name="stop_min">
					<option value="0">0</option>
					<option value="10">10</option>
					<option value="20">20</option>
					<option value="30">30</option>
					<option value="40">40</option>
					<option value="50">50</option>
				</select> 
				Hour
				<select name="stop_hour">
					<option value="0">0</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
				</select>
				<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Change Settings" name="change_logger_cron" />
		</form>
		<br>
	</div>
</div>

<?php

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	if (isset($_POST["fftw_start"])){
		$cmd = "rtl_power_fftw -r 250000 -f " . $_POST["cfreq"]. " -b 128 -t 0.1 -g " . $_POST["gain"]. " -q -d 0 -e " . $_POST["rtime"]. " -m /home/" . $_POST["rname"];
		echo '<pre>';
		$test = system("sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtlsdr ".$cmd." 2>&1", $ret);
		echo '</pre>';
	}
	if (isset($_POST["fftw_stop"])){
		echo '<pre>';
		$result = system("sudo docker stop $(sudo docker ps -a -q --filter ancestor=rtlsdr) 2>&1", $ret);
		echo '</pre>';
	}
	if (isset($_POST["log_start"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash -c 'rtl_433 -f ".$_POST["center_freq"]." -s ".$_POST["freq_range"]." -t -q -A -l ".$_POST["log_level"]." -g " . $_POST["log_gain"]. " 2> /home/" . $_POST["log_name"]."'";
		unliveExecuteCommand($cmd);
	}
	if (isset($_POST["log_stop"])){
		echo '<pre>';
		$result = system("sudo docker stop $(sudo docker ps -a -q --filter ancestor=rtl_433_mod) 2>&1", $ret);
		echo '</pre>';
	}
	if (isset($_POST["change_logger_cron"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash -c 'rtl_433 -f ".$_POST["center_freq"]." -s ".$_POST["freq_range"]." -t -q -A -l ".$_POST["log_level"]." -g " . $_POST["log_gain"]. " 2> /home/" . $_POST["log_name"]."'";
		if($_POST["start_timer"]=="reboot"){
			$change= "@reboot root " .$cmd;
			$search = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash";
			$file_to_replace="/tmp/crontab";
			$result = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"", $ret);
			echo "System will now start logger upon start with the following settings: Frequency: ".$_POST["center_freq"]." Frequency-Range: ".$_POST["freq_range"]." Log-Level: ".$_POST["log_level"]." Gain: " . $_POST["log_gain"]. " and File-Name: ". $_POST["log_name"];
		}
		if($_POST["start_timer"]=="start_no"){
			$change= "#@reboot root " .$cmd;
			$search = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash";
			$file_to_replace="/tmp/crontab";
			$result = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"", $ret);
			echo '</pre>';
			echo "System will not start logger upon start";
		}
		if($_POST["start_timer"]=="start_on_time"){
			$change= $_POST["start_min"]. " ".$_POST["start_hour"]." root " .$cmd;
			$search = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash";
			echo '<pre>';
			$result = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\"", $ret);
			echo '</pre>';
			echo "System will now start logger upon start with the following settings: <br><br>Frequency: ".$_POST["center_freq"]." Frequency-Range: ".$_POST["freq_range"]." Log-Level: ".$_POST["log_level"]." Gain: " . $_POST["log_gain"]. " and File-Name: ". $_POST["log_name"];
		}
	}
	function unliveExecuteCommand($cmd)
	{
		while (@ ob_end_flush()); // end all output buffers if any
		$proc = popen("$cmd 2>&1", 'r');
		pclose($proc);
	}
	
?>

<!-- Enter text here-->


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


</body>

</html>