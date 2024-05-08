<?php


// Set the default time zone to Qatar
date_default_timezone_set('Asia/Qatar');

// Get the current timestamp
$currentTimestamp = time();

// Format the timestamp as per the specified formatt
$qatarTime = date("l, j F, Y - h:i A", $currentTimestamp);


define("NOTIFICATIONS_FILE", "notifications.json");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$notifications = file_exists(NOTIFICATIONS_FILE) ? json_decode(file_get_contents(NOTIFICATIONS_FILE), true) : [];

function sendEmail($to, $subject, $body, $cc = "", $bcc = "", $includeDownload = false) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'MocBookPrint@moc.gov.qa';
        $mail->Password   = '@mMoBoPri@@345#$%';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('MocBookPrint@moc.gov.qa', 'MOC Book Printing');
        $mail->addAddress($to);
        if (!empty($cc)) $mail->addCC($cc);
        if (!empty($bcc)) $mail->addBCC($bcc);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
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
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $bookTitle = $_POST['book_title'];
    $author = $_POST['author'];
    $downloadUrl = $_POST['download_url'];
    $bookCode = $_POST['bookCode'];
    $timestamp = $qatarTime;

    $userSubject = "Book Request Confirmation";
    $userMessage = "<html><head><title>Book Request Confirmation</title></head><body><p>Dear {$fullName},</p><p>A new request to get the below book has been received from you and is under process:</p><table border='1'><tr><td>Book Title:</td><td>{$bookTitle}</td></tr><tr><td>Author:</td><td>{$author}</td></tr></table><p>Thanks,<br>MOC Book Printing Admin</p></body></html>";
    $userSuccess = sendEmail($email, $userSubject, $userMessage);

    $adminEmail = "mocbookprint@moc.gov.qa";
    $ccEmails = "Ashaikha@moc.gov.qa", "nalrahmany@moc.gov.qa", "hnasr@moc.gov.qa";
    $bccEmails = "wqahmed705@gmail.com", "waqar.ahmed@qdsnet.com", "syed.nabeel@qdsnet.com";
    $adminSubject = "New Book Request Received";
    $adminMessage = "<html><head><title>New Book Request Received</title></head><body><p>User has requested to print the following book:</p><p>File No: {$bookCode}</p><table border='1'><tr><td>Full Name:</td><td>{$fullName}</td></tr><tr><td>Email:</td><td>{$email}</td></tr><tr><td>Phone:</td><td>{$phone}</td></tr><tr><td>Book Title:</td><td>{$bookTitle}</td></tr><tr><td>Author:</td><td>{$author}</td></tr><tr><td>Download URL:</td><td><a href='{$downloadUrl}'>Download</a></td></tr><tr><td>Timestamp:</td><td>{$timestamp}</td></tr></table></body></html>";
    $adminSuccess = sendEmail($adminEmail, $adminSubject, $adminMessage, $ccEmails, $bccEmails, true);

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
            <?php foreach (array_reverse($notifications) as $notification): ?>
            <tr>
                <td><?php echo $notification["id"]; ?></td>
                <td><?php echo $notification["name"]; ?></td>
                <td><?php echo $notification["email"]; ?></td>
                <td><?php echo $notification["phone"]; ?></td>
                <td><?php echo $notification["file_no"]; ?></td>
                <td><?php echo $notification["title"]; ?></td>
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

        // Reload page every 5 seconds
        setTimeout(function(){
            window.location.reload();
        }, 5000);
    });
</script>

</body>
</html>
