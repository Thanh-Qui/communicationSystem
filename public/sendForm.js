// hàm chức năng mở div ẩn
function openMenuMessage() {
    const displayButton = document.getElementById('displayButton');
    const hiddenButtons = document.getElementById('hiddenButtons');

    if (!displayButton || !hiddenButtons) {
        console.warn('Không tìm thấy phần tử menu hiển thị.');
        return;
    }

    displayButton.addEventListener('click', () => {
        hiddenButtons.style.display = (hiddenButtons.style.display === 'none' || hiddenButtons.style.display === '')
            ? 'block'
            : 'none';
    });

    const buttons = hiddenButtons.querySelectorAll('button');
    buttons.forEach(button => {
        button.addEventListener('click', () => {
            hiddenButtons.style.display = 'none';
        });
    });
}

// chức năng chuyển giọng nói thành văn bản
function voiceToText() {
    const startRecording = document.getElementById('startRecording');
    const messageTextarea = document.querySelector("textarea[name='message']");

    if (!startRecording || !messageTextarea) {
        console.warn('Không tìm thấy phần tử ghi âm hoặc ô nhập tin nhắn.');
        return;
    }

    let recognition;
    let isRecordingText = false;

    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.lang = 'vi-VN';
        recognition.interimResults = true;
        recognition.continuous = false;

        startRecording.addEventListener('click', () => {
            if (isRecordingText) {
                recognition.stop();
            } else {
                recognition.start();
                showNotification('🎙️ Bắt đầu chuyển giọng nói thành văn bản...');
            }
            isRecordingText = !isRecordingText;
        });

        recognition.onresult = (event) => {
            let speechToText = event.results[0][0].transcript;
            if (speechToText.endsWith('.')) {
                speechToText = speechToText.slice(0, -1);
            }
            messageTextarea.value = speechToText;
        };

        recognition.onspeechend = () => {
            if (!isRecordingText) {
                recognition.stop();
            }
        };

        recognition.onerror = (event) => {
            console.error("Lỗi nhận diện giọng nói:", event.error);
            isRecordingText = false;
        };
    } else {
        showNotification('⚠️ Trình duyệt không hỗ trợ nhận diện giọng nói.');
    }
}

// gửi định vị
function sendLocation() {
    const sendLocationButton = document.getElementById('sendLocationButton');
    const sendLocationUrl = sendLocationButton.dataset.sendLocationUrl;

    if (!sendLocationButton) {
        console.warn('Không tìm thấy nút gửi vị trí!');
        return;
    }

    sendLocationButton.addEventListener('click', async (e) => {
        e.preventDefault();

        if (!navigator.geolocation) {
            alert('Trình duyệt không hỗ trợ định vị!');
            return;
        }

        sendLocationButton.classList.add('loading');

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                temporaryMsgId += 1;
                let tempID = `temp_${temporaryMsgId}`;
                const tempLocationMessage = sendTempMessageCard(loadingSVG("28px"), tempID);
                messagesContainer.find(".messages").append(tempLocationMessage);
                scrollToBottom(messagesContainer);

                const formData = new FormData();
                formData.append("latitude", latitude);
                formData.append("longitude", longitude);
                formData.append("channel_id", currentChannelId());
                formData.append("temporaryMsgId", tempID);
                formData.append("_token", csrfToken);

                $.ajax({
                    url: sendLocationUrl,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: () => console.log("Sending location..."),
                    success: (data) => {
                        if (data.status === '403') {
                            Swal.fire({
                                icon: "warning",
                                title: "Thông báo",
                                text: "Đã bị chặn không thể gửi tin nhắn!",
                            });
                        } else if (data.error > 0) {
                            errorMessageCard(tempID);
                            console.error(data.error_msg);
                        } else {
                            const tempMsgCardElement = messagesContainer.find(`.message-card[data-id="${tempID}"]`);
                            if (tempMsgCardElement.length) {
                                const locationHTML = `
                                    <div class="location-message">
                                        <iframe
                                            width="100%"
                                            height="200"
                                            frameborder="0"
                                            style="border:0; border-radius: 10px;"
                                            src="https://www.google.com/maps?q=${latitude},${longitude}&hl=vi&z=15&output=embed"
                                            allowfullscreen>
                                        </iframe>
                                    </div>
                                `;
                                tempMsgCardElement.find(".message").html(locationHTML);
                            }

                            scrollToBottom(messagesContainer);
                            updateContactItem(currentChannelId());
                            sendContactItemUpdates(true);
                        }
                    },
                    error: (xhr, status, error) => {
                        errorMessageCard(tempID);
                        console.error("Failed sending the location! Error:", error);
                    },
                });

                sendLocationButton.classList.remove('loading');
            },
            (error) => {
                alert('Không thể lấy vị trí!');
                console.error('Geolocation error:', error);
                sendLocationButton.classList.remove('loading');
            },
            {
                enableHighAccuracy: true,
            }
        );
    });
}

