<!DOCTYPE html>
<html lang="en">
<head>

    {{-- css --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="POS - Bootstrap Admin Template">
    <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern,  html5, responsive">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>Chat Online Manager</title>

    <link rel="shortcut icon" type="image/x-icon" href=" {{ asset('assets/img/logoChat.png') }} ">

    <link rel="stylesheet" href=" {{ asset('assets/css/bootstrap.min.css') }} ">

    <link rel="stylesheet" href=" {{ asset('assets/css/animate.css') }} ">

    <link rel="stylesheet" href=" {{ asset('assets/css/dataTables.bootstrap4.min.css') }} ">

    <link rel="stylesheet" href=" {{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }} ">
    <link rel="stylesheet" href=" {{ asset('assets/plugins/fontawesome/css/all.min.css') }} ">

    <link rel="stylesheet" href=" {{ asset('assets/css/style.css') }} ">
    <link rel="stylesheet" href=" {{ asset('admincss.css') }} ">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">

</head>
<body>

<div class="main-wrapper">

    {{-- start header --}}
    <div id="global-loader">
        <img src="{{ asset('images/vn-flag-full.gif') }}" alt="">
        <img width="400px" src="{{ asset('images/tank.png') }}" alt="">
        {{-- <div class="whirly-loader"> </div> --}}
    </div>
    
    <div class="header">
            
                <div class="header-left active">
                    <a href="{{ url('admin/index') }}" class="logo">
                        <img style="width: 80px; max-height: 100px; margin: 15px" src="{{ asset('assets/img/iconmessage.png') }} " alt="">
                    </a>
                    <a href="{{ url('admin/index') }}" class="logo-small">
                        <img src="{{ asset('assets/img/iconmessage.png') }} " alt="">
                    </a>
                    <a id="toggle_btn" href="javascript:void(0);">
                    </a>
                </div>
            
                <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>
            
                <ul class="nav user-menu">
    
                    <li class="nav-item header-title">
                        <p>Admin Manager</p>
                    </li>
                
                    <li class="nav-item">
                        <div class="top-nav-search">
                            <a href="javascript:void(0);" class="responsive-search">
                                <i class="fa fa-search"></i>
                            </a>
                            <form action="#">
                                <div class="searchinputs">
                                    <input type="text" placeholder="Search Here ...">
                                    <div class="search-addon">
                                        <span><img src="{{ asset('assets/img/icons/closes.svg') }} " alt="img"></span>
                                    </div>
                                </div>
                                <a class="btn" id="searchdiv"><img src="{{ asset('assets/img/icons/search.svg') }} " alt="img"></a>
                            </form>
                            
                            
                        </div>
                    </li>
                
                
                    <li class="nav-item dropdown has-arrow flag-nav">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
                            <img src="{{ asset('assets/img/flags/Vietnam.png') }} " alt="" height="20">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="javascript:void(0);" class="dropdown-item">
                                <img src="{{ asset('assets/img/flags/us.png') }} " alt="" height="16"> English
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <img src="{{ asset('assets/img/flags/fr.png') }} " alt="" height="16"> French
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <img src="{{ asset('assets/img/flags/es.png') }} " alt="" height="16"> Spanish
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <img src="{{ asset('assets/img/flags/de.png') }} " alt="" height="16"> German
                            </a>
                        </div>
                    </li>
                
                
                    <li class="nav-item dropdown has-arrow main-drop">
                        <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                            <span class="user-img"><img src="{{ asset('assets/img/avataradmin.jpg') }} " alt="">
                            <span class="status online"></span></span>
                        </a>
                        <div class="dropdown-menu menu-drop-user">
                            <div class="profilename">
                                <div class="profileset">
                                    <span class="user-img"><img src="{{ asset('assets/img/avataradmin.jpg') }} " alt="">
                                    <span class="status online"></span></span>
                                    <div class="profilesets">
                                        {{-- $users này là đăng ký trong Providers --}}
                                        <h6>{{$users->name}}</h6> 
                                        <h5>Admin</h5>
                                    </div>
                                </div>
                            <hr class="m-0">
                            <a class="dropdown-item" href="{{route('profile.edit')}}"> <i class="me-2" data-feather="user"></i> My Profile</a>
                            {{-- <a class="dropdown-item" href="generalsettings.html"><i class="me-2" data-feather="settings"></i>Settings</a> --}}
                            <hr class="m-0">
                            <form method="POST" action="{{ route('logout') }}" style="display: inline-block;">
                                @csrf
    
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                                    
                                    <div style="display: flex; align-items: center; padding: 7px 10px">
                                        <img src="{{ asset('assets/img/icons/log-out.svg') }}" alt="">
                                        <span style="margin-left: 10px; color: #637381; font-size: 13px;">Logout</span>    
                                    </div>                                
                                    
                                </x-dropdown-link>
                            </form>
                            {{-- <a class="dropdown-item logout pb-0" ><img src="{{ asset('assets/img/icons/log-out.svg') }} " class="me-2" alt="img">Logout</a> --}}
                            </div>
                        </div>
                    </li>
                </ul>
            
            
                <div class="dropdown mobile-user-menu">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="{{route('profile.edit')}}">My Profile</a>
                        {{-- <a class="dropdown-item" href="generalsettings.html">Settings</a> --}}
                        <form method="POST" action="{{ route('logout') }}" style="display: inline-block">
                            @csrf
    
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
    
                                    <span style="color: #333; font-size: 13px;">Logout</span>    
    
                            </x-dropdown-link>
                        </form>
                    </div>
                </div>
            
    </div>
    {{-- end header --}}

    {{-- start sidebar --}}
    <div class="sidebar" id="sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <!-- Dashboard Section -->
                    <li class="active">
                        <a href="{{url('admin/index')}}">
                            <img src="{{ asset('assets/img/icons/dashboard.svg') }}" alt="img">
                            <span> Dashboard</span>
                        </a>
                    </li>
    
                    <!-- Users Section -->
                    <li class="submenu">
                        <a href="javascript:void(0);">
                            <img src="{{ asset('assets/img/icons/users1.svg') }}" alt="img">
                            <span> Quản lý người dùng</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{url('user_manager')}}">Danh sách người dùng</a></li>
                        </ul>
                    </li>
    
                    {{-- Trang quản lý nhóm người dùng --}}
                    <li class="submenu">
                        <a href="javascript:void(0);">
                            <img src="{{ asset('assets/img/icons/places.svg') }}" alt="img">
                            <span> Quản lý nhóm</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{url('group_manager')}}">Danh sách nhóm</a></li>
                        </ul>
                    </li>
    
                    <!-- Settings Section -->
                    <li class="submenu">
                        <a href="javascript:void(0);">
                            <img src="{{ asset('assets/img/icons/transcation.svg') }}" alt="img">
                            <span>Danh Mục</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{url('list_conversation')}}">Danh sách hội thoại</a></li>
                            <li><a href="{{url('list_imageFile')}}">Danh sách hình ảnh và tệp tin</a></li>
                            <li><a href="{{url('list_map')}}">Danh sách bản đồ</a></li>
                            
                        </ul>
                    </li>
    
                    <li class="">
                        <a href="{{ route('manager_news') }}">
                            <img src="{{ asset('assets/img/icons/mail.svg') }}" alt="">
                            <span>Quản lý thông báo</span> </a>
                    </li>
                    <li class="">
                        <a href="{{ route('drive.manager') }}">
                            <img src="{{ asset('assets/img/icons/purchase1.svg') }}" alt="">
                            <span>Quản lý kho lưu trữ</span> </a>
                    </li>
                    <li class="">
                        <a href="{{ url('loginHistory') }}">
                            <img src="{{ asset('assets/img/icons/time.svg') }}" alt="">
                            <span>Lịch sử đăng nhập</span> </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    {{-- end siderbar --}}


    {{-- Body --}}
    {{$slot}}

</div>


    {{-- start footer --}}
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }} "></script>
    {{-- <script src="{{ asset('assets/js/searchUser.js') }}"></script> --}}

    <script src="{{ asset('assets/js/feather.min.js') }} "></script>

    <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }} "></script>

    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }} "></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }} "></script>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }} "></script>

    <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }} "></script>
    <script src="{{ asset('assets/plugins/apexchart/chart-data.js') }} "></script>

    <script src="{{ asset('assets/js/script.js') }} "></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Thư viện hiển thị biểu đồ --}}
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <script src="{{ asset('assets/js/searchUser.js') }}"></script>
    <script>
        function confirmation(e, title = "Bạn có muốn xoá thông tin này?", text = "") {
        e.preventDefault();

        var urlToRedirect = e.currentTarget.getAttribute('href');

        console.log(urlToRedirect);

        swal({
            title: title,
            text: text,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })

        .then((willCancel)=>{
            if (willCancel) {
            window.location.href = urlToRedirect;
            }
        });

        }
    </script>

    {{-- end footer --}}
</body>
</html>