<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/resources/weblib/w3.css">
<link rel="stylesheet" href="/resources/weblib/css/font-awesome.min.css">

<script>
function setVisibility(menu, label, element) {
	var vis = menu.value==label ? "visible" : "hidden";
	document.getElementById(element).style.visibility = vis;
}
</script>

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
	//define ('confKeys', array('device','log_gain','center_freq','freq_range','pre_log_name','raw_log_log_gain','raw_center_freq','raw_freq_range','raw_pre_log_name','time_center_freq','time_freq_range','time_log_level','time_start_timer','time_start_min','time_start_hour','time_stop_timer','time_stop_min','time_stop_hour','time_pre_log_name', 'threshold' ,'nfft','timestep_factor'));
	define ('confKeys', array('antenna_id_0','antenna_position_N_0','antenna_position_E_0','antenna_orientation_0','antenna_beam_width_0','log_gain_0','center_freq_0','freq_range_0','threshold_0','nfft_0','timestep_0','use_sql_0','db_host_0','db_user_0','db_pass_0','antenna_id_1','antenna_position_N_1','antenna_position_E_1','antenna_orientation_1','antenna_beam_width_1','log_gain_1','center_freq_1','freq_range_1','threshold_1','nfft_1','timestep_1','use_sql_1','db_host_1','db_user_1','db_pass_1','timer_start_0','timer_start_time_0','timer_stop_0','timer_stop_time_0','timer_start_1','timer_start_time_1','timer_stop_1','timer_stop_time_1','timer_mode_0','timer_mode_1'));
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
?>

<!-- Enter text here-->
<div class="w3-bar w3-brown w3-mobile">
	<button class="w3-bar-item w3-button tablink w3-mobile" onclick="openCity(event,'tab_logger_range')">Frequency Range</button>
	<button class="w3-bar-item w3-button tablink w3-mobile " onclick="openCity(event,'tab_logger_single')">Single Frequency</button>
	<button class="w3-bar-item w3-button tablink w3-mobile " onclick="openCity(event,'tab_logger_settings')">Settings</button>
	<button class="w3-bar-item w3-button tablink w3-mobile " onclick="openCity(event,'tab_raw_data_ana')">Raw Data Analyzer</button>
	<button class="w3-bar-item w3-button tablink w3-mobile" onclick="openCity(event,'device_info')">Device Information</button>
</div>

<!-------------------------------- Range Logger -------------------------------------------------------------------->

<div id="tab_logger_range" class="w3-container city w3-row-padding w3-mobile" style="display:none">
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver 0</h3><br>
			<form method="POST" enctype="multipart/form-data" action="">
				<input type="submit" class="w3-btn w3-brown" value="Start" name="log_start_0" />
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="log_stop_0" />
			</form>
			<br>
		</div>
	</div>
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver 1</h3><br>
			<form method="POST" enctype="multipart/form-data" action="">
				<input type="submit" class="w3-btn w3-brown" value="Start" name="log_start_1" />
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="log_stop_1" />
			</form>
			<br>
		</div>
	</div>
	<div class="w3-row">
		<div class="w3-container w3-green w3-round" style="margin-right:8px;margin-left:8px">
			<br><a target="_blank" href="/sdr/record/"><h4>Link to Record Folder</h4></a><br>
		</div>
	</div>
</div>

<!-------------------------------- Single Frequency Logger -------------------------------------------------------------------->

<div id="tab_logger_single" class="w3-container city w3-row-padding" style="display:none">
	<div class=" w3-half">
		<div class="w3-panel w3-round w3-green">
			<h3>Receiver 0</h3><br>
			<form method="POST" enctype="multipart/form-data" action="">
				<input type="submit" class="w3-btn w3-brown" value="Start" name="log_single_start_0" />
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="log_single_stop_0" />
			</form>
			<br>
		</div>
	</div>

	<div class="w3-half">
		<div class="w3-panel w3-round w3-green">
			<h3>Receiver 1</h3><br>
			<form method="POST" enctype="multipart/form-data" action="">
				<input type="submit" class="w3-btn w3-brown" value="Start" name="log_single_start_1" />
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="log_single_stop_1" />
			</form>
			<br>
		</div>
	</div>
	<div class="w3-row">
		<div class="w3-container w3-green w3-round" style="margin-right:8px;margin-left:8px">
			<br><a target="_blank" href="/sdr/record/"><h4>Link to Record Folder</h4></a><br>
		</div>
	</div>

		<!--
		<div class=" w3-container" atyle="margin-top:100px">
		</div>-->
	
