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


 <div class="w3-bar w3-brown">
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('Both')">Connect</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('UMTS')">UMTS</button>
  <button class="w3-bar-item w3-button w3-mobile" onclick="openCity('VPN')">VPN</button>
</div>

<form method='POST'> 
	<div id="UMTS" class="w3-container city" style="display:none">
		<p>UMTS is used when no other Internet Connection is avaible. The proper stick needs to be pluged in and installed.</p>
		<input type="submit" class="w3-btn w3-brown" value="Start UMTS" name="start_umts">
		<input type="submit" class="w3-btn w3-brown" value="Stop UMTS" name="stop_umts">
	</div>

	<div id="VPN" class="w3-container city" style="display:none">
		<p>Start a tunnel so the Raspberry pi can be oprated via www. A licence is needed.</p> 
		<input type="submit" class="w3-btn w3-brown" value="Start VPN" name="start_vpn">
		<input type="submit" class="w3-btn w3-brown" value="Stop VPN" name="stop_vpn">
	</div>

	<div id="Both" class="w3-container city" style="display:none">
		<div class="w3-panel w3-green w3-round-xlarge">
			<p>First UMTS will be connected then the VPN</p>
			<input type="submit" class="w3-btn w3-brown" value="Start Both" name="start_umts_vpn">
			<input type="submit" class="w3-btn w3-brown" value="Stop Both" name="stop_umts_vpn">
			<br><br>
		</div>
	</div>
	
	<div id="output" class="w3-container city" style="display:block">
	<br> Please choose one of the option shown above - the result will be displayed here:
		<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			if (isset($_POST["start_umts_vpn"])){ 
				$cmd1 = "sudo docker run --rm --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 1f01 -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";
				$cmd2 = "sudo docker run --rm --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 15ca -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";
				$cmd3 = "sudo docker run --rm -v /var/www/html/connect/:/config/ --privileged --net=host -t umts sh /config/start_umts.sh 2>&1";
				$result1 = liveExecuteCommand($cmd1);
				$result2 = liveExecuteCommand($cmd2);
				sleep(2);
				$result3 = unliveExecuteCommand($cmd3);
			}
			if (isset($_POST["stop_umts_vpn"])){
				$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=umts) 2>&1"; 
				$result = liveExecuteCommand($cmd);
			}
			if (isset($_POST["stop_vpn"])){
				$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=umts) 2>&1"; 
				$result = liveExecuteCommand($cmd);
			}
			if (isset($_POST["start_vpn"])){ 
				$cmd = "sudo docker run --rm -v /var/www/html/connect/:/config/ --privileged --net=host -t umts openvpn /config/client.conf 2>&1";
				$result = unliveExecuteCommand($cmd);
			}
			if (isset($_POST["stop_umts"])){
				$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=umts) 2>&1"; 
				$result = liveExecuteCommand($cmd);
			}
			if (isset($_POST["start_umts"])){
				$cmd1 = "sudo docker run --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 1f01 -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";
				$cmd2 = "sudo docker run --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 15ca -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";				
				$cmd3 = "sudo docker run --privileged --net=host -t umts wvdial 2>&1";
				$result = liveExecuteCommand($cmd1);
				$result = liveExecuteCommand($cmd2);
				sleep(2);
				$result = unliveExecuteCommand($cmd3);
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
function w3_switch(name) {
	var x = document.getElementById(name);
    if (x.style.display == "none") {
        x.style.display = "block";
    } else { 
        x.style.display = "none";
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