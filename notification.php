<?php


// Define notifications file path
define("NOTIFICATIONS_FILE", "notifications.json");

// Load notifications from file
$notifications = json_decode(file_get_contents(NOTIFICATIONS_FILE), true) ?: [];


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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/2.0.7/sorting/datetime-moment.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <style>
 /* Custom styles for DataTable */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    margin-top: 10px;
}

.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0px 10px;
}
.header-section img {
    width: 20%;  /* Reduced logo size */
    height: auto;
}
.header-section h2 {
    font-weight: bold;
}
/* Additional spacing below the DataTable */
#notificationsTable_wrapper {
    margin-bottom: 50px;
}

/* Reduce left and right padding */
.container {
    padding-left: 11% !important;
    padding-right: 11% !important;
    max-width: 100% !important;
}

/* Styling for unread rows */
.unread-row td {
    font-weight: 600;
   // border: 1px solid #8A1538 !important;
}

/* Fix table width and remove horizontal scrollbar */
#notificationsTable {
    width: 100%;
    overflow-x: auto;
}

/* Media queries for responsive design */
@media only screen and (max-width: 576px) {
    .container {
        padding-left: 3% !important;
        padding-right: 3% !important;
    }
}

@media only screen and (min-width: 577px) and (max-width: 768px) {
    .container {
        padding-left: 5% !important;
        padding-right: 5% !important;
    }
}

@media only screen and (min-width: 769px) and (max-width: 992px) {
    .container {
        padding-left: 8% !important;
        padding-right: 8% !important;
    }
}

@media only screen and (min-width: 993px) {
    .container {
        padding-left: 10% !important;
        padding-right: 10% !important;
    }
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
    <div class="table-responsive">
        <table id="notificationsTable" class="table table-striped table-bordered">
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
                <th>Action</th> <!-- New column for action -->
            </tr>
            </thead>
            <tbody>
           
        <?php foreach ($notifications as $notification) {

            // Determine classes based on notification status and language
            $statusClass = ($notification["status"] === 'unread') ? 'unread-row' : '';
            $langClass = ($notification["lang"] === 'ar') ? 'rtl-text' : '';
            $checkboxStatus = ($notification["status"] === 'read') ? 'checked' : ''; // Checkbox checked if status is read

    // Construct the table row with appropriate classes and attributes
            echo '<tr class="' . $statusClass . ' ' . $langClass . '">';
            echo '<td>' . htmlspecialchars($notification["id"]) . '</td>';
            echo '<td>' . htmlspecialchars($notification["name"]) . '</td>';
            echo '<td>' . htmlspecialchars($notification["email"]) . '</td>';
            echo '<td>' . htmlspecialchars($notification["phone"]) . '</td>';
            echo '<td>' . htmlspecialchars($notification["file_no"]) . '</td>';
            echo '<td>' . htmlspecialchars($notification["title"]) . '</td>';
            echo '<td>' . htmlspecialchars($notification["author"]) . '</td>';
            echo '<td>' . htmlspecialchars($notification["timestamp"]) . '</td>';
            echo '<td style="text-align:center;">';
            echo '<input type="checkbox" class="status-checkbox" data-id="' . htmlspecialchars($notification["id"]) . '" data-status="' . htmlspecialchars($notification["status"]) . '" ' . $checkboxStatus . '>';
            echo '</td>'; // Action checkbox
            echo '</tr>';
    } ?>

           
            </tbody>
        </table>
    </div>
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

    $(document).ready(function () {
        // Update status when checkbox is clicked
        $(document).on('click', '.status-checkbox', function () {
            var checkbox = $(this);
            var id = checkbox.data('id');
            var status = checkbox.prop('checked') ? 'read' : 'unread';

            $.ajax({
                url: 'update-notification.php',
                type: 'POST',
                dataType: 'json', // Specify JSON data type for response
                data: { id: id, status: status },
                success: function(response) {
                    if (response.status === 'success') {
                        // Update the UI with the new status
                        checkbox.data('status', response.updated_status); // Update data-status attribute
                        console.log(response.updated_status); // Debug: Log updated status
                      toastr.success(`Notification marked as ${response.updated_status} successfully.`);
                    } else {
                        toastr.error('Error updating notification status: ' + response.message); // Use Toastr for notification
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(xhr.responseText);
                    toastr.error('Error updating notification status.'); // Use Toastr for notification
                }
            });
        });

        // Update the initial UI status on page load
        $('.status-checkbox').each(function() {
            var checkbox = $(this);
            var status = checkbox.data('status'); // Get the status from data-status attribute
            console.log(status); // Debug: Log status

            // Check or uncheck the checkbox based on the status
            if (status === 'read') {
                checkbox.prop('checked', true);
            } else {
                checkbox.prop('checked', false);
            }
        });
    });
</script>

</body>
</html>
