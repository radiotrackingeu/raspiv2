<!DOCTYPE html>
<?php
	//load config
	require_once '../cfg/baseConfig.php';
	//load top menu
	require_once RESOURCES_PATH.'/header.php';
	//load functions
	require_once RESOURCES_PATH.'/helpers.php';
		//load ConfigLite
	require_once CONFIGLITE_PATH.'/Lite.php';
	
		define ('confSection', 'database');
		define ('confKeys', array('db_host', 'db_port', 'db_user', 'db_pass','db_keep'));
		$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
?>

<!-- Enter text here-->

<div class="w3-bar w3-brown w3-mobile">
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'camera_data')">Camera</button>
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'radio_data')">Radio</button>
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'mysql')">Database</button>
</div>

<div id="camera_data" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round w3-padding">
		<h3>Zip Camera's record folder</h3><br>
		<form method='POST' enctype="multipart/form-data">
			<input type="text" name="zip__camera_name" value="<?php echo "Camera_".date('Y_m_d_H_i')?>">
			<input type="submit" class="w3-btn w3-brown" value="Zip All Camera Recordings" name="zip_camera" /> <br><br>
			You can find the zipped files here: <a href="/picam/zipped/">Record Folder</a> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Delete all recordings" name="rm_cam_record_folder" />
			<input type="submit" class="w3-btn w3-brown" value="Delete all zipped files" name="rm_cam_zip_folder" /><br><br>
		</form>
	</div>
</div>

<div id="radio_data" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round w3-padding">

		<h3>Zip Logger's record folder</h3><br>
		<form method='POST' enctype="multipart/form-data">
			<input type="text" name="zip_logger_name" value="<?php echo "Logger_".date('Y_m_d_H_i')?>">
			<input type="submit" class="w3-btn w3-brown" value="Zip All Logger Recordings" name="zip_logger" /> <br><br>
			You can find the zipped files here: <a href="/sdr/zipped/">Record Folder</a> <br><br>
			<input type="submit" class="w3-btn w3-brown" value="Delete all recordings" name="rm_logger_record_folder" />
			<input type="submit" class="w3-btn w3-brown" value="Delete all zipped files" name="rm_logger_zip_folder" /><br><br>

		</form>
	</div>
</div>

<div id="mysql" class="w3-container city" style="display:none">
	<div class="w3-panel w3-green w3-round w3-padding">
		<h3>Control Local Database <span class="w3-tooltip" style="display:inline-block; margin-left:20px">
            <?php if(filter_var(shell_exec("sudo docker inspect -f {{.State.Running}} mysql"),FILTER_VALIDATE_BOOLEAN)) : ?>
					<span class="w3-text w3-small w3-round w3-brown w3-tag"style="position:absolute; bottom:100%; left:50%; margin-left:-80px; width:160px">MySQL database is running.</span>
                    <i class="fas fa-check-circle"></i>
            <?php else : ?>
                    <span class="w3-text w3-small w3-round w3-brown w3-tag" style="position:absolute; bottom:100%; left:50%; margin-left:-100px; width:200px">MySQL database is NOT running.</span>
                    <i class="fas fa-times-circle"></i>
            <?php endif; ?>
                    </span></h3>
        <p>Once started the database will keep running and restarting (i.e. after reboot) until stopped through the button below.</p>
		<form method='POST' enctype="multipart/form-data">
			<input type="submit" class="w3-btn w3-brown" style="width:15%;" value="Start Database" name="start_mysql" />
			<input type="submit" class="w3-btn w3-brown" style="width:15%;" value="Stop Database" name="stop_mysql" /><br><br>
			<input type="submit" class="w3-btn w3-brown" style="width:15%;" value="Start Management Tool" name="start_phpmyadmin" />
			<input type="submit" class="w3-btn w3-brown" style="width:15%;" value="Stop Management Tool" name="stop_phpmyadmin" /><br><br>
			<a target="_blank" href="http://<?php echo $_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+8000)."/phpmyadmin/"?>"> Link to PhpMyAdmin </a>
		</form>
    </div>
    
	<div class="w3-panel w3-green w3-round w3-padding">
        <h3>Database Cleanup</h3>
        <p>Allows you to delete all, or all but the last <i>n</i> signals from the database.</p>
		<form method='POST' enctype="multipart/form-data" action=<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>>
            <label for="db_keep">Number of signals to keep:</label>
            <input type="number" class="w3-input" style="width:20%" id="db_keep" name="db_keep" value="<?php echo isset($config[confSection]['db_keep']) ? $config[confSection]['db_keep'] : 0 ?>"><br>
            <input type="submit" class="w3-btn w3-brown" style="width:15%;" value="Delete data" name="empty_DB" />
		</form>
    </div>
    
	<div class="w3-panel w3-green w3-round w3-padding">
		<h3>Configure Database Connection</h3>
		<form method='POST' enctype="multipart/form-data" action="<?php update_Config($config); echo $_SERVER['PHP_SELF']; ?>">
				<p>
					<label for="db_host">Hostname / IP:</label>
					<input class="w3-input w3-mobile" style="width:20%;" type="text" id="db_host" name="db_host" value="<?php echo isset($config[confSection]['db_host']) ? $config[confSection]['db_host'] : "127.0.0.1" ?>">
				</p>
				<p>
					<label for="db_port">Port:</label>
					<input class="w3-input w3-mobile" style="width:20%;" type="text" id="db_port" name="db_port" value="<?php echo isset($config[confSection]['db_port']) ? $config[confSection]['db_port'] : "3306" ?>">
				</p>
				<p>			
					User:
					<input class="w3-input w3-mobile" style="width:20%;" type="text" name="db_user" value="<?php echo isset($config[confSection]['db_user']) ? $config[confSection]['db_user'] : "root" ?>">
				</p>
				<p>		
					Password:
					<input class="w3-input w3-mobile" style="width:20%;" type="password" name="db_pass" value="<?php echo isset($config[confSection]['db_pass']) ? $config[confSection]['db_pass'] : "" ?>">
				</p>
				<input class="w3-input w3-btn w3-mobile w3-brown" style="width:15%;" type="submit" class="w3-btn w3-brown" value="Change settings" name="change_db_settings"><br>				
		</form>
	</div>
</div>


<!-- Enter text here-->

<?php
	//load footer
	require_once RESOURCES_PATH.'/footer.php';
	//load javascripts
	require_once RESOURCES_PATH.'/javascript.php';
	//load php_scripts
	require_once RESOURCES_PATH.'/php_scripts.php';
 ?>