</div>

<!------------------------------------------------- Tab Logger Settings ------------------------------------------------->

<div id="tab_logger_settings" class="w3-container city w3-row-padding" style="display:none">
		<div class=" w3-half">
	<div class="w3-container w3-panel w3-round w3-green">
			<form method='POST' enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<h3>Receiver 0</h3><br>
			<button type=button onclick="myAccordion('rec0_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Receiver Settings</h4></button>
			
			<div id="rec0_settings" class="w3-container w3-hide w3-bottombar w3-border-grey">
				<p>
				Gain in dB (default 20):<br>
				<input class="w3-input w3-mobile" style="width:30%" type="number" name="log_gain_0" value="<?php echo isset($config['logger']['log_gain_0']) ? $config['logger']['log_gain_0'] : 20 ?>">
				<small>Gain of the recording device. Higher gain results in more noise. max 49DB</small>
				</p>
				<p>
				Center Frequency in Hz:<br>
				<input class="w3-input w3-mobile" style="width:30%" type="number" name="center_freq_0" value="<?php echo isset($config['logger']['center_freq_0']) ? $config['logger']['center_freq_0'] : 150100000 ?>">
				</p>
				<p>
				Frequency Range to monitor:<br>
				<select class="w3-select w3-mobile"  style="width:30%" name="freq_range_0">
					<option value="250000" <?php echo isset($config['logger']['freq_range_0']) && $config['logger']['freq_range_0'] == "250000" ? "selected" : "" ?>>250kHz</option>
					<option value="1024000" <?php echo isset($config['logger']['freq_range_0']) && $config['logger']['freq_range_0'] == "1024000" ? "selected" : "" ?>>1024kHz</option>
				</select> 
				</p>
			</div>
			
			<button type=button onclick="myAccordion('ant0_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Antenna Settings</h4></button>

			<div id="ant0_settings" class="w3-container w3-hide">
				<p>
					Unique name for this Antenna:<br>
					<input class="w3-input w3-mobile" style="width:70%" type="text" name="antenna_id_0" value="<?php echo isset($config['logger']['antenna_id_0']) ? $config['logger']['antenna_id_0'] : "rteu_r0_"?>">
					<small>This - together with a timestamp - will be used as filename and antenna id in the database.</small>
				</p>
				<div class="w3-half" style = "margin-bottom: 16px">
					<label>Antenna Position in decimal degrees N</label>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="antenna_position_N_0" value="<?php echo isset($config['logger']['antenna_position_N_0']) ? $config['logger']['antenna_position_N_0'] : 1.234?>">
				</div>
				<div class="w3-half" style = "margin-bottom: 16px">
					<label>and E:</label>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="antenna_position_E_0" value="<?php echo isset($config['logger']['antenna_position_E_0']) ? $config['logger']['antenna_position_E_0'] : 5.678?>">
				</div>
				<p>
					Antenna Orientation in degrees (i.e. N=0, E=90, S=180):<br>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="antenna_orientation_0" value="<?php echo isset($config['logger']['antenna_orientation_0']) ? $config['logger']['antenna_orientation_0'] : 42?>">
				</p>
				<p>
					Antenna beam width in degrees:<br>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="antenna_beam_width_0" value="<?php echo isset($config['logger']['antenna_beam_width_0']) ? $config['logger']['antenna_beam_width_0'] : 42?>">
				</p>
				<br>
			</div>
			
			<button type=button onclick="myAccordion('det0_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Detection Settings</h4></button>

			<div id="det0_settings" class="w3-container w3-hide">
				<p>				
					<label for="threshold_0"> Log Level </label>
					<input class="w3-input w3-mobile" style="width:30%" type="text" id="threshold_0" name="threshold_0" value="<?php echo isset($config['logger']['threshold_0']) ? $config['logger']['threshold_0'] : 10 ?>">
				</p>
				<p>
					Number of bins in FFT (default: 400):<br>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="nfft_0" value="<?php echo isset($config['logger']['nfft_0']) ? $config['logger']['nfft_0'] : 400 ?>">
				</p>
				<p>
					Number of samples per FFT (default: 50):<br>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="timestep_0" value="<?php echo isset($config['logger']['timestep_0']) ? $config['logger']['timestep_0'] : 50 ?>">
				</p>
				<br>
			</div>
			
			<button type=button onclick="myAccordion('tim0_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Timer Settings</h4></button>
			<div id="tim0_settings" class="w3-container w3-hide">
				<p>
				<label for="timer_mode_0"> Which detection mode to use</label><br>
				<select class="w3-select w3-mobile" style="width:30%" id="timer_mode_0" name="timer_mode_0">
					<option value="freq_range" <?php echo isset($config['logger']['timer_mode_0']) && $config['logger']['timer_mode_0'] == "freq_range" ? "selected" : "" ?>>Use Frequency Range</option> 
					<option value="single_freq" <?php echo isset($config['logger']['timer_mode_0']) && $config['logger']['timer_mode_0'] == "single_freq" ? "selected" : "" ?>>Use single Frequency</option>
				</select>
				</p>
				<p>
				<label for="timer_start_0"> Automatically start at</label><br>
				<select class="w3-select w3-mobile" style="width:30%" id="timer_start_0" name="timer_start_0">
					<option value="start_no" <?php echo isset($config['logger']['timer_start_0']) && $config['logger']['timer_start_0'] == "start_no" ? "selected" : "" ?>>Don't start automatically</option> 
					<option value="start_boot" <?php echo isset($config['logger']['timer_start_0']) && $config['logger']['timer_start_0'] == "start_boot" ? "selected" : "" ?>>Start on boot</option>
					<option value="start_time" <?php echo isset($config['logger']['timer_start_0']) && $config['logger']['timer_start_0'] == "start_time" ? "selected" : "" ?>>Start at given time</option>
				</select>
				<input class="w3-input w3-mobile" style="width:30%" type="time" name="timer_start_time_0" id="timer_start_time_0" value="<?php echo isset($config['logger']['timer_start_time_0']) ? $config['logger']['timer_start_time_0'] : ""?>">
				</p>
				<p>
				<label for="timer_stop_0"> Automatically stop at</label><br>
				<select class="w3-select w3-mobile" style="width:30%" id="timer_stop_0" name="timer_stop_0">
					<option value="stop_no" <?php echo isset($config['logger']['timer_stop_0']) && $config['logger']['timer_stop_0'] == "stop_no" ? "selected" : ""?>>Don't stop automatically</option> 
					<option value="stop_time" <?php echo isset($config['logger']['timer_stop_0']) && $config['logger']['timer_stop_0'] == "stop_time" ? "selected" : ""?>>Stop at given time</option>
				</select>
				<input class="w3-input w3-mobile" style="width:30%" type="time" name="timer_stop_time_0" id="timer_stop_time_0" value="<?php echo isset($config['logger']['timer_stop_time_0']) ? $config['logger']['timer_stop_time_0'] : ""?>">
				</p>
			</div>
			
			<button type=button onclick="myAccordion('dat0_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Database Settings</h4></button>
			<div id="dat0_settings" class="w3-container w3-hide">
			
				<p>
					Enable logging to SQL database? 
					<span class="w3-tooltip"> <i class="fa fa-info-circle" aria-hidden="false"></i>
						<span class="w3-text w3-small w3-round w3-brown w3-tag">
							Go to Data <i class="fa fa-caret-right" aria-hidden="true"></i> Start <i class="fa fa-caret-right" aria-hidden="true"></i> Database to setup connection details.
						</span>
					</span><br>
					<input class="w3-radio w3-mobile" id="use_sql_0_y" type="radio" name="use_sql_0" value="Yes" <?php echo isset($config['logger']['use_sql_0']) && $config['logger']['use_sql_0'] == "Yes" ? 'checked="checked"' : ''?>>
					<label class="w3-margin-right" for="use_sql_0_y">Yes</label>
					<input class="w3-radio w3-mobile" id="use_sql_0_n" type="radio" name="use_sql_0" value="No" <?php echo isset($config['logger']['use_sql_0']) && $config['logger']['use_sql_0'] == "No" ? 'checked="checked"' : ''?>>
					<label class="w3-margin-right" for="use_sql_0_n">No</label>	
				</p>
			</div>
			<input class="w3-input w3-mobile w3-btn w3-brown" style="width:30%; margin-left:auto; margin-right:10%;" type="submit" value="Change Settings" name="change_logger_settings_0"><br>
			</form>
		</div>
	</div>
	<div class="w3-half">
		<div class="w3-container w3-panel w3-green w3-round">
			<h3>Receiver 1</h3><br>

			<form method='POST' enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
				
				<button type=button onclick="myAccordion('rec1_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Receiver Settings</h4></button>
				<div id="rec1_settings" class="w3-container w3-hide">
					<p>
					Gain in dB (default 20):<br>
					<input class="w3-input w3-mobile" style="width:30%" type="number" name="log_gain_1" value="<?php echo isset($config['logger']['log_gain_1']) ? $config['logger']['log_gain_1'] : 20 ?>">
					<small>Gain of the recording device. Higher gain results in more noise. max 49DB</small>
					</p>
					<p>
					Center Frequency in Hz:<br>
					<input class="w3-input w3-mobile" style="width:30%" type="number" name="center_freq_1" value="<?php echo isset($config['logger']['center_freq_1']) ? $config['logger']['center_freq_1'] : 150100000 ?>">
					</p>
					<p>
					Frequency Range to monitor:<br>
					<select class="w3-select w3-mobile"  style="width:30%" name="freq_range_1">
						<option value="250000" <?php echo isset($config['logger']['freq_range_1']) && $config['logger']['freq_range_1'] == "250000" ? "selected" : "" ?>>250kHz</option>
						<option value="1024000" <?php echo isset($config['logger']['freq_range_1']) && $config['logger']['freq_range_1'] == "1024000" ? "selected" : "" ?>>1024kHz</option>
					</select> 
					</p>
					<br>
				</div>
				
				<button type=button onclick="myAccordion('ant1_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Antenna Settings</h4></button>
				<div id="ant1_settings" class="w3-container w3-hide">
					<p>
					Unique name for this Antenna:<br>
					<input class="w3-input w3-mobile" style="width:80%" type="text" name="antenna_id_1" value="<?php echo isset($config['logger']['antenna_id_1']) ? $config['logger']['antenna_id_1'] : "rteu_r1_"?>">
					<small>This - together with a timestamp - will be used as filename and antenna id in the database.</small>
					</p>
					<div class="w3-half w3-mobile" style = "margin-bottom: 16px">
						<label>Antenna Position in decimal degrees N</label>
						<input class="w3-input w3-mobile" style="width:30%" type="text" name="antenna_position_N_1" value="<?php echo isset($config['logger']['antenna_position_N_1']) ? $config['logger']['antenna_position_N_1'] : 1.234?>">
					</div>
					<div class="w3-half w3-mobile" style = "margin-bottom: 16px">
						<label>and E:</label>
						<input class="w3-input w3-mobile" style="width:30%" type="text" name="antenna_position_E_1" value="<?php echo isset($config['logger']['antenna_position_E_1']) ? $config['logger']['antenna_position_E_1'] : 5.678?>">
					</div>
					<p>
					Antenna Orientation in degrees (i.e. N=0, E=90, S=180):<br>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="antenna_orientation_1" value="<?php echo isset($config['logger']['antenna_orientation_1']) ? $config['logger']['antenna_orientation_1'] : 42?>">
					</p>
					<p>
					Antenna beam width in degrees:<br>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="antenna_beam_width_1" value="<?php echo isset($config['logger']['antenna_beam_width_1']) ? $config['logger']['antenna_beam_width_1'] : 42?>">
					</p>
					<br>
				</div>

				<button type=button onclick="myAccordion('det1_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Detection Settings</h4></button>
				<div id="det1_settings" class="w3-container w3-hide">
				
						<label for="threshold_1"> Log Level </label>
						<input class="w3-input w3-mobile" style="width:30%" type="text" id="threshold_1" name="threshold_1" value="<?php echo isset($config['logger']['threshold_1']) ? $config['logger']['threshold_1'] : 10 ?>">
						
					
					<p>
						Number of bins in FFT (default: 400):<br>
						<input class="w3-input w3-mobile" style="width:30%" type="text" name="nfft_1" value="<?php echo isset($config['logger']['nfft_1']) ? $config['logger']['nfft_1'] : 400 ?>">
					</p>
					<p>
						Number of samples per FFT (default: 50):<br>
						<input class="w3-input w3-mobile" style="width:30%" type="text" name="timestep_1" value="<?php echo isset($config['logger']['timestep_1']) ? $config['logger']['timestep_1'] : 50 ?>">
					</p>
					<br>
				
				</div>
				
				<button type=button onclick="myAccordion('tim1_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Timer Settings</h4></button>
				<div id="tim1_settings" class="w3-container w3-hide">
					<p>
					<label for="timer_mode_1"> Which detection mode to use</label><br>
					<select class="w3-select w3-mobile" style="width:30%" id="timer_mode_1" name="timer_mode_1">
						<option value="freq_range" <?php echo isset($config['logger']['timer_mode_1']) && $config['logger']['timer_mode_1'] == "freq_range" ? "selected" : "" ?>>Use Frequency Range</option> 
						<option value="single_freq" <?php echo isset($config['logger']['timer_mode_1']) && $config['logger']['timer_mode_1'] == "single_freq" ? "selected" : "" ?>>Use single Frequency</option>
					</select>
					</p>
					<p>
					<label for="timer_start_1"> Automatically start at</label><br>
					<select class="w3-select w3-mobile" style="width:30%" id="timer_start_1" name="timer_start_1">
						<option value="start_no" <?php echo isset($config['logger']['timer_start_1']) && $config['logger']['timer_start_1'] == "start_no" ? "selected" : ""?>>Don't start automatically</option> 
						<option value="start_boot" <?php echo isset($config['logger']['timer_start_1']) && $config['logger']['timer_start_1'] == "start_boot" ? "selected" : ""?>>Start on boot</option>
						<option value="start_time" <?php echo isset($config['logger']['timer_start_1']) && $config['logger']['timer_start_1'] == "start_time" ? "selected" : ""?>>Start at given time</option>
					</select>
					<input class="w3-input w3-mobile" style="width:30%" type="time" name="timer_start_time_1" id="timer_start_time_1" value="<?php echo isset($config['logger']['timer_start_time_1']) ? $config['logger']['timer_start_time_1'] : ""?>">
					</p>
					<p>
					<label for="timer_stop_1"> Automatically stop at</label><br>
					<select class="w3-select w3-mobile" style="width:30%" id="timer_stop_1" name="timer_stop_1">
						<option value="stop_no" <?php echo isset($config['logger']['timer_stop_1']) && $config['logger']['timer_stop_1'] == "stop_no" ? "selected" : ""?>>Don't stop automatically</option> 
						<option value="stop_time" <?php echo isset($config['logger']['timer_stop_1']) && $config['logger']['timer_stop_1'] == "stop_time" ? "selected" : ""?>>Stop at given time</option>
					</select>
					<input class="w3-input w3-mobile" style="width:30%" type="time" name="timer_stop_time_1" id="timer_stop_time_1" value="<?php echo isset($config['logger']['timer_stop_time_1']) ? $config['logger']['timer_stop_time_1'] : ""?>">
					</p>
				</div>
				
				<button type=button onclick="myAccordion('dat1_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Database Settings</h4></button>
				<div id="dat1_settings" class="w3-container w3-hide">
					<p>
					Enable logging to SQL database? 
					<span class="w3-tooltip"> <i class="fa fa-info-circle" aria-hidden="false"></i>
						<span class="w3-text w3-small w3-round w3-brown w3-tag">
							Go to Data <i class="fa fa-caret-right" aria-hidden="true"></i> Start <i class="fa fa-caret-right" aria-hidden="true"></i> Database to setup connection details.
						</span>
					</span><br>
					<input class="w3-radio w3-mobile w3-padding-24" id="use_sql_1_y" type="radio" name="use_sql_1" value="Yes" <?php echo isset($config['logger']['use_sql_1']) && $config['logger']['use_sql_1'] == "Yes" ? 'checked="checked"' : ''?>>
					<label class=" w3-margin-right" for="use_sql_1_y">Yes</label>
					<input class="w3-radio w3-mobile" id="use_sql_1_n" type="radio" name="use_sql_1" value="No" <?php echo isset($config['logger']['use_sql_1']) && $config['logger']['use_sql_1'] == "No" ? 'checked="checked"' : ''?>>
					<label class="w3-margin-right" for="use_sql_1_n">No</label>	
					</p>
				</div>		
				<input class="w3-input w3-mobile w3-btn w3-brown" style="width:30%; margin-left:auto; margin-right:10%;" type="submit" value="Change settings" name="change_logger_settings_1"><br>				
			</form>
		</div>
	</div>
	<div class="w3-rest w3-center w3-panel w3-green w3-round" style="margin-right:8px; margin-left:8px">
		<form method='POST' enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<br>
			<input type="submit" class="w3-btn w3-brown" value="Compile Raspi 3" name="compile"/>
			<input type="submit" class="w3-btn w3-brown" value="Compile Raspi Zero" name="compile_raspi_zero"/>
			<br><br>
		</form>
	</div>
</div>

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