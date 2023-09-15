<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifyOtp extends Model
{
    use HasFactory;

    protected $table = 'verify_otps';

    protected $fillable = [
        'email',
        'mobile',
        'otp',
    ];

    public function generateOTP()
    {
        // Generate the OTP
        $otp = mt_rand(100000, 999999); // Generate a random 6-digit OTP

        // Store the OTP and email in the database
        VerifyOtp::create([
            'otp' => $otp,
            'email' => request()->input('email'),
        ]);

        // Send the OTP to the user via email or any other method

        // Return a response indicating success or redirect to the appropriate page
    }
}
