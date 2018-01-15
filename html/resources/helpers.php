<?php
	//for debugging purposes
	function console_log( $data ){
	  echo '<script>';
	  echo 'console.log('. json_encode( $data ) .')';
	  echo '</script>';
	}
	
	//Assign currently entered values to config and write config to file.
	function update_Config(&$config,$device=0) {
		foreach (confKeys as $key)
		{
			if ($config->has(confSection,$key) && isset($_POST[$key])) {
				if (is_array($config[confSection][$key])){
					$value = $config[confSection][$key];
					$value[$device] = $_POST[$key];
				}
				else 
					$value = $_POST[$key];
				$config->set(confSection,$key,$value);
			}
		}
		$config->save();		
	}
?>

