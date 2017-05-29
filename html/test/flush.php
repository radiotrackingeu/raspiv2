<?php

   header("Content-Encoding: none");

    // I think maybe you can set output_buffering using ini_set here, but I'm not sure.
    // It didn't work for me the first time at least, but now it does sometimes...
    // So I set output_buffering to Off in my php.ini,
    // which normally, on Linux, you can find at the following location: /etc/php5/apache2/php.ini

    @ini_set('output_buffering','Off');
    @ini_set('zlib.output_compression',Off);
    @ini_set('implicit_flush',1);
    @ini_set('output_handler', '');
    @apache_setenv('no-gzip', 1);	
    @ob_end_clean();
    set_time_limit(0);
    apache_setenv('no-gzip', '1');

ob_start();

    //echo str_repeat('        ',1024*8); //<-- For some reason it now even works without this, in Firefox at least?


    for ($i = 0; $i<10; $i++){

        echo "<br> Line to show.";
        echo str_pad('',4096)."\n";    

        ob_flush();
        flush();
        sleep(1);
    }

    echo "Done.";

    ob_end_flush();


?>