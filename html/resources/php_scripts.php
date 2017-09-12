
<div id="output_php" class="w3-modal">
	<div class="w3-modal-content" style="width: 90%">
		<div class="w3-container w3-blue">
			<span onclick="document.getElementById('output_php').style.display='none'" class="w3-button w3-display-topright">&times;</span>
					

<?php
	//Data Management Functions
	if (isset($_POST["zip_camera"])){
		$cmd="sudo docker run -t --rm --privileged -v /var/www/html/picam/:/tmp/ git zip -r /tmp/zipped/".$_POST["zip__camera_name"]." /tmp/record/ 2>&1";
		start_docker($cmd,'camera_data');
	}
	//System - Software Functions
	if (isset($_POST["update_rep"])){
		$cmd='sudo docker run --rm -t -v /home/pi/gitrep/:/home/pi/gitrep/ -v /var/www/html/:/var/www/html/ --net="host" git sh /home/pi/gitrep/raspiv2/Docker/gitlab/update_html.sh ' .$_POST["git_checkout"]. ' 2>&1';
		start_docker($cmd,'GIT');
	}
	if (isset($_POST["running_containers"])){
		$cmd='sudo docker ps';
		start_docker($cmd,'running_docker');
	}
	//WebRadio Functions
	if (isset($_POST["rtl_fm_start_s"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " . $_POST["Signle_Freq"]. " -g " . $_POST["Radio_Gain"]. " -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'single_freq');
	}
	if (isset($_POST["rtl_fm_start_l"])){
		$cmd = "sudo docker run --rm -t --privileged rtlsdr sh -c 'rtl_fm -M usb -f " . $_POST["Signle_Freq"]. " -g " . $_POST["Radio_Gain"]. " -d 0 | play -r 32k -t raw -v 5 -e s -b 16 -c 1 -V1 -'";
		start_docker_quite($cmd,'single_freq');
	}
	if (isset($_POST["rtl_fm_start_f1"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " .$config['SDR_Radio']['Freq1']. " -g ".$config['SDR_Radio']['Radio_Gain']." -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'multiple_freq');
	}
	if (isset($_POST["rtl_fm_start_f2"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " .$config['SDR_Radio']['Freq2']. " -g ".$config['SDR_Radio']['Radio_Gain']." -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'multiple_freq');
	}
	if (isset($_POST["rtl_fm_start_f3"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " .$config['SDR_Radio']['Freq3']. " -g ".$config['SDR_Radio']['Radio_Gain']." -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'multiple_freq');
	}
	if (isset($_POST["rtl_fm_start_f4"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " .$config['SDR_Radio']['Freq4']. " -g ".$config['SDR_Radio']['Radio_Gain']." -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'multiple_freq');
	}
	if (isset($_POST["rtl_fm_start_f5"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " .$config['SDR_Radio']['Freq5']. " -g ".$config['SDR_Radio']['Radio_Gain']." -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'multiple_freq');
	}
	if (isset($_POST["rtl_fm_start_f6"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " .$config['SDR_Radio']['Freq6']. " -g ".$config['SDR_Radio']['Radio_Gain']." -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'multiple_freq');
	}
	if (isset($_POST["rtl_stop"])){
		$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=rtlsdr) 2>&1";
		start_docker($cmd,'single_freq');
	}
	//Logger Functions
	if (isset($_POST["log_start"])){
		check_docker("logger-sdr-d1");
		$cmd = "sudo docker run --rm --name logger-sdr-d1 -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash -c 'rtl_433 -f ".$_POST["center_freq"]." -s ".$_POST["freq_range"]." -t -q -A -l ".$_POST["log_level"]." -g " . $_POST["log_gain"]. " 2> /home/" . $_POST["log_name"]."'";
		start_docker_quite($cmd,'tab_logger');
	}
	if (isset($_POST["log_stop"])){
		$cmd="sudo docker stop $(sudo docker ps -a -q --filter name=logger-sdr-d1) 2>&1";
		start_docker($cmd, 'tab_logger');
	}
	//Logger Cornjob Functions
	if (isset($_POST["change_logger_cron"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash -c 'rtl_433 -f ".$_POST["time_center_freq"]." -s ".$_POST["time_freq_range"]." -t -q -A -l ".$_POST["time_log_level"]." -g " . $_POST["time_log_gain"]. " 2> /home/".$_POST["time_pre_log_name"]."\\$(date +%Y_%m_%k_%M_%S)'";
		echo $cmd;
		if($_POST["time_start_timer"]=="reboot"){
			$change= "@reboot root " .$cmd;
			$search = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash";
			$file_to_replace="/tmp/crontab";
			echo "System will now start logger upon start with the following settings: \n \n Frequency: ".$_POST["time_center_freq"]."\n Frequency-Range: ".$_POST["time_freq_range"]."\n Log-Level: ".$_POST["time_log_level"]."\n Gain: " . $_POST["time_log_gain"]. "\n File-Name: ". $_POST["time_pre_log_name"];
			start_docker("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"", 'tab_logger_timer');
		}
		if($_POST["time_start_timer"]=="start_no"){
			$change= "#@reboot root " .$cmd;
			$search = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash";
			$file_to_replace="/tmp/crontab";
			echo "System will not start logger upon start";
			start_docker("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"", 'tab_logger_timer');
		}
		if($_POST["time_start_timer"]=="start_on_time"){
			$change= $_POST["time_start_min"]. " ".$_POST["time_start_hour"]." * * * root " .$cmd;
			$search = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash";
			$file_to_replace="/tmp/crontab";
			echo "System will now start logger upon start with the following settings: <br><br>Frequency: ".$_POST["time_center_freq"]." Frequency-Range: ".$_POST["time_freq_range"]." Log-Level: ".$_POST["time_log_level"]." Gain: " . $_POST["time_log_gain"]. " and File-Name: ". $_POST["time_pre_log_name"];
			start_docker("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"", 'tab_logger_timer');			
		}
		$stop_cmd="sudo docker stop \\$(sudo docker ps -a -q --filter ancestor=rtl_433_mod)";
		if($_POST["time_stop_timer"]=="stop_no"){
			$change= "#".$stop_cmd;
			$search = $stop_cmd;
			$file_to_replace="/tmp/crontab";
			echo "System will not stop logger";
			start_docker("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"",'tab_logger_timer');
		}
		if($_POST["time_stop_timer"]=="stop_on_time"){
			$change= $_POST["time_stop_min"]. " ".$_POST["time_stop_hour"]." * * * root " .$stop_cmd;
			$search = $stop_cmd;
			$file_to_replace="/tmp/crontab";
			echo "System will now stop logger at specific time";
			start_docker("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"",'tab_logger_timer');
		}
	}	
	//Remote Connections
	if (isset($_POST["stop_vpn"])){
		$cmd = "sudo docker stop $(sudo docker ps -a -q --filter name=vpn_tunnel) 2>&1"; 
		$result = system($cmd);
	}
	if (isset($_POST["start_vpn"])){ 
		$cmd = "sudo docker run --rm --name vpn_tunnel -v /var/www/html/connect/:/config/ --privileged --net=host -t umts openvpn /config/client.conf 2>&1";
		$result = system($cmd);
	}
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
		$result = system($cmd);
		echo "Config has been removed";
	}
	if (isset($_POST["change_wvdial"])){ 
		$cmd1 = "cp /var/www/html/connect/edeka.conf /var/www/html/connect/wvdial.conf";
		$cmd2 = "sed -i 's/Init3 = AT+CGDCONT=1,\"IP\",.*$/Init3 = AT+CGDCONT=1,\"IP\",".$_POST["apn"]."/' /var/www/html/connect/wvdial.conf"; 
		$cmd3 = "sed -i 's/Phone = .*$/Phone = ".$_POST["dial"]."/' /var/www/html/connect/wvdial.conf"; 
		$result = system($cmd1);
		$result = system($cmd2);
		$result = system($cmd3);
	}
	if (isset($_POST["switch_mode"])){ 
		$cmd = "sudo docker run --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 1f01 -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";
		$result = system($cmd);
	}
	if (isset($_POST["start_umts_vpn"])){ 
		$cmd1 = "sudo docker run --rm --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 1f01 -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";
		$cmd2 = "sudo docker run --rm --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 15ca -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";
		$cmd3 = "sudo docker run --rm -v /var/www/html/connect/:/config/ --privileged --net=host -t umts sh /config/start_umts.sh 2>&1";
		$result1 = system($cmd1);
		$result2 = system($cmd2);
		sleep(2);
		$result3 = system($cmd3);
	}
	if (isset($_POST["stop_umts_vpn"])){
		$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=umts) 2>&1"; 
		$result = system($cmd);
	}
	if (isset($_POST["stop_umts"])){
		$cmd = "sudo docker stop $(sudo docker ps -a -q --filter ancestor=umts) 2>&1"; 
		$result = system($cmd);
	}
	if (isset($_POST["start_umts"])){
		$cmd1 = "sudo docker run --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 1f01 -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";
		$cmd2 = "sudo docker run --privileged --net=host -t -v /lib/modules/4.4.38-v7+/:/lib/modules/4.4.38-v7+/ umts usb_modeswitch -v 12d1 -p 15ca -M '55534243123456780000000000000011062000000100000000000000000000' && sudo modprobe option";				
		$cmd3 = "sudo docker run --privileged --net=host -t umts wvdial 2>&1";
		$result = system($cmd1);
		$result = system($cmd2);
		sleep(2);
		$result = system($cmd3);
	}
	
	//Connect Cornjob Functions
	if (isset($_POST["change_vpn_cron"])){
		$cmd = "sudo docker run --rm --name vpn_tunnel -v /var/www/html/connect/:/config/ --privileged --net=host -t umts openvpn /config/client.conf";
		$search = "sudo docker run --rm --name vpn_tunnel";
		if($_POST["time_start_vpn"]=="reboot"){
			$change= "@reboot root " .$cmd;
			$file_to_replace="/tmp/crontab";
			echo "System will now start logger upon start with the following settings: \n \n Frequency: ".$_POST["time_center_freq"]."\n Frequency-Range: ".$_POST["time_freq_range"]."\n Log-Level: ".$_POST["time_log_level"]."\n Gain: " . $_POST["time_log_gain"]. "\n File-Name: ". $_POST["time_pre_log_name"];
			start_docker("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"", 'tab_logger_timer');
		}
		if($_POST["time_start_vpn"]=="start_no"){
			$change= "#@reboot root " .$cmd;
			$file_to_replace="/tmp/crontab";
			echo "System will not start logger upon start";
			start_docker("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"", 'tab_logger_timer');
		}
	}	
	
	//Raw Data Recorder Functions
	if (isset($_POST["sdr_start"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/tmp/ rtlsdr bash -c 'rtl_sdr -f ".$_POST["raw_center_freq"]." -s ".$_POST["raw_freq_range"]." -g ".$_POST["raw_log_log_gain"]." /tmp/".$_POST["raw_pre_log_name"].$_POST["log_name"]."'";
		echo $cmd;
		start_docker_quite($cmd,'raw_data');
	}
	
	
	//General Functions
	function start_docker($docker_cmd,$block_to_jump){
		echo "<script type='text/javascript'>document.getElementById('output_php').style.display='block';</script>";
		echo '<pre>';
		system($docker_cmd, $ret);
		echo '</pre>';
		echo "<script type='text/javascript'>document.getElementById('".$block_to_jump."').style.display = 'block';</script>";
	}
	function start_docker_quite($docker_cmd,$block_to_jump){
		system($docker_cmd." >/dev/null 2>/dev/null &");
		echo "<script type='text/javascript'>document.getElementById('".$block_to_jump."').style.display = 'block';</script>";	
	}
	function check_docker($docker_name){
		if(shell_exec("sudo docker inspect -f {{.State.Running}} $(sudo docker ps -a -q --filter name=".$docker_name.")")){
			echo "<span class='w3-tag w3-red w3-xlarge'>Device is in use</span> \n \n";
		}
		else{
			echo "Device is not in use";
		}
	}
?>

		</div>
	</div>
</div>