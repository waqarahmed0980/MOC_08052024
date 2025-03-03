<?php
$strings = [
    "h2" => ["en" => "Book printing", "ar" => "طباعة الكتاب"],
    "button" => ["en" => "Get the book", "ar" => "اطبع الكتاب"],
    "pop_up_p" => ["en" => "Submit your information", "ar" => "أرسل معلوماتك"],
    "uname" => ["en" => "Full Name", "ar" => "الاسم الكامل"],
    "uemail" => ["en" => "Email", "ar" => "بريد إلكتروني"],
    "uphone" => ["en" => "Phone", "ar" => "هاتف"],
    "close_btn" => ["en" => "Close", "ar" => "إغلاق"],
    "submit_btn" => ["en" => "Submit", "ar" => "إرسال"],
    "book_1_title" => ["en" => "Metropolis of Culture", "ar" => "حواضر الثقافة"],
    "book_2_title" => ["en" => "Archaeological School in Qatar", "ar" => "المدرسة الأثرية في قطر"],
    "book_3_title" => ["en" => "Pearls of the Gulf", "ar" => "لؤلؤ الخليج"],
    "book_1_author" => ["en" => "Civilization Scholars", "ar" => "علماء الحضارة"],
    "book_2_author" => ["en" => "Salma Salah Al-Qibti", "ar" => "سلمى صلاح القبطي"],
    "book_3_author" => ["en" => "Khaled Abdullah Abdul Aziz Ziara", "ar" => "خالد عبد الله عبد العزيز زيارة"],
];

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ar';
$lang = in_array($lang, ['en', 'ar']) ? $lang : 'ar';
$direction = ($lang == 'ar') ? 'rtl' : 'ltr';
$textAlign = ($lang == 'ar') ? 'right' : 'left';
$alignment = ($lang == 'ar') ? 'text-right' : 'text-left';

