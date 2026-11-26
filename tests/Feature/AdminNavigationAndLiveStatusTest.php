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
        $response->assertSee('Dashboard');
        $response->assertSee('Manajemen Pengguna');
        $response->assertSee('Log Aktivitas');
        $response->assertSee('Cadangan Data');
        $response->assertSee('Monitoring Sistem');
        $response->assertSee('Pengaturan Sistem');
        $response->assertDontSee('Laporan Divisi');
        $response->assertDontSee('Profil Akun Admin');
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
     * Test live presence status across divisions (iklan, sirkulasi, petugas_inkaso) in administrator monitoring.
     */
    public function test_iklan_sirkulasi_and_inkaso_live_presence_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'administrator',
            'status' => 'active',
            'last_seen_at' => now(),
        ]);
        Cache::put('user-is-online-' . $admin->id, true, now()->addMinutes(3));

        $userIklan = User::factory()->create([
            'name' => 'Staf Iklan Koran',
            'role' => 'iklan',
            'status' => 'active',
            'last_seen_at' => now(),
        ]);
        Cache::put('user-is-online-' . $userIklan->id, true, now()->addMinutes(3));

        $userSirkulasi = User::factory()->create([
            'name' => 'Staf Sirkulasi',
            'role' => 'sirkulasi',
            'status' => 'active',
            'last_seen_at' => null, // Offline
        ]);

        $userInkaso = User::factory()->create([
            'name' => 'Kolektor Inkaso Lapangan',
            'role' => 'petugas_inkaso',
            'status' => 'active',
            'last_seen_at' => now(),
        ]);
        Cache::put('user-is-online-' . $userInkaso->id, true, now()->addMinutes(3));

        // Administrator polls online status API
        $response = $this->actingAs($admin)->get(route('admin.users.online-status'));
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertTrue($json['users'][$userIklan->id]['is_online']);
        $this->assertFalse($json['users'][$userSirkulasi->id]['is_online']);
        $this->assertTrue($json['users'][$userInkaso->id]['is_online']);
        $this->assertEquals('Petugas Inkaso', $json['users'][$userInkaso->id]['role_label']);

        // When inkaso logs out, status immediately becomes offline
        $this->actingAs($userInkaso)->post(route('logout'));
        $this->assertFalse($userInkaso->fresh()->isOnline());

        // Re-check from admin perspective
        $response2 = $this->actingAs($admin)->get(route('admin.users.online-status'));
        $json2 = $response2->json();
        $this->assertFalse($json2['users'][$userInkaso->id]['is_online']);
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

    /**
     * Test admin activity logs and system configuration render without blade syntax error.
     */
    public function test_admin_pages_render_and_laporan_redirects_to_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'administrator',
            'status' => 'active',
        ]);

        // 1. Activity Logs renders successfully
        $responseLogs = $this->actingAs($admin)->get(route('admin.activity-logs.index'));
        $responseLogs->assertStatus(200);

        // 2. Pengaturan Sistem renders successfully with standardized inputs
        $responseConfig = $this->actingAs($admin)->get(route('admin.pengaturan.index'));
        $responseConfig->assertStatus(200);
        $responseConfig->assertSee('Pengaturan Sistem');
        $responseConfig->assertSee('Profil Perusahaan');

        // 3. Admin Laporan redirects to Admin Dashboard
        $responseLaporan = $this->actingAs($admin)->get(route('admin.laporan.index'));
        $responseLaporan->assertRedirect(route('admin.dashboard'));
    }
}
