{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="POS - Bootstrap Admin Template">
    <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>Login - Chat Online</title>
    <link rel="shortcut icon" type="image/x-icon" href=" {{ asset('assets/img/logoChat.png') }} ">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }} ">

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }} ">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="account-page">

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper">
                
                <!-- Login Content -->
                <div class="login-content">
                    <div class="login-userset">
                        
                        <!-- Logo -->
                        <div class="login-logo">
                            <img src="{{ asset('assets/img/logoChat.png') }} " alt="img">
                        </div>

                        <!-- Heading -->
                        <div class="login-userheading" style="margin-bottom: 30px; text-align: center">
                            {{-- <h3>Login In</h3> --}}
                            <h1 style="font-size: 48px"><strong><span style="color: #d35400">L </span>o g i n</strong> </h1>
                            {{-- <h4>Please login to your account</h4> --}}
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                    
                            <!-- Email Address -->
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                    
                            <!-- Password -->
                            <div class="mt-4">
                                <x-input-label for="password" :value="__('Password')" />
                    
                                <x-text-input id="password" class="block mt-1 w-full"
                                                type="password"
                                                name="password"
                                                required autocomplete="current-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                    
                            <!-- Remember Me -->
                            <div class="block mt-4">
                                <label for="remember_me" class="inline-flex items-center">
                                    <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                                </label>
                            </div>
                    
                            <div class="flex items-center justify-end mt-4">
                                @if (Route::has('password.request'))
                                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                                        {{ __('Forgot your password?') }}
                                    </a>
                                @endif

                                @if (Route::has('register'))
                                    <x-button-register style="margin-left: 20px">
                                        Register

                                    </x-button-register>
                                @endif
                    
                                <x-primary-button class="ms-3">
                                    {{ __('Log in') }} 
                                </x-primary-button>
                            </div>
                        </form>

                            
                        

                        <!-- Social Sign In -->
                        <div class="form-setlogin">
                            <h4>Or sign up with</h4>
                        </div>
                        <div class="form-sociallink">
                            <!-- Link to QR Scanner -->
                            <div class="mt-6 text-center">
                                <a href="{{ route('qr.scanner') }}" class="btn btn-primary" style="width: 100%">
                                    {{ __('Đăng nhập bằng mã QR ') }} <i class="fas fa-qrcode"></i>
                                </a>
                            </div>
                            {{-- <ul>
                                <li>
                                    <a href="javascript:void(0);">
                                        <img src="{{ asset('assets/img/icons/google.png') }} " class="me-2" alt="google">
                                        Sign Up using Google
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">
                                        <img src="{{ asset('assets/img/icons/facebook.png') }} " class="me-2" alt="facebook">
                                        Sign Up using Facebook
                                    </a>
                                </li>
                            </ul> --}}
                        </div>
                    </div>
                </div>

                <!-- Login Image -->
                <div class="login-img">
                    <img src="{{ asset('assets/img/login1.png') }} " alt="img">
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }} "></script>

    <!-- Feather Icons -->
    <script src="{{ asset('assets/js/feather.min.js') }} "></script>

    <!-- Bootstrap Bundle JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }} "></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/script.js') }} "></script>
</body>
</html>
