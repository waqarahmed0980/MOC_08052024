<?php

// Define notifications file path
define("NOTIFICATIONS_FILE", "notifications.json");

// Load notifications from file or initialize as empty array
$notifications = json_decode(file_get_contents(NOTIFICATIONS_FILE), true) ?: [];

// Set the default time zone to Qatar
date_default_timezone_set('Asia/Qatar');

// Process POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $fullName = filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $bookTitle = filter_input(INPUT_POST, 'book_title', FILTER_SANITIZE_STRING);
    $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_STRING);
    $downloadUrl = filter_input(INPUT_POST, 'download_url', FILTER_SANITIZE_URL);
    $bookCode = filter_input(INPUT_POST, 'bookCode', FILTER_SANITIZE_STRING);
    $lang = filter_input(INPUT_POST, 'lang', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);
    $prefix = '+974';
    $phone_with_prefix = $prefix . $phone;

    // Validate input data
    if (!$fullName || !$email || !$bookTitle || !$author || !$downloadUrl || !$bookCode || !$lang || !$phone) {
        // Log invalid input
        error_log('Invalid input data: ' . json_encode($_POST));

        // Return error response for invalid input
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    // Save notification to file
    $currentTimestamp = time();
    $qatarTime = date("d M Y h:i A", $currentTimestamp);

    $newNotification = [
        "id" => count($notifications) + 1,
        "name" => $fullName,
        "email" => $email,
        "phone" => $phone_with_prefix,
        "file_no" => $bookCode,
        "title" => $bookTitle,
        "author" => $author,
        "download_url" => $downloadUrl,
        "timestamp" => $qatarTime,
        "lang" => $lang,
        "status" => "unread"
    ];

    $notifications[] = $newNotification;

    // Write to file with file locking to prevent race conditions
    $fileHandle = fopen(NOTIFICATIONS_FILE, 'w');
    if ($fileHandle === false) {
        // Log file write error
        error_log('Error opening file for writing: ' . NOTIFICATIONS_FILE);

        // Return error response for file write error
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error']);
        exit;
    }
    
    if (flock($fileHandle, LOCK_EX)) { // acquire exclusive lock
        fwrite($fileHandle, json_encode($notifications, JSON_PRETTY_PRINT));
        flock($fileHandle, LOCK_UN); // release lock
    } else {
        // Log file lock error
        error_log('Error acquiring file lock: ' . NOTIFICATIONS_FILE);

        // Return error response for file lock error
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error']);
        exit;
    }
    
    fclose($fileHandle);

    // Return success response
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    // Log invalid request method
    error_log('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);

    // Return error response for invalid request method
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}





?>
