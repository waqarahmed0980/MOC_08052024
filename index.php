<?php

$strings = [
    "h2" => ["en" => "Book printing", "ar" => "طباعة الكتاب"],
    "button" => ["en" => "Get the book", "ar" => "احصل على الكتاب"],
    "pop_up_p" => ["en" => "Submit your information", "ar" => "أرسل معلوماتك"],
    "uname" => ["en" => "Full Name", "ar" => "الاسم الكامل"],
    "uemail" => ["en" => "Email", "ar" => "بريد إلكتروني"],
    "uphone" => ["en" => "Phone", "ar" => "هاتف"],
    "close_btn" => ["en" => "Close", "ar" => "يغلق"],
    "submit_btn" => ["en" => "Submit", "ar" => "يُقدِّم"],
    "book_1_title" => ["en" => "Metropolis of Culture", "ar" => "حواضر الثقافة"],
    "book_2_title" => ["en" => "Archaeological School in Qatar", "ar" => "المدرسة الأثرية في قطر"],
    "book_3_title" => ["en" => "Pearls of the Gulf", "ar" => "لؤلؤ الخليج"],
    "book_1_author" => ["en" => "Civilization Scholars", "ar" => "علماء الحضارة"],
    "book_2_author" => ["en" => "Salma Salah Al-Qibti", "ar" => "سلمى صلاح القبطي"],
    "book_3_author" => ["en" => "Khaled Abdullah Abdul Aziz Ziara", "ar" => "خالد عبد الله عبد العزيز زيارة" ],
];

// Set the language
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ar';
$lang = in_array($lang, ['en', 'ar']) ? $lang : 'ar';
$direction = ($lang == 'ar') ? 'rtl' : 'ltr';
$textAlign = ($lang == 'ar') ? 'right' : 'left';

$alignment = ($lang == 'ar') ? 'text-right' : 'text-left';

// Sample Book Data
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
    <title>Responsive Book Gallery</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        body {
            background-color: #f5f2e9;
            background-image: url("images/background-moc.png");
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-position: bottom center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            width: 100%;
        }

        .book-card {
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
        }

        .card {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 45rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            background-color: white;
            padding: 10px;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding-left: 15px;
            padding-top: 10px;
            padding-bottom: 10px;
            text-align: <?php echo $textAlign; ?>;
        }

        .card-title {
            margin-bottom: 5px;
        }

        .card-text {
            margin-bottom: 5px;
        }

        .card-img {
            width: 140px; 
            height: auto;
            object-fit: cover;
        }

        .btn-select {
            background-color: black;
            color: white;
        }

        .btn-select:hover {
            background-color: #8A1538;
            color: white;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
        }

        .modal-dialog-centered {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0 auto;
        }

        .toast-top-right {
            top: 12px;
            right: 12px;
        }
    </style>
</head>
<body class="text-<?php echo $alignment; ?>">




    <!-- Language Switcher -->
    <?php  if ($_GET['lang'] == 'en') { ?>
<div class="d-flex justify-content-end w-100 pr-4 mt-2">
    <a href="?lang=ar" class="link">AR </a>
</div>

<?php } else { ?>

<div class="d-flex justify-content-end w-100 pl-4 mt-2">
    <a href="?lang=en" class="link">EN</a>
</div>

<?php } ?>






<!-- Header -->
    <nav class="navbar navbar-light justify-content-center" style="margin-top: -30vh;">
        <div class="container text-center">
            <a class="navbar-brand d-flex flex-column align-items-center" href="#">
                <img src="images/moc-logo-black.png" class="d-inline-block align-top" alt="Ministry of Culture" style="width: 75%; height: auto;">
                <span class="h2 font-weight-bold mt-2"><?php echo $strings["h2"][$lang]; ?></span>
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <?php foreach ($books as $book): ?>
            <div class="col-md-6 col-lg-4 book-card">
                <div class="card">
                    <div class="row no-gutters">
                        <div class="col-auto">
                            <img src="images/book-<?php echo $book['id']; ?>.jpg" class="card-img" alt="<?php echo $strings[$book['title']][$lang]; ?>">
                        </div>
                        <div class="col">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $strings[$book['title']][$lang]; ?></h5>
                                <p class="card-text"><?php echo $strings[$book['author']][$lang]; ?></p>
                                <button class="btn btn-select" data-toggle="modal" data-target="#exampleModal"
    onclick="setModalData('<?php echo $strings[$book['title']][$lang]; ?>', '<?php echo $strings[$book['author']][$lang]; ?>', '<?php echo $book['download_url']; ?>', '<?php echo $book['id']; ?>')">
    <?php echo $strings["button"][$lang]; ?>
</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo $strings["submit_btn"][$lang]; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><?php echo $strings["pop_up_p"][$lang]; ?></p>
                    <form id="submission-form" method="post" action="notification.php">
                        <input type="hidden" name="book_title" id="book_title">
                        <input type="hidden" name="author" id="author">
                        <input type="hidden" name="download_url" id="download_url">
                        <input type="hidden" name="bookCode" id="bookCode">
                        <div class="form-group">
                            <label for="fullName"><?php echo $strings["uname"][$lang]; ?></label>
                            <input type="text" class="form-control" id="fullName" name="fullName" required>
                        </div>
                        <div class="form-group">
                            <label for="email"><?php echo $strings["uemail"][$lang]; ?></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div classumper_group-label for="phone"><?php echo $strings["uphone"][$lang]; ?></label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $strings["close_btn"][$lang]; ?></button>
                            <button type="submit" class="btn btn-primary"><?php echo $strings["submit_btn"][$lang]; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="moc.js"></script>
    <script>
        function setModalData(title, author, downloadUrl, bookCode) {
            document.getElementById("exampleModalLabel").innerText = title;
            document.getElementById("book_title").value = title;
            document.getElementById("author"). value = author;
            document.getElementById("download_url").value = downloadUrl;
            document.getElementById("bookCode").value = 'Book-'+ bookCode;
        }

        document.getElementById("submission-form").addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: 'notification.php',
                type: 'POST',
                data: formData,
                success: function() {
                    $('#exampleModal').modal('hide');
                    // toastr.success("Notification Sent!", "Success", {
                    //     positionClass: "toast-top-right",
                    //     timeOut: 5000,
                    //     extendedTimeOut: 1000,
                    // });
                    $('#submission-form').trigger("reset");
                    $('#exampleModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    const errorMessage = xhr.status + ': ' + xhr.statusText;
                    toastr.error("Failed to send notification - " + errorMessage, "Error", {
                        positionClass: "toast-top-right",
                        timeOut: 5000,
                        extendedTimeOut: 1000,
                    });
                }
            });
        });
    </script>

</body>
</html>