{{-- -------------------- Saved Messages -------------------- --}}
@if($get == 'saved')
    <table
        class="messenger-list-item {{Auth::user()->channel_id ? 'contact-item' :'search-item'}}"
        data-channel="{{ Auth::user()->channel_id }}"
        data-user="{{ Auth::user()->id }}"
    >
        <tr data-action="0">
            {{-- Avatar side --}}
            <td>
            <div class="saved-messages avatar av-m">
                <span class="far fa-bookmark"></span>
            </div>
            </td>
            {{-- center side --}}
            <td>
                <p>Saved Messages <span>You</span></p>
                <span>Save messages secretly</span>
            </td>
        </tr>
    </table>

    {{-- kho lưu trữ tài liệu cá nhân --}}
    <a href="{{ route('myStorage') }}" style="text-decoration: none">
        <table style="width: 100%;" class="myStorage-light">
            <tr data-action="1">
                {{-- Avatar side --}}
                <td style="padding-left: 10px">
                    <div class="saved-messages avatar av-m">
                        <i class="far fa-circle"></i>
                    </div>
                </td>
                {{-- center side --}}
                <td style="padding: 10px; color: white; font-size: 14px; width: 100%">
                    <p style="display: flex; justify-content: space-between; margin: 0;">
                        <span>Local Storage</span>
                        <span style="font-size: 12px">You</span>
                    </p>
                </td>
            </tr>
        </table>
    </a>
    
@endif

{{-- -------------------- Contact User -------------------- --}}
 @if($get == 'contact-user' && !!$lastMessage)
<?php
$lastMessageBody = mb_convert_encoding($lastMessage->body, 'UTF-8', 'UTF-8');
// $lastMessageBody = strlen($lastMessageBody) > 30 ? mb_substr($lastMessageBody, 0, 30, 'UTF-8').'..' : $lastMessageBody;

$isPoll = false;
$pollData = json_decode($lastMessageBody, true);
if (json_last_error() === JSON_ERROR_NONE && isset($pollData['type']) && $pollData['type'] === 'poll') {
    $isPoll = true;
}
if (!$isPoll) {
    $lastMessageBody = strlen($lastMessageBody) > 30 ? mb_substr($lastMessageBody, 0, 30, 'UTF-8').'..' : $lastMessageBody;
}
?>
<table class="messenger-list-item contact-item" data-channel="{{ $channel->id }}">
    <tr data-action="0">
        {{-- Avatar side --}}
        <td style="position: relative">
            @if($user->active_status)
                @if ($user->active_status == 2)
                    <span></span>
                @else
                    <span class="activeStatus"></span>   
                @endif
                
            @endif
        <div class="avatar av-m"
        style="background-image: url('{{ $user->avatar }}');">
        
        </div>
        </td>
        {{-- center side --}}
        <td>
        <p>
            {{ strlen($user->name) > 12 ? trim(substr($user->name,0,12)).'..' : $user->name }}
            <span class="contact-item-time" data-time="{{$lastMessage->created_at}}">{{ $lastMessage->timeAgo }}</span>
        </p>
        <span>
            {{-- Last Message user indicator --}}
            {!!
                $lastMessage->from_id == Auth::user()->id
                ? '<span class="lastMessageIndicator">You :</span>'
                : ''
            !!}
            {{-- Last message body --}}
            @if($lastMessage->attachment == null)
                {{-- THêm điều kiện hiển thị định vị thay vì gửi đoạn gg --}}
                @if (str_contains($lastMessageBody, 'https://www.google.com/maps?'))
                    <span>Gửi định vị</span>
                @else
                    @if($isPoll)
                        <span class="fas fa-poll"></span> Bình chọn
                    @else
                        {!! str_replace('>', '', $lastMessageBody) !!}
                    @endif
                    {{-- {!!
                        str_replace('>', '', $lastMessageBody) 
                    !!} --}}
                @endif
            {{-- {!!
                str_replace('>', '', $lastMessageBody) 
            !!} --}}
            @else
            <span class="fas fa-file"></span> Attachment
            @endif
        </span>
        {{-- New messages counter --}}
            {!! $unseenCounter > 0 ? "<b>".$unseenCounter."</b>" : '' !!}

            
        </td>

    </tr>
</table>
@endif

{{-- -------------------- Contact Group -------------------- --}}
@if($get == 'contact-group' && !!$lastMessage)
<?php
$lastMessageBody = mb_convert_encoding($lastMessage->body, 'UTF-8', 'UTF-8');
// $lastMessageBody = strlen($lastMessageBody) > 30 ? mb_substr($lastMessageBody, 0, 30, 'UTF-8').'..' : $lastMessageBody;

