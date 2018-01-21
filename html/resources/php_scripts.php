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
		$cmd='sudo docker run --rm -t -v /home/pi/gitrep/:/home/pi/gitrep/ -v /var/www/html/:/var/www/html/ --net="host" git sh /home/pi/gitrep/raspiv2/Docker/gitlab/update_html.sh '.$_POST["git_checkout"].' '.$_POST["git_keepcfg"].' 2>&1';
		start_docker($cmd,'GIT');
	}
	if (isset($_POST["running_containers"])){
		$cmd='sudo docker ps';
		start_docker($cmd,'running_docker');
	}
	//WebRadio Functions
	if (isset($_POST["rtl_fm_start_s"])){
		$cmd = "sudo docker run --rm -t --name webradio --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " . $_POST["Single_Freq"]. " -g " . $_POST["Radio_Gain"]. " -d ".$config['SDR_Radio']['device']." | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'single_freq');
	}
	if (isset($_POST["rtl_fm_start_l"])){
		$cmd = "sudo docker run --rm -t --name webradio -d ".$config['SDR_Radio']['device']." --privileged rtlsdr sh -c 'rtl_fm -M usb -f " . $_POST["Single_Freq"]. " -g " . $_POST["Radio_Gain"]. " -d 0 | play -r 32k -t raw -v 5 -e s -b 16 -c 1 -V1 -'";
		start_docker_quite($cmd,'single_freq');
	}
	if (isset($_POST["rtl_fm_start_f1"])){
		$cmd = "sudo docker run --rm -t --name webradio --device=/dev/bus/usb -d ".$config['SDR_Radio']['device']." -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " .$config['SDR_Radio']['Freq1'][$config['SDR_Radio']['device']]. " -g ".$config['SDR_Radio']['Radio_Gain'][$config['SDR_Radio']['device']]." -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'multiple_freq');
	}
	if (isset($_POST["rtl_fm_start_f2"])){
		$cmd = "sudo docker run --rm -t --name webradio --device=/dev/bus/usb -d ".$config['SDR_Radio']['device']." -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " .$config['SDR_Radio']['Freq2'][$config['SDR_Radio']['device']]. " -g ".$config['SDR_Radio']['Radio_Gain'][$config['SDR_Radio']['device']]." -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'multiple_freq');
	}
	if (isset($_POST["rtl_fm_start_f3"])){
		$cmd = "sudo docker run --rm -t --name webradio --device=/dev/bus/usb -d ".$config['SDR_Radio']['device']." -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " .$config['SDR_Radio']['Freq3'][$config['SDR_Radio']['device']]. " -g ".$config['SDR_Radio']['Radio_Gain'][$config['SDR_Radio']['device']]." -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'multiple_freq');
	}
	if (isset($_POST["rtl_fm_start_f4"])){
		$cmd = "sudo docker run --rm -t --name webradio --device=/dev/bus/usb -d ".$config['SDR_Radio']['device']." -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " .$config['SDR_Radio']['Freq4'][$config['SDR_Radio']['device']]. " -g ".$config['SDR_Radio']['Radio_Gain'][$config['SDR_Radio']['device']]." -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'multiple_freq');
	}
	if (isset($_POST["rtl_fm_start_f5"])){
		$cmd = "sudo docker run --rm -t --name webradio --device=/dev/bus/usb -d ".$config['SDR_Radio']['device']." -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " .$config['SDR_Radio']['Freq5'][$config['SDR_Radio']['device']]. " -g ".$config['SDR_Radio']['Radio_Gain'][$config['SDR_Radio']['device']]." -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'multiple_freq');
	}
	if (isset($_POST["rtl_fm_start_f6"])){
		$cmd = "sudo docker run --rm -t --name webradio --device=/dev/bus/usb -d ".$config['SDR_Radio']['device']." -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr sh -c 'rtl_fm -M usb -f " .$config['SDR_Radio']['Freq6'][$config['SDR_Radio']['device']]. " -g ".$config['SDR_Radio']['Radio_Gain'][$config['SDR_Radio']['device']]." -d 0 | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240'";
		start_docker_quite($cmd,'multiple_freq');
	}
	if (isset($_POST["rtl_stop"])){
		$cmd = "sudo docker stop $(sudo docker ps -a -q --filter name=webradio) 2>&1";
		start_docker($cmd,'single_freq');
	}
	if (isset($_POST["change_device_single_freq"])){
		echo "<script type='text/javascript'>document.getElementById('single_freq').style.display = 'block';</script>";	
	}
	if (isset($_POST["change_device_multiple_freq"])){
		echo "<script type='text/javascript'>document.getElementById('multiple_freq').style.display = 'block';</script>";	
	}
	if (isset($_POST["change_device_freq_settings"])){
		echo "<script type='text/javascript'>document.getElementById('freq_settings').style.display = 'block';</script>";	
	}
	if (isset($_POST["change_device_single_freq_recs"])){
		echo "<script type='text/javascript'>document.getElementById('single_freq_recs').style.display = 'block';</script>";	
	}	
	if (isset($_POST["change_device_multiple_freq_rec"])){
		echo "<script type='text/javascript'>document.getElementById('multiple_freq_rec').style.display = 'block';</script>";	
	}
	if (isset($_POST["change_device_tab_logger"])){
		echo "<script type='text/javascript'>document.getElementById('tab_logger').style.display = 'block';</script>";	
	}
	if (isset($_POST["change_device_tab_logger_timer"])){
		echo "<script type='text/javascript'>document.getElementById('tab_logger_timer').style.display = 'block';</script>";	
	}
	
	//Logger Range Functions
	 if (isset($_POST["log_start_0"])){
		$file_name = $config['logger']['antenna_id_0'] . date('Y_m_d_H_i');
		$file_path = "/tmp/record/" . $file_name;
		$run_id = write_run_to_db($config, 0, $file_name);
		$cmd = cmd_docker(0)." '".cmd_rtl_sdr($config, 0)." 2> ".$file_path." | ".cmd_liquidsdr($config, 0).cmd_sql($config, 0, $run_id)." >> ". $file_path." 2>&1'";
		start_docker_echo($cmd,'tab_logger_range','Started Receiver 1.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
	}
	if (isset($_POST["log_stop_0"])){
		$cmd="sudo docker stop $(sudo docker ps -a -q --filter name=logger-sdr-d0) 2>&1";
		start_docker($cmd, 'tab_logger_range');
	}
	
	if (isset($_POST["log_start_1"])){
		$file_name = $config['logger']['antenna_id_1'] . date('Y_m_d_H_i');
		$file_path = "/tmp/record/" . $file_name;
		$run_id = write_run_to_db($config, 1, $file_name);
		$cmd = cmd_docker(1)." '".cmd_rtl_sdr($config, 1)." 2> ".$file_path." | ".cmd_liquidsdr($config, 1).cmd_sql($config, 1, $run_id)." >> ". $file_path." 2>&1'";
		start_docker_echo($cmd,'tab_logger_range','Started Receiver 2.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
	}
	 
	if (isset($_POST["log_stop_1"])){
		$cmd="sudo docker stop $(sudo docker ps -a -q --filter name=logger-sdr-d1) 2>&1";
		start_docker($cmd, 'tab_logger_range');
	}
	
	//Logger Single Frequency Functions
	 if (isset($_POST["log_single_start_0"])){
		$file_name = $config['logger']['antenna_id_0'] . date('Y_m_d_H_i');
		$file_path = "/tmp/record/" . $file_name;
		$run_id = write_run_to_db($config, 0, $file_name);
		$cmd = cmd_docker(0)." '".cmd_rtl_sdr($config, 0)." 2> ".$file_path." | ".cmd_matched_filters($config, 0).cmd_sql($config, 0, $run_id)." >> ". $file_path." 2>&1'";
		start_docker_echo($cmd,'tab_logger_single','Started Receiver 1.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
	}
	if (isset($_POST["log_single_stop_0"])){
		$cmd="sudo docker stop $(sudo docker ps -a -q --filter name=logger-sdr-d0) 2>&1";
		start_docker($cmd, 'tab_logger_single');
	}
	
	if (isset($_POST["log_single_start_1"])){
		$file_name = $config['logger']['antenna_id_1'] . date('Y_m_d_H_i');
		$file_path = "/tmp/record/" . $file_name;
		$run_id = write_run_to_db($config, 1, $file_name);
		$cmd = cmd_docker(1)." '".cmd_rtl_sdr($config, 1)." 2> ".$file_path." | ".cmd_matched_filters($config, 1).cmd_sql($config, 1, $run_id)." >> ". $file_path." 2>&1'";
		start_docker_echo($cmd,'tab_logger_single','Started Receiver 2.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
	}
	 
	if (isset($_POST["log_single_stop_1"])){
		$cmd="sudo docker stop $(sudo docker ps -a -q --filter name=logger-sdr-d1) 2>&1";
		start_docker($cmd, 'tab_logger_single');
	}
	
	
	//General Looger Function
	function cmd_docker($dev) {
		return "sudo docker run --rm --name=logger-sdr-d".$dev." --net=host -t --device=/dev/bus/usb -v /var/www/html/sdr/:/tmp/ liquidsdr bash -c";
	}
	
	function cmd_sql($config, $dev, $run_id) {
		if (!is_int($run_id)) {
			echo $run_id;  // on error write_run_to_db returns error message rather than run id.
			return "";
		}
		return " --sql --db_host ".$config['database']['db_host']." --db_port ".$config['database']['db_port']." --db_user ".$config['database']['db_user']." --db_pass ".$config['database']['db_pass']." --db_run_id ".$run_id;
	}
	
	function cmd_rtl_sdr($config, $dev) {
		return "rtl_sdr -d ".$dev." -f ".$config['logger']['center_freq_'.$dev]." -s ".$config['logger']['freq_range_'.$dev]." -g ".$config['logger']['log_gain_'.$dev]." -";
	}
	function cmd_liquidsdr($config, $dev) {
		return "/tmp/liquidsdr/rtlsdr_signal_detect -s -t ".$config['logger']['threshold_'.$dev]." -r ".$config['logger']['freq_range_'.$dev]." -b ".$config['logger']['nfft_'.$dev]." -n ".$config['logger']['timestep_'.$dev];
	}
	function cmd_matched_filters($config, $dev) {
		return "/tmp/liquidsdr/matched_signal_detect -s -t ".$config['logger']['threshold_'.$dev]." -r ".$config['logger']['freq_range_'.$dev]." -b ".$config['logger']['nfft_'.$dev]." -n ".$config['logger']['timestep_'.$dev];
	}
	
	//Logger Settings Functions
	if (isset($_POST["change_logger_settings_0"])){
		$file_name = $config['logger']['antenna_id_0'] . '\$(date +%Y_%m_%k_%M_%S)';
		$file_path = "/tmp/record/" . $file_name;
		$run_id = write_run_to_db($config, 0, $file_name);
		///home/".$_POST["time_pre_log_name"]."\$(date +%Y_%m_%k_%M_%S)'
		if($_POST["timer_mode_0"]=="single_freq"){
			$cmd = cmd_docker(0)." '".cmd_rtl_sdr($config, 0)." 2> ".$file_path." | ".cmd_matched_filters($config, 0).cmd_sql($config, 0, $run_id)." >> ". $file_path." 2>&1'";
			echo $cmd;
		}
		if($_POST["timer_mode_0"]=="freq_range"){
			$cmd = cmd_docker(0)." '".cmd_rtl_sdr($config, 0)." 2> ".$file_path." | ".cmd_matched_filters($config, 0).cmd_sql($config, 0, $run_id)." >> ". $file_path." 2>&1'";
			echo $cmd;
		}
		if($_POST["timer_start_0"]=="reboot"){
			$change= "@reboot root " .$cmd;
			$search = "logger-sdr-d0";
			$file_to_replace="/tmp/crontab";
			$cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"", 'tab_logger_timer'
			start_docker_echo($cmd_change,"Logger will now start upon boot with the given settings");
		}
		if($_POST["timer_start_0"]=="start_no"){
			$change= "#logger-sdr-d0"
			$search = "logger-sdr-d0";
			$file_to_replace="/tmp/crontab";
			$cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"", 'tab_logger_timer'
			start_docker($cmd_change, "System will not start logger upon start");
		}
		if($_POST["timer_start_0"]=="start_on_time"){
			$change= $_POST["time_start_min"]. " ".$_POST["time_start_hour"]." * * * root " .$cmd;
			$search = "logger-sdr-d0";
			$file_to_replace="/tmp/crontab";
			echo "System will now start logger upon start with the following settings: <br><br>Frequency: ".$_POST["time_center_freq"]." Frequency-Range: ".$_POST["time_freq_range"]." Log-Level: ".$_POST["time_log_level"]." Gain: " . $_POST["time_log_gain"]. " and File-Name: ". $_POST["time_pre_log_name"];
			start_docker("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"", 'tab_logger_timer');			
		}
	}
	
	
	//Compile function 
	if (isset($_POST["compile"])){
		$gcc="gcc -g -O2  -ffast-math -mcpu=cortex-a7 -mfloat-abi=hard -mfpu=neon-vfpv4 -Wall -I/usr/include/mysql -fPIC  /tmp/rtlsdr_signal_detect.c /liquidsdr/liquid-dsp/libliquid.a -o /tmp/rtlsdr_signal_detect -lfftw3f -lm -lc -I/usr/include/mysql -L/usr/lib/arm-linux-gnueabihf -lmysqlclient -lpthread -lz -lm -ldl";
		$cmd = "sudo docker run -t --rm -v /var/www/html/sdr/liquidsdr/:/tmp/ liquidsdr ".$gcc;
		start_docker($cmd,'tab_logger_settings');
		$gcc_matched="gcc -g -O2  -ffast-math -mcpu=cortex-a7 -mfloat-abi=hard -mfpu=neon-vfpv4 -Wall -I/usr/include/mysql -fPIC  /tmp/matched_filter.c /liquidsdr/liquid-dsp/libliquid.a -o /tmp/matched_signal_detect -lfftw3f -lm -lc -I/usr/include/mysql -L/usr/lib/arm-linux-gnueabihf -lmysqlclient -lpthread -lz -lm -ldl";
		$cmd_machted = "sudo docker run -t --rm -v /var/www/html/sdr/liquidsdr/:/tmp/ liquidsdr ".$gcc_matched;
		start_docker($cmd_matched,'tab_logger_settings');
	}
	
	if (isset($_POST["compile_raspi_zero"])){
		$gcc="gcc -g -O2  -ffast-math -Wall -I/usr/include/mysql -fPIC  /tmp/rtlsdr_signal_detect.c /liquidsdr/liquid-dsp/libliquid.a -o /tmp/rtlsdr_signal_detect -lfftw3f -lm -lc -I/usr/include/mysql -L/usr/lib/arm-linux-gnueabihf -lmysqlclient -lpthread -lz -lm -ldl";
		$cmd = "sudo docker run -t --rm -v /var/www/html/sdr/liquidsdr/:/tmp/ liquidsdr ".$gcc;
		start_docker($cmd,'tab_logger_settings');
		$gcc="gcc -g -O2  -ffast-math -Wall -I/usr/include/mysql -fPIC  /tmp/matched_filter.c /liquidsdr/liquid-dsp/libliquid.a -o /tmp/matched_signal_detect -lfftw3f -lm -lc -I/usr/include/mysql -L/usr/lib/arm-linux-gnueabihf -lmysqlclient -lpthread -lz -lm -ldl";
		$cmd = "sudo docker run -t --rm -v /var/www/html/sdr/liquidsdr/:/tmp/ liquidsdr ".$gcc;
		start_docker($cmd,'tab_logger_settings');
	}

	//change_logger_settings_1
	
	//Logger Cronjob Functions
	if (isset($_POST["change_logger_cron"])){
		$cmd = "sudo docker run --rm --name logger-sdr-d1 -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash -c 'rtl_433 -f ".$_POST["time_center_freq"]." -s ".$_POST["time_freq_range"]." -t -q -A -l ".$_POST["time_log_level"]." -g " . $_POST["time_log_gain"]. " 2> /home/".$_POST["time_pre_log_name"]."\$(date +%Y_%m_%k_%M_%S)'";
		echo $cmd;
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
	if (isset($_POST["change_VPN_cron"])){
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
		$cmd = "sudo docker run --rm --name=raw-sdr-d1 -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/tmp/ rtlsdr bash -c 'rtl_sdr -f ".$_POST["raw_center_freq"]." -s ".$_POST["raw_freq_range"]." -g ".$_POST["raw_log_log_gain"]." /tmp/".$_POST["raw_pre_log_name"].$_POST["log_name"]."'";
		start_docker_quite($cmd,'tab_raw_data');
	}
	if (isset($_POST["sdr_stop"])){
		$cmd="sudo docker stop $(sudo docker ps -a -q --filter name=raw-sdr-d1) 2>&1";
		start_docker($cmd, 'tab_logger');
	}
	
	//Raw Data Analzer sudo docker run -it --rm -v /var/www/html/sdr/liquidsdr/:/tmp/ liquidsdr
	if (isset($_POST["start_analyze"])){
		$cmd = "sudo docker run -t --rm --name liquidsdr -v /var/www/html/sdr/:/tmp/ liquidsdr bash -c '/tmp/liquidsdr/rtlsdr_signal_detect -s > /tmp/test'";
		start_docker($cmd,'tab_raw_data_ana');
	}
	
	if (isset($_POST["stop_analyze"])){
		$cmd="sudo docker stop $(sudo docker ps -a -q --filter name=liquidsdr) 2>&1";
		start_docker($cmd, 'tab_logger');
	}
	
	//Check wether Receivers are running update_device_info
	if (isset($_POST["update_device_info"])){
		echo "<script type='text/javascript'>document.getElementById('device_info').style.display = 'block';</script>";
	}
	
	//WebRX
	if (isset($_POST["rtl_websdr_d0"])){
		$cmd = "sudo docker run --rm -t --name webrx-sdr-d0 --device=/dev/bus/usb -v /var/www/html/sdr/:/cfiles/ -p ".($_SERVER['SERVER_PORT']+1).":8073 webrx sh /cfiles/start_openwebrx.sh";
		start_docker_quite($cmd,'webrx_tab');
	}
	if (isset($_POST["rtl_websdr_stop_d0"])){
		$cmd = "sudo docker stop webrx-sdr-d0 2>&1";
		start_docker_echo($cmd,'webrx_tab','Spectrogram Server 0 stopped if it was running');
	}
	if (isset($_POST["change_config_websdr_d0"])){
		$cmd = "sh /var/www/html/sdr/change_config_webrx.sh ".$_POST["fft_fps_0"]." ".$_POST["fft_size_0"]." ".$_POST["samp_rate_0"]." ".$_POST["center_freq_0"]." ".$_POST["rf_gain_0"]." 2>&1";
		start_docker($cmd,'settings_webrx_tab');
	}
	if (isset($_POST["rtl_websdr_d1"])){
		$cmd = "sudo docker run --rm -t --name webrx-sdr-d1 --device=/dev/bus/usb -v /var/www/html/sdr/:/cfiles/ -p ".($_SERVER['SERVER_PORT']+2).":8073 webrx sh /cfiles/start_openwebrx_d1.sh";
		start_docker_quite($cmd,'webrx_tab');
	}
	if (isset($_POST["rtl_websdr_stop_d1"])){
		$cmd = "sudo docker stop webrx-sdr-d1 2>&1";
		start_docker_echo($cmd,'webrx_tab','Spectrogram Server 1 stopped if it was running');
	}
	if (isset($_POST["change_config_websdr_d1"])){
		$cmd = "sh /var/www/html/sdr/change_config_webrx_d1.sh ".$_POST["fft_fps_1"]." ".$_POST["fft_size_1"]." ".$_POST["samp_rate_1"]." ".$_POST["center_freq_1"]." ".$_POST["rf_gain_1"]." 2>&1";
		start_docker($cmd,'settings_webrx_tab');
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
	function start_docker_echo($docker_cmd,$block_to_jump,$statement){
		echo "<script type='text/javascript'>document.getElementById('output_php').style.display='block';</script>";
		system($docker_cmd." >/dev/null 2>/dev/null &");
		echo '<pre>';
		echo $statement;
		echo '</pre>';
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
	
	// SQL Functions
	
	function write_run_to_db($config, $device, $file_name) {
		if ($config['logger']['use_sql_'.$device] != "Yes")
			return -1;
		$con = mysqli_connect($config['database']['db_host'].":".$config['database']['db_port'], $config['database']['db_user'], $config['database']['db_pass']);
			if (mysqli_connect_errno()) {
				return "Connection to ".$config['database']['db_host'].":".$config['database']['db_port']." failed: " . mysqli_connect_error();	
			} else {
				$cmd_sql = "INSERT INTO rteu.runs (device,pos_x,pos_y,orientation,beam_width,gain,center_freq,freq_range,threshold,fft_bins,fft_samples)".
					" VALUE ('".$file_name."',".                                     $config['logger']['antenna_position_N_'.$device].",".
								$config['logger']['antenna_position_E_'.$device].",".$config['logger']['antenna_orientation_'.$device].",".
								$config['logger']['antenna_beam_width_'.$device].",".$config['logger']['log_gain_'.$device].",".
								$config['logger']['center_freq_'.$device].",".       $config['logger']['freq_range_'.$device].",".
								$config['logger']['threshold_'.$device].",".         $config['logger']['nfft_'.$device].",".
								$config['logger']['timestep_'.$device].");";
				if(!mysqli_query($con, $cmd_sql))
					return "Failed to write to db: ". mysqli_error($con);
				else
					return mysqli_insert_id($con);
			}
	}

?>

		</div>
	</div>
</div>

<script>
// Script to close the window upon click outside the box - special request of Philipp - otherwise he quits
// Get the modal
var modal = document.getElementById('output_php');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
