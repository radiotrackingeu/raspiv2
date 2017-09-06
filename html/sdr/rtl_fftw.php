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
	define ('confSection', 'logger_433');
	define ('confKeys', array('log_gain','center_freq','freq_range','log_level','pre_log_name'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
 ?>

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
		<form method="POST" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>" >

				Gain in DB:<br>
				<input type="number" name="log_gain" value="<?php echo isset($config['logger_433']['log_gain']) ? $config['logger_433']['log_gain'] : 20 ?>"><br>
				Gain of the recording device. Higher gain results in more noise. max 49DB
				<br><br>
				Center Frequency in Hz:<br>
				<input type="number" name="center_freq" value="<?php echo isset($config['logger_433']['center_freq']) ? $config['logger_433']['center_freq'] : 150100000 ?>"><br>
				Frequency Range to monitor: <br>
				<select name="freq_range">
					<option value="250000" <?php echo isset($config['logger_433']['freq_range']) && $config['logger_433']['freq_range'] == "250000" ? "selected" : "" ?>>250kHz</option>
					<option value="1024000" <?php echo isset($config['logger_433']['freq_range']) && $config['logger_433']['freq_range'] == "1024000" ? "selected" : "" ?>>1024kHz</option>
				</select> 
				<br>
				Log Detection Level:<br>
				<input type="text" name="log_level" value="<?php echo isset($config['logger_433']['log_level']) ? $config['logger_433']['log_level'] : 1 ?>"><br>
				0 means automatic - level up to 16384 - the tricky part is setting a good log level compared to the gain: try and error
				<br><br>
				Prefix and Record Name:<br>
				<input type="text" name="pre_log_name" value="<?php echo isset($config['logger_433']['pre_log_name']) ? $config['logger_433']['pre_log_name'] : "rteu" ?>">
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
				<input type="number" name="log_gain" value="<?php echo isset($config['logger_433']['log_gain']) ? $config['logger_433']['log_gain'] : 20 ?>"><br>
				Gain of the recording device. Higher gain results in more noise. max 49DB
				<br><br>
				Center Frequency in Hz:<br>
				<input type="number" name="center_freq" value="<?php echo isset($config['logger_433']['center_freq']) ? $config['logger_433']['center_freq'] : 150100000 ?>"><br>
				Frequency Range to monitor: <br>
				<select name="freq_range">
					<option value="250000" <?php echo isset($config['logger_433']['freq_range']) && $config['logger_433']['freq_range'] == "250000" ? "selected" : "" ?>>250kHz</option>
					<option value="1024000" <?php echo isset($config['logger_433']['freq_range']) && $config['logger_433']['freq_range'] == "1024000" ? "selected" : "" ?>>1024kHz</option>
				</select> 
				<br>
				Log Detection Level:<br>
				<input type="text" name="log_level" value="<?php echo isset($config['logger_433']['log_level']) ? $config['logger_433']['log_level'] : 1 ?>"><br>
				0 means automatic - level up to 16384 - the tricky part is setting a good log level compared to the gain: try and error
				<br><br>
				Prefix and Record Name:<br>
				<input type="text" name="pre_log_name" value="<?php echo isset($config['logger_433']['pre_log_name']) ? $config['logger_433']['pre_log_name'] : "rteu" ?>">
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
			<input type="radio" name="stop_timer" value="stop_on_time"> Stop at times stated below<br>
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
			echo "System will not start logger upon start";
		}
		if($_POST["start_timer"]=="start_on_time"){
			$change= $_POST["start_min"]. " ".$_POST["start_hour"]." * * * root " .$cmd;
			$search = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash";
			$file_to_replace="/tmp/crontab";
			$result = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"", $ret);
			echo "System will now start logger upon start with the following settings: <br><br>Frequency: ".$_POST["center_freq"]." Frequency-Range: ".$_POST["freq_range"]." Log-Level: ".$_POST["log_level"]." Gain: " . $_POST["log_gain"]. " and File-Name: ". $_POST["log_name"];
		}
		$stop_cmd="sudo docker stop \\$(sudo docker ps -a -q --filter ancestor=rtl_433_mod)";
		if($_POST["stop_timer"]=="stop_no"){
			$change= "#".$stop_cmd;
			$search = $stop_cmd;
			$file_to_replace="/tmp/crontab";
			$result = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"", $ret);
			echo "System will not stop logger";
		}
		if($_POST["stop_timer"]=="stop_on_time"){
			$change= $_POST["stop_min"]. " ".$_POST["stop_hour"]." * * * root " .$stop_cmd;
			$search = $stop_cmd;
			$file_to_replace="/tmp/crontab";
			$result = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"", $ret);
			echo "System will now stop logger at specific time";
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