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
	define ('confSection', 'logger');
	define ('confKeys', array('device','log_gain','center_freq','freq_range','pre_log_name','raw_log_log_gain','raw_center_freq','raw_freq_range','raw_pre_log_name','time_center_freq','time_freq_range','time_log_level','time_start_timer','time_start_min','time_start_hour','time_stop_timer','time_stop_min','time_stop_hour','time_pre_log_name', 'threshold' ,'nfft','timestep_factor'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
?>

<!-- Enter text here-->
<div class="w3-bar w3-brown w3-mobile">
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('tab_logger_range')">Frequency Range</button>
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('tab_logger')">Single Frequency</button>
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('tab_logger_timer')">Settings</button>
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('tab_logger_settings')">Settings_new</button>
	<!--
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('tab_raw_data')">Raw Data Recorder</button>-->
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('tab_raw_data_ana')">Raw Data Analyzer</button>
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('device_info')">Device Information</button>
</div>

<!-------------------------------- Logger Settings-------------------------------------------------------------------->

<div id="tab_logger_range" class="w3-container city w3-row-padding w3-mobile" style="display:none">
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver 0</h3><br>
			<form method="POST" enctype="multipart/form-data" action="">
				<input type="submit" class="w3-btn w3-brown" value="Start" name="log_start" />
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="log_stop" />
			</form>
			<br>
		</div>
	</div>
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver 1</h3><br>
			<form method="POST" enctype="multipart/form-data" action="">
				<input type="submit" class="w3-btn w3-brown" value="Start" name="log_start" />
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="log_stop" />
			</form>
			<br>
		</div>
	</div>
</div>


<!-------------------------------- Logger-------------------------------------------------------------------
<div id="tab_logger_settings" class="w3-container city w3-row-padding w3-mobile" style="display:none">
	<div class="w3-half w3-panel w3-green w3-round">
		<h3>Logger Analyzer settings - Receiver 0</h3><br>
		<form method="POST" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>" >
				Gain in DB:<br>
				<input type="number" name="log_gain" value="<?php echo isset($config['logger']['log_gain'][$config['logger']['device']]) ? $config['logger']['log_gain'][$config['logger']['device']] : 20 ?>"><br>
				Gain of the recording device. Higher gain results in more noise. max 49DB
				<br><br>
				Center Frequency in Hz:<br>
				<input type="number" name="center_freq" value="<?php echo isset($config['logger']['center_freq'][$config['logger']['device']]) ? $config['logger']['center_freq'][$config['logger']['device']] : 150100000 ?>"><br>
				Frequency Range to monitor: <br>
				<select name="freq_range">
					<option value="250000" <?php echo isset($config['logger']['freq_range'][$config['logger']['device']]) && $config['logger']['freq_range'][$config['logger']['device']] == "250000" ? "selected" : "" ?>>250kHz</option>
					<option value="1024000" <?php echo isset($config['logger']['freq_range'][$config['logger']['device']]) && $config['logger']['freq_range'][$config['logger']['device']] == "1024000" ? "selected" : "" ?>>1024kHz</option>
				</select> 
				<br>
				Detection - Threshold (default 10):<br>
				<input type="text" name="threshold" value="<?php echo isset($config['logger']['threshold'][$config['logger']['device']]) ? $config['logger']['threshold'][$config['logger']['device']] : 10 ?>"><br>
				<br>
				Detection - Number of bins in FFT (default: 400):<br>
				<input type="text" name="nfft" value="<?php echo isset($config['logger']['nfft'][$config['logger']['device']]) ? $config['logger']['nfft'][$config['logger']['device']] : 400 ?>"><br>
				<br>
				Detection - Timestep as Fraction of Number of bins (default: 8):<br>
				<input type="text" name="timestep_factor" value="<?php echo isset($config['logger']['timestep_factor'][$config['logger']['device']]) ? $config['logger']['timestep_factor'][$config['logger']['device']] : 8 ?>"><br>
				<br> i.e. Timestep is No of bins divided by this.
				<br>
				Prefix and Record Name:<br>
				<input type="text" name="pre_log_name" value="<?php echo isset($config['logger']['pre_log_name'][$config['logger']['device']]) ? $config['logger']['pre_log_name'][$config['logger']['device']] : "rteu" ?>">
				<input type="text" name="log_name" value="<?php echo date('Y_m_d_H_i')?>"><br>
				Each record will be given a file name, be careful, the same name will overwrite existing files. You can find the results here: <a href="/sdr/record/">Record Folder</a><br><br>
				
			<input type="submit" class="w3-btn w3-brown" value="Start Receiver <?php echo $config['logger']['device']+1;?>" name="log_start" />
			<input type="submit" class="w3-btn w3-brown" value="Stop Receiver <?php echo $config['logger']['device']+1;?>" name="log_stop" />
		</form>
		<br>
	</div>
</div>

<!-------------------------------- Logger Settings-------------------------------------------------------------------

<div id="tab_logger_settings" class="w3-container city w3-row-padding w3-mobile" style="display:none">
	<div class="w3-half w3-panel w3-green w3-round">
		<h3>Logger Analyzer settings - Receiver 0</h3><br>
		<form method="POST" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>" >
				Gain in DB:<br>
				<input type="number" name="log_gain" value="<?php echo isset($config['logger']['log_gain'][$config['logger']['device']]) ? $config['logger']['log_gain'][$config['logger']['device']] : 20 ?>"><br>
				Gain of the recording device. Higher gain results in more noise. max 49DB
				<br><br>
				Center Frequency in Hz:<br>
				<input type="number" name="center_freq" value="<?php echo isset($config['logger']['center_freq'][$config['logger']['device']]) ? $config['logger']['center_freq'][$config['logger']['device']] : 150100000 ?>"><br>
				Frequency Range to monitor: <br>
				<select name="freq_range">
					<option value="250000" <?php echo isset($config['logger']['freq_range'][$config['logger']['device']]) && $config['logger']['freq_range'][$config['logger']['device']] == "250000" ? "selected" : "" ?>>250kHz</option>
					<option value="1024000" <?php echo isset($config['logger']['freq_range'][$config['logger']['device']]) && $config['logger']['freq_range'][$config['logger']['device']] == "1024000" ? "selected" : "" ?>>1024kHz</option>
				</select> 
				<br>
				Detection - Threshold (default 10):<br>
				<input type="text" name="threshold" value="<?php echo isset($config['logger']['threshold'][$config['logger']['device']]) ? $config['logger']['threshold'][$config['logger']['device']] : 10 ?>"><br>
				<br>
				Detection - Number of bins in FFT (default: 400):<br>
				<input type="text" name="nfft" value="<?php echo isset($config['logger']['nfft'][$config['logger']['device']]) ? $config['logger']['nfft'][$config['logger']['device']] : 400 ?>"><br>
				<br>
				Detection - Timestep as Fraction of Number of bins (default: 8):<br>
				<input type="text" name="timestep_factor" value="<?php echo isset($config['logger']['timestep_factor'][$config['logger']['device']]) ? $config['logger']['timestep_factor'][$config['logger']['device']] : 8 ?>"><br>
				<br> i.e. Timestep is No of bins divided by this.
				<br>
				Prefix and Record Name:<br>
				<input type="text" name="pre_log_name" value="<?php echo isset($config['logger']['pre_log_name'][$config['logger']['device']]) ? $config['logger']['pre_log_name'][$config['logger']['device']] : "rteu" ?>">
				<input type="text" name="log_name" value="<?php echo date('Y_m_d_H_i')?>"><br>
				Each record will be given a file name, be careful, the same name will overwrite existing files. You can find the results here: <a href="/sdr/record/">Record Folder</a><br><br>
				
			<input type="submit" class="w3-btn w3-brown" value="Start Receiver <?php echo $config['logger']['device']+1;?>" name="log_start" />
			<input type="submit" class="w3-btn w3-brown" value="Stop Receiver <?php echo $config['logger']['device']+1;?>" name="log_stop" />
		</form>
		<br>
	</div>
</div>

<!------------------------------------------------- Tab Logger Settings ------------------------------------------------->

<div id="tab_logger_settings" class="w3-container city w3-row-padding" style="display:none">
	<div class="w3-half">
		<div class="w3-container w3-panel w3-green w3-round">
			<h3>Logger settings - Receiver 1</h3><br>

			<form method='POST' enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
				<h4> Antenna Settings </h4>
				<p>
					Unique name for this Antenna:<br>
					<input class="w3-input" type="text" name="antenna_id_0" value="<?php echo isset($config['logger']['antenna_id_0']) ? $config['logger']['antenna_id_0'] : rteu_r0_?>">
					<small>This - together with a timestamp - will be used as filename and antenna id in the database.</small>
				</p>
				<p>
					Antenna Position in decimal degrees N and E:<br>
					<div class="w3-half">
					<input class="w3-input w3-animate-input" style="width:30%" type="text" name="antenna_position_N_0" value="<?php echo isset($config['logger']['antenna_position_N_0']) ? $config['logger']['antenna_position_N_0'] : 1.234?>">
					<label>°N</label>
					</div>
					<div class="w3-half">
					<input class="w3-input w3-animate-input" style="width:30%" type="text" name="antenna_position_E_0" value="<?php echo isset($config['logger']['antenna_position_E_0']) ? $config['logger']['antenna_position_E_0'] : 5.678?>">
					<label>°E</label>
					</div>
				</p>
				<p>
					Antenna Orientation in degrees (i.e. N=0, E=90, S=180):<br>
					<input class="w3-input w3-animate-input" style="width:30%" type="text" name="antenna_orientation_0" value="<?php echo isset($config['logger']['antenna_orientation_0']) ? $config['logger']['antenna_orientation_0'] : 42?>">
				</p>
				<br>
				<h4> Receiver Settings</h4>
				<p>
				Gain in dB (default 20):<br>
				<input class="w3-input w3-animate-input" style="width:30%" type="number" name="log_gain_0" value="<?php echo isset($config['logger']['log_gain_0']) ? $config['logger']['log_gain_0'] : 20 ?>">
				<small>Gain of the recording device. Higher gain results in more noise. max 49DB</small>
				</p>
				<p>
				Center Frequency in Hz:<br>
				<input class="w3-input w3-animate-input" style="width:30%" type="number" name="center_freq_0" value="<?php echo isset($config['logger']['center_freq_0']) ? $config['logger']['center_freq_0'] : 150100000 ?>">
				</p>
				<p>
				Frequency Range to monitor:<br>
				<select class="w3-select" name="freq_range_0">
					<option value="250000" <?php echo isset($config['logger']['freq_range_0']) && $config['logger']['freq_range_0'] == "250000" ? "selected" : "" ?>>250kHz</option>
					<option value="1024000" <?php echo isset($config['logger']['freq_range_0']) && $config['logger']['freq_range_0'] == "1024000" ? "selected" : "" ?>>1024kHz</option>
				</select> 
				</p>
				<br>
				<h4> Detection Settings </h4>
				
					<label for="threshold_0"> Log Level </label>
					<input class="w3-input" class="w3-input" type="text" id="threshold_0" name="threshold_0" value="<?php echo isset($config['logger']['threshold_0']) ? $config['logger']['threshold_0'] : 10 ?>">
					
				
				<p>
					Number of bins in FFT (default: 400):<br>
					<input class="w3-input" type="text" name="nfft_0" value="<?php echo isset($config['logger']['nfft_0']) ? $config['logger']['nfft_0'] : 400 ?>">
				</p>
				<p>
					Number of samples per FFT (default: 50):<br>
					<input class="w3-input" type="text" name="timestep_0" value="<?php echo isset($config['logger']['timestep_factor_0']) ? $config['logger']['timestep_factor_0'] : 50 ?>">
				</p>
				<br>
				<h4> Logging Settings </h4>
				<p>
					Enable logging to SQL database:
					<input class="w3-input" type="checkbox" name="use_sql_0" value="use_sql_0" <?php echo isset($config['logger']['use_sql_0']) && $config['logger']['use_sql_0'] == "1" ? "checked" : "" ?>>
				</p>
				<p>
					Adress of SQL Server:
					<input class="w3-input" type="text" name="db_host_0" value="<?php echo isset($config['logger']['db_host_0']) ? $config['logger']['db_host_0'] : "127.0.0.1:3306" ?>">
				</p>
				<p>			
					User:
					<input class="w3-input" type="text" name="db_user_0" value="<?php echo isset($config['logger']['db_user_0']) ? $config['logger']['db_user_0'] : "root" ?>">
				</p>
				<p>		
					Password:
					<input class="w3-input w3-animate-input" style="30%" type="text" name="db_pass_0" value="<?php echo isset($config['logger']['db_pass_0']) ? $config['logger']['db_pass_0'] : "" ?>">
				</p>
				<input class="w3-input" type="submit" class="w3-btn w3-brown" value="Change Settings" name="change_logger_settings_0" />
			</form>
		</div>
	</div>
	<div class="w3-half">
		<div class="w3-container w3-panel w3-green w3-round">
			<h3>Logger settings - Receiver 2</h3><br>

			<form method='POST' enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
				<h4> Antenna Settings </h4>
				<p>
					Unique name for this Antenna:<br>
					<input class="w3-input" type="text" name="antenna_id_1" value="<?php echo isset($config['logger']['antenna_id_1']) ? $config['logger']['antenna_id_1'] : rteu_r0_?>">
					<input class="w3-input" type="text" name="log_name" value="<?php echo date('Y_m_d_H_i')?>"><br>
					This - together with a timestamp - will be used as filename and antenna id in the database.
				</p>
				<p>
					Antenna Position in decimal degrees N and E:<br>
					<input class="w3-input" type="text" name="antenna_position_N_1" value="<?php echo isset($config['logger']['antenna_position_N_1']) ? $config['logger']['antenna_position_N_1'] : 1.234?>"> °N  
					<input class="w3-input" type="text" name="antenna_position_E_1" value="<?php echo isset($config['logger']['antenna_position_E_1']) ? $config['logger']['antenna_position_E_1'] : 5.678?>"> °E
				</p>
				<p>
					Antenna Orientation in degrees (i.e. N=0, E=90, S=180):<br>
					<input class="w3-input" type="text" name="antenna_orientation_1" value="<?php echo isset($config['logger']['antenna_orientation_1']) ? $config['logger']['antenna_orientation_1'] : 42?>"> °
				</p>
				<br>
				<h4> Receiver Settings</h4>
				<p>
				Gain in dB (default 20):<br>
				<input class="w3-input" type="number" name="log_gain_1" value="<?php echo isset($config['logger']['log_gain_1']) ? $config['logger']['log_gain_1'] : 20 ?>"><br>
				Gain of the recording device. Higher gain results in more noise. max 49DB
				</p>
				<p>
				Center Frequency in Hz:<br>
				<input class="w3-input" type="number" name="center_freq_1" value="<?php echo isset($config['logger']['center_freq_1']) ? $config['logger']['center_freq_1'] : 150100000 ?>"><br>
				</p>
				<p>
				Frequency Range to monitor: <br>
				<select name="freq_range_1">
					<option value="250000" <?php echo isset($config['logger']['freq_range_1']) && $config['logger']['freq_range_1'] == "250000" ? "selected" : "" ?>>250kHz</option>
					<option value="1024000" <?php echo isset($config['logger']['freq_range_1']) && $config['logger']['freq_range_1'] == "1024000" ? "selected" : "" ?>>1024kHz</option>
				</select> 
				</p>
				<br>
				<h4> Detection Settings </h4>
				
					<input class="w3-input" class="w3-input" type="text" id="threshold_1" name="threshold_1" value="<?php echo isset($config['logger']['threshold_1']) ? $config['logger']['threshold_1'] : 10 ?>">
					<label for="threshold_1"> Log Level </label>
					
				
				<p>
					Number of bins in FFT (default: 400):<br>
					<input class="w3-input" type="text" name="nfft_1" value="<?php echo isset($config['logger']['nfft_1']) ? $config['logger']['nfft_1'] : 400 ?>">
				</p>
				<p>
					Number of samples per FFT (default: 50):<br>
					<input class="w3-input" type="text" name="timestep_1" value="<?php echo isset($config['logger']['timestep_factor_1']) ? $config['logger']['timestep_factor_1'] : 50 ?>">
				</p>
				<br>
				<h4> Logging Settings </h4>
				<p>
					Enable logging to SQL database:
					<input class="w3-input" type="checkbox" name="use_sql_1" value="use_sql_1" <?php echo isset($config['logger']['use_sql_1']) && $config['logger']['use_sql_1'] == "1" ? "checked" : "" ?>>
				</p>
				<p>
					Adress of SQL Server:
					<input class="w3-input" type="text" name="db_host_1" value="<?php echo isset($config['logger']['db_host_1']) ? $config['logger']['db_host_1'] : "127.0.0.1:3306" ?>">
				</p>
				<p>			
					User:
					<input class="w3-input" type="text" name="db_user_1" value="<?php echo isset($config['logger']['db_user_1']) ? $config['logger']['db_user_1'] : "root" ?>">
				</p>
				<p>		
					Password:
					<input class="w3-input w3-animate-input" style="30%" type="text" name="db_pass_1" value="<?php echo isset($config['logger']['db_pass_1']) ? $config['logger']['db_pass_1'] : "" ?>">
				</p>
				<input class="w3-input" type="submit" class="w3-btn w3-brown" value="Change Settings" name="change_logger_settings_1" />
			</form>
		</div>
	</div>
</div>

<!-------------------------------- Logger Timer -------------------------------------------------------------------->

<div id="tab_logger_timer" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
<!--		<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF'];?>">
			<br>
			<select name="device">
				<option value=1 >Receiver 1</option>
				<option value=2 >Receiver 2</option>
			</select> 
			<input type='submit' class='w3-btn w3-brown' value='Switch receiver' name='change_device_tab_logger_timer'/>
			<br><br>
		</form>
-->
		<h3>Logger settings - Receiver <?php echo $config['logger']['device']+1;?></h3><br>

		<form method='POST' enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			Gain in DB:<br>
			<input type="number" name="time_log_gain" value="<?php echo isset($config['logger']['time_log_gain'][$config['logger']['device']]) ? $config['logger']['time_log_gain'][$config['logger']['device']] : 20 ?>"><br>
			Gain of the recording device. Higher gain results in more noise. max 49DB
			<br><br>
			Center Frequency in Hz:<br>
			<input type="number" name="time_center_freq" value="<?php echo isset($config['logger']['time_center_freq'][$config['logger']['device']]) ? $config['logger']['time_center_freq'][$config['logger']['device']] : 150100000 ?>"><br>
			Frequency Range to monitor: <br>
			<select name="time_freq_range">
				<option value="250000" <?php echo isset($config['logger']['time_freq_range'][$config['logger']['device']]) && $config['logger']['time_freq_range'][$config['logger']['device']] == "250000" ? "selected" : "" ?>>250kHz</option>
				<option value="1024000" <?php echo isset($config['logger']['time_freq_range'][$config['logger']['device']]) && $config['logger']['time_freq_range'][$config['logger']['device']] == "1024000" ? "selected" : "" ?>>1024kHz</option>
			</select> 
			<br>
			Log Detection Level:<br>
			<input type="text" name="time_log_level" value="<?php echo isset($config['logger']['time_log_level'][$config['logger']['device']]) ? $config['logger']['time_log_level'][$config['logger']['device']] : 1 ?>"><br>
			0 means automatic - level up to 16384 - the tricky part is setting a good log level compared to the gain: try and error
			<br><br>
			Prefix and Record Name:<br>
			<input type="text" name="time_pre_log_name" value="<?php echo isset($config['logger']['time_pre_log_name'][$config['logger']['device']]) ? $config['logger']['time_pre_log_name'][$config['logger']['device']] : "rteu_" ?>">
			Each record will be given a file name, be careful, the same name will overwrite existing files. You can find the results here: <a href="/sdr/record/">Record Folder</a><br><br>

			<input type="radio" name="time_start_timer" value="start_no" <?php echo isset($config['logger']['time_start_timer'][$config['logger']['device']]) && $config['logger']['time_start_timer'][$config['logger']['device']] == "start_no" ? "checked" : "" ?>> No start<br>
			<input type="radio" name="time_start_timer" value="reboot" <?php echo isset($config['logger']['time_start_timer'][$config['logger']['device']]) && $config['logger']['time_start_timer'][$config['logger']['device']] == "reboot" ? "checked" : "" ?>> Start at Boot<br>
			<input type="radio" name="time_start_timer" value="start_on_time" <?php echo isset($config['logger']['time_start_timer'][$config['logger']['device']]) && $config['logger']['time_start_timer'][$config['logger']['device']] == "start_on_time" ? "checked" : "" ?>> Start at times stated below<br>
			Minute (0 - 59)
			<input type="number" name="time_start_min" value="<?php echo isset($config['logger']['time_start_min'][$config['logger']['device']]) ? $config['logger']['time_start_min'][$config['logger']['device']] : 0 ?>"><br>
			Hour (0-23)
			<input type="number" name="time_start_hour" value="<?php echo isset($config['logger']['time_start_hour'][$config['logger']['device']]) ? $config['logger']['time_start_hour'][$config['logger']['device']] : 0 ?>"><br>
			<br><br>
			<input type="radio" name="time_stop_timer" value="stop_no" <?php echo isset($config['logger']['time_stop_timer'][$config['logger']['device']]) && $config['logger']['time_stop_timer'][$config['logger']['device']] == "stop_no" ? "checked" : "" ?>> No stop<br>
			<input type="radio" name="time_stop_timer" value="stop_on_time" <?php echo isset($config['logger']['time_stop_timer'][$config['logger']['device']]) && $config['logger']['time_stop_timer'][$config['logger']['device']] == "stop_on_time" ? "checked" : "" ?>> Stop at times stated below<br>
			Minute (0 - 59)
			<input type="number" name="time_stop_min" value="<?php echo isset($config['logger']['time_stop_min'][$config['logger']['device']]) ? $config['logger']['time_stop_min'][$config['logger']['device']] : 0 ?>"><br>
			Hour (0-23)
			<input type="number" name="time_stop_hour" value="<?php echo isset($config['logger']['time_stop_hour'][$config['logger']['device']]) ? $config['logger']['time_stop_hour'][$config['logger']['device']] : 0 ?>"><br>
			<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Change Settings" name="change_logger_cron" />
		</form>
		<br>
	</div>
</div>

<!--
<div id="tab_raw_data" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="POST" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>" >
			<br>
			Gain in DB:<br>
			<input type="number" name="raw_log_log_gain" value="<?php echo isset($config['logger']['raw_log_log_gain'][$config['logger']['device']]) ? $config['logger']['raw_log_log_gain'][$config['logger']['device']] : 20 ?>"><br>
			Gain of the recording device. Higher gain results in more noise. max 49DB
			<br><br>
			Center Frequency in Hz:<br>
			<input type="number" name="raw_center_freq" value="<?php echo isset($config['logger']['raw_center_freq'][$config['logger']['device']]) ? $config['logger']['raw_center_freq'][$config['logger']['device']] : 150100000 ?>"><br>
			Frequency Range to monitor: <br>
			<select name="raw_freq_range">
				<option value="250000" <?php echo isset($config['logger']['raw_freq_range'][$config['logger']['device']]) && $config['logger']['raw_freq_range'][$config['logger']['device']] == "250000" ? "selected" : "" ?>>250kHz</option>
				<option value="1024000" <?php echo isset($config['logger']['raw_freq_range'][$config['logger']['device']]) && $config['logger']['raw_freq_range'][$config['logger']['device']] == "1024000" ? "selected" : "" ?>>1024kHz</option>
			</select> 
			<br>
			Prefix and Record Name:<br>
			<input type="text" name="raw_pre_log_name" value="<?php echo isset($config['logger']['raw_pre_log_name'][$config['logger']['device']]) ? $config['logger']['raw_pre_log_name'][$config['logger']['device']] : "SDR_" ?>">
			<input type="text" name="log_name" value="<?php echo date('Y_m_d_H_i')?>"><br>
			Each record will be given a file name, be careful, the same name will overwrite existing files. You can find the results here: <a href="/sdr/record/">Record Folder</a><br><br>
			<input type="submit" class="w3-btn w3-brown" value="Start" name="sdr_start" />
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="sdr_stop" />
		</form>
		<br>
	</div>
</div>
-->
<!---->
<div id="tab_raw_data_ana" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="POST" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>" >
			<br>
			Select one file:<br>
			<select name="analyzer_file">
				<option value="" selected="selected">-----</option>
				<?php 
					foreach(glob(dirname(__FILE__) . '/record/*') as $filename){
					$filename = basename($filename);
					echo "<option value='" . $filename . "'>".$filename."</option>";
				}
				?>
			</select> 
			<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Compile" name="compile"/>
			<input type="submit" class="w3-btn w3-brown" value="Start" name="start_analyze"/>
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="stop_analyze"/>
		</form>
		<br>
	</div>
</div>

<div id="device_info" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br>
		<form method="post" enctype="multipart/form-data">
			<input type='submit' class='w3-btn w3-brown' value='Update' name='update_device_info'/>
			<br><br>
		</form>
		<?php 
		if(shell_exec("sudo docker inspect -f {{.State.Running}} $(sudo docker ps -a -q --filter name=sdr-d0)")){
			echo "<span class='w3-tag w3-red w3-xlarge'>Radio 1 running</span> \n \n";
		}
		else{
			echo "<span class='w3-tag w3-green w3-xlarge'>Radio 1 not running</span> \n \n";
		}
		?>
		<br><br>
		<?php 
		if(shell_exec("sudo docker inspect -f {{.State.Running}} $(sudo docker ps -a -q --filter name=sdr-d1)")){
			echo "<span class='w3-tag w3-red w3-xlarge'>Radio 2 running</span> \n \n";
		}
		else{
			echo "<span class='w3-tag w3-green w3-xlarge'>Radio 2 not running</span> \n \n";
		}
		?>
		<br><br>
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