<DOCTYPE html>
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
	define ('confSection', 'WebRX');
	define ('confKeys', array('device','fft_size','fft_fps','samp_rate','center_freq','rf_gain'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
?>
 
<!---------------- Tab Menu -------------------------->
<div class="w3-bar w3-brown">
	<button class="w3-bar-item w3-button" onclick="openCity('webrx_tab')">WebRX</button>
	<button class="w3-bar-item w3-button" onclick="openCity('settings_webrx_tab')">Settings</button>
</div>

<!-- Enter text here-->

<div id="webrx_tab" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br><br>
		<form method="POST" enctype="multipart/form-data">
			<input type="submit" class="w3-btn w3-brown" value="Start" name="rtl_websdr">
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_websdr_stop">
			<br><br>
			<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+1)?>"> Link to OpenWebRX </a>
			<br><br>
		</form>
	</div>
</div>

<div id="settings_webrx_tab" class="w3-container city" style="display:none">
	<div id="UMTS" class="w3-container">
		<div class="w3-panel w3-green w3-round">
		<br><br>
		<form method="POST" enctype="multipart/form-data action="<?php update_Config($config); echo $_SERVER['PHP_SELF'];?>">
			FFTs per second: <br>
			<input type="number" name="fft_fps" value="<?php echo isset($config['WebRX']['fft_fps']) ? $config['WebRX']['fft_fps'] : 27 ?>"><br> <br>
			Number of bins in FFT: <br>
			<select name="fft_size">
				<option value="256" <?php echo isset($config['WebRX']['fft_size']) && $config['WebRX']['fft_size'] == "256" ? "selected" : "" ?>>256</option>
				<option value="512" <?php echo isset($config['WebRX']['fft_size']) && $config['WebRX']['fft_size'] == "512" ? "selected" : "" ?>>512</option>
				<option value="1024" <?php echo isset($config['WebRX']['fft_size']) && $config['WebRX']['fft_size'] == "1024" ? "selected" : "" ?>>1024</option>
				<option value="2048" <?php echo isset($config['WebRX']['fft_size']) && $config['WebRX']['fft_size'] == "2048" ? "selected" : "" ?>>2048</option>
				<option value="4096" <?php echo isset($config['WebRX']['fft_size']) && $config['WebRX']['fft_size'] == "4096" ? "selected" : "" ?>>4096</option>
			</select> <br><br>
			Sample rate / Frequency Range: <br>
			<select name="samp_rate">
				<option value="250000" <?php echo isset($config['WebRX']['samp_rate']) && $config['WebRX']['samp_rate'] == "250000" ? "selected" : "" ?>>250k</option>
				<option value="1024000" <?php echo isset($config['WebRX']['samp_rate']) && $config['WebRX']['samp_rate'] == "1024000" ? "selected" : "" ?>>1024k</option>
			</select><br><br>
			Center Frequency in Hz: <br>
			<input type="number" name="center_freq" value="<?php echo isset($config['WebRX']['center_freq']) ? $config['WebRX']['center_freq'] : 150100000 ?>"><br><br>
			Gain: <br>
			<input type="number" name="rf_gain" value="<?php echo isset($config['WebRX']['rf_gain']) ? $config['WebRX']['rf_gain'] : 20 ?>"><br><br>
			<input type="submit" class="w3-btn w3-brown" value="Change settings befor start" name="change_config_websdr">
			<input type="submit" class="w3-btn w3-brown" value="Start" name="rtl_websdr">
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_websdr_stop">
			<br><br>
			<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+1)?>"> Link to OpenWebRX </a>
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

</body>
</html>