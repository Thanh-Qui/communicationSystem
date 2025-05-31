<?php
    $seenIcon = (!!$seen ? 'check-double' : 'check');
    $timeAndSeen = "<span data-time='$created_at' class='message-time'>
            ".($isSender ? "<span class='fas fa-$seenIcon' seen'></span>" : '' )." <span class='time'>$timeAgo</span>
        </span>";

    $isJson = false;
    $pollData = null;

    if (is_string($message)) {
        $decoded = json_decode($message, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['type']) && $decoded['type'] === 'poll') {
            $isJson = true;
            $pollData = $decoded;

            // Lấy vote counts từ DB
            $voteCounts = DB::table('poll_votes')->where('message_id', $id)->select('option', DB::raw('count(*) as total'))->groupBy('option')->pluck('total', 'option')->toArray();

            $pollData['vote_counts'] = $voteCounts;

            // Lấy lựa chọn người dùng hiện tại
            $userVotes = DB::table('poll_votes')
                ->where('message_id', $id)
                ->where('user_id', auth()->id())
                ->pluck('option') // Lấy danh sách nhiều option
                ->toArray();

            $pollData['user_vote'] = $userVotes;
        }
    }

    // Thực hiện hiển thị tin nhắn sau khi trả lời
    $userIdReply = DB::table('ch_messages')->where('id', $isReply)->value('from_id');
    $userName = DB::table('users')->where('id', $userIdReply)->value('name');
    $MessageParent = DB::table('ch_messages')->where('id', $isReply)->value('body');
?>

@php
// Kiểm tra nếu tin nhắn là một URL
$isUrl = filter_var($message, FILTER_VALIDATE_URL);
// Kiểm tra nếu URL là một Google Maps URL
$isGoogleMapsUrl = strpos($message, 'https://www.google.com/maps?q=') === 0;
@endphp

