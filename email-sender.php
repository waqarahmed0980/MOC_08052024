<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'email-formatting.php'; // Include email formatting functions

// Set CORS headers for incoming requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Set security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Content-Security-Policy: default-src 'self'");

// Function to send email
function sendEmail($to, $subject, $body, $cc = "", $bcc = "") {
    $mail = new PHPMailer(true);
    try {
        // Configure PHPMailer settings
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

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract form data
    $fullName = $_POST['fullName'] ?? '';
    $bookTitle = $_POST['book_title'] ?? '';
    $author = $_POST['author'] ?? '';
    $email = $_POST['email'] ?? '';
    $lang = $_POST['lang'] ?? '';

    // Define admin email and BCC recipients
    // $adminEmail = "MocBookPrintOpr@moc.gov.qa";
    $adminEmail = "syednabeeljavedzaidi@gmail.com";
    $bcc = ["waqar.ahmed@qdsnet.com", "syed.nabeel@qdsnet.com"];


    

    // Send confirmation emails based on language
    if ($lang === 'en') {
        // Format email content for user and admin
        $subject = "Book Request Confirmation";
        $userMessage = formatEmailEn($fullName, $bookTitle, $author);
        $adminMessage = formatEmailAdminEn($fullName, $bookTitle, $author);
    } elseif ($lang === 'ar') {
        // Format email content for user and admin
        $subject = "تأكيد طلب الكتاب";
        $userMessage = formatEmailAr($fullName, $bookTitle, $author);
        $adminMessage = formatEmailAdminAr($fullName, $bookTitle, $author);
    }

    // Send emails
    $userEmailSent = sendEmail($email, $subject, $userMessage);
    $adminEmailSent = sendEmail($adminEmail, $subject, $adminMessage, "", implode(',', $bcc));

    // Check if both emails were sent successfully
    if ($userEmailSent && $adminEmailSent) {
        // Both emails sent successfully
        // Proceed with further actions or return success response
        http_response_code(200); // OK
        echo json_encode(['success' => true]);
    } else {
        // Handle email sending failure
        error_log("Failed to send email(s)");
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to send emails']);
    }
} else {
    // Invalid request method
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method not allowed']);
}

?>
