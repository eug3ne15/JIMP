<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\SendOtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OtpLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_with_email_and_password_inputs(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Email:');
        $response->assertSee('Password:');
        $response->assertSee('6-digit verification code will be sent to your email after password entry');
    }

    public function test_submitting_invalid_credentials_does_not_send_otp(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'jane.doe@judiciary.go.ke',
            'password' => Hash::make('correct-password-123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'jane.doe@judiciary.go.ke',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        Notification::assertNothingSent();
    }

    public function test_submitting_valid_credentials_sends_otp_and_redirects_to_verification_form(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'jane.doe@judiciary.go.ke',
            'name' => 'Jane Doe',
            'password' => Hash::make('SecretPass@123'),
            'role' => 'intern',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'jane.doe@judiciary.go.ke',
            'password' => 'SecretPass@123',
        ]);

        // User is not logged in yet (pending OTP verification)
        $this->assertGuest();

        $response->assertRedirect(route('otp.verify.form'));
        $response->assertSessionHas('status');
        $response->assertSessionHas('otp_user_id', $user->id);
        $response->assertSessionHas('otp_email', $user->email);

        Notification::assertSentTo($user, SendOtpNotification::class, function ($notification) use ($user) {
            $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $notification->otp);

            $mail = $notification->toMail($user);
            $this->assertStringContainsString('Verification Code', $mail->subject);
            $this->assertStringContainsString($notification->otp, implode(' ', $mail->introLines));

            return true;
        });
    }

    public function test_user_can_verify_otp_and_login_successfully(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'intern.user@judiciary.go.ke',
            'password' => Hash::make('MyPassword@123'),
            'role' => 'intern',
        ]);

        // Step 1: Submit email & password
        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'MyPassword@123',
        ]);

        $capturedOtp = null;
        Notification::assertSentTo($user, SendOtpNotification::class, function ($notification) use (&$capturedOtp) {
            $capturedOtp = $notification->otp;

            return true;
        });

        $this->assertNotNull($capturedOtp);

        // Step 2: Submit the valid OTP code received in email
        $response = $this->post(route('otp.verify'), [
            'code' => $capturedOtp,
        ]);

        $response->assertRedirect(route('dashboard.intern'));
        $this->assertAuthenticatedAs($user);

        // Session keys cleared
        $this->assertNull(session('otp_user_id'));
        $this->assertNull(session('otp_email'));

        // Single-use: submitting the same code again fails
        Auth::logout();
        session(['otp_user_id' => $user->id, 'otp_email' => $user->email]);

        $secondAttempt = $this->post(route('otp.verify'), [
            'code' => $capturedOtp,
        ]);

        $secondAttempt->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_role_based_redirects_after_otp_verification(): void
    {
        Notification::fake();

        $roles = [
            'hr' => 'hr.dashboard',
            'supervisor' => 'supervisor.dashboard',
            'admin' => 'admin.dashboard',
            'intern' => 'dashboard.intern',
        ];

        foreach ($roles as $role => $expectedRoute) {
            $user = User::factory()->create([
                'email' => "{$role}.test@judiciary.go.ke",
                'password' => Hash::make('RolePassword@123'),
                'role' => $role,
            ]);

            $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'RolePassword@123',
            ]);

            $otp = null;
            Notification::assertSentTo($user, SendOtpNotification::class, function ($notification) use (&$otp) {
                $otp = $notification->otp;

                return true;
            });

            $response = $this->post(route('otp.verify'), ['code' => $otp]);
            $response->assertRedirect(route($expectedRoute));
            $this->assertAuthenticatedAs($user);

            Auth::logout();
        }
    }

    public function test_invalid_otp_code_fails_verification(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'target@judiciary.go.ke',
            'password' => Hash::make('ValidPass123'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'ValidPass123',
        ]);

        $response = $this->post(route('otp.verify'), [
            'code' => '999999',
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_rate_limiter_blocks_brute_force_verification(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'victim@judiciary.go.ke',
            'password' => Hash::make('ValidPass123'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'ValidPass123',
        ]);

        // Attempt 5 incorrect guesses
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('otp.verify'), ['code' => '00000'.$i]);
        }

        // 6th attempt should be blocked by rate limiter
        $blockedResponse = $this->post(route('otp.verify'), ['code' => '000006']);
        $blockedResponse->assertSessionHasErrors('code');
        $this->assertStringContainsString('Too many failed attempts', session('errors')->first('code'));
    }

    public function test_user_can_resend_otp_code(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'resend.test@judiciary.go.ke',
            'password' => Hash::make('ValidPass123'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'ValidPass123',
        ]);

        // Request resend
        $response = $this->post(route('otp.resend'));
        $response->assertRedirect();
        $response->assertSessionHas('status');

        Notification::assertSentToTimes($user, SendOtpNotification::class, 2);
    }

    public function test_unauthenticated_user_accessing_otp_verify_directly_is_redirected_to_login(): void
    {
        $response = $this->get(route('otp.verify.form'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }
}
