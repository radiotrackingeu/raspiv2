<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/resources/weblib/w3.css">
<link rel="stylesheet" href="/resources/weblib/css/font-awesome.min.css">

<link rel="stylesheet" href="/resources/additional.css">

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
	
	$GLOBALS["num_rec"] = exec("lsusb | grep -c -e '0bda:2838'");
	$GLOBALS["num_rec"] = 4;
	$tmparr=array();
	$valuearr=array('antenna_id_','antenna_position_N_','antenna_position_E_','antenna_orientation_','antenna_beam_width_','log_gain_','center_freq_','freq_range_','threshold_','nfft_','timestep_','use_sql_','db_host_','db_user_','db_pass_');
	for ($i=0; $i<4; $i++) {
			foreach($valuearr as $var) {
				$tmparr[]=$var.$i;
			}
		}
	//define config section and items.
	define ('confSection', 'logger');
	define ('confKeys', $tmparr);
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
?>

<!-- Enter text here-->
<div class="w3-bar w3-brown w3-mobile">
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'tab_logger_range')">Frequency Range</button>
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'tab_logger_single')">Single Frequency</button>
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'tab_logger_settings')">Settings</button>
</div>

<!-------------------------------- Range Logger -------------------------------------------------------------------->

<div id="tab_logger_range" class="city w3-mobile" style="display:none">
	<div class= "w3-row-padding">
		<?php for ($i=0; $i<$GLOBALS["num_rec"]; $i++): ?>
			<div class="w3-half">
				<div class="w3-panel w3-green w3-round">
					<h3>Receiver <?=$i?></h3><br>
					Range: <?php echo ($config['logger']['center_freq_'.$i]-$config['logger']['freq_range_'.$i]/2)/1000000?> MHz to <?php echo ($config['logger']['center_freq_'.$i]+$config['logger']['freq_range_'.$i]/2)/1000000?> MHz
					<br>
					Gain: <?php echo $config['logger']['log_gain_'.$i]?> dB
					<br>
					Threshold: <?php echo $config['logger']['threshold_'.$i]?> dB above Noise
					<br>
					<form class="w3-right-align" method="POST" enctype="multipart/form-data" action="">
						<input type="submit" class="w3-btn w3-brown" value="Start" name="log_start_<?=$i?>" />
						<input type="submit" class="w3-btn w3-brown" value="Stop" name="log_stop_<?=$i?>" />
					</form>
					<br>
				</div>
			</div>
		<?php endfor;?>
	</div>
	<div class="w3-row-padding">
		<div class="w3-half">
			<div class=" w3-panel w3-green w3-round">
				<br><a target="_blank" href="/sdr/record/"><h4>Link to Record Folder</h4></a><br>
				<?php for ($i=0; $i<$GLOBALS["num_rec"]; $i++): ?>
				<?php 
				if(shell_exec("sudo docker inspect -f {{.State.Running}} $(sudo docker ps -a -q --filter name=sdr-d".$i.")")){
					echo "<span class='w3-tag w3-red w3-large'>Radio ".$i." running</span> \n \n";
				}
				else{
					echo "<span class='w3-tag w3-green w3-large'>Radio ".$i." not running</span> \n \n";
				}
				?>
				<br><br>
				<?php endfor;?>
				<form method="post" enctype="multipart/form-data">
					<input type='submit' class='w3-btn w3-brown' value='Update Receiver Status' name='update_device_info_fr'/>
					<br><br>
				</form>
				
			</div>
		</div>
	</div>
</div>
<!-------------------------------- Single Frequency Logger -------------------------------------------------------------------->

