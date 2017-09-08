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
	define ('confSection', 'SDR_Radio');
	define ('confKeys', array('Signle_Freq','Freq1','Freq2','Freq3','Freq4','Freq5','Freq6', 'Radio_Gain'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');	
?>

<!-- Enter text here-->

<div class="w3-bar w3-brown">
	<button class="w3-bar-item w3-button" onclick="openCity('single_freq')">Single Frequency</button>
	<button class="w3-bar-item w3-button" onclick="openCity('multiple_freq')">Multiple Frequencies</button>
	<button class="w3-bar-item w3-button" onclick="openCity('freq_settings')">Frequencies Settings</button>
</div>
<div id="single_freq" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<br>
			<strong>Frequencies:</strong><br>
			<input type="text" name="Signle_Freq" value="<?php echo isset($config['SDR_Radio']['Signle_Freq']) ? $config['SDR_Radio']['Signle_Freq'] : "150.1M" ?>" /><br>
			Set the frequency you want to listen to. You can use multipliers like M and k. Turn slightly below the frequency for better results.<br>
			Together with a treshold bigger then 0 you can scan multiple frequencies if you add a -f (i.e. 150.1M -f 150.120M)
			<br><br>
			<strong>Gain in DB:</strong><br>
			<input type="text" name="Radio_Gain" value="<?php echo isset($config['SDR_Radio']['Radio_Gain']) ? $config['SDR_Radio']['Radio_Gain'] : 20 ?>" /><br>
			Set a gain value. Remember higher gains result in higher noise levels.
			<br>
			<br>
			Start and Stop receiver - to set a new frequency/gain, first stop and restart: 
			<br>
			<br>
			<input type="submit" class="w3-btn w3-brown" value="Start Browser playback" name="rtl_fm_start_s"/>
			<input type="submit" class="w3-btn w3-brown" value="Start Local playback" name="rtl_fm_start_l"/>
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_stop"/>
			<br>
			<br>
			<audio controls>
				<source src="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+1); ?>" type="audio/mpeg" controls preload="none">
				Your browser does not support the audio element.
			</audio>
		</form>
	</div>
</div>

<div id="multiple_freq" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="post" enctype="multipart/form-data">
			<br>
			<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq1'] ?>" name="rtl_fm_start_f1"/><br><br>
			<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq2'] ?>" name="rtl_fm_start_f2"/><br><br>
			<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq3'] ?>" name="rtl_fm_start_f3"/><br><br>
			<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq4'] ?>" name="rtl_fm_start_f4"/><br><br>
			<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq5'] ?>" name="rtl_fm_start_f5"/><br><br>
			<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq6'] ?>" name="rtl_fm_start_f6"/><br><br>
		</form>
	</div>
</div>

<div id="freq_settings" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<br>
			<strong>Frequency 1:</strong><br>
			<input type="text" name="Freq1" value="<?php echo isset($config['SDR_Radio']['Freq1']) ? $config['SDR_Radio']['Freq1'] : "150.1M" ?>" /><br>
			<strong>Frequency 2:</strong><br>
			<input type="text" name="Freq2" value="<?php echo isset($config['SDR_Radio']['Freq2']) ? $config['SDR_Radio']['Freq2'] : "150.1M" ?>" /><br>
			<strong>Frequency 3:</strong><br>
			<input type="text" name="Freq3" value="<?php echo isset($config['SDR_Radio']['Freq3']) ? $config['SDR_Radio']['Freq3'] : "150.1M" ?>" /><br>
			<strong>Frequency 4:</strong><br>
			<input type="text" name="Freq4" value="<?php echo isset($config['SDR_Radio']['Freq4']) ? $config['SDR_Radio']['Freq4'] : "150.1M" ?>" /><br>
			<strong>Frequency 5:</strong><br>
			<input type="text" name="Freq5" value="<?php echo isset($config['SDR_Radio']['Freq5']) ? $config['SDR_Radio']['Freq5'] : "150.1M" ?>" /><br>
			<strong>Frequency 6:</strong><br>
			<input type="text" name="Freq6" value="<?php echo isset($config['SDR_Radio']['Freq6']) ? $config['SDR_Radio']['Freq6'] : "150.1M" ?>" /><br>
			<strong>Gain in DB:</strong><br>
			<input type="text" name="Radio_Gain" value="<?php echo isset($config['SDR_Radio']['Radio_Gain']) ? $config['SDR_Radio']['Radio_Gain'] : 20 ?>" /><br>
			<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Save Settings" name="save_settings"/>
		</form>
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
