<?php

define("NOTIFICATIONS_FILE", "notifications.json");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Load existing notification data from the file
$notifications = file_exists(NOTIFICATIONS_FILE) ? json_decode(file_get_contents(NOTIFICATIONS_FILE), true) : [];

function sendEmail($to, $subject, $body, $cc = "", $bcc = "") {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'MocBookPrint@moc.gov.qa';
        $mail->Password   = '@mMoBoPri@@345#$%';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('MocBookPrint@moc.gov.qa', 'MOC Book Printing');
        $mail->addAddress($to);
        if (!empty($cc)) {
            $mail->addCC($cc);
        }
        if (!empty($bcc)) {
            $mail->addBCC($bcc);
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $bookTitle = $_POST['book_title'];
    $author = $_POST['author'];
    $downloadUrl = $_POST['download_url'];
    $bcode = $_POST['bookCode'];
    $timestamp = $_POST['timestamp'];

    // Prepare the email content for the user
    $userSubject = "Book Request Confirmation";
    $userMessage = "
        <html>
        <head>
            <title>Book Request Confirmation</title>
        </head>
        <body>
            <p>Dear {$fullName},</p>
            <p>A new request to get the below book has been received from you and is under process:</p>
            <table border='1'>
                <tr><td>Book Title:</td><td>{$bookTitle}</td></tr>
                <tr><td>Author:</td><td>{$author}</td></tr>
            </table>
            <p>Thanks,<br>MOC Book Printing Admin</p>
        </body>
        </html>
    ";

    $userSuccess = sendEmail($email, $userSubject, $userMessage);

    // Prepare the email content for the admin
    $adminEmail = "admin@example.com";
    $ccEmails = "cc1@example.com, cc2@example.com";
    $bccEmails = "bcc1@example.com, bcc2@example.com";
    $adminSubject = "New Book Request Received";
    $adminMessage = "
        <html>
        <head>
            <title>New Book Request Received</title>
        </head>
        <body>
            <p>User has requested to print the following book:</p>
            <p><a href='{$downloadUrl}' class='btn btn-primary'>Download</a></p>
            <table border='1'>
                <tr><td>Full Name:</td><td>{$fullName}</td></tr>
                <tr><td>Email:</td><td>{$email}</td></tr>
                <tr><td>Phone:</td><td>{$phone}</td></tr>
                <tr><td>Book Title:</td><td>{$bookTitle}</td></tr>
                <tr><td>Author:</td><td>{$author}</td></tr>
                <tr><td>Download URL:</td><td><a href='{$downloadUrl}'>Download</a></td></tr>
                <tr><td>Timestamp:</td><td>{$timestamp}</td></tr>
            </table>
        </body>
        </html>
    ";

    $adminSuccess = sendEmail($adminEmail, $adminSubject, $adminMessage, $ccEmails, $bccEmails);

    // Update the notification data
    $newNotification = [
        "id" => count($notifications) + 1,
        "name" => $fullName,
        "email" => $email,
        "phone" => $phone,
        "title" => $bookTitle,
        "author" => $author,
        "download_url" => $downloadUrl,
        "timestamp" => $timestamp,
    ];

    $notifications[] = $newNotification;

    // Save the notifications to the file
    file_put_contents(NOTIFICATIONS_FILE, json_encode($notifications, JSON_PRETTY_PRINT));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">
    <style>
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 10px;
        }

        .header-section img {
            width: 50%;
            height: auto;
        }

        .header-section h2 {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="header-section">
        <img src="images/moc-logo-black.png" alt="Ministry of Culture">
        <h2>NOTIFICATIONS</h2>
    </div>

    <!-- DataTable -->
    <table id="notificationsTable" class="display">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Full Name</th>
                <th>Email Address</th>
                <th>Phone Number</th>
                <th>Book Code</th>
                <th>Book Title</th>
                <th>Author Name</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notifications as $notification): ?>
            <tr>
                <td><?php echo $notification["id"]; ?></td>
                <td><?php echo $notification["name"]; ?></td>
                <td><?php echo $notification["email"]; ?></td>
                <td><?php echo $notification["phone"]; ?></td>
                <td><?php echo $notification["bcode"]; ?></td>
                <td><?php echo $notification["title"]; ?></dt>
                <td><?php echo $notification["author"]; ?></td>
                <td><?php echo $notification["timestamp"]; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        $('#notificationsTable').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'collection',
                    text: 'Export',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Excel',
                            titleAttr: 'Export as Excel'
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV',
                            titleAttr: 'Export as CSV'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'PDF',
                            titleAttr: 'Export as PDF',
                            orientation: 'portrait',
                            pageSize: 'A4'
                        }
                    ]
                }
            ],
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    });
</script>


</body>
</html>
