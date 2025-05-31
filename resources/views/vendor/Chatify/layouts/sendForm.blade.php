<div class="messenger-sendCard">

    {{-- hiển thị đoạn tin nhắn cần trả lời --}}
    <div id="reply-preview" class="reply-preview" style="display: none;"></div>

    <form id="message-form" method="POST" action="{{ route('send.message') }}" enctype="multipart/form-data">
        @csrf
        <label><span class="fas fa-paperclip"></span><input disabled='disabled' type="file" class="upload-attachment" name="file" accept=".{{implode(', .',config('chatify.attachments.allowed_images'))}}, .{{implode(', .',config('chatify.attachments.allowed_files'))}}" /></label>
        <button class="emoji-button"></span><span class="fas fa-smile"></button>
        
        {{-- Xử lý về việc ghi âm --}}
        <button class="sendAudio-button" id="recordButton" data-send-audio-url="{{ route('send.audio') }}">
            <span class="fas fa-microphone"></span>
        </button>

        <div style="position: relative;">
            <!-- Nút 3 chấm -->
            <button type="button" id="displayButton">
                <span class="fas fa-ellipsis-h"></span>
            </button>
        
            <div id="hiddenButtons" style="display: none;">
                <button type="button" class="sendVideo-button" id="recordVideoButton" data-send-video-url="{{ route('send.video') }}">
                    <span class="fas fa-video" style="color: white"></span>Ghi hình
                </button>
                <button type="button" class="sendVideo-button" type="button" id="openDrawingBoard" data-send-draw-url="{{ route('send.drawing') }}">
                    <span class="fas fa-pen" style="color: white"></span>Vẽ hình
                </button>
                <button type="button" class="record-button" id="startRecording">
                    <span class="fas fa-microphone-alt" style="color: white"></span>Chuyển văn bản
                </button>
                <button type="button" class="sendLocation-button" id="sendLocationButton" data-send-location-url="{{ route('send.location') }}">
                    <span class="fas fa-map-marker-alt" style="color: white"></span>Vị trí
                </button>

                <button class="sendPoll-button" type="button" data-to-id="{{ $channel_id }}">
                    <span class="fas fa-poll"  style="color: white"></span> Bình chọn
                </button>
            </div>
        </div>
        

        {{-- Chức năng vẽ --}}
        <div id="drawingBoardModal" style="display: none;">
            <div class="modal-content" style="margin: 15px;">
                <button id="closeCanvas"><i class="fa-solid fa-xmark"></i></button>
                <canvas id="drawingCanvas" width="500" height="400" style="border: 1px solid #2ecc71; border-radius: 6px"></canvas>

                
                <div id="colorPicker">
                    <button class="color-btn" style="background-color: red;"></button>
                    <button class="color-btn" style="background-color: green;"></button>
                    <button class="color-btn" style="background-color: blue;"></button>
                    <button class="color-btn" style="background-color: black;"></button>
                    <button class="color-btn" style="background-color: yellow;"></button>
                    <button class="color-btn" style="background-color: #8e44ad;"></button>
                    <button class="color-btn" style="background-color: #f1c40f;"></button>
                    <button class="color-btn" style="background-color: #e67e22;"></button>
                    <button class="color-btn" style="background-color: #bdc3c7;"></button>
                    <button class="color-btn" style="background-color: #FC427B;"></button>
                    <button class="color-btn" style="background-color: #25CCF7;"></button>
                </div>

                <div id="buttonContainer" style="margin-top: 10px;">
                    <button id="clearCanvas">Xóa toàn bộ</button>
                    <button id="eraseButton">Xóa</button>
                    <button id="saveCanvas">Lưu</button>
                </div>
                
            </div>
        </div>
        <input type="hidden" id="drawingImage" name="drawing_image" />
        
        <textarea name="message" class="m-send app-scroll" placeholder="Type a message.."></textarea>
        <button disabled='disabled' class="send-button"><span class="fas fa-paper-plane"></span></button>

        <input type="hidden" id="channel-id" value="{{ $channel_id }}">
        <input type="hidden" id="reply_message_id" name="reply_message_id">
    </form>
</div>


<!-- Form mở camera -->
<div id="videoModal" class="modal" style="display: none;">
    <div class="modal-content" style="text-align: center; border-radius: 7px">
      <video id="liveVideo" autoplay muted style="width: 80%; max-height: 300px; background: black; border-radius: 10px; margin: auto"></video>
  
      <p>Đang quay video...</p>
      <button id="stopRecordingButton">Dừng ghi hình</button>
    </div>
  </div>

{{--  form hiển thị thông báo --}}
<div class="notification_audio" id="notification">
  <span id="notification-text"></span>
</div>


