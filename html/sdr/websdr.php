<DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/resources/weblib/w3.css">
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
	define ('confSection', 'WebRX');
	define ('confKeys', array('fft_size_0','fft_fps_0','samp_rate_0','center_freq_0','rf_gain_0','fft_size_1','fft_fps_1','samp_rate_1','center_freq_1','rf_gain_1'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
?>
 
<!---------------- Tab Menu -------------------------->
<div class="w3-bar w3-brown">
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event,'webrx_tab')">Spectrogram</button>
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event,'settings_webrx_tab')">Settings</button>
</div>

<!-- Enter text here-->

<div id="webrx_tab" class="w3-container city w3-row-padding w3-mobile" style="display:none">
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver 0</h3>
			<form method="POST" enctype="multipart/form-data">
				<input type="submit" class="w3-btn w3-brown" value="Start" name="rtl_websdr_d0">
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_websdr_stop_d0">
				<br><br>
				<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+1)?>"> Link to Device 0 </a>
				<br><br>
			</form>
		</div>
	</div>
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver 1</h3>
			<form method="POST" enctype="multipart/form-data">
				<input type="submit" class="w3-btn w3-brown" value="Start" name="rtl_websdr_d1">
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_websdr_stop_d1">
				<br><br>
				<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+2)?>"> Link to Device 1 </a>
				<br><br>
			</form>
		</div>
	</div>
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver 2</h3>
			<form method="POST" enctype="multipart/form-data">
				<input type="submit" class="w3-btn w3-brown" value="Start" name="rtl_websdr_d2">
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_websdr_stop_d2">
				<br><br>
				<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+3)?>"> Link to Device 2 </a>
				<br><br>
			</form>
		</div>
	</div>
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver 3</h3>
			<form method="POST" enctype="multipart/form-data">
				<input type="submit" class="w3-btn w3-brown" value="Start" name="rtl_websdr_d3">
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_websdr_stop_d3">
				<br><br>
				<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+4)?>"> Link to Device 3 </a>
				<br><br>
			</form>
		</div>
	</div>
</div>

<div id="settings_webrx_tab" class="w3-container city w3-row-padding w3-mobile" style="display:none">
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver 0</h3>
			<form method="POST" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>" >

				FFTs per second: <br>
				<input type="number" name="fft_fps_0" value="<?php echo isset($config['WebRX']['fft_fps_0']) ? $config['WebRX']['fft_fps_0'] : 27 ?>"><br> <br>
				Number of bins in FFT: <br>
				<select name="fft_size_0">
					<option value="256" <?php echo isset($config['WebRX']['fft_size_0']) && $config['WebRX']['fft_size_0'] == "256" ? "selected" : "" ?>>256</option>
					<option value="512" <?php echo isset($config['WebRX']['fft_size_0']) && $config['WebRX']['fft_size_0'] == "512" ? "selected" : "" ?>>512</option>
					<option value="1024" <?php echo isset($config['WebRX']['fft_size_0']) && $config['WebRX']['fft_size_0'] == "1024" ? "selected" : "" ?>>1024</option>
					<option value="2048" <?php echo isset($config['WebRX']['fft_size_0']) && $config['WebRX']['fft_size_0'] == "2048" ? "selected" : "" ?>>2048</option>
					<option value="4096" <?php echo isset($config['WebRX']['fft_size_0']) && $config['WebRX']['fft_size_0'] == "4096" ? "selected" : "" ?>>4096</option>
				</select> <br><br>
				Sample rate / Frequency Range: <br>
				<select name="samp_rate_0">
					<option value="250000" <?php echo isset($config['WebRX']['samp_rate_0']) && $config['WebRX']['samp_rate_0'] == "250000" ? "selected" : "" ?>>250k</option>
					<option value="1024000" <?php echo isset($config['WebRX']['samp_rate_0']) && $config['WebRX']['samp_rate_0'] == "1024000" ? "selected" : "" ?>>1024k</option>
				</select><br><br>
				Center Frequency in Hz: <br>
				<input type="number" name="center_freq_0" value="<?php echo isset($config['WebRX']['center_freq_0']) ? $config['WebRX']['center_freq_0'] : 150100000 ?>"><br><br>
				Gain: <br>
				<input type="number" name="rf_gain_0" value="<?php echo isset($config['WebRX']['rf_gain_0']) ? $config['WebRX']['rf_gain_0'] : 20 ?>"><br><br>
				<input type="submit" class="w3-btn w3-brown w3-right" value="Change settings" name="change_config_websdr_d0"><br><br>
			</form>
		</div>
	</div>
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver 1</h3>
			<form method="POST" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>" >
				FFTs per second: <br>
				<input type="number" name="fft_fps_1" value="<?php echo isset($config['WebRX']['fft_fps_1']) ? $config['WebRX']['fft_fps_1'] : 27 ?>"><br> <br>
				Number of bins in FFT: <br>
				<select name="fft_size_1">
					<option value="256" <?php echo isset($config['WebRX']['fft_size_1']) && $config['WebRX']['fft_size_1'] == "256" ? "selected" : "" ?>>256</option>
					<option value="512" <?php echo isset($config['WebRX']['fft_size_1']) && $config['WebRX']['fft_size_1'] == "512" ? "selected" : "" ?>>512</option>
					<option value="1024" <?php echo isset($config['WebRX']['fft_size_1']) && $config['WebRX']['fft_size_1'] == "1024" ? "selected" : "" ?>>1024</option>
					<option value="2048" <?php echo isset($config['WebRX']['fft_size_1']) && $config['WebRX']['fft_size_1'] == "2048" ? "selected" : "" ?>>2048</option>
					<option value="4096" <?php echo isset($config['WebRX']['fft_size_1']) && $config['WebRX']['fft_size_1'] == "4096" ? "selected" : "" ?>>4096</option>
				</select> <br><br>
				Sample rate / Frequency Range: <br>
				<select name="samp_rate_1">
					<option value="250000" <?php echo isset($config['WebRX']['samp_rate_1']) && $config['WebRX']['samp_rate_1'] == "250000" ? "selected" : "" ?>>250k</option>
					<option value="1024000" <?php echo isset($config['WebRX']['samp_rate_1']) && $config['WebRX']['samp_rate_1'] == "1024000" ? "selected" : "" ?>>1024k</option>
				</select><br><br>
				Center Frequency in Hz: <br>
				<input type="number" name="center_freq_1" value="<?php echo isset($config['WebRX']['center_freq_1']) ? $config['WebRX']['center_freq_1'] : 150100000 ?>"><br><br>
				Gain: <br>
				<input type="number" name="rf_gain_1" value="<?php echo isset($config['WebRX']['rf_gain_1']) ? $config['WebRX']['rf_gain_1'] : 20 ?>"><br><br>
				<input type="submit" class="w3-right w3-brown w3-btn" value="Change settings" name="change_config_websdr_d1"><br><br>
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

</body>
</html>