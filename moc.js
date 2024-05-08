function setModalData(title, author, downloadUrl) {
    document.getElementById("exampleModalLabel").innerText = title;
    document.getElementById("book_title").value = title;
    document.getElementById("author").value = author;
    document.getElementById("download_url").value = downloadUrl;
    document.getElementById("timestamp").value = new Date().toLocaleString("en-US", {timeZone: "Asia/Qatar"});
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
            toastr.success("Notification Sent!", "Success", {
                positionClass: "toast-top-right",
                timeOut: 5000,
                extendedTimeOut: 1000,
            });
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
