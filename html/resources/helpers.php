<?php
	//for debugging purposes
	function console_log( $data ){
	  echo '<script>';
	  echo 'console.log('. json_encode( $data ) .')';
	  echo '</script>';
	}
	
	//Assign currently entered values to config and write config to file.
	function update_Config(&$config) {
		// $config->read();
		// var_dump($config);
		foreach (confKeys as $value)
		{
			if ($config->has(confSection,$value) && isset($_POST[$value])) {
				// console_log('is set: '.$value);
				$config->set(confSection,$value,$_POST[$value]);
			}
		}
		$config->save();		
	}
?>