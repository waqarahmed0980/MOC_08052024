<?php

// Set CORS headers for incoming requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Set security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self'");

// Define country code prefix
$prefix = '+974';

// Function to send SMS
function sendSMS($phone_with_prefix, $message, $apiPassword) {
    // API endpoint URL
    $url = "https://messaging.ooredoo.qa/bms/soap/Messenger.asmx/HTTP_SendSms";

    // Parameters for the API request
    $params = http_build_query([
        'customerID' => '1465',
        'userName' => 'qauthor',
        'userPassword' => $apiPassword,
        'originator' => 'MOC',
        'smsText' => $message,
        'recipientPhone' => $phone_with_prefix,
        'messageType' => 'ArabicWithLatinNumbers',
        'defDate' => date('YmdHis'),
        'blink' => 'false',
        'flash' => 'false',
        'Private' => 'false'
    ]);

    // Initialize cURL session
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Log the response
    error_log("API Response: $response");

    // Check for errors
    if(curl_errno($ch)) {
        $error_message = curl_error($ch);
        // Log the error
        error_log("cURL Error: $error_message");
        // Retry without SSL if SSL verification failed
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        // Log the response after retry
        error_log("API Response (Retry without SSL): $response");
        // Check for errors after retry
        if(curl_errno($ch)) {
            $error_message = curl_error($ch);
            // Log the error
            error_log("cURL Error (Retry without SSL): $error_message");
            // Return false to indicate failure
            return false;
        }
    }

    // Close cURL session
    curl_close($ch);

    // Return the API response
    return $response;
}

// Function to handle successful SMS sending
function handleSuccess($response) {
    http_response_code(200); // OK
    
    // If the response is empty, just log success
    if (empty($response)) {
        logError("SMS Sent Successfully");
        logError("No response received from API");
        exit;
    }
    
    // If the response is already in XML format, echo it as is
    if (is_string($response)) {
        // Send XML response from the API
        header('Content-Type: application/xml');
        echo $response;

        // Log success and response
        logError("SMS Sent Successfully");
        logError("API Response: " . htmlentities($response));
        exit;
    } else {
        // Convert array to XML format
        $xml = new SimpleXMLElement('<root/>');
        array_walk_recursive($response, function($value, $key) use ($xml) {
            $xml->addChild($key, $value);
        });
        
        // Send XML response
        header('Content-Type: application/xml');
        echo $xml->asXML();

        // Log success and response
        logError("SMS Sent Successfully");
        logError("API Response: " . print_r($response, true));
        logError("Received XML Response from API: " . htmlentities($xml->asXML()));

        exit;
    }
}



// Function to handle SMS sending failure
function handleFailure($response, $apiPassword) {
    http_response_code(500); // Internal Server Error
    $error_message = 'Failed to send SMS';
    if (empty($response)) {
        $error_message .= ': Empty response from API';
    } else {
        $error_message .= ': ' . $response;
    }
    // Log the error
    logError($error_message);
    // Retry sending SMS without SSL
    $response = sendSMS($phone_with_prefix, $message, $apiPassword);
    if ($response !== false) {
        handleSuccess($response);
    } else {
        // Log the retry failure
        logError("Retry failed to send SMS");
        // Return error response
        echo json_encode(['error' => 'Failed to send SMS. Please try again later.']);
        exit;
    }
}

// Handle incoming request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input parameters
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $lang = isset($_POST['lang']) ? trim($_POST['lang']) : '';

    // Validate phone number format and length
    if (!preg_match('/^\d{8}$/', $phone)) {
        http_response_code(400); // Bad request
        echo json_encode(['error' => 'Invalid phone number format']);
        exit;
    }

    // Add country code prefix to the phone number
    $phone_with_prefix = $prefix . $phone;

    // Determine message based on language
    $messageEn = "Thank you for your book printing request. It is being processed.";
    $messageAr = "شكرا لك على طلبك لطباعة الكتاب. يتم المعالجة الآن.";
    $message = ($lang == "en") ? $messageEn : $messageAr;

    // Define API password (replace with your actual password)
    $apiPassword = 'sT@4147uiy';

    // Send SMS
    $response = sendSMS($phone_with_prefix, $message, $apiPassword);

    // Check if SMS was sent successfully
    if ($response !== false) {
        handleSuccess($response);
    } else {
        handleFailure($response, $apiPassword);
    }
} else {
    // Handle unsupported request method
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

?>