<div id="tab_logger_single" class="city w3-mobile" style="display:none">
<div class="w3-row-padding">
	<?php for ($i=0; $i<$GLOBALS["num_rec"]; $i++): ?>
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver <?=$i?></h3><br>
			Frequency: <?php echo ($config['logger']['center_freq_'.$i]/1000000)?> MHz
			<br>
			Gain: <?php echo $config['logger']['log_gain_'.$i]?> dB
			<br>
			Threshold: <?php echo $config['logger']['threshold_'.$i]?> dB above Noise
			<br>
			<form class="w3-right-align" method="POST" enctype="multipart/form-data" action="">
				<input type="submit" class="w3-btn w3-brown" value="Start" name="log_single_start_<?=$i?>" />
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="log_single_stop_<?=$i?>" />
			</form>
			<br>
		</div>
	</div>
	<?php endfor;?>
</div>
	<div class="w3-row-padding">
		<div class="w3-half">
			<div class=" w3-panel w3-green w3-round">
				<br><a target="_blank" href="/sdr/record/"><h4>Link to Record Folder</h4></a><br>
				<?php for ($i=0; $i<$GLOBALS["num_rec"]; $i++): ?>
				<?php 
				if(shell_exec("sudo docker inspect -f {{.State.Running}} $(sudo docker ps -a -q --filter name=sdr-d".$i.")")){
					echo "<span class='w3-tag w3-red w3-large'>Radio ".$i." running</span> \n \n";
				}
				else{
					echo "<span class='w3-tag w3-green w3-large'>Radio ".$i." not running</span> \n \n";
				}
				?>
				<br><br>
				<?php endfor;?>
				<form method="post" enctype="multipart/form-data">
					<input type='submit' class='w3-btn w3-brown' value='Update Receiver Status' name='update_device_info_fr'/>
					<br><br>
				</form>
			</div>
		</div>
	</div>
</div>

<!------------------------------------------------- Tab Logger Settings ------------------------------------------------->

