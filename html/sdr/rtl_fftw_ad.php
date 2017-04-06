<form method='POST'> 
<img src="fridata.png" alt="www.fridata.de" style = "width:600px">
<br><br>
***** <a href="index.html">Back to Main Menu</a> ***** <a href="http://radio-tracking.eu">Offical Project Website</a> *****
<br><br>
If you got Questions, don't hesitate to contact me: <a href= "mailto:ralf.zeidler@fridata.de">ralf.zeidler@fridata.de</a>. 
<br>
<br>To record a Frequency Spektrum for a given time, just modify the entries below and press Start.

<h3>Record properties</h3><br>
<table style="width:90%">

	<tr>
		<td>Frequency Range:</td>
		<td><input type="text" name="srate" value="250000"></td>
		<td>Sets the range of observed frequencies around the center frequency</td>
	</tr>
	<tr>
		<td>Center Frequency:</td>
		<td><input type="text" name="cfreq" value="150190k"></td>
		<td>Frequency in the middle of the frequency range</td>
	</tr>
	<tr>
		<td>Number of Bins:</td>
		<td><input type="text" name="bins" value="300"></td>
		<td>Frequency resolution, more bins need more processing power. The Frequency Range divided by the number of bins results in the single frequency steps recorded</td>
	</tr>
	<tr>
		<td>Integration time in seconds:</td>
		<td><input type="text" name="itime" value="0.1"></td>
		<td>This is the effectiv integration time, which means the time the signal is actually recorded, without the FFT Process. </td>
	</tr>
	<tr>
		<td>Gain in mDB:</td>
		<td><input type="text" name="gain" value="400"></td>
		<td>Gain of the recording device. Higher gains result in more noise.</td
	</tr>
	<tr>
		<td>Device Number:</td>
		<td><input type="text" name="device" value="0"></td>
		<td>If several SDR devices are connected, you can start several recordings (right now tested up to two) using differnt devices. The numbers are consecutive, increasing and starting at 0.</td>
	</tr>
	<tr>
		<td>Record Time:</td>
		<td><input type="text" name="rtime" value="1m"></td>
		<td>The is the actual overall recording time. You can use units like m for minutes and h for hours.</td>
	</tr>
	<tr>
		<td>Record Name:</td>
		<td><input type="text" name="rname" value="d0"></td>
		<td>Each record will be given a file name, be careful, the same name will overwrite existing files. You can find the results here: <a href="/record/">Record Folder</a></td>
	</tr>
</table>

<br>
<br>
Start or stop recording/s: 
<br>
<br>
<input type="submit" value="Start" name="rtl_tcp_start" />
<input type="submit" value="Stop last" name="rtl_tcp_stop_last" />
<input type="submit" value="Stop all" name="rtl_tcp_stop_all" />
</form> 
<?php
	session_start();
	$cmd = "rtl_power_fftw -r " . $_POST["srate"]. " -f " . $_POST["cfreq"]. " -b " . $_POST["bins"]. " -t " . $_POST["itime"]. " -g " . $_POST["gain"]. " -q -d " . $_POST["device"]. " -e " . $_POST["rtime"]. " -m /var/www/html/record/" . $_POST["rname"];
	$descr = array(
    		0 => array(
        		'pipe',
        		'r'
    		) ,
    		1 => array(
        		'pipe',
	        	'w'
		) ,
		2 => array(
        		'pipe',
        		'w'
    		)
	);
	$pipes = array();

	
	
	if (isset($_POST["rtl_tcp_start"])){
		echo "<br /><br /> Server started - ";
		$process = proc_open($cmd, $descr, $pipes);
	
	
		if (is_resource($process))
		{
    		// We have a running process. We can now get the PID
    		$info = proc_get_status($process);

    		// Store PID in session to later kill it
    		$_SESSION['current_pid'] = $info['pid'];
		echo fgets($pipes[2], 1024);
		echo fgets($pipes[2], 1024);
		echo fgets($pipes[2], 1024);    
}
	} 
	if (isset($_POST["rtl_tcp_stop_last"])){
		echo "<br /><br /> Stoped last started recording";
		$pid = $_SESSION['current_pid']+1;
		system("kill -9 $pid");
	}
	if (isset($_POST["rtl_tcp_stop_all"])){
		echo "<br /><br /> Stopped all recordings";
		system("pkill -9 rtl_power_fftw");
	}



?>
<br><br>
***** <a href="index.html">Back to Main Menu</a> ***** <a href="http://radio-tracking.eu">Offical Project Website</a> *****
<br><br>



