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
	define ('confKeys', array('device','Single_Freq','Freq1','Freq2','Freq3','Freq4','Freq5','Freq6', 'Radio_Gain'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
?>

<!---------------- Single Frequency -------------------------->
<div class="w3-bar w3-brown">
	<button class="w3-bar-item w3-button" onclick="openCity('single_freq')">Single Frequency</button>
	<button class="w3-bar-item w3-button" onclick="openCity('multiple_freq')">Multiple Frequencies</button>
	<button class="w3-bar-item w3-button" onclick="openCity('freq_settings')">Frequencies Settings</button>
	<button class="w3-bar-item w3-button" onclick="openCity('single_freq_rec')">Single Frequency Recorder</button>
</div>
<div id="single_freq" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<br>
			<?php
				if($config['SDR_Radio']['device']==1){
					echo "<strong>Receiver 1 selected</strong><br><br>";
					echo "<input type='submit' class='w3-btn w3-brown' value='Switch to receiver 2' name='change_device_to_2'/>";
					if (isset($_POST["change_device_to_2"])){
						$config['SDR_Radio']['device']==2;
						echo "<script type='text/javascript'>document.getElementById('single_freq').style.display = 'block';</script>";
					}
				}
				if($config['SDR_Radio']['device']==2){
					echo "<strong>Receiver 2 selected</strong><br><br>";
					echo "<input type='submit' class='w3-btn w3-brown' value='Switch to receiver 1' name='change_device_to_1'/>";
					if (isset($_POST["change_device_to_1"])){
						echo $config['SDR_Radio']['device'];
						$config['SDR_Radio']['device']==1;
						echo "<script type='text/javascript'>document.getElementById('single_freq').style.display = 'block';</script>";
					}
				}
			?>
			<br><br>
			
			<strong>Frequency:</strong><br>
			<input type="text" name="Single_Freq" value="<?php echo isset($config['SDR_Radio']['Single_Freq'][$config['device']]) ? $config['SDR_Radio']['Single_Freq'][$config['device']] : "150.1M" ?>" /><br>
			Set the frequency you want to listen to. You can use multipliers like M and k. Turn slightly below the frequency for better results.<br>
			Together with a treshold bigger then 0 you can scan multiple frequencies if you add a -f (i.e. 150.1M -f 150.120M)
			<br><br>
			<strong>Gain in DB:</strong><br>
			<input type="text" name="Radio_Gain" value="<?php echo isset($config['SDR_Radio']['Radio_Gain'][$config['device']]) ? $config['SDR_Radio']['Radio_Gain'][$config['device']] : 20 ?>" /><br>
			Set a gain value. Remember higher gains result in higher noise levels.
			<br>
			<br>
			Start and Stop receiver - to set a new frequency/gain, first stop and restart: 
			<br>
			<br>
			<input type="submit" class="w3-btn w3-brown" value="Start Browser playback" name="rtl_fm_start_s"/>
			<input type="submit" class="w3-btn w3-brown" value="Start Local playback" name="rtl_fm_start_l"/>
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_stop"/>
			<br><br>
			<?php echo start_playback(); ?>
			<br>
		</form>
	</div>
</div>

<!---------------- Multiple Frequencies -------------------------->

<div id="multiple_freq" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="post" enctype="multipart/form-data">
			<br>
			<?php
				if($config['SDR_Radio']['device']==1){
					echo "<strong>Receiver 1 selected</strong><br><br>";
					echo "<input type='submit' class='w3-btn w3-brown' value='Switch to receiver 2' name='change_device_to_2'/>";
					if (isset($_POST["change_device_to_2"])){
						$config['SDR_Radio']['device']==2;
						echo "<script type='text/javascript'>document.getElementById('single_freq').style.display = 'block';</script>";
					}
				}
				if($config['SDR_Radio']['device']==2){
					echo "<strong>Receiver 2 selected</strong><br><br>";
					echo "<input type='submit' class='w3-btn w3-brown' value='Switch to receiver 1' name='change_device_to_1'/>";
					if (isset($_POST["change_device_to_1"])){
						echo $config['SDR_Radio']['device'];
						$config['SDR_Radio']['device']==1;
						echo "<script type='text/javascript'>document.getElementById('single_freq').style.display = 'block';</script>";
					}
				}
			?>
			<br><br>
			<strong>Frequencies:</strong><br>
			<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq1'][$config['SDR_Radio']['device']] ?>" name="rtl_fm_start_f1"/>
			<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq2'][$config['SDR_Radio']['device']] ?>" name="rtl_fm_start_f2"/><br><br>
			<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq3'][$config['SDR_Radio']['device']] ?>" name="rtl_fm_start_f3"/>
			<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq4'][$config['SDR_Radio']['device']] ?>" name="rtl_fm_start_f4"/><br><br>
			<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq5'][$config['SDR_Radio']['device']] ?>" name="rtl_fm_start_f5"/>
			<input type="submit" class="w3-btn w3-brown" value="<?php echo $config['SDR_Radio']['Freq6'][$config['SDR_Radio']['device']] ?>" name="rtl_fm_start_f6"/><br><br><br>
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_stop"/>
		</form>
		Press button to start playback at the specified frequency.
		<br><br>
		<?php echo start_playback(); ?>
		<br><br>
	</div>
</div>

<!---------------- Frequencies Settings -------------------------->
<!--- TODO Do we want to set these individually for both devices? ---->
<div id="freq_settings" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<br>
			<?php
				if($config['SDR_Radio']['device']==1){
					echo "<strong>Receiver 1 selected</strong><br><br>";
					echo "<input type='submit' class='w3-btn w3-brown' value='Switch to receiver 2' name='change_device_to_2'/>";
					if (isset($_POST["change_device_to_2"])){
						$config['SDR_Radio']['device']==2;
						echo "<script type='text/javascript'>document.getElementById('single_freq').style.display = 'block';</script>";
					}
				}
				if($config['SDR_Radio']['device']==2){
					echo "<strong>Receiver 2 selected</strong><br><br>";
					echo "<input type='submit' class='w3-btn w3-brown' value='Switch to receiver 1' name='change_device_to_1'/>";
					if (isset($_POST["change_device_to_1"])){
						echo $config['SDR_Radio']['device'];
						$config['SDR_Radio']['device']==1;
						echo "<script type='text/javascript'>document.getElementById('single_freq').style.display = 'block';</script>";
					}
				}
			?>
			<br><br>
			<strong>Frequency 1:</strong><br>
			<input type="text" name="Freq1" value="<?php echo isset($config['SDR_Radio']['Freq1'][$config['SDR_Radio']['device']]) ? $config['SDR_Radio']['Freq1'][$config['SDR_Radio']['device']] : "150.1M" ?>" /><br>
			<strong>Frequency 2:</strong><br>
			<input type="text" name="Freq2" value="<?php echo isset($config['SDR_Radio']['Freq2'][$config['SDR_Radio']['device']]) ? $config['SDR_Radio']['Freq2'][$config['SDR_Radio']['device']] : "150.1M" ?>" /><br>
			<strong>Frequency 3:</strong><br>
			<input type="text" name="Freq3" value="<?php echo isset($config['SDR_Radio']['Freq3'][$config['SDR_Radio']['device']]) ? $config['SDR_Radio']['Freq3'][$config['SDR_Radio']['device']] : "150.1M" ?>" /><br>
			<strong>Frequency 4:</strong><br>
			<input type="text" name="Freq4" value="<?php echo isset($config['SDR_Radio']['Freq4'][$config['SDR_Radio']['device']]) ? $config['SDR_Radio']['Freq4'][$config['SDR_Radio']['device']] : "150.1M" ?>" /><br>
			<strong>Frequency 5:</strong><br>
			<input type="text" name="Freq5" value="<?php echo isset($config['SDR_Radio']['Freq5'][$config['SDR_Radio']['device']]) ? $config['SDR_Radio']['Freq5'][$config['SDR_Radio']['device']] : "150.1M" ?>" /><br>
			<strong>Frequency 6:</strong><br>
			<input type="text" name="Freq6" value="<?php echo isset($config['SDR_Radio']['Freq6'][$config['SDR_Radio']['device']]) ? $config['SDR_Radio']['Freq6'][$config['SDR_Radio']['device']] : "150.1M" ?>" /><br>
			<strong>Gain in DB:</strong><br>
			<input type="text" name="Radio_Gain" value="<?php echo isset($config['SDR_Radio']['Radio_Gain'][$config['SDR_Radio']['device']]) ? $config['SDR_Radio']['Radio_Gain'][$config['SDR_Radio']['device']] : 20 ?>" /><br>
			<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Save Settings" name="save_settings"/>
		</form>
	</div>
</div>

<!---------------- Single Frequency Recorder-------------------------->

<div id="single_freq_rec" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<br>
			<?php
				if($config['SDR_Radio']['device']==1){
					echo "<strong>Receiver 1 selected</strong><br><br>";
					echo "<input type='submit' class='w3-btn w3-brown' value='Switch to receiver 2' name='change_device_to_2'/>";
					if (isset($_POST["change_device_to_2"])){
						$config['SDR_Radio']['device']==2;
						echo "<script type='text/javascript'>document.getElementById('single_freq').style.display = 'block';</script>";
					}
				}
				if($config['SDR_Radio']['device']==2){
					echo "<strong>Receiver 2 selected</strong><br><br>";
					echo "<input type='submit' class='w3-btn w3-brown' value='Switch to receiver 1' name='change_device_to_1'/>";
					if (isset($_POST["change_device_to_1"])){
						echo $config['SDR_Radio']['device'];
						$config['SDR_Radio']['device']==1;
						echo "<script type='text/javascript'>document.getElementById('single_freq').style.display = 'block';</script>";
					}
				}
			?>
			<br><br>
			<strong>Frequencies:</strong><br>
			<input type="text" name="Single_Freq" value="<?php echo isset($config['SDR_Radio']['Single_Freq'][$config['device']]) ? $config['SDR_Radio']['Single_Freq'][$config['device']] : "150.1M" ?>" /><br>
			Set the frequency you want to listen to. You can use multipliers like M and k. Turn slightly below the frequency for better results.<br>
			Together with a treshold bigger then 0 you can scan multiple frequencies if you add a -f (i.e. 150.1M -f 150.120M)
			<br><br>
			<strong>Gain in DB:</strong><br>
			<input type="text" name="Radio_Gain" value="<?php echo isset($config['SDR_Radio']['Radio_Gain'][$config['device']]) ? $config['SDR_Radio']['Radio_Gain'][$config['device']] : 20 ?>" /><br>
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
		</form>
	</div>
</div>

<!---------------- Multiple Frequencies Recorder -------------------------->

<div id="multiple_freq_rec" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
			<br>
			<strong>Frequencies:</strong><br>
			<input type="text" name="Single_Freq" value="<?php echo isset($config['SDR_Radio']['Single_Freq'][$config['SDR_Radio']['device']]) ? $config['SDR_Radio']['Single_Freq'][$config['SDR_Radio']['device']] : "150.1M" ?>" /><br>
			Set the frequency you want to listen to. You can use multipliers like M and k. Turn slightly below the frequency for better results.<br>
			Together with a treshold bigger then 0 you can scan multiple frequencies if you add a -f (i.e. 150.1M -f 150.120M)
			<br><br>
			<strong>Gain in DB:</strong><br>
			<input type="text" name="Radio_Gain" value="<?php echo isset($config['SDR_Radio']['Radio_Gain'][$config['SDR_Radio']['device']]) ? $config['SDR_Radio']['Radio_Gain'][$config['SDR_Radio']['device']] : 20 ?>" /><br>
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
	
	function start_playback(){
		echo "<audio controls autoplay preload=none><source src='http://".$_SERVER['SERVER_NAME'].':'.($_SERVER['SERVER_PORT']+1)."'type='audio/mpeg'>Your browser does not support the audio element.</audio>";
	}
 ?>

</body>
</html>
