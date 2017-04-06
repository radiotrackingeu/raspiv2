<?php
    echo '<pre>';
    $content = system('sudo docker images', $ret);
    echo '</pre>';
?>