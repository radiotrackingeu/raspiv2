<div id="output_php" class="w3-modal">
	<div class="w3-modal-content" style="width: 90%">
		<div class="w3-container w3-blue">
			<span onclick="document.getElementById('output_php').style.display='none'" class="w3-button w3-display-topright">&times;</span>
					

<?php
////////////////////////    Radio     ////////////////////////
{
  //--------------------    Logger    --------------------//
  {
      //Functions
    function cmd_docker($dev) {
        return "sudo docker run --rm --name=logger-sdr-d".$dev." --net=host -t --device=/dev/bus/usb -v /var/www/html/sdr/:/tmp/ liquidsdr:1.0 bash -c";
    }
    function cmd_sql($config, $dev, $run_id) {
        if (!is_int($run_id)) {
            return "";
        }
        return " --sql --db_host ".$config['database']['db_host']." --db_port ".$config['database']['db_port']." --db_user ".$config['database']['db_user']." --db_pass ".$config['database']['db_pass']." --db_run_id ".$run_id;
    }
    function cmd_rtl_sdr($config, $dev) {
        return "rtl_sdr -d ".$dev." -f ".$config['logger']['center_freq_'.$dev]." -s ".$config['logger']['freq_range_'.$dev]." -g ".$config['logger']['log_gain_'.$dev]." -";
    }
    function cmd_liquidsdr($config, $dev) {
        return "/tmp/liquidsdr/rtlsdr_signal_detect -s -t ".$config['logger']['threshold_'.$dev]." -r ".$config['logger']['freq_range_'.$dev]." -b ".$config['logger']['nfft_'.$dev]." -n ".$config['logger']['timestep_'.$dev]." --ll ".$config['logger']['minDuration_'.$dev]." --lu ".$config['logger']['maxDuration_'.$dev];
    }
    function cmd_matched_filters($config, $dev) {
        return "/tmp/liquidsdr/matched_signal_detect -s -t ".$config['logger']['threshold_'.$dev]." -r ".$config['logger']['freq_range_'.$dev]." -p 22";
    }
    function write_run_to_db($config, $device, $file_name) {
        $hostname=`hostname`;
        $PiSN=`cat /proc/cpuinfo | grep Serial | cut -d ' ' -f 2`;
        if ($config['logger']['use_sql_'.$device] != "Yes")
            return "Writing to database is switched off - see settings";
        $con = mysqli_connect($config['database']['db_host'].":".$config['database']['db_port'], $config['database']['db_user'], $config['database']['db_pass']);
            if (mysqli_connect_errno()) {
                return "Connection to ".$config['database']['db_host'].":".$config['database']['db_port']." failed: " . mysqli_connect_error();	
            } else {
                $cmd_sql = "INSERT INTO rteu.runs (device,hostname,PiSN,pos_x,pos_y,orientation,beam_width,gain,center_freq,freq_range,threshold,fft_bins,fft_samples)".
                    " VALUE ('".$file_name."','".$hostname."','".$PiSN."',".
                                $config['logger']['antenna_position_N_'.$device].",".   $config['logger']['antenna_position_E_'.$device].",".
                                $config['logger']['antenna_orientation_'.$device].",".  $config['logger']['antenna_beam_width_'.$device].",".
                                $config['logger']['log_gain_'.$device].",".             $config['logger']['center_freq_'.$device].",".
                                $config['logger']['freq_range_'.$device].",".           $config['logger']['threshold_'.$device].",".
                                $config['logger']['nfft_'.$device].",".                 $config['logger']['timestep_'.$device].
                    ");";
                if(!mysqli_query($con, $cmd_sql))
                    return "Failed to write to db: ". mysqli_error($con);
                else
                    return mysqli_insert_id($con);
            }
    }
    //..................    Range     ..................//
    {
        //Start/Stop
        if (isset($_POST["log_start_all"])){
            for ($i=0; $i<$GLOBALS["num_rec"]; $i++) {
              if(report_device_use('Range Logger',$i,'tab_logger_range')) {
                $file_name = $config['logger']['antenna_id_'.$i] ."_". date('Y_m_d_H_i');
                $file_path = "/tmp/record/" . $file_name;
                $run_id = write_run_to_db($config, $i, $file_name);
                $cmd = cmd_docker($i)." '".cmd_rtl_sdr($config, $i)." 2> ".$file_path." | ".cmd_liquidsdr($config, $i).cmd_sql($config, $i, $run_id)." >> ". $file_path." 2>&1'";
                $msg = 'Started Receiver '.$i.'.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id.'<br>';
                start_docker_echo($cmd,'tab_logger_range',$msg);
              }
            }
        }
        if (isset($_POST["log_stop_all"])){
            $cmd ="sudo docker stop $(sudo docker ps --filter name=logger-sdr --format {{.Names}})";
            start_docker($cmd, 'tab_logger_range');
        }
        if (isset($_POST["log_start_0"])){
            if(report_device_use('Range Logger',0,'tab_logger_range')) {
              $file_name = $config['logger']['antenna_id_0'] ."_". date('Y_m_d_H_i');
              $file_path = "/tmp/record/" . $file_name;
              $run_id = write_run_to_db($config, 0, $file_name);
              $cmd = cmd_docker(0)." '".cmd_rtl_sdr($config, 0)." 2> ".$file_path." | ".cmd_liquidsdr($config, 0).cmd_sql($config, 0, $run_id)." >> ". $file_path." 2>&1'";
              start_docker_echo($cmd,'tab_logger_range','Started Receiver 0.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
            }
        }
        if (isset($_POST["log_stop_0"])){
            $cmd="sudo docker stop logger-sdr-d0 2>&1";
            start_docker($cmd, 'tab_logger_range');
        }
        if (isset($_POST["log_start_1"])){
          if(report_device_use('Range Logger',1,'tab_logger_range')) {
            $file_name = $config['logger']['antenna_id_1'] ."_". date('Y_m_d_H_i');
            $file_path = "/tmp/record/" . $file_name;
            $run_id = write_run_to_db($config, 1, $file_name);
            $cmd = cmd_docker(1)." '".cmd_rtl_sdr($config, 1)." 2> ".$file_path." | ".cmd_liquidsdr($config, 1).cmd_sql($config, 1, $run_id)." >> ". $file_path." 2>&1'";
            start_docker_echo($cmd,'tab_logger_range','Started Receiver 1.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
          }
        }
        if (isset($_POST["log_stop_1"])){
            $cmd="sudo docker stop logger-sdr-d1 2>&1";
            start_docker($cmd, 'tab_logger_range');
        }
        if (isset($_POST["log_start_2"])){
          if(report_device_use('Range Logger',2,'tab_logger_range')) {
            $file_name = $config['logger']['antenna_id_2'] ."_". date('Y_m_d_H_i');
            $file_path = "/tmp/record/" . $file_name;
            $run_id = write_run_to_db($config, 2, $file_name);
            $cmd = cmd_docker(2)." '".cmd_rtl_sdr($config, 2)." 2> ".$file_path." | ".cmd_liquidsdr($config, 2).cmd_sql($config, 2, $run_id)." >> ". $file_path." 2>&1'";
            start_docker_echo($cmd,'tab_logger_range','Started Receiver 2.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
          }
        }
        if (isset($_POST["log_stop_2"])){
            $cmd="sudo docker stop logger-sdr-d2 2>&1";
            start_docker($cmd, 'tab_logger_range');
        }
        if (isset($_POST["log_start_3"])){
          if(report_device_use('Range Logger',3,'tab_logger_range')) {
            $file_name = $config['logger']['antenna_id_3'] ."_". date('Y_m_d_H_i');
            $file_path = "/tmp/record/" . $file_name;
            $run_id = write_run_to_db($config, 3, $file_name);
            $cmd = cmd_docker(3)." '".cmd_rtl_sdr($config, 3)." 2> ".$file_path." | ".cmd_liquidsdr($config, 3).cmd_sql($config, 3, $run_id)." >> ". $file_path." 2>&1'";
            start_docker_echo($cmd,'tab_logger_range','Started Receiver 3.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
          }
        }
        if (isset($_POST["log_stop_3"])){
            $cmd="sudo docker stop logger-sdr-d3 2>&1";
            start_docker($cmd, 'tab_logger_range');
        }
    }
    //..................    Single    ..................//
    {
        //Start/Stop
        if (isset($_POST["log_single_start_all"])){
            for ($i=0; $i<$GLOBALS["num_rec"]; $i++) {
              if(report_device_use('Single Logger',$i,'tab_logger_single')) {
                $file_name = $config['logger']['antenna_id_'.$i] ."_". date('Y_m_d_H_i');
                $file_path = "/tmp/record/" . $file_name;
                $run_id = write_run_to_db($config, $i, $file_name);
                $cmd = cmd_docker($i)." '".cmd_rtl_sdr($config, $i)." 2> ".$file_path." | ".cmd_matched_filters($config, $i).cmd_sql($config, $i, $run_id)." >> ". $file_path." 2>&1'";
                $msg = 'Started Receiver '.$i.'.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id.'<br>';
                start_docker_echo($cmd,'tab_logger_single',$msg);
              }
            }
        }
        if (isset($_POST["log_single_stop_all"])){
            $cmd = "sudo docker stop $(sudo docker ps --filter name=logger-sdr --format {{.Names}})";
            start_docker($cmd, 'tab_logger_single');
        }
        if (isset($_POST["log_single_start_0"])){
          if(report_device_use('Single Logger',0,'tab_logger_single')) {
            $file_name = $config['logger']['antenna_id_0'] ."_". date('Y_m_d_H_i');
            $file_path = "/tmp/record/" . $file_name;
            $run_id = write_run_to_db($config, 0, $file_name);
            $cmd = cmd_docker(0)." '".cmd_rtl_sdr($config, 0)." 2> ".$file_path." | ".cmd_matched_filters($config, 0).cmd_sql($config, 0, $run_id)." >> ". $file_path." 2>&1'";
            start_docker_echo($cmd,'tab_logger_single','Started Receiver 0.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
          }
        }
        if (isset($_POST["log_single_stop_0"])){
            $cmd="sudo docker stop logger-sdr-d0 2>&1";
            start_docker($cmd, 'tab_logger_single');
        }
        if (isset($_POST["log_single_start_1"])){
          if(report_device_use('Single Logger',1,'tab_logger_single')) {
            $file_name = $config['logger']['antenna_id_1'] ."_". date('Y_m_d_H_i');
            $file_path = "/tmp/record/" . $file_name;
            $run_id = write_run_to_db($config, 1, $file_name);
            $cmd = cmd_docker(1)." '".cmd_rtl_sdr($config, 1)." 2> ".$file_path." | ".cmd_matched_filters($config, 1).cmd_sql($config, 1, $run_id)." >> ". $file_path." 2>&1'";
            start_docker_echo($cmd,'tab_logger_single','Started Receiver 1.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
          }
        }
        if (isset($_POST["log_single_stop_1"])){
            $cmd="sudo docker stop logger-sdr-d1 2>&1";
            start_docker($cmd, 'tab_logger_single');
        }
        if (isset($_POST["log_single_start_2"])){
          if(report_device_use('Single Logger',2,'tab_logger_single')) {
            $file_name = $config['logger']['antenna_id_2'] ."_". date('Y_m_d_H_i');
            $file_path = "/tmp/record/" . $file_name;
            $run_id = write_run_to_db($config, 2, $file_name);
            $cmd = cmd_docker(2)." '".cmd_rtl_sdr($config, 2)." 2> ".$file_path." | ".cmd_matched_filters($config, 2).cmd_sql($config, 2, $run_id)." >> ". $file_path." 2>&1'";
            start_docker_echo($cmd,'tab_logger_single','Started Receiver 2.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
          }
        }
        if (isset($_POST["log_single_stop_2"])){
            $cmd="sudo docker stop logger-sdr-d2 2>&1";
            start_docker($cmd, 'tab_logger_single');
        }
        if (isset($_POST["log_single_start_3"])){
          if(report_device_use('Single Logger',3,'tab_logger_single')) {
            $file_name = $config['logger']['antenna_id_3'] ."_". date('Y_m_d_H_i');
            $file_path = "/tmp/record/" . $file_name;
            $run_id = write_run_to_db($config, 3, $file_name);
            $cmd = cmd_docker(3)." '".cmd_rtl_sdr($config, 3)." 2> ".$file_path." | ".cmd_matched_filters($config, 3).cmd_sql($config, 3, $run_id)." >> ". $file_path." 2>&1'";
            start_docker_echo($cmd,'tab_logger_single','Started Receiver 3.<br>Device id: <a target="_blank" href="/sdr/record/'.$file_name.'">'.$file_name.'</a><br>Run id: '.$run_id);
          }
        }
        if (isset($_POST["log_single_stop_3"])){
            $cmd="sudo docker stop logger-sdr-d3 2>&1";
            start_docker($cmd, 'tab_logger_single');
        }
    }
    //..................   Settings   ..................//
    {
        //Settings
        if (isset($_POST["change_logger_settings"])){
            for ($i=0; $i<4; $i++) {
                $file_name = $config['logger']['antenna_id_'.$i] ."_". "\\$(date +\\\\\%Y_\\\\\%m_\\\\\%d_\\\\\%H_\\\\\%M_\\\\\%S)";
                $file_path = "/tmp/record/" . $file_name;
                $run_id = -1;
                if($_POST["timer_start_".$i]=="start_boot"||$_POST["timer_start_".$i]=="start_time"){
                    $run_id = write_run_to_db($config, $i, $config['logger']['antenna_id_'.$i]."_"."__autostarted___");
                }
                if($run_id>=0 && $_POST["timer_mode_".$i]=="single_freq"){
                    $cmd = cmd_docker($i)." '".cmd_rtl_sdr($config, $i)." 2> ".$file_path." | ".cmd_matched_filters($config, $i).cmd_sql($config, $i, $run_id)." >> ". $file_path." 2>\&1'";
                }
                if($run_id>=0 && $_POST["timer_mode_".$i]=="freq_range"){
                    $cmd = cmd_docker($i)." '".cmd_rtl_sdr($config, $i)." 2> ".$file_path." | ".cmd_liquidsdr($config, $i).cmd_sql($config, $i, $run_id)." >> ". $file_path." 2>\&1'";
                }
                if($run_id>=0 && $_POST["timer_start_".$i]=="start_boot"){
                    $change= "@reboot root " .$cmd;
                    $search = cmd_docker($i);
                    $file_to_replace="/tmp/crontab";
                    $cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"";
                    start_docker_echo($cmd_change,"tab_logger_settings","Receiver ".$i." will now start and log upon boot with the given settings.");
                }
                if($_POST["timer_start_".$i]=="start_no"){
                    $change= "#".cmd_docker($i);
                    $search = cmd_docker($i);
                    $file_to_replace="/tmp/crontab";
                    $cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/ -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"" .$file_to_replace."\"";
                    start_docker_echo($cmd_change,"tab_logger_settings", "Receiver ".$i." will not start automatically.");
                }
                if($run_id>=0 && $_POST["timer_start_".$i]=="start_time"){
                    #new input
                    $change= substr($config['logger']['timer_start_time_'.$i],3, 2) . " ".substr($config['logger']['timer_start_time_'.$i], 0, 2)." * * * root " .$cmd;
                    #where to write
                    $search = cmd_docker($i);
                    $file_to_replace="/tmp/crontab";
                    $cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";
                    //echo $cmd_change;
                    start_docker_echo($cmd_change,"tab_logger_settings","Receiver ".$i." will now start and log every day at ".$config['logger']['timer_start_time_'.$i].".");			
                }
                if($_POST["timer_stop_".$i]=="stop_no"){
                    $stop_cmd="sudo docker stop \\$(sudo docker ps -a -q --filter name=logger-sdr-d".$i.")";
                    $change= "#".$stop_cmd;
                    $search = $stop_cmd;
                    $file_to_replace="/tmp/crontab";
                    $cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";
                    start_docker_echo($cmd_change,"tab_logger_settings","Receiver ".$i." will not be stopped automatically.");			
                }
                if($_POST["timer_stop_".$i]=="stop_time"){
                    $stop_cmd="sudo docker stop \\$(sudo docker ps -a -q --filter name=logger-sdr-d".$i.")";
                    $change=substr($config['logger']['timer_stop_time_'.$i],3, 2) . " ".substr($config['logger']['timer_stop_time_'.$i], 0, 2)." * * * root " .$stop_cmd;
                    $search = $stop_cmd;
                    $file_to_replace="/tmp/crontab";
                    $cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";
                    start_docker_echo($cmd_change,"tab_logger_settings","Receiver ".$i." will be stopped every day at ".$config['logger']['timer_stop_time_'.$i].".");			
                }
                if ($_POST["use_sql_0"]=="Yes" || $_POST["use_sql_1"]=="Yes" || $_POST["use_sql_2"]=="Yes" || $_POST["use_sql_3"]=="Yes") {
                    if(!filter_var(shell_exec("sudo docker inspect -f {{.State.Running}} mysql"), FILTER_VALIDATE_BOOLEAN)) {
                        $cmd = "sudo docker start mysql 2>&1";
                        start_docker_echo($cmd,"tab_logger_settings", "Started MySQL Database.");
                    }
                }
                
            }
        }
        //Compiling 
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
    }
  }
  //----------------------  Spectrogram ----------------------//
  {
    //Functions
    function cmd_webRX_start($id) {
        if (!check_container_exists("webrx-sdr-d".$id))
            exec("sudo docker create -t --name webrx-sdr-d".$id." --device=/dev/bus/usb -v /var/www/html/sdr/:/cfiles/ -p ".($id+81).":8073 webrx:1.0 sh /cfiles/start_openwebrx_d".$id.".sh >/dev/null");
		return "sudo docker start webrx-sdr-d".$id;
}
    function cmd_webRX_stop($id) {
        return "sudo docker stop webrx-sdr-d".$id." 2>&1";
    }
    //....................  Spectrogram ....................//
    {    //Start/Stop
        if (isset($_POST["rtl_websdr_start_all"])){
            for ($i=0; $i<$GLOBALS["num_rec"]; $i++) {
            if(report_device_use('Spectoram',$i,'webrx_tab')) {
                start_docker_quite(cmd_webRX_start($i),'webrx_tab');
              }
            }
        }
        if (isset($_POST["rtl_websdr_stop_all"])){
            $cmd = "sudo docker stop $(sudo docker ps --filter name=webrx-sdr --format {{.Names}})";
            start_docker($cmd, 'webrx_tab');
        }
        if (isset($_POST["rtl_websdr_d0"])){
          if(report_device_use('Spectoram',1,'webrx_tab')) {
            start_docker_echo(cmd_webRX_start(0),'webrx_tab',"Starting Spectrogram server for receiver 0");
          }
        }
        if (isset($_POST["rtl_websdr_stop_d0"])){
            start_docker_echo(cmd_webRX_stop(0),'webrx_tab','Spectrogram Server 0 stopped if it was running');
        }
        if (isset($_POST["rtl_websdr_d1"])){
          if(report_device_use('Spectoram',1,'webrx_tab')) {
            start_docker_echo(cmd_webRX_start(1),'webrx_tab',"Starting Spectrogram server for receiver 1");
          }
        }
        if (isset($_POST["rtl_websdr_stop_d1"])){
            start_docker_echo(cmd_webRX_stop(1),'webrx_tab','Spectrogram Server 1 stopped if it was running');
        }
        if (isset($_POST["rtl_websdr_d2"])){
          if(report_device_use('Spectoram',2,'webrx_tab')) {
            start_docker_echo(cmd_webRX_start(2),'webrx_tab',"Starting Spectrogram server for receiver 2");
          }
        }
        if (isset($_POST["rtl_websdr_stop_d2"])){
            start_docker_echo(cmd_webRX_stop(2),'webrx_tab','Spectrogram Server 2 stopped if it was running');
        }
        if (isset($_POST["rtl_websdr_d3"])){
          if(report_device_use('Spectoram',3,'webrx_tab')) {
            start_docker_echo(cmd_webRX_start(3),'webrx_tab',"Starting Spectrogram server for receiver 3");
          }
        }
        if (isset($_POST["rtl_websdr_stop_d3"])){
            start_docker_echo(cmd_webRX_stop(3),'webrx_tab','Spectrogram Server 3 stopped if it was running');
        }
    }
    //....................   Settings   ....................//
    {    if (isset($_POST["change_webRX_settings"])){
            for ($i=0; $i<4; $i++) {
                echo "Receiver ".$i.":";
                $cmd = "sh /var/www/html/sdr/change_config_webrx.sh ".$i." ".$_POST["fft_fps_".$i]." ".$_POST["fft_size_".$i]." ".$_POST["samp_rate_".$i]." ".$_POST["center_freq_".$i]." ".$_POST["rf_gain_".$i]." 2>&1";
                start_docker($cmd,'settings_webrx_tab');
            }
        }
    }
  }
  //----------------------   WebRadio   ----------------------//
  {
    //Functions
    function cmd_webradio($config, $dev, $freq) {
        $cmd_docker = "sudo docker run --rm -t --name webradio_".$dev." --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1+$dev).":1240 rtlsdr:1.0 sh -c";
        $cmd_rtl_fm = "rtl_fm -M usb -f ".$config['SDR_Radio']['Freq'.$freq.'_'.$dev]." -g ".$config['SDR_Radio']['Radio_Gain_'.$dev]." -d ".$dev." | sox -traw -v 10 -r24k -es -b16 -c1 -V1 - -tmp3 - | socat -u - TCP-LISTEN:1240";
        return $cmd_docker." '".$cmd_rtl_fm."'";
    }
    function webradio_stop($dev) {
        $cmd = "sudo docker stop webradio_".$dev;
        system($cmd." >/dev/null 2>/dev/null &");
    }
    //....................   Playback   ....................//
    {   //Start
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
        //Stop
        if (isset($_POST["rtl_stop_0"])){
            webradio_stop(0);
        }
        if (isset($_POST["rtl_stop_1"])){
            webradio_stop(1);
        }
    }
  }
  //---------------------- SDR#-Server  ----------------------//
  {
    //Start/Stop
    if (isset($_POST["rtl_tcp_start_d0"])){
      if(report_device_use('SDR#-Server',0,'sdr_server')) {
        $cmd = "sudo docker run --rm --name=sharp_server-sdr-d0 -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+1).":1234 rtlsdr:1.0 rtl_tcp -d 0 -a  '0.0.0.0' -p '1234' 2>&1";
        start_docker_quite($cmd,'sdr_server');
      }
    }
    if (isset($_POST["rtl_tcp_start_81_d0"])){
      if(report_device_use('SDR#-Server',0,'sdr_server')) {
        $cmd = "sudo docker run --rm --name=sharp_server-sdr-d0 -t --device=/dev/bus/usb -p 81:1234 rtlsdr:1.0 rtl_tcp -d 0 -a  '0.0.0.0' -p '1234' 2>&1";
        start_docker_quite($cmd,'sdr_server');
      }
    }
    if (isset($_POST["rtl_tcp_stop_d0"])){
        $cmd = "sudo docker stop sharp_server-sdr-d0 2>&1";
        start_docker($cmd,'sdr_server');
    }
    if (isset($_POST["rtl_tcp_start_d1"])){
      if(report_device_use('SDR#-Server',1,'sdr_server')) {
        $cmd = "sudo docker run --rm --name=sharp_server-sdr-d1 -t --device=/dev/bus/usb -p ".($_SERVER['SERVER_PORT']+2).":1234 rtlsdr:1.0 rtl_tcp -d 1 -a  '0.0.0.0' -p '1234' 2>&1";
        start_docker_quite($cmd,'sdr_server');
      }
    }
    if (isset($_POST["rtl_tcp_start_82_d1"])){
      if(report_device_use('SDR#-Server',1,'sdr_server')) {
        $cmd = "sudo docker run --rm --name=sharp_server-sdr-d1 -t --device=/dev/bus/usb -p 82:1234 rtlsdr:1.0 rtl_tcp -d 1 -a  '0.0.0.0' -p '1234' 2>&1";
        start_docker_quite($cmd,'sdr_server');
      }
    }
    if (isset($_POST["rtl_tcp_stop_d1"])){
        $cmd = "sudo docker stop sharp_server-sdr-d1) 2>&1";
        start_docker($cmd,'sdr_server');
    }
  }
}
////////////////////////    Camera    ////////////////////////
{
    if (isset($_POST["activate_i2c"])){
      echo "<script type='text/javascript'>document.getElementById('output_php').style.display='block';</script>";
      echo '<pre>';
      $test = system("sudo docker run -t --rm --privileged -v /boot/:/tmp1/ -v /etc/:/tmp2/ -v /var/www/html/picam/:/tmp3/ i2c:1.0 sh /tmp3/start_i2c.sh 2>&1", $ret);
      echo '</pre>';
      
    }
    
    if (isset($_POST["deactivate_i2c"])){
      echo "<script type='text/javascript'>document.getElementById('output_php').style.display='block';</script>";
      echo '<pre>';
      $test = system("sudo docker run -t --rm --privileged -v /boot/:/tmp1/ -v /etc/:/tmp2/ -v /var/www/html/picam/:/tmp3/ i2c:1.0 sh /tmp3/stop_i2c.sh 2>&1", $ret);
      echo '</pre>';
    }
    // All scripts in picam.php
}
////////////////////////  Microphone  ////////////////////////
{
    // All scripts in micro.php
}
////////////////////////     Data     ////////////////////////
{
  //--------------------    Camera    --------------------//
  {
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
  }
  //--------------------    Radio     --------------------//
  {
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
  }
  //--------------------   Database   --------------------//
  {
    if (isset($_POST["start_mysql"])){
        if (!check_container_exists("mysql"))
            exec("sudo docker create -t --restart=unless-stopped --name=mysql -e MYSQL_ROOT_PASSWORD=rteuv2! -p 3306:3306 -v /var/www/html/data/mysql:/var/lib/mysql  mysql:1.0 2>&1");
        $cmd = "sudo docker start mysql 2>&1";
        start_docker_quite($cmd,'mysql');
    }
    if (isset($_POST["stop_mysql"])){
        $cmd = "sudo docker stop mysql";
        start_docker($cmd,'mysql');
    }
    if (isset($_POST["start_phpmyadmin"])){
        $cmd = "sudo docker run -t --name=phpmyadmin --rm --net=host -v /var/www/html/data/:/cfiles/ phpmyadmin:1.0 2>&1";
        start_docker_quite($cmd,'mysql');
    }
    if (isset($_POST["stop_phpmyadmin"])){
        $cmd = "sudo docker stop phpmyadmin";
        start_docker($cmd,'mysql');
    }
    if (isset($_POST["empty_DB"])) {
        $cmd="sudo docker run -t --rm --net=host mysql:1.0 mysql --host=".$config['database']['db_host']." --user=".$config['database']['db_user']." --password=".$config['database']['db_pass']." rteu -e \"";
        $statement="";
        if (isset($_POST["db_keep"]) && $_POST["db_keep"] != 0) {
            $sql1 = "DELETE FROM \`signals\` WHERE \`id\` < (SELECT MIN(\`id\`) FROM (SELECT * FROM \`signals\` ORDER BY \`id\` DESC LIMIT ".$_POST["db_keep"].") AS \`last_entries\`);";
            $sql2 = "DELETE FROM \`runs\` WHERE NOT EXISTS ( SELECT 1 FROM \`signals\` WHERE \`runs\`.\`id\` = \`signals\`.\`run\`);";
            $cmd .= $sql1." ".$sql2."\"";
            $statement = "Deleted all but ".$_POST["db_keep"]." entries!";
        } else {
            $sql = "SET FOREIGN_KEY_CHECKS = 0; TRUNCATE table runs; TRUNCATE TABLE signals; SET FOREIGN_KEY_CHECKS = 1";
            $cmd .= $sql."\"";
            $statement = "Database is now empty!";
        }
        start_docker_echo($cmd, 'mysql', $statement);
    }
    if (isset($_POST["change_db_settings"]) || isset($_POST["empty_DB"])){
        echo "<script type='text/javascript'>document.getElementById('mysql').style.display = 'block';</script>";
    }
  }
}
////////////////////////     WiFi     ////////////////////////
{
  //Functions
	if (isset($_POST["reboot"])){
        $cmd = 'sudo reboot now';
        start_docker_echo($cmd,'','Rebooting now!');
	}
  //--------------------   Hotspot    --------------------//
  {
    if (isset($_POST["start_hotspot"])){
        $pw=$_POST["pw_hotspot"];
        if (strlen($pw)>=8 && strlen($pw)<=63) {
            $ssid=addslashes(addcslashes($_POST["ssid_hotspot"],"\\"));
            $pw=addslashes(addcslashes($pw,"\\"));
            $cmd = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/wifi/:/tmp1/ -v /etc/:/tmp/ wifi:1.0 sh /tmp1/start_hotspot_stop_wifi.sh \"".$ssid."\" \"".$pw."\"";
            start_docker_echo($cmd, 'hotspot', "Hotspot will be active after reboot.\n SSID:     ".$_POST["ssid_hotspot"]."\n Password: ".$_POST["pw_hotspot"]);
        } else
            start_docker_echo(":", 'hotspot', "Password needs to be 8 to 63 characters long.");
    }
  }
  //-------------------- WiFi Connect --------------------//
  {
    // be aware of the wifi version in the shell script!!!
    if (isset($_POST["connect_wifi"])){
        $ssid=addslashes(addcslashes($_POST["ssid_wifi"],"\\"));
        $pw=addslashes(addcslashes($_POST["pw_wifi"],"\\"));
        $cmd = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/wifi/:/tmp1/ -v /etc/:/tmp/ wifi:1.0 sh /tmp1/stop_hotspot_set_wifi_ssid.sh \"".$ssid."\" \"".$pw."\"";
        start_docker_echo($cmd, 'wifi_con', "Wifi will connect to new network after reboot. Hotspot will be deactivated.\n SSID:     ".$_POST["ssid_wifi"]."\n Password: ".$_POST["pw_wifi"]);
    }
  }
  //-------------------- LAN Connect  --------------------//
  {
    if (isset($_POST["change_lan"])){
        $cmd = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/wifi/:/tmp1/ -v /etc/:/tmp2/ wifi:1.0 sh /tmp1/static_lan.sh ".$_POST["lan_ip"]." ".$_POST["lan_gate"];
        start_docker($cmd,'lan');
    }
    if (isset($_POST["change_auto"])){
        $cmd = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/wifi/:/tmp1/ -v /etc/:/tmp2/ wifi:1.0 sh /tmp1/dhcp_lan.sh ".$_POST["lan_ip"]." ".$_POST["lan_gate"];
        start_docker($cmd,'lan');
    }
    if (isset($_POST["ifconfig_all"])){
        $cmd = 'ifconfig -a';
        start_docker($cmd,'lan');
    }
  }
}
////////////////////////    Remote    ////////////////////////
{
  //--------------------    Tunnel    --------------------//
  {
    //Start/Stop
    if (isset($_POST["stop_vpn"])){
        $cmd = "sudo docker stop vpn_tunnel 2>&1"; 
        start_docker($cmd,'VPN');
    }
    if (isset($_POST["start_vpn"])){ 
        $cmd = "sudo docker run --rm --name vpn_tunnel -v /var/www/html/connect/:/config/ --privileged --net=host -t umts:1.0 openvpn /config/client.conf 2>&1";
        start_docker_quite($cmd,'VPN');
    }
    //Cron
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
  }
  //--------------------    Setup     --------------------//
  {
    if (isset($_POST["upload_cert"])){
        $target_dir = "/connect/";
        $target_file = "/var/www/html/connect/client.conf";
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file has been uploaded.";
            if (is_writeable($target_file)) {
                prepend("#certname:".$_FILES["fileToUpload"]["name"]."\n",$target_file);
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    if (isset($_POST["rm_cert"])){
        $cmd = "rm /var/www/html/connect/client.conf";
        start_docker_echo($cmd,'VPN_Setup','Config has been removed');
    }
  }
}
////////////////////////    System    ////////////////////////
{
  //--------------------   Software   --------------------//
  {
    //..................  Sys Update  ..................//
        //git update
        if (isset($_POST["update_rep"])){
            $cmd='sudo docker run --rm -t -v /home/pi/gitrep/:/home/pi/gitrep/ -v /var/www/html/:/var/www/html/ --net="host" git:1.0 sh /home/pi/gitrep/raspiv2/Docker/gitlab/update_html.sh '.$_POST["git_checkout"].' '.$_POST["git_keepcfg"].' 2>&1';
            start_docker($cmd,'GIT');
        }
    //..................  App Update  ..................//
        //scripts in gitlab.php
    //..................  App Status  ..................//
        //list running containers
        if (isset($_POST["running_containers"])){
            $cmd='sudo docker ps';
            start_docker($cmd,'running_docker');
        }
    //..................    Config    ..................//
        //scripts in gitlab.php
  }
  //--------------------    System    --------------------//
  {
    //..................  Time/Date   ..................//
    {
        if (isset($_POST["update_date"])){
            $cmd = "sudo docker run -t --rm --privileged git:1.0 date --set \"".$_POST["new_date"]."\" 2>&1";
            start_docker($cmd,'date');
        }
        if (isset($_POST["update_date_from_client"])){
            $cmd = "sudo docker run -t --rm --privileged git:1.0 date --set \"".$_POST["client_time_input"]."\" 2>&1";
            start_docker($cmd,'date');
        }
    }
    //..................   Hostname   ..................//
    {
        if (isset($_POST["change_hostname"])){
            $cmd = "sudo docker run -t --rm -v /var/www/html/git/:/tmp1/ -v /etc/:/tmp/ git:1.0 bash /tmp1/change_hostname.sh ".$_POST["new_hostname"]." 2>&1";
            start_docker($cmd,'hostname');
        }
    }
    //..................     Disc     ..................//
    {
        if (isset($_POST["exp_disc"])){
            $cmd = "sudo docker run -t --rm -v /var/www/html/git/:/tmp1/ -v /etc/:/tmp/ git:1.0 bash /tmp1/expand_disk.sh 2>&1";
            start_docker($cmd,"expand_disc");
        }
        if (isset($_POST["stop_exp_disc"])){
            $cmd = "sudo docker run -t --rm -v /var/www/html/git/:/tmp1/ -v /etc/:/tmp/ git:1.0 bash /tmp1/stop_expand.sh 2>&1";
            start_docker($cmd,"expand_disc");
        }
        if (isset($_POST["disc_usage"])){
            $cmd = "df -h";
            start_docker($cmd,"expand_disc");
        }
    }
    //..................     USB      ..................//
    {
        $cmd_usbpower = "sudo docker run --rm -d --privileged hubctrl:1.0 ./hub-ctrl -h 0 -P 2 -p ";
        //On/Off
        if (isset($_POST['usb_power_on'])) {
                start_docker_echo($cmd_usbpower.'1 2>&1', 'usbpower', 'USB ports on.');
        }
        if (isset($_POST['usb_power_off'])) {
                start_docker_echo($cmd_usbpower.'0 2>&1', 'usbpower', 'USB port off.');
        }
        //Cron
        if(isset($_POST["disable_usb_timer"])){
            $change= "#".$cmd_usbpower.'1';
            $search = $cmd_usbpower.'1';
            $file_to_replace="/tmp/crontab";
            $cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";
            start_docker_echo($cmd_change,"usbpower","USB power on timer disabled.");			
            
            $change= "#".$cmd_usbpower.'0';
            $search = $cmd_usbpower.'0';
            $file_to_replace="/tmp/crontab";
            $cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";
            start_docker_echo($cmd_change,"usbpower","USB power off timer disabled.");			
        }
        if(isset($_POST["set_usb_timer"])){
            $change= substr($config['system']['usb_timer_start_time'],3, 2) . " ".substr($config['system']['usb_timer_start_time'], 0, 2)." * * * root " .$cmd_usbpower.'1';
            $search = $cmd_usbpower.'1';
            $file_to_replace="/tmp/crontab";
            $cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";
            start_docker_echo($cmd_change,"usbpower","USB power on timer set.");			
            
            $change= substr($config['system']['usb_timer_stop_time'],3, 2) . " ".substr($config['system']['usb_timer_stop_time'], 0, 2)." * * * root " .$cmd_usbpower.'0';
            $search = $cmd_usbpower.'0';
            $file_to_replace="/tmp/crontab";
            $cmd_change = "sudo docker run -t --rm --privileged --net=host -v /var/www/html/sdr/:/tmp1/  -v /etc/:/tmp/ git:1.0 sh /tmp1/cronjob_logger.sh \"".$search."\" \"".$change."\" \"".$file_to_replace."\"";
            start_docker_echo($cmd_change,"usbpower","USB power off timer set.");			
        }
    }
    //..................  Sensors   ..................//
    {
      if (isset($_POST['sensors_start'])) {
        $hostname=exec('hostname');
        $cmd_docker = "sudo docker run --rm -t --net=host -v /home/pi/gitrep/raspiv2/Docker/sensors/script.py:/root/script.py --privileged sensors python /root/script.py -n ".$hostname." -h ".$config['database']['db_host']." -P ".$config['database']['db_port']." -u ".$config['database']['db_user']." -p ".$config['database']['db_pass'];
        $replace = "*/".$_POST['sensors_interval']." * * * * root ".$cmd_docker;
        $search = "sudo docker run.* sensors";
        $file_to_replace = "/tmp/crontab";
        $cmd_change = "sudo docker run -t --rm -v /var/www/html/sdr/cronjob_logger.sh:/tmp/cronjob.sh -v /etc:/tmp git:1.0 sh /tmp/cronjob.sh \"".$search."\" \"".$replace."\" \"" .$file_to_replace."\"";
        start_docker_echo($cmd_change, "sensors", "System and sensor values will now be logged every ".$_POST['sensors_interval']." minutes to database at ".$config['database']['db_host'].".");
      }
      if (isset($_POST['sensors_stop'])) {
        $search = "sudo docker run.* sensors";
        $replace = "#sudo docker run sensors";
        $file_to_replace = "/tmp/crontab";
        $cmd_change = "sudo docker run -t --rm -v /var/www/html/sdr/cronjob_logger.sh:/tmp/cronjob.sh -v /etc:/tmp git:1.0 sh /tmp/cronjob.sh \"".$search."\" \"".$replace."\" \"" .$file_to_replace."\"";
        start_docker_echo($cmd_change, "sensors", "System and sensor values will no longer be logged.");
      }
      if (isset($_POST['sensors_create_table'])) {
        echo "<script type='text/javascript'>document.getElementById('output_php').style.display='block';</script>";
        echo '<pre>';
        $con = @mysqli_connect($config['database']['db_host'].":".$config['database']['db_port'], $config['database']['db_user'], $config['database']['db_pass'],"rteu");
        if (mysqli_connect_errno()) {
          echo "Connection to ".$config['database']['db_host'].":".$config['database']['db_port']." failed: " . mysqli_connect_error();
        } else {
          $create = "CREATE TABLE IF NOT EXISTS sensors (
                      `id` bigint(20) NOT NULL AUTO_INCREMENT,
                      `timestamp` varchar(30) NOT NULL,
                      `hostname` varchar(60) NOT NULL,
                      `PiSN` varchar(20) NOT NULL,
                      `cpu_temp` float DEFAULT NULL,
                      `air_temp` float DEFAULT NULL,
                      `air_pressure` float DEFAULT NULL,
                      `air_humidity` float DEFAULT NULL,
                      `battery_voltage` float DEFAULT NULL,
                      `magnetometer` float DEFAULT NULL,
                      `disk_space_used` smallint(6) NOT NULL,
                      `cpu_load` tinyint(4) NOT NULL,
                      `mem_load` tinyint(4) NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ";
          //echo $create."<br>";
          $result = mysqli_query($con, $create);
          //echo mysqli_error($con);
          if ($result)
            echo "Successfully created table </em>rteu.sensors</em>";
          else {
            echo "Error: Could not create table!";
            echo mysqli_error($con);
          }
          mysqli_free_result($result);
        }
        mysqli_close($con);
        echo '</pre>';
        echo "<script type='text/javascript'>document.getElementById('".$block_to_jump."').style.display = 'block';</script>";
      }
    }
    //..................  Passwords   ..................//
    {
        //http
        if(isset($_POST['update_password'])){
            $cmd = "sudo docker run -t --rm -v /etc/apache2/.htpasswd:/tmp/pwfile pwchange:1.1 /pwchange.sh \"".$_POST['old_pw']."\" \"".$_POST['new_pw']."\" \"".$_POST['new_pw_confirm']."\" 2>&1";
            start_docker($cmd, "passwords");
        }
        //mysql
        if(isset($_POST['update_mysql_password'])){
            $cmd = "sudo docker run -t --net=host --rm pwchange:1.1 /mysql_pwchange.sh \"".$_POST['old_mysql_pw']."\" \"".$_POST['new_mysql_pw']."\" \"".$_POST['new_mysql_pw_confirm']."\" 2>&1";
            start_docker($cmd, "passwords");
        }
    }
    //..................   Sys Info   ..................//
        //scripts in system.php
  }
}
////////////////////////    License   ////////////////////////
{
    //no scripts
}
////////////////////////    General   ////////////////////////
{
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
        if(filter_var(shell_exec("sudo docker inspect -f {{.State.Running}} ".$docker_name), FILTER_VALIDATE_BOOLEAN)){
            echo "<span class='w3-tag w3-red w3-xlarge'>Device is in use</span> \n \n";
        }
        else{
            echo "Device is not in use";
        }
    }
    function check_container_exists($name) {
        $ret_val = 1;
        system("sudo docker inspect ".$name." >/dev/null 2>&1", $ret_val);
        return !(filter_var($ret_val,FILTER_VALIDATE_BOOLEAN));
    }
        
    function report_device_use($service,$id,$block) {
      $avail=!check_device_use($id);
      if (!$avail) {
        start_docker_echo("",$block,"Could not start ".$service." on Receiver ".$id."<br>Receiver ".$id." is currently in use by another service (i.e. Logger, Spectrogram, SDR#, Webradio, ...).");
      }
      return($avail);
    }
}
////////////////////////    Unused    ////////////////////////
{
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
