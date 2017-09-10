
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
		$cmd = "sudo docker run --rm --name logger-sdr-d1 -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/home/ rtl_433_mod bash -c 'rtl_433 -f ".$_POST["center_freq"]." -s ".$_POST["freq_range"]." -t -q -A -l ".$_POST["log_level"]." -g " . $_POST["log_gain"]. " 2> /home/" . $_POST["log_name"]."'";
		start_docker_quite($cmd,'logger');
	}
	if (isset($_POST["log_stop"])){
		$cmd="sudo docker stop $(sudo docker ps -a -q --filter name=logger-sdr-d1) 2>&1";
		start_docker($cmd, 'logger');
	}
	//Raw Data Recorder Functions
	if (isset($_POST["sdr_start"])){
		$cmd = "sudo docker run --rm -t --device=/dev/bus/usb -v /var/www/html/sdr/record/:/tmp/ rtlsdr bash -c 'rtl_sdr -f ".$_POST["center_freq"]." -s ".$_POST["freq_range"]." -g ".$_POST["log_gain"]." /tmp/".$_POST["log_name"]."'";
		echo $cmd;
		start_docker_quite($cmd,'raw_data');
	}
	//General Functions
	function start_docker($docker_cmd,$block_to_jump){
		echo "<script type='text/javascript'>document.getElementById('output_php').style.display='block';</script>";
		echo '<pre>';
		$test = system($docker_cmd, $ret);
		echo '</pre>';
		echo "<script type='text/javascript'>document.getElementById('".$block_to_jump."').style.display = 'block';</script>";
		check_docker($docker_name);
	}
	function start_docker_quite($docker_cmd,$block_to_jump){
		echo "<script type='text/javascript'>document.getElementById('output_php').style.display='block';</script>";
		system($docker_cmd." >/dev/null 2>/dev/null &");
		echo "<p>Process started</p>";
		echo "<script type='text/javascript'>document.getElementById('".$block_to_jump."').style.display = 'block';</script>";	
	}
	function check_docker($docker_name){
		if(shell_exec("sudo docker inspect -f {{.State.Running}} $(sudo docker ps -a -q --filter name=".$docker_name.")")){
			echo "<span class='w3-tag w3-red w3-xlarge'>Device is in use</span> \n";
		}
		else{
			echo "Device is not in use";
		}
	}
?>

		</div>
	</div>
</div>