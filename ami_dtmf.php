<?php
// AMI connection details
$host = '127.0.0.1';    // Asterisk server IP
$port = 5038;           // AMI port
$username = 'admin';    // AMI username
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

        // Get the DTMF digit
        $digit = $eventData['Digit'];
        $context = $eventData['Context'];

        // Replace '#' with 'hash'
        if ($digit == "#") {
            $digit = "hash";
        }

        // Construct the output in the desired format
        $callerID = $eventData['CallerIDNum'];
        $output = $callerID . " pressed " . $digit;

        if ($context == "ivr-1") {
            $output = $callerID . " pressed " . $digit . " in IVR menu";
        }

        // Call the function with the new format
        getdtmf($output);

        // Print the output (optional)
        echo $output . "\n";
    }
}

// Logout and close the socket
fputs($socket, "Action: Logoff\r\n");
fclose($socket);

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
