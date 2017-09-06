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
	//load functions
	require_once RESOURCES_PATH.'/helpers.php';
	//load ConfigLite
	require_once CONFIGLITE_PATH.'/Lite.php';
	
	//define config section and items.
	define ('confSection', 'SDR_Radio');
	define ('confKeys', array('Freq1','Freq2','Freq3','Freq4','Freq5','Freq6', 'Radio_Gain'));
	
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');	
?>

<!-- Enter text here-->

<div class="w3-bar w3-brown">
  <button class="w3-bar-item w3-button" onclick="openCity('Radio')">Single Frequency</button>
</div>
<div id="UMTS" class="w3-container">
<form method="post" enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">

<h3>Receiver Settings</h3><br>
<table style="width:90%">

	<tr>
		<td>Frequencies:</td>
		<td><input type="text" name="Freq3" value="<?php echo isset($config['SDR_Radio']['Freq3']) ? $config['SDR_Radio']['Freq3'] : "150.1M" ?>" /></td>
		<td>Set the frequency you want to listen to. You can use multipliers like M and k. Turn slightly below the frequency for better results. Together with a treshold bigger then 0 you can scan multiple frequencies if you add a -f (i.e. 150.1M -f 150.120M) </td>
	</tr>
	<tr>
		<td>Gain in DB:</td>
		<td><input type="text" name="Radio_Gain" value="<?php echo isset($config['SDR_Radio']['Radio_Gain']) ? $config['SDR_Radio']['Radio_Gain'] : 20 ?>" /></td>
		<td>Set a gain value. Remember higher gains result in higher noise levels. </td>
	</tr>
</table>

<br>
<br>
Start and Stop receiver - to set a new frequency/gain, first stop and restart: 
<br>
<br>
<input type="submit" class="w3-btn w3-brown" value="Start Browser playback" name="rtl_fm_start_s"/>
<input type="submit" class="w3-btn w3-brown" value="Start Local playback" name="rtl_fm_start_l"/>
<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_stop"/>

<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	if (isset($_POST["rtl_fm_start_s"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " . $_POST["Freq3"]. " -g " . $_POST["Radio_Gain"]. " -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		$result = unliveExecuteCommand($cmd);
	}
	if (isset($_POST["rtl_fm_start_l"])){
		$cmd = "sudo docker run --rm -t --privileged rtlsdr sh -c 'rtl_fm -M usb -f " . $_POST["Freq3"]. " -g " . $_POST["Radio_Gain"]. " -d 0 | play -r 32k -t raw -v 5 -e s -b 16 -c 1 -V1 -'";
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

<?php
	//load footer
	require_once RESOURCES_PATH.'/footer.php';
 ?>

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