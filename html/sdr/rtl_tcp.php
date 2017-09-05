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
 ?>
 
<!-- Enter text here-->
<div id="UMTS" class="w3-container">
<br>
Start or stop the SDR# Server. Remember that only one service can run on each device. 
<br><br>
<form method='POST'> 
<input type="submit" class="w3-btn" value="Start" name="rtl_tcp_start" />
<input type="submit" class="w3-btn" value="Start with Port 81" name="rtl_tcp_start_81" />
<input type="submit" class="w3-btn" value="Stop" name="rtl_tcp_stop" />
</form> 
<br>

Please enter the following information in SDR#
<br><br>
Host:<?php echo $_SERVER['SERVER_NAME']; ?>
<br>
Port:<?php echo ($_SERVER['SERVER_PORT']+1); ?>
<br><br>

</div>
<?php
	if (isset($_POST["rtl_tcp_start"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1234 rtlsdr rtl_tcp -a  '0.0.0.0' -p '1234' 2>&1";
		$result = system($cmd);
	}
	if (isset($_POST["rtl_tcp_start_81"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -p 81:1234 rtlsdr rtl_tcp -a  '0.0.0.0' -p '1234' 2>&1";
		$result = system($cmd);
	}
	if (isset($_POST["rtl_tcp_stop"])){
		$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=rtlsdr) 2>&1";
		echo '<pre>';
		$result = system($cmd);
		echo '</pre>';
	}
?>

<!-- Enter text here-->

<script>
function openCity(cityName) {
    var i;
    var x = document.getElementsByClassName("city");
    for (i = 0; i < x.length; i++) {
       x[i].style.display = "none";  
    }
    document.getElementById(cityName).style.display = "block";  
}
function w3_switch(name) {
	var x = document.getElementById(name);
    if (x.style.display == "none") {
        x.style.display = "block";
    } else { 
        x.style.display = "none";
    }
}
</script>


</body>

</html>
