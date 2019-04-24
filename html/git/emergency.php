<!DOCTYPE html>
<html>
<title><?php trim(system("hostname"))?></title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="/resources/weblib/w3.css">
<link rel="stylesheet" href="/resources/weblib/css/fontawesome-all.css">
<link rel="stylesheet" href="/resources/weblib/css/font-awesome.min.css">
<link rel="stylesheet" href="/resources/additional.css">

<body>
<div class="w3-cell-row">
	<div class="w3-container w3-cell w3-cell-right w3-large w3-green w3-mobile" style="width:20%">
		<br>
    <a href='../index.php'>
      <img src="/images/logo_rteu.png" align="left" alt="radio-tracking.eu" style="width:70%">
    </a>
	</div>
	<div class="w3-container w3-cell w3-cell-middle w3-large w3-green w3-mobile" style="width:80%">
		<br>
		<h1>radio-tracking.eu</h1>
	</div>	

</div>
<div id="GIT" class="w3-container w3-mobile city" style="display:block">
	<div class="w3-panel w3-green w3-round w3-padding">
		Update the User Interface - if a single Application has been updated - please go afterwards to Applications. Please also choose to keep your old config file or update it with standard settings.<br><br>
		<form method="POST" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>">
				<select name="git_checkout">
					<option value="master" >Stable Version</option>
					<option value="live" selected>Development Version</option>
				</select>
				<select name="git_keepcfg">
					<option value="updatecfg" selected>Update config file</option>
					<option value="keepcfg" >Keep old config file</option>
				</select>
				<select name="git_switch_system">
					<option value="build_none" SELECTED>No change in system</option>
					<option value="build_raspi3">Update for Raspberry Pi 3</option>
					<option value="build_raspi_zerow">Update for Raspberry Pi Zero W</option>
				</select>
			<input class="w3-btn w3-brown" type="submit" value="Update User Interface" name="update_rep"/>
		</form>
    <hr>
    Checkout from local repository - This allows you to reset the user interface without internet connectivity.
    <form method="POST" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>">
      <select name="git_branch">
        <?php exec('git -C /home/pi/gitrep/raspiv2 branch',$branches);?>
        <?php foreach ($branches as $b):?>
          <option value='<?=$b?>'><?=$b?></option>
        <?php endforeach; ?>
      </select>
      <input class="w3-btn w3-brown" type="submit" value="Reset from local repository" name="copy_rep"/>
    </form>
		<br>
	</div>
</div>


<div id="output_php" class="w3-modal">
  <div class="w3-modal-content" style="width: 90%">
    <div class="w3-container w3-blue">
      <span onclick="document.getElementById('output_php').style.display='none'" class="w3-button w3-display-topright">&times;</span>


<?php
  function start_docker($docker_cmd,$block_to_jump){
    echo "<script type='text/javascript'>document.getElementById('output_php').style.display='block';</script>";
    echo '<pre>';
    system($docker_cmd, $ret);
    echo '</pre>';
    echo "<script type='text/javascript'>document.getElementById('".$block_to_jump."').style.display = 'block';</script>";
  }

//git update
  if (isset($_POST["update_rep"])){
    $cmd='sudo docker run --rm -t -v /home/pi/gitrep/:/home/pi/gitrep/ -v /var/www/html/:/var/www/html/ --net="host" git:1.0 sh /home/pi/gitrep/raspiv2/Docker/gitlab/update_html.sh '.$_POST["git_checkout"].' '.$_POST["git_keepcfg"].' 2>&1';
    start_docker($cmd,'GIT');
  }
  //git reset
  if (isset($_POST["copy_rep"])){
    $cmd='sudo docker run --rm -t -v /home/pi/gitrep/:/home/pi/gitrep/ -v /var/www/html/:/var/www/html/ git:1.0 sh /home/pi/gitrep/raspiv2/Docker/gitlab/copy_rep.sh '.str_replace(array("* ","  "),"",$_POST["git_branch"]).' 2>&1';
    start_docker($cmd,'GIT');
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

<br>
<div class="w3-container w3-brown w3-middle">
	<h3>
    Online-Website: <a href="https://radio-tracking.eu/">radio-tracking.eu</a>
    Email: <a href= "mailto:info@radio-tracking.eu">info@radio-tracking.eu</a>
	</h3>
</div>

</body>
</html>
