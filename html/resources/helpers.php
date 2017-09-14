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
		foreach (confKeys as $key)
		{
			if ($config->has(confSection,$key) && isset($_POST[$key])) {
				// console_log('is set: '.$key);
				if (is_array($config[confSection][$key])){
					$value = $config[confSection][$key];
					$value[$config[confSection]['device']] = $_POST[$key];
				}
				else 
					$value = $_POST[$key];
				
				$config->set(confSection,$key,$value);
			}
		}
		$config->save();		
		// console_log('saved log for '.__FILE__);
	}
?>