<?php

// Set CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Define notifications file path
define("NOTIFICATIONS_FILE", "notifications.json");

// Include required files
require_once 'email-sender.php';
require_once 'email-formatting.php';
require_once 'send-sms.php';

// Set the default time zone to Qatar
date_default_timezone_set('Asia/Qatar');

// Get the current timestamp
$currentTimestamp = time();

// Format the timestamp as per the specified format
$qatarTime = date("d M Y h:i A", $currentTimestamp);

// Load notifications from file
$notifications = json_decode(file_get_contents(NOTIFICATIONS_FILE), true) ?: [];


// Process POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $fullName = htmlspecialchars($_POST['fullName']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $bookTitle = htmlspecialchars($_POST['book_title']);
    $author = htmlspecialchars($_POST['author']);
    $downloadUrl = filter_var($_POST['download_url'], FILTER_SANITIZE_URL);
    $bookCode = htmlspecialchars($_POST['bookCode']);
    $lang = ($_POST['lang']);
    $phone = htmlspecialchars($_POST['phone']);
    $prefix = '+974';
    $phone_with_prefix = $prefix . $phone;

    // Define admin email and BCC recipients
    // $adminEmail = "MocBookPrintOpr@moc.gov.qa";
    // $bcc = ["waqar.ahmed@qdsnet.com", "syed.nabeel@qdsnet.com"];

    $adminEmail = "misbanabeel@gmail.com";
    $bcc = ["syednabeeljavedzaidi@gmail.com", "syed.nabeel@qdsnet.com"];

    // Send confirmation emails based on language
    if ($lang === 'en') {
        $subject = "Book Request Confirmation";
        $userMessage = formatEmailEn($fullName, $bookTitle, $author);
        sendEmail($email, $subject, $userMessage);
        $adminMessage = formatEmailAdminEn($fullName, $bookTitle, $author);
    } elseif ($lang === 'ar') {
        $subject = "تأكيد طلب الكتاب";
        $userMessage = formatEmailAr($fullName, $bookTitle, $author);
        sendEmail($email, $subject, $userMessage);
        $adminMessage = formatEmailAdminAr($fullName, $bookTitle, $author);
    }

    // Send admin notification email
    $subject = ($lang === 'en') ? "New Book Request Received" : "تم استلام طلب كتاب جديد";
    sendEmail($adminEmail, $subject, $adminMessage, "", implode(',', $bcc));

    // Save notification to file
    $newNotification = [
        "id" => count($notifications) + 1,
        "name" => $fullName,
        "email" => $email,
        "phone" => $phone_with_prefix,
        "file_no" => $bookCode,
        "title" => $bookTitle,
        "author" => $author,
        "download_url" => $downloadUrl,
        "timestamp" => $qatarTime
    ];
    $notifications[] = $newNotification;
    file_put_contents(NOTIFICATIONS_FILE, json_encode($notifications, JSON_PRETTY_PRINT));

     // Determine message based on language
    $messageEn = "Thank you for your book printing request. It is being processed.";
    $messageAr = "شكرا لك على طلبك لطباعة الكتاب. يتم المعالجة الآن.";
    $smsResponse = ($lang == "en") ? sendSMS($phone_with_prefix, $messageEn) : sendSMS($phone_with_prefix, $messageAr);

    // Log SMS response
    $logMessage = "Dear Developer,<br>Please check the following log:<br>" . htmlspecialchars($smsResponse);
    sendEmail('syednabeeljavedzaidi@gmail.com', 'MOC Book Print SMS API LOG', $logMessage);

    // Pass logs to JavaScript
    echo "<script>console.log('SMS Response: " . addslashes($smsResponse) . "');</script>";

    // Display response message to the user
    if (strpos($smsResponse, 'Error') === 0) {
        echo "<p>Failed to send SMS: $smsResponse</p>";
    } else {
        echo "<p>SMS sent successfully!</p>";
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
            order: [0, 'DESC'],
            pageLength: 20,
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
        }, 10000);
    });
</script>

</body>
</html>