// chức năng vẽ hình
// drawingBoard.js

let currentColor = 'black';
let isErasing = false;
let isDrawing = false;
let lastX = 0;
let lastY = 0;
const offsetY = 70;
const ctx = document.getElementById('drawingCanvas').getContext('2d');

// Hàm mở bảng vẽ
function openDrawingBoard() {
    const drawingBoardModal = document.getElementById('drawingBoardModal');
    drawingBoardModal.style.display = 'flex'; // Hiển thị modal
}

// Hàm đóng bảng vẽ
function closeDrawingBoard() {
    const drawingBoardModal = document.getElementById('drawingBoardModal');
    drawingBoardModal.style.display = 'none'; // Ẩn modal
}

// Hàm xử lý thay đổi màu vẽ
function changeColor(button) {
    currentColor = button.style.backgroundColor; // Lấy màu từ nút bấm
    ctx.strokeStyle = currentColor; // Cập nhật màu nét vẽ
}

// Hàm chuyển chế độ xóa
function toggleErase() {
    isErasing = !isErasing; // Đổi trạng thái xóa
    const eraseButton = document.getElementById('eraseButton');
    if (isErasing) {
        eraseButton.classList.add('active'); // Làm nổi bật nút xóa
    } else {
        eraseButton.classList.remove('active'); // Tắt nổi bật nút xóa
    }
}

// Hàm bắt đầu vẽ
function startDrawing(e) {
    e.preventDefault(); // Ngăn hành động mặc định
    isDrawing = true;

    if (e.type === "mousedown") {
        lastX = e.offsetX;
        lastY = e.offsetY;
    } else if (e.type === "touchstart") {
        lastX = e.touches[0].clientX - drawingCanvas.offsetLeft;
        lastY = e.touches[0].clientY - drawingCanvas.offsetTop - offsetY;
    }

    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
}

// Hàm vẽ
function draw(e) {
    if (!isDrawing) return;

    e.preventDefault(); // Ngăn hành động mặc định

    let x, y;
    if (e.type === "mousemove") {
        x = e.offsetX;
        y = e.offsetY;
    } else if (e.type === "touchmove") {
        x = e.touches[0].clientX - drawingCanvas.offsetLeft;
        y = e.touches[0].clientY - drawingCanvas.offsetTop - offsetY;
    }

    if (isErasing) {
        ctx.clearRect(x - 10, y - 10, 20, 20); // Xóa vùng nhỏ quanh con trỏ
    } else {
        ctx.lineTo(x, y);
        ctx.stroke();
    }

    lastX = x;
    lastY = y;
}

// Hàm dừng vẽ
function stopDrawing() {
    isDrawing = false;
    ctx.closePath();
}

// Hàm làm sạch bảng vẽ
function clearCanvas() {
    ctx.clearRect(0, 0, drawingCanvas.width, drawingCanvas.height);
}

// Hàm lưu hình vẽ và gửi qua AJAX
function saveCanvas() {
    const imageDataURL = drawingCanvas.toDataURL('image/png');
    drawingBoardModal.style.display = 'none';
    sendDrawing(imageDataURL); // Gửi hình vẽ qua AJAX
}