$isPoll = false;
$pollData = json_decode($lastMessageBody, true);
if (json_last_error() === JSON_ERROR_NONE && isset($pollData['type']) && $pollData['type'] === 'poll') {
    $isPoll = true;
}
if (!$isPoll) {
    $lastMessageBody = strlen($lastMessageBody) > 30 ? mb_substr($lastMessageBody, 0, 30, 'UTF-8').'..' : $lastMessageBody;
}
?>
<table class="messenger-list-item contact-item" data-channel="{{ $channel->id }}">
    <tr data-action="0">
        {{-- Avatar side --}}
        <td style="position: relative">
            <div class="avatar av-m"
                 style="background-image: url('{{ $channel->avatar }}');">
            </div>
        </td>
        {{-- center side --}}
        <td>
            <p>
                {{ strlen($channel->name) > 12 ? trim(substr($channel->name,0,12)).'..' : $channel->name }}
                <span class="contact-item-time" data-time="{{$lastMessage->created_at}}">{{ $lastMessage->timeAgo }}</span>
            </p>
            <span>
                {{-- Last Message user indicator --}}
                {!!
                    $lastMessage->from_id == Auth::user()->id
                    ? '<span class="lastMessageIndicator">You :</span>'
                    : '<span class="lastMessageIndicator">'. $lastMessage->user_name .' :</span>'
                !!}
                {{-- Last message body --}}
                @if($lastMessage->attachment == null)
                    {{-- THêm điều kiện hiển thị định vị thay vì gửi đoạn gg --}}
                    @if (str_contains($lastMessageBody, 'https://www.google.com/maps?'))
                        <span>Gửi định vị</span>
                    @else
                        @if($isPoll)
                            <span class="fas fa-poll"></span> Bình chọn
                        @else
                            {!! str_replace('>', '', $lastMessageBody) !!}
                        @endif
                        {{-- {!!
                            str_replace('>', '', $lastMessageBody) 
                        !!} --}}
                    @endif
                {{-- {!!
                    str_replace('>', '', $lastMessageBody) 
                !!} --}}
                @else
                    <span class="fas fa-file"></span> Attachment
                @endif
            </span>
            {{-- New messages counter --}}
            {!! $unseenCounter > 0 ? "<b>".$unseenCounter."</b>" : '' !!}
        </td>
    </tr>
</table>
@endif

{{-- -------------------- Search Item -------------------- --}}
@if($get == 'search_item')
<table class="messenger-list-item search-item" data-user="{{ $user->id }}">
    <tr data-action="0">
        {{-- Avatar side --}}
        <td>
            <div class="avatar av-m"
            style="background-image: url('{{ $user->avatar }}');">
            </div>
        </td>
        {{-- center side --}}
        <td>
            <p>{{ strlen($user->name) > 12 ? trim(substr($user->name,0,12)).'..' : $user->name }}</p>
        </td>
    </tr>
</table>
@endif

{{-- -------------------- Modal Search Item -------------------- --}}
@if($get == 'user_search_item')
    <table class="user-list-item" data-user="{{ $user->id }}">
        <tr data-action="0">
            {{-- Avatar side --}}
            <td>
                <div class="avatar av-s"
                     style="background-image: url('{{ $user->avatar }}');">
                </div>
            </td>
            {{-- center side --}}
            <td>
                <p>{{ strlen($user->name) > 12 ? trim(substr($user->name,0,12)).'..' : $user->name }}</p>
            </td>
            
            {{-- <td>
                <a href="{{url('remove_group', $user->id)}}" style="color: red; text-decoration: none">xóa</a>
            </td> --}}
        </tr>
    </table>
@endif

{{-- -------------------- Shared photos Item -------------------- --}}
@if($get == 'sharedPhoto')
    @if($type == 'image')
        <div class="shared-photo chat-image" style="background-image: url('{{ $image }}')">
        </div>
    @elseif($type == 'video')
        <div class="shared-video">
            <video controls>
                <source src="{{ $image }}" type="video/{{ pathinfo($image, PATHINFO_EXTENSION) }}">
                Your browser does not support the video tag.
            </video>
        </div>
    @elseif($type == 'audio')
        <div class="shared-audio">
            <audio controls>
                <source src="{{ $image }}" type="audio/{{ pathinfo($image, PATHINFO_EXTENSION) }}">
                Your browser does not support the audio element.
            </audio>
        </div>
    @endif
@endif

@if($get == 'sharedFile')
    <div class="shared-file chat-file">
        <a style="color: black" href="{{ route(config('chatify.attachments.download_route_name'), ['fileName' => basename($file['new_name'])]) }}" target="_blank">{{ html_entity_decode($file['old_name'])  }}</a>
    </div>
@endif





