document.addEventListener("DOMContentLoaded", function () {
    const dropZone = document.querySelector(".messages-container");

    // Khi tệp được kéo vào vùng
    dropZone.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropZone.classList.add("dragover"); // Thêm hiệu ứng khi kéo vào
    });

    // Khi tệp rời khỏi vùng thả
    dropZone.addEventListener("dragleave", (e) => {
        e.preventDefault();
        dropZone.classList.remove("dragover"); // Xóa hiệu ứng khi rời khỏi
    });

    // Khi tệp được thả vào vùng
    dropZone.addEventListener("drop", (e) => {
        e.preventDefault();
        dropZone.classList.remove("dragover"); // Xóa hiệu ứng

        let files = e.dataTransfer.files; // Lấy danh sách tệp được thả
        if (files.length) {
            let file = files[0]; // Lấy tệp đầu tiên
            if (!attachmentValidate(file)) {
                alert("Invalid file type or size!"); // Kiểm tra hợp lệ
                return;
            }

            let formData = new FormData();
            formData.append("file", file); // Gắn tệp vào formData
            formData.append("channel_id", currentChannelId()); // ID kênh hiện tại
            formData.append("_token", csrfToken); // Token CSRF

            // Hiển thị loading
            const tempID = `temp_${Date.now()}`;
            messagesContainer.find(".messages").append(
                sendTempMessageCard(loadingSVG("28px"), tempID)
            );

            // Gửi tệp tin qua AJAX
            $.ajax({
                url: $("#message-form").attr("action"), // URL xử lý gửi tin nhắn
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: (data) => {
                    if (data.error.status === 1) {
                        Swal.fire({
                            icon: "warning",
                            title: "Thông báo",
                            text: data.error.message,
                        });
                        errorMessageCard(tempID);
                        return;
                    }

                    // Thêm tin nhắn từ server và xóa tạm tin nhắn
                    const tempMsgCardElement = messagesContainer.find(
                        `.message-card[data-id=${tempID}]`
                    );
                    tempMsgCardElement.before(data.message);
                    tempMsgCardElement.remove();
                    scrollToBottom(messagesContainer);
                },
                error: () => {
                    errorMessageCard(tempID);
                    console.error(
                        "Failed sending the file! Please check your server response."
                    );
                },
            });
        }
    });
});

function attachmentValidate(file) {
    // Lấy kích thước tệp tối đa từ cấu hình
    const maxFileSize = window.chatify.maxUploadSize * 1024 * 1024; // Chuyển đổi MB thành byte

    // Lấy danh sách các loại tệp được phép tải lên
    const allowedTypes = window.chatify.allAllowedExtensions;

    // Kiểm tra kích thước tệp
    if (file.size > maxFileSize) {
        alert("File size exceeds the maximum limit of " + (maxFileSize / 1024 / 1024) + " MB!");
        return false;
    }

    // Kiểm tra loại tệp
    const fileExtension = file.name.split('.').pop().toLowerCase();
    if (!allowedTypes.includes(fileExtension)) {
        alert("File type not allowed! Allowed types: " + allowedTypes.join(', '));
        return false;
    }

    return true;
}
