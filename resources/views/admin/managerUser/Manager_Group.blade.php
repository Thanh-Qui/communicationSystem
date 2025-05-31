<x-layout-admin>
    <div class="page-wrapper">

        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Danh sách nhóm người dùng </h4>
                    <h6>Tổng danh sách nhóm người dùng</h6>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-top">
                        <div class="search-set">

                            <div class="search-input">
                                <a class="btn btn-searchset">
                                    <img src="{{ asset('assets/img/icons/search-white.svg') }} " alt="img">
                                </a>
                                {{-- Form tìm kiếm người dùng --}}
                                <form action="{{ url('searchGroup') }}">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label>
                                            <input type="search" name="searchGroup" id="searchGroup" data-search-group="{{ url('searchGroup') }}" class="form-control form-control-sm" placeholder="Search..." aria-controls="DataTables_Table_0">
                                        </label>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="wordset">
                            <ul>

                                <li>
                                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="print" aria-label="print">
                                        <img src="assets/img/icons/printer.svg" alt="img">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
    
                    <div class="table-responsive">
                        <div  class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <table class="table datanew no-footer" role="grid">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_asc" tabindex="0" rowspan="1" colspan="1" aria-sort="ascending" >
                                            SL
                                        </th>
                                        <th class="sorting" >Tên nhóm</th>
                                        <th class="sorting" >Số lượng thành viên</th>
                                        <th class="sorting" >Ảnh đại diện nhóm</th>

                                        <th class="sorting" >Xem thành viên</th>
                                    </tr>
                                </thead>
                                <tbody id="groupTableBody">
                                    
                                    @php
                                        $sn = ($countGroup->currentPage() - 1) * $countGroup->perPage() + 1;
                                    @endphp
                                    @foreach ($countGroup as $data)
                                        <tr class="odd">
                                            <td>{{$sn++}}</td>
                                            <td>{{$data->name}}</td>
                                            <td>{{$data->member_count}}</td>
                                            <td>
                                                @if ($data->avatar != 'avatar.png')
                                                   <img style="height: 50px; width: 50px" src="{{ asset('storage/channels-avatar/' . $data->avatar) }}" alt=""> 
                                                @else
                                                    <img style="height: 50px; width: 50px" src="{{ asset('storage/users-avatar/avatar.png') }}" alt=""> 
                                                @endif
                                                
                                            </td>

                                            <td>
                                                <a href="javascript:void(0);" onclick="showMembers('{{ $data->id }}')">
                                                    <i class="fa-solid fa-eye memberIconClick"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                </tbody>
                            </table>

                           
                        </div>
                    </div>

                    <div id="" style="display: flex; justify-content: center; align-items: center; margin-top: 15px">
                        {{$countGroup->onEachSide(1)->withPath('group_manager')->links('pagination::bootstrap-4', ['pageName' => 'group_page'])}}
                    </div>
                </div>
            </div>


            <div class="page-header">
                <div class="page-title">
                    <h6>Tổng danh sách cuộc trò chuyện</h6>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-top">
                        <div class="search-set">

                            <div class="search-input">
                                <a class="btn btn-searchset">
                                    <img src="{{ asset('assets/img/icons/search-white.svg') }} " alt="img">
                                </a>
                                {{-- Form tìm kiếm người dùng --}}
                                <form action="{{ url('conversationGroup') }}">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label>
                                            <input type="search" name="searchConversation" id="searchConversation" value="{{request()->get('searchConversation')}}" class="form-control form-control-sm" placeholder="Search..." aria-controls="DataTables_Table_0">
                                        </label>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="wordset">
                            <ul>

                                <li>
                                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="print" aria-label="print">
                                        <img src="assets/img/icons/printer.svg" alt="img">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
    
                    <div class="table-responsive">
                        <div  class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <table class="table datanew no-footer" role="grid">
                                <thead>
                                    <tr role="row">
                                        {{-- <th class="sorting_asc" tabindex="0" rowspan="1" colspan="1" aria-sort="ascending" >
                                            SL
                                        </th> --}}
                                        <th class="sorting" >Tên nhóm</th>
                                        <th class="sorting" >Người gửi</th>
                                        <th class="sorting" >Tin nhắn</th>

                                        <th class="sorting" >Nội dung đa phương tiện</th>
                                        <th class="sorting" >Thời gian gửi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($messages))
                                        @foreach ($messages as $message)
                                            @php
                                                $isPoll = false;
                                                $pollData = json_decode($message->body, true);
                                                if (json_last_error() === JSON_ERROR_NONE && isset($pollData['type']) && $pollData['type'] === 'poll') {
                                                    $isPoll = true;
                                                }
                                                
                                            @endphp
                                            <tr>
                                                <td>
                                                    {{$message->name}}
                                                </td>
                                                <td>
                                                    {{$message->fromUser->name}}
                                                </td>
                                                <td>
                                                    @if ($isPoll)
                                                        <p>Bình chọn</p>
                                                    @else
                                                        {{ str_replace('>', '', html_entity_decode($message->body))}}
                                                    @endif
                                                    
                                                </td>

                                                <td>
                                                    @php
                                                        // Giải mã dữ liệu JSON
                                                        $attachment = json_decode($message->attachment, true);
                                                    @endphp
                                                
                                                    @if (isset($attachment['new_name']))
                                                        @php
                                                            // Lấy phần mở rộng của tệp
                                                            $fileExtension = pathinfo($attachment['new_name'], PATHINFO_EXTENSION);
                                                        @endphp
                                                
                                                        @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                            <!-- Hiển thị hình ảnh -->
                                                            <img src="{{ asset('storage/attachments/' . $attachment['new_name']) }}" alt="Attachment" style="max-width: 100px; max-height: 100px;" />
                                                        
                                                        @elseif (in_array($fileExtension, ['mp3', 'wav', 'ogg']))
                                                            <!-- Hiển thị audio -->
                                                            <audio controls style="max-width: 100%;">
                                                                <source src="{{ asset('storage/attachments/' . $attachment['new_name']) }}" type="audio/{{ $fileExtension }}">
                                                                Trình duyệt của bạn không hỗ trợ phát âm thanh.
                                                            </audio>
                                                        
                                                        @elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg']))
                                                            <!-- Hiển thị video -->
                                                            <video controls style="max-width: 100%; max-height: 200px;">
                                                                <source src="{{ asset('storage/attachments/' . $attachment['new_name']) }}" type="video/{{ $fileExtension }}">
                                                                Trình duyệt của bạn không hỗ trợ phát video.
                                                            </video>
                                                        
                                                        @else
                                                            <!-- Hiển thị liên kết tải về cho tệp tin -->
                                                            <a href="{{ asset('storage/attachments/' . $attachment['new_name']) }}" download>
                                                                Tải tệp: {{ $attachment['old_name'] }}
                                                            </a>
                                                        @endif
                                                    @endif
                                                </td>
                                                
    
    
    
                                                <td>{{ $message->created_at }}</td>
                                            </tr>
                                        @endforeach
                                        
                                    @endif
                                    
                                </tbody>
                            </table>

                           
                        </div>
                    </div>
                    @isset($messages)
                        <div id="" style="display: flex; justify-content: center; align-items: center; margin-top: 15px">
                            {{$messages->onEachSide(1)->links('pagination::bootstrap-4', ['pageName' => 'conversationGround_page'])}}
                        </div>
                    @endisset
                    
                </div>
            </div>


        </div>
    </div>

    <!-- Modal hiển thị danh sách thành viên -->
    <div id="memberModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
                background: white; padding: 35px; border-radius: 10px; box-shadow: 0px 0px 10px gray; z-index: 100;">
        <h3>Danh sách thành viên</h3>
        <ul id="memberModalBody" class="list-group" style="max-height: 250px; overflow-y: auto; padding: 0; margin: 0"></ul>
        <button class="btn btn-primary" style="margin-top: 10px; float: right" onclick="document.getElementById('memberModal').style.display='none'">Đóng</button>
    </div>

    <script src="{{ asset('assets/js/searchUser.js') }}"></script>
</x-layout-admin>