<x-layout-admin>
    
    <div class="page-wrapper">
        <div class="content">

            <div class="page-header">
                <div class="page-title">
                    <h4>Danh sách bản đồ </h4>
                    <h6>Tổng danh sách các bản đồ được gửi</h6>
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
                                        <th class="sorting" >Bản đồ đã gửi</th>

                                        <th class="sorting" >Thời gian gửi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sn = 1;
                                    @endphp
                                    @foreach ($list_map as $maps)

                                        <tr>
                                            <td>{{$sn++}}</td>
                                            <td>{{$maps->fromUser->name}}</td>
                                            <td>
                                                <div class="google-maps-embed">
                                                    <?php
                                                        // Trích xuất URL từ chuỗi
                                                        preg_match('/https:\/\/www\.google\.com\/maps\?q=[^\s>]+/', $maps->body, $matches);
                                                        $mapUrl = isset($matches[0]) ? $matches[0] : ''; // Nếu có URL thì lấy, nếu không thì để trống
                                                    ?>
                                                    @if($mapUrl)
                                                        <iframe 
                                                            src="{{ $mapUrl }}&output=embed" 
                                                            width="80%" 
                                                            height="200" 
                                                            style="border:0;" 
                                                            allowfullscreen="" 
                                                            loading="lazy">
                                                        </iframe>
                                                    @else
                                                        <p>Không có bản đồ để hiển thị.</p>
                                                    @endif
                                                </div>
                                            </td>

                                            <td>
                                                {{$maps->created_at}}
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
                        {{$list_map->onEachSide(1)->links()}}
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