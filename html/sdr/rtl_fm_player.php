<html>

<title><?php trim(system("hostname"));?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="/resources/weblib/w3.css">
<link rel="stylesheet" href="/resources/weblib/css/font-awesome.min.css">
<link rel="stylesheet" href="/resources/additional.css">

<body>

<div class="w3-cell-row">
	<div class="w3-container w3-cell w3-cell-right w3-large w3-green w3-mobile w3-padding-16" style="overflow:hidden; width:20%">
		<img src="/images/logo_rteu.png" align="left" alt="radio-tracking.eu" style="width:70%">
	</div>
	<div class="w3-container w3-cell w3-cell-middle w3-large w3-green w3-mobile" style="width:80%">
		<br>
		<h1>radio-tracking.eu</h1>
	</div>	

</div>

<div class="w3-row row-padding w3-container">
	<div class="w3-panel w3-green w3-round w3-padding-24" >
		<audio controls autoplay preload="none"><source src='<?php echo "http://".$_SERVER['SERVER_NAME'].":".($_SERVER['SERVER_PORT']+1+$_POST['dev'])?>' type='audio/mpeg'>Your browser does not support the audio element.</audio>
		<form method="post" enctype="multipart/form-data" style="padding-left:20px; display:inline-block; vertical-align:top" onsubmit="return closeSelf(this);">
			<input type="submit" class="w3-btn w3-brown" value="Stop" name="rtl_stop_<?php echo $_POST['dev']; ?>"/>
		</form>

	</div>



</div>
<script type="text/javascript">
	function closeSelf(f) {
		f.submit();
		window.close();
	}
</script>
<?php
	//load config
	require_once '../cfg/baseConfig.php';
	//load ConfigLite
	require_once CONFIGLITE_PATH.'/Lite.php';
	//load values from config
	$config = new Config_Lite(CONFIGFILES_PATH.'/globalconfig');
	//load footer
	require_once RESOURCES_PATH.'/footer.php';
	//load php_scripts
	require_once RESOURCES_PATH.'/php_scripts.php';
	?>