@include('Chatify::layouts.headLinks')
<link rel="stylesheet" href="{{ asset('stylecss.css') }}">
<div class="messenger">
    {{-- ----------------------Users/Groups lists side---------------------- --}}
    <div class="messenger-listView {{ !!$channel_id ? 'conversation-active' : '' }}">
        {{-- Header and search bar --}}
        <div class="m-header">
            <nav>
                <a href="#"><i class="fas fa-inbox"></i> <span class="messenger-headTitle">MESSAGES</span> </a>
                {{-- header buttons --}}
                <nav class="m-header-right">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();" >
                            <i style="margin-bottom: -10px" class="fas fa-sign-out-alt"></i>
                        </x-dropdown-link>
                    </form>

                    <a class="letter" href="{{ url('system-notification') }}">
                        @php
                            $id_user = Auth::id();
                            $totalNews = DB::table('news')->count();

                            $newsUser = DB::table('news_users')->where('id_user', $id_user)->count();
                            
                            $countNews = $totalNews - $newsUser;
                        @endphp
                        <p id="countNews">{{$countNews}}</p>
                        <i class="fas fa-envelope"></i>
                    </a>
                    

                    <a href="{{ route('qr.show') }}"><i class="fas fa-qrcode"></i></a>
                    <a href="{{route('profile.edit')}}"><i class="fas fa-address-card"></i></a>
                    <a href="#"><i class="fas fa-users group-btn"></i></a>
                    <a href="#"><i class="fas fa-cog settings-btn"></i></a>
                    <a href="#" class="listView-x"><i class="fas fa-times"></i></a>
                </nav>
            </nav>
            {{-- Search input --}}
            <input type="text" class="messenger-search" placeholder="Search" style="width: 85%" />
            <span class="fas fa-microphone" id="startRecordingSearch" style="cursor: pointer"></span>

            {{-- Tabs --}}
            {{-- <div class="messenger-listView-tabs">
                <a href="#" class="active-tab" data-view="users">
                    <span class="far fa-user"></span> Contacts</a>
            </div> --}}
        </div>
        {{-- tabs and lists --}}
        <div class="m-body contacts-container">
           {{-- Lists [Users/Group] --}}
           {{-- ---------------- [ User Tab ] ---------------- --}}
           <div class="show messenger-tab users-tab app-scroll" data-view="users">
               {{-- Favorites --}}
               <div class="favorites-section">
                <p class="messenger-title"><span>Favorites</span></p>
                <div class="messenger-favorites app-scroll-hidden"></div>
               </div>
               {{-- Saved Messages --}}
               <p class="messenger-title"><span>Your Space</span></p>
               {!! view('Chatify::layouts.listItem', ['get' => 'saved']) !!}
               {{-- Contact --}}
               <p class="messenger-title"><span>All Messages</span></p>
               <div class="listOfContacts" style="width: 100%;height: calc(100% - 272px);position: relative;"></div>
           </div>
             {{-- ---------------- [ Search Tab ] ---------------- --}}
           <div class="messenger-tab search-tab app-scroll" data-view="search">
                {{-- items --}}
                <p class="messenger-title"><span>Search</span></p>
                <div class="search-records">
                    <p class="message-hint center-el"><span>Type to search..</span></p>
                </div>
             </div>
        </div>
    </div>

    {{-- ----------------------Messaging side---------------------- --}}
    <div class="messenger-messagingView">
        {{-- header title [conversation name] amd buttons --}}
        <div class="m-header m-header-messaging">
            <nav class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                {{-- header back button, avatar and user name --}}
                <div class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">

                    <a href=""{{ route('chatify',) }} class="show-listView"><i class="fas fa-arrow-left"></i></a>
                    {{-- <a href="#" class="show-listView"><i class="fas fa-arrow-left"></i></a> --}}
                    <div class="avatar av-s header-avatar" style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;">
                    </div>
                    <a href="#" class="user-name">{{ config('chatify.name') }}</a>
                </div>
                {{-- header buttons --}}
                {{-- <a href="/"><i class="fas fa-home"></i></a> --}}

                <nav class="m-header-right">
                    <a href="#" class="add-to-favorite"><i class="fas fa-star"></i></a>

                    {{-- Hiển thị hình ảnh đã gửi --}}
                    {{-- <a href="#" class="show-cloud"><i class="fas fa-cloud-upload-alt"></i></a>
                    <div class="image-preview" style="display: none;">
                        <div class="messenger-infoView-shared">
                            <p class="messenger-title"><span>Shared Photos</span></p>
                            <div class="shared-photos-list"></div>
                        </div>
                    </div> --}}
                    

                    {{-- hiển thị hình ảnh lưu trữ --}}
                    <button type="button" style="border: none; background: transparent" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </button>
                    
                    {{-- form hiển thị các hình ảnh và file được lưu trữ --}}
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="false">
                        <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Shared Photos Or Files</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="messenger-infoView-shared">
                                    {{-- <p class="messenger-title"><span>Shared Photos</span></p> --}}
                                    <div class="shared-photos-list share-display"></div>
                                    <hr>
                                    <div class="shared-files-list share-display"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                        </div>
                    </div>

                    {{-- Chức năng chọn hình ảnh background cho giao diện --}}
                    <form action="{{ route('updateBackground', $channel_id) }}" method="POST" enctype="multipart/form-data" id="backgroundForm">
                        @csrf
                        <div class="background-upload">
                            <input type="file" name="background" id="background_input" class="background_btn">
                        </div>
                    </form>


                    {{-- chặn người dùng --}}
                    <a href="#" class="add-to-lock"><i class="fas fa-lock"></i></a>


                    {{-- Chức năng tìm kiếm --}}
                    <a href="#" id="search-icon"><i class="fas fa-search"></i></a>
                    <div id="search-container" class="hidden">
                        <input type="text" placeholder="Tìm kiếm..." id="searchKeyword" />
                        <button onclick="highlightText()">search</button>
                        <div class="custom-search">
                            <a href="#" onclick="navigateHighlight(-1)"><i class="fa-solid fa-up-long"></i></a>
                            <a href="#" onclick="navigateHighlight(1)"><i class="fa-solid fa-down-long"></i></a>
                            <span id="keywordCount" style="color: #bdc3c7;font-size: 10px;font-family: sans-serif;"></span>
                        </div>
                        
                    </div>


                    <a href="#" class="show-infoSide"><i class="fas fa-info-circle"></i></a>
                </nav>
            </nav>
            {{-- Internet connection --}}
            <div class="internet-connection">
                <span class="ic-connected">Connected</span>
                <span class="ic-connecting">Connecting...</span>
                <span class="ic-noInternet">No internet access</span>
            </div>
        </div>

        {{-- Hiển thị giao diện background cho kênh trò chuyện --}}
        @php
            $channel = DB::table('ch_channels')->where('id', $channel_id)->first();
            $backgroundImage = null;

            // Kiểm tra nếu channel tồn tại và có backgroundChannel
            if ($channel && $channel->id == $channel_id && $channel->backgroundChannel) {
                $background = json_decode($channel->backgroundChannel);
                if ($background && isset($background->new_name)) {
                    $path = 'storage/attachments/' . $background->new_name;
                    // Kiểm tra file tồn tại
                    if (file_exists(public_path($path))) {
                        $backgroundImage = $path;
                    }
                }
            }
            
        @endphp

        {{-- Messaging area --}}
        <div class="m-body messages-container app-scroll custombg" id="content" style="background: url('{{ $backgroundImage ? asset($backgroundImage) : '' }}'); background-size: cover">
            <div class="messages">
                {{-- <p class="message-hint center-el"><span>Please select a chat to start messaging</span></p> --}}
                <p class="message-hint center-el"><span>Welcome back, {{$users->name}} <i class="fa-solid fa-sun" style="color: #f1c40f"></i>
                     <br>Hope today brings you progress and peace of mind!</span>
                </p>
            </div>
            {{-- Typing indicator --}}
            <div class="typing-indicator">
                <div class="message-card typing">
                    <div class="message">
                        <span class="typing-dots">
                            <span class="dot dot-1"></span>
                            <span class="dot dot-2"></span>
                            <span class="dot dot-3"></span>
                        </span>
                    </div>
                </div>
            </div>

        </div>
        {{-- Send Message Form --}}
        @include('Chatify::layouts.sendForm')
    </div>

    {{-- ---------------------- Info side ---------------------- --}}
    <div class="messenger-infoView app-scroll">
        <nav>
            
            <p></p>
            <a href="#"><i class="fas fa-times"></i>
                
            </a>
        </nav>
        
    </div>


