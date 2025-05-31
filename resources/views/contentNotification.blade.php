<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thông Báo</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/iconmessage.png') }}">
    <link rel="stylesheet" href="{{ asset('stylecss.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>
<body>
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
            <p><a href="{{ url('system-notification') }}">Quay về</a> / Thông báo</p>
            <hr>
        </div>


        @foreach ($news as $data)
        @php
            $attachment = json_decode($data->img);
        @endphp
            
            <div class="sn-content">
                <h3>{{$data->title}}</h3>
                <p>{{$data->created_at}}</p>
                <div class="sn-main-content">
                    <p>
                        {!! nl2br($data->content) !!}
                        
                    </p>

                    <div style="text-align: center">
                        @isset($attachment)
                            <img style="max-width: 100%; height: auto; object-fit: contain;" 
                                src="{{ asset('storage/attachments/' . $attachment->new_name) }}" 
                                alt="">
                        @endisset
                        
                    </div>
                    
                    
                </div>
            </div>
        @endforeach
        

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

</body>
</html>