<script>

    document.addEventListener('DOMContentLoaded', function () {
        openMenuMessage();
        voiceToText();
        sendLocation();
    });


    function openModalAddToDo() {
        const buttons = document.querySelectorAll('.sendPoll-button');
        buttons.forEach(button => {
            if (!button.dataset.listenerAttached) {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    currentToId = this.dataset.toId; // Lưu to_id khi click
                    const modal = new bootstrap.Modal(document.getElementById('addToDo'));
                    modal.show();
                });
                button.dataset.listenerAttached = "true";
            }
        });
    }

    function addPollOption() {
        const container = document.getElementById('poll-options');
        const index = container.children.length + 1;
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'options[]';
        input.className = 'form-control mb-2';
        input.placeholder = 'Lựa chọn ' + index;
        input.required = true;
        container.appendChild(input);
    }

    function handlePollCreateSuccess(data, tempID) {
        const tempMsgCardElement = messagesContainer.find(`.message-card[data-id="${tempID}"]`);
        
        if (tempMsgCardElement.length) {
            // Update the temporary ID to the permanent ID received from server
            tempMsgCardElement.attr('data-id', data.message_id);
            
            const pollData = data.poll_data;
            const html = generatePollHTML({
                id: data.message_id, // Use permanent ID from server
                title: pollData.title,
                options: pollData.options,
                end_date: pollData.end_date,
            });
            
            tempMsgCardElement.find(".message").html(html);
            
            // Update any other attributes that might reference the old ID
            const pollBox = tempMsgCardElement.find(`#poll-${tempID}`);
            if (pollBox.length) {
                pollBox.attr('id', `poll-${data.message_id}`);
            }
        }
        
        scrollToBottom(messagesContainer);
        updateContactItem(data.channel_id);
        sendContactItemUpdates(true);
    }

    function generatePollHTML(pollData) {
        const id = pollData.id;
        const title = pollData.title;
        const options = pollData.options;
        const endDate = pollData.end_date;

        let optionsHTML = options.map((option, index) => {
            const count = 0; // temp poll chưa có ai vote
            return `
            <label style="cursor: pointer; background: #f1f2f6;" class="list-group-item d-flex justify-content-between align-items-center mb-2 rounded">
                <div class="d-flex align-items-center">
                    <div style="flex-shrink: 0;">
                        <input type="checkbox"
                            name="poll_${id}[]"
                            id="poll_${id}_${index}"
                            value="${option}"
                            class="form-check-input me-2">
                    </div>
                    <span style="padding-right: 12px; font-size: 14px">${option}</span>
                </div>
                <span class="badge bg-secondary">${count}</span>
            </label>`;
        }).join('');

        return `
        <div class="poll-box rounded bg-white text-dark mb-3" id="poll-${id}">
            <div class="modal-header flex-column align-items-start">
                <p style="font-size: 18px" class="modal-title m-0"><strong>${title}</strong></p>
                <span style="font-size: 15px; color: rgb(137, 137, 137)">Chọn nhiều phương án</span>
                <span style="font-size: 15px; color: rgb(137, 137, 137)">Thời gian kết thúc: ${endDate}</span>
            </div>
            <div class="modal-body" style="max-height: 150px; overflow: auto">
                <div class="poll-options list-group">
                    ${optionsHTML}
                </div>
            </div>
            <div class="p-2">
                <button style="width: 100%;" class="btn btn-outline-primary" onclick="submitPoll('${id}')">Bình chọn</button>
            </div>
        </div>`;
    }

    document.addEventListener('DOMContentLoaded', function () {
        openModalAddToDo();

        const pollForm = document.getElementById('pollForm');

        pollForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(pollForm);
            const title = formData.get('title');
            const options = formData.getAll('options[]');
            const end_date = formData.get('end_date');

            if (options.length < 2) {
                alert("Vui lòng nhập ít nhất 2 lựa chọn.");
                return;
            }

            const channelId = currentChannelId();
            const tempID = `temp_poll_${++temporaryMsgId}`;

            const tempPollMessage = sendTempMessageCard(loadingSVG("28px"), tempID);
            messagesContainer.find(".messages").append(tempPollMessage);
            scrollToBottom(messagesContainer);

            formData.append("channel_id", channelId);
            formData.append("temporaryMsgId", tempID);
            formData.append("_token", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            const sendPollRoute = window.sendPollRoute || "/poll/send"; // Fallback

            $.ajax({
                url: sendPollRoute,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: () => {
                    console.log("Sending poll to:", sendPollRoute);
                },
                success: function (data) {
                    if (data.status === '403') {
                        Swal.fire({
                            icon: "warning",
                            title: "Thông báo",
                            text: "Đã bị chặn không thể gửi tin nhắn!",
                        });
                        errorMessageCard(tempID);
                    } else if (data.status !== '200') {
                        errorMessageCard(tempID);
                        console.error(data.error || "Gửi bình chọn thất bại.");
                    } else {
                        // Handle success with permanent IDs
                        handlePollCreateSuccess(data, tempID);
                    }
                    bootstrap.Modal.getInstance(document.getElementById('addToDo')).hide();
                    pollForm.reset();
                },
                error: function (xhr, status, error) {
                    errorMessageCard(tempID);
                    console.error("Failed to send poll:", error);
                }
            });
        });
    });
</script>