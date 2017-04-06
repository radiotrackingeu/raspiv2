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


<nav class="w3-sidenav w3-light-grey" style="width:25%">
	<h4> <b> Options to choose:</b></h4>
	<a class="w3-green" href="/index.html"><i class="fa fa-home"></i> Home</a>
	<div class="w3-dropdown-hover">
		<a href="#"><i class="fa fa-podcast"></i> Radio <i class="fa fa-caret-down"></i></a>
		<div class="w3-dropdown-content w3-white w3-card-4">
			<a href="/sdr/rtl_fm.php">WebRadio</a>
			<a href="/sdr/rtl_fftw.php">Recorder</a>
			<a href="/sdr/rtl_tcp.php">SDR# Server</a>
			<a href="/sdr/websdr.php">Web Server</a>
		</div>
	</div>
	<div class="w3-dropdown-hover">
		<a href="#"><i class="fa fa fa-exchange"></i> Www <i class="fa fa-caret-down"></i></a>
		<div class="w3-dropdown-content w3-white w3-card-4">
			<a href="/connect/connect.php">Start</a>
			<a href="/connect/umts_setup.php">UMTS Setup</a>
			<a href="/connect/vpn_setup.php">VPN Setup</a>
		</div>
	</div>
	<div class="w3-dropdown-hover">
		<a href="/system/system.php"><i class="fa fa-wrench"></i> System <i class="fa fa-caret-down"></i></a>
		<div class="w3-dropdown-content w3-white w3-card-4">
			<a href="/git/gitlab.php">Software</a>
			<a href="/connect/umts_setup.php">UMTS Setup</a>
		</div>
	</div>
	<a href="/licence.php"><i class="fa fa-registered"></i> License</a>
</nav>

<div style="margin-left:25%">

<!-- Enter text here-->


 <div class="w3-bar w3-black">
  <button class="w3-bar-item w3-button" onclick="openCity('VPN')">VPN</button>

</div>

<form method='POST' enctype="multipart/form-data"> 
	<div id="VPN" class="w3-container city">
		<p>APN needs to be changed acordingly the provider's needs. The Swtich Mode must be executed if the UMTS Dongle has been removed while the system was running. </p>
		<input type="submit" class="w3-btn" value="Swtich Mode" name="switch_mode">
		<?php
			if (isset($_POST["switch_mode"])){ 
				$cmd = "sudo docker run --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 1f01 -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";
				$result = liveExecuteCommand($cmd);
			}
		?>
		<br><br>
		Standard Settings: Edeka Mobil
		<br><br>
		<label class="w3-label">First Name</label>
		<input type="text" class="w3-input" value="&quot;data.access.de&quot;" name="apn">
		<label class="w3-label">First Name</label>
		<input type="text" class="w3-input" value="*99***1#" name="dial">
		<input type="submit" class="w3-btn" value="Change Settings" name="change_wvdial">
		<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			if (isset($_POST["change_wvdial"])){ 
				$cmd1 = "cp /var/www/html/connect/edeka.conf /var/www/html/connect/wvdial.conf";
				$cmd2 = "sed -i 's/Init3 = AT+CGDCONT=1,\"IP\",.*$/Init3 = AT+CGDCONT=1,\"IP\",".$_POST["apn"]."/' /var/www/html/connect/wvdial.conf"; 
				$cmd3 = "sed -i 's/Phone = .*$/Phone = ".$_POST["dial"]."/' /var/www/html/connect/wvdial.conf"; 
				$result = liveExecuteCommand($cmd1);
				$result = liveExecuteCommand($cmd2);
				$result = liveExecuteCommand($cmd3);
			}
		?>
	</div>
</form>
<script>
function openCity(cityName) {
    var i;
    var x = document.getElementsByClassName("city");
    for (i = 0; i < x.length; i++) {
       x[i].style.display = "none";  
    }
    document.getElementById(cityName).style.display = "block";  
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