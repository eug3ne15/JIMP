<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class SecurePasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_page_recognizes_email_from_database_and_has_no_email_input(): void
    {
        $user = User::factory()->create([
            'email' => 'jane.intern@judiciary.go.ke',
            'name' => 'Jane Intern',
        ]);

        $token = Password::createToken($user);

        $response = $this->get(route('password.reset', ['token' => $token]));

        $response->assertStatus(200);
        // User's email is displayed
        $response->assertSee('jane.intern@judiciary.go.ke');
        $response->assertSee('Jane Intern');
        $response->assertSee('Resetting password for:');
        // No editable email input field exists
        $response->assertDontSee('<input type="email" id="email" name="email"', false);
        // Hidden token and email exist
        $response->assertSee('<input type="hidden" name="token" value="'.$token.'">', false);
        $response->assertSee('<input type="hidden" name="email" value="'.$user->email.'">', false);
    }

    public function test_reset_password_page_rejects_invalid_token(): void
    {
        $response = $this->get(route('password.reset', ['token' => 'nonexistent-token']));

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_reset_password_without_entering_email(): void
    {
        $user = User::factory()->create([
            'email' => 'intern.user@judiciary.go.ke',
            'password' => Hash::make('old-password-123'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'password' => 'NewSecurePass@2026',
            'password_confirmation' => 'NewSecurePass@2026',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $this->assertTrue(Hash::check('NewSecurePass@2026', $user->fresh()->password));
    }

    public function test_attacker_cannot_change_another_persons_password_using_their_token(): void
    {
        $alice = User::factory()->create([
            'email' => 'alice@judiciary.go.ke',
            'password' => Hash::make('alice-original-password'),
        ]);

        $bob = User::factory()->create([
            'email' => 'bob@judiciary.go.ke',
            'password' => Hash::make('bob-original-password'),
        ]);

        // Alice requests a password reset token
        $aliceToken = Password::createToken($alice);

        // Attacker attempts to use Alice's token to reset Bob's password by injecting Bob's email
        $response = $this->post(route('password.update'), [
            'token' => $aliceToken,
            'email' => 'bob@judiciary.go.ke',
            'password' => 'HackedPassword@123',
            'password_confirmation' => 'HackedPassword@123',
        ]);

        // The system resolves Alice's email from the database token and updates Alice's password,
        // while Bob's password remains completely untouched!
        $this->assertTrue(Hash::check('bob-original-password', $bob->fresh()->password));
        $this->assertFalse(Hash::check('HackedPassword@123', $bob->fresh()->password));
    }
}
