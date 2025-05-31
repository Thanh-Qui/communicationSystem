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
                    <form action="{{ url('update_news', $news->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                          <label for="exampleInputEmail1" class="form-label">Tiêu đề thông báo</label>
                          <input type="text" name="title" class="form-control" id="exampleInputEmail1" value="{{$news->title}}">
                        </div>
                        <div class="mb-3">
                          <label for="exampleInputPassword1" class="form-label">Nội dung thông báo</label>
                          <textarea name="content" class="form-control" id="" cols="30" rows="10">{{$news->content}}</textarea>
                        </div>

                        <div class="mb-3">
                            @php
                                $attachment = json_decode($news->img);
                                
                            @endphp
                            @if (isset($attachment))
                                <div>
                                    <label for="exampleInputPassword1" class="form-label">Hình ảnh minh họa cũ</label>
                                </div>
                                <img style="max-width: 100px; max-height: 90px" src="{{ asset('storage/attachments/' . $attachment->new_name) }}" alt="">
                            @endif
                            
                          </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Hình ảnh minh họa mới</label>
                            <input type="file" name="img" class="form-control" id="imgInput" accept="image/*">

                            <img id="previewImage" src="" alt="Xem trước ảnh" style="max-width: 100%; margin-top: 10px; display: none;">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Cập nhật thông báo</button>
                    </form>
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