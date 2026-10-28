<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    /**
     * Generate, hash, cache, and send the OTP via email.
     */
    public function sendOtp(User $user): string
    {
        // Generate a secure 6-digit numeric code
        $otp = sprintf('%06d', random_int(0, 999999));

        // Store the hashed OTP in cache for 10 minutes
        Cache::put(
            $this->getCacheKey($user),
            Hash::make($otp),
            now()->addMinutes(10)
        );

        // Send email notification to user
        $user->notify(new SendOtpNotification($otp));

        return $otp;
    }

    /**
     * Verify the submitted code against the cached hash.
     */
    public function verifyOtp(User $user, string $submittedCode): bool
    {
        $key = $this->getCacheKey($user);
        $hashedOtp = Cache::get($key);

        if (! $hashedOtp || ! Hash::check($submittedCode, $hashedOtp)) {
            return false;
        }

        // Single-use: destroy the OTP immediately upon successful verification
        Cache::forget($key);

        return true;
    }

    /**
     * Get the cache key for storing the user's OTP.
     */
    private function getCacheKey(User $user): string
    {
        return "otp_for_user_{$user->id}";
    }
}
