<DOCTYPE html>
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
	define ('confKeys', array('Freq1_0','Freq1_1','Freq2_0','Freq2_1','Freq3_0','Freq3_1','Freq4_0','Freq4_1','Freq5_0','Freq5_1','Freq6_0','Freq6_1','Radio_Gain_0','Radio_Gain_1'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
?>

<!---------------- Tab Menu -------------------------->
<div class="w3-bar w3-brown">
	<button class="w3-bar-item w3-button w3-mobile tablink active-item" onclick="openCity(event,'playback')">Playback</button>
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event,'freq_settings')">Define Frequencies</button>
</div>

<!---------------- Multiple Frequencies -------------------------->
<div id="playback" class="w3-container city w3-row-padding w3-mobile" style="display:block">
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<form method="post" enctype="multipart/form-data" action="rtl_fm_player.php" target="_blank">
				<h3>Receiver 0</h3><br>
				<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq1_0'] ?>" name="rtl_fm_start_0_f1"/>
				<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq2_0'] ?>" name="rtl_fm_start_0_f2"/><br><br>
				<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq3_0'] ?>" name="rtl_fm_start_0_f3"/>
				<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq4_0'] ?>" name="rtl_fm_start_0_f4"/><br><br>
				<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq5_0'] ?>" name="rtl_fm_start_0_f5"/>
				<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq6_0'] ?>" name="rtl_fm_start_0_f6"/>
				<input type="hidden" value="0" name="dev">
			</form>
			Press button to start playback at the specified frequency.<br><br>
		</div>
	</div>
		<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<form method="post" enctype="multipart/form-data" action="rtl_fm_player.php" target="_blank">
				<h3>Receiver 1</h3><br>
				<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq1_1'] ?>" name="rtl_fm_start_1_f1"/>
				<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq2_1'] ?>" name="rtl_fm_start_1_f2"/><br><br>
				<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq3_1'] ?>" name="rtl_fm_start_1_f3"/>
				<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq4_1'] ?>" name="rtl_fm_start_1_f4"/><br><br>
				<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq5_1'] ?>" name="rtl_fm_start_1_f5"/>
				<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq6_1'] ?>" name="rtl_fm_start_1_f6"/>
				<input type="hidden" value="1" name="dev">
			</form>
			Press button to start playback at the specified frequency.<br><br>
		</div>
	</div>
</div>
<!---------------- Frequencies Settings -------------------------->
<div id="freq_settings" class="w3-container city w3-row-padding w3-mobile" style="display:none">
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<form method='POST' enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
				<h3>Receiver 0</h3><br>
				<br>
				<strong>Frequency 1:</strong><br>
				<input type="text" name="Freq1_0" value="<?php echo isset($config['SDR_Radio']['Freq1_0']) ? $config['SDR_Radio']['Freq1_0'] : "150.1M" ?>" /><br>
				<strong>Frequency 2:</strong><br>
				<input type="text" name="Freq2_0" value="<?php echo isset($config['SDR_Radio']['Freq2_0']) ? $config['SDR_Radio']['Freq2_0'] : "150.1M" ?>" /><br>
				<strong>Frequency 3:</strong><br>
				<input type="text" name="Freq3_0" value="<?php echo isset($config['SDR_Radio']['Freq3_0']) ? $config['SDR_Radio']['Freq3_0'] : "150.1M" ?>" /><br>
				<strong>Frequency 4:</strong><br>
				<input type="text" name="Freq4_0" value="<?php echo isset($config['SDR_Radio']['Freq4_0']) ? $config['SDR_Radio']['Freq4_0'] : "150.1M" ?>" /><br>
				<strong>Frequency 5:</strong><br>
				<input type="text" name="Freq5_0" value="<?php echo isset($config['SDR_Radio']['Freq5_0']) ? $config['SDR_Radio']['Freq5_0'] : "150.1M" ?>" /><br>
				<strong>Frequency 6:</strong><br>
				<input type="text" name="Freq6_0" value="<?php echo isset($config['SDR_Radio']['Freq6_0']) ? $config['SDR_Radio']['Freq6_0'] : "150.1M" ?>" /><br>
				<strong>Gain in DB:</strong><br>
				<input type="text" name="Radio_Gain_0" value="<?php echo isset($config['SDR_Radio']['Radio_Gain_0']) ? $config['SDR_Radio']['Radio_Gain_0'] : 20 ?>" /><br>
				<br><br>
				<input type="submit" class="w3-btn w3-brown" value="Save Settings" name="change_settings_SDR_Radio_0"/>
			</form>
		</div>
	</div>
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<form method='POST' enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
				<h3>Receiver 1</h3><br>
				<br>
				<strong>Frequency 1:</strong><br>
				<input type="text" name="Freq1_1" value="<?php echo isset($config['SDR_Radio']['Freq1_1']) ? $config['SDR_Radio']['Freq1_1'] : "150.1M" ?>" /><br>
				<strong>Frequency 2:</strong><br>
				<input type="text" name="Freq2_1" value="<?php echo isset($config['SDR_Radio']['Freq2_1']) ? $config['SDR_Radio']['Freq2_1'] : "150.1M" ?>" /><br>
				<strong>Frequency 3:</strong><br>
				<input type="text" name="Freq3_1" value="<?php echo isset($config['SDR_Radio']['Freq3_1']) ? $config['SDR_Radio']['Freq3_1'] : "150.1M" ?>" /><br>
				<strong>Frequency 4:</strong><br>
				<input type="text" name="Freq4_1" value="<?php echo isset($config['SDR_Radio']['Freq4_1']) ? $config['SDR_Radio']['Freq4_1'] : "150.1M" ?>" /><br>
				<strong>Frequency 5:</strong><br>
				<input type="text" name="Freq5_1" value="<?php echo isset($config['SDR_Radio']['Freq5_1']) ? $config['SDR_Radio']['Freq5_1'] : "150.1M" ?>" /><br>
				<strong>Frequency 6:</strong><br>
				<input type="text" name="Freq6_1" value="<?php echo isset($config['SDR_Radio']['Freq6_1']) ? $config['SDR_Radio']['Freq6_1'] : "150.1M" ?>" /><br>
				<strong>Gain in DB:</strong><br>
				<input type="text" name="Radio_Gain_1" value="<?php echo isset($config['SDR_Radio']['Radio_Gain_1']) ? $config['SDR_Radio']['Radio_Gain_1'] : 20 ?>" /><br>
				<br><br>
				<input type="submit" class="w3-btn w3-brown" value="Save Settings" name="change_settings_SDR_Radio_1"/>
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
	
	function start_playback(){
		echo "<audio controls autoplay preload=none><source src='http://".$_SERVER['SERVER_NAME'].':'.($_SERVER['SERVER_PORT']+1)."'type='audio/mpeg'>Your browser does not support the audio element.</audio>";
	}
 ?>
