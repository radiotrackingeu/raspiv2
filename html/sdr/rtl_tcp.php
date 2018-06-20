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
?>
 
<!---------------- Tab Menu -------------------------->
<div class="w3-bar w3-brown">
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event,'sdr_server')">SDR#-Server</button>
</div>

<!-- Enter text here-->

<div id="sdr_server" class="w3-container city w3-row-padding w3-mobile" style="display:none">
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver 0</h3>
			<br>
			Start or stop the SDR# Server.
			<br><br>
			<form method='POST'> 
				<input type="submit" class="w3-btn w3-brown" value="Start" name="rtl_tcp_start_d0" />
				<input type="submit" class="w3-btn w3-brown" value="Start with Port 81" name="rtl_tcp_start_81_d0" />
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_tcp_stop_d0" />
			</form> 
			<br>

			Please enter the following information in SDR# - except if you pressed the Start with Port 81 Button, then it is 81.
			<br><br>
			Host:<?php echo $_SERVER['SERVER_NAME'];?>
			<br>
			Port:<?php echo ($_SERVER['SERVER_PORT']+1);?>
			<br><br>
		</div>
	</div>
	<div class="w3-half">
		<div class="w3-panel w3-green w3-round">
			<h3>Receiver 1</h3>
			<br>
			Start or stop the SDR# Server.
			<br><br>
			<form method='POST'> 
				<input type="submit" class="w3-btn w3-brown" value="Start" name="rtl_tcp_start_d1" />
				<input type="submit" class="w3-btn w3-brown" value="Start with Port 82" name="rtl_tcp_start_82_d1" />
				<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_tcp_stop_d1" />
			</form> 
			<br>

			Please enter the following information in SDR# - except if you pressed the Start with Port 82 Button, then it is 82.
			<br><br>
			Host:<?php echo $_SERVER['SERVER_NAME']; ?>
			<br>
			Port:<?php echo ($_SERVER['SERVER_PORT']+2); ?>
			<br><br>
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
