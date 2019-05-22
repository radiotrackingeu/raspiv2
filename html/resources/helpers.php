<?php
    //number of receivers connected
    $GLOBALS["num_rec"] = exec("lsusb | grep -c -e '0bda:2838' -e '0bda:2832'");
	
    //for debugging purposes
	function console_log( $data ){
	  echo '<script>';
	  echo 'console.log('. json_encode( $data ) .')';
	  echo '</script>';
	}
	
	//Assign currently entered values to config and write config to file.
	function update_Config(&$config) {
		foreach (confKeys as $key)
		{
			if ($config->has(confSection,$key) && isset($_POST[$key])) {
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
	}
    
    //prepend string to a textfile
    function prepend($string, $orig_filename) {
        $context = stream_context_create();
        $orig_file = fopen($orig_filename, 'r', 1, $context);

        $temp_filename = tempnam(sys_get_temp_dir(), 'php_prepend_');
        file_put_contents($temp_filename, $string);
        file_put_contents($temp_filename, $orig_file, FILE_APPEND);

        fclose($orig_file);
        unlink($orig_filename);
        rename($temp_filename, $orig_filename);
}
    
    function check_device_use($id) {
        return filter_var(shell_exec("sudo docker ps -q --filter name=sdr-d".$id." | wc -l"), FILTER_VALIDATE_BOOLEAN);
    }
    
    function check_container_runnning($container_name) {
        $ret_val=0;
        unset($ret_arr);
        exec("sudo docker inspect -f {{.State.Running}} ".$container_name,$ret_arr, $ret_val);
        error_log(print_r($ret_arr));
        if ($ret_val!=0)
            return FALSE;
        else
            return filter_var($ret_arr[0], FILTER_VALIDATE_BOOLEAN);
    }
    
    function check_image_exists($name) {
        $ret_val = 0;
        system("sudo docker inspect ".$name." >/dev/null 2>&1", $ret_val);
        return $ret_val==0;
    }
?>

