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
<div class="w3-panel w3-green w3-round">
	<br>
	Start the software, if it isn't already running (just try by clicking on the link).
	<br><br>

	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="submit" class="w3-btn w3-brown" value="Start Audio Record" name="start_record_mic">

			<br><br>
	</form>
</div>
	
	<p>
	<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			
			if (isset($_POST["start_record_mic"])){
				echo '<pre>'; //Shit - wrong spelling of microphone (mircophone)
				$test = system("sudo docker run -t --rm --privileged -v /var/www/html/micro/:/tmp/ mircophone AUDIODEV=hw:1 rec -c1 -r 250000 /tmp/record.wav sinc 10000 silence 1 0.1 1% trim 0 5 2>&1", $ret);
				echo '</pre>';
			}

	?>
	</p>
<!-- Enter text here-->

<div id="container"></div>

<div class="w3-container w3-center w3-brown">
  Online-Website: <a href="https://radio-tracking.eu/">radio-tracking.eu</a>
  Email: <a href= "mailto:info@radio-tracking.eu">info@radio-tracking.eu</a>
</div>


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