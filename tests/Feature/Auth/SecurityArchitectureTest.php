<?php

namespace Tests\Feature\Auth;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_seo_blocking_headers_are_present_on_all_responses(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_three_failed_login_attempts_triggers_temporary_lockout(): void
    {
        $user = User::factory()->create([
            'email' => 'wartawan@kabarpriangan.co.id',
            'password' => Hash::make('1r12skabar17riangan!'),
            'status' => 'active',
        ]);

        // Percobaan 1: Gagal
        $res1 = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-pass-1',
            'g-recaptcha-response' => 'test-token',
        ]);
        $res1->assertSessionHasErrors('email');

        // Percobaan 2: Gagal
        $res2 = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-pass-2',
            'g-recaptcha-response' => 'test-token',
        ]);
        $res2->assertSessionHasErrors('email');

        // Percobaan 3: Ambang batas tercapai -> Lockout 15 Menit
        $res3 = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-pass-3',
            'g-recaptcha-response' => 'test-token',
        ]);
        $res3->assertSessionHasErrors('email');

        // Verifikasi status lockout di database
        $user->refresh();
        $this->assertNotNull($user->locked_until);
        $this->assertTrue($user->isLocked());

        // Verifikasi log tercatat
        $this->assertDatabaseHas('activity_logs', [
            'email_attempted' => $user->email,
            'action' => 'ACCOUNT_LOCKED_15M',
        ]);
    }

    public function test_login_requires_recaptcha_verification(): void
    {
        $user = User::factory()->create([
            'email' => 'wartawan2@kabarpriangan.co.id',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
            // tanpa g-recaptcha-response
        ]);

        $response->assertSessionHasErrors('g-recaptcha-response');
        $this->assertGuest();
    }

    public function test_disabled_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'disabled@kabarpriangan.co.id',
            'password' => Hash::make('1r12skabar17riangan!'),
            'status' => 'disable',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => '1r12skabar17riangan!',
            'g-recaptcha-response' => 'test-token',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->assertDatabaseHas('activity_logs', [
            'email_attempted' => $user->email,
            'action' => 'LOGIN_REJECTED_DISABLED',
        ]);
    }

    public function test_successful_login_clears_failed_attempts_and_records_activity(): void
    {
        $user = User::factory()->create([
            'email' => 'staf@kabarpriangan.co.id',
            'password' => Hash::make('1r12skabar17riangan!'),
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => '1r12skabar17riangan!',
            'g-recaptcha-response' => 'test-token',
        ]);

        $this->assertAuthenticatedAs($user);
        $user->refresh();
        $this->assertEquals(0, $user->failed_login_attempts);
        $this->assertNotNull($user->last_login_at);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'LOGIN_SUCCESS',
        ]);
    }

    public function test_database_backup_command_runs_successfully(): void
    {
        $exitCode = Artisan::call('backup:database', [
            '--frequency' => 'manual',
            '--manual' => true,
        ]);

        $this->assertEquals(0, $exitCode);
        $this->assertDatabaseHas('system_backups', [
            'status' => 'success',
            'frequency' => 'manual',
        ]);
    }
}
