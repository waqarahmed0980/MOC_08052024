



<?php

define("NOTIFICATIONS_FILE", "notifications.json");

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set the default time zone to Qatar
date_default_timezone_set('Asia/Qatar');

// Get the current timestamp
$currentTimestamp = time();

// Format the timestamp as per the specified format
$qatarTime = date("l, j F, Y - h:i A", $currentTimestamp);

$notifications = file_exists(NOTIFICATIONS_FILE) ? json_decode(file_get_contents(NOTIFICATIONS_FILE), true) : [];

function sendEmail($to, $subject, $body, $cc = "", $bcc = "") {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';  // Support for Unicode characters
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'MocBookPrint@moc.gov.qa';
        $mail->Password   = '@mMoBoPri@@345#$%';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('MocBookPrint@moc.gov.qa', 'MOC Book Printing');
        $mail->addAddress($to);
        if (!empty($cc)) {
            foreach (explode(',', $cc) as $ccEmail) {
                $mail->addCC(trim($ccEmail));
            }
        }
        if (!empty($bcc)) {
            foreach (explode(',', $bcc) as $bccEmail) {
                $mail->addBCC(trim($bccEmail));
            }
        }
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail send failed: " . $e->getMessage());
        return false;
    }
}

function formatEmail($fullName, $bookTitle, $author) {
    return <<<HTML
    <html>
    <head>
    <title>Notification</title>
    <style>
        body { font-family: 'Arial', sans-serif; direction: rtl; text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
    </head>
    <body>
    <p>Dear {$fullName},</p>
    <p>Below are the details of the book request:</p>
    <table>
        <tr><th>Book Title</th><td>{$bookTitle}</td></tr>
        <tr><th>Author</th><td>{$author}</td></tr>
    </table>
    <p>Thanks,<br>MOC Book Printing Admin</p>
    </body>
    </html>
HTML;
}



function sendSMS($phone, $message) {
    $url = "https://messaging.ooredoo.qa/bms/soap/Messenger.asmx/HTTP_SendSms";
    $params = http_build_query([
        'customerID' => '1465',
        'userName' => 'qauthor',
        'userPassword' => 'sT@4147uiy',
        'originator' => 'MOC',
        'smsText' => $message,
        'recipientPhone' => $phone,
        'messageType' => 'ArabicWithLatinNumbers',
        'defDate' => '',
        'blink' => 'false',
        'flash' => 'false',
        'Private' => 'false'
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = htmlspecialchars($_POST['fullName']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST['phone']);
    $bookTitle = htmlspecialchars($_POST['book_title']);
    $author = htmlspecialchars($_POST['author']);
    $downloadUrl = filter_var($_POST['download_url'], FILTER_SANITIZE_URL);
    $bookCode = htmlspecialchars($_POST['bookCode']);

    $userMessage = formatEmail($fullName, $bookTitle, $author);
    $userSuccess = sendEmail($email, "Book Request Confirmation", $userMessage);

    $adminEmail = "mocbookprint@moc.gov.qa";
    $ccEmails = "nalrahmany@moc.gov.qa,hnasr@moc.gov.qa,ashaikha@moc.gov.qa";
    $bccEmails = "wqahmed705@gmail.com,waqar.ahmed@qdsnet.com";

    $adminMessage = formatEmail($fullName, $bookTitle, $author);
    $adminSuccess = sendEmail($adminEmail, "New Book Request Received", $adminMessage, $ccEmails, $bccEmails);

    $smsMessage = "Thank you, {$fullName}, for your request '{$bookTitle}'. It is being processed.";
    sendSMS($phone, $smsMessage);

    // Send SMS
    $smsMessage = "Thank you, {$fullName}, for your request '{$bookTitle}'. It is being processed.";
    sendSMS($phone, $smsMessage);

    $newNotification = [
        "id" => count($notifications) + 1,
        "name" => $fullName,
        "email" => $email,
        "phone" => $phone,
        "file_no" => $bookCode,
        "title" => $bookTitle,
        "author" => $author,
        "download_url" => $downloadUrl,
        "timestamp" => $timestamp
    ];


// Save notification
    $newNotification = [
        "id" => count($notifications) + 1,
        "name" => $fullName,
        "email" => $email,
        "phone" => $phone,
        "file_no" => $bookCode,
        "title" => $bookTitle,
        "author" => $author,
        "download_url" => $downloadUrl,
        "timestamp" => $qatarTime
    ];

    $notifications[] = $newNotification;
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <style>
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 10px;
        }
        .header-section img {
            width: 30%;  /* Reduced logo size */
            height: auto;
        }
        .header-section h2 {
            font-weight: bold;
        }
        /* Additional spacing below the DataTable */
        #notificationsTable_wrapper {
            margin-bottom: 50px;
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
                <th>File No.</th>
                <th>Book Title</th>
                <th>Author Name</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($notifications as $notification) {

    // $currentFormattedTime = $notification["timestamp"];

    // $timestamp = strtotime($currentFormattedTime);

    // if (!$timestamp) {
    //     $sortableDate = "0000-00-00 00:00:00"; 
    //     $qatarTime = "Invalid date format"; 
    // } else {
 
    //     $sortableDate = date('Y-m-d H:i:s', $timestamp);
    //     $qatarTime = $currentFormattedTime; 
    // }

   
    echo '<tr>';
    echo '<td>' . $notification["id"] . '</td>';
    echo '<td>' . htmlspecialchars($notification["name"]) . '</td>';
    echo '<td>' . htmlspecialchars($notification["email"]) . '</td>';
    echo '<td>' . htmlspecialchars($notification["phone"]) . '</td>';
    echo '<td>' . htmlspecialchars($notification["file_no"]) . '</td>';
    echo '<td>' . htmlspecialchars($notification["title"]) . '</td>';
    echo '<td>' . htmlspecialchars($notification["author"]) . '</td>';
    echo '<td>' . htmlspecialchars($notification["timestamp"]) . '</td>';
    echo '</tr>';
}


 ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#notificationsTable').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            order: [[7, 'DESC']], // Order by Timestamp descending
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
                        }
                        // {
                        //     extend: 'csvHtml5',
                        //     text: 'CSV',
                        //     titleAttr: 'Export as CSV'
                        // },
                        // {
                        //     extend: 'pdfHtml5',
                        //     text: 'PDF',
                        //     titleAttr: 'Export as PDF',
                        //     orientation: 'portrait',
                        //     pageSize: 'A4'
                        // }
                    ]
                }
            ],
            columnDefs: [
                { targets: 7, type: 'date' }
            ]
        });
         // Reload page every 10 seconds
        setTimeout(function(){
            window.location.reload();
        }, 10000);
    });

</script>

</body>
</html>
