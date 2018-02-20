<div id="output_php" class="w3-modal">
	<div class="w3-modal-content" style="width: 90%">
		<div class="w3-container w3-blue">
			<span onclick="document.getElementById('output_php').style.display='none'" class="w3-button w3-display-topright">&times;</span>
					

<?php
	//System - Software Functions
	if (isset($_POST["update_rep"])){
		$cmd='sudo docker run --rm -t -v /home/pi/gitrep/:/home/pi/gitrep/ -v /var/www/html/:/var/www/html/ --net="host" git:1.0 sh /home/pi/gitrep/raspiv2/Docker/gitlab/update_html.sh '.$_POST["git_checkout"].' '.$_POST["git_keepcfg"].' 2>&1';
		start_docker($cmd,'GIT');
	}
	if (isset($_POST["running_containers"])){
		$cmd='sudo docker ps';
		start_docker($cmd,'running_docker');
	}
	//WebRadio Functions
	
	function cmd_webradio($config, $dev, $freq) {
		$cmd_docker = "sudo docker run --rm -t --name webradio_".$dev." --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1240 rtlsdr:1.0 sh -c";
		$cmd_rtl_fm = "rtl_fm -M usb -f ".$config['SDR_Radio']['Freq'.$freq.'_'.$dev]." -g ".$config['SDR_Radio']['Radio_Gain_'.$dev]." -d ".$dev." | sox -traw -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240";
		return $cmd_docker." '".$cmd_rtl_fm."'";
	}
	function webradio_stop($dev) {
		$cmd = "sudo docker stop webradio_".$dev;
		system($cmd." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_fm_start_0_f1"])){
		system(cmd_webradio($config,0,1)." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_fm_start_1_f1"])){
		system(cmd_webradio($config,1,1)." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_fm_start_0_f2"])){
		system(cmd_webradio($config,0,2)." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_fm_start_1_f2"])){
		system(cmd_webradio($config,1,2)." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_fm_start_0_f3"])){
		system(cmd_webradio($config,0,3)." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_fm_start_1_f3"])){
		system(cmd_webradio($config,1,3)." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_fm_start_0_f4"])){
		system(cmd_webradio($config,0,4)." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_fm_start_1_f4"])){
		system(cmd_webradio($config,1,4)." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_fm_start_0_f5"])){
		system(cmd_webradio($config,0,5)." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_fm_start_1_f5"])){
		system(cmd_webradio($config,1,5)." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_fm_start_0_f6"])){
		system(cmd_webradio($config,0,6)." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_fm_start_1_f6"])){
		system(cmd_webradio($config,1,6)." >/dev/null 2>/dev/null &");
	}
	if (isset($_POST["rtl_stop_0"])){
		webradio_stop(0);
	}
	if (isset($_POST["rtl_stop_1"])){
		webradio_stop(1);
	}
	
	//Logger Range Functions
	 if (isset($_POST["log_start_0"])){
		$file_name = $config['logger']['antenna_id_0'] ."_". date('Y_m_d_H_i');
		$file_path = "/tmp/record/" . $file_name;
		$run_id = write_run_to_db($config, 0, $file_name);
		$cmd = cmd_docker(0)." '".cmd_rtl_sdr($config, 0)." 2> ".$file_path." | ".cmd_liquidsdr($config, 0).cmd_sql($config, 0, $run_id)." >> ". $file_path." 2>&1'";
		start_docker_echo($cmd,'tab_logger_range','Started Receiver 0.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
	}
	if (isset($_POST["log_stop_0"])){
		$cmd="sudo docker stop $(sudo docker ps -a -q --filter name=logger-sdr-d0) 2>&1";
		start_docker($cmd, 'tab_logger_range');
	}
	
	if (isset($_POST["log_start_1"])){
		$file_name = $config['logger']['antenna_id_1'] ."_". date('Y_m_d_H_i');
		$file_path = "/tmp/record/" . $file_name;
		$run_id = write_run_to_db($config, 1, $file_name);
		$cmd = cmd_docker(1)." '".cmd_rtl_sdr($config, 1)." 2> ".$file_path." | ".cmd_liquidsdr($config, 1).cmd_sql($config, 1, $run_id)." >> ". $file_path." 2>&1'";
		start_docker_echo($cmd,'tab_logger_range','Started Receiver 1.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
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
		start_docker_echo($cmd,'tab_logger_single','Started Receiver 0.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
	}
	if (isset($_POST["log_single_stop_0"])){
		$cmd="sudo docker stop logger-sdr-d0 2>&1";
		start_docker($cmd, 'tab_logger_single');
	}
	
	if (isset($_POST["log_single_start_1"])){
		$file_name = $config['logger']['antenna_id_1'] . date('Y_m_d_H_i');
		$file_path = "/tmp/record/" . $file_name;
		$run_id = write_run_to_db($config, 1, $file_name);
		$cmd = cmd_docker(1)." '".cmd_rtl_sdr($config, 1)." 2> ".$file_path." | ".cmd_matched_filters($config, 1).cmd_sql($config, 1, $run_id)." >> ". $file_path." 2>&1'";
		start_docker_echo($cmd,'tab_logger_single','Started Receiver 1.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
	}
	 
	if (isset($_POST["log_single_stop_1"])){
		$cmd="sudo docker stop logger-sdr-d1 2>&1";
		start_docker($cmd, 'tab_logger_single');
	}
	
	
	//General Looger Function
	function cmd_docker($dev) {
		return "sudo docker run --rm --name=logger-sdr-d".$dev." --net=host -t --device=/dev/bus/usb -v /var/www/html/sdr/:/tmp/ liquidsdr:1.0 bash -c";
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
		return "/tmp/liquidsdr/matched_signal_detect -s -t ".$config['logger']['threshold_'.$dev]." -r ".$config['logger']['freq_range_'.$dev]." -p 22";
	}
	
	//Logger Settings Functions
	if (isset($_POST["change_logger_settings_0"])){
		$file_name = $config['logger']['antenna_id_0'] ."_". '\$(date +%Y_%m_%k_%M_%S)';
		$file_path = "/tmp/record/" . $file_name;
		if($_POST["timer_start_0"]=="start_boot"||$_POST["timer_start_0"]=="start_time"){
			$run_id = write_run_to_db($config, 0, $config['logger']['antenna_id_0']."_".$config['logger']['timer_start_time_0']);
		}
		///home/".$_POST["time_pre_log_name"]."\$(date +%Y_%m_%k_%M_%S)'
		if($_POST["timer_mode_0"]=="single_freq"){
			$cmd = cmd_docker(0)." '".cmd_rtl_sdr($config, 0)." 2> ".$file_path." | ".cmd_matched_filters($config, 0).cmd_sql($config, 0, $run_id)." >> ". $file_path." 2>&1'";
		}
		if($_POST["timer_mode_0"]=="freq_range"){
			$cmd = cmd_docker(0)." '".cmd_rtl_sdr($config, 0)." 2> ".$file_path." | ".cmd_matched_filters($config, 0).cmd_sql($config, 0, $run_id)." >> ". $file_path." 2>&1'";
		}
		if($_POST["timer_start_0"]=="start_boot"){
			$change= "@reboot root " .$cmd;
			$search = "logger-sdr-d0";
			$file_to_replace="/tmp/crontab";
			$cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"";
			
			start_docker_echo($cmd_change,"tab_logger_settings","Logger will now start upon boot with the given settings");
		}
		if($_POST["timer_start_0"]=="start_no"){
			$change= "#logger-sdr-d0";
			$search = "logger-sdr-d0";
			$file_to_replace="/tmp/crontab";
			$cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"";
			start_docker($cmd_change,"tab_logger_settings");
		}
		if($_POST["timer_start_0"]=="start_time"){
			$change= substr($config['logger']['timer_start_time_0'],3, 2) . " ".substr($config['logger']['timer_start_time_0'], 0, 2)." * * * root " .$cmd;
			$search = "logger-sdr-d0";
			$file_to_replace="/tmp/crontab";
			$cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";
			echo $cmd_change;
			start_docker_echo($cmd_change,"tab_logger_settings","System will now start logger upon stated time");			
		}
		if($_POST["timer_stop_0"]=="stop_no"){
			$stop_cmd="sudo docker stop \\$(sudo docker ps -a -q --filter name=logger-sdr-d0)";
			$change= "#".$stop_cmd;
			$search = $stop_cmd;
			$file_to_replace="/tmp/crontab";
			$cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";
			start_docker($cmd_change,"tab_logger_settings");			
		}
		if($_POST["timer_stop_0"]=="stop_time"){
			$stop_cmd="sudo docker stop \\$(sudo docker ps -a -q --filter name=logger-sdr-d0)";
			$change=substr($config['logger']['timer_stop_time_1'],3, 2) . " ".substr($config['logger']['timer_stop_time_1'], 0, 2)." * * * root " .$stop_cmd;
			$search = $stop_cmd;
			$file_to_replace="/tmp/crontab";
			$cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";
			start_docker_echo($cmd_change,"tab_logger_settings","System will now stop logger upon stated time");			
		}
	}
	
	if (isset($_POST["change_logger_settings_1"])){
		$file_name = $config['logger']['antenna_id_1'] ."_". '\$(date +%Y_%m_%k_%M_%S)';
		$file_path = "/tmp/record/" . $file_name;
		if($_POST["timer_start_1"]=="start_boot"||$_POST["timer_start_1"]=="start_time"){
			$run_id = write_run_to_db($config, 1, $config['logger']['antenna_id_1']."_".$config['logger']['timer_start_time_1']);
		}
		if($_POST["timer_mode_1"]=="single_freq"){
			$cmd = cmd_docker(1)." '".cmd_rtl_sdr($config, 1)." 2> ".$file_path." | ".cmd_matched_filters($config, 1).cmd_sql($config, 1, $run_id)." >> ". $file_path." 2>&1'";
		}
		if($_POST["timer_mode_1"]=="freq_range"){
			$cmd = cmd_docker(1)." '".cmd_rtl_sdr($config, 1)." 2> ".$file_path." | ".cmd_matched_filters($config, 1).cmd_sql($config, 1, $run_id)." >> ". $file_path." 2>&1'";
		}
		if($_POST["timer_start_1"]=="start_boot"){
			$change= "@reboot root " .$cmd;
			$search = "logger-sdr-d1";
			$file_to_replace="/tmp/crontab";
			$cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"";
			
			start_docker_echo($cmd_change,"tab_logger_settings","Logger will now start upon boot with the given settings");
		}
		if($_POST["timer_start_1"]=="start_no"){
			$change= "#logger-sdr-d1";
			$search = "logger-sdr-d1";
			$file_to_replace="/tmp/crontab";
			$cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"";
			start_docker_echo($cmd_change,"tab_logger_settings","System will not start logger upon start");
		}
		if($_POST["timer_start_1"]=="start_time"){
			$change= substr($config['logger']['timer_start_time_1'],3, 2) . " ".substr($config['logger']['timer_start_time_1'], 0, 2)." * * * root " .$cmd;
			$search = "logger-sdr-d1";
			$file_to_replace="/tmp/crontab";
			$cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";
			start_docker_echo($cmd_change,"tab_logger_settings","System will now start logger upon stated time");			
		}
	}
	
	//Compile function 
	if (isset($_POST["compile"])){
		$gcc="gcc -g -O2  -ffast-math -mcpu=cortex-a7 -mfloat-abi=hard -mfpu=neon-vfpv4 -Wall -I/usr/include/mysql -fPIC  /tmp/rtlsdr_signal_detect.c /liquidsdr/liquid-dsp/libliquid.a -o /tmp/rtlsdr_signal_detect -lfftw3f -lm -lc -I/usr/include/mysql -L/usr/lib/arm-linux-gnueabihf -lmysqlclient -lpthread -lz -lm -ldl";
		$cmd = "sudo docker run -t --rm -v /var/www/html/sdr/liquidsdr/:/tmp/ liquidsdr:1.0 ".$gcc;
		start_docker($cmd,'tab_logger_settings');
		$gcc_matched="gcc -g -O2  -ffast-math -mcpu=cortex-a7 -mfloat-abi=hard -mfpu=neon-vfpv4 -Wall -I/usr/include/mysql -fPIC  /tmp/matched_filter.c /liquidsdr/liquid-dsp/libliquid.a -o /tmp/matched_signal_detect -lfftw3f -lm -lc -I/usr/include/mysql -L/usr/lib/arm-linux-gnueabihf -lmysqlclient -lpthread -lz -lm -ldl";
		$cmd_machted = "sudo docker run -t --rm -v /var/www/html/sdr/liquidsdr/:/tmp/ liquidsdr:1.0 ".$gcc_matched;
		start_docker($cmd_machted,'tab_logger_settings');
	}
	
	if (isset($_POST["compile_raspi_zero"])){
		$gcc="gcc -g -O2  -ffast-math -Wall -I/usr/include/mysql -fPIC  /tmp/rtlsdr_signal_detect.c /liquidsdr/liquid-dsp/libliquid.a -o /tmp/rtlsdr_signal_detect -lfftw3f -lm -lc -I/usr/include/mysql -L/usr/lib/arm-linux-gnueabihf -lmysqlclient -lpthread -lz -lm -ldl";
		$cmd = "sudo docker run -t --rm -v /var/www/html/sdr/liquidsdr/:/tmp/ liquidsdr:1.0 ".$gcc;
		start_docker($cmd,'tab_logger_settings');
		$gcc_matched="gcc -g -O2  -ffast-math -Wall -I/usr/include/mysql -fPIC  /tmp/matched_filter.c /liquidsdr/liquid-dsp/libliquid.a -o /tmp/matched_signal_detect -lfftw3f -lm -lc -I/usr/include/mysql -L/usr/lib/arm-linux-gnueabihf -lmysqlclient -lpthread -lz -lm -ldl";
		$cmd_machted = "sudo docker run -t --rm -v /var/www/html/sdr/liquidsdr/:/tmp/ liquidsdr:1.0 ".$gcc_matched;
		start_docker($cmd_machted,'tab_logger_settings');
	}
	
	//Remote Connections
	if (isset($_POST["stop_vpn"])){
		$cmd = "sudo docker stop $(sudo docker ps -a -q --filter name=vpn_tunnel) 2>&1"; 
		$result = system($cmd);
	}
	if (isset($_POST["start_vpn"])){ 
		$cmd = "sudo docker run --rm --name vpn_tunnel -v /var/www/html/connect/:/config/ --privileged --net=host -t umts:1.0 openvpn /config/client.conf 2>&1";
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
	
	//Connect Cornjob Functions
	if (isset($_POST["change_VPN_cron"])){
		$cmd = "sudo docker run --rm --name vpn_tunnel -v /var/www/html/connect/:/config/ --privileged --net=host -t umts:1.0 openvpn /config/client.conf";
		$search = "sudo docker run --rm --name vpn_tunnel";
		if($_POST["time_start_vpn"]=="reboot"){
			$change= "@reboot root " .$cmd;
			$file_to_replace="/tmp/crontab";
			echo "System will now start VPN tunnel upon boot.";
			start_docker("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"", 'tab_logger_timer');
		}
		if($_POST["time_start_vpn"]=="start_no"){
			$change= "#@reboot root " .$cmd;
			$file_to_replace="/tmp/crontab";
			echo "System will not start VPN tunnel upon start.";
			start_docker("sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"", 'tab_logger_timer');
		}
	}	
	
	//Check whether Receivers are running update_device_info
	if (isset($_POST["update_device_info_fr"])){
		echo "<script type='text/javascript'>document.getElementById('tab_logger_range').style.display = 'block';</script>";
	}
	if (isset($_POST["update_device_info_sf"])){
		echo "<script type='text/javascript'>document.getElementById('tab_logger_single').style.display = 'block';</script>";
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
	
	//SDR# - Server
	
	if (isset($_POST["rtl_tcp_start_d0"])){
		$cmd = "sudo docker run --rm --name=sharp_server_sdr_d0 -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1234 rtlsdr:1.0 rtl_tcp -d 0 -a  '0.0.0.0' -p '1234' 2>&1";
		start_docker_quite($cmd,'sdr_server');
	}
	if (isset($_POST["rtl_tcp_start_81_d0"])){
		$cmd = "sudo docker run --rm --name=sharp_server_sdr_d0 -t --device=/dev/bus/usb -p 81:1234 rtlsdr:1.0 rtl_tcp -d 0 -a  '0.0.0.0' -p '1234' 2>&1";
		start_docker_quite($cmd,'sdr_server');
	}
	if (isset($_POST["rtl_tcp_stop_d0"])){
		$cmd = "sudo docker stop sharp_server_sdr_d0 2>&1";
		start_docker($cmd,'sdr_server');
	}
	
	if (isset($_POST["rtl_tcp_start_d1"])){
		$cmd = "sudo docker run --rm --name=sharp_server_sdr_d1 -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+2).":1234 rtlsdr:1.0 rtl_tcp -d 1 -a  '0.0.0.0' -p '1234' 2>&1";
		start_docker_quite($cmd,'sdr_server');
	}
	if (isset($_POST["rtl_tcp_start_82_d1"])){
		$cmd = "sudo docker run --rm --name=sharp_server_sdr_d1 -t --device=/dev/bus/usb -p 82:1234 rtlsdr:1.0 rtl_tcp -d 1 -a  '0.0.0.0' -p '1234' 2>&1";
		start_docker_quite($cmd,'sdr_server');
	}
	if (isset($_POST["rtl_tcp_stop_d1"])){
		$cmd = "sudo docker stop sharp_server_sdr_d1) 2>&1";
		start_docker($cmd,'sdr_server');
	}
	
	//Data-Database Settings
	if (isset($_POST["zip_camera"])){
		$cmd="sudo docker run -t --rm --privileged -v /var/www/html/picam/:/tmp/ git:1.0 zip -r /tmp/zipped/".$_POST["zip__camera_name"]." /tmp/record/ 2>&1";
		start_docker($cmd,'camera_data');
	}
	if (isset($_POST["rm_cam_zip_folder"])){
		$cmd = "rm -rf /var/www/html/picam/zipped/* 2>&1";
		start_docker($cmd,'camera_data');
	}
	if (isset($_POST["rm_cam_record_folder"])){
		$cmd = "rm -rf /var/www/html/picam/record/* 2>&1";
		start_docker($cmd,'camera_data');
	}
	if (isset($_POST["zip_logger"])){
		$cmd = "sudo docker run -t --rm --privileged -v /var/www/html/sdr/:/tmp/ git:1.0 zip -r /tmp/zipped/".$_POST["zip_logger_name"]." /tmp/record/ 2>&1";
		start_docker($cmd,'radio_data');
	}
	if (isset($_POST["rm_logger_zip_folder"])){
		$cmd = "rm -rf /var/www/html/sdr/zipped/* 2>&1";
		start_docker($cmd,'radio_data');
	}
	if (isset($_POST["rm_logger_record_folder"])){
		$cmd = "rm -rf /var/www/html/sdr/record/* 2>&1";
		start_docker($cmd,'radio_data');
	}
	if (isset($_POST["start_mysql"])){
		$autostart=isset($config['database']['db_start']) && $config['database']['db_start']=="Yes" ? "--restart=always " : "";
		$rm_container=isset($config['database']['db_start']) && $config['database']['db_start']!="Yes" ? "--rm " : "";
		$cmd = "sudo docker run -t ".$autostart."--name=mysql ".$rm_container." -e MYSQL_ROOT_PASSWORD=rteuv2! -p 3306:3306 -v /var/www/html/data/mysql:/var/lib/mysql mysql:1.0 2>&1";
		echo $cmd;
		start_docker($cmd,'mysql');
	}
	if (isset($_POST["stop_mysql"])){
		$cmd = "sudo docker stop mysql";
		start_docker($cmd,'mysql');
	}
	if (isset($_POST["start_phpmyadmin"])){
		$cmd = "sudo docker run -t --name=phpmyadmin--rm --net=host -v /var/www/html/data/:/cfiles/ phpmyadmin:1.0 2>&1";
		start_docker_quite($cmd,'phpmyadmin');
	}
	if (isset($_POST["stop_phpmyadmin"])){
		$cmd = "sudo docker stop phpmyadmin";
		start_docker($cmd,'phpmyadmin');
	}
	
	if (isset($_POST["change_db_settings"])){
		echo "<script type='text/javascript'>document.getElementById('mysql').style.display = 'block';</script>";
	}
	
	//System functions
	
	if (isset($_POST["update_date"])){
		echo '<pre>';
		$test = system("sudo docker run -t --rm --privileged git:1.0 date --set \"".$_POST["new_date"]."\" 2>&1", $ret);
		echo '</pre>';
	}
	if (isset($_POST["change_hostname"])){
		echo '<pre>';
		$test = system("sudo docker run -t --rm -v /var/www/html/git/:/tmp1/ -v /etc/:/tmp/ git:1.0 bash /tmp1/change_hostname.sh ".$_POST["new_hostname"]." 2>&1", $ret);
		echo '</pre>';
	}
	if (isset($_POST["cron_light_on"])){
		echo '<pre>';
		$cmd = $_POST["cron_lights"]."root       sudo docker run -t --rm --privileged -v /var/www/html/picam/:/tmp/ i2c:1.0 sh /tmp/start_all_lights.sh 2>&1";
		$file = "/etc/crontab";
		$test = system("sudo docker run -t --rm --privileged -v /var/www/html/git/:/tmp/ git:1.0 sh /tmp/add_cronjob.sh ".$cmd." ".$file , $ret);
		echo '</pre>';
	}	
	if (isset($_POST["exp_disc"])){
		echo '<pre>';
		$test = system("sudo docker run -t --rm -v /var/www/html/git/:/tmp1/ -v /etc/:/tmp/ git:1.0 bash /tmp1/expand_disk.sh 2>&1", $ret);
		echo '</pre>';
	}
	if (isset($_POST["stop_exp_disc"])){
		echo '<pre>';
		$test = system("sudo docker run -t --rm -v /var/www/html/git/:/tmp1/ -v /etc/:/tmp/ git:1.0 bash /tmp1/stop_expand.sh 2>&1", $ret);
		echo '</pre>';
	}
	if (isset($_POST["disc_usage"])){
		echo '<pre>';
		$test = system('df -h', $ret);
		echo '</pre>';
	}	
	if (isset($_POST["reboot"])){
		echo '<pre>';
		$test = system('sudo reboot', $ret);
		echo '</pre>';
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
	
	//SQL Functions
	
	function write_run_to_db($config, $device, $file_name) {
		if ($config['logger']['use_sql_'.$device] != "Yes")
			return "Wirting to database is switched off - see settings";
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
// Script to close the window upon click outside the box
// Get the modal
var modal = document.getElementById('output_php');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
