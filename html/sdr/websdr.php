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
<div id="UMTS" class="w3-container">
<br><br>
<form method="POST">
 <input type="submit" class="w3-btn" value="Start" name="rtl_websdr">
 <input type="submit" class="w3-btn" value="Stop" name="rtl_websdr_stop">
</form>

<br><br>
<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+1)?>"> Link to Interface </a>

	<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	if (isset($_POST["rtl_websdr"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":8073 webrx";
		echo $cmd;
		$result = unliveExecuteCommand($cmd);
	}
		if (isset($_POST["rtl_websdr_stop"])){
		echo '<pre>';
		$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=webrx) 2>&1";
		$result = liveExecuteCommand($cmd);
		echo $result;
		echo '</pre>';
	}
	?>
	
<br><br>

<br><br>
</div>

<!-- Enter text here-->
</div>


<script>
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