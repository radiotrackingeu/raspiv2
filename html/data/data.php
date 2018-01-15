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
	<button class="w3-bar-item w3-button w3-mobile" onclick="openCity('mysql')">Database</button>
</div>

<div id="camera_data" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br>
		<h3>Zip Camera's record folder</h3><br>
		<form method='POST' enctype="multipart/form-data">
			<input type="text" name="zip__camera_name" value="<?php echo "Camera_".date('Y_m_d_H_i')?>">
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
			<input type="text" name="zip_logger_name" value="<?php echo "Logger_".date('Y_m_d_H_i')?>">
			<input type="submit" class="w3-btn w3-brown" value="Zip All Logger Recordings" name="zip_logger" /> <br><br>
			You can find the zipped files here: <a href="/sdr/zipped/">Record Folder</a> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Delete all recordings" name="rm_logger_record_folder" />
			<input type="submit" class="w3-btn w3-brown" value="Delete all zipped files" name="rm_logger_zip_folder" /><br><br>

		</form>
		<br>
	</div>
</div>

<div id="mysql" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round">
		<br>

		<h3>Manage Data in Databases</h3><br>
		<form method='POST' enctype="multipart/form-data">
			<input type="submit" class="w3-btn w3-brown" value="Start Database" name="start_mysql" /> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Start ManagementTool" name="start_phpmyadmin" /> <br><br>
			<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+8000)."/phpmyadmin/"?>"> Link to Device PphMyAdmin </a>
		</form>
		Start Database upon start
		Stop starting DB upon start
		
		<br>
	</div>
</div>


<!-- Enter text here-->


<?php
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
		$test = system("sudo docker run -t --rm --privileged -v /var/www/html/sdr/:/tmp/ git zip -r /tmp/zipped/".$_POST["zip_logger_name"]." /tmp/record/ 2>&1", $ret);
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
	if (isset($_POST["start_mysql"])){
		echo '<pre>';
		$test = system("sudo docker run -t --rm -e MYSQL_ROOT_PASSWORD=rteuv2! -p 3306:3306 -v /var/www/html/data/mysql:/var/lib/mysql mysql 2>&1", $ret);
		echo '</pre>';
	}
	if (isset($_POST["start_phpmyadmin"])){
		echo '<pre>';
		$test = system("sudo docker run -t --rm --net=host -v /var/www/html/data/:/cfiles/ phpmyadmin 2>&1", $ret);
		echo '</pre>';
	}
?>



<?php
	//load footer
	require_once RESOURCES_PATH.'/footer.php';
	//load javascripts
	require_once RESOURCES_PATH.'/javascript.php';
	//load php_scripts
	require_once RESOURCES_PATH.'/php_scripts.php';
 ?>

 
 
</body>
</html>