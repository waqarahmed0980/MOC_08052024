<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Function to send email
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

?>
