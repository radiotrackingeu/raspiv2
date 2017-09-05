<!DOCTYPE html>
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
	<div class="w3-panel w3-green w3-round">
		<br><br>
		<form method="POST" enctype="multipart/form-data">
			FFTs per second: <br>
			<input type="number" name="fft_fps" value="27"><br> <br>
			Number of bins in FFT: <br>
			<select name="fft_size">
				<option value="256">256</option>
				<option value="512" selected="selected">512</option>
				<option value="1024">1024</option>
				<option value="2048">2048</option>
				<option value="4096">4096</option>
			</select> <br><br>
			Sample rate / Frequency Range: <br>
			<select name="samp_rate">
				<option value="250000">250k</option>
				<option value="1024000">1024k</option>
			</select><br><br>
			Center Frequency in Hz: <br>
			<input type="number" name="center_freq" value="150100000"><br><br>
			Gain: <br>
			<input type="number" name="rf_gain" value="20"><br><br>
			<input type="submit" class="w3-btn w3-brown" value="Change settings befor start" name="change_config_websdr">
			<input type="submit" class="w3-btn w3-brown" value="Start" name="rtl_websdr">
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_websdr_stop">
			<br><br>
			<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+1)?>"> Link to OpenWebRX </a>
			<br><br>
		</form>
	</div>

<br><br>


	<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	if (isset($_POST["rtl_websdr"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/:/cfiles/ -p ".($_SERVER['SERVER_PORT']+1).":8073 webrx sh /cfiles/start_openwebrx.sh";
		$result = unliveExecuteCommand($cmd);
	}
	if (isset($_POST["rtl_websdr_stop"])){
		echo '<pre>';
		$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=webrx) 2>&1";
		$result = liveExecuteCommand($cmd);
		#echo $result;
		echo '</pre>';
	}
	if (isset($_POST["change_config_websdr"])){
		echo '<pre>';
		$cmd = "sh /var/www/html/sdr/change_config_webrx.sh ".$_POST["fft_fps"]." ".$_POST["fft_size"]." ".$_POST["samp_rate"]." ".$_POST["center_freq"]." ".$_POST["rf_gain"]." 2>&1";
		$result = liveExecuteCommand($cmd);
		echo $result;
		echo '</pre>';
	}
	?>
	
<br><br>

<br><br>
</div>

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

<?php 

function liveExecuteCommand($cmd)
{

    while (@ ob_end_flush()); // end all output buffers if any

    $proc = popen("$cmd 2>&1 ; echo Exit status : $?", 'r');

    $live_output     = "";
    $complete_output = "";

    while (!feof($proc))
    {
        $live_output     = fread($proc, 4096);
        $complete_output = $complete_output . $live_output;
        echo "$live_output";
        @ flush();
    }

    pclose($proc);

    // get exit status
    preg_match('/[0-9]+$/', $complete_output, $matches);

    // return exit status and intended output
    return array (
                    'exit_status'  => intval($matches[0]),
                    'output'       => str_replace("Exit status : " . $matches[0], '', $complete_output)
                 );
}
function unliveExecuteCommand($cmd)
{
    while (@ ob_end_flush()); // end all output buffers if any
    $proc = popen("$cmd 2>&1 ; echo Exit status : $?", 'r');
    pclose($proc);
}



?>