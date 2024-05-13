<?php

// Define notifications file path
define("NOTIFICATIONS_FILE", "notifications.json");

// Check if ID and status are provided in the request
if (isset($_POST['id']) && isset($_POST['status'])) {
    // Retrieve ID and status from the request
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Load notifications from file
    $notifications = json_decode(file_get_contents(NOTIFICATIONS_FILE), true) ?: [];

    // Find the notification with the given ID
    $notificationKey = array_search($id, array_column($notifications, 'id'));

    // If notification found, update its status
    if ($notificationKey !== false) {
        $notifications[$notificationKey]['status'] = $status;

        // Save updated notifications to file
        file_put_contents(NOTIFICATIONS_FILE, json_encode($notifications, JSON_PRETTY_PRINT));

        // Return success message and updated status
        echo json_encode(['status' => 'success', 'updated_status' => $status]);
    } else {
        // Return error message if notification ID not found
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => "Notification not found with ID: $id"]);
    }
} else {
    // Return error message if ID or status not provided
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => "ID or status not provided in the request."]);
}

?>
