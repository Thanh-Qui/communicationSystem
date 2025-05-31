<x-layout-admin>
    <div class="page-wrapper">
        
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Quản lý thông báo </h4>
                    <h6>Thêm thông báo</h6>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('add_news') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                          <label for="exampleInputEmail1" class="form-label">Tiêu đề thông báo</label>
                          <input type="text" name="title" class="form-control" id="exampleInputEmail1">
                        </div>
                        <div class="mb-3">
                          <label for="exampleInputPassword1" class="form-label">Nội dung thông báo</label>
                          <textarea name="content" class="form-control" id="" cols="30" rows="10"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Hình ảnh minh họa</label>
                            <input type="file" name="img" class="form-control" id="imgInput" accept="image/*">

                            <img id="previewImage" src="" alt="Xem trước ảnh" style="max-width: 100%; margin-top: 10px; display: none;">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Đăng thông báo</button>
                    </form>
                </div>
            </div>

            <div class="page-header">
                <div class="page-title">
                    <h6>Danh sách thông báo</h6>
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
                                {{-- Form tìm kiếm người dùng (không cần phải tại thêm 1 route để thực hiện việc tìm kiếm) --}}
                                <form action="{{ route('manager_news') }}" method="GET">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label>
                                            <input type="search" name="searchNews" value="{{request('searchNews')}}" class="form-control form-control-sm" placeholder="Search..." aria-controls="DataTables_Table_0">
                                        </label>
                                    </div>
                                </form>
                            </div>
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
                                        <th class="sorting" >Tiêu đề thông báo</th>
                                        <th class="sorting" >Nội dung thông báo</th>
                                        <th class="sorting" >Hình ảnh minh họa</th>

                                        <th class="sorting" >Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php
                                    $sn = ($getNews->currentPage() - 1) * $getNews->perPage() + 1;
                                    
                                    if (isset($getNews)) {
                                        foreach ($getNews as $news) {
                                            $attachment = json_decode($news->img);
                                            ?>
                                                <tr>
                                                    <td>{{$sn++}}</td>
                                                    <td>{{$news->title}}</td>
                                                    <td>
                                                        @php
                                                           $contentLimit = Str::limit("$news->content", 60)
                                                        @endphp
                                                        {{$contentLimit}}
                                                    </td>
                                                    <td>
                                                        @if (isset($news->img))
                                                            <img style="max-width: 85px; max-height: 60px" src="{{ asset('storage/attachments/' . $attachment->new_name) }}" alt="">
                                                        @else
                                                            <span>Không có hình ảnh</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a onclick="confirmation(event)" href="{{ url('delete_news', $news->id) }}"><i style="color: red" class="fa-solid fa-trash"></i></a>
                                                        <a style="margin-left: 10px" href="{{ url('viewUpdate_news', $news->id) }}"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    </td>
                                                </tr>
                                            <?php
                                        }
                                    }
                                    
                                    ?>
                                </tbody>
                                
                            </table>

                            
                        </div>
                    </div>
                    {{-- Phân trang --}}
                    <div style="display: flex; justify-content: center; align-items: center; margin-top: 10px">
                        @if (isset($getNews))
                            {{$getNews->appends(['searchNews' => request('searchNews')])->links()}}
                        @endif
                                
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.getElementById("imgInput").addEventListener("change", function(event) {
            let file = event.target.files[0]; // Lấy file được chọn
            if (file) {
                let reader = new FileReader(); // Tạo FileReader để đọc file
                reader.onload = function(e) {
                    let previewImage = document.getElementById("previewImage");
                    previewImage.src = e.target.result; // Gán đường dẫn ảnh vào src
                    previewImage.style.display = "block"; // Hiển thị ảnh
                }
                reader.readAsDataURL(file); // Đọc file dưới dạng URL
            }
        });

    </script>
</x-layout-admin>