// Hàm gửi hình vẽ qua AJAX
function sendDrawing(imageDataURL) {
    let temporaryMsgId = `temp_${new Date().getTime()}`;

    const formData = new FormData();
    formData.append("drawing_image", imageDataURL);
    formData.append("channel_id", currentChannelId());
    formData.append("temporaryMsgId", temporaryMsgId);
    formData.append("_token", csrfToken);

    const tempDrawingMessage = sendTempMessageCard(loadingSVG("28px"), temporaryMsgId);
    messagesContainer.find(".messages").append(tempDrawingMessage);
    scrollToBottom(messagesContainer);
    const sendDrawUrl = document.getElementById('openDrawingBoard').dataset.sendDrawUrl;

    // Gửi qua AJAX
    $.ajax({
        url: sendDrawUrl,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: () => console.log("Sending drawing..."),
        success: (data) => handleDrawingResponse(data, temporaryMsgId),
        error: (xhr, status, error) => {
            errorMessageCard(temporaryMsgId);
            console.error("Failed sending the drawing! Error:", error);
        }
    });
}

// Hàm xử lý phản hồi sau khi gửi hình vẽ
function handleDrawingResponse(data, temporaryMsgId) {
    if (data.status === '403') {
        Swal.fire({
            icon: "warning",
            title: "Thông báo",
            text: "Đã bị chặn không thể gửi tin nhắn!",
        });
    } else if (data.error > 0) {
        errorMessageCard(temporaryMsgId);
        console.error(data.error_msg);
    } else {
        const tempMsgCardElement = messagesContainer.find(`.message-card[data-id="${temporaryMsgId}"]`);
        if (tempMsgCardElement.length) {
            const drawingHTML = `
                <div class="message" style="height:170px; width:260px">
                    <img src="${data.image_url}" alt="Drawing" style="max-width: 100%; background: white;; border-radius: 6px" />
                </div>
            `;
            tempMsgCardElement.find(".message").html(drawingHTML);
        }
        scrollToBottom(messagesContainer);
        updateContactItem(currentChannelId());
        sendContactItemUpdates(true);
    }
}

// Hàm khởi tạo các sự kiện
function initializeEvents() {
    // Mở bảng vẽ
    document.getElementById('openDrawingBoard').addEventListener('click', openDrawingBoard);
    
    // Đóng bảng vẽ
    document.getElementById('closeCanvas').addEventListener('click', closeDrawingBoard);

    // Chọn màu
    document.querySelectorAll('.color-btn').forEach(button => {
        button.addEventListener('click', () => changeColor(button));
    });

    // Chế độ xóa
    document.getElementById('eraseButton').addEventListener('click', toggleErase);

    // Bắt đầu vẽ
    const drawingCanvas = document.getElementById('drawingCanvas');
    drawingCanvas.addEventListener('mousedown', startDrawing);
    drawingCanvas.addEventListener('mousemove', draw);
    drawingCanvas.addEventListener('mouseup', stopDrawing);

    drawingCanvas.addEventListener('touchstart', startDrawing);
    drawingCanvas.addEventListener('touchmove', draw);
    drawingCanvas.addEventListener('touchend', stopDrawing);

    // Làm sạch bảng vẽ
    document.getElementById('clearCanvas').addEventListener('click', clearCanvas);

    // Lưu bảng vẽ
    document.getElementById('saveCanvas').addEventListener('click', saveCanvas);
}

// Gọi hàm khởi tạo khi trang được tải
document.addEventListener('DOMContentLoaded', initializeEvents);

let mediaRecorder;
let audioChunks = [];
let stream;
let isRecording = false;
const CHANNEL_ID = document.getElementById('channel-id').value;

const recordButton = document.getElementById('recordButton');
recordButton.addEventListener('click', toggleRecording);

function toggleRecording(e) {
    e.preventDefault();

    if (!isRecording) {
        startRecording();
    } else {
        stopRecording();
    }

    isRecording = !isRecording;
}

