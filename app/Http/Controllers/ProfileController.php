<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    // public function edit(Request $request): View
    // {
    
    //     return view('profile.edit', [
    //         'user' => $request->user(),            
    //     ]);
    // }

    public function edit(Request $request): View
    {
        if (config('session.driver') !== 'database') {
            $sessions = collect();
        } else {
            $sessions = DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', Auth::id())
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(function ($session) {
                    return (object) [
                        'agent' => $this->createAgent($session),
                        'ip_address' => $session->ip_address,
                        'is_current_device' => $session->id === request()->session()->getId(),
                        'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    ];
                });
        }
        // dd($sessions);

        return view('profile.edit', [
            'user' => $request->user(),
            'sessions' => $sessions,
        ]);
    }
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     *  đăng xuất tất cả session, ngoài trừ session đang đăng nhập hiện tại
     */ 
    public function logoutOtherSessions(Request $request)
    {
        // Xác thực mật khẩu
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        // Nếu mật khẩu đúng, tiếp tục xóa session
        if (config('session.driver') !== 'database') {
            return back();  // Nếu session không lưu trong database, không thể logout được.
        }

        $currentSessionId = $request->session()->getId();

        DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', Auth::id())
            ->where('id', '!=', $currentSessionId) // Trừ session hiện tại
            ->delete();

        toastr()->closeButton()->success('Tất cả các phiên đăng nhập đã bị đăng xuất');
        return back()->with('status', 'All other sessions have been logged out.');
    }

    protected function createAgent($session)
    {
        return tap(new Agent(), fn ($agent) => $agent->setUserAgent($session->user_agent));
    }
}
