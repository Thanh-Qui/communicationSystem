<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        $user = User::all();
        $user_id = Auth::user()->id;
        $user_check = User::where('id', $user_id)->first();
        if ($user_check->active_status != 2) {
            if ($user_check) {

                // kiểm tra xác thực two factor authentication
                if ($user_check->two_factor_code !== null) {
                    // Tạo mã 2FA mới và lưu vào session
                    $user_check->generateTwoFactorCode();
                    // Chuyển tới form nhập mã 2FA
                    return redirect()->route('2fa.verify');
                }    

            // Xóa lịch sử đăng nhập quá 30 ngày
            LoginHistory::where('created_at', '<', now()->subHour(720))->delete();
            $loginHistory = new LoginHistory();
            // kiểm tra nếu là admin thì không cần lưu lịch sử đăng nhập
            $userType = User::find($user_id);
            if ($userType->user_type != 'admin') {
                $loginHistory->user_id = $user_id;
                $loginHistory->ip_address = $request->ip();
                $loginHistory->user_agent = $request->userAgent();
                $loginHistory->save();
            }
            
            $user_check->active_status = 1;
            $user_check->save();
            }

            if (Auth::user()->user_type == 'admin') {
                return redirect('admin/index');
            }
            
            return redirect()->intended(route('chatify', compact('user'), absolute: false));
        }else {
            Auth::logout();
            toastr()->closeButton()->error('Tài khoản của bạn đã bị khóa. Vui lòng liên hệ với Admin');
            return redirect()->back();
        }
        

        // return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {

        $user_id = Auth::user()->id;
        $user_check = User::where('id', $user_id)->first();
        if ($user_check) {
            $user_check->active_status = 0;

            $user_check->save();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
