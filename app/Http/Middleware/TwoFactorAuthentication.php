<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Kiểm tra nếu người dùng đã đăng nhập và có mã 2FA trong session
        $twoFactorCode = session('two_factor_code');
        $twoFactorExpiresAt = session('two_factor_expires_at');
        
        // Kiểm tra xem người dùng có mã 2FA hợp lệ và thời gian hết hạn còn hiệu lực không
        if ($user && $twoFactorCode && $twoFactorExpiresAt) {
            // Đảm bảo two_factor_expires_at là kiểu Carbon
            $twoFactorExpiresAt = Carbon::parse($twoFactorExpiresAt);
    
            // kiểm tra code có hết hạn hay không. nếu time now nhỏ hơn time verify thì chuyển trang verify
            if (now()->lessThan($twoFactorExpiresAt)) {
                return redirect()->route('2fa.verify');
            } else {
                // Mã đã hết hạn → xóa khỏi session và chuyển hướng
                session()->forget(['two_factor_code', 'two_factor_expires_at']);
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'two_factor_code' => 'Mã xác thực đã hết hạn. Vui lòng đăng nhập lại.'
                ]);
            }
        }
    
        // Tiếp tục request nếu không cần xác minh 2FA
        return $next($request);
    }
}
