<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/resources/weblib/w3.css">
<link rel="stylesheet" href="/resources/weblib/css/font-awesome.min.css">
<link rel="stylesheet" href="/resources/additional.css">

<body>

<?php
	//load config
	require_once '../cfg/baseConfig.php';
	//load top menu
	require_once RESOURCES_PATH.'/header.php';
 ?>
 
<!-- Enter text here-->
<div class="w3-container">
<div class="w3-panel w3-green w3-round w3-padding">
	If you haven't done so yet, install the app. This only needs to be done once!
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="submit" class="w3-btn w3-brown" value="Install Player" name="install_playback"/>
	</form><br>
	The system will automatically reboot after installing. 
</div>
<div class="w3-panel w3-green w3-round w3-padding">
	Select file to play:<br>
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<select class="w3-mobile"  name="playback_file">
	<?php foreach(glob("files/*.wav") as $filename) {
		echo "<option value='".basename($filename)."'>".basename($filename)."</option>";
	}?>
	</select>
	<br><br>
			<input type="submit" class="w3-btn w3-brown" value="Start Playback" name="start_playback">
			<input type="submit" class="w3-btn w3-brown" value="Stop Playback" name="stop_playback">
	</form>
</div>
<div class="w3-panel w3-green w3-round w3-padding">
	<form method="POST" enctype="multipart/form-data">
	Upload Files:
		<div class="w3-bar w3-padding">
			<input type="submit" class="w3-btn w3-brown" value="Upload" name="ul_wav">
			<input type="file" class="w3-btn w3-green" name="file_wav[]" style="hover:none" multiple>
		</div>
	</form><br>
	Please refresh the page after uploading.
</div>
<div class="w3-panel w3-green w3-round w3-padding">
	Select files to delete:<br>
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<select class="w3-mobile"  name="files_delete[]" multiple>
		<?php foreach(glob("files/*.wav") as $filename) {
			echo "<option value='".basename($filename)."'>".basename($filename)."</option>";
		}?>
		</select>
		<br><br>
		<input type="submit" class="w3-btn w3-brown" value="Delete Files" name="delete_files">
	</form><br>
	Please refresh the page after deleting.
</div>
</div>
<div id="container">
	
	<p>
	<?php
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		if (isset($_POST["start_playback"])){
				echo '<pre>';
				system("sudo docker run -td --restart=unless-stopped --privileged -v /var/www/html/playback/files/:/tmp/ playback:1.0 play /tmp/".$_POST['playback_file']." repeat 10000 2>&1 > /dev/null", $ret);
				echo '</pre>';
		}

		if (isset($_POST["stop_playback"])){
				echo '<pre>';
				system("sudo docker stop \$(sudo docker ps -a -q --filter ancestor=playback:1.0 --filter status=running) 2>&1 > /dev/null", $ret);
				echo '</pre>';
		}
		
		if (isset($_POST["install_playback"])){
			echo '<pre>';
			system('sudo docker build -t playback:1.0 /home/pi/gitrep/raspiv2/Docker/playback/. 2>&1', $ret);
			$search = 'dtparam=audio=on';
			$change= "#".$search;
			$file_to_replace="/tmp/config.txt";
			$cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /boot/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";
			system($cmd_change);
			$search = 'dtoverlay=hifiberry-dac';
			$change= $search;
			$file_to_replace="/tmp/config.txt";
			$cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /boot/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";			
			system($cmd_change);
			system("sudo reboot now");
			echo '</pre>';
		}
		
		if (isset($_POST["ul_wav"])) {
			$total = count($_FILES["file_wav"]["name"]);
			if ($total==0)
				echo "No files given!<br>";
			else
				for ($i=0; $i<$total; $i++) {
					if ($_FILES["file_wav"]["name"] == "") continue;
					if ($_FILES["file_wav"]["size"] == 0 ) continue;
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mtype = finfo_file($finfo,$_FILES["file_wav"]["tmp_name"][$i]);
					if ($mtype != "audio/x-wav") {
						echo $_FILES["file_wav"]["name"][$i]." is not a .wav file and was skipped.<br>";
						continue;
					}
					if (move_uploaded_file($_FILES["file_wav"]["tmp_name"][$i], "files/".$_FILES["file_wav"]["name"][$i])){
								echo "Successfully uploaded ".$_FILES["file_wav"]["name"][$i].".<br>";
							} else {
								echo "Could not upload ".$_FILES["file_wav"]["name"][$i]."!<br>";
							}
				}
		}
		
		if (isset($_POST["delete_files"])){
			for ($i=0; $i< count($_POST['files_delete']); $i++) {
				if (unlink("files/".$_POST['files_delete'][$i])) 
					echo "Successfully deleted ".$_POST['files_delete'][$i]."<br>";
				else 
					echo "Could not delete ".$_POST['files_delete'][$i]."<br>";
			}
		}
	
	?>
	</p>
<!-- Enter text here-->

</div>

<div class="w3-container w3-center w3-brown">
  Online-Website: <a href="https://radio-tracking.eu/">radio-tracking.eu</a>
  Email: <a href= "mailto:info@radio-tracking.eu">info@radio-tracking.eu</a>
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


</body>

</html>