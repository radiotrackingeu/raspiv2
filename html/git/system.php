<!DOCTYPE html>
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
 ?>
 
<!-- Enter text here-->

<div class="w3-bar w3-brown w3-mobile">
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'schedule')">Schedule</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'date')">Time/Date</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'hostname')">Hostname</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'expand_disc')">Expand Disc</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'infos')">System Information</button>
</div>

<div id="schedule" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="POST">
			<br>
			You can leave out the weekday <br><br>
			<input type="text" name="new_date" value="* * * * *"> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Light on" name="cron_light_on"><br>
			<input type="text" name="new_date" value="* * * * *"> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Light off" name="cron_light_off"><br>
			<br>
		</form>
	</div>
</div>	

<div id="date" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="POST">
			<br>
			You can leave out the weekday <br><br>
			<input type="text" name="new_date" value="<?php echo shell_exec("date")?>"> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Update date and time" name="update_date"><br>
			<br>
		</form>
	</div>
</div>	

<div id="hostname" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="POST">
			<br>
			Please choose a new hostname - it needs to start with characters. <br><br>
			<input type="text" name="new_hostname" value="<?php echo shell_exec("cat /etc/hostname")?>"> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Update Hostname" name="change_hostname"><br>
			<br>
		</form>
	</div>
</div>	

<div id="expand_disc" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<form method="POST">
			<br>
			Expands disc at reboot: If you want to use the whole free disc space on your mini sd memory card, you need to 
			press the Expand Disc on Reboot button and reboot - after the reboot please press the Stop Expanding Disc on 
			Start otherwise it will try to expand the disc on every reboot<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Show disc usage" name="disc_usage"><br>
			<br>
			<input type="submit" class="w3-btn w3-brown" value="Expand Disc on Reboot" name="exp_disc"><br>
			<br>
			<input type="submit" class="w3-btn w3-brown" value="Stop Expanding Disc on Start" name="stop_exp_disc"><br>
			<br>
			<input type="submit" class="w3-btn w3-brown" value="Reboot" name="reboot"><br>
			<br>
		</form>
	</div>
</div>	

<div id="infos" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br> The Temperature of the CPU is:  <?php echo shell_exec("cat /sys/class/thermal/thermal_zone0/temp") ?>
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