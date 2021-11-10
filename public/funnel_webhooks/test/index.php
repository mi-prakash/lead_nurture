<?php 

    $json_data = file_get_contents('php://input');

    $myfile = fopen("clickfunnel.txt", "a") or die("Unable to open file!");
    $txt = $json_data."\n";
    fwrite($myfile, $txt);
    fclose($myfile);
    echo "success";