async function startRecording() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);

        mediaRecorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                audioChunks.push(event.data);
            }
        };

        mediaRecorder.onstop = () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            audioChunks = [];
            sendAudio(audioBlob);
            stopStream();
        };

        mediaRecorder.start();
        showNotification('Bắt đầu ghi âm');
        recordButton.classList.add('recording');
    } catch (error) {
        showNotification('Chức năng ghi âm chưa hoặc không được hỗ trợ');
        console.error(error);
    }
}

function stopRecording() {
    mediaRecorder.stop();
    showNotification('Kết thúc ghi âm và gửi ghi âm');
    recordButton.classList.remove('recording');
    stopStream();
}

function stopStream() {
    if (stream) {
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
    }
}

function sendAudio(audioBlob) {
    const temporaryMsgId = generateTempMsgId();
    const formData = new FormData();
    formData.append("audio", audioBlob, "recording.mp3");
    formData.append("channel_id", currentChannelId());
    formData.append("temporaryMsgId", temporaryMsgId);
    formData.append("_token", csrfToken);

    const sendAudioUrl = document.getElementById('recordButton').dataset.sendAudioUrl;

    const tempAudioMessage = sendTempMessageCard(loadingSVG("28px"), temporaryMsgId);
    messagesContainer.find(".messages").append(tempAudioMessage);
    scrollToBottom(messagesContainer);

    $.ajax({
        url: sendAudioUrl,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: () => {
            console.log("Gửi bản ghi âm");
        },
        success: (data) => {
            handleAudioSuccess(data, temporaryMsgId);
        },
        error: (xhr, status, error) => {
            handleAudioError(error, temporaryMsgId);
        }
    });
}

function handleAudioSuccess(data, temporaryMsgId) {
    if (data.status === '403') {
        Swal.fire({
            icon: "warning",
            title: "Thông báo",
            text: "Đã bị chặn không thể gửi tin nhắn!",
        });
    } else if (data.error > 0) {
        errorMessageCard(temporaryMsgId);
        console.error(data.error_msg);
    } else {
        const tempMsgCardElement = messagesContainer.find(`.message-card[data-id="${temporaryMsgId}"]`);
        if (tempMsgCardElement.length) {
            const audioHTML = `
                <audio controls>
                    <source src="${data.audio_url}" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            `;
            tempMsgCardElement.find(".message").html(audioHTML);
        }

        scrollToBottom(messagesContainer);
        updateContactItem(currentChannelId());
        sendContactItemUpdates(true);
    }
}

function handleAudioError(error, temporaryMsgId) {
    errorMessageCard(temporaryMsgId);
    console.error("Failed sending the audio! Error:", error);
}

function generateTempMsgId() {
    temporaryMsgId += 1;
    return `temp_${temporaryMsgId}`;
}



let isVideoRecording = false; // Trạng thái quay video
let videoMediaRecorder; // MediaRecorder instance
let videoChunks = []; // Mảng lưu trữ video chunks
let videoStream; // Video stream

const recordVideoButton = document.getElementById('recordVideoButton'); // Nút record
const stopRecordingButton = document.getElementById('stopRecordingButton'); // Nút stop trong form
const liveVideo = document.getElementById('liveVideo'); // Video element để hiển thị stream

recordVideoButton.addEventListener('click', toggleVideoRecording);
stopRecordingButton.addEventListener('click', stopVideoRecording);

// Hàm toggle video recording
function toggleVideoRecording(e) {
    e.preventDefault();
    if (isVideoRecording) {
        return;
    }
    startVideoRecording();
}

