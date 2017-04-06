<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/weblib/w3.css">
<link rel="stylesheet" href="/weblib/css/font-awesome.min.css">

<body>

<div class="w3-container w3-green">
<h1>radio-tracking.eu</h1>

  <img src="/images/logo_rteu.png" alt="Nice car" style="width:20%">
 <br><br>
 
</div>


<nav class="w3-sidenav w3-bar-block w3-light-grey w3-card-2" style="width:25%">
	<h4> <b> Options to choose:</b></h4>
	<a class="w3-green w3-bar-item w3-button" href="/index.html"><i class="fa fa-home"></i> Home</a>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('radio')">
		<i class="fa fa-podcast"></i> Radio <i class="fa fa-caret-down"></i></div>
		<div id="radio" class="w3-hide w3-white w3-card-4">
			<a href="/sdr/rtl_fm.php">WebRadio</a>
			<a href="/sdr/rtl_fftw.php">Recorder</a>
			<a href="/sdr/rtl_tcp.php">SDR# Server</a>
			<a href="/sdr/websdr.php">Web Server</a>
		</div>
	</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('www')">
		<i class="fa fa fa-exchange"></i> Www <i class="fa fa-caret-down"></i></div>
		<div id="www" class="w3-hide w3-white w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">UMTS Setup</a>
			<a href="/connect/vpn_setup.php">VPN Setup</a>
		</div>
	</div>
	<div class="w3-bar-item w3-button" onclick="myAccFunc('system')">
		<i class="fa fa-wrench"></i> System <i class="fa fa-caret-down"></i></div>
		<div id="system" class="w3-hide w3-white w3-card-4">
			<a href="/git/gitlab.php">Software</a>
			<a href="/connect/umts_setup.php">UMTS Setup</a>
		</div>
	</div>
	<a href="/licence.php"><i class="fa fa-registered"></i> License</a>
</nav>

<div style="margin-left:25%">

<!-- Enter text here-->

<div class="w3-bar w3-black">
  <button class="w3-bar-item w3-button" onclick="openCity('Radio')">VPN</button>
</div>
<div id="UMTS" class="w3-container">
<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<h3>Receiver Settings</h3><br>
<table style="width:90%">

	<tr>
		<td>Frequencies:</td>
		<td><input type="text" name="freq" value="<?php echo isset($_POST['freq']) ? $_POST['freq'] : '150.1M' ?>" /></td>
		<td>Set the frequency you want to listen to. You can use multipliers like M and k. Turn slightly below the frequency for better results. Together with a treshold bigger then 0 you can scan multiple frequencies if you add a -f (i.e. 150.1M -f 150.120M) </td>
	</tr>
	<tr>
		<td>Gain in DB:</td>
		<td><input type="text" name="gain" value="<?php echo isset($_POST['gain']) ? $_POST['gain'] : '20' ?>" /></td>
		<td>Set a gain value. Remeber higher gains results in higher noise levels. </td>
	</tr>
</table>

<br>
<br>
Start and Stop receiver - to set a new frequency/gain, first stop and restart: 
<br>
<br>
<input type="submit" class="w3-btn" value="Start" name="rtl_fm_start_l"/>
<input type="submit" class="w3-btn" value="Stop" name="rtl_stop"/>
<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	if (isset($_POST["rtl_fm_start_l"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " . $_POST["freq"]. " -g " . $_POST["gain"]. " -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		$result = unliveExecuteCommand($cmd);
	} 
	if (isset($_POST["rtl_stop"])){
		echo '<pre>';
		$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=rtlsdr) 2>&1";
		$result = liveExecuteCommand($cmd);
		echo $result;
		echo '</pre>';
	}
?>
</form> 
<br>
<br>

<audio controls>
  <source src="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+1); ?>" type="audio/mpeg" controls preload="none">
  Your browser does not support the audio element.
</audio>
</div>
<script>
function openCity(cityName) {
    var i;
    var x = document.getElementsByClassName("city");
    for (i = 0; i < x.length; i++) {
       x[i].style.display = "none";  
    }
    document.getElementById(cityName).style.display = "block";  
}
function myAccFunc(element_id) {
    var x = document.getElementById(element_id);
    if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
        x.previousElementSibling.className += " w3-green";
    } else { 
        x.className = x.className.replace(" w3-show", "");
        x.previousElementSibling.className = 
        x.previousElementSibling.className.replace(" w3-green", "");
    }
}

function myDropFunc(element_id) {
    var x = document.getElementById(element_id);
    if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
        x.previousElementSibling.className += " w3-green";
    } else { 
        x.className = x.className.replace(" w3-show", "");
        x.previousElementSibling.className = 
        x.previousElementSibling.className.replace(" w3-green", "");
    }
}
</script>

<!-- Enter text here-->

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