<div class="message-card-wrapper @if($isSender) mc-sender @endif" id="chatMessages">
    @if($loadUserInfo)
    <div class="message-user" style="color: #007bff">
        <img src="{{$user->avatar}}"/>
        <p>{{$user->name}}</p>
    </div>
    @endif
    <div class="message-card @if($isSender) mc-sender @endif" data-id="{{ $id }}">
        {{-- Delete Message Button --}}
        @if ($isSender)
            <div class="actions">
                <i class="fas fa-trash delete-btn" data-id="{{ $id }}" title="delete"></i>
            </div>
            {{-- chuyển tiếp tin nhắn --}}
            <div class="actions">
                <i class="fas fa-share share-btn" title="share" data-to-id="{{ $id }}"></i>
            </div>
            {{-- Trả lời tin nhắn --}}
            @if ($message && !preg_match('/^>+$/', trim($message)) && !$isGoogleMapsUrl && !$isJson)
                <div class="actions">
                    <i class="fas fa-quote-right feedback-btn" data-id="{{ $id }}" title="reply"
                        onclick="replyToMessage('{{ $id }}', '{{ $message }}')"></i>
                </div>
            @endif
            {{-- Like tin nhắn --}}
            @if (!$isJson)
                <div class="actions">
                    <i class="fas fa-heart like-btn" data-id="{{ $id }}" title="like"></i>
                </div>
            @endif
            
        @endif
        {{-- Card --}}
        <div class="message-card-content">
            @if (@$attachment->type != 'image' || $message)
                <div class="message" id="message-{{$id}}">

                    @if ($isGoogleMapsUrl)
                        {{-- Hiển thị Google Maps nhúng nếu URL hợp lệ --}}
                        <div class="google-maps-embed">
                            <iframe src="{{ $message }}&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy">
                            </iframe>
                        </div>
                    @elseif ($isUrl)
                        <a href="{{ $message }}" target="_blank" class="message-link">
                            <span class="message-text">{{ $message }}</span>
                            <div class="message-icon">
                                <img src="{{ asset('images/iconmessage.png') }}" alt="Icon Message">
                            </div>
                        </a>
                        
                    @else
                        {{-- {!! ($message == null && $attachment != null && @$attachment->type != 'file') ? $attachment->title : nl2br(str_replace('>', '', $message)) !!} --}}
                        
                        {{-- bình chọn và tin nhắn --}}
                        @if ($isJson && $pollData['type'] === 'poll')
                            <div class="poll-box rounded bg-white text-dark mb-3 text-start" id="poll-{{ $id }}">
                                <!-- Header -->
                                <div class="modal-header flex-column align-items-start">
                                    <p style="font-size: 18px; font-weight: bold" class="modal-title m-0">
                                       {{ $pollData['title'] }}
                                    </p>
                                    <span style="font-size: 15px; color: rgb(137, 137, 137)">Chọn nhiều phương án</span>
                                    @if (isset($pollData['end_date']))
                                        <span style="font-size: 15px; color: rgb(137, 137, 137)">
                                            Thời gian kết thúc:
                                            {{ \Carbon\Carbon::parse($pollData['end_date'])->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span style="font-size: 15px; color: rgb(137, 137, 137)">Không có thời gian kết
                                            thúc</span>
                                    @endif

                                </div>

                                <!-- Body -->
                                <div class="modal-body scrollbar-custom" style="max-height: 150px; overflow: auto">
                                    <div class="poll-options list-group">
                                        @foreach ($pollData['options'] as $index => $option)
                                            @php
                                                $count = $pollData['vote_counts'][$option] ?? 0;
                                                $voted =
                                                    isset($pollData['user_vote']) &&
                                                    in_array($option, $pollData['user_vote']);
                                            @endphp
                                            <label style="cursor: pointer; background: #f1f2f6"
                                                class="list-group-item d-flex justify-content-between align-items-center mb-2 rounded {{ $voted ? 'bg-primary text-white' : '' }}">
                                                <div class="d-flex align-items-center">
                                                    <div style="flex-shrink: 0;">
                                                        <input type="checkbox" name="poll_{{ $id }}[]"
                                                            id="poll_{{ $id }}_{{ $index }}"
                                                            value="{{ $option }}" class="form-check-input me-2"
                                                            {{ $voted ? 'checked' : '' }}>
                                                    </div>
                                                    <span
                                                        style="padding-right: 7px ;font-size: 14px">{{ $option }}</span>
                                                </div>
                                                <span
                                                    class="badge {{ $voted ? 'bg-light text-dark' : 'bg-secondary' }}">{{ $count }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="p-2">
                                    <button style="width: 100%;" class="btn btn-outline-primary"
                                        onclick="submitPoll('{{ $id }}')">Bình chọn</button>
                                </div>
                            </div>
                        @else
                            @if (isset($isReply))
                                <div class="reply-message" onclick="scrollToMessage('{{ $isReply }}')" style="cursor: pointer">
                                    <p><b>{{ $userName }}</b></p>
                                    <span>{!! str_replace('>', '', $MessageParent) !!}</span>
                                </div>
                            @endif
                            {{-- Nội dung tin nhắn bình thường --}}
                            {!! ($message == null && $attachment != null && @$attachment->type != 'file') ? $attachment->title : nl2br(str_replace('>', '', $message)) !!}
                        @endif
                    @endif
                        
                    {!! $timeAndSeen !!}

                    {{-- {!! ($message == null && $attachment != null && @$attachment->type != 'file') ? $attachment->title : nl2br($message) !!}
                    {!! $timeAndSeen !!} --}}

                    {{-- If attachment is a file --}}
                    @if(@$attachment->type == 'file')
                        <a style="color: black; background-color: white" href="{{ route(config('chatify.attachments.download_route_name'), ['fileName'=>$attachment->file]) }}" class="file-download" onclick="return handleDownload(event, '{{ route(config('chatify.attachments.download_route_name'), ['fileName'=>$attachment->file]) }}')">
                            <span class="fas fa-file"></span> {!! html_entity_decode($attachment->title) !!}
                        </a>
                    @endif
                </div>
            @endif

            @php
                $fileExtension = pathinfo($attachment->file, PATHINFO_EXTENSION); // Lấy phần mở rộng của tệp
            @endphp
            @if(@$attachment->type == 'image')
                <div class="image-wrapper" style="text-align: {{$isSender ? 'end' : 'start'}}">
                    
                @if(in_array(strtolower($fileExtension), ['png', 'jpg', 'jpeg', 'gif']))
                <div class="image-wrapper" style="text-align: {{$isSender ? 'end' : 'start'}}">
                    <div class="image-file chat-image" style="background-image: url('{{ Chatify::getAttachmentUrl($attachment->file) }}')">
                        <div>{{ $attachment->title }}</div>
                    </div>

                </div>
                @elseif(in_array(strtolower($fileExtension), ['mp4','mp3']))
                <div class="video-wrapper" style="text-align: {{$isSender ? 'end' : 'start'}}">
                    <video width="100%" controls style="border-radius: 15px; text-align: center">
                        <source src="{{ Chatify::getAttachmentUrl($attachment->file) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    {{-- <div>{{ $attachment->title }}</div> --}}
                    <div style="margin-bottom: 5px;">
                        {!! $timeAndSeen !!}
                    </div>
                </div>
                @endif
                    
                </div>
            @endif

            @php
                $countGreaterThan = substr_count($message, '>');
            @endphp

            @if ($countGreaterThan > 0)
                <div class="reaction-wrapper" style="z-index: 1">
                    <div class="reaction">
                        <i class="fas fa-heart"></i>
                        <span style="margin-left: 5px">{{ $countGreaterThan }}</span>
                    </div>
                </div>
            @endif

        </div>

        @if (!$isSender && !$isJson)
            <div class="actions" >
                <i class="fas fa-heart like-btn" data-id="{{ $id }}"></i>
            </div>
            <div class="actions">
                <i class="fas fa-share share-btn" title="share" data-to-id="{{ $id }}"></i>
            </div>
            @if ($message && !preg_match('/^>+$/', trim($message)) && !$isGoogleMapsUrl)
                <div class="actions">
                    <i class="fas fa-quote-right feedback-btn" data-id="{{ $id }}" title="reply"
                        onclick="replyToMessage('{{ $id }}', '{{ $message }}')"></i>
                </div>
            @endif
        @endif
        
    </div>

</div>
