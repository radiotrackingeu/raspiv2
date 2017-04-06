<form method='POST'> 
<img src="fridata.png" alt="www.fridata.de" style = "width:600px">
<br><br>
***** <a href="index.html">Back to Main Menu</a> ***** <a href="http://radio-tracking.eu">Offical Project Website</a> *****
<br><br>
If you got Questions, don't hesitate to contact me: <a href= "mailto:ralf.zeidler@fridata.de">ralf.zeidler@fridata.de</a>. 
<br>
<br>First edit the settings and then start the receiver either locally, which uses the Raspberry-Pi Audio Output. Or you can start a stream and use the deveice IP and Port of the stream to connect via a client (i.e. <a href="http://www.videolan.org/vlc/">VLC</a>). 

<h3>Receiver Settings</h3><br>
<table style="width:90%">

	<tr>
		<td>Frequencies:</td>
		<td><input type="text" name="freq" value="150.1M"></td>
		<td>Set the frequency you want to listen to. You can use multipliers like M and k. Turn slightly below the frequency for better results. Together with a treshold bigger then 0 you can scan multiple frequencies if you add a -f (i.e. 150.1M -f 150.120M) </td>
	</tr>
	<tr>
		<td>Squelch Level:</td>
		<td><input type="text" name="squel" value="0"></td>
		<td>Set a treshold level to silence the noise. This setting is necessary if you want to scan multiple frequencies.</td>
	</tr>
	<tr>
		<td>Gain in DB:</td>
		<td><input type="text" name="gain" value="40"></td>
		<td>Set a gain value. Remeber higher gains results in higher noise levels. </td>
	</tr>
	<tr>
		<td>Device Number:</td>
		<td><input type="text" name="device" value="0"></td>
		<td>If several SDR devices are connected, you can start several recordings (right now tested up to two) using differnt devices. The numbers are consecutive, increasing and starting at 0.</td>
	</tr>
</table>

<br>
<br>
Start and Stop receiver - if change settings, first stop and restart: : 
<br>
<br>
<input type="submit" value="Start Lokal" name="rtl_fm_start_l" />
<input type="submit" value="Start Stream" name="rtl_fm_start_s" />
<input type="submit" value="Stop" name="rtl_fm_stop" />
</form> 
<?php
	session_start();
	//| aplay -r 24k -f S16_LE -t raw -c 1" ;
	$cmd_l = "rtl_fm -M usb -f " . $_POST["freq"]. " -g " . $_POST["gain"]. " -l " . $_POST["squel"]. " -d " . $_POST["device"]. "| play -v 20 -t raw -r 24k -es -b 16 -c 1 -V1 - ";
	$cmd_s = "rtl_fm -M usb -f " . $_POST["freq"]. " -g " . $_POST["gain"]. " -l " . $_POST["squel"]. " -d " . $_POST["device"]. "| sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240";

	if (isset($_POST["rtl_fm_start_l"])){
		echo "<br /><br /> Server gestartet - ";
		$process = popen($cmd_l, "r");
		echo "<br>" . $cmd_l;
	
	
		if (is_resource($process))
		{
    		// We have a running process. We can now get the PID
    		$info = proc_get_status($process);

    		// Store PID in session to later kill it
    		$_SESSION['current_pid'] = $info['pid'];
		echo fgets($pipes[2], 1024). "<br>";
		echo fgets($pipes[2], 1024). "<br><br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		ob_flush();
        	flush();
		}
	} 
	if (isset($_POST["rtl_fm_start_s"])){
		echo "<br /><br /> Server started - ";
		$process = popen($cmd_s, "r");
		//echo "<br>" . $cmd_s;
		$ip = exec("ip -f inet addr show eth0 | grep -Po 'inet \K[\d.]+'");
		echo "\n the link is: <a href=tcp://", $ip, ":1240>tcp://", $ip, ":1240</a>","\n";
	
		if (is_resource($process))
		{
    		// We have a running process. We can now get the PID
    		$info = proc_get_status($process);

    		// Store PID in session to later kill it
    		$_SESSION['current_pid'] = $info['pid'];
		echo fgets($pipes[2], 1024). "<br>";
		echo fgets($pipes[2], 1024). "<br><br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		echo fgets($pipes[1], 1024). "<br>";
		ob_flush();
        	flush();
		}
	}
	if (isset($_POST["rtl_fm_stop"])){
		echo "<br /><br /> Server angehalten";
		$pid = $_SESSION['current_pid']+1;
		system("kill -9 $pid");
		system("pkill -9 rtl_fm");
	}




?>
<br><br>
***** <a href="index.html">Back to Main Menu</a> ***** <a href="http://radio-tracking.eu">Offical Project Website</a> *****
<br>



