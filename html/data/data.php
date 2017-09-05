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

<div class="w3-bar w3-brown w3-mobile">
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('camera_data')">Camera</button>
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('radio_data')">Radio</button>
</div>

<div id="camera_data" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br>

		<h3>Zip Camera's record folder</h3><br>
		<form method='POST' enctype="multipart/form-data">
			<input type="text" name="zip_name" value="<?php echo "Camera_".date('Y_m_d_H_i')?>">
			<input type="submit" class="w3-btn w3-brown" value="Zip All Camera Recordings" name="zip_camera" /> <br><br>
			You can find the zipped files here: <a href="/picam/zipped/">Record Folder</a> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Delete all recordings" name="rm_cam_record_folder" />
			<input type="submit" class="w3-btn w3-brown" value="Delete all zipped files" name="rm_cam_zip_folder" /><br><br>

		</form>
		<br>
	</div>
</div>
<div id="radio_data" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br>

		<h3>Zip Logger's record folder</h3><br>
		<form method='POST' enctype="multipart/form-data">
			<input type="text" name="zip_name" value="<?php echo "Logger_".date('Y_m_d_H_i')?>">
			<input type="submit" class="w3-btn w3-brown" value="Zip All Logger Recordings" name="zip_logger" /> <br><br>
			You can find the zipped files here: <a href="/sdr/zipped/">Record Folder</a> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Delete all recordings" name="rm_logger_record_folder" />
			<input type="submit" class="w3-btn w3-brown" value="Delete all zipped files" name="rm_logger_zip_folder" /><br><br>

		</form>
		<br>
	</div>
</div>


<!-- Enter text here-->

	<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			
			
			if (isset($_POST["zip_camera"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged -v /var/www/html/picam/:/tmp/ git zip -r /tmp/zipped/".$_POST["zip_name"]." /tmp/record/ 2>&1", $ret);
				echo '</pre>';
			}
			if (isset($_POST["rm_cam_zip_folder"])){
				echo '<pre>';
				$test = system("rm -rf /var/www/html/picam/zipped/* 2>&1", $ret);
				echo '</pre>';
			}
			if (isset($_POST["rm_cam_record_folder"])){
				echo '<pre>';
				$test = system("rm -rf /var/www/html/picam/record/* 2>&1", $ret);
				echo '</pre>';
			}
			if (isset($_POST["zip_logger"])){
				echo '<pre>';
				$test = system("sudo docker run -t --rm --privileged -v /var/www/html/sdr/:/tmp/ git zip -r /tmp/zipped/".$_POST["zip_name"]." /tmp/record/ 2>&1", $ret);
				echo '</pre>';
			}
			if (isset($_POST["rm_logger_zip_folder"])){
				echo '<pre>';
				$test = system("rm -rf /var/www/html/sdr/zipped/* 2>&1", $ret);
				echo '</pre>';
			}
			if (isset($_POST["rm_logger_record_folder"])){
				echo '<pre>';
				$test = system("rm -rf /var/www/html/sdr/record/* 2>&1", $ret);
				echo '</pre>';
			}

	?>


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