// Hàm bắt đầu quay video
async function startVideoRecording() {
    isVideoRecording = true;

    try {
        videoStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        liveVideo.srcObject = videoStream;
        liveVideo.style.display = 'block';

        videoMediaRecorder = new MediaRecorder(videoStream);

        videoMediaRecorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                videoChunks.push(event.data);
            }
        };

        videoMediaRecorder.onstop = () => {
            const videoBlob = new Blob(videoChunks, { type: 'video/mp4' });
            videoChunks = [];
            sendVideo(videoBlob); // Gửi video lên server
            stopVideoStream(); // Dừng stream sau khi quay xong
        };

        videoMediaRecorder.start();
        recordVideoButton.classList.add('recording');
        showNotification("Bắt đầu ghi hình");

        // Hiển thị modal hoặc form nổi
        videoModal.style.display = 'block';
    } catch (error) {
        showNotification('Chức ghi hình chưa được hỗ trợ');
    }
}

// Hàm dừng quay video
function stopVideoRecording() {
    if (videoMediaRecorder && isVideoRecording) {
        videoMediaRecorder.stop();
        recordVideoButton.classList.remove('recording');
        stopVideoStream();
        videoModal.style.display = 'none';
        showNotification("Kết thúc ghi hình và gửi bản ghi hình");
        isVideoRecording = false;
    }
}

// Hàm dừng video stream
function stopVideoStream() {
    if (videoStream) {
        const tracks = videoStream.getTracks();
        tracks.forEach(track => track.stop());
    }
    liveVideo.style.display = 'none';
}

// Hàm gửi video lên server
function sendVideo(videoBlob) {
    const temporaryMsgId = generateTempMsgId();
    const formData = new FormData();
    formData.append("video", videoBlob, "recording.mp4");
    formData.append("channel_id", currentChannelId());
    formData.append("temporaryMsgId", temporaryMsgId);
    formData.append("_token", csrfToken);

    const tempVideoMessage = sendTempMessageCard(loadingSVG("28px"), temporaryMsgId);
    messagesContainer.find(".messages").append(tempVideoMessage);
    scrollToBottom(messagesContainer);

    const sendVideoUrl = document.getElementById('recordVideoButton').dataset.sendVideoUrl;
    
    $.ajax({
        url: sendVideoUrl,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: () => {
            console.log("Gửi bản ghi hình");
        },
        success: (data) => {
            handleVideoSuccess(data, temporaryMsgId);
        },
        error: (xhr, status, error) => {
            handleVideoError(error, temporaryMsgId);
        },
    });
}

// Hàm xử lý khi gửi video thành công
function handleVideoSuccess(data, temporaryMsgId) {
    if (data.status === '403') {
        Swal.fire({
            icon: "warning",
            title: "Thông báo",
            text: "Đã bị chặn không thể gửi tin nhắn!",
        });
    } else if (data.error > 0) {
        errorMessageCard(temporaryMsgId);
        console.error(data.error_msg);
    } else {
        const tempMsgCardElement = messagesContainer.find(`.message-card[data-id="${temporaryMsgId}"]`);
        if (tempMsgCardElement.length) {
            const videoHTML = `
                <video controls style='max-width: 350px; border-radius: 7px '>
                    <source src="${data.video_url}" type="video/mp4">
                    Your browser does not support the video element.
                </video>
            `;
            tempMsgCardElement.find(".message").html(videoHTML);
        }

        scrollToBottom(messagesContainer);
        updateContactItem(currentChannelId());
        sendContactItemUpdates(true);
    }
}

// Hàm xử lý khi gửi video thất bại
function handleVideoError(error, temporaryMsgId) {
    errorMessageCard(temporaryMsgId);
    console.error("Failed sending the video! Error:", error);
}

// Hàm tạo mã tin nhắn tạm thời
function generateTempMsgId() {
    temporaryMsgId += 1;
    return `temp_${temporaryMsgId}`;
}