$books = [
    ["id" => 1, "title" => "book_1_title", "author" => "book_1_author", "download_url" => "downloads/book-1.pdf"],
    ["id" => 2, "title" => "book_2_title", "author" => "book_2_author", "download_url" => "downloads/book-2.pdf"],
    ["id" => 3, "title" => "book_3_title", "author" => "book_3_author", "download_url" => "downloads/book-3.pdf"],
];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $direction; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOC Book Printing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="icon" href="/images/favicon.png" type="image/x-icon">
    
    <?php if ($lang === 'ar'): ?>
        <style>
            @font-face {
                font-family: 'QatarFont';
                src: url('QatarFont-Regular.woff2') format('woff2'),
                     url('QatarFont-Regular.woff') format('woff');
                font-weight: normal;
                font-style: normal;
            }
            body, button, input, textarea {
                font-family: 'QatarFont', sans-serif;
            }
        </style>
    <?php endif; ?>
    <style>
        body {
        background-color: #f5f2e9;
        background-image: url("images/moc-background-new.png");
        background-size: cover;
        background-attachment: fixed;
        background-repeat: no-repeat;
        background-position: bottom bottom;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        flex-direction: column;
        overflow: hidden;
        margin: 0;
        padding: 0;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
        }

        .card-img {
            padding: 5px;
        }

        /* In RTL mode */
        /* [dir="rtl"] .modal-header .close {
            margin-left: -1rem;
            margin-right: 0;
        }

        [dir="rtl"] .modal-footer {
            flex-direction: row-reverse;
        } */

        [dir="rtl"] .form-control {
            border-radius: 0 0.25rem 0.25rem 0;
        }

        [dir="rtl"] .input-group-prepend,
        [dir="rtl"] .input-group-text {
            border-radius: 0.25rem 0 0 0.25rem;
        }

        /* General adjustments */
        .card {
            height: 100%;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-grow: 1;
        }

        .card-title {
            height: 4rem; /* Adjust height as needed */
            overflow: hidden;
        }

        .card-text {
            height: 4rem; /* Adjust height as needed */
            overflow: hidden;
        }

        /* Full Width Handling */
        .full-width {
            width: 100%;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 1280px) {
            body {
                overflow-y: auto;
            }
        }

        .min-h-screen {
        min-height: 70vh;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-4 text-<?php echo $alignment; ?> full-width">

    <!-- Logo -->
     <!-- 
    <div class="mb-2">
        <img src="images/moc-logo-black.png" alt="Logo" class="w-96 mx-auto">
    </div>
    -->
    
    <!-- Language Switcher -->
    <div class="flex justify-end w-full max-w-7xl mb-2">
        <?php if ($lang == 'ar'): ?>
            <a href="?lang=en" class="text-blue-500 px-2">EN</a>
        <?php else: ?>
            <a href="?lang=ar" class="text-blue-500 px-2">AR</a>
        <?php endif; ?>
    </div>

    <!-- Header -->
    <h2 class="text-3xl font-bold mb-5"><?php echo $strings["h2"][$lang]; ?></h2>

    <!-- Cards Section -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3 w-full max-w-7xl full-width">
        <?php foreach ($books as $book): ?>
           <a href="#" class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 card">
    <img class="object-cover w-full h-48 md:h-auto md:w-48 md:rounded-none md:rounded-l-lg card-img" src="images/book-<?php echo $book['id']; ?>.jpg" alt="">
    <div class="flex flex-col justify-between p-4 leading-normal card-body">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-700 dark:text-white card-title"><?php echo $strings[$book['title']][$lang]; ?></h5>
        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400 card-text"><?php echo $strings[$book['author']][$lang]; ?></p>
        <div class="flex mt-auto">
            <button onclick="setModalData('<?php echo $strings[$book['title']][$lang]; ?>', '<?php echo $strings[$book['author']][$lang]; ?>', '<?php echo $book['download_url']; ?>', '<?php echo $book['id']; ?>')" class="px-4 py-2 text-white bg-black rounded" data-toggle="modal" data-target="#bookFormModal">
                <?php echo $strings["button"][$lang]; ?>
            </button>
        </div>
    </div>
</a>

        <?php endforeach; ?>
    </div>

    <!-- Modal -->
    <div id="bookFormModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-80 hidden">
        <div class="bg-white rounded-lg shadow-lg w-96">
            <div class="px-4 py-2 text-lg font-bold border-b flex justify-between items-center">
                <span id="bookFormModalLabel"></span>
                <button onclick="closeModal()" class="text-gray-600 <?php echo $lang === 'ar' ? 'mr-auto ml-0' : 'ml-auto mr-0'; ?>">&times;</button>
            </div>
            <div class="p-4 space-y-4">
                <p class="text-lg"><?php echo $strings["pop_up_p"][$lang]; ?></p>
                <form id="submission-form" method="post" class="space-y-4">
                    <input type="hidden" name="book_title" id="book_title">
                    <input type="hidden" name="author" id="author">
                    <input type="hidden" name="download_url" id="download_url">
                    <input type="hidden" name="bookCode" id="bookCode">
                    <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">

                    <div class="form-group">
                        <label for="fullName" class="text-lg"><?php echo $strings["uname"][$lang]; ?> <span class="text-red-500">*</span></label>
                        <input type="text" required class="w-full px-4 py-2 border rounded" id="fullName" name="fullName">
                    </div>
                    <div class="form-group">
                        <label for="email" class="text-lg"><?php echo $strings["uemail"][$lang]; ?> <span class="text-red-500">*</span></label>
                        <input type="email" required class="w-full px-4 py-2 border rounded" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="phone" class="text-lg"><?php echo $strings["uphone"][$lang]; ?> <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <span class="px-2 py-2 border border-r-0 rounded-<?php echo ($lang === 'ar') ? 'r' : 'l'; ?> bg-gray-200">+974</span>
                            <input type="tel" required class="w-full px-4 py-2 border rounded-<?php echo ($lang === 'ar') ? 'l' : 'r'; ?> <?php echo ($lang === 'ar') ? 'text-right' : ''; ?>" id="phone" name="phone">
                        </div>
                    </div>
                    <div class="modal-footer flex justify-end space-x-2">
                        <button type="button" onclick="closeModal()" class="ml-2 px-4 py-2 text-white bg-gray-500 rounded"><?php echo $strings["close_btn"][$lang]; ?></button>
                        <button type="submit" class="px-4 py-2 text-white bg-black rounded"><?php echo $strings["submit_btn"][$lang]; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <script>

    function setModalData(title, author, downloadUrl, bookCode) {
        document.getElementById("bookFormModalLabel").innerText = title;
        document.getElementById("book_title").value = title;
        document.getElementById("author").value = author;
        document.getElementById("download_url").value = downloadUrl;
        document.getElementById("bookCode").value = 'Book-' + bookCode;
        document.getElementById("bookFormModal").classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById("bookFormModal").classList.add('hidden');
    }

    document.getElementById("submission-form").addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        closeModal();

        // Function to handle AJAX success
        function handleSuccess(response) {
            console.log(response);
        }

        // Function to handle AJAX error
        function handleError(endpoint, xhr, status, error) {
            console.error(`Failed to send to ${endpoint}`, error);
        }

        // Function to handle SMS AJAX success with XML response
        function handleSMSSuccess(response) {
            try {
                // Extract the relevant information from the XML
                const sendResult = $(response).find('SendResult').text();
                // Log or display the extracted information
                console.log("SMS Sent:", sendResult);
            } catch (error) {
                console.error("Failed to parse XML:", error.message);
            }
        }

        // AJAX call to record-update.php
        $.ajax({
            url: 'record-update.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                handleSuccess('Record Updated!');
            },
            error: function(xhr, status, error) {
                handleError('record-update.php', xhr, status, error);
            }
        });

        // AJAX call to send-sms.php
        $.ajax({
            url: 'send-sms.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                handleSMSSuccess(response);
            },
            error: function(xhr, status, error) {
                handleError('send-sms.php', xhr, status, error);
            }
        });

        // AJAX call to email-sender.php
        $.ajax({
            url: 'email-sender.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                handleSuccess('Email Sent!');
            },
            error: function(xhr, status, error) {
                handleError('email-sender.php', xhr, status, error);
            }
        });

        // Reset the form
        $('#submission-form').trigger("reset");
    });
</script>

</body>
</html>
