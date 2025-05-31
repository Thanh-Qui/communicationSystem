<link rel="stylesheet" href="{{ asset('stylecss.css') }}">

<?php
    $isGroup = isset($channel->owner_id);
?>
<nav>
    <p>{{isset($channel->owner_id) ? 'Group Details' : 'User Details'}}</p>
    <a href="#"><i class="fas fa-times"></i></a>
</nav>

<div class="avatar avatar-channel av-l chatify-d-flex"></div>
<p class="info-name">{{ config('chatify.name') }}</p>
@if($isGroup)
    <div style="max-width: 250px; margin: auto">
        <h4 style="text-align: center; margin-bottom: 10px; margin-top: 30px; font-weight: normal; font-size: 14px; color: #007bff">Users in this group</h4>
        <div class="app-scroll users-list">
            @foreach($channel->users as $user)
            <div class="user-item" style="display: flex; align-items: center; justify-content: space-between;">
                {!! view('Chatify::layouts.listItem', ['get' => 'user_search_item', 'user' => Chatify::getUserWithAvatar($user)])->render() !!}
                
                {{-- kiểm tra quyền xóa người dùng là trường nhóm --}}
                @if ($channel->owner_id == $users->id && $user->id != $users->id)
                    <a onclick="confirmation(event)" href="{{ url('remove_group', [$user->id, $channel->id]) }}" style="color: red; text-decoration: none; margin-left: 10px;"><i class="fa-solid fa-xmark"></i></a>
                    <a onclick="confirmationStar(event)" href="{{ url('channel/toggle-permission', [$channel->id, $user->id]) }}" 
                        style="color: yellow; text-decoration: none; margin-left: 10px;">
                        @php
                            $canAddUsers = DB::table('ch_channel_user')->where('channel_id', $channel->id)->where('user_id', $user->id)->value('can_add_users');
                        @endphp
                    
                        @if($canAddUsers == 1)
                            <i class="fa-solid fa-star"></i>
                        @elseif($canAddUsers == 0)
                            <i class="fa-regular fa-star"></i>
                        @endif
                    </a>
                    

                @endif
            </div>
            @endforeach
            
        </div>
    </div>
@endif

<div class="messenger-infoView-btns">
    @if($isGroup && $channel && $channel->owner_id === Auth::user()->id || DB::table('ch_channel_user')->where('channel_id', $channel->id)->where('user_id', Auth::id())->value('can_add_users'))

        <hr>
        <h4 style="text-align: center; margin-bottom: 10px; margin-top: 30px; font-weight: normal; font-size: 14px; color: #007bff">Add user to group</h4>

        {{-- Chức năng tìm kiếm và thêm người dùng vào nhóm --}}
        <!-- Form tìm kiếm người dùng -->
        <form action="{{url('chatify/' . $channel->id . '/search')}}" method="post" id="searchUserForm" style="margin: 15px" onsubmit="event.preventDefault(); searchUsers();">
            @csrf
            <input type="text" name="username" placeholder="Search user..." id="username" autocomplete="off">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        {{-- <div style="background: whitesmoke; border-radius: 7px; margin: 15px">
            <!-- Kết quả tìm kiếm -->
            <div id="searchResults" class="form-control" ></div>

            <!-- Hiển thị người dùng đã chọn -->
            <div id="selectedUser" style="margin-top: 10px; font-weight: bold;"></div>
        </div> --}}

        {{-- Hiển thị danh sách người dùng --}}
        <div style="background: whitesmoke; border-radius: 7px; margin: 15px">
            <!-- Kết quả tìm kiếm (Ẩn mặc định) -->
            <div id="searchResults" class="form-control" style="display: none;"></div>
        </div>

        <ul id="selectedUsersList" class="list-group mx-3 mb-3" style="max-height: 200px; overflow-y: auto;"></ul>


        {{-- Form thêm người dùng vào nhóm --}}
        <form action="{{url('chatify/' . $channel->id . '/join')}}" method="post" id="addUserForm">
            @csrf
            <input type="hidden" name="user_ids" id="user_ids">
            <input class="btn-addUser" type="submit" name="" id="" value="Add User" style="font-size: 14px; font-family: sans-serif">
        </form>

        {{-- <a href="#" class="danger delete-group" style="margin-bottom: 15px">Delete Group</a> --}}
        {{-- Chỉ hiển thị "Delete Group" cho trưởng nhóm --}}
        @if($channel->owner_id === Auth::user()->id)
            <a href="#" class="danger delete-group" style="margin-bottom: 15px">Delete Group</a>
        @elseif (DB::table('ch_channel_user')->where('channel_id', $channel->id)->where('user_id', Auth::id())->value('can_add_users'))
            <a href="#" class="danger leave-group">Leave Group</a>
        @endif
    @elseif($isGroup)
        <a href="#" class="danger leave-group">Leave Group</a>
    @else
        {{-- <a href="#" class="danger delete-conversation">Delete Conversation</a> --}}
    @endif
    
</div>

{{-- shared photos --}}
{{-- <div class="messenger-infoView-shared">
    <p class="messenger-title"><span>Shared Photos</span></p>
    <div class="shared-photos-list"></div>
</div> --}}

{{-- 
<div>
    <p style="text-align: center; margin-bottom: 10px; margin-top: 30px; font-weight: normal; font-size: 14px; color: #007bff"> Shared Photos </p>
    <div class="photos-lists"></div>
</div> --}}


<script>

    let selectedUsers = [];

    function searchUsers() {
        const username = document.getElementById('username').value;

        if (username.trim() === '') {
            document.getElementById('searchResults').style.display = 'none';
            return;
        }

        fetch(`{{ url('chatify/' . $channel->id . '/search') }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username })
        })
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById('searchResults');
            resultsDiv.innerHTML = '';

            if (data.users.length > 0) {
                resultsDiv.style.display = 'block';

                data.users.forEach(user => {
                    const userDiv = document.createElement('div');
                    userDiv.textContent = `${user.name} (${user.email})`;
                    userDiv.style.cursor = 'pointer';
                    userDiv.className = 'user-item';

                    userDiv.addEventListener('click', function () {
                        if (!selectedUsers.find(u => u.id === user.id)) {
                            selectedUsers.push(user);
                            updateSelectedUsers();
                        }

                        resultsDiv.style.display = 'none';
                        document.getElementById('username').value = '';
                    });

                    resultsDiv.appendChild(userDiv);
                });
            } else {
                resultsDiv.innerHTML = '<p>Không tìm thấy người dùng</p>';
                resultsDiv.style.display = 'block';
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function updateSelectedUsers() {
        const selectedList = document.getElementById('selectedUsersList');
        const userIdsInput = document.getElementById('user_ids');

        selectedList.innerHTML = '';

        selectedUsers.forEach(user => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.textContent = `${user.name} (${user.email})`;

            const removeBtn = document.createElement('button');
            removeBtn.className = 'btn btn-danger btn-sm';
            removeBtn.innerHTML = '&times;';
            removeBtn.onclick = () => {
                selectedUsers = selectedUsers.filter(u => u.id !== user.id);
                updateSelectedUsers();
            };

            li.appendChild(removeBtn);
            selectedList.appendChild(li);
        });

        // Cập nhật hidden input với danh sách user_id
        userIdsInput.value = JSON.stringify(selectedUsers.map(u => u.id));
    }


</script>

