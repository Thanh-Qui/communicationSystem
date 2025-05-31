<x-layout-admin>
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Danh sách hình ảnh </h4>
                    <h6>Tổng danh sách hình ảnh</h6>
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

                    {{-- Danh sách hình ảnh --}}
                    <div class="table-responsive">
                        <div  class="dataTables_wrapper dt-bootstrap4 no-footer">

                            <div class="image-gallery">
                                @foreach ($list_image as $images)
                                    @php
                                        // Giải mã chuỗi JSON trong cột 'attachment' để lấy 'new_name'
                                        $attachment = json_decode($images->attachment);
                                    @endphp
                            
                                    @if ($attachment && isset($attachment->new_name))
                                        <img onclick="openModal('{{ asset('storage/attachments/' . $attachment->new_name) }}')" class="image-item" src="{{ asset('storage/attachments/' . $attachment->new_name) }}" alt="">
                                    @endif
                                @endforeach
                            </div>

                            <div style="display: flex; justify-content: center; align-items: center; margin-top: 15px">
                                {{$list_image->onEachSide(1)->links()}}
                            </div>
                          
                            <div id="myModal" class="modal">
                                <span class="close" onclick="closeModal()">&times;</span>
                                <img class="modal-content" id="modalImage">
                            </div>
                            

                        </div>
                    </div>
    

                </div>
            </div>


            <div class="page-header">
                <div class="page-title">
                    <h4>Danh sách tệp tin </h4>
                    <h6>Tổng danh sách tệp tin đã gửi</h6>
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
                                {{-- <form action="{{url('list_conversation')}}">   
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label>
                                            <input type="search" name="searchMessage" id="searchMessage" value="{{request()->get('searchMessage')}}"  class="form-control form-control-sm" placeholder="Search..." aria-controls="DataTables_Table_0">
                                        </label>
                                    </div>
                                </form> --}}

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
                                        <th>Người gửi</th>
                                        <th class="sorting" >File đã gửi</th>

                                        <th class="sorting" >Thời gian gửi</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyUserLockAcount">
                                    @php
                                        $sn = 1;
                                    @endphp
                                    @foreach ($list_file as $files)
                                        @php
                                            // Giải mã chuỗi JSON trong cột 'attachment' để lấy 'new_name'
                                            $attachment = json_decode($files->attachment);
                                        @endphp
                                        <tr>
                                            <td>{{$sn++}}</td>
                                            <td>{{$files->fromUser->name}}</td>
                                            <td>
                                                <a href="{{ asset('storage/attachments/' . $attachment->new_name) }}" target="_blank">
                                                    <span>{!! html_entity_decode($attachment->old_name)  !!}</span>
                                                </a>
                                            </td>
                                            
                                            <td>
                                                {{$files->created_at}}
                                            </td>

                                            {{-- <td>
                                                <a onclick="confirmation(event)" class="confirm-text" href="#">
                                                    <i class="fa-solid fa-trash-can" style="color: red; font-size: 20px"></i>
                                                </a>
                                            </td> --}}

                                            
                                        </tr>
                                    @endforeach
                                </tbody>

                                
                            </table>
                                
                           
                        </div>
                    </div>
                    <div style="display: flex; justify-content: center; align-items: center; margin-top: 15px">
                        {{$list_file->onEachSide(1)->links('pagination::bootstrap-4', ['pageName' => 'file_page'])}}
                    </div>
                </div>
            </div>



            <div class="page-header">
                <div class="page-title">
                    <h4>Danh sách video và bản audio </h4>
                    <h6>Tổng danh sách video và bản audio đã gửi</h6>
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
                                        <th>Người gửi</th>
                                        <th class="sorting" >Video đã gửi</th>

                                        <th class="sorting" >Thời gian gửi</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyUserLockAcount">
                                    @php
                                        $sn = 1;
                                    @endphp
                                    @foreach ($list_video as $videos)
                                        @php
                                            // Giải mã chuỗi JSON trong cột 'attachment' để lấy 'new_name'
                                            $attachment = json_decode($videos->attachment);
                                        @endphp
                                        <tr>
                                            <td>{{$sn++}}</td>
                                            <td>{{$videos->fromUser->name}}</td>
                                            <td>
                                                @if (str_ends_with($attachment->new_name, '.mp3'))
                                                    <audio controls>
                                                        <source src="{{ asset('storage/attachments/' . $attachment->new_name) }}" type="audio/mpeg">
                                                        Trình duyệt của bạn chưa hỗ trợ cho chức năng này
                                                    </audio>
                                                @else
                                                    <video controls style="max-width: 300px; border-radius: 10px">
                                                        <source src="{{ asset('storage/attachments/' . $attachment->new_name) }}" type="video/{{ pathinfo($attachment->new_name, PATHINFO_EXTENSION) }}">
                                                        Trình duyệt của bạn chưa hỗ trợ cho chức năng này
                                                    </video>
                                                @endif
                                                

                                            </td>

                                            <td>
                                                {{$videos->created_at}}
                                            </td>

                                            {{-- <td>
                                                <a onclick="confirmation(event)" class="confirm-text" href="#">
                                                    <i class="fa-solid fa-trash-can" style="color: red; font-size: 20px"></i>
                                                </a>
                                            </td> --}}

                                            
                                        </tr>
                                    @endforeach
                                </tbody>

                                
                            </table>
                                
                           
                        </div>
                    </div>
                    <div style="display: flex; justify-content: center; align-items: center; margin-top: 15px">
                        {{$list_video->onEachSide(1)->links('pagination::bootstrap-4', ['pageName' => 'video_page'])}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function openModal(src) {
            document.getElementById("modalImage").src = src;
            document.getElementById("myModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        // Đóng modal khi click ra ngoài hình ảnh
        window.onclick = function(event) {
            const modal = document.getElementById("myModal");
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</x-layout-admin>