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
	define ('confSection', array('logger'));
	define ('confKeys', array('log_gain','center_freq','freq_range','log_level','pre_log_name','raw_log_log_gain','raw_center_freq','raw_freq_range','raw_pre_log_name','time_center_freq','time_freq_range','time_log_level','time_start_timer','time_start_min','time_start_hour','time_stop_timer','time_stop_min','time_stop_hour','time_pre_log_name'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
	
	//load php_scripts
	require_once RESOURCES_PATH.'/php_scripts.php';
 ?>

<!-- Enter text here-->
<div class="w3-bar w3-brown w3-mobile">
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('logger')">Logger</button>
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('logger_timer')">Logger Timer</button>
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('spectrum')">Spectrum</button>
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('raw_data')">Raw Data Recorder</button>
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
				<input type="number" name="log_gain" value="<?php echo isset($config['logger']['log_gain']) ? $config['logger']['log_gain'] : 20 ?>"><br>
				Gain of the recording device. Higher gain results in more noise. max 49DB
				<br><br>
				Center Frequency in Hz:<br>
				<input type="number" name="center_freq" value="<?php echo isset($config['logger']['center_freq']) ? $config['logger']['center_freq'] : 150100000 ?>"><br>
				Frequency Range to monitor: <br>
				<select name="freq_range">
					<option value="250000" <?php echo isset($config['logger']['freq_range']) && $config['logger']['freq_range'] == "250000" ? "selected" : "" ?>>250kHz</option>
					<option value="1024000" <?php echo isset($config['logger']['freq_range']) && $config['logger']['freq_range'] == "1024000" ? "selected" : "" ?>>1024kHz</option>
				</select> 
				<br>
				Log Detection Level:<br>
				<input type="text" name="log_level" value="<?php echo isset($config['logger']['log_level']) ? $config['logger']['log_level'] : 1 ?>"><br>
				0 means automatic - level up to 16384 - the tricky part is setting a good log level compared to the gain: try and error
				<br><br>
				Prefix and Record Name:<br>
				<input type="text" name="pre_log_name" value="<?php echo isset($config['logger']['pre_log_name']) ? $config['logger']['pre_log_name'] : "rteu" ?>">
				<input type="text" name="log_name" value="<?php echo date('Y_m_d_H_i')?>"><br>
				Each record will be given a file name, be careful, the same name will overwrite existing files. You can find the results here: <a href="/sdr/record/">Record Folder</a><br><br>
				
			<?php check_docker("logger-sdr-d1"); ?>
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
		<form method='POST' enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
		
				Gain in DB:<br>
				<input type="number" name="time_log_gain" value="<?php echo isset($config['logger']['time_log_gain']) ? $config['logger']['time_log_gain'] : 20 ?>"><br>
				Gain of the recording device. Higher gain results in more noise. max 49DB
				<br><br>
				Center Frequency in Hz:<br>
				<input type="number" name="time_center_freq" value="<?php echo isset($config['logger']['time_center_freq']) ? $config['logger']['time_center_freq'] : 150100000 ?>"><br>
				Frequency Range to monitor: <br>
				<select name="time_freq_range">
					<option value="250000" <?php echo isset($config['logger']['time_freq_range']) && $config['logger']['time_freq_range'] == "250000" ? "selected" : "" ?>>250kHz</option>
					<option value="1024000" <?php echo isset($config['logger']['time_freq_range']) && $config['logger']['time_freq_range'] == "1024000" ? "selected" : "" ?>>1024kHz</option>
				</select> 
				<br>
				Log Detection Level:<br>
				<input type="text" name="time_log_level" value="<?php echo isset($config['logger']['time_log_level']) ? $config['logger']['time_log_level'] : 1 ?>"><br>
				0 means automatic - level up to 16384 - the tricky part is setting a good log level compared to the gain: try and error
				<br><br>
				Prefix and Record Name:<br>
				<input type="text" name="time_pre_log_name" value="<?php echo isset($config['logger']['time_pre_log_name']) ? $config['logger']['time_pre_log_name'] : "rteu" ?>">
				<input type="text" name="log_name" value="<?php echo date('Y_m_d_H_i')?>"><br>
				Each record will be given a file name, be careful, the same name will overwrite existing files. You can find the results here: <a href="/sdr/record/">Record Folder</a><br><br>

			<input type="radio" name="time_start_timer" value="start_no" <?php echo isset($config['logger']['time_start_timer']) && $config['logger']['time_start_timer'] == "start_no" ? "checked" : "" ?>> No start<br>
			<input type="radio" name="time_start_timer" value="reboot" <?php echo isset($config['logger']['time_start_timer']) && $config['logger']['time_start_timer'] == "reboot" ? "checked" : "" ?>> Start at Boot<br>
			<input type="radio" name="time_start_timer" value="start_on_time" <?php echo isset($config['logger']['time_start_timer']) && $config['logger']['time_start_timer'] == "start_on_time" ? "checked" : "" ?>> Start at times stated below<br>
			Minute (0 - 59)
			<input type="number" name="time_start_min" value="<?php echo isset($config['logger']['time_start_min']) ? $config['logger']['time_start_min'] : 0 ?>"><br>
			Hour (0-23)
			<input type="number" name="time_start_hour" value="<?php echo isset($config['logger']['time_start_hour']) ? $config['logger']['time_start_hour'] : 0 ?>"><br>
			<br><br>
			<input type="radio" name="time_stop_timer" value="stop_no" <?php echo isset($config['logger']['time_stop_timer']) && $config['logger']['time_stop_timer'] == "stop_no" ? "checked" : "" ?>> No stop<br>
			<input type="radio" name="time_stop_timer" value="stop_on_time" <?php echo isset($config['logger']['time_stop_timer']) && $config['logger']['time_stop_timer'] == "stop_on_time" ? "checked" : "" ?>> Stop at times stated below<br>
			Minute (0 - 59)
			<input type="number" name="time_stop_min" value="<?php echo isset($config['logger']['time_stop_min']) ? $config['logger']['time_stop_min'] : 0 ?>"><br>
			Hour (0-23)
			<input type="number" name="time_stop_hour" value="<?php echo isset($config['logger']['time_stop_hour']) ? $config['logger']['time_stop_hour'] : 0 ?>"><br>
			<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Change Settings" name="change_logger_cron" />
		</form>
		<br>
	</div>
</div>

<div id="raw_data" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="POST" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>" >
			<br>
			Gain in DB:<br>
			<input type="number" name="raw_log_log_gain" value="<?php echo isset($config['logger']['raw_log_log_gain']) ? $config['logger']['raw_log_log_gain'] : 20 ?>"><br>
			Gain of the recording device. Higher gain results in more noise. max 49DB
			<br><br>
			Center Frequency in Hz:<br>
			<input type="number" name="raw_center_freq" value="<?php echo isset($config['logger']['raw_center_freq']) ? $config['logger']['raw_center_freq'] : 150100000 ?>"><br>
			Frequency Range to monitor: <br>
			<select name="raw_freq_range">
				<option value="250000" <?php echo isset($config['logger']['raw_freq_range']) && $config['logger']['raw_freq_range'] == "250000" ? "selected" : "" ?>>250kHz</option>
				<option value="1024000" <?php echo isset($config['logger']['raw_freq_range']) && $config['logger']['raw_freq_range'] == "1024000" ? "selected" : "" ?>>1024kHz</option>
			</select> 
			<br>
			Prefix and Record Name:<br>
			<input type="text" name="raw_pre_log_name" value="<?php echo isset($config['logger']['raw_pre_log_name']) ? $config['logger']['raw_pre_log_name'] : "SDR_" ?>">
			<input type="text" name="log_name" value="<?php echo date('Y_m_d_H_i')?>"><br>
			Each record will be given a file name, be careful, the same name will overwrite existing files. You can find the results here: <a href="/sdr/record/">Record Folder</a><br><br>
			<input type="submit" class="w3-btn w3-brown" value="Start" name="sdr_start" />
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="sdr_stop" />
		</form>
		<br>
	</div>
</div>

<?php
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
	if (isset($_POST["change_logger_cron"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash -c 'rtl_433 -f ".$_POST["time_center_freq"]." -s ".$_POST["time_freq_range"]." -t -q -A -l ".$_POST["time_log_level"]." -g " . $_POST["time_log_gain"]. " 2> /home/".$_POST["time_pre_log_name"] ."$(date +%Y_%M_%k_%m_%S)'";
		echo $cmd;
		if($_POST["start_timer"]=="reboot"){
			$change= "@reboot root " .$cmd;
			$search = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash";
			$file_to_replace="/tmp/crontab";
			$result = system("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"", $ret);
			echo "System will now start logger upon start with the following settings: Frequency: ".$_POST["time_center_freq"]." Frequency-Range: ".$_POST["time_freq_range"]." Log-Level: ".$_POST["time_log_level"]." Gain: " . $_POST["time_log_gain"]. " and File-Name: ". $_POST["time_pre_log_name"];
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
			echo "System will now start logger upon start with the following settings: <br><br>Frequency: ".$_POST["time_center_freq"]." Frequency-Range: ".$_POST["time_freq_range"]." Log-Level: ".$_POST["time_log_level"]." Gain: " . $_POST["time_log_gain"]. " and File-Name: ". $_POST["time_pre_log_name"];
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
?>

<!-- Enter text here-->

<?php
	//load footer
	require_once RESOURCES_PATH.'/footer.php';
	//load javascripts
	require_once RESOURCES_PATH.'/javascript.php';
 ?>

</body>
</html>