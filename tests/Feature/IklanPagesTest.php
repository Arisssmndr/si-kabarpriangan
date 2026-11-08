<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TransaksiKoran;
use App\Models\Pelanggan;
use App\Models\SalesAe;
use App\Models\JenisIklan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IklanPagesTest extends TestCase
{
    use RefreshDatabase;
    public function test_transaksikoran_index_renders_without_error()
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $response = $this->actingAs($user)->get(route('transaksikoran.index'));
        $response->assertStatus(200);
        $response->assertDontSee('ErrorException');
    }

    public function test_dashboard_renders_without_error()
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $response = $this->actingAs($user)->get(route('iklan.dashboard'));
        $response->assertStatus(200);
    }

    public function test_laporan_index_renders_without_error()
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        $response = $this->actingAs($user)->get(route('laporan.index'));
        $response->assertStatus(200);
    }
}
