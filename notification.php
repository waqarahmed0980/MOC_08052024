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
$qatarTime = date("d M Y h:i A", $currentTimestamp);

$notifications = json_decode(file_get_contents(NOTIFICATIONS_FILE), true) ?: [];


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

function formatEmailEn($fullName, $bookTitle, $author) {
    return <<<HTML
    <html>
    <head>
    <title>Notification</title>
    <style>
        body { font-family: sans-serif; direction: ltr; text-align: left; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
    </head>
    <body>
    <p>Dear {$fullName},</p>
    <p>Below are the details of the requested book </p>
    <table>
        <tr><th>Book Title</th><td>{$bookTitle}</td></tr>
        <tr><th>Author</th><td>{$author}</td></tr>
    </table>
    <p>Thanks,<br>MOC Book Printing Admin</p>
    </body>
    </html>
HTML;
}

function formatEmailAr($fullName, $bookTitle, $author) {
    return <<<HTML
    <html>
    <head>
    <title>إشعار</title>
    <style>
        body { font-family: sans-serif; direction: rtl; text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
    </head>
    <body>
    <p>السيد/ السيدة {$fullName},</p>
    <p>تم إرسال الكتاب للطباعة، تفاصيل الكتاب:</p>
    <table>
        <tr><th>عنوان الكتاب</th><td>{$bookTitle}</td></tr>
        <tr><th>اسم المؤلف</th><td>{$author}</td></tr>
    </table>
    <p>شكرًا,<br>شكرا لاستخدامكم خدمة طباعة الكتب</p>
    </body>
    </html>
HTML;
}

function formatEmailAdminEn($fullName, $bookTitle, $author) {
    return <<<HTML
    <html>
    <head>
    <title>Notification</title>
    <style>
        body { font-family: sans-serif; direction: ltr; text-align: left; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
    </head>
    <body>
    <p>User has requested Book Printing.</p>
    <p>Below are the details of the book requested:</p>
    <table>
        <tr><th>Book Title</th><td>{$bookTitle}</td></tr>
        <tr><th>Author</th><td>{$author}</td></tr>
        <tr><th>Requested by</th><td>{$fullName}</td></tr>
    </table>
    <p>Thanks,<br>MOC Book Printing Admin</p>
    </body>
    </html>
HTML;
}

function formatEmailAdminAr($fullName, $bookTitle, $author) {
    return <<<HTML
    <html>
    <head>
    <title>إشعار</title>
    <style>
        body { font-family: sans-serif; direction: rtl; text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
    </head>
    <body>
    <p>لقد طلب المستخدم طباعة الكتاب.</p>
    <p>وفيما يلي تفاصيل الكتاب المطلوب:</p>
    <table>
        <tr><th> عنوان الكتاب </th><td>{$bookTitle}</td></tr>
        <tr><th> اسم المؤلف </th><td>{$author}</td></tr>
        <tr><th> بتوصية من </th><td>{$fullName}</td></tr>
    </table>
    <p>شكرًا,<br>إدارة طباعة الكتب – وزارة الثقافة</p>
    </body>
    </html>
HTML;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullName = htmlspecialchars($_POST['fullName']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST['phone']);
    $bookTitle = htmlspecialchars($_POST['book_title']);
    $author = htmlspecialchars($_POST['author']);
    $downloadUrl = filter_var($_POST['download_url'], FILTER_SANITIZE_URL);
    $bookCode = htmlspecialchars($_POST['bookCode']);
    $lang = ($_POST['lang']);
    $adminEmail = "MocBookPrintOpr@moc.gov.qa";
    $bcc = ["waqar.ahmed@qdsnet.com", "syed.nabeel@qdsnet.com"];
    if ($lang === 'en') {
        $subject = "Book Request Confirmation";
        $userMessage = formatEmailEn($fullName, $bookTitle, $author);
        sendEmail($email, $subject, $userMessage);
        $adminMessage = formatEmailAdminEn($fullName, $bookTitle, $author);
        $subject = "New Book Request Received";
        sendEmail($adminEmail, $subject, $adminMessage, "", implode(',', $bcc));
    } elseif ($lang === 'ar') {
        $userMessage = formatEmailAr($fullName, $bookTitle, $author);
        $subject = "تأكيد طلب الكتاب";
        sendEmail($email, $subject, $userMessage);
        $adminMessage = formatEmailAdminAr($fullName, $bookTitle, $author);
        $subject = "تم استلام طلب كتاب جديد";
        sendEmail($adminEmail, $subject, $adminMessage, "", implode(',', $bcc));
    }
    
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

 // SMS sending
    $messageEn = "Thank you, for book printing request. It is being processed.";
    $messageAr = "شكرًا لك، على طلب طباعة الكتاب. يتم معالجته.";
    if ($lang == "en") {
        $smsResponse = sendSMS($phone, $messageEn);
    } else {
        $smsResponse = sendSMS($phone, $messageAr);
    }
    $networkDetails = getNetworkDetails();
    $logMessage = "Dear Developer,<br>Please check the following log:<br>" . htmlspecialchars($smsResponse) . "<br><br>Network Details:<br>" . $networkDetails;
    sendEmail('syednabeeljavedzaidi@gmail.com', 'MOC Book Print SMS API LOG', $logMessage);
    // Pass logs to JavaScript
    echo "<script>console.log('SMS Response: " . addslashes($smsResponse) . "');</script>";
    echo "<script>console.log('Network Details: " . addslashes($networkDetails) . "');</script>";

    function sendSMS($phone, $message) {
    $url = "https://messaging.ooredoo.qa/bms/soap/Messenger.asmx/HTTP_SendSms";
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

    function getNetworkDetails() {
    $ip = $_SERVER['SERVER_ADDR'];
    $host = gethostname();
    $dns = dns_get_record($host, DNS_A);
    $details = "Server IP: $ip<br>Hostname: $host<br>DNS Records:<br>";

    foreach ($dns as $record) {
        $details .= "Host: {$record['host']} - IP: {$record['ip']}<br>";
    }

    return $details;
}
    
}

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/images/favicon.png" type="image/x-icon">
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
    <script src="https://cdn.datatables.net/plug-ins/2.0.7/sorting/datetime-moment.js"></script>
    
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

    echo '<tr>';
    echo '<td>' . $notification["id"] . '</td>';
    echo '<td>' . $notification["name"] . '</td>';
    echo '<td>' . $notification["email"] . '</td>';
    echo '<td>' . $notification["phone"] . '</td>';
    echo '<td>' . $notification["file_no"] . '</td>';
    echo '<td>' . $notification["title"] . '</td>';
    echo '<td>' . $notification["author"] . '</td>';
    echo '<td>' . $notification["timestamp"] . '</td>';
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
            order: [0, 'asc'],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
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
                        // Uncomment below to include CSV and PDF export options
                        /*
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
                        */
                    ]
                },
                'colvis', // Button to control column visibility
                {
                    extend: 'print',
                    text: 'Print',
                    titleAttr: 'Print Table'
                }
            ],
            columnDefs: [
                { targets: 7, type: 'date' } // Assuming column 7 is a date
            ]
        });

        // Reload page every 10 seconds
        setTimeout(function(){
            window.location.reload();
        }, 60000);
    });
</script>

</body>
</html>