// hàm download tệp tin
function handleDownload(event, url) {
    event.preventDefault(); // Ngừng hành động mặc định của liên kết

    const isWebView = /wv/.test(navigator.userAgent) || /Android.*Version\/[\d.]+.*Chrome\/[.0-9]+ Mobile/.test(navigator.userAgent);

    if (isWebView) {
        // Nếu đang ở trong WebView, dùng cách truyền thống để tải file
        window.location.href = url;  // WebView sẽ tự xử lý tải file
    } else {
        // Dùng fetch + blob cho trình duyệt
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        Swal.fire({
                            icon: "warning",
                            title: "Thông báo",
                            text: data.error,
                        });
                    });
                }
                return response.blob(); // Nếu file tồn tại, trả về blob
            })
            .then(blob => {
                if (blob) {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = ''; // Bạn có thể đặt tên file ở đây nếu cần
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                }
            })
            .catch(error => {
                console.error('Error downloading file:', error);
            });
    }
}

// update giao diện
function updatePollUIRealtime(messageId, voteCounts, userVotes = null) {
    console.log(`Updating poll UI in real-time for message ${messageId}:`, { 
        voteCounts, 
        userVotes
    });

    const pollContainer = document.querySelector(`#poll-${messageId}`);
    if (!pollContainer) {
        console.error(`Poll container #poll-${messageId} not found`);
        return;
    }

    // Cập nhật tất cả các lựa chọn
    document.querySelectorAll(`#poll-${messageId} .poll-options label`).forEach(label => {
        const input = label.querySelector("input[type='checkbox']");
        const option = input.value;
        const badge = label.querySelector(".badge");
        
        // QUAN TRỌNG: Cập nhật số lượng cho tất cả các tùy chọn, kể cả khi là 0
        if (voteCounts) {
            // Nếu option tồn tại trong voteCounts, hiển thị giá trị đó
            // Nếu không tồn tại hoặc là undefined, hiển thị 0
            badge.textContent = voteCounts[option] !== undefined ? voteCounts[option] : "0";
        }
        
        // Chỉ cập nhật trạng thái đã chọn nếu là người dùng hiện tại đã bình chọn
        if (userVotes !== null) {
            input.checked = userVotes.includes(option);

            // Cập nhật giao diện dựa trên lựa chọn
            if (userVotes.includes(option)) {
                label.classList.add("bg-primary", "text-white");
                label.classList.remove("bg-light");
                badge.className = "badge bg-light text-dark";
            } else {
                label.classList.remove("bg-primary", "text-white");
                label.classList.add("bg-light");
                badge.className = "badge bg-secondary";
            }
        }
    });

    // Cập nhật tổng số người bình chọn
    const totalVoters = calculateTotalVoters(voteCounts);
    const totalVotersElement = pollContainer.querySelector(".total-voters");
    if (totalVotersElement) {
        totalVotersElement.textContent = `${totalVoters} người đã bình chọn`;
    }
}

// Hàm tính tổng số người bình chọn
function calculateTotalVoters(voteCounts) {
    let total = 0;
    for (const option in voteCounts) {
        total += parseInt(voteCounts[option] || 0);
    }
    return total;
}

// bình chọn 1 hoặc nhiều lựa chọn
function submitPoll(messageId) {
    const selected = document.querySelectorAll(`input[name="poll_${messageId}[]"]:checked`);
    const options = Array.from(selected).map(el => el.value);
        
    const route = window.pollVoteRoute;
    const token = window.csrfToken;

    // Hiển thị trạng thái đang xử lý
    const pollContainer = document.querySelector(`#poll-${messageId}`);
    if (pollContainer) {
        pollContainer.classList.add("poll-processing");
    }

    fetch(route, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token
        },
        body: JSON.stringify({
            message_id: messageId,
            options: options
        })
    })
    .then(res => res.json())
    .then(data => {
        // Xóa trạng thái xử lý
        if (pollContainer) {
            pollContainer.classList.remove("poll-processing");
        }

        if (data.status === '200') {
            // Cập nhật giao diện cho người dùng hiện tại
            console.log("Poll submitted successfully:", data);
            
            // // Người dùng hiện tại sẽ thấy lựa chọn của họ được cập nhật ngay lập tức
            // updatePollUI(messageId, data.vote_counts, data.user_vote || [], data.voters);
        } else {
            // Hiển thị thông báo lỗi
            showInfoNotification('info' ,data.error || "Đã xảy ra lỗi khi bình chọn");
        }
    })
    .catch(error => {
        console.error("Error voting on poll:", error);
        // Xóa trạng thái xử lý
        if (pollContainer) {
            pollContainer.classList.remove("poll-processing");
        }
        showInfoNotification('error' ,"Đã xảy ra lỗi khi kết nối tới máy chủ");
    });
}

