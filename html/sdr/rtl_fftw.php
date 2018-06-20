<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="/resources/weblib/w3.css">
<link rel="stylesheet" href="/resources/weblib/css/fontawesome-all.css">
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
	
	
	$tmparr=array();
	$valuearr=array('antenna_id_','antenna_position_N_','antenna_position_E_','antenna_orientation_','antenna_beam_width_','log_gain_','center_freq_','freq_range_','threshold_','minDuration_','maxDuration_','nfft_','timestep_','use_sql_','timer_start_','timer_start_time_','timer_stop_','timer_stop_time_','timer_mode_');
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
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'tab_logger_signals')">Latest Signals</button>
</div>

<!-------------------------------- Range Logger -------------------------------------------------------------------->

<div id="tab_logger_range" class="city w3-mobile" style="display:none">
    <?php if ($GLOBALS["num_rec"] == 0): ?>
        <div class= "w3-row-padding">
            <div class="w3-panel w3-green w3-round w3-padding" style="margin-right:8px;margin-left:8px">
            No receivers detected! Please connect at least one receiver and reload the page.
            </div>
        </div>
    <?php else: ?>
        <div class= "w3-row-padding">
            <div class="w3-panel w3-green w3-round w3-padding" style="margin-right:8px;margin-left:8px">
                <form method="POST" enctype="multipart/form-data" action="">
                    <input type="submit" class="w3-btn w3-brown" value="Start all" name="log_start_all" />
                    <input type="submit" class="w3-btn w3-brown" value="Stop all" name="log_stop_all" />
                </form>
            </div>
        </div>
        <div class= "w3-row-padding">
            <?php for ($i=0; $i<$GLOBALS["num_rec"]; $i++): ?>
                <div class="w3-half">
                    <div class="w3-panel w3-green w3-round">
                        <h3>Receiver <?=$i?> <b><?php echo $config['logger']['antenna_id_'.$i]?></b></h3><br>
                        Range: <span style="margin-left:37px"><?php echo ($config['logger']['center_freq_'.$i]-$config['logger']['freq_range_'.$i]/2)/1000000?> MHz to <?php echo ($config['logger']['center_freq_'.$i]+$config['logger']['freq_range_'.$i]/2)/1000000?> MHz</span>
                        <br>
                        Gain: <span style="margin-left:50px"><?php echo $config['logger']['log_gain_'.$i]?> dB</span>
                        <br>
                        Threshold: <span style="margin-left:10px"><?php echo $config['logger']['threshold_'.$i]?> dB above Noise</span>
                        <br>
                        Duration: <span style="margin-left:19px"><?php echo $config['logger']['minDuration_'.$i]." - ".$config['logger']['maxDuration_'.$i]?> sec</span>
                        <br>
                        MySQL: <span style="margin-left:32px"><?php if($config['logger']['use_sql_'.$i]=="Yes") :?>Writing to database at <?php echo $config['database']['db_host'];?>
                               <?php else:?> Not writing to database.<?php endif;?></span>
                        <form class="w3-right-align" method="POST" enctype="multipart/form-data" action="" style="margin-top:10px">
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
                    if(check_device_use($i)){
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
    <?php endif;?>
</div>
<!-------------------------------- Single Frequency Logger -------------------------------------------------------------------->

<div id="tab_logger_single" class="city w3-mobile" style="display:none">
<?php if ($GLOBALS["num_rec"] == 0): ?>
	<div class= "w3-row-padding">
		<div class="w3-panel w3-green w3-round w3-padding" style="margin-right:8px;margin-left:8px">
		No receivers detected! Please connect at least one receiver and reload the page.
		</div>
	</div>
<?php else: ?>
	<div class= "w3-row-padding">
		<div class="w3-panel w3-green w3-round w3-padding" style="margin-right:8px;margin-left:8px">
			<form method="POST" enctype="multipart/form-data" action="">
				<input type="submit" class="w3-btn w3-brown" value="Start all" name="log_single_start_all" />
				<input type="submit" class="w3-btn w3-brown" value="Stop all" name="log_single_stop_all" />
			</form>
		</div>
	</div>
<div class="w3-row-padding">
	<?php for ($i=0; $i<$GLOBALS["num_rec"]; $i++): ?>
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver <?=$i?> <b><?php echo $config['logger']['antenna_id_'.$i]?></b></h3><br>
			Frequency: <span style="margin-left:10px"><?php echo ($config['logger']['center_freq_'.$i]/1000000)?> MHz</span>
			<br>
			Gain: <span style="margin-left:54px"><?php echo $config['logger']['log_gain_'.$i]?> dB</span>
			<br>
			Threshold: <span style="margin-left:14px"><?php echo $config['logger']['threshold_'.$i]?> dB above Noise</span>
			<br>
            MySQL: <span style="margin-left:36px"><?php if($config['logger']['use_sql_'.$i]=="Yes") :?>Writing to database at <?php echo $config['database']['db_host'];?>
                               <?php else:?> Not writing to database<?php endif;?></span>
			<form class="w3-right-align" method="POST" enctype="multipart/form-data" action="" style="margin-top:10px">
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
				if(check_device_use($i)){
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
	<?php endif;?>
</div>

<!-------------------------------- Tab Logger Settings ------------------------------------------------------------------>

<div id="tab_logger_settings" class="city w3-mobile" style="display:none">
    <form method='POST' id="rec_settings" enctype="multipart/form-data" action="<?php update_Config($config);?>">
        <div class="w3-row-padding">
            <?php for ($i=0; $i<4; $i++): ?>
                <div class="w3-half">
                    <div class="w3-panel w3-green w3-round">
                        <h3>Receiver <?=$i?>
                            <?php if ($i==0) :?> 
                                <span class="w3-tooltip" style="float:right">
                                    <span class="w3-text w3-small w3-round w3-brown w3-tag">Copy settings (except name and orientation) to all receivers.</span>
                                    <i class="fas fa-clone" onclick="copyInput('rec_settings', ['antenna_id_', 'antenna_orientation_', 'change_logger_setting'])" style="cursor:pointer;float:right"></i>
                                </span>
                            <?php endif;?>
                        </h3><br>
                        <button type=button onclick="myAccordion('rec<?=$i?>_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Receiver Settings</h4></button>
                        <div id="rec<?=$i?>_settings" class="w3-container w3-hide">
                            <p>
                            Gain in dB (default 20):<br>
                            <input class="w3-input w3-mobile" style="width:30%" type="number" id="log_gain_<?=$i?>" name="log_gain_<?=$i?>" value="<?php echo isset($config['logger']['log_gain_'.$i]) ? $config['logger']['log_gain_'.$i] : 20 ?>">
                            <small>Gain of the recording device. Higher gain results in more noise. max 49DB</small>
                            </p>
                            <p>
                            Center Frequency in Hz:<br>
                            <input class="w3-input w3-mobile" style="width:30%" type="number" id="center_freq_<?=$i?>" name="center_freq_<?=$i?>" value="<?php echo isset($config['logger']['center_freq_'.$i]) ? $config['logger']['center_freq_'.$i] : 150100000 ?>">
                            </p>
                            <p>
                            Frequency Range to monitor:<br>
                            <select class="w3-select w3-mobile"  style="width:30%" id="freq_range_<?=$i?>" name="freq_range_<?=$i?>">
                                <option value="250000" <?php echo isset($config['logger']['freq_range_'.$i]) && $config['logger']['freq_range_'.$i] == "250000" ? "selected" : "" ?>>250kHz</option>
                                <option value="1024000" <?php echo isset($config['logger']['freq_range_'.$i]) && $config['logger']['freq_range_'.$i] == "1024000" ? "selected" : "" ?>>1024kHz</option>
                            </select> 
                            </p>
                        </div>
                        <button type=button onclick="myAccordion('ant<?=$i?>_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Antenna Settings</h4></button>

                        <div id="ant<?=$i?>_settings" class="w3-container w3-hide">
                            <p>
                                Unique name for this Antenna:<br>
                                <input class="w3-input w3-mobile" style="width:70%" type="text" id="antenna_id_<?=$i?>" name="antenna_id_<?=$i?>" value="<?php echo isset($config['logger']['antenna_id_'.$i]) ? $config['logger']['antenna_id_'.$i] : "rteu_r".$i."_"?>">
                                <small>This - together with a timestamp - will be used as filename and antenna id in the database.</small>
                            </p>
                            Antenna Position:&emsp;
                            <span class="w3-tooltip">
                                <i class="fas fa-map-marker-alt" onclick="getLocation(<?=$i?>,1)" style="cursor:pointer"></i>
                                <span class="w3-text w3-small w3-round w3-brown w3-tag">
                                    Get Position from Browser.
                                </span>
                            </span><br>
                            <div id="input_position_<?=i?>" class="w3-half" style = "margin-bottom: 16px">
                                <label>Latitude</label>
                                <input class="w3-input w3-mobile" style="width:30%" type="text" id="antenna_position_N_<?=$i?>" name="antenna_position_N_<?=$i?>" value="<?php echo isset($config['logger']['antenna_position_N_'.$i]) ? $config['logger']['antenna_position_N_'.$i] : 1.234?>">
                            </div>
                            <div class="w3-half" style = "margin-bottom: 16px">
                                <label>Longitude</label>
                                <input class="w3-input w3-mobile" style="width:30%" type="text" id="antenna_position_E_<?=$i?>" name="antenna_position_E_<?=$i?>" value="<?php echo isset($config['logger']['antenna_position_E_'.$i]) ? $config['logger']['antenna_position_E_'.$i] : 5.678?>">
                            </div>
                            <p>
                                Antenna Orientation in degrees (i.e. N=0, E=90, S=180):&emsp;
                                <span class="w3-tooltip">
                                    <i class="fas fa-location-arrow" onclick="getLocation(<?=$i?>,0)" style="cursor:pointer"></i>
                                    <span class="w3-text w3-small w3-round w3-brown w3-tag">
                                        Get Position from Browser.
                                    </span>
                                </span><br><br>
                                <input class="w3-input w3-mobile" style="width:30%" type="text" id="antenna_orientation_<?=$i?>" name="antenna_orientation_<?=$i?>" value="<?php echo isset($config['logger']['antenna_orientation_'.$i]) ? $config['logger']['antenna_orientation_'.$i] : 42?>">
                            </p>
                            <p>
                                Antenna beam width in degrees:<br>
                                <input class="w3-input w3-mobile" style="width:30%" type="text" id="antenna_beam_width_<?=$i?>" name="antenna_beam_width_<?=$i?>" value="<?php echo isset($config['logger']['antenna_beam_width_'.$i]) ? $config['logger']['antenna_beam_width_'.$i] : 42?>">
                            </p>
                            <br>
                        </div>

                        <button type=button onclick="myAccordion('det<?=$i?>_settings')" class="w3-button w3-green w3-block w3-left-align"><h4>Detection Settings</h4></button>
                        <div id="det<?=$i?>_settings" class="w3-container w3-hide">
                            <p>				
                                <label for="threshold_<?=$i?>"> Log Level: </label>
                                <input class="w3-input w3-mobile" style="width:30%" type="number" id="threshold_<?=$i?>" name="threshold_<?=$i?>" value="<?php echo isset($config['logger']['threshold_'.$i]) ? $config['logger']['threshold_'.$i] : 10 ?>">
                            </p>
                            <p>
                                <label for="minDuration_<?=$i?>"> Minimum Signal Length (seconds):</label>
                                <input class="w3-input w3-mobile" style="width:30%" type="number" id="minDuration_<?=$i?>" name="minDuration_<?=$i?>" step="any" value="<?php echo isset($config['logger']['minDuration_'.$i]) ? $config['logger']['minDuration_'.$i] : 0 ?>">
                            </p>
                            <p>
                                <label for="maxDuration_<?=$i?>"> Maximum Signal Length (seconds):</label>
                                <input class="w3-input w3-mobile" style="width:30%" type="number" id="maxDuration_<?=$i?>" name="maxDuration_<?=$i?>" step="any" value="<?php echo isset($config['logger']['maxDuration_'.$i]) ? $config['logger']['maxDuration_'.$i] : 1 ?>">
                            </p>
                            <p>
                                Number of bins in FFT (default: 400):<br>
                                <input class="w3-input w3-mobile" style="width:30%" type="number" id="nfft_<?=$i?>" name="nfft_<?=$i?>" value="<?php echo isset($config['logger']['nfft_'.$i]) ? $config['logger']['nfft_'.$i] : 400 ?>">
                            </p>
                            <p>
                                Number of samples per FFT (default: 50):<br>
                                <input class="w3-input w3-mobile" style="width:30%" type="number" id="timestep_<?=$i?>" name="timestep_<?=$i?>" value="<?php echo isset($config['logger']['timestep_'.$i]) ? $config['logger']['timestep_'.$i] : 50 ?>">
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
                                <input class="w3-radio w3-mobile" id="use_sql_<?=$i?>_y" type="radio" name="use_sql_<?=$i?>" value="Yes" <?php echo isset($config['logger']['use_sql_'.$i]) && $config['logger']['use_sql_'.$i] == "Yes" ? 'checked="true"' : ''?>>
                                <label class="w3-margin-right" for="use_sql_<?=$i?>_y">Yes</label>
                                <input class="w3-radio w3-mobile" id="use_sql_<?=$i?>_n" type="radio" name="use_sql_<?=$i?>" value="No" <?php echo isset($config['logger']['use_sql_'.$i]) && $config['logger']['use_sql_'.$i] == "No" ? 'checked="false"' : ''?>>
                                <label class="w3-margin-right" for="use_sql_<?=$i?>_n">No</label>	
                            </p>
                        </div>
                    </div>
                </div>
            <?php endfor;?>
        </div>
    </form>
    <input form="rec_settings" class="w3-mobile w3-btn w3-brown" style="position:fixed;right:140px;bottom:70px;" type="submit" value="Change Settings" id="change_logger_settings" name="change_logger_settings"><br>
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

<!-------------------------------- Tab Latest Signals ------------------------------------------------------------------>

<div id="tab_logger_signals" class="city w3-mobile" style="display:<?php echo (isset($_POST['get_signals']) ? 'block' : 'none')?>">
    <div class= "w3-row-padding">
        <div class="w3-panel w3-green w3-round w3-padding" style="margin-right:8px;margin-left:8px">
            <form method='POST' enctype="multipart/form-data" action="" onsubmit="openCity(event, 'tab_logger_signals')">
                <label for="signal_length">Signal length in s:</label>
                <div id="signal_length" class="w3-bar">
                    <input class="w3-mobile" type="number" value="0.02" pattern='[0-9\.]+' step="any" title="Use . as decimal separator" name="signal_length_from"> - 
                    <input class="w3-mobile" type="number" value="0.03" pattern='[0-9\.]+' step="any" title="Use . as decimal separator" name="signal_length_to">
                </div><br>
                <label for="signal_freq">Signal freq in Hz:</label>
                <div id="signal_freq" class="w3-bar">
                    <input class="w3-mobile" type="number" value="150100000" pattern='[0-9]+' name="signal_freq_from"> - 
                    <input class="w3-mobile" type="number" value="150130000" pattern='[0-9]+' name="signal_freq_to">
                </div><br>
                <label for="signal_strength">Minimum signal strength:<br></label>
                <input class="w3-mobile" type="number" value="30" pattern='[0-9]+' name="signal_strength" id="signal_strength"><br><br>
                <label for="num_results">Number of signals to get:<br></label>
                <input class="w3-mobile" type="number" value="30" pattern='[0-9]+' name="num_results" id="num_results"><br><br>
                <input type="submit" class="w3-btn w3-brown" value="Get Signals" name="get_signals"/>
            </form>
        </div>
    </div>

    <?php if (isset($_POST['get_signals'])) : ?>
        <div class= "w3-row-padding">
            <style>
                .w3-hoverable tbody tr:hover{background-color:#795548}
            </style>
            <div class="w3-panel w3-green w3-round w3-padding" style="margin-right:8px;margin-left:8px">
                <?php
                    $con = @mysqli_connect($config['database']['db_host'].":".$config['database']['db_port'], $config['database']['db_user'], $config['database']['db_pass'],"rteu");
                    if (mysqli_connect_errno()) {
                        echo "Connection to ".$config['database']['db_host'].":".$config['database']['db_port']." failed: " . mysqli_connect_error();
                    } else {
                        $select = "SELECT signals.*, runs.center_freq FROM signals LEFT JOIN runs ON signals.run=runs.id";
                        $sig_length = "duration >= ".$_POST['signal_length_from']." AND duration <= ".$_POST['signal_length_to'];
                        $sig_freq = "signal_freq >= ".$_POST['signal_freq_from'].".0 - center_freq AND signal_freq <= ".$_POST['signal_freq_to'].".0 - center_freq";
                        $sig_strength = "max_signal>= ".$_POST['signal_strength'];
                        $query = $select." WHERE ".$sig_length." AND ".$sig_freq." AND ".$sig_strength." LIMIT ".$_POST['num_results'];
                        //echo $query."<br>";
                        $result = mysqli_query($con, $query);
                        //echo mysqli_error($con);
                        if (!$result)
                            echo mysqli_info($con);
                        elseif (mysqli_num_rows($result) == 0)
                            echo "<br><b>No entries matched the given parameters!</b><br>";
                        else {
                            echo "<div class='w3-responsive'>\n";
                            echo "<table class='w3-table w3-hoverable w3-bordered'>\n";
                            echo "<tr class='w3-brown'><th>Timestamp</th><th>Duration</th><th>Frequency</th><th>Bandwidth</th><th>Strength</th></tr>\n";
                            while ($row = mysqli_fetch_array($result)) {
                                $sig_freq=(float)$row['signal_freq'];
                                $center_freq=(float)$row['center_freq'];
                                echo "<tr><td> ".$row['timestamp']." </td><td> ".$row['duration']." </td><td> ".($sig_freq+$center_freq)." </td><td> ".$row['signal_bw']." </td><td> ".$row['max_signal']." </td></tr>\n";
                            }
                            mysqli_free_result($result);
                            echo "</table>\n";
                            echo "</div>\n";
                        }
                        mysqli_close($con);
                    }
                ?>
            </div>
        </div>
    <?php endif;?>
</div>

<div id="geoModal" class="w3-modal">
    <div class="w3-modal-content">
        <div class="w3-container">
            <span onclick="document.getElementById('geoModal').style.display='none'" class="w3-button w3-display-topright">&times;</span>
            <p id="modalText"></p>
        </div>
    </div>
</div>


<?php
    //load footer
    require_once RESOURCES_PATH.'/footer.php';
    //load javascripts
    require_once RESOURCES_PATH.'/javascript.php';
    //load php_scripts
    require_once RESOURCES_PATH.'/php_scripts.php';
 ?>
 
<script>
    function getLocation(i,loc) {
        if (navigator.geolocation) {
            if (loc) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    showPosition(i,position);
                });
            } else {
                navigator.geolocation.getCurrentPosition(function(position) {
                    showHeading(i,position);
                });
            }
        } else { 
            document.getElementById("modalText").innerText = "<b> Geolocation is not supported by this browser.</b>";
            document.getElementById('geoModal').style.display='block';
        }
    }

    function showPosition(i, position) {
        document.getElementById("antenna_position_N_"+i).value = position.coords.latitude;
        document.getElementById("antenna_position_E_"+i).value = position.coords.longitude;
    }

    function showHeading(i, position) {
        document.getElementById("antenna_orientation_"+i).value = position.heading;
    }
</script>

</body>
</html>