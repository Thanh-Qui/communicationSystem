<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->user_type == 'admin' || !Auth::check()) {
            // nếu người dùng chưa đăng nhập hoặc ko phải admin chuyển đến trang kế tiếp
            return $next($request);
        }

        return redirect()->route('chatify');
    }
}
