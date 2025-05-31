<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function index()
    {
        return view('auth.two-factor');
    }

    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|digits:6',
        ]);

        $user = Auth::user();

        // Kiểm tra mã 2FA từ session
        $twoFactorCode = session('two_factor_code');
        $twoFactorExpiresAt = session('two_factor_expires_at');

        // Kiểm tra giá trị mã và thời gian hết hạn từ session
        // dd($twoFactorCode, $request->two_factor_code, $twoFactorExpiresAt);

        if (!$twoFactorCode) {
            return back()->withErrors(['two_factor_code' => 'Không tìm thấy mã xác thực.']);
        }

        if ((string) $request->two_factor_code !== (string) $twoFactorCode) {
            return back()->withErrors(['two_factor_code' => 'Mã xác thực không đúng.']);
        }
        
        // Kiểm tra mã đã hết hạn chưa
        if (now()->greaterThan($twoFactorExpiresAt)) {
            return back()->withErrors(['two_factor_code' => 'Mã xác thực đã hết hạn.']);
        }

        // Sau khi xác thực thành công, xóa mã 2FA khỏi session
        session()->forget(['two_factor_code', 'two_factor_expires_at']);

        return redirect()->intended('/chatify');
    }

    public function addTwoFactorKey(Request $request) {

        // xác thực mật khẩu
        $request->validate([
            'password2fa' => ['required', 'current_password'],
        ]);

        if(Auth::user()->two_factor_code == null){
            $user = User::find($request->user_id);
            $user->two_factor_code = bcrypt(Str::random(6));
            toastr()->closeButton()->success('Bật xác thực thành công');
        }else {
            $user = User::find($request->user_id);
            $user->two_factor_code = null;
            toastr()->closeButton()->success('Tắt xác thực thành công');
        }
        $user->save();

        
        return redirect()->back();
    }

    public function resend() {
        $user_id = Auth::id();
        $user_check = User::where('id', $user_id)->first();
        if($user_check) {
            if ($user_check->two_factor_code !== null) {
                // Tạo mã 2FA mới và lưu vào session
                $user_check->generateTwoFactorCode();
                // Chuyển tới form nhập mã 2FA
                toastr()->closeButton()->success('Gửi mã 2FA thành công');
                return redirect()->route('2fa.verify');
            } 
        }

        toastr()->closeButton()->error('Hệ thống đang bảo trì');
        return redirect()->back();
    }
}