function showInfoNotification(type ,message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type,
            title: 'Thông báo',
            text: message,
        });
    } else {
        alert(message);
    }
}


// modal chuyển tiếp
function openModalShareMessage() {
    document.addEventListener('click', function (e) {
        const shareIcon = e.target.closest('.share-btn');
        if (shareIcon) {
            e.preventDefault();
            
            // id của tin nhắn
            const messageId = shareIcon.dataset.toId;
            document.getElementById('shareMessageId').value = messageId;
    
            const modal = new bootstrap.Modal(document.getElementById('shareMessage'));
            modal.show();
        }
    });
}

// tìm kiếm user để chuyển tiếp
function searchUserDrive(inputId, suggestionsId) {
    const userSearch = document.getElementById(inputId);
    const userSuggestions = document.getElementById(suggestionsId);
    const selectedDisplay = document.getElementById('selectedListDisplay');
    window.selectedList = [];
  
    userSearch.addEventListener('input', function () {
      const query = userSearch.value.trim();
  
      if (query.length >= 1) {
        fetch(`/searchUserDrive?query=${encodeURIComponent(query)}`)
          .then(response => response.json())
          .then(data => {
            userSuggestions.innerHTML = '';
  
            const users = data.users || [];
            const groups = data.groups || [];
  
            if (users.length === 0 && groups.length === 0) {
              userSuggestions.style.display = 'none';
              return;
            }
  
            [...users.map(u => ({...u, type: 'user'})), ...groups.map(g => ({...g, type: 'group'}))].forEach(itemData => {
              const item = document.createElement('a');
              item.classList.add('list-group-item', 'list-group-item-action');
              item.href = '#';
              item.innerText = itemData.type === 'user' ? `${itemData.name} (${itemData.email})` : `${itemData.name} (Nhóm)`;
  
              item.addEventListener('click', function () {
                const exists = selectedList.find(i => i.id === itemData.id && i.type === itemData.type);
                if (!exists) {
                  selectedList.push({ id: itemData.id, type: itemData.type, label: item.innerText });
  
                  const li = document.createElement('li');
                  li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                  li.textContent = item.innerText;
  
                  const removeBtn = document.createElement('button');
                  removeBtn.classList.add('btn', 'btn-sm', 'btn-danger');
                  removeBtn.innerHTML = '&times;';
                  removeBtn.onclick = () => {
                    const index = selectedList.findIndex(i => i.id === itemData.id && i.type === itemData.type);
                    if (index !== -1) {
                      selectedList.splice(index, 1);
                      li.remove();
                      updateHiddenInput(); //cập nhật lại input ẩn!
                    }
                  };
                  
  
                  li.appendChild(removeBtn);
                  selectedDisplay.appendChild(li);
                }
  
                userSearch.value = '';
                userSuggestions.innerHTML = '';
                updateHiddenInput();
              });
  
              userSuggestions.appendChild(item);
            });
  
            userSuggestions.style.display = 'block';
          });
      } else {
        userSuggestions.style.display = 'none';
      }
    });
  
    function updateHiddenInput() {
      const hiddenInput = document.getElementById('selectedListData');
      hiddenInput.value = JSON.stringify(selectedList);
    }
    
  
    document.addEventListener('click', function (e) {
      if (!userSearch.contains(e.target) && !userSuggestions.contains(e.target)) {
        userSuggestions.style.display = 'none';
      }
    });
}



document.addEventListener('DOMContentLoaded', function() {
    openModalShareMessage();

    searchUserDrive('userSearch', 'userSuggestions');
    shareMessage();
})

