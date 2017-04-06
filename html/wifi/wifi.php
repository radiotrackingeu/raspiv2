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
		</tr>

		<tr>
			<td>Wifi</td> 
			<td><input type="submit" value="Create AP" name="start_ap" /></td> 
			<td><input type="submit" value="Stop AP" name="stop_ap" /></td>
			<td><input type="submit" value="Start on Start" name="start_start" /></td>
		</tr>

	</table>
</form>
<?php

	if (isset($_POST["start_ap"])){
		echo '<pre>';
		$test = system('sudo docker run -t --privileged --net=host wifi 2>&1', $ret);
		echo '</pre>';
	}
	if (isset($_POST["stop_ap"])){
		echo '<pre>';
		$test = system('sudo docker stop $(sudo docker ps -a -q --filter ancestor=wifi) 2>&1', $ret);
		echo '</pre>';
	}
	//if (isset($_POST["start_start"])){
	//	echo '<pre>';
	//	$test = system('sudo docker run -t --privileged --net=host --restart unless-stopped wifi', $ret);
	//	echo '</pre>';
	//}
?>

