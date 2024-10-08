<?php
// AMI connection details
$host = '127.0.0.1';    // Asterisk server IP
$port = 5038;           // AMI port
$username = 'ch8W0jBkmCsH';    // AMI username
$password = 'epK9rfpNwKbv';  // AMI password

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

$callers = [];

// Monitor events in a loop
while (!feof($socket)) {
    $line = fgets($socket, 4096);

    // Check if the event is a DTMF event
    if (strpos($line, 'Event: DTMFBegin') !== false) {
        $eventData = [];
        while (($eventLine = trim(fgets($socket, 4096))) !== "") {
            if (strpos(trim($eventLine), ':') !== strlen(trim($eventLine)) -1) {
                //echo "DTMF Event: " . $eventLine . "\n";
                list($key, $value) = explode(': ', $eventLine);
                $eventData[$key] = $value;
            }
            //list($key, $value) = explode(': ', $eventLine);
            //$eventData[$key] = $value;
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

    }
    
    // Check if the event is QueueCallerJoin
    if (strpos($line, 'Event: QueueCallerJoin') !== false) {
        $eventData = [];
        while (($eventLine = trim(fgets($socket, 4096))) !== "") {
            if (strpos(trim($eventLine), ':') !== strlen(trim($eventLine)) -1) {
                //echo "abc: " . $eventLine . "\n";
                list($key, $value) = explode(': ', $eventLine);
                $eventData[$key] = $value;
            }
        }

        // Get the callerid and callee
        $caller = $eventData['CallerIDNum'];
        $count = $eventData['Count'];
	    $linkedid = $eventData['Linkedid'];
	
	    $callers[$linkedid] = $caller;

	$queue = "";
	
	foreach ($callers as $key => $value) {
    		$queue .= $value . "\n";
	}

        $output = "Waiting: $count \n$queue\n";
        getdtmf($output);
        file_put_contents('/var/www/html/data.txt', $output);
    }


    // Check if the event is QueueCallerLeave
    if (strpos($line, 'Event: QueueCallerLeave') !== false) {
        $eventData = [];
        while (($eventLine = trim(fgets($socket, 4096))) !== "") {
            if (strpos(trim($eventLine), ':') !== strlen(trim($eventLine)) -1) {
                //echo "abc: " . $eventLine . "\n";
                list($key, $value) = explode(': ', $eventLine);
                $eventData[$key] = $value;
            }
        }

        // Get the callerid and callee
        $caller = $eventData['CallerIDNum'];
        $count = $eventData['Count'];
	$linkedid = $eventData['Linkedid'];
	
	unset($callers[$linkedid]);

	$queue = "";

	if ($count > 0) {
	    foreach ($callers as $key => $value) {
    		$queue .= $value . "\n";
	    }
	} else {
	    $queue = "";
	}     
        $output = "Waiting: $count \n$queue\n";
        getdtmf($output);
        file_put_contents('/var/www/html/data.txt', $output);
    }

    // Check if the event is AgentConnect
    if (strpos($line, 'Event: AgentConnect') !== false) {
        $eventData = [];
        while (($eventLine = trim(fgets($socket, 4096))) !== "") {
            if (strpos(trim($eventLine), ':') !== strlen(trim($eventLine)) -1) {
                //echo "abc: " . $eventLine . "\n";
                list($key, $value) = explode(': ', $eventLine);
                $eventData[$key] = $value;
            }
        }

        // Get the callerid and callee
        $caller = $eventData['CallerIDNum'];
        $callee = $eventData['Interface'];

	// Split by `/` and `@`
	$parts = explode('/', $callee); // Split at the `/`
	$callee = explode('@', $parts[1])[0]; // Further split at `@`
  
        //101 answered call from 44763636474
        $output = "$callee answered call from $caller\n";
        
        getdtmf($output);
    }
    
    if (strpos($line, 'Event: AgentComplete') !== false) {
        $eventData = [];
        while (($eventLine = trim(fgets($socket, 4096))) !== "") {
            if (strpos(trim($eventLine), ':') !== strlen(trim($eventLine)) -1) {
                //echo "abc: " . $eventLine . "\n";
                list($key, $value) = explode(': ', $eventLine);
                $eventData[$key] = $value;
            }
        }

	// Get the callerid and callee
        $caller = $eventData['CallerIDNum'];
        $callee = $eventData['Interface'];

	// Split by `/` and `@`
	$parts = explode('/', $callee); // Split at the `/`
	$callee = explode('@', $parts[1])[0]; // Further split at `@`        

        //101 finished on phone
        $output = "$callee finished on phone from $caller\n";
               
        getdtmf($output);
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
