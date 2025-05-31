{{-- <!DOCTYPE html>
<html lang="en" style="height: 100%">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('stylecss.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>Trang đang bảo trì</title>
</head>
<body style="height: 100%">
    <div style="padding: 15px">
        <a href="{{url('admin/index')}}"><i class="fa-solid fa-backward"></i></a>
    </div>
    <div class="container">
        <div class="wrapper" style="margin-bottom: 100px">
            <h1 style="font-size: 56px">Trang đang bảo trì</h1>
            <p style="font-size: 24px">Vui lòng quay lại trong ít phút <i style="color: red; font-size: 24px" class="fa-solid fa-heart"></i>
                <i style="color: red; font-size: 24px" class="fa-solid fa-heart"></i>
            </p>
        </div>
    </div>
    

</body>
</html> --}}

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="POS - Bootstrap Admin Template">
    <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern,  html5, responsive">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>Page Not Found</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/iconmessage.png') }}">
    
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }} ">
    
    <link rel="stylesheet" href="{{ asset(' assets/css/animate.css') }}">
    
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }} ">
    
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }} ">
    
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }} ">
</head>
<body class="error-page">
    <div id="global-loader" style="display: none;">
        <div class="whirly-loader"> </div>
    </div>
    
    <div class="main-wrapper">
    <div class="error-box">
    {{-- <h1>404</h1> --}}
    <img src="{{ asset('images/bg404.png') }}" alt="">
    {{-- <h3 class="h2 mb-3"><i class="fas fa-exclamation-circle"></i> Oops! Page not found!</h3> --}}
    <p class="h4 font-weight-normal">The page you requested was not found.</p>

    @if (isset($users))
        @if ($users->user_type === 'admin')
            <a href="{{url('admin/index')}}" class="btn btn-primary">Back to Home</a>
        @else
            <a href="{{route('chatify')}}" class="btn btn-primary">Back to Home</a>
        @endif
    @else
        <a href="{{ url('/') }}" class="btn btn-primary">Back to Login in</a>
    @endif
    </div>
    </div>
    
    
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }} "></script>
    
    <script src="{{ asset('assets/js/feather.min.js') }} "></script>
    
    <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }} "></script>
    
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }} "></script>
    
    <script src="{{ asset('assets/js/script.js') }} "></script>
    
    <div class="sidebar-overlay"></div>
</body>
</html>