<div id="tab_logger_settings" class="city w3-mobile" style="display:none">
<div class="w3-row-padding">
	<?php for ($i=0; $i<4; $i++): ?>
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<form method='POST' enctype="multipart/form-data" action="<?php update_Config($config);?>">
			<h3>Receiver <?=$i?></h3><br>
			<button type=button onclick="myAccordion('rec<?=$i?>_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Receiver Settings</h4></button>
			
			<div id="rec<?=$i?>_settings" class="w3-container w3-hide">
				<p>
				Gain in dB (default 20):<br>
				<input class="w3-input w3-mobile" style="width:30%" type="number" name="log_gain_<?=$i?>" value="<?php echo isset($config['logger']['log_gain_'.$i]) ? $config['logger']['log_gain_'.$i] : 20 ?>">
				<small>Gain of the recording device. Higher gain results in more noise. max 49DB</small>
				</p>
				<p>
				Center Frequency in Hz:<br>
				<input class="w3-input w3-mobile" style="width:30%" type="number" name="center_freq_<?=$i?>" value="<?php echo isset($config['logger']['center_freq_'.$i]) ? $config['logger']['center_freq_'.$i] : 150100000 ?>">
				</p>
				<p>
				Frequency Range to monitor:<br>
				<select class="w3-select w3-mobile"  style="width:30%" name="freq_range_<?=$i?>">
					<option value="250000" <?php echo isset($config['logger']['freq_range_'.$i]) && $config['logger']['freq_range_'.$i] == "250000" ? "selected" : "" ?>>250kHz</option>
					<option value="1024000" <?php echo isset($config['logger']['freq_range_'.$i]) && $config['logger']['freq_range_'.$i] == "1024000" ? "selected" : "" ?>>1024kHz</option>
				</select> 
				</p>
			</div>
			
			<button type=button onclick="myAccordion('ant<?=$i?>_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Antenna Settings</h4></button>

			<div id="ant<?=$i?>_settings" class="w3-container w3-hide">
				<p>
					Unique name for this Antenna:<br>
					<input class="w3-input w3-mobile" style="width:70%" type="text" name="antenna_id_<?=$i?>" value="<?php echo isset($config['logger']['antenna_id_'.$i]) ? $config['logger']['antenna_id_'.$i] : "rteu_r".$i."_"?>">
					<small>This - together with a timestamp - will be used as filename and antenna id in the database.</small>
				</p>
					Antenna Position:
					<button type="button" onclick="getLocation()">Try It</button>
					<p id="demo"></p>
				<div class="w3-half" style = "margin-bottom: 16px">
					<label>Latitude</label>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="antenna_position_N_<?=$i?>" value="<?php echo isset($config['logger']['antenna_position_N_'.$i]) ? $config['logger']['antenna_position_N_'.$i] : 1.234?>">
				</div>
				<div class="w3-half" style = "margin-bottom: 16px">
					<label>Longitude</label>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="antenna_position_E_<?=$i?>" value="<?php echo isset($config['logger']['antenna_position_E_'.$i]) ? $config['logger']['antenna_position_E_'.$i] : 5.678?>">
				</div>
				<p>
					Antenna Orientation in degrees (i.e. N=0, E=90, S=180):<br>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="antenna_orientation_<?=$i?>" value="<?php echo isset($config['logger']['antenna_orientation_'.$i]) ? $config['logger']['antenna_orientation_'.$i] : 42?>">
				</p>
				<p>
					Antenna beam width in degrees:<br>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="antenna_beam_width_<?=$i?>" value="<?php echo isset($config['logger']['antenna_beam_width_'.$i]) ? $config['logger']['antenna_beam_width_'.$i] : 42?>">
				</p>
				<br>
			</div>
			
			<button type=button onclick="myAccordion('det<?=$i?>_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Detection Settings</h4></button>

			<div id="det<?=$i?>_settings" class="w3-container w3-hide">
				<p>				
					<label for="threshold_<?=$i?>"> Log Level </label>
					<input class="w3-input w3-mobile" style="width:30%" type="text" id="threshold_<?=$i?>" name="threshold_<?=$i?>" value="<?php echo isset($config['logger']['threshold_'.$i]) ? $config['logger']['threshold_'.$i] : 10 ?>">
				</p>
				<p>
					Number of bins in FFT (default: 400):<br>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="nfft_<?=$i?>" value="<?php echo isset($config['logger']['nfft_'.$i]) ? $config['logger']['nfft_'.$i] : 400 ?>">
				</p>
				<p>
					Number of samples per FFT (default: 50):<br>
					<input class="w3-input w3-mobile" style="width:30%" type="text" name="timestep_<?=$i?>" value="<?php echo isset($config['logger']['timestep_'.$i]) ? $config['logger']['timestep_'.$i] : 50 ?>">
				</p>
				<br>
			</div>
			
			<button type=button onclick="myAccordion('tim<?=$i?>_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Timer Settings</h4></button>
			<div id="tim<?=$i?>_settings" class="w3-container w3-hide">
				<p>
				<label for="timer_mode_<?=$i?>"> Which detection mode to use</label><br>
				<select class="w3-select w3-mobile" style="width:30%" id="timer_mode_<?=$i?>" name="timer_mode_<?=$i?>">
					<option value="freq_range" <?php echo isset($config['logger']['timer_mode_'.$i]) && $config['logger']['timer_mode_'.$i] == "freq_range" ? "selected" : "" ?>>Use Frequency Range</option> 
					<option value="single_freq" <?php echo isset($config['logger']['timer_mode_'.$i]) && $config['logger']['timer_mode_'.$i] == "single_freq" ? "selected" : "" ?>>Use single Frequency</option>
				</select>
				</p>
				<p>
				<label for="timer_start_<?=$i?>"> Automatically start at</label><br>
				<select class="w3-select w3-mobile" style="width:30%" id="timer_start_<?=$i?>" name="timer_start_<?=$i?>">
					<option value="start_no" <?php echo isset($config['logger']['timer_start_'.$i]) && $config['logger']['timer_start_'.$i] == "start_no" ? "selected" : "" ?>>Don't start automatically</option> 
					<option value="start_boot" <?php echo isset($config['logger']['timer_start_'.$i]) && $config['logger']['timer_start_'.$i] == "start_boot" ? "selected" : "" ?>>Start on boot</option>
					<option value="start_time" <?php echo isset($config['logger']['timer_start_'.$i]) && $config['logger']['timer_start_'.$i] == "start_time" ? "selected" : "" ?>>Start at given time</option>
				</select>
				<input class="w3-input w3-mobile" style="width:30%" type="time" name="timer_start_time_<?=$i?>" id="timer_start_time_<?=$i?>" value="<?php echo isset($config['logger']['timer_start_time_'.$i]) ? $config['logger']['timer_start_time_'.$i] : ""?>">
				</p>
				<p>
				<label for="timer_stop_<?=$i?>"> Automatically stop at</label><br>
				<select class="w3-select w3-mobile" style="width:30%" id="timer_stop_<?=$i?>" name="timer_stop_<?=$i?>">
					<option value="stop_no" <?php echo isset($config['logger']['timer_stop_'.$i]) && $config['logger']['timer_stop_'.$i] == "stop_no" ? "selected" : ""?>>Don't stop automatically</option> 
					<option value="stop_time" <?php echo isset($config['logger']['timer_stop_'.$i]) && $config['logger']['timer_stop_'.$i] == "stop_time" ? "selected" : ""?>>Stop at given time</option>
				</select>
				<input class="w3-input w3-mobile" style="width:30%" type="time" name="timer_stop_time_<?=$i?>" id="timer_stop_time_<?=$i?>" value="<?php echo isset($config['logger']['timer_stop_time_'.$i]) ? $config['logger']['timer_stop_time_'.$i] : ""?>">
				</p>
			</div>
			
			<button type=button onclick="myAccordion('dat<?=$i?>_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Database Settings</h4></button>
			<div id="dat<?=$i?>_settings" class="w3-container w3-hide">
			
				<p>
					Enable logging to SQL database? 
					<span class="w3-tooltip"> <i class="fa fa-info-circle" aria-hidden="false"></i>
						<span class="w3-text w3-small w3-round w3-brown w3-tag">
							Go to Data <i class="fa fa-caret-right" aria-hidden="true"></i> Start <i class="fa fa-caret-right" aria-hidden="true"></i> Database to setup connection details.
						</span>
					</span><br>
					<input class="w3-radio w3-mobile" id="use_sql_<?=$i?>_y" type="radio" name="use_sql_<?=$i?>" value="Yes" <?php echo isset($config['logger']['use_sql_'.$i]) && $config['logger']['use_sql_'.$i] == "Yes" ? 'checked="checked"' : ''?>>
					<label class="w3-margin-right" for="use_sql_<?=$i?>_y">Yes</label>
					<input class="w3-radio w3-mobile" id="use_sql_<?=$i?>_n" type="radio" name="use_sql_<?=$i?>" value="No" <?php echo isset($config['logger']['use_sql_'.$i]) && $config['logger']['use_sql_'.$i] == "No" ? 'checked="checked"' : ''?>>
					<label class="w3-margin-right" for="use_sql_<?=$i?>_n">No</label>	
				</p>
			</div>
			<input class="w3-input w3-mobile w3-btn w3-brown" style="width:30%; margin-left:auto; margin-right:10%;" type="submit" value="Change Settings" name="change_logger_settings_<?=$i?>"><br>
			</form>
		</div>
	</div>
	<?php endfor;?>
	</div>
	<div class="w3-row-padding">
		<div class="w3-container w3-green w3-round" style="margin-right:8px;margin-left:8px">
			<form method='POST' enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<br>
				After an update a compilation of the code might be necessary. 
				<br><br>
				<input type="submit" class="w3-btn w3-brown" value="Compile Raspi 3" name="compile"/>
				<input type="submit" class="w3-btn w3-brown" value="Compile Raspi Zero" name="compile_raspi_zero"/>
				<br><br>
			</form>
		</div>
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
 
 <script>
var xy = document.getElementById("demo");

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else { 
        xy.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) {
    xy.innerHTML = "Latitude: " + position.coords.latitude + 
    "<br>Longitude: " + position.coords.longitude;
}
</script>

</body>
</html>