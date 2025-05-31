<x-layout-admin>
    
    <div class="page-wrapper">
        
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Quản lý kho lưu trữ </h4>
                </div>
            </div>

            <div class="page-header">
                <div class="page-title">
                    <h6>Quản lý thư mục</h6>
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
                                <form action="{{ route('drive.manager') }}" method="GET">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label>
                                            <input type="search" name="searchFolder" value="{{request('searchFolder')}}" class="form-control form-control-sm" placeholder="Search..." aria-controls="DataTables_Table_0">
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
                                        <th class="sorting" >Tên thư mục</th>
                                        <th class="sorting" >Tên người dùng</th>
                                        <th class="sorting" >Trạng thái xóa</th>

                                        <th class="sorting" >Thời gian tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = ($folders->currentPage() - 1) * $folders->perPage() + 1;
                                    @endphp
                                    @foreach ($folders as $folder)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$folder->name}}</td>
                                            <td>{{$userNames->firstWhere('id', $folder->user_id)->name ?? '-'}}</td>
                                            <td>{{$folder->status == 0 ? 'Đang hoạt động' : 'Đang trong quá trình xóa'}}</td>
                                            <td>{{$folder->created_at}}</td>
                                        </tr>
                                    @endforeach

                                    
                                    
                                </tbody>
                                
                            </table>

                            
                        </div>
                    </div>
                    {{-- Phân trang --}}
                    <div style="display: flex; justify-content: center; align-items: center; margin-top: 15px">
                        {{$folders->appends(request()->query())->links()}}
                    </div>
                </div>
            </div>


            {{-- Quản lý tệp tin --}}
            <div class="page-header">
                <div class="page-title">
                    <h6>Quản lý tệp tin</h6>
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
                                <form action="{{ route('drive.manager') }}" method="GET">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label>
                                            <input type="search" name="searchFile" value="{{request('searchFile')}}" class="form-control form-control-sm" placeholder="Search..." aria-controls="DataTables_Table_0">
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
                                        <th class="sorting" >Tên tệp tin</th>
                                        <th class="sorting" >Kích thước tệp tin</th>
                                        <th class="sorting" >Tên người dùng</th>
                                        <th class="sorting" >Tên thư mục</th>
                                        <th class="sorting" >Trạng thái xóa</th>

                                        <th class="sorting" >Thời gian tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = ($files->currentPage() - 1) * $files->perPage() + 1;
                                    @endphp
                                    @foreach ($files as $file)
                                        <tr
                                            onclick="window.location.href='{{ route('files.download', $file->id) }}'"
                                            style="cursor: pointer"
                                        >
                                            <td>{{$i++}}</td>
                                            <td>{{$file->name}}</td>
                                            <td>{{ number_format($file->size) }} KB</td>
                                            <td>{{$userNames->firstWhere('id', $file->user_id)->name ?? '-'}}</td>
                                            <td>{{$folders->firstWhere('id', $file->folder_id)->name ?? '-'}}</td>
                                            <td>{{$file->status == 0 ? 'Đang hoạt động' : 'Đang trong quá trình xóa'}}</td>
                                            <td>{{$file->created_at}}</td>
                                        </tr>
                                    @endforeach
                                    
                                </tbody>
                                
                            </table>

                            
                        </div>
                    </div>
                    {{-- Phân trang --}}
                    <div style="display: flex; justify-content: center; align-items: center; margin-top: 15px">
                        {{$files->appends(request()->query())->links()}}
                    </div>
                </div>
            </div>


        </div>

    </div>
</x-layout-admin>