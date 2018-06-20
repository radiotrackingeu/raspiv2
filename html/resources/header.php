<div class="w3-cell-row">
	<div class="w3-container w3-cell w3-cell-right w3-large w3-green w3-mobile" style="width:20%">
		<br>
		<img src="/images/logo_rteu.png" align="left" alt="radio-tracking.eu" style="width:70%">
	</div>
	<div class="w3-container w3-cell w3-cell-middle w3-large w3-green w3-mobile" style="width:80%">
		<br>
		<h1>radio-tracking.eu</h1>
	</div>	

</div>
<div class="w3-cell-row">
	<div class="w3-container w3-cell w3-large  w3-green w3-mobile">
		<button class="w3-button w3-green w3-round-xxlarge w3-hover-red w3-xlarge" onclick="w3_switch('sidebar')"><i class="fa fa-bars" aria-hidden="true">  Menu</i></button>
	</div>
 </div>

<div class="w3-bar w3-light-grey" style="display:none" id="sidebar">
	<!-- Home -->
	<a class="w3-bar-item w3-button w3-mobile" href="/index.php"><i class="fa fa-home"></i> Home</a>
	
	<!-- Radio -->
	<div class="w3-dropdown-hover w3-mobile">
		<button class="w3-button">
			<i class="fa fa-podcast"></i> Radio <i class="fa fa-caret-down"></i>
		</button>
		<div id="radio" class="w3-dropdown-content w3-card-4">
			<a href="/sdr/rtl_fftw.php">Logger</a>
			<a href="/sdr/websdr.php">Spectrogram</a>
			<a href="/sdr/rtl_fm.php">WebRadio</a>
			<a href="/sdr/rtl_tcp.php">SDR#-Server</a>
		</div>
	</div>

	<!-- Camera -->
	<div class="w3-dropdown-hover w3-mobile">
		<a class="w3-bar-item w3-button w3-mobile" href="/picam/picam.php"><i class="fa fa-camera"></i> Camera</a>
	</div>

	<!-- Microphone -->
	<div class="w3-dropdown-hover w3-mobile">
		<a class="w3-bar-item w3-button w3-mobile" href="/micro/micro.php"><i class="fa fa-microphone"></i> Microphone</a>
	</div>
	
	<!-- GPS 
	<div class="w3-dropdown-hover w3-mobile">
		<button class="w3-button">
			<i class="fa fa-compass"></i> GPS <i class="fa fa-caret-down"></i>
		</button>
		<div id="gps" class="w3-dropdown-content w3-card-4">
			<a href="/gps/gps.php">Start</a>
			<a href="/gps/gps_setup.php">Setup</a>
		</div>
	</div>
		-->
	
	<!-- Data storage -->
	<div class="w3-dropdown-hover w3-mobile">
		<a class="w3-bar-item w3-button w3-mobile" href="/data/data.php"><i class="fa fa-database "></i> Data</a>
	</div>
	
	<!-- WiFi -->
	<div class="w3-dropdown-hover w3-mobile">
		<a class="w3-bar-item w3-button w3-mobile" href="/wifi/wifi.php"><i class="fa fa-wifi"></i> WiFi</a>
	</div>
		
	<!-- Remote control -->
	<div class="w3-dropdown-hover w3-mobile">
		<a class="w3-bar-item w3-button w3-mobile" href="/connect/connect.php"><i class="fa fa-exchange"></i> Remote</a>
	</div>
	
	<!-- System settings -->
	<div class="w3-dropdown-hover w3-mobile">
		<button class="w3-button">
			<i class="fa fa-wrench"></i> System <i class="fa fa-caret-down"></i>
		</button>
		<div id="system" class="w3-dropdown-content w3-card-4">
			<a href="/git/gitlab.php">Software</a>
			<a href="/git/system.php">System</a>
		</div>
	</div>
	
	<!-- License -->
	<a class="w3-bar-item w3-button w3-mobile" href="/license.php"><i class="fa fa-registered"></i> License</a>
</div>