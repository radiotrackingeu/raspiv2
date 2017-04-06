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


	<div id="VPN" class="w3-container city">
		<p>APN needs to be changed acordingly the provider's needs.</p>
		    <form method="post" enctype="multipart/form-data">
				Select config-file to upload:
				<input type="file" name="fileToUpload" id="fileToUpload">
				<input type="submit" value="Upload Config" name="upload_cfg">
				<br><br><br>
				<input type="submit" class="w3-btn" value="Remove Config" name="rm_config">
			</form>
			<?php
				error_reporting(E_ALL);
				ini_set('display_errors', 1);
				if (isset($_POST["upload_cfg"])){
					$target_dir = "/connect/";
					$target_file = "/var/www/html/connect/client.conf";
					$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
					if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
						echo "The file has been uploaded.";
					} else {
						echo "Sorry, there was an error uploading your file.";
					}
				}				
				if (isset($_POST["rm_config"])){
					$cmd = "rm /var/www/html/connect/client.conf";
					$result = liveExecuteCommand($cmd);
					echo "Config has been removed";
				}
				
			?>
			
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