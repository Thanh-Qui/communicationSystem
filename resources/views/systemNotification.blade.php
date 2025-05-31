<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thông báo</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/iconmessage.png') }}">
    <link rel="stylesheet" href="{{ asset('stylecss.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


</head>
<body style="margin: 0px; padding: 0px">
    {{-- Header --}}
    <div class="sn-header position-relative d-flex align-items-center px-4">
        <!-- Hiển thị title -->
        <div class="sn-header-center text-center mx-auto d-flex align-items-center gap-3">
            
            <div>
                <img src="{{ asset('assets/img/iconmessage.png') }}" alt="" height="auto">
                <p class="mb-0">HỆ THỐNG TRAO ĐỔI THÔNG TIN</p>
                <span>Thông báo hệ thống</span>
            </div>
        </div>
    
        <!-- phần hiển thị chức năng profile và logout -->
        <div class="position-absolute end-0 mobile-hidden" style="margin-right: 10px">
            <div class="dropdown">
            
                <a style="background: transparent; border: none" class="btn btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('storage/users-avatar/' . ($users->avatar ?? 'avatar.png')) }}" alt="Avatar" class="rounded-circle" width="35" height="35">
                    <span>{{$users->name}}</span>

                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <div class="dropdown-custom">
                            <div style="padding: 10px">
                                <x-responsive-nav-link :href="route('profile.edit')">
                                    <i class="fa-regular fa-user"></i> {{ __('Profile') }}
                                </x-responsive-nav-link>
                            </div>
                                
                            <div style="padding: 10px">
                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                
                                    <x-responsive-nav-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                     <i class="fa-solid fa-arrow-right-from-bracket"></i>   {{ __('Log Out') }}
                                    </x-responsive-nav-link>
                                </form>
                            </div>
                        </div>
                        

                    </li>
                   
                </ul>
            </div>
        </div>
    </div>
    


    <div class="sn-container">

        <div class="sn-title">
            <p><a href="{{ route('chatify') }}">Trang chủ</a> / Thông báo</p>
            <hr>
        </div>

        <div>
            <h5>Thông báo</h5>
            <div class="search-notity mb-2">
                <form action="{{ route('system.notify') }}">
                    <input type="text" name="searchNews" id="title" class="form-control" value="{{request('searchNews')}}" placeholder="Nhập tên tìm kiếm">
                </form>
            </div>
            <div class="container-notify">
                @foreach ($news as $data)
                    <a href="{{ url('content-notification', $data->id) }}" >
                        <div class="sn-notification">

                            <img src="assets/img/iconmessage.png" alt="icon">
                            <div class="content">
                                <p class="title">{{$data->title}}</p>
                                
                                @php
                                    $id_user = Auth::id();
                                    $seenNews = DB::table('news_users')
                                                    ->where('id_news', $data->id) // 9
                                                    ->where('id_user', $id_user) // 27
                                                    ->exists();
                                @endphp
                                <div class="date">
                                    <span>{{ $data->created_at->format('d/m/Y H:i') }}</span>
                                    <div style="text-align: end">
                                    
                                        @if (!$seenNews)
                                            <i class="fa-solid fa-circle" style="color: rgb(57, 57, 234)"></i><span class="status-dot me-1"></span> Chưa xem
                                        @else
                                            <span class="text-success"><i class="fa fa-check-circle me-1"></i> Đã xem</span>
                                        @endif
                                    </div>
                                    
                                </div>
                                
                            </div>
                            
                        </div>
                    </a>
                @endforeach
            </div>
            
            
        </div>
        <div style="display: flex; justify-content: center; align-items: center">
            {{$news->onEachSide(1)->links()}}
        </div>

    </div>

    
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

</body>
</html>