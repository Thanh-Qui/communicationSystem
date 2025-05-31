{{-- Modal tạo bình chọn --}}
<div class="modal fade" id="addToDo" tabindex="-1" aria-labelledby="addToDoLabel" aria-hidden="true" style="z-index: 1100;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="#" method="POST" id="pollForm">
        @csrf

        <div class="modal-header">
          <h3 class="modal-title" id="addToDoLabel">Tạo bình chọn</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>

          <div class="modal-body">
              <!-- Tiêu đề bình chọn -->
              <div class="mb-3">
              <label for="pollTitle" class="form-label">Tiêu đề bình chọn</label>
              <input type="text" class="form-control" id="pollTitle" name="title" placeholder="Nhập tiêu đề bình chọn..." required>
              </div>
  
              <!-- Danh sách lựa chọn -->
              <div class="mb-3">
              <label class="form-label">Lựa chọn</label>
              <div id="poll-options">
                  <input type="text" class="form-control mb-2" name="options[]" placeholder="Lựa chọn 1" required>
                  <input type="text" class="form-control mb-2" name="options[]" placeholder="Lựa chọn 2" required>
              </div>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addPollOption()">+ Thêm lựa chọn</button>
              </div>
  
              <!-- Ngày bắt đầu và kết thúc (tuỳ chọn) -->
              <div class="row">
              <div class="col">
                  <label for="date-end" class="form-label">Ngày kết thúc</label>
                  <input type="datetime-local" class="form-control" name="end_date" required>
              </div>
              </div>
          </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal chuyển tiếp  --}}
<div class="modal fade" id="shareMessage"  tabindex="-1" aria-labelledby="shareMessageLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" style="width: 100%">
      @csrf
      <input type="hidden" name="message_id" id="shareMessageId">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="shareMessageLabel">Chuyển tiếp</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="userSearch" class="form-label">Chọn người dùng (Nhóm)</label>
            <input type="text" id="userSearch" class="form-control" placeholder="Nhập tên người dùng" autocomplete="off">
            <div id="userSuggestions" class="list-group" style="display: none; position: absolute; z-index: 1000; width: 90%; max-height: 200px; overflow-y: auto;"></div>
            
            <input type="hidden" name="list_data" id="selectedListData">
            <!-- Danh sách đã chọn -->
            <ul id="selectedListDisplay" class="mt-2 list-group"></ul>

          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="shareMessageButton" data-share-url="{{ route('share.message') }}">Chuyển tiếp</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
    function isDelete(){
        var confirmation = confirm("Bạn có muốn xoá thông tin này không?");
        if (confirmation == true) {
            // alert("Bạn đã xoá loại món này");
            return true;
        }else{
            // alert("Không xoá loại món này");
            return false;
        }
    }

    function confirmation(e) {
      e.preventDefault();

      var urlToRedirect = e.currentTarget.getAttribute('href');

      console.log(urlToRedirect);

      swal({
        title: "Bạn có muốn xoá thông tin này?",
        text: "This delete will be parmanet",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })

      .then((willCancel)=>{
        if (willCancel) {
          window.location.href = urlToRedirect;
        }
      });

    }

    function confirmationStar(e) {
      e.preventDefault();

      var urlToRedirect = e.currentTarget.getAttribute('href');

      console.log(urlToRedirect);

      swal({
        title: "Bạn có muốn thực hiện điều này?",
        text: "Do you want to do this?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })

      .then((willCancel)=>{
        if (willCancel) {
          window.location.href = urlToRedirect;
        }
      });

    }

    function showNotification(message, duration = 3000) {
        const notification = document.getElementById('notification');
        const notificationText = document.getElementById('notification-text');
        notificationText.textContent = message;
        notification.style.display = 'block';

        setTimeout(() => {
            notification.style.display = 'none';
        }, duration);
    }

</script>

<script src="https://js.pusher.com/7.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js"></script>
<script >
    // Gloabl Chatify variables from PHP to JS
    window.chatify = {
        name: "{{ config('chatify.name') }}",
        sounds: {!! json_encode(config('chatify.sounds')) !!},
        allowedImages: {!! json_encode(config('chatify.attachments.allowed_images')) !!},
        allowedFiles: {!! json_encode(config('chatify.attachments.allowed_files')) !!},
        maxUploadSize: {{ Chatify::getMaxUploadSize() }},
        pusher: {!! json_encode(config('chatify.pusher')) !!},
        pusherAuthEndpoint: '{{route("pusher.auth")}}'
    };
    window.chatify.allAllowedExtensions = chatify.allowedImages.concat(chatify.allowedFiles);

    window.sendPollRoute = "{{ route('send.poll') }}";
    window.pollVoteRoute = "{{ route('poll.vote') }}";
    window.csrfToken = "{{ csrf_token() }}";
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/chatify/utils.js') }}"></script>
<script src="{{ asset('js/chatify/code.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }} "></script>
<script src="{{ asset('sendFileDrap.js') }}"></script>

<style>
  #botmanWidgetRoot > div {
        min-width: 100px !important;
        min-height: 80px !important;
        margin-bottom: 43px;
    }

    .desktop-closed-message-avatar {
      top: 5px !important;
    }

    .mobile-closed-message-avatar {
      top: 5px !important;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js"></script>
    <script>
        var botmanWidget = {
            frameEndpoint: '/botman/chat',
            title: 'Trò chuyện với ChatBot',
            introMessage: 'Chào bạn! Tôi là trợ lý ảo của hệ thống này. Tôi có thể giúp gì cho bạn hôm nay?',
            placeholderText: 'Nhập tin nhắn tại đây...',
            mainColor: '#F79F1F',
            bubbleBackground: '#dff9fb',
            aboutText: 'ChatBot',
            bubbleAvatarUrl: 'https://img.icons8.com/?size=60&id=9Otd0Js4uSYi&format=png',

        };
    </script>


<script src="https://cdn.jsdelivr.net/npm/mark.js/dist/mark.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script src="{{ asset('custom.js') }}"></script>
<script src="{{ asset('sendForm.js') }}"></script>
