<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Drive </title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assets2/vendors/feather/feather.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets2/vendors/mdi/css/materialdesignicons.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets2/vendors/ti-icons/css/themify-icons.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets2/vendors/font-awesome/css/font-awesome.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets2/vendors/typicons/typicons.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets2/vendors/simple-line-icons/css/simple-line-icons.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets2/vendors/css/vendor.bundle.base.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets2/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }} ">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets2/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }} ">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets2/js/select.dataTables.min.css') }} ">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets2/css/style.css') }} ">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('images/iconmessage.png') }} " />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('userFiles.css') }}">
    
</head>
<body class="with-welcome-text">
    <div class="container-scroller">
        {{-- Header --}}
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start" style="background-color: #F2F7F8">
              <div class="me-3">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                  <span class="icon-menu"></span>
                </button>
              </div>
              <div>
                <a class="navbar-brand brand-logo" href="{{ route('chatify') }}">
                  <img style="min-height: 60px" src="{{ asset('images/iconmessage.png') }} " alt="logo" /> <span style="color: black">Drive</span>
                </a>
                <a class="navbar-brand mb-3 pc-none" href="{{ route('chatify') }}">
                  <span class="mdi mdi-keyboard-backspace" style="color: black"></span>
                </a>
              </div>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-top" style="background-color: #F2F7F8">
        
              {{-- Tìm kiếm file hoặc folder --}}
              <ul class="navbar-nav">
                <li class="nav-item fw-semibold d-lg-block ms-0">
                    <div class="search-bar">
                      <form action="{{ route('drive.search') }}" method="get">
                          <input type="text" name="search" class="form-control" style="border-radius: 30px" placeholder="Search in Drive">
                      </form>
                        
                    </div>
                </li>
              </ul>
              <ul class="navbar-nav ms-auto">
        
                {{-- lịch của các năm --}}
                <li class="nav-item d-none d-lg-block">
                  <div id="datepicker-popup" class="input-group date datepicker navbar-date-picker">
                    <span class="input-group-addon input-group-prepend border-right">
                      <span class="icon-calendar input-group-text calendar-icon"></span>
                    </span>
                    <input type="text" class="form-control">
                  </div>
                </li>
        
                {{-- Thông tin cá nhân --}}
                <li class="nav-item dropdown d-lg-block user-dropdown">
                      @php
                          $userId = Auth::id();
                          $userName = DB::table('users')->where('id', $userId)->first();
                      @endphp
        
                  <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <img height="40px" width="40px" class="img-md rounded-circle" src="{{ asset('storage/users-avatar/'. ($userName->avatar ?? 'avatar.png')) }} " alt="Profile image">
                  <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                    <div class="dropdown-header text-center">
                      
                      
                      <img height="40px" width="40px" class="img-md rounded-circle" src="{{ asset('storage/users-avatar/'. ($userName->avatar ?? 'avatar.png')) }} " alt="Profile image">
                      <p class="mb-1 mt-3 fw-semibold">{{$userName->name}}</p>
                      <p class="fw-light text-muted mb-0">{{$userName->email}}</p>
                    </div>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile</a>
                    {{-- <a class="dropdown-item"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out</a> --}}
                    {{-- <a class="dropdown-item" href="generalsettings.html"><i class="me-2" data-feather="settings"></i>Settings</a> --}}
                    <form method="POST" action="{{ route('logout') }}" class="dropdown-item">
                        @csrf
                        <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>
                        <x-dropdown-link :href="route('logout')" style="text-decoration: none; padding: 0; color: black"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                  </div>
                </li>
                
              </ul>
              <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
                <span class="mdi mdi-menu"></span>
              </button>
            </div>
        </nav>

        <div class="container-fluid page-body-wrapper">
            {{-- Sidebar --}}
            <nav class="sidebar sidebar-offcanvas" id="sidebar" style="background-color: #F2F7F8">
                <ul class="nav">
            
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="">
                            <button type="button" class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i> New folder</button>
                        </a>
                    </li> --}}
            
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#newFolderModal">
                            <i class="menu-icon mdi mdi-plus-box"></i>
                            <span class="menu-title">New Folder</span>
                        </a>
                    </li>
            
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('myStorage') }}">
                        <i class="menu-icon mdi mdi-file-document"></i>
                        <span class="menu-title">Documentation</span>
                        </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('drive.todo') }}">
                      <i class="menu-icon mdi mdi-clipboard-list-outline"></i>
                      <span class="menu-title">To-Do List</span>
                      </a>
                  </li>
            
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('drive.trash') }}">
                        <i class="mdi mdi-trash-can-outline menu-icon"></i>
                        <span class="menu-title">Trash</span>
                        </a>
                    </li>
            
                    <br>
                    <li>
                        <a class="nav-link" href="#">
                            <div class="progress" style="height: 10px; margin-bottom: 7px">
                                <div id="storageProgress" class="progress-bar" role="progressbar" 
                                    style="width: {{ $storageData['usedPercentage'] }}%" 
                                    aria-valuenow="{{ $storageData['usedPercentage'] }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                </div>
                            </div>
                            <p style="text-align: center">{{ $storageData['displaySize'] }} of 10GB used</p>
                        </a>
                            
                    </li>
                </ul>
                
              </nav>
            
              {{-- Giao diện tạo thư mục --}}
            <div class="modal fade" id="newFolderModal" tabindex="-1" aria-labelledby="newFolderLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="newFolderLabel">Create New Folder</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('folder.create') }}" method="post" id="newFolderForm" enctype="multipart/form-data">
                                @csrf
                                
                                {{-- chỗ vị trí value lấy id từ route /folder/{id} vì nó truyền id của folder và lấy id từ route đó  --}}
                                <input type="hidden" name="parent_id" id="parentIdInput" value="{{ request()->route('id') }}">
            
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="folderName" name="folderName" required>
                                </div>
                                <button type="submit" class="btn btn-primary" style="float: right">Tạo</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            
            {{-- <script>
                document.getElementById("newFolderForm").addEventListener("submit", function(event) {
                    let folderName = document.getElementById("folderName").value;
                    
                    if (folderName.trim() === "") {
                        alert("Folder name cannot be empty!");
                        event.preventDefault(); // Ngăn gửi form nếu dữ liệu không hợp lệ
                        return;
                    }
                
                    console.log("Creating folder:", folderName);
                
                    // Không chặn form gửi request nữa
                });
            </script> --}}

            {{-- Body --}}
            {{$slot}}
        </div>
    </div>

    {{-- Footer --}}

        {{-- modal rename --}}
        <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content" style="z-index: 1055;">
                <div class="modal-header">
                  <h3 class="modal-title" id="renameModalLabel">Thay đổi tên</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" action="{{ route('drive.rename') }}">
                    @csrf
                    <div class="mb-3">
                      <label for="newName" class="form-label">Nhập tên mới</label>
                      <input type="text" class="form-control" id="newName" name="new_name">
                      @error('new_name')
                          <p style="color: red">{{$message}}</p>
                      @enderror
                    </div>
                    <input type="hidden" name="item_id" id="modalItemId">
                    <input type="hidden" name="item_size" id="modalItemSize">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        </div>
          
        <!-- Modal shareFile -->
        <div class="modal fade" id="shareFileModal" tabindex="-1" aria-labelledby="shareFileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <form method="POST" action="{{ route('drive.share') }}" style="width: 100%">
                @csrf
                {{-- id của file --}}
                <input type="hidden" name="file_id" id="shareFileId">
                <div class="modal-content">
                  <div class="modal-header">
                    <h3 class="modal-title" id="shareFileModalLabel">Chia sẻ tài liệu</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="userSearch" class="form-label">Chọn người dùng (Nhóm)</label>
                      <input type="text" id="userSearch" class="form-control" placeholder="Nhập tên người dùng" autocomplete="off">
                      <div id="userSuggestions" class="list-group" style="display: none; position: absolute; z-index: 1000; width: 90%; max-height: 200px; overflow-y: auto;"></div>
                      
                      {{-- id channel hoặc user (gửi cả nhóm và 1-1) --}}
                      {{-- <input type="hidden" name="list_id" id="selectedListId">
                      <input type="hidden" name="list_type" id="listType"> --}}
      
                      {{-- danh sách chọn nhiều người cùng lúc --}}
                      <input type="hidden" name="list_data" id="selectedListData">
                      <!-- Danh sách đã chọn -->
                      <ul id="selectedListDisplay" class="mt-2 list-group"></ul>
      
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="shareFileButton" data-share-url="{{ route('drive.share') }}">Chia sẻ</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                  </div>
                </div>
              </form>
            </div>
        </div>
      
        {{-- Modal move folder --}}
        <div class="modal fade" id="moveFolderModal" tabindex="-1" aria-labelledby="moveFolderModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="{{ route('folders.move') }}" method="POST">
                  @csrf
                  {{-- id của folder đang được di chuyển --}}
                  <input type="hidden" name="folder_id" id="folder_id_to_move">
                  
                  {{-- id của thư mục đích sẽ được chuyển tới --}}
                  <input type="hidden" name="target_folder" id="target_folder_input">
                  
                  <div class="modal-header">
                    <h3 class="modal-title" id="moveFolderModalLabel">Di chuyển thư mục</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                  </div>
          
                  <div class="modal-body">
                    <p>Chọn thư mục đích:</p>
                    <div style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; border-radius: 6px; padding: 10px;">
      
                      {{-- Mục thư mục gốc --}}
                      <div class="folder-option" data-id="null" style="cursor: pointer; padding: 8px; border-bottom: 1px solid #eee;">
                        🏠 My Drive
                      </div>
      
                      {{-- Danh sách thư mục con --}}
                      @php
                          $folders = DB::table('folders')->where('status', 0)->get();
                      @endphp
                      @foreach ($folders as $folder)
                        <div class="folder-option" 
                             data-id="{{ $folder->id }}"
                             style="cursor: pointer; padding: 8px; border-bottom: 1px solid #eee;">
                          📁 {{ $folder->name }}
                        </div>
                      @endforeach
                    </div>
                  </div>
          
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Di chuyển</button>
                  </div>
                </form>
              </div>
            </div>
        </div>

        {{-- Modal move file --}}
        <div class="modal fade" id="moveFileModal" tabindex="-1" aria-labelledby="moveFileModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <form action="{{ route('files.moveFile') }}" method="POST">
                @csrf
                {{-- id của folder đang được di chuyển --}}
                <input type="hidden" name="file_id" id="file_id_to_move">
                
                {{-- id của thư mục đích sẽ được chuyển tới --}}
                <input type="hidden" name="target_file" id="target_file_input">
                
                <div class="modal-header">
                  <h3 class="modal-title" id="moveFileModalLabel">Di chuyển tệp tin</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <div class="modal-body">
                  <p>Chọn thư mục đích:</p>
                  <div style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; border-radius: 6px; padding: 10px;">

                    {{-- Mục thư mục gốc --}}
                    <div class="file-option" data-id="null" style="cursor: pointer; padding: 8px; border-bottom: 1px solid #eee;">
                      🏠 My Drive
                    </div>

                    {{-- Danh sách thư mục con --}}
                    @php
                        $folders = DB::table('folders')->where('status', 0)->get();
                    @endphp
                    @foreach ($folders as $folder)
                      <div class="file-option" 
                          data-id="{{ $folder->id }}"
                          style="cursor: pointer; padding: 8px; border-bottom: 1px solid #eee;">
                        📁 {{ $folder->name }}
                      </div>
                    @endforeach
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Di chuyển</button>
                </div>
              </form>
            </div>
          </div>
        </div>


        {{-- Modal add work --}}
        <div class="modal fade" id="addToDo" tabindex="-1" aria-labelledby="addToDoLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <form action="#" >
                @csrf
                
                <div class="modal-header">
                    <h3 class="modal-title" id="addToDoLabel">Thêm công việc</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                      <label for="nameWork" class="form-label" style="font-size: 16px">Tên công việc</label>
                      <input type="text" class="form-control" id="nameWork" placeholder="Tên công việc...">
                    </div>
                    <div class="mb-3">
                      <label for="description" class="form-label">Mô tả công việc</label>
                      <textarea class="form-control" name="" id="description" cols="30" rows="30" placeholder="Mô tả công việc..."></textarea>
                    </div>
                    <div class="row">
                      <div class="col">
                        <label for="date-start" class="form-label">Ngày bắt đầu</label>
                        <input type="date" class="form-control">
                      </div>
                      <div class="col">
                        <label for="date-end" class="form-label">Ngày kết thúc</label>
                        <input type="date" class="form-control">
                      </div>
                    </div>
                </div>

                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Lưu</button>
                  
                </div>
              </form>
            </div>
          </div>
        </div>


      {{-- Footer --}}
      <script src="{{ asset('assets2/vendors/js/vendor.bundle.base.js') }} "></script>
      <script src="{{ asset('assets2/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }} "></script>
      <!-- endinject -->
      <!-- Plugin js for this page -->
      <script src="{{ asset('assets2/vendors/chart.js/chart.umd.js') }} "></script>
      <script src="{{ asset('assets2/vendors/progressbar.js/progressbar.min.js') }} "></script>
      <!-- End plugin js for this page -->
      <!-- inject:js -->
      <script src="{{ asset('assets2/js/off-canvas.js') }} "></script>
      <script src="{{ asset('assets2/js/template.js') }} "></script>
      <script src="{{ asset('assets2/js/settings.js') }} "></script>
      <script src="{{ asset('assets2/js/hoverable-collapse.js') }} "></script>
      <script src="{{ asset('assets2/js/todolist.js') }} "></script>
      <!-- endinject -->
      <!-- Custom js for this page-->
      <script src="{{ asset('assets2/js/jquery.cookie.js') }} " type="text/javascript"></script>
      {{-- <script src="{{ asset('assets2/js/dashboard.js') }} "></script> --}}
      
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
      
      
      <script>
            function confirmation(e, title = "Bạn có muốn xoá thông tin này?", button = ['Hủy', 'Xóa']) {
                 e.preventDefault(); // Chặn hành động mặc định ngay lập tức
                 e.stopPropagation();
      
                 var urlToRedirect = e.currentTarget.getAttribute('href');
      
                 swal({
                    title: title,
                    text: "",
                    icon: "warning",
                    buttons: button,
                    dangerMode: true,
                 }).then((willDelete) => {
                    if (willDelete) {
                          window.location.href = urlToRedirect; // Chỉ điều hướng khi người dùng xác nhận
                    }
                 });
              }
              
      
      </script>
      
      <script src="{{ asset('storageDrive.js') }}"></script>
      
</body>
</html>