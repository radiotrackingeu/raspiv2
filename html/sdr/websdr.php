<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/resources/weblib/w3.css">
<link rel="stylesheet" href="/resources/weblib/css/fontawesome-all.css">
<link rel="stylesheet" href="/resources/weblib/css/font-awesome.min.css">
<link rel="stylesheet" href="/resources/additional.css">

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
	$tmparr=array();
	$valuearr=array('fft_size_','fft_fps_','samp_rate_','center_freq_','rf_gain_');
	for ($i=0; $i<4; $i++) {
			foreach($valuearr as $var) {
				$tmparr[]=$var.$i;
			}
		}
	define ('confSection', 'WebRX');
	define ('confKeys', $tmparr);
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
?>
 
<!---------------- Tab Menu -------------------------->
<div class="w3-bar w3-brown w3-mobile">
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event,'webrx_tab')">Spectrogram</button>
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event,'settings_webrx_tab')">Settings</button>
</div>

<!---------------- Tabs ------------------------------>
<!---------------- Spectrogram ------------------------>
<div id="webrx_tab" class="city w3-mobile" style="display:none">
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
                    <input type="submit" class="w3-btn w3-brown" value="Start all" name="rtl_websdr_start_all" />
                    <input type="submit" class="w3-btn w3-brown" value="Stop all" name="rtl_websdr_stop_all" />
                </form>
            </div>
        </div>
        <div class= "w3-row-padding">
            <?php for ($i=0; $i<$GLOBALS["num_rec"]; $i++): ?>
            <div class="w3-half">
                <div class="w3-panel w3-green w3-round">
                    <h3>Receiver <?=$i?></h3>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="submit" class="w3-btn w3-brown" value="Start" name="rtl_websdr_d<?=$i?>">
                        <input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_websdr_stop_d<?=$i?>">
                        <br><br>
                        <a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+1+$i)?>"> Link to Device <?=$i?> </a>
                        <br><br>
                    </form>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    <?php endif;?>
</div>

<!---------------- Settings -------------------------->
<div id="settings_webrx_tab" class="city w3-mobile" style="display:none">
    <form method='POST' id="webRX_settings" enctype="multipart/form-data" action="<?php update_Config($config);?>">	
		<div class="w3-row-padding">
			<?php for ($i=0; $i<4; $i++): ?>
				<div class="w3-half">
					<div class="w3-panel w3-green w3-round">
						<h3>Receiver <?=$i?>
							<?php if ($i==0) :?>
								<span class="w3-tooltip" style="float:right">
									<span class="w3-text w3-small w3-round w3-brown w3-tag">Copy settings to all receivers.</span>
									<i class="fas fa-clone" onclick="copyInput('webRX_settings', [])" style="cursor:pointer;float:right"></i>
								</span>
							<?php endif;?></h3><br>
							FFTs per second: <br>
							<input type="number" name="fft_fps_<?=$i?>" value="<?php echo isset($config['WebRX']['fft_fps_'.$i]) ? $config['WebRX']['fft_fps_'.$i] : 27 ?>"><br> <br>
							Number of bins in FFT: <br>
							<select name="fft_size_<?=$i?>">
								<option value="256" <?php echo isset($config['WebRX']['fft_size_'.$i]) && $config['WebRX']['fft_size_'.$i] == "256" ? "selected" : "" ?>>256</option>
								<option value="512" <?php echo isset($config['WebRX']['fft_size_'.$i]) && $config['WebRX']['fft_size_'.$i] == "512" ? "selected" : "" ?>>512</option>
								<option value="1024" <?php echo isset($config['WebRX']['fft_size_'.$i]) && $config['WebRX']['fft_size_'.$i] == "1024" ? "selected" : "" ?>>1024</option>
								<option value="2048" <?php echo isset($config['WebRX']['fft_size_'.$i]) && $config['WebRX']['fft_size_'.$i] == "2048" ? "selected" : "" ?>>2048</option>
								<option value="4096" <?php echo isset($config['WebRX']['fft_size_'.$i]) && $config['WebRX']['fft_size_'.$i] == "4096" ? "selected" : "" ?>>4096</option>
							</select> <br><br>
							Sample rate / Frequency Range: <br>
							<select name="samp_rate_<?=$i?>">
								<option value="250000" <?php echo isset($config['WebRX']['samp_rate_'.$i]) && $config['WebRX']['samp_rate_'.$i] == "250000" ? "selected" : "" ?>>250k</option>
								<option value="1024000" <?php echo isset($config['WebRX']['samp_rate_'.$i]) && $config['WebRX']['samp_rate_'.$i] == "1024000" ? "selected" : "" ?>>1024k</option>
							</select><br><br>
							Center Frequency in Hz: <br>
							<input type="number" name="center_freq_<?=$i?>" value="<?php echo isset($config['WebRX']['center_freq_'.$i]) ? $config['WebRX']['center_freq_'.$i] : 150100000 ?>"><br><br>
							Gain: <br>
							<input type="number" name="rf_gain_<?=$i?>" value="<?php echo isset($config['WebRX']['rf_gain_'.$i]) ? $config['WebRX']['rf_gain_'.$i] : 20 ?>"><br><br>
					</div>
				</div>
			<?php endfor; ?>
		</div>
	</form>
    <input form="webRX_settings" class="w3-mobile w3-btn w3-brown" style="position:fixed;right:140px;bottom:70px;" type="submit" value="Change Settings" id="change_webRX_settings" name="change_webRX_settings"><br>	
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