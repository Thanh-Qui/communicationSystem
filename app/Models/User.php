<?php

namespace App\Models;

use App\Events\TwoFactorCodeGenerated;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'gender',
        'dob',
        'address',
        'phone',
        'user_type',
        'password',
        'two_factor_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function generateTwoFactorCode()
    {
        $twoFactorCode = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(3);
        
        // Lưu mã và thời gian hết hạn vào session (chuyển đối tượng Carbon thành chuỗi)
        session([
            'two_factor_code' => $twoFactorCode, 
            'two_factor_expires_at' => $this->two_factor_expires_at->toDateTimeString()
        ]);
    
        event(new TwoFactorCodeGenerated($this));
    }
    


    protected $casts = [
        'two_factor_expires_at' => 'datetime',
    ];
}
