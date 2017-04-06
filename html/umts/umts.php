<form method='POST'> 
	<title>radio-tracking.eu</title>
	<img src="/images/logo.PNG" alt="www.radio-tracking.eu" style = "width:270px">
	<h1>radio-tracking.eu</h1>
	<br><br>
		***** <a href="index.html">Back to Main Menu</a> ***** <a href="http://radio-tracking.eu">Offical Project Website</a> *****
	<br><br>
	If you got Questions, don't hesitate to contact me: <a href= "mailto:ralf.zeidler@fridata.de">ralf.zeidler@fridata.de</a>. 
	<br>
	<br>First download then install the feature - installing requires also an internet connection and requires some time.  
	<table>
		<tr> 
			<th>Feature</th> 
			<th>Download</th> 
			<th>Install</th>
			<th>Desciption</th>
			<th>Other</th> 
		</tr>

		<tr>
			<td>Wifi</td> 
			<td><input type="submit" value="Switch Mode" name="start_mod" /></td> 
			<td><input type="submit" value="Start UMTS" name="start_umts" /></td>
			<td><input type="submit" value="Stop UMTS" name="stop_umts" /></td>
			<td><input type="submit" value="Start OpenVPN" name="start_openvpn" /></td>
		</tr>

	</table>
</form>
<?php 

	if (isset($_POST["start_mod"])){
		while (@ ob_end_flush()); // end all output buffers if any
	
		$cmd = "sudo docker run --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 1f01 -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";

		$proc = popen($cmd, 'r');
		echo '<pre>';
		while (!feof($proc))
		{
			echo fread($proc, 4096);
			@ flush();
		}
		echo '</pre>';
	}
	if (isset($_POST["start_umts"])){
		
		$ip = exec("ip -f inet addr show ppp0 | grep -Po 'inet \K[\d.]+'");
		
		while (@ ob_end_flush()); // end all output buffers if any
	
		$cmd = "sudo docker run --privileged --net=host -t umts wvdial 2>&1";

		$proc = popen($cmd, 'r');
		echo '<pre>';
		while (!feof($proc))
		{
			echo fread($proc, 4096);
			@ flush();
		}
		echo '</pre>';
	}
	if (isset($_POST["start_openvpn"])){
		
		$ip = exec("ip -f inet addr show ppp0 | grep -Po 'inet \K[\d.]+'");
		
		while (@ ob_end_flush()); // end all output buffers if any
	
		$cmd = "sudo docker run --privileged --net=host -t umts openvpn /etc/openvpn/client.conf 2>&1";

		$proc = popen($cmd, 'r');
		echo '<pre>';
		while (!feof($proc))
		{
			echo fread($proc, 4096);
			@ flush();
		}
		echo '</pre>';
	}
	if (isset($_POST["stop_umts"])){
		while (@ ob_end_flush()); // end all output buffers if any
	
		$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=umts) 2>&1"; 
		$proc = popen($cmd, 'r');
		echo '<pre>';
		while (!feof($proc))
		{
			echo fread($proc, 4096);
			@ flush();
		}
		echo '</pre>';
	}

?>