<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminNavigationAndLiveStatusTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin dashboard can be rendered and contains all administrator menus in the sidebar.
     */
    public function test_admin_dashboard_renders_with_complete_navigation_menus(): void
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->create([
            'role' => 'administrator',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Administrator');
        $response->assertSee('Dashboard Admin');
        $response->assertSee('Pengguna & Sesi Live', false);
        $response->assertSee('Audit Log Keamanan');
        $response->assertSee('Cadangan Data (Backup)');
        $response->assertSee('Status & Log Sistem', false);
        $response->assertSee('Laporan Eksekutif');
        $response->assertSee('Pengaturan Sistem');
        $response->assertSee('Live Status Sesi Admin & Pengguna', false);
    }

    /**
     * Test live online status API returns real-time user data.
     */
    public function test_admin_online_status_api(): void
    {
        $admin = User::factory()->create([
            'role' => 'administrator',
            'status' => 'active',
            'last_seen_at' => now(),
        ]);
        Cache::put('user-is-online-' . $admin->id, true, now()->addMinutes(3));

        $offlineUser = User::factory()->create([
            'role' => 'iklan',
            'status' => 'active',
            'last_seen_at' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.online-status'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'online_count',
            'offline_count',
            'admin_online_count',
            'total_count',
            'users',
            'timestamp',
        ]);

        $json = $response->json();
        $this->assertTrue($json['users'][$admin->id]['is_online']);
        $this->assertFalse($json['users'][$offlineUser->id]['is_online']);
    }

    /**
     * Test user online status becomes active on login and inactive on logout.
     */
    public function test_user_active_on_login_and_inactive_on_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'administrator',
            'status' => 'active',
            'password' => bcrypt('password123!'),
            'last_seen_at' => null,
        ]);

        $this->assertFalse($user->fresh()->isOnline());

        // Simulate login
        $this->actingAs($user);
        $user->update(['last_seen_at' => now()]);
        Cache::put('user-is-online-' . $user->id, true, now()->addMinutes(3));

        $this->assertTrue($user->fresh()->isOnline());

        // Simulate logout
        $response = $this->post(route('logout'));
        $response->assertRedirect('/');

        $this->assertFalse($user->fresh()->isOnline());
    }

    /**
     * Test non-admin user cannot access admin dashboard.
     */
    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $kasir = User::factory()->create([
            'role' => 'kasir',
            'status' => 'active',
        ]);

        $response = $this->actingAs($kasir)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }
}
