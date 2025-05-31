<x-layout-admin>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Danh sách người dùng </h4>
                    <h6>Tổng danh sách người dùng</h6>
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
                                {{-- <form action="{{url('search_listMessages')}}"> --}}
                                <form action="{{ url('list_conversation') }}">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label>
                                            <input type="search" name="searchMessage" id="searchMessage"
                                                value="{{ request()->get('searchMessage') }}"
                                                class="form-control form-control-sm" placeholder="Search..."
                                                aria-controls="DataTables_Table_0">
                                        </label>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="wordset">
                            <ul>

                                <li>
                                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="print"
                                        aria-label="print">
                                        <img src="assets/img/icons/printer.svg" alt="img">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <table class="table datanew no-footer" role="grid">
                                <thead>
                                    <tr role="row">
                                        {{-- <th class="sorting_asc" tabindex="0" rowspan="1" colspan="1" aria-sort="ascending" >
                                            SL
                                        </th> --}}
                                        <th>Người gửi</th>
                                        <th>Người nhận</th>

                                        <th>Tin nhắn</th>
                                        <th>Nội dung đa phương tiện</th>
                                        <th>Thời gian gửi</th>

                                    </tr>
                                </thead>
                                <tbody id="listMessages">

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
                                                {{-- Lấy người gửi --}}
                                                <td>{{ $message->fromUser->name }}</td>

                                                <td>
                                                    @php
                                                        // tạo mảng để lưu tên người dùng
                                                        $listUser = [];
                                                    @endphp

                                                    {{-- lấy người nhận --}}
                                                    @foreach ($message->usersInChannel as $user)
                                                        @if ($user->id !== $message->from_id)
                                                            @php
                                                                // lưu người dùng vào mảng
                                                                $listUser[] = $user->name;
                                                            @endphp
                                                        @endif
                                                    @endforeach

                                                    {{-- Hiển thị danh sách người nhận --}}
                                                    {{ implode(', ', $listUser) }}
                                                </td>

                                                <td>
                                                    @if ($isPoll)
                                                        <p>Bình chọn</p>
                                                    @else
                                                        {{ str_replace('>', '', html_entity_decode($message->body)) }}
                                                    @endif
                                                    
                                                </td>

                                                {{-- <td>
                                                @php
                                                    // Giải mã dữ liệu JSON
                                                    $attachment = json_decode($message->attachment, true);
                                                @endphp
                                    
                                                @if (isset($attachment['new_name']))
                                                    <img src="{{ asset('storage/attachments/' . $attachment['new_name']) }}" alt="Attachment" style="max-width: 100px; max-height: 100px;"/>
                                                @endif
                                            </td> --}}


                                                <td>
                                                    @php
                                                        // Giải mã dữ liệu JSON
                                                        $attachment = json_decode($message->attachment, true);
                                                    @endphp

                                                    @if (isset($attachment['new_name']))
                                                        @php
                                                            // Lấy phần mở rộng của tệp
                                                            $fileExtension = pathinfo(
                                                                $attachment['new_name'],
                                                                PATHINFO_EXTENSION,
                                                            );
                                                        @endphp

                                                        @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                            <!-- Hiển thị hình ảnh -->
                                                            <img src="{{ asset('storage/attachments/' . $attachment['new_name']) }}"
                                                                alt="Attachment"
                                                                style="max-width: 100px; max-height: 100px;" />
                                                        @elseif (in_array($fileExtension, ['mp3', 'wav', 'ogg']))
                                                            <!-- Hiển thị audio -->
                                                            <audio controls style="max-width: 100%;">
                                                                <source
                                                                    src="{{ asset('storage/attachments/' . $attachment['new_name']) }}"
                                                                    type="audio/{{ $fileExtension }}">
                                                                Trình duyệt của bạn không hỗ trợ phát âm thanh.
                                                            </audio>
                                                        @elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg']))
                                                            <!-- Hiển thị video -->
                                                            <video controls style="max-width: 100%; max-height: 200px;">
                                                                <source
                                                                    src="{{ asset('storage/attachments/' . $attachment['new_name']) }}"
                                                                    type="video/{{ $fileExtension }}">
                                                                Trình duyệt của bạn không hỗ trợ phát video.
                                                            </video>
                                                        @else
                                                            <!-- Hiển thị liên kết tải về cho tệp tin -->
                                                            <a href="{{ asset('storage/attachments/' . $attachment['new_name']) }}"
                                                                download>
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
                    <div style="display: flex; justify-content: center; align-items: center; margin-top: 10px">
                        @if (isset($messages))
                            {{ $messages->appends(request()->input())->onEachSide(1)->links() }}
                        @endif

                    </div>
                    {{-- <div style="display: flex; justify-content: center; align-items: center; margin-top: 15px" id="paginationLinks">
                        {{$user->onEachSide(1)->links()}}
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</x-layout-admin>
