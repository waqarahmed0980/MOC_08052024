<?php

// Load notifications from file
$notifications = json_decode(file_get_contents(NOTIFICATIONS_FILE), true) ?: [];

// Check if notification ID and status are provided
if (isset($_POST['notification_id']) && isset($_POST['status'])) {
    $notificationId = $_POST['notification_id'];
    $status = $_POST['status']; // 'read' or 'unread'

    // Find the notification in the array and update its status
    foreach ($notifications as &$notification) {
        if ($notification['id'] == $notificationId) {
            $notification['status'] = $status;
            break;
        }
    }

    // Save the updated notifications back to the file
    file_put_contents(NOTIFICATIONS_FILE, json_encode($notifications, JSON_PRETTY_PRINT));

    // Send a response indicating success
    echo json_encode(['success' => true]);
} else {
    // Send a response indicating failure if parameters are missing
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
}
?>
