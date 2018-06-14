<!DOCTYPE html>
<html>

<title>radio-tracking.eu</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/resources/weblib/w3.css">
<link rel="stylesheet" href="/resources/weblib/css/fontawesome-all.css">
<link rel="stylesheet" href="/resources/weblib/css/font-awesome.min.css">
<link rel="stylesheet" href="/resources/additional.css">
<script type="text/javascript">
    var serverTime= <?php echo time()*1000;?>;
    var localTime = new Date();
    var timeDiff = serverTime - localTime.getTime();

    setInterval(function() {
        var serverTime = new Date().getTime() + timeDiff;
        document.getElementById("server_time").innerHTML = new Date(serverTime).toUTCString();    
    }, 1000);
</script>

<body>
<?php
	//load config
	require_once './cfg/baseConfig.php';
	//load top menu
	require_once RESOURCES_PATH.'/header.php';
    //load functions
	require_once RESOURCES_PATH.'/helpers.php';
    //load ConfigLite
	require_once CONFIGLITE_PATH.'/Lite.php';
    
    //load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
 ?>
<!-- Enter text here-->

<div class="w3-bar w3-brown w3-mobile">
	<button class="w3-bar-item w3-button w3-mobile tablink active-item" onclick="openCity(event, 'status')">Status</button>
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'features')">Features</button>
	<button class="w3-bar-item w3-button w3-mobile tablink" onclick="openCity(event, 'pdf_manuals')">PDF Manuals</button>
</div>

<div id="status" class="city w3-mobile" style="display:block">
    <div class= "w3-row-padding">
        <div class="w3-panel w3-green w3-round w3-padding" style="margin-right:8px;margin-left:8px">
            <b id="server_time" style="float:right"></b>
            <h2><?php system("hostname")?></h2>
            <div style="margin-left:20px">
            <?php echo $GLOBALS["num_rec"] ?> receivers connected - <?php system("sudo docker ps | grep -c logger");?> running.<br>
            MySQL-Server: <span class="w3-tooltip" style="display:inline-block; margin-left:10px">
                <?php if(filter_var(shell_exec("sudo docker inspect -f {{.State.Running}} mysql"),FILTER_VALIDATE_BOOLEAN)) : ?>
					<span class="w3-text w3-small w3-round w3-brown w3-tag"style="position:absolute; bottom:100%; left:50%; margin-left:-80px; width:160px">MySQL database is running.</span>
                    <i class="fas fa-check-circle"></i>
                <?php else : ?>
                    <span class="w3-text w3-small w3-round w3-brown w3-tag" style="position:absolute; bottom:100%; left:50%; margin-left:-100px; width:200px">MySQL database is NOT running.</span>
                    <i class="fas fa-times-circle"></i>
                <?php endif; ?>
            </span><br>
            WiFi Hotspot: <span class="w3-tooltip" style="display:inline-block; margin-left:22px">
                <?php if(filter_var(shell_exec("sudo docker inspect -f {{.State.Running}} wifi"),FILTER_VALIDATE_BOOLEAN)) : ?>
					<span class="w3-text w3-small w3-round w3-brown w3-tag"style="position:absolute; bottom:100%; left:50%; margin-left:-80px; width:160px">WiFi Hotspot is running.</span>
                    <i class="fas fa-check-circle"></i>
                <?php else : ?>
                    <span class="w3-text w3-small w3-round w3-brown w3-tag" style="position:absolute; bottom:100%; left:50%; margin-left:-100px; width:200px">WiFi Hotspot is NOT running.</span>
                    <i class="fas fa-times-circle"></i>
                <?php endif; ?>
            </span><br>
            VPN: <span class="w3-tooltip" style="display:inline-block; margin-left:87px">
                <?php if(filter_var(shell_exec("sudo docker inspect -f {{.State.Running}} vpn_tunnel"),FILTER_VALIDATE_BOOLEAN)) : ?>
					<span class="w3-text w3-small w3-round w3-brown w3-tag"style="position:absolute; bottom:100%; left:50%; margin-left:-80px; width:160px">VPN tunnel established.</span>
                    <i class="fas fa-check-circle"></i>
                <?php else : ?>
                    <span class="w3-text w3-small w3-round w3-brown w3-tag" style="position:absolute; bottom:100%; left:50%; margin-left:-100px; width:200px">VPN tunnel NOT established.</span>
                    <i class="fas fa-times-circle"></i>
                <?php endif; ?>
            </span>
            </div>
        </div>
        <div class="w3-panel w3-green w3-round w3-padding" style="margin-right:8px;margin-left:8px">            
        <?php for ($i=0; $i<$GLOBALS["num_rec"]; $i++): ?>
                <button type="button" onclick="myAccordion('rec<?=$i?>_status')" class="w3-button w3-green w3-block w3-left-align">Logger <?=$i?> <b><?php echo $config['logger']['antenna_id_'.$i]?></b>: <?php if(filter_var(shell_exec("sudo docker inspect -f {{.State.Running}} logger-sdr-d".$i), FILTER_VALIDATE_BOOLEAN)) echo "running"; else echo "not running"; ?></button>
                <div id="rec<?=$i?>_status" class="w3-container w3-hide" style="margin-left:15px">
                    Frequency: <span style="margin-left:10px"><?php echo ($config['logger']['center_freq_'.$i]/1000000)?> MHz
                    <br>
                    Range: <span style="margin-left:41px"><?php echo ($config['logger']['center_freq_'.$i]-$config['logger']['freq_range_'.$i]/2)/1000000?> MHz to <?php echo ($config['logger']['center_freq_'.$i]+$config['logger']['freq_range_'.$i]/2)/1000000?> MHz</span>
                    <br>
                    Gain: <span style="margin-left:54px"><?php echo $config['logger']['log_gain_'.$i]?> dB</span>
                    <br>
                    Threshold: <span style="margin-left:14px"><?php echo $config['logger']['threshold_'.$i]?> dB above Noise</span>
                    <br>
                    Duration: <span style="margin-left:23px"><?php echo $config['logger']['minDuration_'.$i]." - ".$config['logger']['maxDuration_'.$i]?> sec</span>
                    <br>
                    MySQL: <span style="margin-left:36px"><?php if($config['logger']['use_sql_'.$i]=="Yes") :?>Writing to database at <?php echo $config['database']['db_host'];?>
                               <?php else:?> Not writing to database.<?php endif;?></span>
                    <br>
                    Timer: <span style="margin-left:45px"><?php echo (
                        $config['logger']['timer_start_'.$i]!="start_no" ? "Start ".(
                            $config['logger']['timer_mode_'.$i]=="freq_range" ? "range" : (
                                $config['logger']['timer_mode_'.$i]=="single_freq" ? "single frequency" : "" 
                            )
                        )." logger at ".(
                            $config['logger']['timer_start_'.$i] == "start_time" ? $config['logger']['timer_start_time_'.$i] : "boot"
                        ) : "No autostart"
                    )."  -  ".(
                        $config['logger']['timer_stop_'.$i]=="stop_time" ? "Stop logger at ".$config['logger']['timer_stop_time_'.$i] : "No autostop"                        
                    ) ?></span>
                </div>
            <?php endfor;?>
        </div>
        <div class="w3-panel w3-green w3-round w3-padding" style="margin-right:8px;margin-left:8px">
            Apps currently running:<br>
            <div style="margin-left:20px">
            <pre><?php system("sudo docker ps --filter \"status=running\" --format \"table {{.Names}}\t{{.Image}}\"");?></pre>
            </div>
        </div>
    </div>
