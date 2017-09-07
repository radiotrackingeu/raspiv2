<div id="output_php" class="w3-modal">
	<div class="w3-modal-content">
		<div class="w3-container w3-blue">
			<span onclick="document.getElementById('output_php').style.display='none'" class="w3-button w3-display-topright">&times;</span>
					

<?php
	
	if (isset($_POST["zip_camera"])){
		$cmd="sudo docker run -t --rm --privileged -v /var/www/html/picam/:/tmp/ git zip -r /tmp/zipped/".$_POST["zip__camera_name"]." /tmp/record/ 2>&1";
		start_docker($cmd,'camera_data');
		}
		/*
	if (isset($_POST["zip_camera"])){
		echo "<script type='text/javascript'>document.getElementById('output_php').style.display='block';</script>";
		echo '<pre>';
		$test = system("sudo docker run -t --rm --privileged -v /var/www/html/picam/:/tmp/ git zip -r /tmp/zipped/".$_POST["zip__camera_name"]." /tmp/record/ 2>&1", $ret);
		echo '</pre>';
	}*/
	function start_docker($docker_cmd,$block_to_jump){
		echo "<script type='text/javascript'>document.getElementById('output_php').style.display='block';</script>";
		echo '<pre>';
		$test = system($docker_cmd, $ret);
		echo '</pre>';
		echo "<script type='text/javascript'>document.getElementById(".$block_to_jump.").style.display = 'block';</script>";		
	}
?>

		</div>
	</div>
</div>