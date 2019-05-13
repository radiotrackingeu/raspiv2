<!DOCTYPE html>
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
	define ('confSection', 'system');
	define ('confKeys', array('usb_timer_start_time', 'usb_timer_stop_time'));
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
 ?>

<script type="text/javascript">
function get_Time() {
	var t = new Date();
	document.getElementById("client_time").innerHTML = t.toUTCString();
	document.getElementById("client_time_input").value = t.toUTCString();
}
setInterval("get_Time()",1000);
</script>
 
<!-- Enter text here-->

<div class="w3-bar w3-brown w3-mobile">
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'date')">Time/Date</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'hostname')">Hostname</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'expand_disc')">Expand Disc</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'usbpower')">USB Power</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'sensors')">Sensors</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'passwords')">Passwords</button>
  <button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'infos')">System Information</button>
</div>	

<div id="date" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round w3-padding">
		<form method="POST">
			You can leave out the weekday <br><br>
			<input type="text" name="new_date" value="<?php echo shell_exec("date")?>"> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Update date and time" name="update_date"><br>
		</form>
	</div>
	<div class="w3-panel w3-green w3-round w3-padding">
			Current date and time on your device:  <b id="client_time"></b><br><br>
		<form method="POST" id="set_time_from_client" enctype="multipart/form-data" action="">
			<input type="submit" class="w3-btn w3-brown" value="Update date and time" name="update_date_from_client">
			<input type="hidden" name="client_time_input" id="client_time_input" value="">
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

<div id="usbpower" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round w3-padding">
		Turn Power to all USB ports on or off:
		<form method="POST" enctype="multipart/form-data" action="">
			<input type="submit" class="w3-btn w3-brown" value="USB Power ON" name="usb_power_on">
			<input type="submit" class="w3-btn w3-brown" value="USB Power OFF" name="usb_power_off">
		</form>

	</div>
	<div class="w3-panel w3-green w3-round w3-padding">
		Set a timer to power all USB ports on and off automatically:
		<form method="POST" enctype="multipart/form-data" action="<?php update_Config($config);?>"> 
			<label for="usb_timer_start_time"> Power on at:</label><br>
			<input class="w3-mobile" type="time" name="usb_timer_start_time" id="usb_timer_start_time" value="<?php echo isset($config['system']['usb_timer_start_time']) ? $config['system']['usb_timer_start_time'] : ""?>"><br>
			<label for="usb_timer_stop_time"> Power off at:</label><br>
			<input class="w3-mobile"  type="time" name="usb_timer_stop_time" id="usb_timer_stop_time" value="<?php echo isset($config['system']['usb_timer_stop_time']) ? $config['system']['usb_timer_stop_time'] : ""?>"><br><br>
			<input class="w3-mobile w3-btn w3-brown"  type="submit" value="Set Timer" name="set_usb_timer">
			<input class="w3-mobile w3-btn w3-brown"  type="submit" value="Disable Timer" name="disable_usb_timer"><br>
		</form>
	</div>
</div>

<div id="sensors" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round w3-padding">
    <h3>This feature is currently under development!</h3>
    This feature allows you to write data from different sensor to the database. It will also log key system figures such as CPU Temperature, CPU Load, Memory Load, File System Usage. All data will be written to the <em>rteu.sensors</em> table in the configured database.
		<form method="POST" class="w3-padding">
			<label for="interval">Polling interval in minutes:</label><br>
      <input type="number" class="w3-mobile" value="1" name="sensors_interval" style="margin-bottom:10px"><br>
			<input type="submit" class="w3-btn w3-brown" value="Start" name="sensors_start">
      <input type="submit" class="w3-btn w3-brown" value="Stop" name="sensors_stop">
		</form>
	</div>
	<div class="w3-panel w3-green w3-round w3-padding">
    Click here to create the table <em>rteu.sensors</em> if it doesn't already exists<br>
		<form method="POST" class="w3-padding">
      <input type="submit" class="w3-btn w3-brown" value="Create sensors table" name="sensors_create_table">
		</form>
    
    
	</div>
</div>	

<div id="passwords" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round w3-padding">
		<b>This page allows you to change the default passwords.</b><br>
		Never use quotation marks " or ' in passwords!
	</div>
	<div class="w3-panel w3-green w3-round w3-padding">
		<form method="POST">
			<b>Web-Interface:</b><br>
			Here you can change the password for user "pi" used to log into this web-interface.<br><br>
			Old password:<br>
			<input type="password" name="old_pw" id="old_pw" value=""> <br><br>
			New password:<br>
			<input type="password" name="new_pw" id="new_pw" value=""> <br><br>
			Confirm new password:<br>
			<input type="password" name="new_pw_confirm" name="new_pw_confirm" value=""> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Update password" name="update_password"><br>
		</form>
	</div>
	<div class="w3-panel w3-green w3-round w3-padding">
		<form method="POST">
			<b>MySQL: rteu</b><br>
			Here you can change the password of database-user "rteu". Don't forget to also set the new password in the <a href="../data/data.php">database settings</a>!<br>
			All services currently using the database will need to be restarted!<br><br>
			Old password:<br>
			<input type="password" name="old_mysql_pw" id="old_mysql_pw" value=""> <br><br>
			New password:<br>
			<input type="password" name="new_mysql_pw" id="new_mysql_pw" value=""> <br><br>
			Confirm new password:<br>
			<input type="password" name="new_mysql_pw_confirm" name="new_mysql_pw_confirm" value=""> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Update password" name="update_mysql_password"><br>
		</form>
	</div>
</div>	

<div id="infos" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round w3-padding">
		The Temperature of the CPU is:  <?php echo round(0.001 * intVal(fgets(fopen("/sys/class/thermal/thermal_zone0/temp","r"))))."&thinsp;&deg;C" ?>
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