</div>

<div id="features" class="city w3-mobile" style="display:none">
    <div class= "w3-row-padding">
        <div class="w3-panel w3-green w3-round w3-padding" style="margin-right:8px;margin-left:8px">
            <h2>Version 3.0 aka Nyctalus</h2>
            <p>
            <h4>Radio signal logging</h4>
            ->   on a 250 kHz/1 MHz frequency range or on single frequency (higher detection range)<br>
            ->   Output is saved as .csv file or in a MySQL Database<br>
            ->   Up to two receivers are supported<br>
            <h4>Browser-Spectrum-Viewer</h4>
            ->   Visual feedback of the spectrum<br>
            ->   Demodulation<br>
            ->   To check for noise sources<br>
            <h4>SDR# support</h4>
            ->   Monitor a Frequency Range of up to 2MHz<br>
            ->   Handy
            <h4>Remote Access</h4>
            ->   Using 2G/3G/4G Hotspots<br>
            ->   VPN-Certificate is required (paid feature)<br>
            <h4>WiFi</h4>
            ->   Can create own hotspot for access<br>
            ->   Login to an external Hotspot<br>
            <h4>Camera</h4>
            ->   With motion detection and IR-Lights switch<br>
            <h4>Software Handling</h4>
            ->   Global Setup-File to easily duplicate settings<br>
            ->   Update via WiFi or LAN - stable and development version<br>
            <br><br>
            List of features still in development:<br><br>
            - Automated Microphone Recordings<br>
            - GPS Logging<br>
            -> get GPS from Cell Phone<br>
            </p>
        </div>
	</div>
</div>

<div id="pdf_manuals" class="city w3-mobile" style="display:none">
    <div class= "w3-row-padding">
        <div class="w3-panel w3-green w3-round" style="margin-right:8px;margin-left:8px">
            <a target="_blank" href="/instructions/radiotrackingeu_basic_setup.pdf"><h4>Basic Setup Instructions</h4></a><br>
            to be continued...  <br><br>
        </div>
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
 
</body>
</html>
