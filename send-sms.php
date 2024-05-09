<?php

// Function to send SMS using Ooredoo Qatar messaging API
function sendSMS($phone, $message) {
    $url = "http://messaging.ooredoo.qa/bms/soap/Messenger.asmx/HTTP_SendSms";
    $params = [
        'customerID' => '1465',
        'userName' => 'qauthor',
        'userPassword' => 'sT@4147uiy',
        'originator' => 'MOC',
        'smsText' => $message,
        'recipientPhone' => $phone,
        'messageType' => 'ArabicWithLatinNumbers',
        'defDate' => date('YmdHis'),
        'blink' => 'false',
        'flash' => 'false',
        'Private' => 'false'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true); // Include header in output
    curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $response = 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $response;
}
