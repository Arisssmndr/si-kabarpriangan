<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_forgot_password_is_disabled_and_redirects_to_login(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('warning');
    }

    public function test_public_reset_password_request_is_disabled_and_redirects_to_login(): void
    {
        $response = $this->post('/forgot-password', ['email' => 'user@example.com']);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('warning');
    }
}