// chuyển tiếp tin nhắn
function shareMessage() {
    const shareButton = document.getElementById('shareMessageButton');
    const shareUrl = shareButton?.dataset.shareUrl;

    if (!shareButton || !shareUrl) {
        console.warn('Không tìm thấy nút chia sẻ hoặc URL!');
        return;
    }

    shareButton.addEventListener('click', function (e) {
        e.preventDefault();

        const messageId = document.getElementById('shareMessageId').value;
        const selectedList = window.selectedList || [];

        if (selectedList.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Thông báo',
                text: 'Bạn chưa chọn người hoặc nhóm chia sẻ!',
            });
            return;
        }

        // Gán dữ liệu vào input ẩn để form giữ đồng bộ
        document.getElementById('selectedListData').value = JSON.stringify(selectedList);

        const formData = new FormData();
        formData.append("channel_id", currentChannelId());
        formData.append("message_id", messageId);
        formData.append("list_data", JSON.stringify(selectedList));
        formData.append("_token", csrfToken);
        
        shareButton.classList.add('loading');

        $.ajax({
            url: shareUrl,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === '200') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: response.message,
                    });
            
                    $('#shareMessage').modal('hide');
            
                    // ✅ Cập nhật lại đúng channel_id từ response
                    response.results.forEach(item => {
                        if (item.channel_id) {
                            updateContactItem(item.channel_id);
                        }
                    });
            
                    // Gửi thông báo cập nhật UI
                    sendContactItemUpdates(true);
            
                    // Xóa dữ liệu đã chọn
                    document.getElementById('selectedListDisplay').innerHTML = '';
                    window.selectedList = [];
                    document.getElementById('selectedListData').value = '';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: response.error || 'Có lỗi xảy ra!',
                    });
                }
            },
            
            error: function (xhr, status, error) {
                console.error("Lỗi khi gửi yêu cầu chia sẻ:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể chia sẻ tin nhắn!',
                });
            },
            complete: function () {
                shareButton.classList.remove('loading');
            }
        });
    });
}

// Trả lời tin nhắn cụ thể
function replyToMessage(messageId, messageContent) {
    // Set the reply_message_id hidden input
    document.getElementById('reply_message_id').value = messageId;

    console.log("Setting reply to message ID:", messageId);
    // Create or show reply preview
    let replyPreview = document.getElementById('reply-preview');
    // Create the reply preview content
    let previewContent = messageContent;
    // Trim the content if it's too long
    if (previewContent.length > 50) {
        previewContent = previewContent.substring(0, 50) + '...';
    }

    replyPreview.innerHTML = `
        <div class="reply-content">
            <span class="reply-icon"><i class="fa-solid fa-reply"></i></span>
            <span class="reply-text">${previewContent.replace('>', '')}</span>
        </div>
        <button type="button" class="cancel-reply" onclick="cancelReply()">
            <i class="fa-solid fa-times"></i>
        </button>
    `;

    // Show the reply preview
    replyPreview.style.display = 'flex';

    // Focus on textarea to start typing reply
    document.querySelector('.m-send').focus();

    // Enable the textarea and send button if they're disabled
    document.querySelector('.m-send').removeAttribute('readonly');
    document.querySelector('.send-button').removeAttribute('disabled');
}

// xóa trạng thái trả lời
function cancelReply() {
    // Clear the reply_message_id input
    document.getElementById('reply_message_id').value = '';

    // Hide the reply preview
    const replyPreview = document.getElementById('reply-preview');
    if (replyPreview) {
        replyPreview.style.display = 'none'; // chỉ ẩn chứ không xóa
        replyPreview.innerHTML = ''; // xóa nội dung nếu cần
    }
}

// trỏ tới tin nhắn được trả lời
function scrollToMessage(messageId) {
    const target = document.getElementById('message-' + messageId);
    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Thêm class highlight border
        target.classList.add('highlight-border');

        // Sau 2s, xoá class border để tránh giữ hiệu ứng quá lâu
        setTimeout(() => {
            target.classList.remove('highlight-border');
        }, 2000);
    }
}