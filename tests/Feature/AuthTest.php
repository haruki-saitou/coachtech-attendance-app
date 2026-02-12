<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    public function test_login()
    {
        $this->get('/login')->assertSee('ログイン');
    }

    public function test_register()
    {
        $this->get('/register')->assertSee('会員登録');
    }

    public function test_logout()
    {
        $this->get('/logout')->assertSee('ログアウト');
    }

    public function test_name_is_required()
    {
        $response = $this->post('/register',[
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    public function test_email_is_required()
    {
        $response = $this->post('/register',[
            'name' => 'test',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_password_is_required()
    {
        $response = $this->post('/register',[
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_password_confirmation_is_required()
    {
        $response = $this->post('/register',[
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    public function test_password_and_password_confirmation_must_match()
    {
        $response = $this->post('/register',[
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('password', 'パスワードと一致しません');
    }

    public function test_password_must_be_at_least_8_characters()
    {
        $response = $this->post('/register',[
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ]);

        $response->assertSessionHasErrors('password', 'パスワードは8文字以上で入力してください');
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
        $response = $this->post('/login',[
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/attendance');
    }

    public function test_unverified_user_cannot_access_attendance_page()
    {
        $user = User::factory()->unverified()->create();
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertRedirect('/email/verify');
    }

    public function test_verified_user_can_access_attendance_page()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
    }

    public function test_registration_sends_verification_email()
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        Notification::assertSentTo(
            User::where('email', 'test@example.com')->first(), VerifyEmail::class
        );
    }
}