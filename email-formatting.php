<?php

// Function to format email content in English
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

// Function to format email content in Arabic
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

// Function to format admin email content in English
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

// Function to format admin email content in Arabic
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

?>