</div>
    
    
</div>
<script>
    document.getElementById('background_input').addEventListener('change', function() {
        // Khi người dùng chọn một file, tự động gửi form
        document.getElementById('backgroundForm').submit();
    });
    

    document.addEventListener('DOMContentLoaded', () => {
        const startRecordingSearch = document.getElementById('startRecordingSearch'); // Nút microphone
        const searchInput = document.querySelector(".messenger-search"); // Input search
        let recognitionSearch;
        let isRecordingSearch = false; // Theo dõi trạng thái

        // Kiểm tra hỗ trợ nhận diện giọng nói
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            recognitionSearch = new SpeechRecognition();
            recognitionSearch.lang = 'vi-VN'; // Đặt ngôn ngữ là Tiếng Việt
            recognitionSearch.interimResults = false; // Chỉ lấy kết quả cuối cùng
            recognitionSearch.continuous = false; // Không nhận dạng liên tục

            // Xử lý khi nhấn nút microphone
            startRecordingSearch.addEventListener('click', () => {
                if (isRecordingSearch) {
                    recognitionSearch.stop(); // Dừng ghi âm
                    showNotification('Đã dừng nhận diện giọng nói.');
                } else {
                    recognitionSearch.start(); // Bắt đầu ghi âm
                    showNotification('Bắt đầu nhận diện giọng nói.');
                }
                isRecordingSearch = !isRecordingSearch; // Đổi trạng thái
            });

            // Khi nhận được kết quả
            recognitionSearch.onresult = (event) => {
                // if (event.results.length > 0) {
                //     const speechToText = event.results[0][0].transcript; // Lấy kết quả
                //     searchInput.value = speechToText; // Gán vào input
                //     showNotification(`Nhận diện xong: ${speechToText}`);
                // }

                let speechToText = event.results[0][0].transcript;
                // Loại bỏ dấu chấm ở cuối câu nếu có
                if (speechToText.endsWith('.')) {
                    speechToText = speechToText.slice(0, -1);  // Loại bỏ dấu chấm
                }

                // Hiển thị vào textarea giống như bạn nhập văn bản
                searchInput.value = speechToText;  // Gán giá trị cho textarea
                searchInput.dispatchEvent(new Event('keyup'));
                showNotification(`Nhận diện xong: ${speechToText}`);
            };

            // Xử lý khi không có giọng nói
            recognitionSearch.onspeechend = () => {
                recognitionSearch.stop(); // Dừng nhận diện
                isRecordingSearch = false;
                showNotification('Kết thúc nhận diện giọng nói.');
            };

            // Xử lý lỗi
            recognitionSearch.onerror = (event) => {
                console.error("Lỗi nhận diện:", event.error);
                showNotification(`Lỗi: ${event.error}`);
                isRecordingSearch = false; // Đặt lại trạng thái
            };
        } else {
            showNotification('Trình duyệt không hỗ trợ nhận diện giọng nói.');
            startRecordingSearch.style.display = 'none'; // Ẩn nút microphone nếu không hỗ trợ
        }

    });


//     $('#myModal').modal({
//     backdrop: false
// });


</script>

@include('Chatify::layouts.modals')
@include('Chatify::layouts.footerLinks')

{{-- <script>
    function updateNewsCount() {
        $.ajax({
            url: "{{ url('/count-news') }}", // Gọi API
            type: "GET",
            success: function(response) {
                $("#countNews").text(response.count); // Cập nhật số lượng
            }
        });
    }

    // Cập nhật mỗi 5 giây
    setInterval(updateNewsCount, 5000);
</script> --}}

