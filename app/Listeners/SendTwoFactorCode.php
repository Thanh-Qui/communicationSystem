<?php

namespace App\Listeners;

use App\Events\TwoFactorCodeGenerated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTwoFactorCode
{
    /**
     * Handle the event.
     */
    public function handle(TwoFactorCodeGenerated $event): void
    {
        $twoFactorCode = session('two_factor_code');
        
        // Kiểm tra xem mã 2FA có đúng trong session không    
        if (!$twoFactorCode) {
            return;
        }

        $user = $event->user;
    
        // Gửi email với mã 2FA
        // Mail::raw("Your Two Factor Anthentication code is: {$twoFactorCode}", function ($message) use ($event) {
        //     $message->to($event->user->email)
        //             ->subject('Your Two Factor Code');
        // });

        Mail::send('components.email-form-2fa', [
            'user' => $user,
            'twoFactorCode' => $twoFactorCode,
            ], function ($message) use ($user) {
                $message->to($user->email)->subject('Your Two Factor Code');
            }
        );
    }
}
